<?php

namespace App\Repositories;

use App\Models\Housing;
use App\Repositories\Contracts\HousingRepositoryInterface;

class HousingRepository implements HousingRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        return Housing::query()
            ->with(['branch', 'admin'])
            ->when(!empty($filters['search']), fn($q) => $q->where('name', 'like', '%' . $filters['search'] . '%'))
            ->when(isset($filters['active']) && $filters['active'] !== '', fn($q) => $q->where('active', (bool) $filters['active']))
            ->when(!empty($filters['branch_id']), fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->latest()
            ->paginate(20);
    }

    public function getAllActive()
    {
        return Housing::where('active', true)->orderBy('name')->get();
    }

    public function findById(int $id)
    {
        return Housing::with(['branch', 'admin'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Housing::create($data);
    }

    public function update(int $id, array $data)
    {
        $housing = $this->findById($id);
        $housing->update($data);
        return $housing;
    }

    public function delete(int $id): bool
    {
        return (bool) $this->findById($id)->delete();
    }

    public function restore(int $id): bool
    {
        return (bool) Housing::withTrashed()->findOrFail($id)->restore();
    }

    public function getTrashed()
    {
        return Housing::onlyTrashed()->with(['branch', 'admin'])->latest()->get();
    }
}
