<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('to_branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('admin_id')->constrained('admins')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('description')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transfers');
    }
};
