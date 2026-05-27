<?php

namespace Database\Seeders;

use App\Models\Airport;
use App\Models\Nationality;
use Illuminate\Database\Seeder;

class NationalityAirportSeeder extends Seeder
{
    public function run(): void
    {
        // ── Nationalities ─────────────────────────────────────────────────────
        $nationalities = [
            ['name' => 'إندونيسية',    'code' => 'ID'],
            ['name' => 'إثيوبية',      'code' => 'ET'],
            ['name' => 'أثيوبية',      'code' => 'ET2'],
            ['name' => 'بنجلاديشية',   'code' => 'BD'],
            ['name' => 'فلبينية',      'code' => 'PH'],
            ['name' => 'سريلانكية',    'code' => 'LK'],
            ['name' => 'كينية',        'code' => 'KE'],
            ['name' => 'أوغندية',      'code' => 'UG'],
            ['name' => 'بوروندية',     'code' => 'BI'],
            ['name' => 'غانية',        'code' => 'GH'],
            ['name' => 'تنزانية',      'code' => 'TZ'],
            ['name' => 'نيجيرية',      'code' => 'NG'],
            ['name' => 'سنغالية',      'code' => 'SN'],
            ['name' => 'كاميرونية',    'code' => 'CM'],
            ['name' => 'هندية',        'code' => 'IN'],
            ['name' => 'باكستانية',    'code' => 'PK'],
            ['name' => 'نيبالية',      'code' => 'NP'],
        ];

        foreach ($nationalities as $nat) {
            Nationality::firstOrCreate(['name' => $nat['name']], array_merge($nat, ['active' => true]));
        }

        // ── Saudi Airports ────────────────────────────────────────────────────
        $airports = [
            ['name' => 'مطار الملك عبدالعزيز الدولي',               'code' => 'JED', 'city' => 'جدة'],
            ['name' => 'مطار الملك خالد الدولي',                     'code' => 'RUH', 'city' => 'الرياض'],
            ['name' => 'مطار الملك فهد الدولي',                      'code' => 'DMM', 'city' => 'الدمام'],
            ['name' => 'مطار الأمير محمد بن عبدالعزيز الدولي',      'code' => 'MED', 'city' => 'المدينة المنورة'],
            ['name' => 'مطار الطائف الإقليمي',                       'code' => 'TIF', 'city' => 'الطائف'],
            ['name' => 'مطار أبها الإقليمي',                         'code' => 'AHB', 'city' => 'أبها'],
            ['name' => 'مطار الأمير عبدالمحسن بن عبدالعزيز',        'code' => 'YNB', 'city' => 'ينبع'],
            ['name' => 'مطار تبوك الإقليمي',                         'code' => 'TUU', 'city' => 'تبوك'],
            ['name' => 'مطار جازان الإقليمي',                        'code' => 'GIZ', 'city' => 'جازان'],
            ['name' => 'مطار نجران الإقليمي',                        'code' => 'EAM', 'city' => 'نجران'],
            ['name' => 'مطار القصيم الإقليمي',                       'code' => 'GAS', 'city' => 'بريدة'],
            ['name' => 'مطار حائل الإقليمي',                         'code' => 'HAS', 'city' => 'حائل'],
            ['name' => 'مطار عرعر الإقليمي',                         'code' => 'RAE', 'city' => 'عرعر'],
            ['name' => 'مطار سكاكا الإقليمي',                        'code' => 'AJF', 'city' => 'الجوف'],
            ['name' => 'مطار بيشة الإقليمي',                         'code' => 'BHH', 'city' => 'بيشة'],
        ];

        foreach ($airports as $airport) {
            Airport::firstOrCreate(['code' => $airport['code']], array_merge($airport, ['active' => true]));
        }
    }
}
