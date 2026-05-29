<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained('workers')->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained('recruitment_contracts')->nullOnDelete();
            $table->string('notes')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'no_show'])->default('scheduled');
            $table->timestamps();
            $table->unique(['trip_id', 'worker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_workers');
    }
};
