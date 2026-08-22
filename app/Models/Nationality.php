<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nationality extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'code', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class);
    }

    /**
     * صور الجنسيات المعروضة في الموقع العام، مفهرسة بكود ISO.
     * الكود أثبت من الاسم لأن التسميات العربية تختلف بين مصادر البيانات
     * («بنغلاديش» / «بنجلاديشية»، «فليبين» / «الفلبين»).
     */
    private const PHOTOS = [
        'ET' => '02_worker_ethiopia.jpg',
        'LK' => '03_worker_sri_lanka.jpg',
        'KE' => '04_worker_kenya.jpg',
        'BD' => '05_worker_bangladesh.jpg',
        'PH' => '06_worker_philippines.jpg',
    ];

    /** رابط صورة الجنسية، أو null إن لم تتوفر صورة لها. */
    public function photoUrl(): ?string
    {
        $file = self::PHOTOS[strtoupper((string) $this->code)] ?? null;

        return $file ? asset($file) : null;
    }
}
