<?php

namespace Database\Seeders\Main;

use App\Models\IncomeType;
use Illuminate\Database\Seeder;

class IncomeTypeMainSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'إيرادات الاستقدام',           'description' => 'إيرادات من عمليات الاستقدام وتوظيف العمالة'],
            ['name' => 'إيرادات التفاوضات الرجالية',  'description' => 'إيرادات من تفاوضات توظيف العمالة'],
            ['name' => 'إيرادات الـ PVT',              'description' => 'إيرادات PVT'],
            ['name' => 'إيرادات التنازلات',            'description' => 'إيرادات من التنازل عن العقود'],
            ['name' => 'إيرادات التسويات',             'description' => 'إيرادات من تسويات العقود'],
            ['name' => 'إيرادات الإيجارات',            'description' => 'إيرادات من تأجير العمالة'],
            ['name' => 'إيرادات أخرى',                 'description' => 'إيرادات متنوعة أخرى'],
        ];

        foreach ($types as $t) {
            IncomeType::firstOrCreate(
                ['name' => $t['name']],
                array_merge($t, ['active' => true])
            );
        }

        $this->command->info('✓ Income types seeded (' . count($types) . ')');
    }
}
