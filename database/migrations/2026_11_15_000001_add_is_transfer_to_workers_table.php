<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * تمييز سير «نقل الكفالة» (عاملة داخل المملكة) عن سير الاستقدام.
 * الافتراضي false فتبقى كل السير الحالية استقداماً كما هي.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->boolean('is_transfer')->default(false)->after('cv_withdrawn_at')->index();
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropIndex(['is_transfer']);
            $table->dropColumn('is_transfer');
        });
    }
};
