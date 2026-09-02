<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\City;
use App\Models\Nationality;

class RecruitmentContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_number', 'client_id', 'branch_id', 'admin_id', 'request_date',
        'visa_cancelled_at', 'visa_cancelled_by_admin_id', 'previous_worker_id', 'visa_cancel_reason',
        'visa_image', 'visa_type', 'visa_number',
        'arrival_airport_id', 'origin_nationality_id', 'delivery_city_id',
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
            'visa_cancelled_at' => 'datetime',
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

    /**
     * مهلة كل مرحلة بالأيام (SLA) — منطق عمل لا يُترجم.
     * null = بلا مهلة محددة.
     */
    private const STAGE_DAYS = [
        1 => null, 2 => 5, 3 => 5, 4 => 5, 5 => 5, 6 => 4, 7 => 7, 8 => 10,
        9 => null, 10 => 6, 11 => null, 12 => null, 13 => null, 14 => null, 15 => null,
    ];

    /**
     * مراحل العقد الـ15. التسميات والأوصاف تأتي من ملفات الترجمة
     * contracts.php داخل مجلد lang فتتغيّر مع لغة الواجهة.
     */
    /** حالة «إلغاء التأشير» في مسار العقد. */
    public const STATUS_VISA_CANCELLED = 9;

    /** حالة «تم الاستلام» — بعدها لا يصحّ إلغاء التأشيرة. */
    public const STATUS_RECEIVED = 13;

    /** هل أُلغيت تأشيرة هذا العقد؟ */
    public function isVisaCancelled(): bool
    {
        return $this->visa_cancelled_at !== null;
    }

    /**
     * هل يمكن إلغاء تأشيرة هذا العقد؟
     *
     * الإلغاء مقصور على ما قبل التسليم: بعد وصول العاملة واستلامها يصبح
     * العقد منفَّذاً، وأي تراجع عنه يمرّ بمسار الرجيع لا بإلغاء التأشيرة.
     */
    public function canCancelVisa(): bool
    {
        return ! $this->isVisaCancelled()
            && $this->worker_id !== null
            && (int) $this->current_status < self::STATUS_RECEIVED;
    }

    /**
     * من يحق له إلغاء التأشيرة: قسم التنسيق وحده (والسوبر أدمن)،
     * فهو القسم الذي يتابع إجراءات التأشيرة لدى الجهات الخارجية.
     */
    public function canCancelVisaBy(?Admin $actor): bool
    {
        if (! $actor || ! $this->canCancelVisa()) {
            return false;
        }

        return $actor->isSuperAdmin() || $actor->department === 'coordination';
    }

    public static function statuses(): array
    {
        $out = [];

        foreach (self::STAGE_DAYS as $num => $days) {
            $out[$num] = [
                'label'   => __("contracts.statuses.{$num}"),
                'days'    => $days,
                'desc'    => __("contracts.status_desc.{$num}"),
                'example' => __("contracts.status_example.{$num}"),
            ];
        }

        return $out;
    }

    public static function visaTypes(): array
    {
        return [
            'domestic'       => __('contracts.visa_types.domestic'),
            'rehabilitation' => __('contracts.visa_types.rehabilitation'),
        ];
    }

    public static function paymentStatuses(): array
    {
        return [
            'pending' => __('contracts.payment.pending'),
            'partial' => __('contracts.payment.partial'),
            'full'    => __('contracts.payment.full'),
        ];
    }

    public static function departments(): array
    {
        return [
            'customer_service' => __('contracts.departments.customer_service'),
            'accounts'         => __('contracts.departments.accounts'),
            'coordination'     => __('contracts.departments.coordination'),
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

    public function deliveryCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'delivery_city_id');
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
