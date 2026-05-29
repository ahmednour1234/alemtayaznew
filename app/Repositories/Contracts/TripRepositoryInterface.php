<?php

namespace App\Repositories\Contracts;

interface TripRepositoryInterface
{
    public function getAll(array $filters = []);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): bool;
    public function getUpcoming(?int $branchId, int $days = 7);
}
