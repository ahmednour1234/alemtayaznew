<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NationalitySeeder extends Seeder
{
    public function run(): void
    {
        $nationalities = [
            ['name' => 'بنغلاديش', 'code' => 'BD'],
            ['name' => 'سريلانكا', 'code' => 'LK'],
            ['name' => 'فليبين',   'code' => 'PH'],
            ['name' => 'كينيا',    'code' => 'KE'],
            ['name' => 'إثيوبيا',  'code' => 'ET'],
            ['name' => 'بوروندي',  'code' => 'BI'],
        ];

        foreach ($nationalities as $nat) {
            DB::table('nationalities')->updateOrInsert(
                ['code' => $nat['code']],
                array_merge($nat, ['active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
