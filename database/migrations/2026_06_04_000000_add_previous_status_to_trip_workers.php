<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trip_workers', function (Blueprint $table) {
            $table->unsignedTinyInteger('previous_contract_status')->nullable()->after('contract_id');
        });
    }

    public function down(): void
    {
        Schema::table('trip_workers', function (Blueprint $table) {
            $table->dropColumn('previous_contract_status');
        });
    }
};
