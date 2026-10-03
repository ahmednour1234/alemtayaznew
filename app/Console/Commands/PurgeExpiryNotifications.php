<?php

namespace App\Console\Commands;

use App\Models\AdminNotification;
use Illuminate\Console\Command;

/**
 * حذف إشعارات انتهاء الحجز القديمة.
 *
 * بعد إلغاء مهلة الـ72 ساعة صارت هذه الإشعارات تتحدّث عن شيء لم يعد قائماً:
 * «يتبقّى كذا ساعة» و«انتهى الحجز تلقائياً». تبقى في الجرس فتُربك الموظّف
 * وتدفعه لمطاردة حجوزات لم تنتهِ أصلاً.
 */
class PurgeExpiryNotifications extends Command
{
    /** أنواع الإشعارات المرتبطة بالمهلة الملغاة. */
    private const TYPES = [
        'worker_reservation_expired',
        'worker_reservation_expiring',
    ];

    protected $signature = 'notifications:purge-expiry
                            {--apply : نفّذ الحذف؛ بدونه عرض فقط}';

    protected $description = 'حذف إشعارات انتهاء الحجز التي فقدت معناها بعد إلغاء المهلة';

    public function handle(): int
    {
        $query = AdminNotification::whereIn('type', self::TYPES);

        $count = (clone $query)->count();

        if ($count === 0) {
            $this->info('لا توجد إشعارات من هذا النوع.');
            return self::SUCCESS;
        }

        $this->warn("وُجد {$count} إشعاراً عن مهلة الحجز الملغاة.");

        // عيّنة ليتبيّن المستخدم ما سيُحذف قبل أن يُقرّر
        $this->table(
            ['النوع', 'العنوان', 'التاريخ'],
            (clone $query)->latest()->limit(5)->get()
                ->map(fn ($n) => [
                    $n->type,
                    mb_strimwidth((string) $n->title, 0, 40, '…'),
                    $n->created_at?->format('Y-m-d H:i'),
                ])->all()
        );

        if (! $this->option('apply')) {
            $this->comment('عرض فقط — أعد التشغيل مع ‎--apply‎ للحذف.');
            return self::SUCCESS;
        }

        $deleted = $query->delete();

        $this->info("تم حذف {$deleted} إشعاراً.");

        return self::SUCCESS;
    }
}
