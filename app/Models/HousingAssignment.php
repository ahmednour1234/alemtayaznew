<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HousingAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'worker_housing_assignments';

    protected $fillable = [
        'worker_id', 'housing_id', 'branch_id', 'admin_id',
        'check_in_date', 'check_out_date', 'expected_check_out_date', 'notes', 'reason',
        'worker_status',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date'           => 'date',
            'check_out_date'          => 'date',
            'expected_check_out_date' => 'date',
        ];
    }

    /** حالات العمالة المتاحة */
    public static function workerStatuses(): array
    {
        return [
            'normal'  => ['label' => 'نظامية', 'bg' => '#dcfce7', 'color' => '#16a34a'],
            'escaped' => ['label' => 'هاربة',  'bg' => '#fee2e2', 'color' => '#b91c1c'],
            'sick'    => ['label' => 'مريضة',  'bg' => '#fef3c7', 'color' => '#b45309'],
        ];
    }

    public function getWorkerStatusLabelAttribute(): string
    {
        return self::workerStatuses()[$this->worker_status]['label'] ?? $this->worker_status;
    }

    /** هل العاملة في فترة الضمان؟ (عقد استقدام وصل منذ أقل من 3 أشهر) */
    public function isInGuaranteePeriod(): bool
    {
        $contract = $this->worker?->recruitmentContracts()
            ->whereNotNull('arrival_date')
            ->latest('arrival_date')
            ->first();

        if (! $contract || ! $contract->arrival_date) {
            return false;
        }

        return $contract->arrival_date->diffInDays(now()) <= 90;
    }

    /** أسباب التسكين المتاحة */
    public static function reasons(): array
    {
        return [
            'sponsorship_transfer' => 'نقل كفالة',
            'deportation'          => 'تسفير',
            'handover'             => 'تسليم',
            'rental'               => 'تأجير',
            'settlement'           => 'تسوية',
        ];
    }

    /** خيارات وجهة العاملة عند المغادرة */
    public static function dispositions(): array
    {
        return [
            'rental'     => 'تأجير',
            'settlement' => 'تسوية',
        ];
    }

    public function getReasonLabelAttribute(): string
    {
        return self::reasons()[$this->reason] ?? ($this->reason ?? '—');
    }

    // ── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereNull('check_out_date');
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function housing(): BelongsTo
    {
        return $this->belongsTo(Housing::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function rental(): HasOne
    {
        return $this->hasOne(HousingRental::class);
    }

    public function settlement(): HasOne
    {
        return $this->hasOne(HousingSettlement::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return is_null($this->check_out_date);
    }

    public function daysStayed(): int
    {
        $end = $this->check_out_date ?? now();
        return (int) $this->check_in_date->diffInDays($end);
    }
}
