<?php

namespace App\Services\CvPanel;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Client;
use App\Models\Worker;
use App\Models\WorkerActivityLog;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * حجز السير الذاتية من لوحة CV.
 *
 * العميل يرى السيرة في الصفحة العامة ويشاركها عبر واتساب مع خدمة العملاء،
 * فيحجزها الموظّف هنا لمدة 72 ساعة. الحجز يستخدم نفس حقول العاملة المستعملة
 * في النظام الأساسي (client_id / assigned_at / assigned_by_admin_id)، فتظل
 * العاملة المحجوزة قابلة لإنشاء عقد استقدام لها هناك بلا ازدواج بيانات.
 *
 * ما يميّز هذه اللوحة هو وجهة الإشعار: منسّقو الجنسية المعنيّة وحدهم — لا
 * منسّقو الفرع كافة — لأن الإسناد هنا بالجنسية.
 */
class CvReservationService
{
    /**
     * يحجز عاملة لعميل لمدة محدودة.
     *
     * @throws RuntimeException إن لم تعد العاملة متاحة
     */
    public function reserve(Worker $worker, Client $client, Admin $actor): Worker
    {
        return DB::transaction(function () use ($worker, $client, $actor) {
            // نُعيد التحميل مع قفل السطر: بين عرض الصفحة والضغط على الزر قد
            // يكون موظّف آخر قد حجزها، والقفل يمنع حجزين متزامنين.
            $fresh = Worker::whereKey($worker->id)->lockForUpdate()->firstOrFail();

            if ($fresh->status !== 'available' || $fresh->client_id !== null) {
                throw new RuntimeException(__('cv-panel.reserve.unavailable'));
            }

            if (! $fresh->active) {
                throw new RuntimeException(__('cv-panel.reserve.unavailable'));
            }

            $fresh->update([
                'client_id'            => $client->id,
                'status'               => 'reserved',
                'assigned_by_admin_id' => $actor->id,
                'assigned_at'          => now(),
            ]);

            $this->log(
                $fresh,
                $actor,
                "حجز السيرة الذاتية للعميل «{$client->name}» لمدة {$fresh->reservationHours()} ساعة عبر لوحة السير الذاتية"
            );

            $this->notifyCoordinators($fresh, $client, $actor);

            return $fresh;
        });
    }

    /**
     * يُشعر منسّقي الجنسية بأن سيرة من جنسيتهم حُجزت، ليتابعوا إنشاء العقد.
     *
     * المخاطَبون هم المسندة إليهم هذه الجنسية تحديداً؛ ولو لم يكن للجنسية
     * منسّق بعد فلا يضيع الإشعار — يذهب إلى المديرين حتى لا يمرّ الحجز بلا
     * متابعة حتى انتهاء المهلة.
     */
    private function notifyCoordinators(Worker $worker, Client $client, Admin $actor): void
    {
        $recipients = $this->coordinatorsFor($worker->nationality_id);

        if ($recipients->isEmpty()) {
            // «سوبر أدمن» دور لا عمود، فنجمعه عبر العلاقة لا بشرط على الجدول
            $recipients = Admin::where('active', true)
                ->where(function ($q) {
                    $q->whereIn('department', ['branch_manager', 'chairman'])
                      ->orWhereHas('roles', fn ($r) => $r->where('slug', 'super-admin'));
                })
                ->get();
        }

        $hours   = $worker->reservationHours();
        $natName = $worker->nationality?->display_name ?? '—';
        $title   = 'حجز سيرة ذاتية — بانتظار إنشاء العقد';
        $body    = "حجز {$actor->name} السيرة الذاتية «{$worker->name}» ({$natName}) للعميل «{$client->name}» "
                 . "لمدة {$hours} ساعة. أكّد إنشاء عقد الاستقدام قبل انتهاء المهلة وإلا عادت متاحة تلقائياً.";
        $url     = route('admin.workers.show', $worker->id);

        foreach ($recipients as $admin) {
            AdminNotification::create([
                'admin_id' => $admin->id,
                'type'     => 'cv_reserved',
                'title'    => $title,
                'body'     => $body,
                'url'      => $url,
            ]);
        }
    }

    /** منسّقو جنسية بعينها. */
    public function coordinatorsFor(?int $nationalityId)
    {
        if (! $nationalityId) {
            return Admin::query()->whereRaw('1 = 0')->get();
        }

        return Admin::where('active', true)
            ->where('department', 'coordination')
            ->whereHas('managedNationalities', fn ($q) => $q->where('nationalities.id', $nationalityId))
            ->get();
    }

    /** التسجيل لا يجب أن يُفشل الحجز نفسه. */
    private function log(Worker $worker, Admin $actor, string $label): void
    {
        try {
            WorkerActivityLog::create([
                'worker_id'   => $worker->id,
                'worker_name' => $worker->name,
                'admin_id'    => $actor->id,
                'admin_name'  => $actor->name,
                'action'      => 'assigned',
                'label'       => $label,
                'ip_address'  => request()?->ip(),
            ]);
        } catch (\Throwable) {
        }
    }
}
