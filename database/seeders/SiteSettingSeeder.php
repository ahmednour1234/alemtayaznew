<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * يزرع مفاتيح إعدادات الموقع بقيمها الافتراضية.
 * آمن لإعادة التشغيل: لا يمسّ أي مفتاح سبق للمسؤول ضبطه.
 */
class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (SiteSetting::defaults() as $key => $value) {
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
