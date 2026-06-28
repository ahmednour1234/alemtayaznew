<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Security & compliance logging tables: security incidents, access logs,
 * failed-login logs, export/download logs, and role/permission-change logs.
 * All reference admins via a nullable, set-null FK so logs survive admin deletion.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('type');                                  // e.g. brute_force, suspicious_access, manual
            $table->string('severity')->default('medium');           // low | medium | high | critical
            $table->text('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('related_model_type')->nullable();
            $table->unsignedBigInteger('related_model_id')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('open');               // open | investigating | resolved | false_positive
            $table->foreignId('resolved_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'severity']);
            $table->index('status');
            $table->index(['related_model_type', 'related_model_id']);
        });

        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('guard')->nullable();
            $table->string('route_name')->nullable();
            $table->text('url')->nullable();
            $table->string('method', 10)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->string('action_type')->nullable();               // sensitive_access | export | download | ...
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['user_id', 'created_at']);
            $table->index(['action_type', 'created_at']);
            $table->index('route_name');
        });

        Schema::create('failed_login_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();                     // attempted identifier
            $table->string('guard')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('failure_reason')->nullable();            // bad_credentials | inactive | locked_out
            $table->timestamp('created_at')->nullable();

            $table->index(['email', 'created_at']);
            $table->index(['ip_address', 'created_at']);
        });

        Schema::create('export_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('export_type');                           // excel | pdf | download | template
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('file_name')->nullable();
            $table->json('filters')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['user_id', 'created_at']);
            $table->index(['export_type', 'created_at']);
        });

        Schema::create('permission_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('changed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('target_user_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('permission_id')->nullable();
            $table->string('action');                                // assigned | removed | created | updated | deleted
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['changed_by', 'created_at']);
            $table->index(['target_user_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_change_logs');
        Schema::dropIfExists('export_logs');
        Schema::dropIfExists('failed_login_logs');
        Schema::dropIfExists('access_logs');
        Schema::dropIfExists('security_incidents');
    }
};
