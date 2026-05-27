<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Services\BranchService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BranchStatementExport;

class BranchStatementController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
        private readonly BranchService $branchService,
    ) {}

    public function index(Request $request)
    {
        $me       = Auth::guard('admin')->user();
        $branches = $me->isBranchAdmin()
            ? $this->branchService->allActive()->where('id', $me->branch_id)
            : $this->branchService->allActive();
        $report   = null;

        // Branch admins are auto-directed to their own branch
        $branchId = $me->isBranchAdmin() ? $me->branch_id : $request->branch_id;

        if ($branchId) {
            $report = $this->reportService->getBranchStatement(
                (int) $branchId,
                $request->date_from,
                $request->date_to
            );
        }

        return view('admin.reports.branch-statement', compact('branches', 'report'));
    }

    public function export(Request $request)
    {
        $me       = Auth::guard('admin')->user();
        $branchId = $me->isBranchAdmin() ? $me->branch_id : $request->branch_id;
        $request->validate(['branch_id' => ['nullable', 'exists:branches,id']]);
        $data = $this->reportService->getBranchStatement(
            (int) $branchId,
            $request->date_from,
            $request->date_to
        );
        return Excel::download(new BranchStatementExport($data), 'branch_statement_' . now()->format('Y-m-d') . '.xlsx');
    }
}
