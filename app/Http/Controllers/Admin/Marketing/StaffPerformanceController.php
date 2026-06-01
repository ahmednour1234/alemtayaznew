<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\Lead;
use App\Models\LeadCallLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffPerformanceController extends Controller
{
    public function index(Request $request)
    {
        $me = Auth::guard('admin')->user();

        // Only super-admin or branch_manager / chairman
        $allowed = $me->isSuperAdmin() || in_array($me->department, ['branch_manager', 'chairman']);
        if (! $allowed) {
            abort(403, 'هذا التقرير مخصص لمدير النظام ومدير الفرع فقط.');
        }

        // ── Date range ──────────────────────────────────────────────────────
        $period = $request->input('period', 'this_month');
        [$dateStart, $dateEnd] = match ($period) {
            'last_month'  => [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ],
            'last_3'      => [Carbon::now()->subMonths(3)->startOfDay(), Carbon::now()->endOfDay()],
            'last_6'      => [Carbon::now()->subMonths(6)->startOfDay(), Carbon::now()->endOfDay()],
            'all'         => [Carbon::createFromDate(2000, 1, 1), Carbon::now()->endOfDay()],
            default       => [Carbon::now()->startOfMonth(), Carbon::now()->endOfDay()], // this_month
        };

        // ── Branch filter ────────────────────────────────────────────────────
        $branches = $me->isSuperAdmin() ? Branch::orderBy('name')->get() : collect();
        $branchId = null;

        if ($me->isBranchAdmin()) {
            $branchId = $me->branch_id;
        } elseif ($request->filled('branch_id')) {
            $branchId = (int) $request->input('branch_id');
        }

        // ── Get admins ───────────────────────────────────────────────────────
        $adminsQuery = Admin::with('branch')
            ->withoutTrashed()
            ->where('active', true)
            ->orderBy('name');

        if ($branchId) {
            $adminsQuery->where('branch_id', $branchId);
        }

        $admins = $adminsQuery->get();

        // ── Build per-admin stats ─────────────────────────────────────────────
        $rows = $admins->map(function (Admin $admin) use ($dateStart, $dateEnd) {

            // Leads assigned to this admin in period
            $baseLeads = Lead::where('assigned_admin_id', $admin->id)
                ->whereBetween('created_at', [$dateStart, $dateEnd]);

            $totalLeads    = (clone $baseLeads)->count();
            $converted     = (clone $baseLeads)->where('status', 'converted')->count();
            $inProgress    = (clone $baseLeads)->where('status', 'in_progress')->count();
            $newLeads      = (clone $baseLeads)->where('status', 'new')->count();
            $archived      = (clone $baseLeads)->where('status', 'archived')->count();

            // Call logs made BY this admin in period
            $baseCalls = LeadCallLog::where('lead_call_logs.admin_id', $admin->id)
                ->whereBetween('lead_call_logs.created_at', [$dateStart, $dateEnd]);

            $totalCalls  = (clone $baseCalls)->count();
            $calledLeads = (clone $baseCalls)->distinct('lead_id')->count('lead_id');
            $neverCalled = max(0, $totalLeads - $calledLeads);

            // Same-day follow-up: call made on same calendar day as lead creation
            $sameDayCalls = LeadCallLog::where('lead_call_logs.admin_id', $admin->id)
                ->join('leads', 'leads.id', '=', 'lead_call_logs.lead_id')
                ->whereBetween('lead_call_logs.created_at', [$dateStart, $dateEnd])
                ->whereRaw('DATE(lead_call_logs.created_at) = DATE(leads.created_at)')
                ->count();

            // Delayed follow-ups: calls made AFTER scheduled follow_up_at
            $delayedCalls = (clone $baseCalls)
                ->whereNotNull('follow_up_at')
                ->whereColumn('created_at', '>', 'follow_up_at')
                ->count();

            // Rates
            $callRate       = $totalLeads  > 0 ? round($calledLeads / $totalLeads * 100, 1)  : 0;
            $conversionRate = $totalLeads  > 0 ? round($converted   / $totalLeads * 100, 1)  : 0;
            $sameDayRate    = $totalCalls  > 0 ? round($sameDayCalls / $totalCalls * 100, 1) : 0;
            $delayedRate    = $totalCalls  > 0 ? round($delayedCalls / $totalCalls * 100, 1) : 0;

            // Composite score (0–100)
            // call_rate 30%, conversion_rate 40%, same_day_rate 20%, activity 10%
            $activityScore  = min($totalCalls, 100) / 100 * 100;
            $score = round(
                ($callRate       * 0.30) +
                ($conversionRate * 0.40) +
                ($sameDayRate    * 0.20) +
                ($activityScore  * 0.10),
                1
            );

            // Performance tier
            $tier = match (true) {
                $score >= 70 => ['label' => 'ممتاز',   'color' => 'green'],
                $score >= 45 => ['label' => 'جيد',     'color' => 'amber'],
                $score >= 20 => ['label' => 'مقبول',   'color' => 'orange'],
                default      => ['label' => 'ضعيف',    'color' => 'red'],
            };

            return [
                'admin'           => $admin,
                'total_leads'     => $totalLeads,
                'converted'       => $converted,
                'in_progress'     => $inProgress,
                'new_leads'       => $newLeads,
                'archived'        => $archived,
                'total_calls'     => $totalCalls,
                'called_leads'    => $calledLeads,
                'never_called'    => $neverCalled,
                'same_day_calls'  => $sameDayCalls,
                'delayed_calls'   => $delayedCalls,
                'call_rate'       => $callRate,
                'conversion_rate' => $conversionRate,
                'same_day_rate'   => $sameDayRate,
                'delayed_rate'    => $delayedRate,
                'score'           => $score,
                'tier'            => $tier,
            ];
        })
        ->sortByDesc('score')
        ->values();

        // ── Summary totals ───────────────────────────────────────────────────
        $summary = [
            'total_staff'      => $rows->count(),
            'total_leads'      => $rows->sum('total_leads'),
            'total_converted'  => $rows->sum('converted'),
            'total_calls'      => $rows->sum('total_calls'),
            'never_called'     => $rows->sum('never_called'),
            'avg_score'        => $rows->count() > 0 ? round($rows->avg('score'), 1) : 0,
            'top_performer'    => $rows->first(),
        ];

        return view('admin.marketing.staff-performance', compact(
            'rows', 'summary', 'branches', 'branchId', 'period', 'dateStart', 'dateEnd', 'me'
        ));
    }
}
