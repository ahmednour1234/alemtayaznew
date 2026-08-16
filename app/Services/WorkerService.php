<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Worker;
use App\Models\WorkerActivityLog;
use App\Repositories\Contracts\WorkerRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class WorkerService
{
    public function __construct(
        private readonly WorkerRepositoryInterface $repo,
        private readonly NotificationService $notifications,
    ) {}

    /**
     * يسجّل إجراءً على عاملة في سجل التدقيق.
     *
     * نحفظ اسم العاملة واسم الموظّف نصّاً وقت الإجراء ليبقى السجل مقروءاً
     * بعد الحذف. ولا نُدرج أرقام الجواز أو الهاتف في الوصف لأنها بيانات
     * حساسة مشفّرة في قاعدة البيانات.
     *
     * التسجيل لا يجب أن يُفشل العملية الأصلية، لذا نبتلع أي خطأ.
     */
    private function log(?Worker $worker, string $action, ?string $label = null, ?int $workerId = null, ?string $workerName = null): void
    {
        try {
            $admin = Auth::guard('admin')->user();

            WorkerActivityLog::create([
                'worker_id'   => $worker?->id ?? $workerId,
                'worker_name' => $worker?->name ?? $workerName,
                'admin_id'    => $admin?->id,
                'admin_name'  => $admin?->name,
                'action'      => $action,
                'label'       => $label,
                'ip_address'  => Request::ip(),
            ]);
        } catch (\Throwable) {
            // لا نُعطّل العملية بسبب فشل التسجيل
        }
    }

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

        $this->log($worker, 'created', 'تم إضافة العاملة' . ($cv ? ' مع رفع CV' : ''));

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

            $data                     = $commonData;
            $data['cv_path']          = $file->store('workers/cvs', 'public');
            $data['original_cv_name'] = $originalName;
            $data['name']             = pathinfo($originalName, PATHINFO_FILENAME);

            $newWorker = $this->repo->create($data);
            $this->log($newWorker, 'created', 'تم إضافة العاملة ضمن رفع CV جماعي');
            $created[] = $newWorker;
        }

        // One grouped notification for the whole batch
        if (! empty($created)) {
            // Reload first worker with nationality for the label
            $firstWorker = \App\Models\Worker::with('nationality')->find($created[0]->id);
            $this->sendBulkCvUploadNotification($created, $commonData, $firstWorker);
        }

        return compact('created', 'duplicates');
    }

    /**
     * Process duplicate files previously saved to temp storage.
     * $tempPaths: ['originalName.pdf' => 'temp_bulk/key/originalName.pdf']
     */
    public function bulkStoreFromTempPaths(array $commonData, array $tempPaths): array
    {
        $created = [];

        foreach ($tempPaths as $originalName => $tempPath) {
            if (! Storage::exists($tempPath)) {
                continue;
            }

            $finalPath = 'workers/cvs/' . uniqid() . '_' . $originalName;
            Storage::disk('public')->put($finalPath, Storage::get($tempPath));
            Storage::delete($tempPath);

            $data                     = $commonData;
            $data['cv_path']          = $finalPath;
            $data['original_cv_name'] = $originalName;
            $data['name']             = pathinfo($originalName, PATHINFO_FILENAME);

            $newWorker = $this->repo->create($data);
            $this->log($newWorker, 'created', 'تم إضافة العاملة ضمن رفع CV جماعي');
            $created[] = $newWorker;
        }

        // Clean up temp directory
        if (! empty($tempPaths)) {
            $dir = dirname(reset($tempPaths));
            Storage::deleteDirectory($dir);
        }

        if (! empty($created)) {
            $firstWorker = \App\Models\Worker::with('nationality')->find($created[0]->id);
            $this->sendBulkCvUploadNotification($created, $commonData, $firstWorker);
        }

        return compact('created');
    }

    // ── Update ────────────────────────────────────────────────────────────────

    /**
     * @param bool $logChange مرّر false حين يكون التعديل جزءاً من عملية أكبر
     *                        (مثل الحجز) حتى لا يتكرر السطر في سجل التدقيق.
     */
    public function update(int $id, array $data, ?UploadedFile $cv = null, ?UploadedFile $passportImage = null, bool $logChange = true): mixed
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

        $worker = $this->repo->update($id, $data);

        if ($logChange) {
            // أسماء عربية للحقول — لا نكتب قيمها لأن بعضها بيانات حساسة
            $names = [
                'name' => 'الاسم', 'passport_number' => 'رقم الجواز', 'visa_number' => 'رقم التأشيرة',
                'phone' => 'الهاتف', 'age' => 'العمر', 'nationality_id' => 'الجنسية',
                'profession' => 'المهنة', 'experience' => 'الخبرة', 'religion' => 'الديانة',
                'gender' => 'الجنس', 'notes' => 'ملاحظات', 'status' => 'الحالة',
                'branch_id' => 'الفرع', 'client_id' => 'العميل',
            ];

            $changed = array_values(array_intersect_key(
                $names,
                array_diff_key($data, array_flip(['cv_path', 'original_cv_name', 'passport_image']))
            ));

            $label = $cv
                ? 'تم استبدال ملف الـ CV'
                : 'تم تعديل بيانات العاملة' . ($changed ? ' — ' . implode('، ', array_slice($changed, 0, 6)) : '');

            $this->log($this->repo->findById($id), $cv ? 'cv_uploaded' : 'updated', $label);
        }

        return $worker;
    }

    // ── Delete / Restore ──────────────────────────────────────────────────────

    public function destroy(int $id): void
    {
        $worker = $this->repo->findById($id);

        if ($worker->recruitmentContracts()->exists()) {
            throw new \RuntimeException('لا يمكن حذف عاملة مرتبطة بعقد استقدام.');
        }

        // نسجّل قبل الحذف لنلتقط الاسم
        $this->log($worker, 'deleted', 'تم حذف العاملة');

        $this->repo->delete($id);
    }

    /**
     * حذف جماعي للعاملات. العاملات المرتبطة بعقد استقدام تُستثنى ولا تُحذف.
     *
     * @param  int[] $ids
     * @return array{deleted:int, skipped:array<int,string>}
     */
    public function bulkDestroy(array $ids): array
    {
        if (! $ids) {
            return ['deleted' => 0, 'skipped' => []];
        }

        $workers = Worker::whereIn('id', $ids)
            ->withCount('recruitmentContracts')
            ->get();

        $deletable = [];
        $skipped   = [];

        $toLog = [];

        foreach ($workers as $worker) {
            if ($worker->recruitment_contracts_count > 0) {
                $skipped[] = $worker->name ?: ('عاملة #' . $worker->id);
                continue;
            }

            $deletable[] = $worker->id;
            $toLog[]     = $worker;
        }

        $deleted = 0;
        if ($deletable) {
            // الحذف الجماعي يتم باستعلام واحد فلا تعمل أحداث الموديل،
            // لذا نسجّل كل عاملة يدوياً قبل الحذف.
            foreach ($toLog as $worker) {
                $this->log($worker, 'deleted', 'تم حذف العاملة ضمن حذف جماعي');
            }

            $deleted = Worker::whereIn('id', $deletable)->delete();
        }

        return ['deleted' => $deleted, 'skipped' => $skipped];
    }

    /**
     * كل معرّفات العاملات المطابقة للفلاتر الحالية (متجاوزاً الـ pagination).
     *
     * @return int[]
     */
    public function idsMatchingFilters(array $filters): array
    {
        return $this->repo->filteredQuery($filters)->pluck('id')->all();
    }

    public function restore(int $id): void
    {
        $this->repo->restore($id);

        $this->log($this->repo->findById($id), 'restored', 'تم استعادة العاملة من المحذوفات');
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

        $this->log($worker, 'assigned', "تم حجز العاملة للعميل «{$clientName}»");

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

        // Capture info before unassign
        $clientName = $worker->client?->name ?? 'عميل';
        $branchId   = $worker->branch_id;
        $workerName = $worker->name ?: 'عاملة';
        $url        = route('admin.workers.show', $worker->id);

        $this->repo->unassign($id);

        $this->log($worker, 'unassigned', "تم إلغاء تعيين العاملة من العميل «{$clientName}»");

        // Notify coordination + branch manager
        $title = 'إلغاء تعيين عاملة';
        $body  = "تم إلغاء تعيين العاملة «{$workerName}» من العميل «{$clientName}» بواسطة {$actor->name}";

        $branchAdmins = Admin::where('active', true)
            ->where('branch_id', $branchId)
            ->whereIn('department', ['coordination', 'branch_manager'])
            ->get();

        foreach ($branchAdmins as $admin) {
            if ($admin->id !== $actor->id) {
                $this->createNotification($admin->id, 'worker_unassigned', $title, $body, $url);
            }
        }
    }

    // ── WhatsApp ──────────────────────────────────────────────────────────────

    /**
     * الحد الأقصى للعاملات في رسالة واتساب واحدة.
     *
     * روابط wa.me تُمرَّر عبر عنوان URL، والنص العربي يتضخّم عند الترميز
     * (كل حرف ≈ 9 بايت). قياس فعلي بأسماء طويلة: 75 عاملة ≈ 29,000 حرف،
     * و80 عاملة تتجاوز حد Chrome (~32,000) فتفشل. اخترنا 60 لهامش أمان،
     * وما زاد عنه يُقسَّم تلقائياً على رسائل متتابعة.
     */
    public const WHATSAPP_MAX_PER_MESSAGE = 60;

    /** الحد الأقصى لإجمالي العاملات عبر كل الرسائل في عملية إرسال واحدة. */
    public const WHATSAPP_MAX_TOTAL = 300;

    /**
     * بيانات العاملات اللازمة لبناء رسالة واتساب في المتصفح.
     * محدودة بـ WHATSAPP_MAX_PER_MESSAGE حتى يبقى الرابط ضمن حدود المتصفح.
     *
     * @param  int[] $workerIds
     * @return array<int, array{id:int, name:?string, nationality:?string, cv_url:string}>
     */
    public function whatsappPayload(array $workerIds): array
    {
        if (! $workerIds) {
            return [];
        }

        return Worker::with('nationality')
            ->whereIn('id', $workerIds)
            ->whereNotNull('cv_path')
            ->limit(self::WHATSAPP_MAX_TOTAL)
            ->get()
            ->map(fn(Worker $w) => [
                'id'          => $w->id,
                'name'        => $w->name,
                'nationality' => $w->nationality?->name,
                'cv_url'      => route('admin.workers.cv', $w->id),
            ])
            ->all();
    }

    public function buildWhatsappUrl(string $phone, array $workerIds): string
    {
        $workers = Worker::with('nationality')
                    ->whereIn('id', $workerIds)
                    ->whereNotNull('cv_path')
                    ->limit(self::WHATSAPP_MAX_PER_MESSAGE)
                    ->get();

        // عزل الأجزاء اللاتينية (الأسماء والروابط) داخل نص عربي RTL
        // حتى لا يقلب واتساب ترتيبها — U+2066 (LRI) ... U+2069 (PDI).
        $lri = "\u{2066}";
        $pdi = "\u{2069}";
        $ltr = fn(string $s): string => $lri . $s . $pdi;

        $lines = ['*مجموعة CV عاملات للمراجعة*', ''];

        foreach ($workers as $i => $w) {
            $url  = route('admin.workers.cv', $w->id);
            $name = trim($w->name ?: ('عاملة ' . ($i + 1)));
            $nat  = $w->nationality?->name;

            $lines[] = '*' . ($i + 1) . '.* ' . $ltr($name);
            if ($nat) {
                $lines[] = "الجنسية: {$nat}";
            }
            $lines[] = $ltr($url);
            $lines[] = '';
        }

        $message = rtrim(implode("\n", $lines));
        $clean   = preg_replace('/[^0-9]/', '', $phone);
        return 'https://wa.me/' . $clean . '?text=' . rawurlencode($message);
    }

    // ── Internal helpers ──────────────────────────────────────────────────────

    private function sendCvUploadNotifications(Worker $worker): void
    {
        $url   = route('admin.workers.show', $worker->id);
        $title = 'CV عاملة جديدة';
        $body  = "تم رفع CV جديدة للعاملة «{$worker->name}»";

        $recipients = Admin::where('active', true)
            ->where(function ($q) use ($worker) {
                $q->whereNull('branch_id')
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

    /**
     * Send one grouped notification for a bulk-upload batch.
     *
     * @param Worker[] $workers
     */
    private function sendBulkCvUploadNotification(array $workers, array $commonData, ?Worker $firstWorker = null): void
    {
        $count      = count($workers);
        $branchId   = $commonData['branch_id'] ?? ($workers[0]->branch_id ?? null);
        $url        = route('admin.workers.index');

        // Build label: nationality + profession if available
        $natName    = $firstWorker?->nationality?->name ?? null;
        $profession = isset($commonData['profession'])
            ? (Worker::professions()[$commonData['profession']] ?? null)
            : null;

        $detail = implode(' - ', array_filter([$natName, $profession]));
        $title  = "تم رفع {$count} CV جديدة";
        $body   = "تم رفع {$count} CV" . ($detail ? " ({$detail})" : '');

        $recipients = Admin::where('active', true)
            ->where(function ($q) use ($branchId) {
                $q->whereNull('branch_id')
                  ->orWhere(function ($q2) use ($branchId) {
                      $q2->where('branch_id', $branchId)
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
        // Avoid duplicate notifications: same admin, type, title and url on the same day
        $exists = AdminNotification::where('admin_id', $adminId)
            ->where('type', $type)
            ->where('title', $title)
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
