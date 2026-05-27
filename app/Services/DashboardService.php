<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\FinancialTransfer;
use App\Models\Income;

class DashboardService
{
    public function getStats(?int $branchId = null): array
    {
        $totalIncome   = Income::when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('amount');
        $totalExpenses = Expense::where('status', 'approved')->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('amount');
        $pendingExpenses  = Expense::where('status', 'pending')->when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();
        $pendingTransfers = FinancialTransfer::where('status', 'pending')
            ->when($branchId, fn($q) => $q->where(fn($q2) =>
                $q2->where('from_branch_id', $branchId)->orWhere('to_branch_id', $branchId)
            ))->count();
        $branchCount   = $branchId ? 1 : Branch::where('active', true)->count();

        return [
            'total_income'      => $totalIncome,
            'total_expenses'    => $totalExpenses,
            'net_profit'        => $totalIncome - $totalExpenses,
            'branch_count'      => $branchCount,
            'pending_expenses'  => $pendingExpenses,
            'pending_transfers' => $pendingTransfers,
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
