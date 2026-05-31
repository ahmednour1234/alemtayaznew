<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\HousingAssignment;
use App\Models\Worker;
use App\Repositories\Contracts\HousingAssignmentRepositoryInterface;

class HousingAssignmentService
{
    public function __construct(
        private readonly HousingAssignmentRepositoryInterface $repo,
        private readonly NotificationService $notificationService
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

        // ── Notifications ────────────────────────────────────────────────────
        $worker  = $assignment->worker ?? Worker::find($data['worker_id']);
        $url     = route('admin.housing-assignments.index');
        $title   = 'تسكين عاملة جديدة';
        $body    = 'تم تسكين العاملة ' . ($worker->name ?? '—') .
                   ($assignment->expected_check_out_date ? ' · التاريخ المتوقع للمغادرة: ' . $assignment->expected_check_out_date->format('Y-m-d') : '');

        // Super-admins + branch admins of the housing branch
        $this->notificationService->notify(
            'housing_assignment_created',
            $title,
            $body,
            $url,
            [$assignment->branch_id]
        );

        // Also notify chairmen (رئيس مجلس الإدارة) not already covered as super-admins
        Admin::where('active', true)
            ->where('department', 'chairman')
            ->whereDoesntHave('roles', fn($q) => $q->where('slug', 'super-admin'))
            ->each(fn($admin) => AdminNotification::create([
                'admin_id' => $admin->id,
                'type'     => 'housing_assignment_created',
                'title'    => $title,
                'body'     => $body,
                'url'      => $url,
            ]));

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
