<?php

namespace App\Repositories;

use App\Models\HousingAssignment;
use App\Repositories\Contracts\HousingAssignmentRepositoryInterface;

class HousingAssignmentRepository implements HousingAssignmentRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        return HousingAssignment::with(['worker.nationality', 'worker.client', 'worker.latestContract.client', 'housing', 'branch', 'admin'])
            ->when(!empty($filters['branch_id']),  fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->when(!empty($filters['housing_id']), fn($q) => $q->where('housing_id', $filters['housing_id']))
            ->when(!empty($filters['reason']),     fn($q) => $q->where('reason', $filters['reason']))
            ->when(isset($filters['active']) && $filters['active'] !== '', function ($q) use ($filters) {
                $filters['active'] ? $q->whereNull('check_out_date') : $q->whereNotNull('check_out_date');
            })
            ->when(!empty($filters['search']), fn($q) =>
                $q->whereHas('worker', fn($wq) => $wq->where('name', 'like', '%' . $filters['search'] . '%'))
            )
            ->latest()
            ->paginate(20);
    }

    public function getActive(array $filters = [])
    {
        return HousingAssignment::with(['worker.nationality', 'housing', 'branch'])
            ->whereNull('check_out_date')
            ->when(!empty($filters['branch_id']),  fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->when(!empty($filters['housing_id']), fn($q) => $q->where('housing_id', $filters['housing_id']))
            ->latest()
            ->get();
    }

    public function findById(int $id)
    {
        return HousingAssignment::with(['worker', 'housing', 'branch', 'admin'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return HousingAssignment::create($data);
    }

    public function update(int $id, array $data)
    {
        $assignment = $this->findById($id);
        $assignment->update($data);
        return $assignment;
    }

    public function delete(int $id): bool
    {
        return (bool) $this->findById($id)->delete();
    }

    public function countActiveByHousing(int $housingId): int
    {
        return HousingAssignment::where('housing_id', $housingId)
            ->whereNull('check_out_date')
            ->count();
    }
}
