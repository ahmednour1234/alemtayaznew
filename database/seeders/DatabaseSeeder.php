<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // للبيئة التطويرية (dev/testing) — يشمل بيانات وهمية
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            BranchSeeder::class,
            NationalityAirportSeeder::class,
        ]);
    }
}

