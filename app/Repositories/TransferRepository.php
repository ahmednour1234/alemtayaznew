<?php

namespace App\Repositories;

use App\Models\FinancialTransfer;
use App\Repositories\Contracts\TransferRepositoryInterface;

class TransferRepository implements TransferRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        return FinancialTransfer::query()
            ->with(['fromBranch', 'toBranch', 'admin', 'approver'])
            // Branch-admin scope: see transfers involving their branch
            ->when(!empty($filters['branch_id']), fn($q) => $q->where(fn($q2) =>
                $q2->where('from_branch_id', $filters['branch_id'])
                   ->orWhere('to_branch_id', $filters['branch_id'])
            ))
            ->when(empty($filters['branch_id']) && !empty($filters['from_branch_id']), fn($q) => $q->where('from_branch_id', $filters['from_branch_id']))
            ->when(empty($filters['branch_id']) && !empty($filters['to_branch_id']),   fn($q) => $q->where('to_branch_id', $filters['to_branch_id']))
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['date_from']), fn($q) => $q->whereDate('date', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn($q) => $q->whereDate('date', '<=', $filters['date_to']))
            ->latest('date')
            ->paginate(20);
    }

    public function findById(int $id)
    {
        return FinancialTransfer::with(['fromBranch', 'toBranch', 'admin', 'approver'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return FinancialTransfer::create($data);
    }

    public function update(int $id, array $data)
    {
        $transfer = FinancialTransfer::findOrFail($id);
        $transfer->update($data);
        return $transfer;
    }

    public function delete(int $id): bool
    {
        return FinancialTransfer::findOrFail($id)->delete();
    }

    public function restore(int $id): bool
    {
        return FinancialTransfer::withTrashed()->findOrFail($id)->restore();
    }

    public function getTrashed()
    {
        return FinancialTransfer::onlyTrashed()->with(['fromBranch', 'toBranch'])->latest()->get();
    }

    public function getPending(?int $branchId = null)
    {
        return FinancialTransfer::with(['fromBranch', 'toBranch', 'admin'])
            ->where('status', 'pending')
            ->when($branchId, fn($q) => $q->where(fn($q2) =>
                $q2->where('from_branch_id', $branchId)->orWhere('to_branch_id', $branchId)
            ))
            ->latest()
            ->get();
    }

    public function getRecent(int $limit = 10)
    {
        return FinancialTransfer::with(['fromBranch', 'toBranch'])->latest('date')->limit($limit)->get();
    }
}
