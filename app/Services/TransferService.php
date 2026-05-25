<?php

namespace App\Services;

use App\Models\Admin;
use App\Repositories\Contracts\TransferRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TransferService
{
    public function __construct(
        private readonly TransferRepositoryInterface $repo
    ) {}

    public function list(array $filters = [])
    {
        return $this->repo->getAll($filters);
    }

    public function find(int $id)
    {
        return $this->repo->findById($id);
    }

    public function pending()
    {
        return $this->repo->getPending();
    }

    public function recent(int $limit = 10)
    {
        return $this->repo->getRecent($limit);
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['status'] = 'pending';
            return $this->repo->create($data);
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $transfer = $this->repo->findById($id);
            if ($transfer->status !== 'pending') {
                throw new \RuntimeException('لا يمكن تعديل تحويل تمت معالجته.');
            }
            return $this->repo->update($id, $data);
        });
    }

    public function approve(int $id, Admin $approver): void
    {
        DB::transaction(function () use ($id, $approver) {
            $transfer = $this->repo->findById($id);
            if (! $transfer->isPending()) {
                throw new \RuntimeException('هذا التحويل ليس في حالة انتظار.');
            }
            $this->repo->update($id, [
                'status'      => 'approved',
                'approved_by' => $approver->id,
                'approved_at' => now(),
            ]);
        });
    }

    public function reject(int $id, Admin $approver, string $reason): void
    {
        DB::transaction(function () use ($id, $approver, $reason) {
            $transfer = $this->repo->findById($id);
            if (! $transfer->isPending()) {
                throw new \RuntimeException('هذا التحويل ليس في حالة انتظار.');
            }
            $this->repo->update($id, [
                'status'           => 'rejected',
                'approved_by'      => $approver->id,
                'approved_at'      => now(),
                'rejection_reason' => $reason,
            ]);
        });
    }

    public function destroy(int $id): bool
    {
        return DB::transaction(fn() => $this->repo->delete($id));
    }

    public function restore(int $id): bool
    {
        return DB::transaction(fn() => $this->repo->restore($id));
    }

    public function trashed()
    {
        return $this->repo->getTrashed();
    }
}
