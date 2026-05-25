<?php

namespace App\Repositories\Contracts;

interface AdminRepositoryInterface
{
    public function getAll(array $filters = []);
    public function findById(int $id);
    public function findByEmail(string $email);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): bool;
    public function restore(int $id): bool;
    public function getTrashed();
}
