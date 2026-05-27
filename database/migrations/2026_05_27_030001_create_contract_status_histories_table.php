<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('recruitment_contracts')->cascadeOnDelete();
            $table->unsignedTinyInteger('status');    // 1-15
            $table->date('status_date')->nullable();  // التاريخ الفعلي لهذه المرحلة
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->text('whatsapp_message')->nullable();  // رسالة واتساب مرسلة مع هذا التحديث
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_status_histories');
    }
};
