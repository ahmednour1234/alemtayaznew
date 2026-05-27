<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();                    // الاسم (اختياري في البداية)
            $table->string('passport_number')->nullable();         // رقم الجواز
            $table->foreignId('nationality_id')
                  ->nullable()->constrained('nationalities')->nullOnDelete(); // الجنسية
            $table->string('profession')->nullable();              // المهنة
            $table->string('gender')->nullable();                  // female | male
            $table->string('experience')->nullable();              // none | 1-3 | 3-5 | 5+
            $table->string('religion')->nullable();                // الديانة
            $table->unsignedSmallInteger('age')->nullable();       // العمر
            $table->string('phone')->nullable();                   // الهاتف
            $table->string('cv_path')->nullable();                 // ملف CV (PDF)
            // ── الحالة ───────────────────────────────────────────────────────
            // available = متاحة (جديد، لم يُختر)
            // reserved  = محجوزة (قيد الدراسة)
            // assigned  = تم التعيين (محجوز لعميل)
            $table->string('status')->default('available');
            $table->foreignId('client_id')
                  ->nullable()->constrained('clients')->nullOnDelete(); // العميل المعين له
            // ── Meta ─────────────────────────────────────────────────────────
            $table->foreignId('branch_id')
                  ->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('admin_id')
                  ->nullable()->constrained('admins')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
