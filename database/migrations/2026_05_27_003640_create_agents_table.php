<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // اسم الوكيل
            $table->string('phone');                         // رقم الجوال
            $table->string('email')->nullable();             // الإيميل
            $table->foreignId('nationality_id')
                  ->nullable()->constrained('nationalities')->nullOnDelete(); // الجنسية
            $table->string('document')->nullable();          // مستند PDF
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
