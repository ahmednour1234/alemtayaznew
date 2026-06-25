<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeMedicalInsurance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id', 'provider', 'policy_number', 'class',
        'start_date', 'end_date', 'cost', 'status', 'notes', 'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'cost'       => 'decimal:2',
        ];
    }

    public static function statuses(): array
    {
        return [
            'active'    => ['label' => 'سارية',   'color' => '#16a34a'],
            'expired'   => ['label' => 'منتهية',  'color' => '#dc2626'],
            'cancelled' => ['label' => 'ملغاة',   'color' => '#64748b'],
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return static::statuses()[$this->status]['label'] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return static::statuses()[$this->status]['color'] ?? '#64748b';
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
