<?php

namespace App\Repositories\Contracts;

interface HousingAssignmentRepositoryInterface
{
    public function getAll(array $filters = []);
    public function getActive(array $filters = []);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): bool;
    public function countActiveByHousing(int $housingId): int;
}
