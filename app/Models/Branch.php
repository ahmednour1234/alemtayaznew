<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    protected $fillable = ['name', 'code', 'phone', 'address', 'city', 'manager_name', 'active'];
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
