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
                                          ->where('created_at', '<=', now()->subDays(7))
                                          ->count(),
        ];

        // ── Branch performance ───────────────────────────────────────────────
        $branchPerformance = (clone $base)
            ->select(
                'branch_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status='resolved' THEN 1 ELSE 0 END) as resolved"),
                DB::raw("SUM(CASE WHEN status='closed' THEN 1 ELSE 0 END) as closed"),
                DB::raw("SUM(CASE WHEN status IN ('new','in_progress') THEN 1 ELSE 0 END) as open"),
                DB::raw("SUM(CASE WHEN status IN ('new','in_progress') AND created_at <= datetime('now','-7 day') THEN 1 ELSE 0 END) as stale"),
                DB::raw('AVG(CASE WHEN resolved_at IS NOT NULL THEN (julianday(resolved_at) - julianday(created_at)) END) as avg_resolution_days')
            )
            ->whereNotNull('branch_id')
            ->groupBy('branch_id')
            ->with('branch:id,name')
            ->get();

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
