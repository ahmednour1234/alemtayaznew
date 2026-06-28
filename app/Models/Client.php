<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory, SoftDeletes, \App\Models\Concerns\HasEncryptedPii;

    protected $fillable = [
        'name', 'national_id', 'phone', 'marital_status', 'classification',
        'national_id_image',
        'required_nationality_id', 'worker_type', 'monthly_salary',
        'branch_id', 'admin_id', 'notes', 'active',
    ];

    protected function casts(): array
    {
        return [
            'active'         => 'boolean',
            'monthly_salary' => 'decimal:2',
            'national_id'    => 'encrypted',
            'phone'          => 'encrypted',
        ];
    }

    /** Encrypted-at-rest PII fields and their searchable hash columns. */
    public function piiHashMap(): array
    {
        return [
            'national_id' => 'national_id_hash',
            'phone'       => 'phone_hash',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function requiredNationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class, 'required_nationality_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(RecruitmentContract::class)->latest();
    }

    // ── Labels ──────────────────────────────────────────────────────────────
    public function getMaritalStatusLabelAttribute(): ?string
    {
        return match ($this->marital_status) {
            'single'   => 'أعزب',
            'married'  => 'متزوج',
            'divorced' => 'مطلق',
            'widowed'  => 'أرمل',
            default    => $this->marital_status,
        };
    }

    public function getClassificationLabelAttribute(): string
    {
        return match ($this->classification) {
            'potential' => 'محتمل',
            'confirmed' => 'مؤكد',
            'premium'   => 'مميز',
            'blocked'   => 'محظور',
            default     => $this->classification,
        };
    }

    public function getClassificationColorAttribute(): string
    {
        return match ($this->classification) {
            'potential' => '#9333ea',
            'confirmed' => '#16a34a',
            'premium'   => '#ca8a04',
            'blocked'   => '#dc2626',
            default     => '#64748b',
        };
    }
}
