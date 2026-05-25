<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Services\BranchService;
use App\Services\ReportService;
use Illuminate\Http\Request;
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
        $branches   = $this->branchService->allActive();
        $reportData = null;

        if ($request->isMethod('get') && ($request->filled('date_from') || $request->filled('branches'))) {
            $reportData = $this->reportService->getIncomeStatement(
                $request->input('branches', []),
                $request->date_from,
                $request->date_to
            );
        }

        return view('admin.reports.income-statement', compact('branches', 'reportData'));
    }

    public function export(Request $request)
    {
        $reportData = $this->reportService->getIncomeStatement(
            $request->input('branches', []),
            $request->date_from,
            $request->date_to
        );
        return Excel::download(new IncomeStatementExport($reportData), 'income_statement_' . now()->format('Y-m-d') . '.xlsx');
    }
}
