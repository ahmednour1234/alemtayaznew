<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'no_answer',              // لم يرد
                'not_suitable',           // غير مناسب
                'nationality_unavailable',// الجنسية غير متوفرة
                'wants_rent',             // يريد إيجار
                'profiles_rejected',      // الجنسية متوفرة لكن السيفات غير مناسبة
                'need_followup',          // يحتاج متابعة
                'converted',              // تم التحويل لعميل
                'wrong_number',           // رقم خاطئ
            ]);
            $table->text('notes')->nullable();
            $table->timestamp('follow_up_at')->nullable(); // موعد المتابعة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_call_logs');
    }
};
