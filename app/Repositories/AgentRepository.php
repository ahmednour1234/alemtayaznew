<?php

namespace App\Repositories;

use App\Models\Agent;
use App\Repositories\Contracts\AgentRepositoryInterface;

class AgentRepository implements AgentRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        return Agent::with('nationality')
            ->when(!empty($filters['nationality_id']), fn($q) => $q->where('nationality_id', $filters['nationality_id']))
            ->when(!empty($filters['search']),         fn($q) => $q->where(function ($q2) use ($filters) {
                $q2->where('name', 'like', '%' . $filters['search'] . '%')
                   ->orWhere('phone', 'like', '%' . $filters['search'] . '%')
                   ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            }))
            ->latest()
            ->paginate(20);
    }

    public function findById(int $id)
    {
        return Agent::with('nationality')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Agent::create($data);
    }

    public function update(int $id, array $data)
    {
        $agent = Agent::findOrFail($id);
        $agent->update($data);
        return $agent->fresh();
    }

    public function delete(int $id): bool
    {
        return Agent::findOrFail($id)->delete();
    }

    public function restore(int $id): bool
    {
        return Agent::withTrashed()->findOrFail($id)->restore();
    }

    public function getTrashed()
    {
        return Agent::onlyTrashed()->latest()->get();
    }
}
