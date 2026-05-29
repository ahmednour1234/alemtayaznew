<?php

namespace Database\Seeders\Main;

use App\Models\Nationality;
use Illuminate\Database\Seeder;

class NationalityMainSeeder extends Seeder
{
    public function run(): void
    {
        $nationalities = [
            ['name' => 'إثيوبيا',    'code' => 'ET'],
            ['name' => 'أوغندا',     'code' => 'UG'],
            ['name' => 'كينيا',      'code' => 'KE'],
            ['name' => 'سريلانكا',   'code' => 'LK'],
            ['name' => 'الفلبين',    'code' => 'PH'],
            ['name' => 'بنجلاديش',   'code' => 'BD'],
            ['name' => 'بوروندي',    'code' => 'BI'],
        ];

        foreach ($nationalities as $nat) {
            Nationality::firstOrCreate(
                ['code' => $nat['code']],
                array_merge($nat, ['active' => true])
            );
        }

        $this->command->info('✓ Nationalities seeded (' . count($nationalities) . ')');
    }
}
