<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HousingVisit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'housing_id',
        'admin_id',
        'visit_date',
        'documentation',
        'documentation_file',
        'branch_employee_notes',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function housing(): BelongsTo
    {
        return $this->belongsTo(Housing::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'housing_visit_admin')
            ->withTimestamps();
    }
}
