<?php

namespace App\Repositories\Contracts;

interface BranchRepositoryInterface
{
    public function getAll(array $filters = []);
    public function getAllActive();
    public function findById(int $id);
    public function findByCode(string $code);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): bool;
    public function restore(int $id): bool;
    public function getTrashed();
}
