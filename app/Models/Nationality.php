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
     * أكواد ISO التي تتوفّر لها صورة في public/nationalities/nat_{CODE}.jpg
     *
     * نعتمد الكود لا الاسم لأن التسميات العربية تختلف بين مصادر البيانات
     * («بنغلاديش» / «بنجلاديشية»، «فليبين» / «الفلبين»).
     */
    private const PHOTO_CODES = ['ET', 'PH', 'LK', 'BD', 'KE', 'BI', 'UG'];

    /** رابط صورة الجنسية، أو null إن لم تتوفر صورة لها. */
    public function photoUrl(): ?string
    {
        $code = strtoupper((string) $this->code);

        return in_array($code, self::PHOTO_CODES, true)
            ? asset('nationalities/nat_' . $code . '.jpg')
            : null;
    }
}
