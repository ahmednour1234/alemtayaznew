<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\FinancialTransfer;
use App\Models\Income;

class DashboardService
{
    public function getStats(): array
    {
        $totalIncome   = Income::sum('amount');
        $totalExpenses = Expense::where('status', 'approved')->sum('amount');
        $pendingExpenses  = Expense::where('status', 'pending')->count();
        $pendingTransfers = FinancialTransfer::where('status', 'pending')->count();
        $branchCount   = Branch::where('active', true)->count();

        return [
            'total_income'        => $totalIncome,
            'total_expenses'      => $totalExpenses,
            'net_profit'          => $totalIncome - $totalExpenses,
            'branch_count'        => $branchCount,
            'pending_expenses'    => $pendingExpenses,
            'pending_transfers'   => $pendingTransfers,
        ];
    }

    public function getChartData(): array
    {
        // Monthly income vs expense for current year
        $months = [];
        $incomes = [];
        $expenses = [];

        for ($m = 1; $m <= 12; $m++) {
            $months[] = \Carbon\Carbon::create(null, $m)->translatedFormat('M');
            $incomes[]  = Income::whereYear('date', now()->year)->whereMonth('date', $m)->sum('amount');
            $expenses[] = Expense::where('status', 'approved')->whereYear('date', now()->year)->whereMonth('date', $m)->sum('amount');
        }

        return compact('months', 'incomes', 'expenses');
    }

    public function getBranchComparisonData(): array
    {
        $branches = Branch::where('active', true)->with([
            'incomes' => fn($q) => $q->whereYear('date', now()->year),
            'expenses' => fn($q) => $q->where('status', 'approved')->whereYear('date', now()->year),
        ])->get();

        return [
            'labels'   => $branches->pluck('name')->toArray(),
            'incomes'  => $branches->map(fn($b) => $b->incomes->sum('amount'))->toArray(),
            'expenses' => $branches->map(fn($b) => $b->expenses->sum('amount'))->toArray(),
        ];
    }
}
