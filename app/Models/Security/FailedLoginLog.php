<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Model;

class FailedLoginLog extends Model
{
    public const UPDATED_AT = null; // append-only: created_at only

    protected $fillable = [
        'email', 'guard', 'ip_address', 'user_agent', 'failure_reason',
    ];
}
