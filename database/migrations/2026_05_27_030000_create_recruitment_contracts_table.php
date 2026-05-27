<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitment_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();      // رقم العقد (auto-generated)
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();   // creator
            $table->date('request_date')->default(now());

            // ── Visa section ─────────────────────────────────────────────────
            $table->string('visa_image')->nullable();
            $table->enum('visa_type', ['domestic', 'rehabilitation'])->nullable();   // تأشيرة عمالة منزلية / تأهيل شامل
            $table->string('visa_number')->nullable();
            $table->foreignId('arrival_airport_id')->nullable()->constrained('airports')->nullOnDelete();
            $table->foreignId('departure_airport_id')->nullable()->constrained('airports')->nullOnDelete();
            $table->foreignId('delivery_airport_id')->nullable()->constrained('airports')->nullOnDelete();

            // ── Musaned section ───────────────────────────────────────────────
            $table->string('musaned_number')->nullable();     // رقم عقد مساند
            $table->date('musaned_date')->nullable();
            $table->string('musaned_file')->nullable();

            // ── Coordination section ──────────────────────────────────────────
            $table->foreignId('worker_id')->nullable()->constrained('workers')->nullOnDelete();
            $table->string('e_doc_number')->nullable();       // رقم التوثيق الالكتروني بمساند
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->enum('current_department', ['customer_service', 'accounts', 'coordination'])->default('customer_service');
            $table->unsignedTinyInteger('current_status')->default(1);  // 1-15

            // ── Accounts section ──────────────────────────────────────────────
            $table->enum('payment_status', ['pending', 'partial', 'full'])->default('pending');
            $table->decimal('total_cost', 10, 2)->nullable();

            // ── Dates ─────────────────────────────────────────────────────────
            $table->date('arrival_date')->nullable();
            $table->date('trial_end_date')->nullable();
            $table->date('contract_end_date')->nullable();

            // ── Misc ──────────────────────────────────────────────────────────
            $table->text('notes')->nullable();
            $table->boolean('client_sms')->default(false);
            $table->boolean('client_rating')->default(false);
            $table->string('rating_image')->nullable();

            $table->boolean('active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_contracts');
    }
};
