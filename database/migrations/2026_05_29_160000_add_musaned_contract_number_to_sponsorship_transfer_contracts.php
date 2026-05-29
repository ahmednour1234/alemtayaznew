<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsorship_transfer_contracts', function (Blueprint $table) {
            $table->string('musaned_contract_number')->nullable()->after('contract_number');
        });
    }

    public function down(): void
    {
        Schema::table('sponsorship_transfer_contracts', function (Blueprint $table) {
            $table->dropColumn('musaned_contract_number');
        });
    }
};
