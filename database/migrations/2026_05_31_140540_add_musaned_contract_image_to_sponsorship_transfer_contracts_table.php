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
        Schema::table('sponsorship_transfer_contracts', function (Blueprint $table) {
            $table->string('musaned_contract_image')->nullable()->after('musaned_contract_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsorship_transfer_contracts', function (Blueprint $table) {
            $table->dropColumn('musaned_contract_image');
        });
    }
};
