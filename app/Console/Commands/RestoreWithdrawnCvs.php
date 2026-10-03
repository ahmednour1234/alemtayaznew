<?php

namespace App\Console\Commands;

use App\Models\Worker;
use App\Models\WorkerActivityLog;
use Illuminate\Console\Command;

/**
 * إعادة السير المتاحة إلى العرض العام.
 *
 * عمود cv_withdrawn_at يسحب السيرة من الموقع فور ارتباطها بعميل، ولا يُمحى
 * بعدها. لكنّ سيراً خُتمت بأثر الفكّ التلقائي القديم ثمّ عادت «متاحة» بلا
 * عميل ولا عقد — فبقيت محجوبة عن العملاء بلا سبب قائم.
 *
 * هذا الأمر يرفع الختم عمّا لا ارتباط له، فتعود للعرض كما كانت.
 */
class RestoreWithdrawnCvs extends Command
{
    protected $signature = 'workers:restore-withdrawn
                            {--nationality= : معرّف الجنسية أو اسمها}
                            {--apply : نفّذ؛ بدونه عرض فقط}';

    protected $description = 'إعادة السير المتاحة غير المرتبطة إلى العرض في الموقع العام';

    public function handle(): int
    {
        $query = Worker::query()
            ->whereNotNull('cv_withdrawn_at')   // مسحوبة الآن
            ->where('status', 'available')      // ومع ذلك متاحة
            ->whereNull('client_id')            // بلا عميل
            ->whereNotNull('cv_path')
            ->where('active', true)
            ->whereDoesntHave('latestContract') // وبلا عقد
            ->with('nationality');

        if ($nat = $this->option('nationality')) {
            $ids = ctype_digit((string) $nat)
                ? [(int) $nat]
                : \App\Models\Nationality::where('name', 'like', '%' . $nat . '%')->pluck('id')->all();

            if (! $ids) {
                $this->error("لم يُعثر على جنسية تطابق «{$nat}».");
                return self::FAILURE;
            }

            $query->whereIn('nationality_id', $ids);
        }

        $workers = $query->get();

        if ($workers->isEmpty()) {
            $this->info('لا توجد سير محجوبة بلا سبب. الحالة سليمة.');
            return self::SUCCESS;
        }

        $this->warn("وُجدت {$workers->count()} سيرة متاحة لكنّها محجوبة عن الموقع:");

        $this->table(
            ['الرقم', 'الاسم', 'الجنسية', 'تاريخ السحب'],
            $workers->map(fn (Worker $w) => [
                $w->id,
                mb_strimwidth((string) $w->name, 0, 34, '…'),
                $w->nationality?->name ?? '—',
                $w->cv_withdrawn_at?->format('Y-m-d H:i'),
            ])->all()
        );

        if (! $this->option('apply')) {
            $this->comment('عرض فقط — أعد التشغيل مع ‎--apply‎ للتنفيذ.');
            return self::SUCCESS;
        }

        foreach ($workers as $worker) {
            // تحديث مباشر بلا أحداث: حارس الموديل يختم السحب عند أي ارتباط،
            // ونحن هنا نرفع الختم قصداً عمّا لا ارتباط له.
            Worker::where('id', $worker->id)->update(['cv_withdrawn_at' => null]);

            $this->log($worker);
        }

        $this->info("تمّت إعادة {$workers->count()} سيرة إلى العرض في الموقع.");

        return self::SUCCESS;
    }

    private function log(Worker $worker): void
    {
        try {
            WorkerActivityLog::create([
                'worker_id'   => $worker->id,
                'worker_name' => $worker->name,
                'admin_id'    => null,
                'admin_name'  => 'النظام',
                'action'      => 'updated',
                'label'       => 'أُعيدت السيرة إلى العرض في الموقع العام (رُفع حجبها لعدم وجود ارتباط)',
                'ip_address'  => null,
            ]);
        } catch (\Throwable) {
            // لا نُعطّل الإعادة بسبب فشل التسجيل
        }
    }
}
