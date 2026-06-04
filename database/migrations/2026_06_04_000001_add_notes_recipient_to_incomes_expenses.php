<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->string('recipient')->nullable()->after('description');
            $table->text('notes')->nullable()->after('recipient');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->string('recipient')->nullable()->after('description');
            $table->text('notes')->nullable()->after('recipient');
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn(['recipient', 'notes']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['recipient', 'notes']);
        });
    }
};
