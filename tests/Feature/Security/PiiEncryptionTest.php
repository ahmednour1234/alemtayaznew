<?php

namespace Tests\Feature\Security;

use App\Models\Client;
use App\Models\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PiiEncryptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_national_id_is_encrypted_at_rest_and_decrypts_via_model(): void
    {
        $client = Client::factory()->create([
            'national_id' => '1122334455',
            'phone'       => '0500000000',
        ]);

        $raw = DB::table('clients')->where('id', $client->id)->first();

        // Stored ciphertext must not equal the plaintext.
        $this->assertNotSame('1122334455', $raw->national_id);
        $this->assertNotSame('0500000000', $raw->phone);

        // The model accessor transparently decrypts.
        $this->assertSame('1122334455', $client->fresh()->national_id);
        $this->assertSame('0500000000', $client->fresh()->phone);
    }

    public function test_hash_columns_are_populated_with_the_keyed_hash(): void
    {
        $client = Client::factory()->create(['national_id' => '1122334455']);

        $raw = DB::table('clients')->where('id', $client->id)->first();

        $this->assertNotNull($raw->national_id_hash);
        $this->assertSame(Client::hashPii('1122334455'), $raw->national_id_hash);
    }

    public function test_where_pii_scope_finds_record_by_exact_plaintext(): void
    {
        Client::factory()->create(['national_id' => '9988776655']);

        $found = Client::wherePii('national_id', '9988776655')->first();

        $this->assertNotNull($found);
        $this->assertSame('9988776655', $found->national_id);
    }

    public function test_worker_passport_is_searchable_by_hash(): void
    {
        $worker = Worker::factory()->create(['passport_number' => 'A1234567']);

        $raw = DB::table('workers')->where('id', $worker->id)->first();
        $this->assertNotSame('A1234567', $raw->passport_number);
        $this->assertSame(Worker::hashPii('A1234567'), $raw->passport_number_hash);

        $this->assertTrue(Worker::wherePii('passport_number', 'A1234567')->exists());
    }

    public function test_empty_pii_produces_null_hash(): void
    {
        $this->assertNull(Client::hashPii(null));
        $this->assertNull(Client::hashPii(''));
        $this->assertNull(Client::hashPii('   '));
    }
}
