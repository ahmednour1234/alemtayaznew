<?php

namespace App\Repositories;

use App\Models\Permission;
use App\Repositories\Contracts\PermissionRepositoryInterface;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function getAll()
    {
        return Permission::orderBy('slug')->get();
    }

    public function findById(int $id)
    {
        return Permission::findOrFail($id);
    }

    public function create(array $data)
    {
        return Permission::create($data);
    }

    public function update(int $id, array $data)
    {
        $permission = Permission::findOrFail($id);
        $permission->update($data);
        return $permission;
    }

    public function delete(int $id): bool
    {
        return Permission::findOrFail($id)->delete();
    }
}
