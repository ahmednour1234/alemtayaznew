<?php

namespace App\Repositories;

use App\Models\SponsorshipTransfer;
use App\Repositories\Contracts\SponsorshipTransferRepositoryInterface;

class SponsorshipTransferRepository implements SponsorshipTransferRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        return SponsorshipTransfer::with(['worker', 'fromClient', 'toClient', 'branch'])
            ->when(!empty($filters['branch_id']),        fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->when(!empty($filters['current_department']),fn($q) => $q->where('current_department', $filters['current_department']))
            ->when(!empty($filters['current_status']),   fn($q) => $q->where('current_status', $filters['current_status']))
            ->when(!empty($filters['payment_status']),   fn($q) => $q->where('payment_status', $filters['payment_status']))
            ->when(!empty($filters['search']),           fn($q) =>
                $q->where('contract_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('worker', fn($wq) => $wq->where('name', 'like', '%' . $filters['search'] . '%'))
            )
            ->latest()
            ->paginate(20);
    }

    public function findById(int $id)
    {
        return SponsorshipTransfer::with(['worker.nationality', 'fromClient', 'toClient', 'branch', 'admin', 'originalContract'])
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return SponsorshipTransfer::create($data);
    }

    public function update(int $id, array $data)
    {
        $transfer = SponsorshipTransfer::findOrFail($id);
        $transfer->update($data);
        return $transfer;
    }

    public function delete(int $id): bool
    {
        return (bool) SponsorshipTransfer::findOrFail($id)->delete();
    }

    public function restore(int $id): bool
    {
        return (bool) SponsorshipTransfer::withTrashed()->findOrFail($id)->restore();
    }

    public function countPending(?int $branchId): int
    {
        return SponsorshipTransfer::whereNotIn('current_status', [3, 4])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->count();
    }
}
