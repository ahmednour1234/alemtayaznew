<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            // ── Personal Data ────────────────────────────────────────────────
            $table->string('name');                          // الاسم
            $table->string('national_id')->nullable()->unique(); // الهوية الوطنية (اختياري)
            $table->string('phone');                             // الجوال
            $table->string('marital_status');                    // الحالة الاجتماعية: single|married|divorced|widowed
            $table->string('classification')->default('potential'); // التصنيف: potential|confirmed|premium|blocked
            $table->string('national_id_image')->nullable(); // صورة الهوية
            // ── Employment / Worker Request Data (بيانات العاملة) ────────────
            $table->foreignId('required_nationality_id')
                  ->nullable()->constrained('nationalities')->nullOnDelete(); // جنسية العاملة المطلوبة
            $table->string('worker_type')->nullable();        // نوع العاملة
            $table->decimal('monthly_salary', 10, 2)->nullable(); // الراتب الشهري
            // ── Meta ─────────────────────────────────────────────────────────
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
