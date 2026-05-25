<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;

class AdminRepository implements AdminRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        return Admin::query()
            ->with('roles')
            ->when(!empty($filters['search']), fn($q) => $q->where('name', 'like', "%{$filters['search']}%")
                ->orWhere('email', 'like', "%{$filters['search']}%"))
            ->when(isset($filters['active']), fn($q) => $q->where('active', $filters['active']))
            ->latest()
            ->paginate(20);
    }

    public function findById(int $id)
    {
        return Admin::with('roles.permissions')->findOrFail($id);
    }

    public function findByEmail(string $email)
    {
        return Admin::where('email', $email)->first();
    }

    public function create(array $data)
    {
        return Admin::create($data);
    }

    public function update(int $id, array $data)
    {
        $admin = Admin::findOrFail($id);
        $admin->update($data);
        return $admin;
    }

    public function delete(int $id): bool
    {
        return Admin::findOrFail($id)->delete();
    }

    public function restore(int $id): bool
    {
        return Admin::withTrashed()->findOrFail($id)->restore();
    }

    public function getTrashed()
    {
        return Admin::onlyTrashed()->with('roles')->latest()->get();
    }
}
