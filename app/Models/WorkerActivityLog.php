<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * سجل تدقيق للعمالة — من أنشأ/عدّل/حذف/استعاد/حجز عاملة ومتى.
 *
 * نحفظ اسم العاملة واسم الموظّف نصّاً وقت الإجراء، فيبقى السجل مفهوماً
 * حتى بعد الحذف النهائي للعاملة أو حذف حساب الموظّف.
 */
class WorkerActivityLog extends Model
{
    protected $fillable = [
        'worker_id', 'worker_name',
        'admin_id', 'admin_name',
        'action', 'label', 'ip_address',
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            'created'     => 'إضافة',
            'updated'     => 'تعديل',
            'deleted'     => 'حذف',
            'restored'    => 'استعادة',
            'assigned'    => 'حجز/تعيين',
            'unassigned'  => 'إلغاء التعيين',
            'cv_uploaded' => 'رفع CV',
            default       => $this->action,
        };
    }

    public function actionIcon(): string
    {
        return match ($this->action) {
            'created'     => 'M12 4v16m8-8H4',
            'updated'     => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            'deleted'     => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M4 7h16',
            'restored'    => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            'assigned'    => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'unassigned'  => 'M10 14L21 3m0 0h-5m5 0v5M3 3l7.07 7.07',
            default       => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        };
    }

    public function actionColor(): string
    {
        return match ($this->action) {
            'created'     => 'text-blue-600 bg-blue-50',
            'updated'     => 'text-amber-600 bg-amber-50',
            'deleted'     => 'text-red-600 bg-red-50',
            'restored'    => 'text-emerald-600 bg-emerald-50',
            'assigned'    => 'text-indigo-600 bg-indigo-50',
            'unassigned'  => 'text-orange-600 bg-orange-50',
            'cv_uploaded' => 'text-violet-600 bg-violet-50',
            default       => 'text-slate-600 bg-slate-50',
        };
    }
}
