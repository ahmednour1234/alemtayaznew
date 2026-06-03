<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sponsorship_transfer_contracts')) {
            return;
        }

        Schema::table('sponsorship_transfer_contracts', function (Blueprint $table) {
            if (! Schema::hasColumn('sponsorship_transfer_contracts', 'needs_medical_exam')) {
                $table->boolean('needs_medical_exam')->default(false)->after('payment_status');
            }

            if (! Schema::hasColumn('sponsorship_transfer_contracts', 'needs_iqama')) {
                $table->boolean('needs_iqama')->default(false)->after('needs_medical_exam');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('sponsorship_transfer_contracts')) {
            return;
        }

        Schema::table('sponsorship_transfer_contracts', function (Blueprint $table) {
            if (Schema::hasColumn('sponsorship_transfer_contracts', 'needs_iqama')) {
                $table->dropColumn('needs_iqama');
            }

            if (Schema::hasColumn('sponsorship_transfer_contracts', 'needs_medical_exam')) {
                $table->dropColumn('needs_medical_exam');
            }
        });
    }
};
