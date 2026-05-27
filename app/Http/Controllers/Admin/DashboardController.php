<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\ExpenseService;
use App\Services\IncomeService;
use App\Services\TransferService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly IncomeService $incomeService,
        private readonly ExpenseService $expenseService,
        private readonly TransferService $transferService,
    ) {}

    public function index()
    {
        $me       = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        $branchId = $me->isBranchAdmin() ? $me->branch_id : null;

        $stats         = $this->dashboardService->getStats($branchId);
        $chartData     = $this->dashboardService->getChartData($branchId);
        $branchChart   = $this->dashboardService->getBranchComparisonData();
        $recentIncomes = $this->incomeService->recent(5, $branchId);
        $recentExpenses = $this->expenseService->recent(5, $branchId);
        $pendingExpenses  = $this->expenseService->pending($branchId);
        $pendingTransfers = $this->transferService->pending($branchId);

        return view('admin.dashboard.index', compact(
            'stats', 'chartData', 'branchChart',
            'recentIncomes', 'recentExpenses',
            'pendingExpenses', 'pendingTransfers'
        ));
    }
}
