<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            // رقم التأشيرة — اختياري، يُدخل عند حجز العاملة لعميل
            $table->string('visa_number')->nullable()->after('passport_number_hash');
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn('visa_number');
        });
    }
};
