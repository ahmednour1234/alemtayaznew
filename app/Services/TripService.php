<?php

namespace App\Services;

use App\Models\Trip;
use App\Repositories\Contracts\TripRepositoryInterface;
use App\Repositories\TripRepository;

class TripService
{
    public function __construct(
        private readonly TripRepositoryInterface $repo
    ) {}

    public function list(array $filters = [])
    {
        return $this->repo->getAll($filters);
    }

    public function find(int $id)
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Trip
    {
        $data['trip_number'] = Trip::generateNumber();
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): Trip
    {
        return $this->repo->update($id, $data);
    }

    public function complete(int $id): Trip
    {
        return $this->repo->update($id, ['status' => 'completed']);
    }

    public function cancel(int $id): Trip
    {
        return $this->repo->update($id, ['status' => 'cancelled']);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    public function addWorker(Trip $trip, int $workerId, ?int $contractId = null, ?string $notes = null, ?int $previousContractStatus = null): void
    {
        $trip->workers()->syncWithoutDetaching([
            $workerId => [
                'contract_id'              => $contractId,
                'notes'                    => $notes,
                'status'                   => 'scheduled',
                'previous_contract_status' => $previousContractStatus,
            ],
        ]);
    }

    public function removeWorker(Trip $trip, int $workerId): void
    {
        $trip->workers()->detach($workerId);
    }

    public function upcoming(?int $branchId, int $days = 7)
    {
        return $this->repo->getUpcoming($branchId, $days);
    }

    public function forCalendar(?int $branchId, string $month)
    {
        return $this->repo->getForCalendar($branchId, $month);
    }
}
