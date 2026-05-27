<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('recruitment_contracts')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('action');           // created | updated | status_changed
            $table->string('section')->nullable(); // customer_service | accounts | coordination | null
            $table->string('label')->nullable();   // human-readable description
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_activity_logs');
    }
};
