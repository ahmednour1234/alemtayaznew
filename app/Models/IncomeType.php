<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'description', 'active'])]
class IncomeType extends Model
{
    use SoftDeletes;

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
