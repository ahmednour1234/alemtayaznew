<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Services\BranchService;
use App\Services\ReportService;
use Illuminate\Http\Request;
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
        $branches = $this->branchService->allActive();
        $data     = null;

        if ($request->filled('branch_id')) {
            $data = $this->reportService->getBranchStatement(
                (int) $request->branch_id,
                $request->date_from,
                $request->date_to
            );
        }

        return view('admin.reports.branch-statement', compact('branches', 'data'));
    }

    public function export(Request $request)
    {
        $request->validate(['branch_id' => ['required', 'exists:branches,id']]);
        $data = $this->reportService->getBranchStatement(
            (int) $request->branch_id,
            $request->date_from,
            $request->date_to
        );
        return Excel::download(new BranchStatementExport($data), 'branch_statement_' . now()->format('Y-m-d') . '.xlsx');
    }
}
