<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComplaintReportsController extends Controller
{
    public function index(Request $request)
    {
        $me = Auth::guard('admin')->user();

        $dateFrom = $request->date_from ?: now()->subMonths(3)->toDateString();
        $dateTo   = $request->date_to   ?: now()->toDateString();
        $branchId = $me && $me->isBranchAdmin() ? $me->branch_id : $request->branch_id;

        $base = Complaint::query()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        $staleCutoff = now()->subDays(7);

        // ── Summary ──────────────────────────────────────────────────────────
        $summary = [
            'total'       => (clone $base)->count(),
            'new'         => (clone $base)->where('status', 'new')->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'resolved'    => (clone $base)->where('status', 'resolved')->count(),
            'closed'      => (clone $base)->where('status', 'closed')->count(),
            'escalated'   => (clone $base)->where('status', 'escalated')->count(),
            'on_musaned'  => (clone $base)->where('on_musaned', true)->count(),
            'stale'       => (clone $base)->whereIn('status', ['new', 'in_progress'])
                                          ->where('created_at', '<=', $staleCutoff)
                                          ->count(),
        ];

        // ── Branch performance ───────────────────────────────────────────────
        $branchPerformance = (clone $base)
            ->with('branch:id,name')
            ->whereNotNull('branch_id')
            ->get(['id', 'branch_id', 'status', 'created_at', 'resolved_at'])
            ->groupBy('branch_id')
            ->map(function ($complaints) use ($staleCutoff) {
                $resolvedDurations = $complaints
                    ->filter(fn($complaint) => $complaint->resolved_at !== null)
                    ->map(fn($complaint) => $complaint->created_at->diffInDays($complaint->resolved_at));

                return (object) [
                    'branch' => $complaints->first()->branch,
                    'total' => $complaints->count(),
                    'resolved' => $complaints->where('status', 'resolved')->count(),
                    'closed' => $complaints->where('status', 'closed')->count(),
                    'open' => $complaints->whereIn('status', ['new', 'in_progress'])->count(),
                    'stale' => $complaints
                        ->whereIn('status', ['new', 'in_progress'])
                        ->filter(fn($complaint) => $complaint->created_at->lte($staleCutoff))
                        ->count(),
                    'avg_resolution_days' => $resolvedDurations->isNotEmpty() ? $resolvedDurations->avg() : null,
                ];
            })
            ->values();

        // ── By problem type ──────────────────────────────────────────────────
        $byProblem = (clone $base)
            ->select('problem_type', DB::raw('COUNT(*) as total'))
            ->groupBy('problem_type')
            ->pluck('total', 'problem_type');

        // ── By priority ──────────────────────────────────────────────────────
        $byPriority = (clone $base)
            ->select('priority', DB::raw('COUNT(*) as total'))
            ->groupBy('priority')
            ->pluck('total', 'priority');

        // ── Trend (last N days) ──────────────────────────────────────────────
        $trend = (clone $base)
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as total'))
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('total', 'd');

        $branches = Branch::where('active', true)->orderBy('name')->get();

        return view('admin.complaints.reports', compact(
            'summary', 'branchPerformance', 'byProblem', 'byPriority', 'trend',
            'branches', 'dateFrom', 'dateTo', 'branchId'
        ));
    }
}
