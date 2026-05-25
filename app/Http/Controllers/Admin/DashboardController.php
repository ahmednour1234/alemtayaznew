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
        $stats         = $this->dashboardService->getStats();
        $chartData     = $this->dashboardService->getChartData();
        $branchChart   = $this->dashboardService->getBranchComparisonData();
        $recentIncomes = $this->incomeService->recent(5);
        $recentExpenses = $this->expenseService->recent(5);
        $pendingExpenses = $this->expenseService->pending();
        $pendingTransfers = $this->transferService->pending();

        return view('admin.dashboard.index', compact(
            'stats', 'chartData', 'branchChart',
            'recentIncomes', 'recentExpenses',
            'pendingExpenses', 'pendingTransfers'
        ));
    }
}
