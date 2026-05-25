<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    public function getAll()
    {
        return Role::with('permissions')->get();
    }

    public function getAllActive()
    {
        return Role::where('active', true)->orderBy('name')->get();
    }

    public function findById(int $id)
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Role::create($data);
    }

    public function update(int $id, array $data)
    {
        $role = Role::findOrFail($id);
        $role->update($data);
        return $role;
    }

    public function delete(int $id): bool
    {
        return Role::findOrFail($id)->delete();
    }

    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        Role::findOrFail($roleId)->permissions()->sync($permissionIds);
    }
}
