<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Worker;
use App\Models\WorkerActivityLog;
use Illuminate\Console\Command;

/**
 * يعمل كل ساعة. حجز العاملة صالح لمدة 72 ساعة فقط:
 *
 *  • قبل مرور 72 ساعة  → تذكير للموظّف الذي حجزها (مرة واحدة عند اقتراب المهلة).
 *  • بعد مرور 72 ساعة  → فكّ الحجز تلقائياً وإشعار الموظّف ومدير الفرع.
 */
class NotifyUncontractedWorkers extends Command
{
    /** مهلة الحجز بالساعات — بعدها يُفكّ الحجز تلقائياً. المصدر الوحيد لهذه القيمة. */
    public const RESERVATION_HOURS = 72;

    /** يُرسل تذكير عند تبقّي هذا العدد من الساعات أو أقل. */
    private const REMINDER_BEFORE_HOURS = 6;

    protected $signature   = 'workers:notify-uncontracted';
    protected $description = 'فكّ حجز العاملات بلا عقد بعد ' . self::RESERVATION_HOURS . ' ساعة مع إشعار المسؤولين';

    public function handle(): void
    {
        // العاملات المحجوزة/المعيَّنة لعميل بلا عقد استقدام
        $workers = Worker::where('active', true)
            ->whereIn('status', ['assigned', 'reserved'])
            ->whereNotNull('client_id')
            ->whereNotNull('assigned_at')
            ->doesntHave('latestContract')
            ->with(['branch', 'client', 'assignedBy'])
            ->get();

        if ($workers->isEmpty()) {
            $this->info('لا توجد عاملات محجوزة بلا عقد.');
            return;
        }

        $released = 0;
        $reminded = 0;

        foreach ($workers as $worker) {
            /** @var \Carbon\Carbon $assignedAt */
            $assignedAt   = $worker->assigned_at;
            $hoursElapsed = $assignedAt->diffInHours(now());

            $workerName = $worker->name           ?? '—';
            $clientName = $worker->client?->name   ?? '—';
            $branchName = $worker->branch?->name   ?? '—';
            $url        = route('admin.workers.show', $worker->id);

            // المهلة تُقرأ من العاملة لا من الثابت: سداد تمارا يمدّدها إلى 5 أيام
            $limitHours = $worker->reservationHours();

            if ($hoursElapsed >= $limitHours) {
                // ── انتهت المهلة: فكّ الحجز تلقائياً ─────────────────────────
                // نلتقط بيانات الحجز قبل تصفيرها لتسجيلها في سجل النشاط
                $statusBefore = $worker->status_label;
                $reserverName = $worker->assignedBy?->name;
                $reservedAt   = $assignedAt->format('Y-m-d H:i');

                $worker->update([
                    'client_id'            => null,
                    'status'               => 'available',
                    'assigned_by_admin_id' => null,
                    'assigned_at'          => null,
                ]);

                // تسجيل في سجل نشاط العاملة — الفاعل هو النظام لا مستخدم،
                // لأن الأمر يعمل عبر المجدول بلا جلسة مسجّلة.
                $this->logAutoRelease(
                    $worker,
                    "فكّ النظام حجز العاملة تلقائياً من العميل «{$clientName}» بعد "
                    . "{$limitHours} ساعة دون إنشاء عقد"
                    . ($worker->hasTamaraPayment() ? ' (مهلة ممددة بسداد تمارا)' : '')
                    . " — الحالة: {$statusBefore} ← متاحة"
                    . ($reserverName ? " (كان الحجز بواسطة {$reserverName} بتاريخ {$reservedAt})" : '')
                );

                $title = 'انتهاء حجز عاملة تلقائياً';
                $body  = "انتهت مهلة حجز العاملة «{$workerName}» للعميل «{$clientName}» (فرع {$branchName}) "
                       . "بعد {$limitHours} ساعة دون إنشاء عقد، وأصبحت متاحة مرة أخرى.";

                // إشعار الموظّف الذي حجزها
                if ($worker->assigned_by_admin_id) {
                    $this->upsertNotification(
                        $worker->assigned_by_admin_id,
                        'worker_reservation_expired',
                        $title,
                        $body,
                        $url
                    );
                }

                // إشعار مديري الفرع والمديرين العامين
                $managers = Admin::where('active', true)
                    ->where(function ($q) use ($worker) {
                        $q->where(function ($inner) use ($worker) {
                            $inner->where('branch_id', $worker->branch_id)
                                  ->where('department', 'branch_manager');
                        })->orWhereNull('branch_id'); // مدير عام
                    })
                    ->get();

                foreach ($managers as $manager) {
                    $this->upsertNotification(
                        $manager->id,
                        'worker_reservation_expired',
                        $title,
                        $body,
                        $url
                    );
                }

                $released++;
                continue;
            }

            // ── تذكير قبل انتهاء المهلة ──────────────────────────────────────
            if (! $worker->assigned_by_admin_id) {
                continue;
            }

            $hoursLeft = $limitHours - (int) $hoursElapsed;

            if ($hoursLeft > self::REMINDER_BEFORE_HOURS) {
                continue; // ما زال أمامه وقت — لا تُزعج الموظّف
            }

            $title = 'تنبيه: حجز عاملة على وشك الانتهاء';
            $body  = "يتبقّى {$hoursLeft} ساعة على انتهاء حجز العاملة «{$workerName}» للعميل «{$clientName}». "
                   . "أنشئ عقد الاستقدام قبل انتهاء المهلة وإلا سيُفكّ الحجز تلقائياً.";

            $this->upsertNotification(
                $worker->assigned_by_admin_id,
                'worker_reservation_expiring',
                $title,
                $body,
                $url
            );

            $reminded++;
        }

        $this->info("تذكيرات: {$reminded} — حجوزات مفكوكة: {$released}");
    }

    /**
     * يسجّل فكّ الحجز التلقائي في سجل نشاط العاملة.
     *
     * الفاعل هنا هو النظام لا مستخدم، فنترك admin_id فارغاً ونكتب
     * admin_name نصّاً ليظهر السطر مفهوماً في الواجهة.
     * التسجيل لا يجب أن يُفشل فكّ الحجز، لذا نبتلع أي خطأ.
     */
    private function logAutoRelease(Worker $worker, string $label): void
    {
        try {
            WorkerActivityLog::create([
                'worker_id'   => $worker->id,
                'worker_name' => $worker->name,
                'admin_id'    => null,
                'admin_name'  => 'النظام',
                'action'      => 'unassigned',
                'label'       => $label,
                'ip_address'  => null,
            ]);
        } catch (\Throwable) {
            // لا نُعطّل فكّ الحجز بسبب فشل التسجيل
        }
    }

    private function upsertNotification(
        int    $adminId,
        string $type,
        string $title,
        string $body,
        string $url
    ): void {
        // منع التكرار: إشعار واحد لكل (موظّف + نوع + عاملة) خلال نافذة الحجز.
        // لا نستخدم "اليوم" هنا لأن الأمر يعمل كل ساعة ونافذة منع التكرار تساوي مهلة الحجز.
        $exists = AdminNotification::where('admin_id', $adminId)
            ->where('type', $type)
            ->where('url', $url)
            ->where('created_at', '>=', now()->subHours(self::RESERVATION_HOURS))
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
