<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * إعدادات الموقع العام (مفتاح/قيمة) مع كاش، فصفحات الموقع تُقرأ كثيراً.
 * أي كتابة تمسح الكاش تلقائياً عبر أحداث الموديل.
 */
class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    private const CACHE_KEY = 'site_settings_all';

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /** كل الإعدادات كمصفوفة مفتاح => قيمة. */
    public static function all_(): array
    {
        return Cache::rememberForever(
            self::CACHE_KEY,
            fn () => static::query()->pluck('value', 'key')->all()
        );
    }

    /** قيمة إعداد واحد مع قيمة افتراضية. */
    public static function get(string $key, ?string $default = null): ?string
    {
        $value = static::all_()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    /** القيم الافتراضية — تُستخدم قبل أن يملأ المسؤول الإعدادات. */
    public static function defaults(): array
    {
        return [
            'company_name'  => 'شركة الامتياز للاستقدام',
            'tagline'       => 'راحة بالك مسؤوليتنا',
            'phone'         => '',
            'whatsapp'      => '',
            'email'         => '',
            'address'       => '',
            'working_hours' => '',
            'about'         => '',
            'map_embed'     => '',
            'facebook'      => '',
            'twitter'       => '',
            'instagram'     => '',
            'snapchat'      => '',
            'tiktok'        => '',
        ];
    }

    /** قيمة إعداد مع الرجوع للافتراضي المعرّف في defaults(). */
    public static function value(string $key): ?string
    {
        return static::get($key, static::defaults()[$key] ?? null);
    }
}
