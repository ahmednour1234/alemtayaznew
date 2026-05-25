<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('expense_type_id')->constrained('expense_types')->restrictOnDelete();
            $table->foreignId('admin_id')->constrained('admins')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('payment_method')->default('cash'); // cash, bank_transfer, card, other
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
