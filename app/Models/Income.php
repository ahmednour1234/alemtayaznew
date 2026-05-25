<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['branch_id', 'income_type_id', 'admin_id', 'amount', 'date', 'payment_method', 'reference_number', 'description', 'attachment'])]
class Income extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'date'   => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function incomeType(): BelongsTo
    {
        return $this->belongsTo(IncomeType::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
