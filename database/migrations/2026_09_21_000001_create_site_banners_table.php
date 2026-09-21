<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * بانرات إعلانية في الموقع العام (عروض المناسبات كاليوم الوطني).
 *
 * البانر صورة تُرفع كما هي ويُفتح عند الضغط عليها واتساب برسالة جاهزة،
 * فلا حاجة لعنوان أو نصّ داخل النظام — التصميم كلّه في الصورة.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();          // للتمييز في لوحة الإدارة فقط
            $table->string('image');                      // مسار الصورة على القرص العام
            $table->string('image_mobile')->nullable();    // نسخة للجوال إن اختلف القياس
            $table->string('alt')->nullable();            // نصّ بديل لقارئات الشاشة
            $table->text('whatsapp_message')->nullable(); // نصّ الاستفسار المُرسل
            $table->string('link')->nullable();           // وجهة بديلة بدل واتساب
            $table->string('placement')->default('home'); // موضع العرض
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);

            // نافذة العرض — تُترك فارغة ليظهر البانر دائماً
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // الاستعلام العام يُصفّي بالموضع والحالة ويُرتّب بالترتيب
            $table->index(['placement', 'active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_banners');
    }
};
