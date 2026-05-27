<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Nationality;

class RecruitmentContract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_number', 'client_id', 'branch_id', 'admin_id', 'request_date',
        'visa_image', 'visa_type', 'visa_number',
        'arrival_airport_id', 'origin_nationality_id', 'delivery_airport_id',
        'musaned_number', 'musaned_date', 'musaned_file',
        'worker_id', 'e_doc_number', 'agent_id',
        'current_department', 'current_status',
        'payment_status', 'total_cost',
        'arrival_date', 'trial_end_date', 'contract_end_date',
        'notes', 'client_sms', 'client_rating', 'rating_image', 'active',
    ];

    protected function casts(): array
    {
        return [
            'request_date'      => 'date',
            'musaned_date'      => 'date',
            'arrival_date'      => 'date',
            'trial_end_date'    => 'date',
            'contract_end_date' => 'date',
            'total_cost'        => 'decimal:2',
            'client_sms'        => 'boolean',
            'client_rating'     => 'boolean',
            'active'            => 'boolean',
        ];
    }

    // ── Static data ──────────────────────────────────────────────────────────

    public static function statuses(): array
    {
        return [
            1  => ['label' => 'جديد',                                        'days' => null,  'desc' => 'استلام طلب العميل وفتح ملف العقد',                                  'example' => 'مثال: 2026-01-10 (تاريخ استلام طلب العميل)'],
            2  => ['label' => 'موافقة السفارة الأجنبية',                      'days' => 5,    'desc' => 'إرسال ملف العاملة إلى السفارة الأجنبية وانتظار الموافقة',             'example' => 'مثال: 2026-01-15 (تاريخ تقديم الملف للسفارة)'],
            3  => ['label' => 'بانتظار موافقة المكتب الخارجي',               'days' => 5,    'desc' => 'إرسال العقد إلى المكتب الخارجي في بلد العاملة وانتظار رده',           'example' => 'مثال: 2026-01-20 (تاريخ إرسال العقد للمكتب)'],
            4  => ['label' => 'تم قبول مكتب العمل الخارجي',                  'days' => 5,    'desc' => 'وصول رد القبول الرسمي من المكتب الخارجي',                             'example' => 'مثال: 2026-01-25 (تاريخ وصول رد القبول)'],
            5  => ['label' => 'انتظار الابروف',                               'days' => 5,    'desc' => 'انتظار صدور الابروف (الموافقة الإلكترونية) من منصة مساند',             'example' => 'مثال: 2026-01-28 (تاريخ طلب الابروف من مساند)'],
            6  => ['label' => 'قبول العقد من مكتب العمل الخارجي',            'days' => 4,    'desc' => 'توقيع العقد النهائي وتأكيده من الطرف الخارجي',                         'example' => 'مثال: 2026-02-01 (تاريخ توقيع العقد)'],
            7  => ['label' => 'إرسال التأشيرة إلى السفارة السعودية',          'days' => 7,    'desc' => 'تسليم جواز العاملة مع ملفها إلى السفارة السعودية للتأشير',             'example' => 'مثال: 2026-02-05 (تاريخ إرسال جواز العاملة)'],
            8  => ['label' => 'تم التأشير',                                   'days' => 10,   'desc' => 'استلام جواز العاملة مختوماً من السفارة السعودية',                      'example' => 'مثال: 2026-02-15 (تاريخ استلام الجواز المختوم)'],
            9  => ['label' => 'إلغاء التأشير',                                'days' => null, 'desc' => 'رفض أو إلغاء التأشيرة من السفارة لأي سبب كان',                       'example' => 'مثال: 2026-02-10 (تاريخ قرار الرفض)'],
            10 => ['label' => 'تصريح سفر بعد تم التأشير',                    'days' => 6,    'desc' => 'استخراج تصريح السفر الرسمي للعاملة من الجهات المختصة',                 'example' => 'مثال: 2026-02-20 (تاريخ صدور تصريح السفر)'],
            11 => ['label' => 'انتظار حجز تذكرة الطيران',                    'days' => null, 'desc' => 'التنسيق مع وكالة السفر وحجز تذكرة الطيران',                           'example' => 'مثال: 2026-02-22 (تاريخ طلب الحجز)'],
            12 => ['label' => 'معاد الوصول',                                  'days' => null, 'desc' => 'تحديد رحلة الطيران وموعد وصول العاملة إلى المطار',                    'example' => 'مثال: 2026-03-01 (تاريخ ورقم الرحلة)'],
            13 => ['label' => 'تم الاستلام',                                  'days' => null, 'desc' => 'استلام العاملة من المطار وتسليمها رسمياً إلى العميل',                 'example' => 'مثال: 2026-03-01 (تاريخ التسليم الفعلي)'],
            14 => ['label' => 'رجيع خلال فترة الضمان',                       'days' => null, 'desc' => 'إعادة العاملة إلى الشركة خلال فترة الضمان (سنتان من تاريخ الوصول)',      'example' => 'مثال: 2026-04-10 (تاريخ الإعادة وسبب الرجيع)'],
            15 => ['label' => 'هروب',                                         'days' => null, 'desc' => 'تسجيل حالة هروب العاملة والإبلاغ عنها',                               'example' => 'مثال: 2026-04-15 (تاريخ الإبلاغ عن الهروب)'],
        ];
    }

    public static function visaTypes(): array
    {
        return [
            'domestic'       => 'تأشيرة عمالة منزلية',
            'rehabilitation' => 'تأشيرة التأهيل الشامل',
        ];
    }

    public static function paymentStatuses(): array
    {
        return [
            'pending' => 'معلق',
            'partial' => 'جزئي',
            'full'    => 'كامل',
        ];
    }

    public static function departments(): array
    {
        return [
            'customer_service' => 'قسم خدمة عملاء',
            'accounts'         => 'قسم حسابات',
            'coordination'     => 'قسم تنسيق',
        ];
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->current_status]['label'] ?? "مرحلة {$this->current_status}";
    }

    public function getDepartmentLabelAttribute(): string
    {
        return self::departments()[$this->current_department] ?? $this->current_department;
    }

    public function getPaymentLabelAttribute(): string
    {
        return self::paymentStatuses()[$this->payment_status] ?? $this->payment_status;
    }

    public function getVisaTypeLabelAttribute(): string
    {
        return self::visaTypes()[$this->visa_type] ?? $this->visa_type ?? '—';
    }

    // ── Relations ─────────────────────────────────────────────────────────────

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function arrivalAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'arrival_airport_id');
    }

    public function originNationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class, 'origin_nationality_id');
    }

    public function deliveryAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'delivery_airport_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ContractStatusHistory::class, 'contract_id')->orderBy('status');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ContractActivityLog::class, 'contract_id')->with('admin')->latest();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Generate a unique contract number like: RC-2026-00001 */
    public static function generateNumber(): string
    {
        $year = now()->year;
        $last = static::withTrashed()->whereYear('created_at', $year)->count();
        return 'RC-' . $year . '-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }
}
