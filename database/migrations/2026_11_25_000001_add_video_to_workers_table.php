<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * فيديو العاملة: رابط (Google Drive / YouTube ...) أو ملف مرفوع.
 * يراه العميل فوراً من الموقع بدل انتظار مكتب مانيلا.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->string('video_url', 1000)->nullable()->after('cv_disk');
            $table->string('video_path')->nullable()->after('video_url');
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn(['video_url', 'video_path']);
        });
    }
};
