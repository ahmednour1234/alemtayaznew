<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ربط الموظف بالجنسيات التي يديرها في لوحة إدارة السير الذاتية.
 *
 * المنسّق قد يُسنَد إليه أكثر من جنسية، فيرى ويرفع سيرها الذاتية وحدها،
 * ويصله إشعار حين تُحجز عاملة من إحداها.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_nationality', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->foreignId('nationality_id')->constrained('nationalities')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['admin_id', 'nationality_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_nationality');
    }
};
