<?php

namespace App\Repositories;

use App\Models\IncomeType;
use App\Repositories\Contracts\IncomeTypeRepositoryInterface;

class IncomeTypeRepository implements IncomeTypeRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        return IncomeType::query()
            ->when(!empty($filters['search']), fn($q) => $q->where('name', 'like', "%{$filters['search']}%"))
            ->when(isset($filters['active']), fn($q) => $q->where('active', $filters['active']))
            ->latest()
            ->paginate(20);
    }

    public function getAllActive()
    {
        return IncomeType::where('active', true)->orderBy('name')->get();
    }

    public function findById(int $id)
    {
        return IncomeType::findOrFail($id);
    }

    public function findByName(string $name)
    {
        return IncomeType::where('name', $name)->first();
    }

    public function create(array $data)
    {
        return IncomeType::create($data);
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
        return IncomeType::withTrashed()->findOrFail($id)->restore();
    }

    public function getTrashed()
    {
        return IncomeType::onlyTrashed()->latest()->get();
    }
}
