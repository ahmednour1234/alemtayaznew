<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HousingSettlement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'housing_assignment_id', 'worker_id', 'branch_id', 'client_id', 'admin_id',
        'reference_number', 'settlement_amount', 'settlement_type', 'settlement_date',
        'document_image', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'settlement_amount' => 'decimal:2',
            'settlement_date'   => 'date',
        ];
    }

    /** أنواع التسوية */
    public static function types(): array
    {
        return [
            'financial' => 'تسوية مالية',
            'contract'  => 'تسوية عقد',
            'dispute'   => 'تسوية نزاع',
            'other'     => 'أخرى',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(HousingAssignment::class, 'housing_assignment_id');
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
