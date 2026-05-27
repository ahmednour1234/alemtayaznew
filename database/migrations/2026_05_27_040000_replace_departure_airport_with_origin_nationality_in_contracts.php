<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->dropForeign(['departure_airport_id']);
            $table->dropColumn('departure_airport_id');
            $table->foreignId('origin_nationality_id')->nullable()->after('arrival_airport_id')->constrained('nationalities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->dropForeign(['origin_nationality_id']);
            $table->dropColumn('origin_nationality_id');
            $table->foreignId('departure_airport_id')->nullable()->after('arrival_airport_id')->constrained('airports')->nullOnDelete();
        });
    }
};
