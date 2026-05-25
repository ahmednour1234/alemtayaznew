<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\FinancialTransfer;
use App\Models\Income;

class ReportService
{
    public function getBranchStatement(int $branchId, ?string $dateFrom, ?string $dateTo): array
    {
        $incomes = Income::with(['incomeType', 'admin'])
            ->where('branch_id', $branchId)
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->orderBy('date')
            ->get();

        $expenses = Expense::with(['expenseType', 'admin'])
            ->where('branch_id', $branchId)
            ->where('status', 'approved')
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->orderBy('date')
            ->get();

        $transfersOut = FinancialTransfer::with(['toBranch'])
            ->where('from_branch_id', $branchId)
            ->where('status', 'approved')
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->orderBy('date')
            ->get();

        $transfersIn = FinancialTransfer::with(['fromBranch'])
            ->where('to_branch_id', $branchId)
            ->where('status', 'approved')
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->orderBy('date')
            ->get();

        $totalIncome      = $incomes->sum('amount') + $transfersIn->sum('amount');
        $totalExpenses    = $expenses->sum('amount') + $transfersOut->sum('amount');

        return [
            'branch'         => Branch::findOrFail($branchId),
            'incomes'        => $incomes,
            'expenses'       => $expenses,
            'transfers_out'  => $transfersOut,
            'transfers_in'   => $transfersIn,
            'total_income'   => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_balance'    => $totalIncome - $totalExpenses,
            'date_from'      => $dateFrom,
            'date_to'        => $dateTo,
        ];
    }

    public function getIncomeStatement(array $branchIds, ?string $dateFrom, ?string $dateTo): array
    {
        $query = Branch::query();
        if (!empty($branchIds)) {
            $query->whereIn('id', $branchIds);
        }
        $branches = $query->where('active', true)->get();

        $rows = $branches->map(function ($branch) use ($dateFrom, $dateTo) {
            $totalIncome = Income::where('branch_id', $branch->id)
                ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
                ->sum('amount');

            $totalApprovedExpenses = Expense::where('branch_id', $branch->id)
                ->where('status', 'approved')
                ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
                ->sum('amount');

            $pendingExpenses = Expense::where('branch_id', $branch->id)
                ->where('status', 'pending')
                ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
                ->sum('amount');

            return [
                'branch'            => $branch,
                'total_income'      => $totalIncome,
                'total_expenses'    => $totalApprovedExpenses,
                'net_profit'        => $totalIncome - $totalApprovedExpenses,
                'pending_expenses'  => $pendingExpenses,
            ];
        });

        return [
            'rows'      => $rows,
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
            'totals'    => [
                'income'   => $rows->sum('total_income'),
                'expenses' => $rows->sum('total_expenses'),
                'net'      => $rows->sum('net_profit'),
            ],
        ];
    }
}
