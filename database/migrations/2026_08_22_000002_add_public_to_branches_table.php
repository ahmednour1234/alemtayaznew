<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * يحدّد الفروع التي تظهر للعملاء في الموقع العام.
 * بعض الفروع إدارية أو داخلية (الوكالات مثلاً) ولا يصحّ عرضها كخيار للعميل.
 *
 * الافتراضي false: لا يظهر أي فرع حتى يُفعَّل صراحةً من لوحة التحكم،
 * فلا نكشف فرعاً داخلياً بالخطأ عند تشغيل الهجرة.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->boolean('public')->default(false)->after('active');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('public');
        });
    }
};
