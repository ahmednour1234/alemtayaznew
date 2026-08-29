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

    /**
     * اسم الجنسية بلغة الواجهة الحالية.
     *
     * الاسم مخزَّن في قاعدة البيانات بالعربية فقط، فنترجمه عبر كود ISO
     * (lang/{locale}/nationalities.php). أي كود بلا ترجمة يعود لاسمه المخزَّن،
     * فلا تختفي جنسية من القوائم لمجرد نقص في ملف اللغة.
     */
    public function getDisplayNameAttribute(): string
    {
        $code = strtoupper((string) $this->code);

        if ($code === '') {
            return (string) $this->name;
        }

        $key = 'nationalities.' . $code;

        return \Illuminate\Support\Facades\Lang::has($key)
            ? __($key)
            : (string) $this->name;
    }

    /**
     * مفتاح الجنسية في روابط الموقع العام.
     * نستخدم كود ISO لأنه فريد ومستقر ولا يتغيّر بتغيّر الاسم العربي؛
     * ونعود للمعرّف الرقمي للجنسيات التي لا كود لها.
     */
    public function getRouteKey(): string
    {
        return $this->code ? strtolower($this->code) : (string) $this->id;
    }

    /** رابط صورة الجنسية، أو null إن لم تتوفر صورة لها. */
    public function photoUrl(): ?string
    {
        $code = strtoupper((string) $this->code);

        return in_array($code, self::PHOTO_CODES, true)
            ? asset('nationalities/nat_' . $code . '.jpg')
            : null;
    }
}
