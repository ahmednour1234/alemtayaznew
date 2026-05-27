<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Repositories\Contracts\ExpenseRepositoryInterface;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        return Expense::query()
            ->with(['branch', 'expenseType', 'admin', 'approver'])
            ->when(!empty($filters['branch_id']), fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->when(!empty($filters['expense_type_id']), fn($q) => $q->where('expense_type_id', $filters['expense_type_id']))
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['payment_method']), fn($q) => $q->where('payment_method', $filters['payment_method']))
            ->when(!empty($filters['date_from']), fn($q) => $q->whereDate('date', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn($q) => $q->whereDate('date', '<=', $filters['date_to']))
            ->latest('date')
            ->paginate(20);
    }

    public function findById(int $id)
    {
        return Expense::with(['branch', 'expenseType', 'admin', 'approver'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Expense::create($data);
    }

    public function update(int $id, array $data)
    {
        $expense = Expense::findOrFail($id);
        $expense->update($data);
        return $expense;
    }

    public function delete(int $id): bool
    {
        return Expense::findOrFail($id)->delete();
    }

    public function restore(int $id): bool
    {
        return Expense::withTrashed()->findOrFail($id)->restore();
    }

    public function getTrashed()
    {
        return Expense::onlyTrashed()->with(['branch', 'expenseType'])->latest()->get();
    }

    public function getPending(?int $branchId = null)
    {
        return Expense::with(['branch', 'expenseType', 'admin'])
            ->where('status', 'pending')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->get();
    }

    public function getTotalByStatus(string $status, array $filters = []): float
    {
        return Expense::query()
            ->where('status', $status)
            ->when(!empty($filters['branch_id']), fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->when(!empty($filters['date_from']), fn($q) => $q->whereDate('date', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn($q) => $q->whereDate('date', '<=', $filters['date_to']))
            ->sum('amount');
    }

    public function getRecent(int $limit = 10, ?int $branchId = null)
    {
        return Expense::with(['branch', 'expenseType'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest('date')->limit($limit)->get();
    }
}
