<?php

namespace App\Console\Commands;

use App\Models\Worker;
use App\Models\WorkerActivityLog;
use Illuminate\Console\Command;

/**
 * مواءمة حالة العاملة مع حالة عقدها.
 *
 * ظهرت عاملات حالتهنّ «متاحة» رغم وجود عقد قائم — بعضهنّ في حالة
 * «تم الاستلام» أي أنهنّ يعملن لدى عملاء فعلاً. ذلك يعرّضهنّ لحجز جديد
 * من الموقع العام أو من شاشة التعيين.
 *
 * حالات العقد المنتهية (14 رجيع، 15 هروب) تُبقي العاملة «متاحة» فهي
 * عادت للمكتب؛ وما دون ذلك يعني ارتباطاً قائماً فتصير «تم التعيين».
 */
class SyncWorkerStatusFromContracts extends Command
{
    protected $signature = 'workers:sync-contract-status {--dry-run : عرض التغييرات دون تنفيذها}';

    protected $description = 'مواءمة حالة العاملات مع حالة عقودهنّ القائمة';

    /** حالات العقد التي تعني انتهاء الارتباط فتبقى العاملة متاحة. */
    private const ENDED = [14, 15];

    public function handle(): int
    {
        $workers = Worker::where('status', 'available')
            ->whereHas('recruitmentContracts')
            ->with('latestContract')
            ->orderBy('id')
            ->get();

        $affected = $workers->filter(function (Worker $w) {
            $status = $w->latestContract?->current_status;

            return $status !== null && ! in_array((int) $status, self::ENDED, true);
        });

        if ($affected->isEmpty()) {
            $this->info('لا توجد عاملات بحاجة إلى مواءمة.');
            return self::SUCCESS;
        }

        $statuses = \App\Models\RecruitmentContract::statuses();

        $this->table(
            ['#', 'الاسم', 'رقم العقد', 'حالة العقد', 'الحالة الجديدة'],
            $affected->map(fn (Worker $w) => [
                $w->id,
                mb_substr($w->name ?? '—', 0, 26),
                $w->latestContract?->contract_number ?? '—',
                $statuses[$w->latestContract?->current_status]['label'] ?? $w->latestContract?->current_status,
                'تم التعيين',
            ])->all()
        );

        if ($this->option('dry-run')) {
            $this->warn("تجربة فقط — {$affected->count()} عاملة ستتأثر. أعد التشغيل بدون --dry-run للتنفيذ.");
            return self::SUCCESS;
        }

        foreach ($affected as $w) {
            $contract = $w->latestContract;

            $w->update([
                'status'    => 'assigned',
                // نستكمل العميل من العقد إن كان ناقصاً على صف العاملة
                'client_id' => $w->client_id ?? $contract?->client_id,
            ]);

            try {
                WorkerActivityLog::create([
                    'worker_id'   => $w->id,
                    'worker_name' => $w->name,
                    'admin_id'    => null,
                    'admin_name'  => 'النظام',
                    'action'      => 'updated',
                    'label'       => 'صحّح النظام الحالة: متاحة ← تم التعيين — لوجود عقد قائم '
                                   . ($contract?->contract_number ?? '') . ' بحالة «'
                                   . ($statuses[$contract?->current_status]['label'] ?? '—') . '»',
                    'ip_address'  => null,
                ]);
            } catch (\Throwable) {
                // لا نُعطّل التصحيح بسبب فشل التسجيل
            }
        }

        $this->info("تمت مواءمة {$affected->count()} عاملة.");

        return self::SUCCESS;
    }
}
