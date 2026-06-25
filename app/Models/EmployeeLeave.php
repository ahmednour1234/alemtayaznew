<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeLeave extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id', 'type', 'start_date', 'end_date', 'days',
        'status', 'reason', 'approved_by', 'decided_at', 'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'decided_at' => 'datetime',
            'days'       => 'integer',
        ];
    }

    public static function types(): array
    {
        return [
            'annual'    => 'سنوية',
            'sick'      => 'مرضية',
            'unpaid'    => 'بدون راتب',
            'emergency' => 'اضطرارية',
            'other'     => 'أخرى',
        ];
    }

    public static function statuses(): array
    {
        return [
            'pending'  => ['label' => 'قيد المراجعة', 'color' => '#ca8a04'],
            'approved' => ['label' => 'معتمدة',        'color' => '#16a34a'],
            'rejected' => ['label' => 'مرفوضة',        'color' => '#dc2626'],
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return static::types()[$this->type] ?? $this->type;
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }
}
