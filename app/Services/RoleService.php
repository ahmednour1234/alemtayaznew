<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Services\Security\SecurityLogger;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function __construct(
        private readonly RoleRepositoryInterface $repo,
        private readonly PermissionRepositoryInterface $permRepo,
        private readonly SecurityLogger $security,
    ) {}

    public function list()
    {
        return $this->repo->getAll();
    }

    public function allActive()
    {
        return $this->repo->getAllActive();
    }

    public function find(int $id)
    {
        return $this->repo->findById($id);
    }

    public function allPermissions()
    {
        return $this->permRepo->getAll();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $permissions = $data['permissions'] ?? [];
            unset($data['permissions']);

            $role = $this->repo->create($data);
            $this->repo->syncPermissions($role->id, $permissions);

            $this->security->logPermissionChange('created', [
                'role_id' => $role->id,
                'after'   => ['name' => $role->name, 'slug' => $role->slug, 'permissions' => array_values($permissions)],
            ]);

            return $role;
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $permissions = $data['permissions'] ?? [];
            unset($data['permissions']);

            $beforePerms = Role::with('permissions:id')->find($id)?->permissions->pluck('id')->all() ?? [];

            $role = $this->repo->update($id, $data);
            $this->repo->syncPermissions($id, $permissions);

            $this->security->logPermissionChange('updated', [
                'role_id' => $id,
                'before'  => ['permissions' => $beforePerms],
                'after'   => ['permissions' => array_values($permissions)],
            ]);

            return $role;
        });
    }

    public function destroy(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $before = Role::with('permissions:id')->find($id);
            $result = $this->repo->delete($id);

            $this->security->logPermissionChange('deleted', [
                'role_id' => $id,
                'before'  => ['name' => $before?->name, 'permissions' => $before?->permissions->pluck('id')->all() ?? []],
            ]);

            return $result;
        });
    }
}
