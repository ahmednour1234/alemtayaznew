<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Client;
use App\Models\Complaint;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\Worker;
use Illuminate\Console\Command;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * One-time backfill: encrypt legacy plaintext PII rows and populate hash columns.
 *
 * SAFE TO RE-RUN. Already-encrypted values are detected (decrypt succeeds) and
 * skipped, only their hash is (re)computed if missing. Run AFTER a DB backup.
 *
 *   php artisan security:encrypt-pii --dry-run   # report only, no writes
 *   php artisan security:encrypt-pii             # perform the migration
 */
class EncryptPiiCommand extends Command
{
    protected $signature = 'security:encrypt-pii {--dry-run : Report what would change without writing}';

    protected $description = 'Encrypt legacy plaintext PII and backfill searchable hash columns';

    /** table => [model class, [field => hashColumn]] */
    private function targets(): array
    {
        return [
            'clients'    => [Client::class,    ['national_id' => 'national_id_hash', 'phone' => 'phone_hash']],
            'workers'    => [Worker::class,    ['passport_number' => 'passport_number_hash', 'phone' => 'phone_hash']],
            'agents'     => [Agent::class,     ['phone' => 'phone_hash']],
            'employees'  => [Employee::class,  ['iqama_number' => 'iqama_hash', 'phone' => 'phone_hash']],
            'complaints' => [Complaint::class, ['phone' => 'phone_hash']],
            'leads'      => [Lead::class,      ['phone' => 'phone_hash']],
        ];
    }

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $this->info($dry ? 'DRY RUN — no data will be written.' : 'Encrypting PII…');

        $totalEncrypted = 0;
        $totalHashed = 0;

        foreach ($this->targets() as $table => [$modelClass, $map]) {
            $columns = array_merge(['id'], array_keys($map), array_values($map));
            $rows = DB::table($table)->get($columns);
            $encryptedCount = 0;
            $hashedCount = 0;

            foreach ($rows as $row) {
                $updates = [];

                foreach ($map as $field => $hashColumn) {
                    $raw = $row->{$field} ?? null;
                    if ($raw === null || $raw === '') {
                        continue;
                    }

                    // Determine whether the stored value is already encrypted.
                    $plain = null;
                    try {
                        Crypt::decryptString($raw);
                        $alreadyEncrypted = true;
                    } catch (DecryptException) {
                        $alreadyEncrypted = false;
                        $plain = $raw;
                    }

                    if (! $alreadyEncrypted) {
                        $updates[$field] = Crypt::encryptString($plain);
                        $encryptedCount++;
                    }

                    // (Re)compute hash when missing — needed for both legacy plaintext
                    // and any encrypted-but-unhashed rows.
                    if (empty($row->{$hashColumn})) {
                        $source = $plain ?? Crypt::decryptString($raw);
                        $updates[$hashColumn] = $modelClass::hashPii($source);
                        $hashedCount++;
                    }
                }

                if ($updates && ! $dry) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            }

            $this->line(sprintf(
                '  %-12s encrypted: %d  hashed: %d  (rows scanned: %d)',
                $table, $encryptedCount, $hashedCount, $rows->count()
            ));
            $totalEncrypted += $encryptedCount;
            $totalHashed += $hashedCount;
        }

        $this->newLine();
        $this->info(sprintf(
            '%s %d field(s) encrypted, %d hash(es) written.',
            $dry ? 'Would have' : 'Done —', $totalEncrypted, $totalHashed
        ));

        if ($dry) {
            $this->warn('Re-run without --dry-run (after a database backup) to apply.');
        }

        return self::SUCCESS;
    }
}
