<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            // وثائق الشركة قد تكون عامة (employee_id = null) أو خاصة بموظف
            $table->foreignId('employee_id')->nullable()->constrained('employees')->cascadeOnDelete();
            $table->string('title');                         // اسم الوثيقة
            $table->string('doc_type')->nullable();          // نوع: عقد / رخصة / شهادة ...
            $table->string('file_path');                     // مسار التخزين الخاص (local disk)
            $table->string('original_name')->nullable();     // الاسم الأصلي للملف المرفوع
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
