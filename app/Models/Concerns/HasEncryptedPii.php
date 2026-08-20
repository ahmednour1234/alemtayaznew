<?php

namespace App\Models\Concerns;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Builder;

/**
 * Adds encrypted-at-rest behaviour to sensitive PII columns while keeping them
 * searchable via a deterministic keyed-hash (HMAC-SHA256) sibling column.
 *
 * The cleartext column is stored with Laravel's `encrypted` cast (non-deterministic
 * ciphertext → not searchable). For every encrypted field we keep a `*_hash`
 * column holding hash_hmac('sha256', normalized_value, app_key) so exact-match
 * lookups and unique validation still work without ever exposing the plaintext.
 *
 * A model using this trait must declare its map via {@see piiHashMap()}:
 *     public function piiHashMap(): array
 *     {
 *         return ['national_id' => 'national_id_hash', 'phone' => 'phone_hash'];
 *     }
 * and add `'national_id' => 'encrypted'` casts for each source field.
 */
trait HasEncryptedPii
{
    public static function bootHasEncryptedPii(): void
    {
        static::saving(function ($model): void {
            foreach ($model->piiHashMap() as $field => $hashColumn) {
                // isDirty() يفكّ تشفير القيمة الأصلية للمقارنة، فيرمي
                // DecryptException لو كان المخزَّن نصاً قديماً غير مشفّر أو
                // مشفّراً بمفتاح APP_KEY سابق. نعتبر الحقل حينها «متغيّراً»
                // فتُعاد كتابة قيمته وهاشه بالمفتاح الحالي بدل تعطيل الحفظ.
                try {
                    $changed = $model->isDirty($field);
                } catch (DecryptException) {
                    $changed = true;
                }

                if ($changed || empty($model->getAttribute($hashColumn))) {
                    $model->setAttribute(
                        $hashColumn,
                        static::hashPii($model->getAttribute($field))
                    );
                }
            }
        });
    }

    /** @return array<string,string> source PII field => hash column */
    abstract public function piiHashMap(): array;

    /**
     * Tolerant read for encrypted PII columns: if a value cannot be decrypted
     * (e.g. a legacy row stored as plaintext before encryption was introduced),
     * fall back to the raw stored value instead of throwing a DecryptException
     * that would otherwise break the whole page.
     */
    public function getAttributeValue($key)
    {
        if (array_key_exists($key, $this->piiHashMap())) {
            try {
                return parent::getAttributeValue($key);
            } catch (DecryptException $e) {
                return $this->getAttributeFromArray($key);
            }
        }

        return parent::getAttributeValue($key);
    }

    /**
     * Deterministic keyed hash used for exact-match lookups on encrypted columns.
     * Returns null for empty values so nullable columns stay null (and never collide).
     */
    public static function hashPii(?string $value): ?string
    {
        $value = $value === null ? '' : trim((string) $value);
        if ($value === '') {
            return null;
        }

        $key = (string) config('app.key');
        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        return hash_hmac('sha256', $value, $key);
    }

    /**
     * Scope: exact-match on an encrypted PII field via its hash column.
     * Usage: Client::wherePii('national_id', $id)->first();
     */
    public function scopeWherePii(Builder $query, string $field, ?string $value): Builder
    {
        $hashColumn = $this->piiHashMap()[$field] ?? null;

        if ($hashColumn === null) {
            return $query->where($field, $value); // not an encrypted field — fall back
        }

        return $query->where($hashColumn, static::hashPii($value));
    }

    /** OR variant of {@see scopeWherePii()} for composite search builders. */
    public function scopeOrWherePii(Builder $query, string $field, ?string $value): Builder
    {
        $hashColumn = $this->piiHashMap()[$field] ?? null;

        if ($hashColumn === null) {
            return $query->orWhere($field, $value);
        }

        return $query->orWhere($hashColumn, static::hashPii($value));
    }
}
