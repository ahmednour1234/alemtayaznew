<?php

namespace App\Repositories;

use App\Models\Branch;
use App\Repositories\Contracts\BranchRepositoryInterface;

class BranchRepository implements BranchRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        return Branch::query()
            ->when(!empty($filters['search']), fn($q) => $q->where('name', 'like', "%{$filters['search']}%")
                ->orWhere('code', 'like', "%{$filters['search']}%"))
            ->when(isset($filters['active']), fn($q) => $q->where('active', $filters['active']))
            ->when(!empty($filters['city']), fn($q) => $q->where('city', $filters['city']))
            ->latest()
            ->paginate(20);
    }

    public function getAllActive()
    {
        return Branch::where('active', true)->orderBy('name')->get();
    }

    public function findById(int $id)
    {
        return Branch::findOrFail($id);
    }

    public function findByCode(string $code)
    {
        return Branch::where('code', $code)->first();
    }

    public function create(array $data)
    {
        return Branch::create($data);
    }

    public function update(int $id, array $data)
    {
        $branch = $this->findById($id);
        $branch->update($data);
        return $branch;
    }

    public function delete(int $id): bool
    {
        return $this->findById($id)->delete();
    }

    public function restore(int $id): bool
    {
        return Branch::withTrashed()->findOrFail($id)->restore();
    }

    public function getTrashed()
    {
        return Branch::onlyTrashed()->latest()->get();
    }
}
