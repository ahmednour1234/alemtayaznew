<?php

namespace App\Console\Commands;

use App\Models\Worker;
use App\Models\WorkerActivityLog;
use Illuminate\Console\Command;

/**
 * تصحيح حالة متناقضة: عاملة «متاحة» ولها عميل.
 *
 * «متاحة» تعني معروضة للحجز، فلا تجتمع مع ارتباط قائم بعميل. نشأت هذه الحالة
 * من الفكّ التلقائي القديم (مهلة الـ72 ساعة) الذي كان يُعيد الحالة إلى
 * «متاحة» دون أن يُفرّغ client_id في كل المسارات، ومن استيراد صفوف ناقصة.
 *
 * أثرها أن السيرة تُعرض للحجز وهي مرتبطة بعميل فعلاً، فيُحجزها موظّف ثانٍ
 * لعميل آخر. الصواب أن تصير «تم التعيين».
 */
class FixAvailableWithClient extends Command
{
    protected $signature = 'workers:fix-available-with-client
                            {--dry-run : عرض المتأثرات دون تعديل}';

    protected $description = 'تحويل العاملات «المتاحات» المرتبطات بعميل إلى «تم التعيين»';

    public function handle(): int
    {
        $workers = Worker::where('status', 'available')
            ->whereNotNull('client_id')
            ->with(['client', 'nationality', 'assignedBy'])
            ->get();

        if ($workers->isEmpty()) {
            $this->info('لا توجد عاملات «متاحات» مرتبطات بعميل. الحالة سليمة.');
            return self::SUCCESS;
        }

        $this->warn("وُجدت {$workers->count()} عاملة متاحة ولها عميل:");

        $this->table(
            ['الرقم', 'الاسم', 'الجنسية', 'العميل', 'حجزها', 'لها عقد'],
            $workers->map(fn (Worker $w) => [
                $w->id,
                mb_strimwidth((string) $w->name, 0, 30, '…'),
                $w->nationality?->name ?? '—',
                mb_strimwidth((string) ($w->client?->name ?? '—'), 0, 26, '…'),
                $w->assignedBy?->name ?? '—',
                $w->hasActiveContract() ? 'نعم' : 'لا',
            ])->all()
        );

        if ($this->option('dry-run')) {
            $this->comment('عرض فقط — لم يُعدّل شيء. أعد التشغيل بلا ‎--dry-run‎ للتطبيق.');
            return self::SUCCESS;
        }

        $fixed = 0;

        foreach ($workers as $worker) {
            $clientName = $worker->client?->name ?? 'عميل';

            /*
             * التحديث بلا أحداث الموديل: حارس الاتساق في Worker::booted يمنع
             * الانتقال إلى «تم التعيين» في بعض الحالات، ونحن هنا نصحّح البيانات
             * قصداً لا نُنشئ حجزاً جديداً.
             */
            Worker::where('id', $worker->id)->update([
                'status'          => 'assigned',
                // نُثبّت سحبها من الموقع العام كي لا تعود للعرض لاحقاً
                'cv_withdrawn_at' => $worker->cv_withdrawn_at ?? now(),
            ]);

            $this->log($worker, $clientName);
            $fixed++;
        }

        $this->info("تم تصحيح {$fixed} عاملة إلى «تم التعيين».");

        return self::SUCCESS;
    }

    /** التسجيل لا يجب أن يُفشل التصحيح. */
    private function log(Worker $worker, string $clientName): void
    {
        try {
            WorkerActivityLog::create([
                'worker_id'   => $worker->id,
                'worker_name' => $worker->name,
                'admin_id'    => null,
                'admin_name'  => 'النظام',
                'action'      => 'assigned',
                'label'       => "تصحيح حالة: كانت «متاحة» وهي مرتبطة بالعميل «{$clientName}»"
                    . ' — حُوّلت إلى «تم التعيين»',
                'ip_address'  => null,
            ]);
        } catch (\Throwable) {
            // لا نُعطّل التصحيح بسبب فشل التسجيل
        }
    }
}
