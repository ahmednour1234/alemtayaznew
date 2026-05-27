<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\RecruitmentContract;
use App\Repositories\Contracts\RecruitmentContractRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RecruitmentContractService
{
    public function __construct(
        private readonly RecruitmentContractRepositoryInterface $repo
    ) {}

    public function getList(array $filters, ?int $branchId): \Illuminate\Pagination\LengthAwarePaginator
    {
        if ($branchId) {
            $filters['branch_id'] = $branchId;
        }
        return $this->repo->getAll($filters);
    }

    public function findById(int $id): RecruitmentContract
    {
        return $this->repo->findById($id);
    }

    public function findByMusanedNumber(string $number): ?RecruitmentContract
    {
        return $this->repo->findByMusanedNumber($number);
    }

    public function store(array $data): RecruitmentContract
    {
        $data['contract_number'] = RecruitmentContract::generateNumber();
        $data['admin_id']        = Auth::guard('admin')->id();

        $data = $this->handleUploads($data);

        $contract = $this->repo->create($data);

        // Notify all admins of the branch
        $this->notifyBranch($contract, 'contracts.created', 'عقد جديد', "تم إنشاء عقد رقم {$contract->contract_number}");

        return $contract;
    }

    public function update(RecruitmentContract $contract, array $data): RecruitmentContract
    {
        $data = $this->handleUploads($data, $contract);
        return $this->repo->update($contract, $data);
    }

    public function updateStatus(RecruitmentContract $contract, int $status, ?string $date, ?string $waMessage): void
    {
        $adminId = Auth::guard('admin')->id();
        $this->repo->updateStatus($contract, $status, $date, $waMessage, $adminId);

        $statusLabel = RecruitmentContract::statuses()[$status]['label'] ?? "مرحلة {$status}";

        $this->notifyBranch(
            $contract,
            'contracts.status_updated',
            "تحديث حالة العقد {$contract->contract_number}",
            "الحالة الجديدة: {$statusLabel}"
        );
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }

    public function getStatsByBranch(): array
    {
        return $this->repo->getStatsByBranch();
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function handleUploads(array $data, ?RecruitmentContract $existing = null): array
    {
        foreach (['visa_image', 'musaned_file', 'rating_image'] as $field) {
            if (isset($data[$field]) && $data[$field] instanceof UploadedFile) {
                // Delete old file if exists
                if ($existing && $existing->{$field}) {
                    Storage::disk('public')->delete($existing->{$field});
                }
                $data[$field] = $data[$field]->store('contracts', 'public');
            } else {
                unset($data[$field]);  // don't overwrite with null
            }
        }
        return $data;
    }

    private function notifyBranch(RecruitmentContract $contract, string $type, string $title, string $body): void
    {
        $url     = route('admin.contracts.show', $contract->id);
        $admins  = Admin::where('branch_id', $contract->branch_id)
            ->orWhereNull('branch_id')   // super admins
            ->where('active', true)
            ->pluck('id');

        $notifications = $admins->map(fn($adminId) => [
            'admin_id'   => $adminId,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'url'        => $url,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        AdminNotification::insert($notifications);
    }
}
