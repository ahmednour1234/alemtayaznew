<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->dropForeign(['delivery_airport_id']);
            $table->dropColumn('delivery_airport_id');
            $table->foreignId('delivery_city_id')->nullable()->after('arrival_airport_id')->constrained('cities')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->dropForeign(['delivery_city_id']);
            $table->dropColumn('delivery_city_id');
            $table->foreignId('delivery_airport_id')->nullable()->constrained('airports')->nullOnDelete();
        });
    }
};
