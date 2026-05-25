<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'code', 'phone', 'address', 'city', 'manager_name', 'active'])]
class Branch extends Model
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

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function transfersFrom(): HasMany
    {
        return $this->hasMany(FinancialTransfer::class, 'from_branch_id');
    }

    public function transfersTo(): HasMany
    {
        return $this->hasMany(FinancialTransfer::class, 'to_branch_id');
    }
}
