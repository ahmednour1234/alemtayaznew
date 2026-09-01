<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * تسجيل سداد العميل عبر «تمارا» أثناء الحجز.
 *
 * السداد يمدّد مهلة الحجز من 72 ساعة إلى 5 أيام (تُحسب من تاريخ الحجز
 * الأصلي لا من لحظة السداد)، فيبقى للعميل وقت أطول لاستكمال العقد.
 *
 * نخزّن لحظة التسجيل ومَن سجّلها ليبقى الإجراء قابلاً للمراجعة.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->timestamp('tamara_paid_at')->nullable()->after('assigned_at');
            $table->foreignId('tamara_paid_by_admin_id')->nullable()->after('tamara_paid_at')
                  ->constrained('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tamara_paid_by_admin_id');
            $table->dropColumn('tamara_paid_at');
        });
    }
};
