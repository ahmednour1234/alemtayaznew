<?php

namespace Database\Seeders\Main;

use App\Models\Branch;
use App\Models\Housing;
use Illuminate\Database\Seeder;

/**
 * HousingMainSeeder
 * ─────────────────
 * ينشئ سكناً واحداً لكل فرع إنتاجي.
 * آمن للتكرار — يستخدم firstOrCreate.
 *
 * php artisan db:seed --class="Database\Seeders\Main\HousingMainSeeder"
 */
class HousingMainSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::pluck('id', 'code');

        $housings = [
            'RYD-001' => [
                'name'     => 'سكن فرع الرياض',
                'address'  => 'الرياض',
                'capacity' => 30,
            ],
            'HFR-001' => [
                'name'     => 'سكن فرع حفر الباطن',
                'address'  => 'حفر الباطن',
                'capacity' => 20,
            ],
            'ARR-001' => [
                'name'     => 'سكن فرع عرعر',
                'address'  => 'عرعر',
                'capacity' => 20,
            ],
        ];

        $count = 0;
        foreach ($housings as $code => $data) {
            $branchId = $branches[$code] ?? null;
            if (! $branchId) {
                $this->command->warn("  ⚠ Branch [{$code}] not found — skipped.");
                continue;
            }

            Housing::firstOrCreate(
                ['branch_id' => $branchId, 'name' => $data['name']],
                [
                    'address'   => $data['address'],
                    'capacity'  => $data['capacity'],
                    'admin_id'  => null,
                    'active'    => true,
                ]
            );
            $count++;
        }

        $this->command->info("✓ Housing seeded ({$count} records)");
    }
}
