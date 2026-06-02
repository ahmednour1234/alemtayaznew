<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            // Track who performed the CV upload (for duplicate detection)
            $table->string('original_cv_name')->nullable()->after('cv_path');

            // Track who assigned the worker to a client and when
            $table->foreignId('assigned_by_admin_id')
                  ->nullable()
                  ->after('admin_id')
                  ->constrained('admins')
                  ->nullOnDelete();

            $table->timestamp('assigned_at')->nullable()->after('assigned_by_admin_id');
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropForeign(['assigned_by_admin_id']);
            $table->dropColumn(['original_cv_name', 'assigned_by_admin_id', 'assigned_at']);
        });
    }
};
