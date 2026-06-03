<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HousingRental extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'housing_assignment_id', 'worker_id', 'branch_id', 'client_id', 'admin_id',
        'contract_number', 'rent_value', 'rent_start_date', 'rent_end_date',
        'contract_image', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'rent_value'      => 'decimal:2',
            'rent_start_date' => 'date',
            'rent_end_date'   => 'date',
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
