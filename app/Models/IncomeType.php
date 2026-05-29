<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncomeType extends Model
{
    protected $fillable = ['name', 'description', 'active'];
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }
}
