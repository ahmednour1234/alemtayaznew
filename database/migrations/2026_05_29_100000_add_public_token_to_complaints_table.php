<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('complaints', 'public_token')) {
            Schema::table('complaints', function (Blueprint $table) {
                $table->string('public_token', 64)->nullable()->unique()->after('complaint_number');
            });
        }

        // Back-fill existing rows
        DB::table('complaints')->whereNull('public_token')->orderBy('id')->each(function ($row) {
            DB::table('complaints')->where('id', $row->id)->update([
                'public_token' => Str::random(48),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn('public_token');
        });
    }
};
