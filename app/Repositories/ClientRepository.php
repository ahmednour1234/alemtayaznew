<?php

namespace App\Repositories;

use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        return Client::with(['branch', 'requiredNationality'])
            ->when(!empty($filters['branch_id']),      fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->when(!empty($filters['classification']), fn($q) => $q->where('classification', $filters['classification']))
            ->when(!empty($filters['marital_status']), fn($q) => $q->where('marital_status', $filters['marital_status']))
            ->when(!empty($filters['search']),         fn($q) => $q->where(function ($q2) use ($filters) {
                $q2->where('name', 'like', '%' . $filters['search'] . '%')
                   ->orWhere('national_id', 'like', '%' . $filters['search'] . '%')
                   ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
            }))
            ->latest()
            ->paginate(20);
    }

    public function findById(int $id)
    {
        return Client::with(['branch', 'admin', 'requiredNationality'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Client::create($data);
    }

    public function update(int $id, array $data)
    {
        $client = Client::findOrFail($id);
        $client->update($data);
        return $client->fresh();
    }

    public function delete(int $id): bool
    {
        return Client::findOrFail($id)->delete();
    }

    public function restore(int $id): bool
    {
        return Client::withTrashed()->findOrFail($id)->restore();
    }

    public function getTrashed()
    {
        return Client::onlyTrashed()->latest()->get();
    }
}
