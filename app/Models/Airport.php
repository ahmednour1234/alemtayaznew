<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Airport extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'code', 'city', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
}
