<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_number')->unique();

            // Optional link to a contract — complaint can be standalone
            $table->foreignId('contract_id')->nullable()->constrained('recruitment_contracts')->nullOnDelete();
            $table->string('contract_type')->nullable(); // e.g. recruitment

            // Auto-derived from contract when available, otherwise filled manually
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('worker_id')->nullable()->constrained('workers')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();

            // Complaint details
            $table->string('problem_type'); // salary, food, escape, work_refusal, abuse, health, other
            $table->text('description');
            $table->string('phone', 30)->nullable();

            // Assignment
            $table->foreignId('assigned_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('new');      // new, in_progress, resolved, closed, escalated

            // Musaned reference
            $table->boolean('on_musaned')->default(false);
            $table->string('musaned_number')->nullable();

            // Resolution
            $table->text('resolution')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('resolved_at')->nullable();

            // Audit
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('last_stale_notified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'branch_id']);
            $table->index(['priority', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
