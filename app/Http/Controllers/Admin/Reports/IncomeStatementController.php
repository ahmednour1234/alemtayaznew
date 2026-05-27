<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Services\BranchService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncomeStatementExport;

class IncomeStatementController extends Controller
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
        $reportData = null;

        // Branch admins always report on their own branch only
        $branchIds = $me->isBranchAdmin()
            ? [$me->branch_id]
            : $request->input('branch_ids', []);

        if ($request->isMethod('get') && ($request->filled('date_from') || $request->filled('branch_ids') || $me->isBranchAdmin())) {
            $reportData = $this->reportService->getIncomeStatement(
                $branchIds,
                $request->date_from,
                $request->date_to
            );
        }

        return view('admin.reports.income-statement', ['branches' => $branches, 'report' => $reportData]);
    }

    public function export(Request $request)
    {
        $me        = Auth::guard('admin')->user();
        $branchIds = $me->isBranchAdmin()
            ? [$me->branch_id]
            : $request->input('branch_ids', []);
        $reportData = $this->reportService->getIncomeStatement(
            $branchIds,
            $request->date_from,
            $request->date_to
        );
        return Excel::download(new IncomeStatementExport($reportData), 'income_statement_' . now()->format('Y-m-d') . '.xlsx');
    }
}
