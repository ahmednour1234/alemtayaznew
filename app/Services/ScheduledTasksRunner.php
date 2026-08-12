<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * يشغّل أوامر الجدولة عند دخول أول مستخدم للوحة التحكم، بديلاً عن cron
 * على السيرفر. كل أمر له مهلة خاصة به حتى لا يُعاد تشغيله مع كل زيارة.
 *
 * ملاحظة: الأوامر نفسها تمنع تكرار الإشعارات داخلياً، فالمهلة هنا
 * للحماية من بطء الصفحة لا من ازدواج الإشعارات.
 */
class ScheduledTasksRunner
{
    /** قفل عام يمنع تشغيل نسختين في نفس اللحظة (طلبات متزامنة). */
    private const LOCK_KEY = 'scheduled_tasks:running';

    /** الأوامر والمهلة بالدقائق بين كل تشغيل والذي يليه. */
    private const COMMANDS = [
        'workers:notify-uncontracted' => 60,    // كل ساعة
        'leads:notify-followups'      => 60,    // كل ساعة
        'contracts:check-delays'      => 1440,  // يومياً
        'contracts:check-arrivals'    => 1440,
        'leads:notify-stale'          => 1440,
        'complaints:notify-stale'     => 1440,
        'housing:check-overdue'       => 1440,
        'housing:check-rental-expiry' => 1440,
        'hr:check-expiries'           => 1440,
        'housing:check-guarantee'     => 10080, // أسبوعياً
    ];

    /**
     * يشغّل ما استحقّ من الأوامر. يعيد أسماء الأوامر التي شُغّلت فعلاً.
     */
    public function runDue(): array
    {
        // منع التشغيل المتزامن: أول طلب يأخذ القفل والبقية تمرّ فوراً
        $lock = Cache::lock(self::LOCK_KEY, 300);

        if (! $lock->get()) {
            return [];
        }

        $ran = [];

        try {
            foreach (self::COMMANDS as $command => $everyMinutes) {
                $key = 'scheduled_tasks:last_run:' . $command;

                if (Cache::has($key)) {
                    continue;
                }

                try {
                    Artisan::call($command);
                    $ran[] = $command;
                } catch (\Throwable $e) {
                    // فشل أمر واحد يجب ألا يكسر لوحة التحكم
                    Log::error("ScheduledTasksRunner: فشل تنفيذ [{$command}]", [
                        'message' => $e->getMessage(),
                    ]);
                }

                // تُسجَّل المهلة حتى عند الفشل تفادياً لإعادة المحاولة مع كل زيارة
                Cache::put($key, now()->toDateTimeString(), now()->addMinutes($everyMinutes));
            }
        } finally {
            $lock->release();
        }

        return $ran;
    }
}
