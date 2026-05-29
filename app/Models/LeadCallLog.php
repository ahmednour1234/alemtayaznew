<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadCallLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'lead_id', 'admin_id', 'status', 'notes', 'follow_up_at',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_at' => 'datetime',
        ];
    }

    public static function statuses(): array
    {
        return [
            'no_answer'               => ['label' => 'لم يرد',                              'color' => 'bg-slate-100 text-slate-600'],
            'not_suitable'            => ['label' => 'غير مناسب',                           'color' => 'bg-red-100 text-red-700'],
            'nationality_unavailable' => ['label' => 'الجنسية غير متوفرة',                  'color' => 'bg-orange-100 text-orange-700'],
            'wants_rent'              => ['label' => 'يريد إيجار',                           'color' => 'bg-purple-100 text-purple-700'],
            'profiles_rejected'       => ['label' => 'السيفات غير مناسبة',                  'color' => 'bg-yellow-100 text-yellow-700'],
            'need_followup'           => ['label' => 'يحتاج متابعة',                        'color' => 'bg-blue-100 text-blue-700'],
            'converted'               => ['label' => 'تم التحويل لعميل',                    'color' => 'bg-green-100 text-green-700'],
            'wrong_number'            => ['label' => 'رقم خاطئ',                            'color' => 'bg-gray-100 text-gray-600'],
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
