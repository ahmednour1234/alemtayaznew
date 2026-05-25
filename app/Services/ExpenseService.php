<?php

namespace App\Services;

use App\Models\Admin;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseService
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $repo
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

    public function totalByStatus(string $status, array $filters = []): float
    {
        return $this->repo->getTotalByStatus($status, $filters);
    }

    public function recent(int $limit = 10)
    {
        return $this->repo->getRecent($limit);
    }

    public function store(array $data, ?UploadedFile $attachment = null)
    {
        return DB::transaction(function () use ($data, $attachment) {
            $data['status'] = 'pending';
            if ($attachment) {
                $data['attachment'] = $attachment->store('attachments/expenses', 'public');
            }
            return $this->repo->create($data);
        });
    }

    public function update(int $id, array $data, ?UploadedFile $attachment = null)
    {
        return DB::transaction(function () use ($id, $data, $attachment) {
            $expense = $this->repo->findById($id);

            if ($expense->status !== 'pending') {
                throw new \RuntimeException('لا يمكن تعديل مصروف تمت معالجته.');
            }

            if ($attachment) {
                if ($expense->attachment) {
                    Storage::disk('public')->delete($expense->attachment);
                }
                $data['attachment'] = $attachment->store('attachments/expenses', 'public');
            }
            return $this->repo->update($id, $data);
        });
    }

    public function approve(int $id, Admin $approver): void
    {
        DB::transaction(function () use ($id, $approver) {
            $expense = $this->repo->findById($id);
            if (! $expense->isPending()) {
                throw new \RuntimeException('هذا المصروف ليس في حالة انتظار.');
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
            $expense = $this->repo->findById($id);
            if (! $expense->isPending()) {
                throw new \RuntimeException('هذا المصروف ليس في حالة انتظار.');
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
