<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractReportsController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    // ── إحصائيات وتحليلات العقود ────────────────────────────────────────────
    public function stats(Request $request)
    {
        $me       = Auth::guard('admin')->user();
        $branchId = $me->isBranchAdmin() ? $me->branch_id : ($request->branch_id ?: null);
        $branches = $me->isBranchAdmin()
            ? collect()
            : Branch::where('active', true)->orderBy('name')->get(['id', 'name']);

        $stats = $this->reportService->getContractStats($branchId ? (int) $branchId : null);

        return view('admin.reports.contracts-stats', compact('stats', 'branches', 'branchId'));
    }

    // ── تقرير العمالة المستلمة ───────────────────────────────────────────────
    public function received(Request $request)
    {
        $me       = Auth::guard('admin')->user();
        $branchId = $me->isBranchAdmin() ? $me->branch_id : ($request->branch_id ?: null);
        $branches = $me->isBranchAdmin()
            ? collect()
            : Branch::where('active', true)->orderBy('name')->get(['id', 'name']);

        $contracts = $this->reportService->getReceivedContracts(
            $request->date_from ?: null,
            $request->date_to   ?: null,
            $branchId ? (int) $branchId : null,
        );

        return view('admin.reports.contracts-received', compact('contracts', 'branches', 'branchId'));
    }

    // ── تقرير العقود المتأخرة ────────────────────────────────────────────────
    public function delayed(Request $request)
    {
        $me       = Auth::guard('admin')->user();
        $branchId = $me->isBranchAdmin() ? $me->branch_id : ($request->branch_id ?: null);
        $branches = $me->isBranchAdmin()
            ? collect()
            : Branch::where('active', true)->orderBy('name')->get(['id', 'name']);

        $contracts = $this->reportService->getDelayedContracts(
            $branchId ? (int) $branchId : null,
        );

        return view('admin.reports.contracts-delayed', compact('contracts', 'branches', 'branchId'));
    }
}
