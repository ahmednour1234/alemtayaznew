<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NationalitySeeder extends Seeder
{
    public function run(): void
    {
        $nationalities = [
            // ── دول الخليج العربي ────────────────────────────────────────────
            ['name' => 'سعودية',    'code' => 'SA'],
            ['name' => 'إماراتية',  'code' => 'AE'],
            ['name' => 'كويتية',    'code' => 'KW'],
            ['name' => 'بحرينية',   'code' => 'BH'],
            ['name' => 'قطرية',     'code' => 'QA'],
            ['name' => 'عُمانية',   'code' => 'OM'],

            // ── دول عربية أخرى ───────────────────────────────────────────────
            ['name' => 'مصرية',     'code' => 'EG'],
            ['name' => 'أردنية',    'code' => 'JO'],
            ['name' => 'سورية',     'code' => 'SY'],
            ['name' => 'لبنانية',   'code' => 'LB'],
            ['name' => 'يمنية',     'code' => 'YE'],
            ['name' => 'عراقية',    'code' => 'IQ'],
            ['name' => 'سودانية',   'code' => 'SD'],
            ['name' => 'مغربية',    'code' => 'MA'],
            ['name' => 'تونسية',    'code' => 'TN'],
            ['name' => 'جزائرية',   'code' => 'DZ'],
            ['name' => 'ليبية',     'code' => 'LY'],
            ['name' => 'موريتانية', 'code' => 'MR'],
            ['name' => 'صومالية',   'code' => 'SO'],
            ['name' => 'جيبوتية',   'code' => 'DJ'],

            // ── دول آسيا (مصادر الاستقدام الرئيسية) ──────────────────────────
            ['name' => 'إندونيسية', 'code' => 'ID'],
            ['name' => 'فلبينية',   'code' => 'PH'],
            ['name' => 'هندية',     'code' => 'IN'],
            ['name' => 'باكستانية', 'code' => 'PK'],
            ['name' => 'بنغلاديشية','code' => 'BD'],
            ['name' => 'سريلانكية', 'code' => 'LK'],
            ['name' => 'نيبالية',   'code' => 'NP'],
            ['name' => 'ميانمارية', 'code' => 'MM'],
            ['name' => 'فيتنامية',  'code' => 'VN'],
            ['name' => 'كمبودية',   'code' => 'KH'],
            ['name' => 'لاوسية',    'code' => 'LA'],
            ['name' => 'تايلاندية', 'code' => 'TH'],
            ['name' => 'أفغانية',   'code' => 'AF'],

            // ── دول أفريقيا ───────────────────────────────────────────────────
            ['name' => 'إثيوبية',   'code' => 'ET'],
            ['name' => 'كينية',     'code' => 'KE'],
            ['name' => 'أوغندية',   'code' => 'UG'],
            ['name' => 'تنزانية',   'code' => 'TZ'],
            ['name' => 'رواندية',   'code' => 'RW'],
            ['name' => 'غانية',     'code' => 'GH'],
            ['name' => 'نيجيرية',   'code' => 'NG'],
            ['name' => 'كاميرونية', 'code' => 'CM'],
            ['name' => 'مدغشقرية',  'code' => 'MG'],
            ['name' => 'سيراليونية','code' => 'SL'],
            ['name' => 'إريترية',   'code' => 'ER'],
        ];

        foreach ($nationalities as $nat) {
            DB::table('nationalities')->updateOrInsert(
                ['code' => $nat['code']],
                array_merge($nat, ['active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
