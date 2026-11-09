<?php

namespace App\Console\Commands;

use App\Models\Nationality;
use App\Models\Worker;
use App\Models\WorkerActivityLog;
use Illuminate\Console\Command;

/**
 * فكّ حجز السير المحجوزة التي لم يُنشأ لها عقد أصلاً، وإرجاعها للموقع.
 *
 * مثال: php artisan workers:release-uncontracted --nationality=فلب
 * بدون ‎--apply‎ يعرض القائمة فقط.
 */
class ReleaseUncontractedReservations extends Command
{
    protected $signature = 'workers:release-uncontracted
                            {--nationality= : معرّف الجنسية أو جزء من اسمها (إلزامي)}
                            {--apply : نفّذ؛ بدونه عرض فقط}';

    protected $description = 'فكّ حجز السير المحجوزة بلا عقد وإرجاعها للعرض في الموقع';

    public function handle(): int
    {
        $nat = (string) $this->option('nationality');
        if ($nat === '') {
            $this->error('حدّد الجنسية بـ ‎--nationality‎.');
            return self::FAILURE;
        }

        $ids = ctype_digit($nat)
            ? [(int) $nat]
            : Nationality::where('name', 'like', '%' . $nat . '%')->pluck('id')->all();

        if (! $ids) {
            $this->error("لم يُعثر على جنسية تطابق «{$nat}».");
            return self::FAILURE;
        }

        $workers = Worker::query()
            ->whereIn('nationality_id', $ids)
            ->where('status', 'reserved')
            ->whereDoesntHave('latestContract')
            ->with('nationality', 'client')
            ->get();

        if ($workers->isEmpty()) {
            $this->info('لا توجد سير محجوزة بلا عقد لهذه الجنسية.');
            return self::SUCCESS;
        }

        $this->warn("وُجدت {$workers->count()} سيرة محجوزة بلا عقد:");
        $this->table(
            ['الرقم', 'الاسم', 'الجنسية', 'العميل', 'تاريخ الحجز'],
            $workers->map(fn (Worker $w) => [
                $w->id,
                mb_strimwidth((string) $w->name, 0, 34, '…'),
                $w->nationality?->name ?? '—',
                $w->client?->name ?? '—',
                $w->assigned_at?->format('Y-m-d H:i') ?? '—',
            ])->all()
        );

        if (! $this->option('apply')) {
            $this->comment('عرض فقط — أعد التشغيل مع ‎--apply‎ للتنفيذ.');
            return self::SUCCESS;
        }

        foreach ($workers as $w) {
            // تحديث مباشر: حارس saving يعيد ختم السحب لو مرّ عبر الموديل
            Worker::whereKey($w->id)->update([
                'status'               => 'available',
                'client_id'            => null,
                'assigned_by_admin_id' => null,
                'assigned_at'          => null,
                'cv_withdrawn_at'      => null,
            ]);

            try {
                WorkerActivityLog::create([
                    'worker_id'   => $w->id,
                    'worker_name' => $w->name,
                    'admin_id'    => null,
                    'admin_name'  => 'النظام',
                    'action'      => 'updated',
                    'label'       => 'فُكّ حجز السيرة (بلا عقد) وأُعيدت للعرض في الموقع العام',
                    'ip_address'  => null,
                ]);
            } catch (\Throwable) {
            }
        }

        $this->info("تمّ فكّ حجز {$workers->count()} سيرة وإرجاعها للموقع.");
        return self::SUCCESS;
    }
}
