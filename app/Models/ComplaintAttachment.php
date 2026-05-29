<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComplaintAttachment extends Model
{
    use HasFactory;
    protected $fillable = [
        'complaint_id', 'path', 'original_name', 'mime_type', 'size',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function getIsImageAttribute(): bool
    {
        return $this->mime_type !== null && str_starts_with($this->mime_type, 'image/');
    }
}
