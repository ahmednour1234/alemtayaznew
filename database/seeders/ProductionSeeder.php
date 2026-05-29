<?php

namespace Database\Seeders;

use Database\Seeders\Main\AgentMainSeeder;
use Database\Seeders\Main\BranchMainSeeder;
use Database\Seeders\Main\CityMainSeeder;
use Database\Seeders\Main\ExpenseTypeMainSeeder;
use Database\Seeders\Main\IncomeTypeMainSeeder;
use Database\Seeders\Main\NationalityMainSeeder;
use Illuminate\Database\Seeder;

/**
 * Production Seeder
 * -----------------
 * يُشغَّل مرة واحدة عند الإطلاق الحقيقي (production).
 * آمن للتكرار — جميع البيانات تستخدم firstOrCreate.
 *
 * php artisan db:seed --class=ProductionSeeder
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  🚀  Production Seeder — Alimtiaz    ');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('');

        // ── 1. الصلاحيات والأدوار ────────────────────────────────────────────
        $this->command->info('── [1/7] الصلاحيات والأدوار...');
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);

        // ── 2. المسؤول الأول ──────────────────────────────────────────────────
        $this->command->info('── [2/7] المسؤول الأول...');
        $this->call(AdminSeeder::class);

        // ── 3. الفروع ─────────────────────────────────────────────────────────
        $this->command->info('── [3/7] الفروع...');
        $this->call(BranchMainSeeder::class);

        // ── 4. الجنسيات والمطارات ─────────────────────────────────────────────
        $this->command->info('── [4/7] الجنسيات والمطارات...');
        $this->call(NationalityMainSeeder::class);
        $this->call(NationalityAirportSeeder::class);   // يضيف المطارات السعودية

        // ── 5. الوكلاء ────────────────────────────────────────────────────────
        $this->command->info('── [5/7] الوكلاء...');
        $this->call(AgentMainSeeder::class);

        // ── 6. أنواع الإيرادات ────────────────────────────────────────────────
        $this->command->info('── [6/7] أنواع الإيرادات...');
        $this->call(IncomeTypeMainSeeder::class);

        // ── 7. أنواع المصروفات ────────────────────────────────────────────────
        $this->command->info('── [7/8] أنواع المصروفات...');
        $this->call(ExpenseTypeMainSeeder::class);

        // ── 8. المدن ─────────────────────────────────────────────────────────
        $this->command->info('── [8/8] مدن المملكة العربية السعودية...');
        $this->call(CityMainSeeder::class);

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  ✅  Production data seeded!          ');
        $this->command->info('  ⚠️  غيّر كلمة مرور المسؤول فوراً    ');
        $this->command->info('     admin@admin.com / password        ');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('');
    }
}
