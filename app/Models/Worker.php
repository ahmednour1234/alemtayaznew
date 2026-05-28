<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Worker extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'passport_number', 'nationality_id', 'profession',
        'gender', 'experience', 'religion', 'age', 'phone',
        'cv_path', 'passport_image', 'status', 'client_id', 'branch_id', 'admin_id',
        'notes', 'active',
    ];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
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

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'available' => 'متاحة',
            'reserved'  => 'محجوزة',
            'assigned'  => 'تم التعيين',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'available' => '#16a34a',
            'reserved'  => '#ca8a04',
            'assigned'  => '#2563eb',
            default     => '#64748b',
        };
    }

    public function getStatusBgAttribute(): string
    {
        return match ($this->status) {
            'available' => '#dcfce7',
            'reserved'  => '#fef9c3',
            'assigned'  => '#dbeafe',
            default     => '#f1f5f9',
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

    public function latestContract(): HasOne
    {
        return $this->hasOne(\App\Models\RecruitmentContract::class)->latest();
    }
}
