<?php

namespace App\Services;

use App\Repositories\Contracts\IncomeRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IncomeService
{
    public function __construct(
        private readonly IncomeRepositoryInterface $repo
    ) {}

    public function list(array $filters = [])
    {
        return $this->repo->getAll($filters);
    }

    public function find(int $id)
    {
        return $this->repo->findById($id);
    }

    public function total(array $filters = []): float
    {
        return $this->repo->getTotalByFilters($filters);
    }

    public function recent(int $limit = 10)
    {
        return $this->repo->getRecent($limit);
    }

    public function store(array $data, ?UploadedFile $attachment = null)
    {
        return DB::transaction(function () use ($data, $attachment) {
            if ($attachment) {
                $data['attachment'] = $attachment->store('attachments/incomes', 'public');
            }
            return $this->repo->create($data);
        });
    }

    public function update(int $id, array $data, ?UploadedFile $attachment = null)
    {
        return DB::transaction(function () use ($id, $data, $attachment) {
            if ($attachment) {
                $old = $this->repo->findById($id);
                if ($old->attachment) {
                    Storage::disk('public')->delete($old->attachment);
                }
                $data['attachment'] = $attachment->store('attachments/incomes', 'public');
            }
            return $this->repo->update($id, $data);
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
