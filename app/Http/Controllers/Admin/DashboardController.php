<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\ExpenseService;
use App\Services\IncomeService;
use App\Services\TransferService;
use Illuminate\Support\Facades\Auth;

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
        $contractsChart = $this->dashboardService->getContractsMonthlyData();
        $statusChart    = $this->dashboardService->getContractsByStatusData();
        $campaignsChart = $this->dashboardService->getCampaignsChartData();
        $sponsorshipChart = $this->dashboardService->getSponsorshipTransfersChart();
        $tomorrowTrips    = $this->dashboardService->getTomorrowTrips();
        $housingStats     = $this->dashboardService->getHousingStats();
        $complaintsChart  = $this->dashboardService->getComplaintsMonthlyData();
        $recentIncomes = $this->incomeService->recent(5, $branchId);
        $recentExpenses = $this->expenseService->recent(5, $branchId);
        $pendingExpenses  = $this->expenseService->pending($branchId);
        $pendingTransfers = $this->transferService->pending($branchId);

        return view('admin.dashboard.index', compact(
            'stats', 'chartData', 'branchChart',
            'contractsChart', 'statusChart', 'campaignsChart',
            'sponsorshipChart', 'tomorrowTrips', 'housingStats', 'complaintsChart',
            'recentIncomes', 'recentExpenses',
            'pendingExpenses', 'pendingTransfers'
        ));
    }

    public function rejectAllPending()
    {
        $admin = Auth::guard('admin')->user();
        if (! $admin->isSuperAdmin() && ! $admin->hasPermission('expenses.approve')) {
            abort(403, 'ليس لديك صلاحية رفض الطلبات.');
        }

        $branchId = $admin->isBranchAdmin() ? $admin->branch_id : null;
        $reason   = 'رفض جماعي من لوحة التحكم';

        foreach ($this->expenseService->pending($branchId) as $expense) {
            try {
                $this->expenseService->reject($expense->id, $admin, $reason);
            } catch (\RuntimeException) {
                // already processed — skip
            }
        }

        foreach ($this->transferService->pending($branchId) as $transfer) {
            try {
                $this->transferService->reject($transfer->id, $admin, $reason);
            } catch (\RuntimeException) {
                // already processed — skip
            }
        }

        return back()->with('success', 'تم رفض جميع الطلبات المعلقة.');
    }
}
