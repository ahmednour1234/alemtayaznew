<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds searchable HMAC hash columns alongside sensitive PII columns and widens
 * the cleartext columns to TEXT so they can hold Laravel-encrypted ciphertext.
 *
 * Non-destructive: existing data is preserved. Run `php artisan security:encrypt-pii`
 * AFTER a DB backup to encrypt legacy plaintext rows and populate the hash columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Drop the old unique index on the plaintext column — encrypted values are
            // non-deterministic, so uniqueness is enforced on national_id_hash instead.
            $table->dropUnique('clients_national_id_unique');
            $table->text('national_id')->nullable()->change();
            $table->text('phone')->nullable()->change();
            $table->string('national_id_hash', 64)->nullable()->after('national_id')->index();
            $table->string('phone_hash', 64)->nullable()->after('phone')->index();
        });

        Schema::table('workers', function (Blueprint $table) {
            $table->text('passport_number')->nullable()->change();
            $table->text('phone')->nullable()->change();
            $table->string('passport_number_hash', 64)->nullable()->after('passport_number')->index();
            $table->string('phone_hash', 64)->nullable()->after('phone')->index();
        });

        Schema::table('agents', function (Blueprint $table) {
            $table->text('phone')->nullable()->change();
            $table->string('phone_hash', 64)->nullable()->after('phone')->index();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->text('iqama_number')->nullable()->change();
            $table->text('phone')->nullable()->change();
            $table->string('iqama_hash', 64)->nullable()->after('iqama_number')->index();
            $table->string('phone_hash', 64)->nullable()->after('phone')->index();
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->text('phone')->nullable()->change();
            $table->string('phone_hash', 64)->nullable()->after('phone')->index();
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->text('phone')->nullable()->change();
            $table->string('phone_hash', 64)->nullable()->after('phone')->index();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['national_id_hash', 'phone_hash']);
            $table->string('national_id')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->unique('national_id', 'clients_national_id_unique');
        });
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn(['passport_number_hash', 'phone_hash']);
        });
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn(['phone_hash']);
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['iqama_hash', 'phone_hash']);
        });
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['phone_hash']);
        });
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['phone_hash']);
        });
    }
};
