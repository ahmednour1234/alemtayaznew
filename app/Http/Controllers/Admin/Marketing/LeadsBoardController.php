<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadsBoardController extends Controller
{
    public function index(Request $request)
    {
        $me = Auth::guard('admin')->user();

        // ── Fetch branches ────────────────────────────────────────────────────
        $branchesQuery = Branch::where('active', true)->orderBy('name');

        if ($me->isBranchAdmin()) {
            $branchesQuery->where('id', $me->branch_id);
        }

        $branches = $branchesQuery->get()->map(function (Branch $branch) {
            // Lead counts per status
            $base = Lead::where('branch_id', $branch->id);

            $branch->leads_total       = (clone $base)->count();
            $branch->leads_new         = (clone $base)->where('status', 'new')->count();
            $branch->leads_in_progress = (clone $base)->where('status', 'in_progress')->count();
            $branch->leads_converted   = (clone $base)->where('status', 'converted')->count();
            $branch->leads_archived    = (clone $base)->where('status', 'archived')->count();

            // Stale: new & no contact for > 1 day
            $branch->leads_stale = (clone $base)
                ->where('status', 'new')
                ->where('created_at', '<=', now()->subDay())
                ->whereNull('last_contacted_at')
                ->count();

            // Critical: any open lead with no contact for > 4 days
            $branch->leads_critical = (clone $base)
                ->whereIn('status', ['new', 'in_progress'])
                ->where('created_at', '<=', now()->subDays(4))
                ->whereNull('last_contacted_at')
                ->count();

            // CS staff distribution
            $branch->cs_staff = Admin::where('branch_id', $branch->id)
                ->where('department', 'customer_service')
                ->where('active', true)
                ->orderBy('name')
                ->get()
                ->map(function (Admin $admin) {
                    $admin->active_leads = Lead::where('assigned_admin_id', $admin->id)
                        ->whereIn('status', ['new', 'in_progress'])
                        ->count();
                    $admin->converted_leads = Lead::where('assigned_admin_id', $admin->id)
                        ->where('status', 'converted')
                        ->count();
                    return $admin;
                });

            // Unassigned leads in this branch
            $branch->unassigned_leads = (clone $base)
                ->whereIn('status', ['new', 'in_progress'])
                ->whereNull('assigned_admin_id')
                ->count();

            return $branch;
        });

        // ── Summary ───────────────────────────────────────────────────────────
        $summary = [
            'total'    => $branches->sum('leads_total'),
            'new'      => $branches->sum('leads_new'),
            'progress' => $branches->sum('leads_in_progress'),
            'converted'=> $branches->sum('leads_converted'),
            'stale'    => $branches->sum('leads_stale'),
            'critical' => $branches->sum('leads_critical'),
        ];

        return view('admin.marketing.leads-board', compact('branches', 'summary', 'me'));
    }

    /**
     * Auto-distribute unassigned leads in a branch among its CS staff.
     * Called via POST /marketing/leads-board/{branch}/auto-assign
     */
    public function autoAssign(Branch $branch)
    {
        $csStaff = Admin::where('branch_id', $branch->id)
            ->where('department', 'customer_service')
            ->where('active', true)
            ->get();

        if ($csStaff->isEmpty()) {
            return back()->with('error', 'لا يوجد موظفو خدمة عملاء في هذا الفرع');
        }

        $unassigned = Lead::where('branch_id', $branch->id)
            ->whereIn('status', ['new', 'in_progress'])
            ->whereNull('assigned_admin_id')
            ->get();

        $assigned = 0;
        foreach ($unassigned as $lead) {
            // Pick the CS staff member with fewest active leads
            $assignee = $csStaff->sortBy(function ($admin) {
                return Lead::where('assigned_admin_id', $admin->id)
                    ->whereIn('status', ['new', 'in_progress'])
                    ->count();
            })->first();

            $lead->update(['assigned_admin_id' => $assignee->id]);

            \App\Models\AdminNotification::create([
                'admin_id' => $assignee->id,
                'type'     => 'lead_assigned',
                'title'    => 'تم تعيين عميل محتمل جديد لك',
                'body'     => 'العميل: ' . $lead->name . ($lead->phone ? ' — ' . $lead->phone : ''),
                'url'      => route('admin.marketing.leads.show', $lead),
            ]);

            $assigned++;
        }

        return back()->with('success', "تم توزيع {$assigned} عميل محتمل على موظفي خدمة العملاء");
    }
}
