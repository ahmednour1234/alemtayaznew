<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Worker;
use App\Repositories\Contracts\WorkerRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class WorkerService
{
    public function __construct(
        private readonly WorkerRepositoryInterface $repo,
        private readonly NotificationService $notifications,
    ) {}

    public function list(array $filters = []): mixed
    {
        return $this->repo->getAll($filters);
    }

    public function find(int $id): mixed
    {
        return $this->repo->findById($id);
    }

    public function trashed(): mixed
    {
        return $this->repo->getTrashed();
    }

    // ── Duplicate CV check ────────────────────────────────────────────────────

    /**
     * Returns the first existing worker that matches the passport_number
     * or the original CV filename; null if no duplicate found.
     */
    public function findDuplicate(?string $passportNumber, ?string $originalCvName): mixed
    {
        if (! $passportNumber && ! $originalCvName) {
            return null;
        }
        return $this->repo->findDuplicateCv($passportNumber, $originalCvName);
    }

    // ── Single store ──────────────────────────────────────────────────────────

    public function store(array $data, ?UploadedFile $cv = null, ?UploadedFile $passportImage = null): mixed
    {
        if ($cv) {
            $data['original_cv_name'] = $cv->getClientOriginalName();
            $data['cv_path']          = $cv->store('workers/cvs', 'public');
        }
        if ($passportImage) {
            $data['passport_image'] = $passportImage->store('workers/passports', 'public');
        }

        $worker = $this->repo->create($data);

        $this->sendCvUploadNotifications($worker);

        return $worker;
    }

    // ── Bulk upload ───────────────────────────────────────────────────────────

    /**
     * Bulk-store CV files.
     *
     * Returns array of ['created' => Worker[], 'duplicates' => string[]]
     */
    public function bulkStore(array $commonData, array $files, bool $skipDuplicates = false): array
    {
        $created    = [];
        $duplicates = [];

        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();

            if (! $skipDuplicates) {
                $dup = $this->repo->findDuplicateCv(null, $originalName);
                if ($dup) {
                    $duplicates[] = $originalName;
                    continue;
                }
            }

            $data                    = $commonData;
            $data['cv_path']         = $file->store('workers/cvs', 'public');
            $data['original_cv_name'] = $originalName;
            $data['name']            = pathinfo($originalName, PATHINFO_FILENAME);
            $worker                  = $this->repo->create($data);
            $this->sendCvUploadNotifications($worker);
            $created[] = $worker;
        }

        return compact('created', 'duplicates');
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(int $id, array $data, ?UploadedFile $cv = null, ?UploadedFile $passportImage = null): mixed
    {
        if ($cv) {
            $old = $this->repo->findById($id);
            if ($old->cv_path) {
                Storage::disk('public')->delete($old->cv_path);
            }
            $data['original_cv_name'] = $cv->getClientOriginalName();
            $data['cv_path']          = $cv->store('workers/cvs', 'public');
        }
        if ($passportImage) {
            $old = $old ?? $this->repo->findById($id);
            if ($old->passport_image) {
                Storage::disk('public')->delete($old->passport_image);
            }
            $data['passport_image'] = $passportImage->store('workers/passports', 'public');
        }
        return $this->repo->update($id, $data);
    }

    // ── Delete / Restore ──────────────────────────────────────────────────────

    public function destroy(int $id): void
    {
        $this->repo->delete($id);
    }

    public function restore(int $id): void
    {
        $this->repo->restore($id);
    }

    // ── Assign to Client ──────────────────────────────────────────────────────

    /**
     * Assign a worker (CV) to a client.
     *
     * Rules:
     *  1. If already assigned to a DIFFERENT client → throw exception.
     *  2. If already has a contract for a different client → throw exception.
     *  3. Records who assigned and when.
     *  4. Notifies coordination + branch manager of that branch.
     */
    public function assignToClient(int $id, int $clientId, int $assignedByAdminId): void
    {
        $worker = $this->repo->findById($id);

        // Rule 1: already assigned to a different client
        if ($worker->client_id && $worker->client_id !== $clientId) {
            throw new \RuntimeException(
                'هذه العاملة مُعيَّنة مسبقاً لعميل آخر ولا يمكن تعيينها لعميل مختلف.'
            );
        }

        // Rule 2: has a contract under a different client
        $contractClientId = \App\Models\RecruitmentContract::where('worker_id', $id)
            ->whereNotNull('client_id')
            ->value('client_id');

        if ($contractClientId && $contractClientId !== $clientId) {
            throw new \RuntimeException(
                'هذه العاملة مرتبطة بعقد لعميل آخر ولا يمكن تعيينها لعميل مختلف.'
            );
        }

        $this->repo->assignToClient($id, $clientId, $assignedByAdminId);

        // Reload to get branch info
        $worker->refresh();

        $clientName = $worker->client?->name ?? 'عميل';
        $url        = route('admin.workers.show', $worker->id);
        $title      = 'تعيين عاملة لعميل';
        $body       = "تم تعيين العاملة «{$worker->name}» للعميل «{$clientName}»";

        // Notify coordination + branch manager (same branch)
        $branchAdmins = Admin::where('active', true)
            ->where('branch_id', $worker->branch_id)
            ->whereIn('department', ['coordination', 'branch_manager'])
            ->get();

        foreach ($branchAdmins as $admin) {
            $this->createNotification($admin->id, 'worker_assigned', $title, $body, $url);
        }
    }

    // ── Unassign ──────────────────────────────────────────────────────────────

    /**
     * Only branch_manager OR the admin who performed the assignment can unassign.
     */
    public function unassign(int $id, Admin $actor): void
    {
        $worker = $this->repo->findById($id);

        $isBranchManager = $actor->department === 'branch_manager' || $actor->isSuperAdmin();
        $isAssigner      = $actor->id === $worker->assigned_by_admin_id;

        if (! $isBranchManager && ! $isAssigner) {
            throw new \RuntimeException(
                'ليس لديك صلاحية إلغاء التعيين. يجب أن تكون مدير الفرع أو الموظف الذي أجرى التعيين.'
            );
        }

        $this->repo->unassign($id);
    }

    // ── WhatsApp ──────────────────────────────────────────────────────────────

    public function buildWhatsappUrl(string $phone, array $workerIds): string
    {
        $workers = Worker::whereIn('id', $workerIds)
                    ->whereNotNull('cv_path')->get();

        $lines = ["السلام عليكم، مرفق مجموعة CV عاملات للمراجعة:\n"];
        foreach ($workers as $i => $w) {
            $url     = Storage::disk('public')->url($w->cv_path);
            $name    = $w->name ?: ('عاملة ' . ($i + 1));
            $nat     = $w->nationality?->name ?? '';
            $lines[] = ($i + 1) . ". {$name}" . ($nat ? " ({$nat})" : '') . "\n{$url}";
        }

        $message = implode("\n\n", $lines);
        $clean   = preg_replace('/[^0-9]/', '', $phone);
        return 'https://wa.me/' . $clean . '?text=' . rawurlencode($message);
    }

    // ── Internal helpers ──────────────────────────────────────────────────────

    private function sendCvUploadNotifications(Worker $worker): void
    {
        $url   = route('admin.workers.show', $worker->id);
        $title = 'CV عاملة جديدة';
        $body  = "تم رفع CV جديدة للعاملة «{$worker->name}»";

        // Notify: customer_service, branch_manager of same branch, chairman/super-admin
        $recipients = Admin::where('active', true)
            ->where(function ($q) use ($worker) {
                $q->whereNull('branch_id')                          // super admin
                  ->orWhere(function ($q2) use ($worker) {
                      $q2->where('branch_id', $worker->branch_id)
                         ->whereIn('department', ['customer_service', 'branch_manager']);
                  })
                  ->orWhere('department', 'chairman');
            })
            ->get();

        foreach ($recipients as $admin) {
            $this->createNotification($admin->id, 'worker_cv_uploaded', $title, $body, $url);
        }
    }

    private function createNotification(int $adminId, string $type, string $title, string $body, string $url): void
    {
        // Avoid duplicate same-day notifications
        $exists = AdminNotification::where('admin_id', $adminId)
            ->where('type', $type)
            ->where('url', $url)
            ->whereDate('created_at', today())
            ->exists();

        if (! $exists) {
            AdminNotification::create([
                'admin_id' => $adminId,
                'type'     => $type,
                'title'    => $title,
                'body'     => $body,
                'url'      => $url,
            ]);
        }
    }
}
