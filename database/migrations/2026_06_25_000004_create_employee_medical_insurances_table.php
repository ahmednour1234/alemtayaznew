<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_medical_insurances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('provider');                  // شركة التأمين
            $table->string('policy_number')->nullable(); // رقم الوثيقة
            $table->string('class')->nullable();         // الفئة / الدرجة
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();        // ميعاد انتهاء التأمين
            $table->decimal('cost', 12, 2)->default(0);
            $table->string('status')->default('active'); // active | expired | cancelled
            $table->text('notes')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_medical_insurances');
    }
};
