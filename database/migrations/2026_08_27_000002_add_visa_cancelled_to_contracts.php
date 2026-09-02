<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إلغاء التأشيرة وفكّ ربط العاملة بالعقد.
 *
 * حين تُلغى التأشيرة قبل التسليم، يبقى العقد قائماً بلا عاملة حتى يُربط
 * بعاملة بديلة. نحفظ لحظة الإلغاء ومَن نفّذه ليبقى الإجراء قابلاً للمراجعة،
 * ونحتفظ بمعرّف العاملة السابقة لأن ربط بديلة لا يعني نسيان الأولى.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->timestamp('visa_cancelled_at')->nullable()->after('current_status');
            $table->foreignId('visa_cancelled_by_admin_id')->nullable()->after('visa_cancelled_at')
                  ->constrained('admins')->nullOnDelete();
            $table->unsignedBigInteger('previous_worker_id')->nullable()->after('visa_cancelled_by_admin_id');
            $table->string('visa_cancel_reason', 500)->nullable()->after('previous_worker_id');
        });
    }

    public function down(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('visa_cancelled_by_admin_id');
            $table->dropColumn(['visa_cancelled_at', 'previous_worker_id', 'visa_cancel_reason']);
        });
    }
};
