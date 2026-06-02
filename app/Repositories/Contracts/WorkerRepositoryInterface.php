<?php

namespace App\Repositories\Contracts;

interface WorkerRepositoryInterface
{
    public function getAll(array $filters = []): mixed;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): void;
    public function restore(int $id): void;
    public function getTrashed(): mixed;
    public function assignToClient(int $id, int $clientId, int $assignedByAdminId): void;
    public function unassign(int $id): void;
    public function findDuplicateCv(?string $passportNumber, ?string $originalCvName): mixed;
}
