<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'email', 'nationality_id', 'document', 'notes', 'active',
    ];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }
}
