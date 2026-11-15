<?php

namespace App\Services\CvPanel;

use App\Models\Worker;
use App\Models\WorkerActivityLog;
use Illuminate\Support\Facades\DB;

/**
 * تنظيف سير جنسية عند رفع دفعة جديدة.
 *
 * المنسّق يرفع دفعة سير جديدة لجنسية فتحلّ محلّ القديمة. القديمة تُحذف حذفاً
 * ناعماً (SoftDeletes) فتبقى في قاعدة البيانات وتبقى ملفاتها على القرص، ولا
 * يُفقد شيء — إنّما تختفي من اللوحة ومن الموقع العام.
 *
 * ما لا يُحذف أبداً: كل سيرة عليها ارتباط قائم — محجوزة، أو مُسندة لعميل،
 * أو لها عقد استقدام. هذه التزامات جارية لا تُمسّ بعملية تنظيف.
 */
class CvPurgeService
{
    /**
     * السير المرشّحة للحذف في جنسية بعينها.
     *
     * تُستخدم مرّتين: لعرض العدد على المنسّق قبل أن يقرّر، ثمّ للحذف نفسه
     * بعد موافقته — فيبقى ما عُرض عليه هو ما يُحذف فعلاً.
     */
    public function candidates(int $nationalityId, bool $transfer = false)
    {
        return Worker::query()
            ->where('nationality_id', $nationalityId)
            // التنظيف داخل النوع نفسه: دفعة استقدام لا تمسّ سير نقل الكفالة والعكس
            ->where('is_transfer', $transfer)
            ->whereNotNull('cv_path')
            // المتاحة وحدها: المحجوزة والمُسندة التزام قائم
            ->where('status', 'available')
            ->whereNull('client_id')
            // ولا سيرة لها عقد استقدام مهما كانت حالتها
            ->whereDoesntHave('latestContract');
    }

    public function countFor(int $nationalityId, bool $transfer = false): int
    {
        return $this->candidates($nationalityId, $transfer)->count();
    }

    /**
     * ينفّذ الحذف الناعم ويسجّله في سجلّ نشاط كل عاملة.
     *
     * @return int عدد ما حُذف
     */
    public function purge(int $nationalityId, $actor, bool $transfer = false): int
    {
        return DB::transaction(function () use ($nationalityId, $actor, $transfer) {
            // نقرأ القائمة داخل المعاملة: لو حُجزت سيرة بين العرض والتنفيذ
            // خرجت من القائمة ولم تُحذف.
            $workers = $this->candidates($nationalityId, $transfer)->get();

            foreach ($workers as $worker) {
                $this->log($worker, $actor);
                $worker->delete(); // حذف ناعم — الصفّ والملف باقيان
            }

            return $workers->count();
        });
    }

    /** التسجيل لا يجب أن يُفشل الحذف نفسه. */
    private function log(Worker $worker, $actor): void
    {
        try {
            WorkerActivityLog::create([
                'worker_id'   => $worker->id,
                'worker_name' => $worker->name,
                'admin_id'    => $actor?->id,
                'admin_name'  => $actor?->name ?? 'النظام',
                'action'      => 'deleted',
                'label'       => 'حُذفت ضمن تنظيف سير الجنسية عند رفع دفعة جديدة'
                    . ' (حذف ناعم — السجلّ والملف محفوظان)',
                'ip_address'  => request()?->ip(),
            ]);
        } catch (\Throwable) {
            // لا نُعطّل التنظيف بسبب فشل التسجيل
        }
    }
}
