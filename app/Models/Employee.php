<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes, \App\Models\Concerns\HasEncryptedPii;

    protected $fillable = [
        'name', 'employee_no', 'iqama_number', 'iqama_expiry_date',
        'job_title', 'phone', 'email', 'hire_date',
        'status', 'probation_end_date',
        'sponsorship_transferred_in', 'previous_sponsor',
        'sponsorship_transfer_date', 'sponsorship_notes',
        'branch_id', 'admin_id', 'notes', 'active',
    ];

    protected function casts(): array
    {
        return [
            'iqama_expiry_date'           => 'date',
            'hire_date'                   => 'date',
            'probation_end_date'          => 'date',
            'sponsorship_transfer_date'   => 'date',
            'sponsorship_transferred_in'  => 'boolean',
            'active'                      => 'boolean',
            'iqama_number'                => 'encrypted',
            'phone'                       => 'encrypted',
        ];
    }

    /** Encrypted-at-rest PII fields and their searchable hash columns. */
    public function piiHashMap(): array
    {
        return [
            'iqama_number' => 'iqama_hash',
            'phone'        => 'phone_hash',
        ];
    }

    // ── Static option lists ────────────────────────────────────────────────────

    public static function statuses(): array
    {
        return [
            'probation'  => ['label' => 'تحت التجربة', 'color' => '#ca8a04'],
            'active'     => ['label' => 'مثبّت',        'color' => '#16a34a'],
            'terminated' => ['label' => 'منتهي الخدمة', 'color' => '#dc2626'],
        ];
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return static::statuses()[$this->status]['label'] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return static::statuses()[$this->status]['color'] ?? '#64748b';
    }

    /** أيام متبقية على تجديد الإقامة (سالب = منتهية). */
    public function getIqamaDaysLeftAttribute(): ?int
    {
        if (! $this->iqama_expiry_date) {
            return null;
        }
        return (int) round(now()->startOfDay()->diffInDays($this->iqama_expiry_date->startOfDay(), false));
    }

    // ── Relations ────────────────────────────────────────────────────────────

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(EmployeeLeave::class)->latest('start_date');
    }

    public function insurances(): HasMany
    {
        return $this->hasMany(EmployeeMedicalInsurance::class)->latest('end_date');
    }
}
