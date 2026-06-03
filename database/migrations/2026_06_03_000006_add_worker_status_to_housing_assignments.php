<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worker_housing_assignments', function (Blueprint $table) {
            // حالة العمالة: نظامية | هاربة | مريضة
            $table->string('worker_status')->default('normal')->after('reason');
        });
    }

    public function down(): void
    {
        Schema::table('worker_housing_assignments', function (Blueprint $table) {
            $table->dropColumn('worker_status');
        });
    }
};
