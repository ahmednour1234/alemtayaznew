<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Worker;
use App\Models\WorkerActivityLog;
use Illuminate\Console\Command;

/**
 * يعرض السير التي حجزها موظّف بعينه ثمّ فكّها النظام تلقائياً، وهي اليوم متاحة.
 *
 * فائدته: بعد إلغاء مهلة الـ72 ساعة بقيت آثار الفكّ التلقائي القديم — سير
 * عادت للإتاحة دون قرار بشري. هذا الأمر يجمعها ليُقرّر فيها الموظّف.
 *
 * اسم الحاجز لا يُخزَّن في عمود مستقلّ عند الفكّ التلقائي (الفاعل هو النظام)،
 * وإنّما يُذكر داخل نصّ السجلّ، فنبحث فيه.
 */
class ListAutoReleasedByAdmin extends Command
{
    protected $signature = 'workers:auto-released
                            {admin? : اسم الموظّف أو جزء منه أو معرّفه الرقمي}
                            {--all : اعرض كل من فُكّ حجزهم تلقائياً أياً كان الحاجز}
                            {--available : اقتصر على من حالتهم «متاحة» الآن}';

    protected $description = 'عرض السير التي حجزها موظّف وفكّها النظام تلقائياً';

    public function handle(): int
    {
        $term = $this->argument('admin');

        if (! $term && ! $this->option('all')) {
            $this->error('اذكر اسم الموظّف، أو استخدم ‎--all‎ لعرض الجميع.');
            return self::FAILURE;
        }

        $adminName = null;

        if ($term) {
            $admin = ctype_digit((string) $term)
                ? Admin::find((int) $term)
                : Admin::where('name', 'like', '%' . $term . '%')->first();

            if (! $admin) {
                $this->error("لم يُعثر على موظّف يطابق «{$term}».");
                return self::FAILURE;
            }

            $adminName = $admin->name;
            $this->info("الموظّف: {$adminName} (#{$admin->id})");
        }

        // سجلّات الفكّ التلقائي: الفاعل هو النظام لا مستخدم
        $logs = WorkerActivityLog::where('action', 'unassigned')
            ->whereNull('admin_id')
            ->where('label', 'like', '%فكّ النظام حجز العاملة تلقائياً%')
            ->when($adminName, fn ($q) => $q->where('label', 'like', '%بواسطة ' . $adminName . '%'))
            ->latest()
            ->get();

        if ($logs->isEmpty()) {
            $this->warn('لا توجد سجلّات فكّ تلقائي مطابقة.');
            return self::SUCCESS;
        }

        // سيرة واحدة قد تتكرّر في السجلّ، فنأخذ أحدث سجلّ لكلّ عاملة
        $logs = $logs->unique('worker_id');

        $workers = Worker::whereIn('id', $logs->pluck('worker_id'))
            ->with('nationality')
            ->get()
            ->keyBy('id');

        $rows    = [];
        $skipped = 0;

        foreach ($logs as $log) {
            $worker = $workers->get($log->worker_id);

            if (! $worker) {
                $skipped++; // حُذفت العاملة بعد ذلك
                continue;
            }

            // ‎--available‎ يقصر النتيجة على ما عاد للإتاحة فعلاً
            if ($this->option('available') && $worker->status !== 'available') {
                continue;
            }

            $rows[] = [
                $worker->id,
                mb_strimwidth((string) $worker->name, 0, 34, '…'),
                $worker->nationality?->name ?? '—',
                $worker->status_label,
                $worker->client_id ? 'نعم' : 'لا',
                $log->created_at?->format('Y-m-d H:i'),
            ];
        }

        if (! $rows) {
            $this->warn('لا توجد سير مطابقة بعد التصفية.');
            return self::SUCCESS;
        }

        $this->table(
            ['الرقم', 'الاسم', 'الجنسية', 'الحالة الآن', 'مرتبطة بعميل', 'تاريخ الفكّ'],
            $rows
        );

        $this->info('الإجمالي: ' . count($rows) . ' سيرة.');

        if ($skipped) {
            $this->warn("وتُخطّيت {$skipped} سيرة حُذفت من النظام.");
        }

        return self::SUCCESS;
    }
}
