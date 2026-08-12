<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\CombinedStatementImport;
use App\Imports\FlexibleStatementImport;
use App\Exports\CombinedStatementTemplateExport;
use App\Services\DashboardService;
use App\Services\ExpenseService;
use App\Services\IncomeService;
use App\Services\ScheduledTasksRunner;
use App\Services\TransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly IncomeService $incomeService,
        private readonly ExpenseService $expenseService,
        private readonly TransferService $transferService,
        private readonly ScheduledTasksRunner $scheduledTasks,
    ) {}

    public function index()
    {
        // بديل cron: تشغيل المهام المجدولة عند دخول لوحة التحكم (محكوم بمهلة لكل أمر)
        $this->scheduledTasks->runDue();

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

    public function approveAllPending()
    {
        $admin = Auth::guard('admin')->user();
        if (! $admin->isSuperAdmin() && ! $admin->hasPermission('expenses.approve')) {
            abort(403, 'ليس لديك صلاحية الموافقة على الطلبات.');
        }

        $branchId = $admin->isBranchAdmin() ? $admin->branch_id : null;

        foreach ($this->expenseService->pending($branchId) as $expense) {
            try {
                $this->expenseService->approve($expense->id, $admin);
            } catch (\RuntimeException) {
                // already processed — skip
            }
        }

        foreach ($this->transferService->pending($branchId) as $transfer) {
            try {
                $this->transferService->approve($transfer->id, $admin);
            } catch (\RuntimeException) {
                // already processed — skip
            }
        }

        return back()->with('success', 'تم الموافقة على جميع الطلبات المعلقة.');
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

    public function importStatementPage()
    {
        return view('admin.combined-import.index');
    }

    public function importStatementTemplate()
    {
        return Excel::download(new CombinedStatementTemplateExport(), 'combined_statement_template.xlsx');
    }

    public function importStatement(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new CombinedStatementImport(), $request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = collect($e->failures())->map(
                fn($f) => 'صف ' . $f->row() . ': ' . implode(', ', $f->errors())
            )->join(' | ');
            return back()->withErrors(['file' => $failures]);
        }

        return back()->with('success', 'تم استيراد البيانات بنجاح.');
    }

    public function importFlexible(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        $import = new FlexibleStatementImport();

        try {
            Excel::import($import, $request->file('file'));
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'حدث خطأ أثناء قراءة الملف: ' . $e->getMessage()]);
        }

        $msg = 'تم الاستيراد بنجاح — '
            . 'إيرادات: ' . $import->incomeCount
            . ' | مصروفات: ' . $import->expenseCount;

        if ($import->skippedCount > 0) {
            $msg .= ' | تم تجاهل: ' . $import->skippedCount . ' صف';
        }

        if (! empty($import->errors)) {
            session()->flash('import_warnings', $import->errors);
        }

        return back()->with('success', $msg);
    }
}

