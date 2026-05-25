<?php

namespace App\Services;

use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function __construct(
        private readonly RoleRepositoryInterface $repo,
        private readonly PermissionRepositoryInterface $permRepo,
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
            return $role;
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $permissions = $data['permissions'] ?? [];
            unset($data['permissions']);

            $role = $this->repo->update($id, $data);
            $this->repo->syncPermissions($id, $permissions);
            return $role;
        });
    }

    public function destroy(int $id): bool
    {
        return DB::transaction(fn() => $this->repo->delete($id));
    }
}
