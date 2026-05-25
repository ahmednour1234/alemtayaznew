<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\ExpenseType;
use App\Models\IncomeType;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            ['name' => 'الفرع الرئيسي',  'code' => 'HQ-001',  'city' => 'الرياض',  'manager_name' => 'أحمد محمد',  'active' => true],
            ['name' => 'فرع جدة',         'code' => 'JED-001', 'city' => 'جدة',     'manager_name' => 'خالد عبدالله', 'active' => true],
            ['name' => 'فرع الدمام',      'code' => 'DAM-001', 'city' => 'الدمام',  'manager_name' => 'سعد الزهراني', 'active' => true],
        ];
        foreach ($branches as $b) {
            Branch::firstOrCreate(['code' => $b['code']], $b);
        }

        $incomeTypes = [
            ['name' => 'إيراد مبيعات',   'description' => 'إيرادات من المبيعات',   'active' => true],
            ['name' => 'إيراد خدمات',    'description' => 'إيرادات من الخدمات',    'active' => true],
            ['name' => 'إيراد استثمارات','description' => 'إيرادات من الاستثمارات', 'active' => true],
        ];
        foreach ($incomeTypes as $t) {
            IncomeType::firstOrCreate(['name' => $t['name']], $t);
        }

        $expenseTypes = [
            ['name' => 'رواتب',          'description' => 'مصروف الرواتب الشهرية',  'active' => true],
            ['name' => 'إيجار',          'description' => 'مصروف الإيجار',          'active' => true],
            ['name' => 'مستلزمات مكتبية','description' => 'مشتريات مكتبية',         'active' => true],
            ['name' => 'كهرباء ومياه',   'description' => 'فواتير خدمات',           'active' => true],
        ];
        foreach ($expenseTypes as $t) {
            ExpenseType::firstOrCreate(['name' => $t['name']], $t);
        }
    }
}
