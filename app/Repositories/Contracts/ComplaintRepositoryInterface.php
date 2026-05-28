<?php

namespace App\Repositories\Contracts;

interface ComplaintRepositoryInterface
{
    public function getAll(array $filters = []);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): void;
    public function restore(int $id): void;
    public function getTrashed();
    public function getStaleNew(int $days = 7);
}
