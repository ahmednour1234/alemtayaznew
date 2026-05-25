<?php

namespace App\Repositories\Contracts;

interface RoleRepositoryInterface
{
    public function getAll();
    public function getAllActive();
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): bool;
    public function syncPermissions(int $roleId, array $permissionIds): void;
}
