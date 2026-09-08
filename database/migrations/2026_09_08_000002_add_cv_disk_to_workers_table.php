<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * قرص تخزين ملف السيرة الذاتية.
 *
 * الملفات القديمة على القرص العام (storage/app/public) وهي مكشوفة عبر مسار
 * ‎/file/{path}‎ لأي زائر. الملفات المرفوعة من لوحة إدارة السير الذاتية
 * تُخزَّن على القرص الخاص ولا تُقرأ إلا عبر كنترولر يتحقّق من إتاحة العاملة.
 *
 * نُبقي العمود nullable: القيمة الفارغة تعني القرص العام (السلوك القديم)،
 * فلا تحتاج الملفات الموجودة إلى نقل.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->string('cv_disk', 20)->nullable()->after('cv_path');
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn('cv_disk');
        });
    }
};
