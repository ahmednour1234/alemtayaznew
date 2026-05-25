<?php

namespace App\Repositories;

use App\Models\Income;
use App\Repositories\Contracts\IncomeRepositoryInterface;

class IncomeRepository implements IncomeRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        return Income::query()
            ->with(['branch', 'incomeType', 'admin'])
            ->when(!empty($filters['branch_id']), fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->when(!empty($filters['income_type_id']), fn($q) => $q->where('income_type_id', $filters['income_type_id']))
            ->when(!empty($filters['payment_method']), fn($q) => $q->where('payment_method', $filters['payment_method']))
            ->when(!empty($filters['date_from']), fn($q) => $q->whereDate('date', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn($q) => $q->whereDate('date', '<=', $filters['date_to']))
            ->latest('date')
            ->paginate(20);
    }

    public function findById(int $id)
    {
        return Income::with(['branch', 'incomeType', 'admin'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Income::create($data);
    }

    public function update(int $id, array $data)
    {
        $income = Income::findOrFail($id);
        $income->update($data);
        return $income;
    }

    public function delete(int $id): bool
    {
        return Income::findOrFail($id)->delete();
    }

    public function restore(int $id): bool
    {
        return Income::withTrashed()->findOrFail($id)->restore();
    }

    public function getTrashed()
    {
        return Income::onlyTrashed()->with(['branch', 'incomeType'])->latest()->get();
    }

    public function getTotalByFilters(array $filters = []): float
    {
        return Income::query()
            ->when(!empty($filters['branch_id']), fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->when(!empty($filters['income_type_id']), fn($q) => $q->where('income_type_id', $filters['income_type_id']))
            ->when(!empty($filters['payment_method']), fn($q) => $q->where('payment_method', $filters['payment_method']))
            ->when(!empty($filters['date_from']), fn($q) => $q->whereDate('date', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn($q) => $q->whereDate('date', '<=', $filters['date_to']))
            ->sum('amount');
    }

    public function getRecent(int $limit = 10)
    {
        return Income::with(['branch', 'incomeType'])->latest('date')->limit($limit)->get();
    }
}
