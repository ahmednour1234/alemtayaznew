<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\ContractActivityLog;
use App\Models\RecruitmentContract;
use App\Models\Worker;
use App\Repositories\Contracts\RecruitmentContractRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Mark worker as assigned
        if (! empty($data['worker_id'])) {
            Worker::where('id', $data['worker_id'])->update(['status' => 'assigned']);
        }

        // Notify: if created by a dept employee → notify next dept; otherwise notify whole branch
        $creator = Auth::guard('admin')->user();
        $dept    = $creator->department ?? null;

        // Log activity
        ContractActivityLog::create([
            'contract_id' => $contract->id,
            'admin_id'    => Auth::guard('admin')->id(),
            'action'      => 'created',
            'section'     => $dept,
            'label'       => 'تم إنشاء العقد',
        ]);

        $next = $this->nextDepartment($dept);

        if ($next) {
            $this->notifyDepartment(
                $contract, $next,
                'contracts.created',
                "عقد جديد يحتاج مراجعتك: {$contract->contract_number}",
                "أنشأ قسم خدمة العملاء عقداً جديداً — يرجى استكمال البيانات"
            );
        } else {
            $this->notifyBranch($contract, 'contracts.created', 'عقد جديد', "تم إنشاء عقد رقم {$contract->contract_number}");
        }

        return $contract;
    }

    public function update(RecruitmentContract $contract, array $data): RecruitmentContract
    {
        $data    = $this->handleUploads($data, $contract);

        // Handle worker change: free old, assign new
        $newWorkerId = $data['worker_id'] ?? null;
        if ($newWorkerId != $contract->worker_id) {
            if ($contract->worker_id) {
                Worker::where('id', $contract->worker_id)->update(['status' => 'available']);
            }
            if ($newWorkerId) {
                Worker::where('id', $newWorkerId)->update(['status' => 'assigned']);
            }
        }

        $updated = $this->repo->update($contract, $data);

        // Log activity — super admins are not bound to a department, so log null (shows as "الإدارة")
        $editor  = Auth::guard('admin')->user();
        $logDept = $editor->isSuperAdmin() ? null : ($editor->department ?? null);
        ContractActivityLog::create([
            'contract_id' => $contract->id,
            'admin_id'    => Auth::guard('admin')->id(),
            'action'      => 'updated',
            'section'     => $logDept,
            'label'       => 'تم تعديل بيانات العقد',
        ]);

        // Notify next department that this section was completed
        $editor = Auth::guard('admin')->user();
        $dept   = $editor->department ?? null;
        $next   = $this->nextDepartment($dept);
        if ($next) {
            $this->notifyDepartment(
                $updated, $next,
                'contracts.updated',
                "عقد {$contract->contract_number} جاهز للمراجعة",
                "أتمّ قسم " . $this->deptLabel($dept) . " بيانات العقد — يرجى استكمال دورك"
            );
        }

        return $updated;
    }

    public function updateStatus(RecruitmentContract $contract, int $status, ?string $date, ?string $waMessage): void
    {
        $adminId     = Auth::guard('admin')->id();
        $this->repo->updateStatus($contract, $status, $date, $waMessage, $adminId);

        // Free worker when contract ends (returned or escaped)
        if (in_array($status, [14, 15]) && $contract->worker_id) {
            Worker::where('id', $contract->worker_id)->update(['status' => 'available']);
        }

        // Log status change
        ContractActivityLog::create([
            'contract_id' => $contract->id,
            'admin_id'    => $adminId,
            'action'      => 'status_changed',
            'section'     => $this->statusToDepartment($status),
            'label'       => 'تحديث الحالة: ' . (RecruitmentContract::statuses()[$status]['label'] ?? "مرحلة {$status}"),
        ]);

        $statusLabel = RecruitmentContract::statuses()[$status]['label'] ?? "مرحلة {$status}";
        $department  = $this->statusToDepartment($status);

        $this->notifyDepartment(
            $contract,
            $department,
            'contracts.status_updated',
            "تحديث حالة العقد {$contract->contract_number}",
            "الحالة الجديدة: {$statusLabel}"
        );
    }

    /** Map status number to the responsible department */
    private function statusToDepartment(int $status): string
    {
        return match (true) {
            $status <= 4  => 'customer_service',
            $status <= 8  => 'accounts',
            default       => 'coordination',
        };
    }

    /** Returns the next department in the workflow chain */
    private function nextDepartment(?string $dept): ?string
    {
        return match ($dept) {
            'customer_service' => 'accounts',
            'accounts'         => 'coordination',
            'accountant'       => 'coordination',
            default            => null,
        };
    }

    /** Human-readable dept label for notifications */
    private function deptLabel(?string $dept): string
    {
        return match ($dept) {
            'customer_service' => 'خدمة العملاء',
            'accounts'         => 'الحسابات',
            'accountant'       => 'المحاسبة',
            'coordination'     => 'التنسيق',
            default            => 'الإدارة',
        };
    }

    /**
     * إلغاء تأشيرة العقد وفكّ ربط العاملة به.
     *
     * يبقى العقد قائماً بلا عاملة حتى يربطه المنسّق بعاملة بديلة، ويلتقطه
     * أمر contracts:notify-unlinked يومياً فلا يُنسى.
     *
     * العاملة تعود «متاحة» لأنها لم تعد مرتبطة بأي عقد؛ ونصفّر عميلها أيضاً
     * وإلا بقيت مرتبطة بعميل بلا سبب — وهو ما يمنعه حارس الاتساق في Worker.
     */
    public function cancelVisa(RecruitmentContract $contract, Admin $actor, ?string $reason = null): void
    {
        $worker = $contract->worker;

        DB::transaction(function () use ($contract, $worker, $actor, $reason) {
            $contract->update([
                'worker_id'                 => null,
                'previous_worker_id'        => $worker?->id,
                'current_status'            => RecruitmentContract::STATUS_VISA_CANCELLED,
                'visa_cancelled_at'         => now(),
                'visa_cancelled_by_admin_id' => $actor->id,
                'visa_cancel_reason'        => $reason,
            ]);

            // نحرّر العاملة بعد فكّ الربط لا قبله، فحارس الاتساق في Worker
            // يمنع «available» ما دام هناك عقد قائم مرتبط بها.
            $worker?->update([
                'status'               => 'available',
                'client_id'            => null,
                'assigned_by_admin_id' => null,
                'assigned_at'          => null,
                'tamara_paid_at'       => null,
                'tamara_paid_by_admin_id' => null,
            ]);
        });

        ContractActivityLog::create([
            'contract_id' => $contract->id,
            'admin_id'    => $actor->id,
            'action'      => 'visa_cancelled',
            'section'     => 'coordination',
            'label'       => 'أُلغيت التأشيرة وفُكّ ربط العاملة «' . ($worker?->name ?? '—') . '» بالعقد'
                           . ($reason ? " — السبب: {$reason}" : ''),
        ]);

        $this->notifyDepartment(
            $contract,
            'coordination',
            'contract_visa_cancelled',
            'إلغاء تأشيرة عقد',
            "أُلغيت تأشيرة العقد {$contract->contract_number} وفُكّ ربط العاملة "
            . '«' . ($worker?->name ?? '—') . '» — العقد بحاجة إلى عاملة بديلة.'
        );
    }

    public function delete(int $id): void
    {
        $contract = $this->repo->findById($id);
        // نحذف العقد أولاً ثم نُحرّر العاملة، لأن حارس الاتساق في Worker
        // يمنع «available» ما دام هناك عقد قائم لم ينتهِ.
        $workerId = $contract->worker_id;

        $this->repo->delete($id);

        if ($workerId) {
            Worker::where('id', $workerId)->update([
                'status'    => 'available',
                'client_id' => null,
            ]);
        }
    }

    public function getTrashed(?int $branchId): \Illuminate\Pagination\LengthAwarePaginator
    {
        return RecruitmentContract::onlyTrashed()
            ->with(['client', 'branch'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest('deleted_at')
            ->paginate(20);
    }

    public function restore(int $id): void
    {
        RecruitmentContract::onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        RecruitmentContract::onlyTrashed()->findOrFail($id)->forceDelete();
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
        $admins  = Admin::where(function ($q) use ($contract) {
                $q->where('branch_id', $contract->branch_id)
                  ->orWhereNull('branch_id');  // super admins
            })
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

    /**
     * Notify the responsible department members in the same branch,
     * plus branch managers of that branch and super admins.
     */
    private function notifyDepartment(RecruitmentContract $contract, string $department, string $type, string $title, string $body): void
    {
        $url = route('admin.contracts.show', $contract->id);

        // Department members in same branch + branch managers + super admins
        $admins = Admin::where('active', true)
            ->where(function ($q) use ($contract, $department) {
                $q->where(function ($q2) use ($contract, $department) {
                        // Same branch → department members or branch manager
                        $q2->where('branch_id', $contract->branch_id)
                           ->whereIn('department', [$department, 'branch_manager']);
                    })
                    ->orWhereNull('branch_id'); // super admins (no branch restriction)
            })
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
