<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agent extends Model
{
    use HasFactory, SoftDeletes, \App\Models\Concerns\HasEncryptedPii;

    protected $fillable = [
        'name', 'phone', 'email', 'nationality_id', 'document', 'notes', 'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'phone'  => 'encrypted',
        ];
    }

    /** Encrypted-at-rest PII fields and their searchable hash columns. */
    public function piiHashMap(): array
    {
        return ['phone' => 'phone_hash'];
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }
}
