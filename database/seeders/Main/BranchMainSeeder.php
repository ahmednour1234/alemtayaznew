<?php

namespace Database\Seeders\Main;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchMainSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            ['name' => 'الرياض',       'code' => 'RYD-001', 'city' => 'الرياض',       'manager_name' => '', 'active' => true],
            ['name' => 'حفر الباطن',   'code' => 'HFR-001', 'city' => 'حفر الباطن',   'manager_name' => '', 'active' => true],
            ['name' => 'عرعر',         'code' => 'ARR-001', 'city' => 'عرعر',         'manager_name' => '', 'active' => true],
        ];

        foreach ($branches as $b) {
            Branch::firstOrCreate(['code' => $b['code']], $b);
        }

        $this->command->info('✓ Branches seeded (' . count($branches) . ')');
    }
}
