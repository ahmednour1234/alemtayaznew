<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\HousingAssignment;
use App\Models\HousingRental;
use App\Models\HousingSettlement;
use App\Models\Worker;
use App\Repositories\Contracts\HousingAssignmentRepositoryInterface;
use Illuminate\Support\Facades\Auth;

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

        // عاملة التأجير تبقى "للتأجير" وتعمل في السكن، وغيرها "في السكن"
        $workerStatus = ($data['reason'] ?? null) === 'rental' ? 'for_rent' : 'in_housing';
        Worker::where('id', $data['worker_id'])->update(['status' => $workerStatus]);

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

    /**
     * تسجيل مغادرة العاملة من السكن مع تحديد وجهتها (تأجير / تسوية) وبياناتها.
     *
     * @param array $data check_out_date, notes, disposition, و بيانات التأجير/التسوية
     */
    public function checkout(int $id, array $data): HousingAssignment
    {
        $current = $this->repo->findById($id);

        $assignment = $this->repo->update($id, [
            'check_out_date' => $data['check_out_date'],
            'notes'          => $data['notes'] ?? $current->notes,
        ]);

        $disposition = $data['disposition'] ?? null;

        if ($disposition === 'rental') {
            HousingRental::create([
                'housing_assignment_id' => $assignment->id,
                'worker_id'             => $assignment->worker_id,
                'branch_id'             => $assignment->branch_id,
                'client_id'             => $data['rental_client_id'] ?? null,
                'admin_id'              => Auth::guard('admin')->id(),
                'contract_number'       => $data['rental_contract_number'] ?? null,
                'rent_value'            => $data['rent_value'] ?? 0,
                'rent_start_date'       => $data['rent_start_date'] ?? $data['check_out_date'],
                'rent_end_date'         => $data['rent_end_date'] ?? null,
                'contract_image'        => $data['rental_contract_image'] ?? null,
                'notes'                 => $data['rental_notes'] ?? null,
            ]);
        } elseif ($disposition === 'settlement') {
            HousingSettlement::create([
                'housing_assignment_id' => $assignment->id,
                'worker_id'             => $assignment->worker_id,
                'branch_id'             => $assignment->branch_id,
                'client_id'             => $data['settlement_client_id'] ?? null,
                'admin_id'              => Auth::guard('admin')->id(),
                'reference_number'      => $data['settlement_reference'] ?? null,
                'settlement_amount'     => $data['settlement_amount'] ?? 0,
                'settlement_type'       => $data['settlement_type'] ?? null,
                'settlement_date'       => $data['settlement_date'] ?? $data['check_out_date'],
                'document_image'        => $data['settlement_document_image'] ?? null,
                'notes'                 => $data['settlement_notes'] ?? null,
            ]);
        }

        // Revert worker status to available if no other active assignment
        $hasOtherActive = HousingAssignment::where('worker_id', $assignment->worker_id)
            ->where('id', '!=', $id)
            ->whereNull('check_out_date')
            ->exists();

        if (! $hasOtherActive) {
            Worker::where('id', $assignment->worker_id)
                ->whereIn('status', ['in_housing', 'for_rent'])
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
