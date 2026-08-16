<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_activity_logs', function (Blueprint $table) {
            $table->id();

            // العاملة قد تُحذف نهائياً — نحتفظ بالسجل مع تفريغ المعرّف
            $table->foreignId('worker_id')->nullable()
                  ->constrained('workers')->nullOnDelete();

            // اسم العاملة وقت الإجراء — يبقى مقروءاً حتى بعد الحذف النهائي
            $table->string('worker_name')->nullable();

            $table->foreignId('admin_id')->nullable()
                  ->constrained('admins')->nullOnDelete();

            // اسم الموظّف وقت الإجراء — يبقى حتى لو حُذف الحساب
            $table->string('admin_name')->nullable();

            // created | updated | deleted | restored | assigned | unassigned | cv_uploaded
            $table->string('action');

            // وصف مقروء بالعربية
            $table->string('label')->nullable();

            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index(['worker_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['admin_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_activity_logs');
    }
};
