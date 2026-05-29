<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cities')) {
            return;
        }

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('region')->nullable();   // المنطقة الإدارية
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique('name');
            $table->index('region');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
