<?php

namespace App\Console\Commands;

use App\Models\Worker;
use App\Models\WorkerActivityLog;
use Illuminate\Console\Command;

/**
 * أمر تنظيف لمرة واحدة (يمكن إعادة تشغيله بأمان).
 *
 * بعض العاملات علقن في حالة «محجوزة/معيَّنة» بلا عميل وبلا عقد وبلا
 * assigned_at — نتيجة مسارات كانت تضع status = assigned دون تعبئة
 * client_id/assigned_at (أبرزها ContractImport حين يخلو الصف من عميل).
 *
 * أمر الـ72 ساعة لا يلتقطهنّ إطلاقاً لأنه يشترط
 * whereNotNull('client_id') و whereNotNull('assigned_at')،
 * فيبقين عالقات إلى الأبد. هذا الأمر يعيدهنّ «متاحة».
 *
 * نستثني من لديها عقد استقدام — فحالتها assigned صحيحة ومقصودة.
 */
class ReleaseStuckWorkerReservations extends Command
{
    protected $signature = 'workers:release-stuck
                            {--dry-run : عرض المتأثرات دون تعديل}';

    protected $description = 'إرجاع العاملات العالقات في حالة محجوزة/معيَّنة بلا عميل ولا عقد إلى «متاحة»';

    public function handle(): int
    {
        $workers = Worker::whereIn('status', ['reserved', 'assigned'])
            ->whereNull('client_id')
            ->whereNull('assigned_at')
            ->doesntHave('recruitmentContracts')
            ->get(['id', 'name', 'status']);

        if ($workers->isEmpty()) {
            $this->info('لا توجد عاملات عالقات.');
            return self::SUCCESS;
        }

        $this->table(
            ['#', 'الاسم', 'الحالة الحالية'],
            $workers->map(fn ($w) => [$w->id, $w->name, $w->status])->all()
        );

        if ($this->option('dry-run')) {
            $this->warn("تجربة فقط — {$workers->count()} عاملة ستتأثر. أعد التشغيل بدون --dry-run للتنفيذ.");
            return self::SUCCESS;
        }

        $ids = $workers->pluck('id');

        $count = Worker::whereIn('id', $ids)->update([
            'status'               => 'available',
            'client_id'            => null,
            'assigned_by_admin_id' => null,
            'assigned_at'          => null,
        ]);

        // تسجيل في سجل نشاط كل عاملة — الفاعل هو النظام لا مستخدم
        foreach ($workers as $w) {
            try {
                WorkerActivityLog::create([
                    'worker_id'   => $w->id,
                    'worker_name' => $w->name,
                    'admin_id'    => null,
                    'admin_name'  => 'النظام',
                    'action'      => 'unassigned',
                    'label'       => 'أرجع النظام العاملة إلى «متاحة» — كانت عالقة في حالة '
                                   . (Worker::statusOptions()[$w->status] ?? $w->status)
                                   . ' بلا عميل ولا عقد',
                    'ip_address'  => null,
                ]);
            } catch (\Throwable) {
                // لا نُعطّل التنظيف بسبب فشل التسجيل
            }
        }

        $this->info("تم إرجاع {$count} عاملة إلى حالة «متاحة».");

        return self::SUCCESS;
    }
}
