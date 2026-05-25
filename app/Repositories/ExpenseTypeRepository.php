<?php

namespace App\Repositories;

use App\Models\ExpenseType;
use App\Repositories\Contracts\ExpenseTypeRepositoryInterface;

class ExpenseTypeRepository implements ExpenseTypeRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        return ExpenseType::query()
            ->when(!empty($filters['search']), fn($q) => $q->where('name', 'like', "%{$filters['search']}%"))
            ->when(isset($filters['active']), fn($q) => $q->where('active', $filters['active']))
            ->latest()
            ->paginate(20);
    }

    public function getAllActive()
    {
        return ExpenseType::where('active', true)->orderBy('name')->get();
    }

    public function findById(int $id)
    {
        return ExpenseType::findOrFail($id);
    }

    public function findByName(string $name)
    {
        return ExpenseType::where('name', $name)->first();
    }

    public function create(array $data)
    {
        return ExpenseType::create($data);
    }

    public function update(int $id, array $data)
    {
        $type = $this->findById($id);
        $type->update($data);
        return $type;
    }

    public function delete(int $id): bool
    {
        return $this->findById($id)->delete();
    }

    public function restore(int $id): bool
    {
        return ExpenseType::withTrashed()->findOrFail($id)->restore();
    }

    public function getTrashed()
    {
        return ExpenseType::onlyTrashed()->latest()->get();
    }
}
