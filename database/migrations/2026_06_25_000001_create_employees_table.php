<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('employee_no')->nullable()->unique();
            $table->string('iqama_number')->nullable();
            $table->date('iqama_expiry_date')->nullable();   // ميعاد تجديد الإقامة
            $table->string('job_title')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('hire_date')->nullable();

            // حالة الموظف: تحت التجربة / مثبّت / منتهي
            $table->string('status')->default('probation');   // probation | active | terminated
            $table->date('probation_end_date')->nullable();   // ميعاد انتهاء فترة التجربة

            // نقل الكفالة: هل نُقل إلينا؟ ومين القديم/الجديد
            $table->boolean('sponsorship_transferred_in')->default(false);
            $table->string('previous_sponsor')->nullable();    // الكفيل السابق
            $table->date('sponsorship_transfer_date')->nullable();
            $table->text('sponsorship_notes')->nullable();

            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('iqama_expiry_date');
            $table->index('probation_end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
