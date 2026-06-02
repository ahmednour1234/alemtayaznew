<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->foreignId('origin_nationality_id')
                  ->nullable()
                  ->after('airport_id')
                  ->constrained('nationalities')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['origin_nationality_id']);
            $table->dropColumn('origin_nationality_id');
        });
    }
};
