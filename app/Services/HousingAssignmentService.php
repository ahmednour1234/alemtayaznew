<?php

namespace App\Services;

use App\Models\HousingAssignment;
use App\Models\Worker;
use App\Repositories\Contracts\HousingAssignmentRepositoryInterface;

class HousingAssignmentService
{
    public function __construct(
        private readonly HousingAssignmentRepositoryInterface $repo
    ) {}

    public function list(array $filters = [])
    {
        return $this->repo->getAll($filters);
    }

    public function activeList(array $filters = [])
    {
        return $this->repo->getActive($filters);
    }

    public function find(int $id)
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): HousingAssignment
    {
        $assignment = $this->repo->create($data);

        // Update worker status to in_housing
        Worker::where('id', $data['worker_id'])->update(['status' => 'in_housing']);

        return $assignment;
    }

    public function checkout(int $id, string $checkOutDate, ?string $notes = null): HousingAssignment
    {
        $assignment = $this->repo->update($id, [
            'check_out_date' => $checkOutDate,
            'notes'          => $notes ?? $this->repo->findById($id)->notes,
        ]);

        // Revert worker status to available if no other active assignment
        $hasOtherActive = HousingAssignment::where('worker_id', $assignment->worker_id)
            ->where('id', '!=', $id)
            ->whereNull('check_out_date')
            ->exists();

        if (! $hasOtherActive) {
            Worker::where('id', $assignment->worker_id)
                ->where('status', 'in_housing')
                ->update(['status' => 'available']);
        }

        return $assignment;
    }

    public function occupancyCount(int $housingId): int
    {
        return $this->repo->countActiveByHousing($housingId);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
