<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Complaint;
use App\Models\Expense;
use App\Models\FinancialTransfer;
use App\Models\Income;
use App\Models\RecruitmentContract;
use App\Models\Worker;

class DashboardService
{
    public function getStats(?int $branchId = null): array
    {
        $now  = now();
        $thisMonth = $now->month;
        $thisYear  = $now->year;
        $lastMonth = $now->copy()->subMonth()->month;
        $lastMonthYear = $now->copy()->subMonth()->year;

        $totalIncome   = Income::when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('amount');
        $totalExpenses = Expense::where('status', 'approved')->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('amount');
        $pendingExpenses  = Expense::where('status', 'pending')->when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();
        $pendingTransfers = FinancialTransfer::where('status', 'pending')
            ->when($branchId, fn($q) => $q->where(fn($q2) =>
                $q2->where('from_branch_id', $branchId)->orWhere('to_branch_id', $branchId)
            ))->count();
        $branchCount   = $branchId ? 1 : Branch::where('active', true)->count();

        // New stats for redesigned dashboard
        $totalWorkers     = Worker::count();
        $activeContracts  = RecruitmentContract::whereNotIn('current_status', [13, 14, 15])->count();
        $pendingContracts = RecruitmentContract::where('current_status', 1)->count();

        // Month-over-month helpers
        $contractsThisMonth = RecruitmentContract::whereYear('created_at', $thisYear)->whereMonth('created_at', $thisMonth)->count();
        $contractsLastMonth = RecruitmentContract::whereYear('created_at', $lastMonthYear)->whereMonth('created_at', $lastMonth)->count();
        $contractsChange    = $contractsLastMonth > 0 ? round((($contractsThisMonth - $contractsLastMonth) / $contractsLastMonth) * 100) : 0;

        $complaintsThisMonth = Complaint::whereYear('created_at', $thisYear)->whereMonth('created_at', $thisMonth)->count();
        $complaintsLastMonth = Complaint::whereYear('created_at', $lastMonthYear)->whereMonth('created_at', $lastMonth)->count();
        $complaintsChange    = $complaintsLastMonth > 0 ? round((($complaintsThisMonth - $complaintsLastMonth) / $complaintsLastMonth) * 100) : 0;

        $completedContracts = RecruitmentContract::where('current_status', 13)->count();
        $completionRate     = ($activeContracts + $completedContracts) > 0
            ? round(($completedContracts / ($activeContracts + $completedContracts)) * 100)
            : 0;

        // Income this month vs last month
        $incomeThisMonth = Income::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereYear('date', $thisYear)->whereMonth('date', $thisMonth)->sum('amount');
        $incomeLastMonth = Income::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereYear('date', $lastMonthYear)->whereMonth('date', $lastMonth)->sum('amount');
        $incomeChange    = $incomeLastMonth > 0 ? round((($incomeThisMonth - $incomeLastMonth) / $incomeLastMonth) * 100) : 0;

        $expensesThisMonth = Expense::where('status', 'approved')->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereYear('date', $thisYear)->whereMonth('date', $thisMonth)->sum('amount');
        $expensesLastMonth = Expense::where('status', 'approved')->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereYear('date', $lastMonthYear)->whereMonth('date', $lastMonth)->sum('amount');
        $expensesChange    = $expensesLastMonth > 0 ? round((($expensesThisMonth - $expensesLastMonth) / $expensesLastMonth) * 100) : 0;

        return [
            'total_income'           => $totalIncome,
            'total_expenses'         => $totalExpenses,
            'net_profit'             => $totalIncome - $totalExpenses,
            'branch_count'           => $branchCount,
            'pending_expenses'       => $pendingExpenses,
            'pending_transfers'      => $pendingTransfers,
            'total_workers'          => $totalWorkers,
            'active_contracts'       => $activeContracts,
            'pending_contracts'      => $pendingContracts,
            'contracts_this_month'   => $contractsThisMonth,
            'contracts_change'       => $contractsChange,
            'complaints_this_month'  => $complaintsThisMonth,
            'complaints_change'      => $complaintsChange,
            'completion_rate'        => $completionRate,
            'income_change'          => $incomeChange,
            'expenses_change'        => $expensesChange,
        ];
    }

    public function getChartData(?int $branchId = null): array
    {
        $months = [];
        $incomes = [];
        $expenses = [];

        for ($m = 1; $m <= 12; $m++) {
            $months[] = \Carbon\Carbon::create(null, $m)->translatedFormat('M');
            $incomes[]  = Income::when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->whereYear('date', now()->year)->whereMonth('date', $m)->sum('amount');
            $expenses[] = Expense::where('status', 'approved')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->whereYear('date', now()->year)->whereMonth('date', $m)->sum('amount');
        }

        return compact('months', 'incomes', 'expenses');
    }

    public function getBranchComparisonData(): array
    {
        $branches = Branch::where('active', true)->get();
        $year     = now()->year;

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[] = \Carbon\Carbon::create($year, $m)->translatedFormat('M');
        }

        $palette = ['#2563eb', '#16a34a', '#f97316', '#9333ea', '#ef4444', '#ca8a04', '#0891b2', '#db2777'];

        $datasets = [];
        foreach ($branches as $i => $branch) {
            $monthlyData = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthlyData[] = (float) Income::where('branch_id', $branch->id)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $m)
                    ->sum('amount');
            }
            $color = $palette[$i % count($palette)];
            $datasets[] = [
                'label'           => $branch->name,
                'data'            => $monthlyData,
                'borderColor'     => $color,
                'backgroundColor' => $color . '22',
                'borderWidth'     => 2.5,
                'tension'         => 0.4,
                'pointRadius'     => 4,
                'pointHoverRadius'=> 6,
                'fill'            => false,
            ];
        }

        return ['labels' => $months, 'datasets' => $datasets];
    }
}
