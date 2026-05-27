<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('city')->nullable();               // المدينة
            $table->foreignId('nationality_id')->nullable()->constrained()->nullOnDelete(); // الجنسية المطلوبة
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('referred_by_admin_id')->nullable()->constrained('admins')->nullOnDelete(); // من جاب التأشيرة
            $table->string('source')->nullable();             // مصدر العميل (إعلان / إحالة / ...)
            $table->enum('status', [
                'new',                  // جديد
                'in_progress',          // قيد المتابعة
                'converted',            // تحول لعميل
                'archived',             // مؤرشف
            ])->default('new');
            $table->text('notes')->nullable();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete(); // بعد التحويل
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
