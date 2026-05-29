<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsorship_transfer_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->foreignId('worker_id')->constrained('workers')->cascadeOnDelete();
            $table->foreignId('from_client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('to_client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('original_contract_id')->nullable()->constrained('recruitment_contracts')->nullOnDelete();
            $table->date('transfer_date')->nullable();
            $table->decimal('total_fees', 10, 2)->default(0);
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->decimal('loss_amount', 10, 2)->default(0)->comment('فقد');
            $table->enum('payment_status', ['pending', 'partial', 'full'])->default('pending');
            $table->enum('current_department', ['customer_service', 'accounts'])->default('customer_service');
            $table->unsignedTinyInteger('current_status')->default(1);
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['branch_id', 'current_status', 'current_department']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsorship_transfer_contracts');
    }
};
