<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Worker extends Model
{
    use HasFactory, SoftDeletes, \App\Models\Concerns\HasEncryptedPii;

    protected $fillable = [
        'name', 'passport_number', 'visa_number', 'nationality_id', 'profession',
        'gender', 'experience', 'religion', 'age', 'phone',
        'cv_path', 'original_cv_name', 'passport_image',
        'status', 'client_id', 'branch_id', 'admin_id',
        'assigned_by_admin_id', 'assigned_at',
        'notes', 'active',
    ];

    protected function casts(): array
    {
        return [
            'active'          => 'boolean',
            'assigned_at'     => 'datetime',
            'passport_number' => 'encrypted',
            'phone'           => 'encrypted',
        ];
    }

    /** Encrypted-at-rest PII fields and their searchable hash columns. */
    public function piiHashMap(): array
    {
        return [
            'passport_number' => 'passport_number_hash',
            'phone'           => 'phone_hash',
        ];
    }

    // ── Static option lists ──────────────────────────────────────────────────

    public static function professions(): array
    {
        return [
            'domestic_worker' => 'عاملة منزلية',
            'nanny'           => 'مربية أطفال',
            'cook'            => 'طباخة',
            'driver'          => 'سائق',
            'nurse'           => 'ممرضة',
            'housekeeper'     => 'مدبرة منزل',
            'security'        => 'حارس أمن',
            'other'           => 'أخرى',
        ];
    }

    public static function experienceOptions(): array
    {
        return [
            'none' => 'بدون خبرة',
            '1-3'  => '1-3 سنوات',
            '3-5'  => '3-5 سنوات',
            '5+'   => 'أكثر من 5 سنوات',
        ];
    }

    public static function genderOptions(): array
    {
        return ['female' => 'أنثى', 'male' => 'ذكر'];
    }

    public static function religionOptions(): array
    {
        return ['muslim' => 'مسلمة', 'christian' => 'مسيحية', 'other' => 'أخرى'];
    }

    /**
     * هل العاملة مرتبطة بعميل حالياً؟
     * الحجز (reserved) والتعيين (assigned) كلاهما يمنع إعادة التعيين
     * ويسمح بإلغائه — راجع WorkerRepository::assignToClient().
     */
    public function isBooked(): bool
    {
        return in_array($this->status, ['reserved', 'assigned'], true);
    }

    /**
     * من يحق له إلغاء التعيين: الموظف الذي أجرى الحجز نفسه فقط
     * (بالإضافة للسوبر أدمن). مدير الفرع لا يملك هذه الصلاحية.
     * نفس القاعدة المطبّقة في WorkerService::unassign().
     */
    public function canBeUnassignedBy(?Admin $actor): bool
    {
        if (! $actor || ! $this->isBooked()) {
            return false;
        }

        return $actor->isSuperAdmin()
            || $actor->id === $this->assigned_by_admin_id;
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'available'            => 'متاحة',
            'reserved'             => 'محجوزة',
            'assigned'             => 'تم التعيين',
            'in_housing'           => 'في السكن',
            'for_rent'             => 'للتأجير',
            'sponsorship_transfer' => 'نقل كفالة',
            'deportation'          => 'تسفير',
            'returned'             => 'عودة',
            default                => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'available'            => '#16a34a',
            'reserved'             => '#ca8a04',
            'assigned'             => '#2563eb',
            'in_housing'           => '#7c3aed',
            'for_rent'             => '#0891b2',
            'sponsorship_transfer' => '#d97706',
            'deportation'          => '#dc2626',
            'returned'             => '#64748b',
            default                => '#64748b',
        };
    }

    public function getStatusBgAttribute(): string
    {
        return match ($this->status) {
            'available'            => '#dcfce7',
            'reserved'             => '#fef9c3',
            'assigned'             => '#dbeafe',
            'in_housing'           => '#ede9fe',
            'for_rent'             => '#cffafe',
            'sponsorship_transfer' => '#fef3c7',
            'deportation'          => '#fee2e2',
            'returned'             => '#f1f5f9',
            default                => '#f1f5f9',
        };
    }

    public function getProfessionLabelAttribute(): string
    {
        return self::professions()[$this->profession] ?? $this->profession ?? '—';
    }

    public function getGenderLabelAttribute(): string
    {
        return self::genderOptions()[$this->gender] ?? '—';
    }

    public function getExperienceLabelAttribute(): string
    {
        return self::experienceOptions()[$this->experience] ?? '—';
    }

    // ── Relations ────────────────────────────────────────────────────────────

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

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

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_by_admin_id');
    }

    public function latestContract(): HasOne
    {
        return $this->hasOne(\App\Models\RecruitmentContract::class)->latest();
    }

    public function recruitmentContracts(): HasMany
    {
        return $this->hasMany(\App\Models\RecruitmentContract::class);
    }

    public function activeHousingAssignment(): HasOne
    {
        return $this->hasOne(\App\Models\HousingAssignment::class)->whereNull('check_out_date')->latest();
    }

    /** سجل التدقيق — من عدّل/حذف/حجز هذه العاملة. */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(\App\Models\WorkerActivityLog::class, 'worker_id')->with('admin')->latest();
    }

    /** True if the worker has an active contract for a specific client */
    public function hasContractForClient(int $clientId): bool
    {
        return $this->latestContract()->where('client_id', $clientId)->exists();
    }
}
