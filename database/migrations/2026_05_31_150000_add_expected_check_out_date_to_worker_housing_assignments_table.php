<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worker_housing_assignments', function (Blueprint $table) {
            $table->date('expected_check_out_date')->nullable()->after('check_out_date');
        });
    }

    public function down(): void
    {
        Schema::table('worker_housing_assignments', function (Blueprint $table) {
            $table->dropColumn('expected_check_out_date');
        });
    }
};
