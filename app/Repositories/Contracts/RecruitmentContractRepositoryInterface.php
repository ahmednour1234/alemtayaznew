<?php

namespace App\Repositories\Contracts;

use App\Models\RecruitmentContract;
use Illuminate\Pagination\LengthAwarePaginator;

interface RecruitmentContractRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function findById(int $id): RecruitmentContract;
    public function findByMusanedNumber(string $musanedNumber): ?RecruitmentContract;
    public function create(array $data): RecruitmentContract;
    public function update(RecruitmentContract $contract, array $data): RecruitmentContract;
    public function delete(int $id): void;
    public function updateStatus(RecruitmentContract $contract, int $status, ?string $date, ?string $waMessage, int $adminId): void;
    public function getStatsByBranch(): array;
}
