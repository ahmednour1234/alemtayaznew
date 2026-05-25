<?php

namespace App\Services;

use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function __construct(
        private readonly AdminRepositoryInterface $repo,
        private readonly RoleRepositoryInterface $roleRepo,
    ) {}

    public function list(array $filters = [])
    {
        return $this->repo->getAll($filters);
    }

    public function find(int $id)
    {
        return $this->repo->findById($id);
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $roles = $data['roles'] ?? [];
            unset($data['roles']);

            $data['password'] = Hash::make($data['password']);
            $admin = $this->repo->create($data);
            $admin->roles()->sync($roles);
            return $admin;
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $roles = $data['roles'] ?? null;
            unset($data['roles']);

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $admin = $this->repo->update($id, $data);

            if ($roles !== null) {
                $admin->roles()->sync($roles);
            }

            return $admin;
        });
    }

    public function destroy(int $id): bool
    {
        return DB::transaction(fn() => $this->repo->delete($id));
    }

    public function restore(int $id): bool
    {
        return DB::transaction(fn() => $this->repo->restore($id));
    }

    public function trashed()
    {
        return $this->repo->getTrashed();
    }

    public function toggleActive(int $id)
    {
        $admin = $this->repo->findById($id);
        return $this->repo->update($id, ['active' => ! $admin->active]);
    }
}
