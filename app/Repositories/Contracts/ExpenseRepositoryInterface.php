<?php

namespace App\Repositories\Contracts;

interface ExpenseRepositoryInterface
{
    public function getAll(array $filters = []);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): bool;
    public function restore(int $id): bool;
    public function getTrashed();
    public function getPending(?int $branchId = null);
    public function getTotalByStatus(string $status, array $filters = []): float;
    public function getRecent(int $limit = 10);
}
