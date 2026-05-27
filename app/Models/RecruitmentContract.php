<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecruitmentContract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_number', 'client_id', 'branch_id', 'admin_id', 'request_date',
        'visa_image', 'visa_type', 'visa_number',
        'arrival_airport_id', 'departure_airport_id', 'delivery_airport_id',
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
            1  => ['label' => 'جديد',                                        'days' => null],
            2  => ['label' => 'موافقة السفارة الأجنبية',                      'days' => 5],
            3  => ['label' => 'بانتظار موافقة المكتب الخارجي',               'days' => 5],
            4  => ['label' => 'تم قبول مكتب العمل الخارجي',                  'days' => 5],
            5  => ['label' => 'انتظار الابروف',                               'days' => 5],
            6  => ['label' => 'قبول العقد من مكتب العمل الخارجي',            'days' => 4],
            7  => ['label' => 'إرسال التأشيرة إلى السفارة السعودية',          'days' => 7],
            8  => ['label' => 'تم التأشير',                                   'days' => 10],
            9  => ['label' => 'إلغاء التأشير',                                'days' => null],
            10 => ['label' => 'تصريح سفر بعد تم التأشير',                    'days' => 6],
            11 => ['label' => 'انتظار حجز تذكرة الطيران',                    'days' => null],
            12 => ['label' => 'معاد الوصول',                                  'days' => null],
            13 => ['label' => 'تم الاستلام',                                  'days' => null],
            14 => ['label' => 'رجيع خلال فترة الضمان',                       'days' => null],
            15 => ['label' => 'هروب',                                         'days' => null],
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

    public function departureAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'departure_airport_id');
    }

    public function deliveryAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'delivery_airport_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ContractStatusHistory::class, 'contract_id')->orderBy('status');
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
