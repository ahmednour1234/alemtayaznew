<?php

namespace App\Repositories;

use App\Models\Worker;
use App\Repositories\Contracts\WorkerRepositoryInterface;

class WorkerRepository implements WorkerRepositoryInterface
{
    public function getAll(array $filters = []): mixed
    {
        $q = Worker::with(['nationality', 'client', 'branch'])
                   ->where('active', true);

        if (!empty($filters['nationality_id'])) {
            $q->where('nationality_id', $filters['nationality_id']);
        }
        if (!empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (!empty($filters['profession'])) {
            $q->where('profession', $filters['profession']);
        }
        if (!empty($filters['search'])) {
            $s = '%' . $filters['search'] . '%';
            $q->where(function ($q2) use ($s) {
                $q2->where('name', 'like', $s)
                   ->orWhere('passport_number', 'like', $s)
                   ->orWhere('phone', 'like', $s);
            });
        }

        return $q->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
    }

    public function findById(int $id): mixed
    {
        return Worker::with(['nationality', 'client', 'branch', 'latestContract.client', 'latestContract.originNationality', 'latestContract.agent'])->findOrFail($id);
    }

    public function create(array $data): mixed
    {
        return Worker::create($data);
    }

    public function update(int $id, array $data): mixed
    {
        $worker = Worker::findOrFail($id);
        $worker->update($data);
        return $worker;
    }

    public function delete(int $id): void
    {
        Worker::findOrFail($id)->delete();
    }

    public function restore(int $id): void
    {
        Worker::withTrashed()->findOrFail($id)->restore();
    }

    public function getTrashed(): mixed
    {
        return Worker::onlyTrashed()->with('nationality')->get();
    }

    public function assignToClient(int $id, int $clientId): void
    {
        Worker::findOrFail($id)->update([
            'client_id' => $clientId,
            'status'    => 'assigned',
        ]);
    }

    public function unassign(int $id): void
    {
        Worker::findOrFail($id)->update([
            'client_id' => null,
            'status'    => 'available',
        ]);
    }
}
