<?php

namespace App\Imports;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Income;
use App\Models\IncomeType;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

/**
 * Flexible import that reads a single sheet with Arabic headers.
 * Detects column positions dynamically from the header row so the import
 * works regardless of column order changes in the exported Excel file.
 *
 * Recognised header names:
 *   التاريخ / تاريخ           → date
 *   الفرع / فرع               → branch
 *   النوع (first occurrence)   → type keyword (إيراد / مصروف)
 *   المبلغ / النوع (numeric)   → amount  (auto-detected from content)
 *   المرجع                     → description (long text) or reference_number (numeric)
 *   العملة                     → reference_number (numeric, when المرجع has text)
 *   المستلم / ملاحظات         → recipient
 *   طريقة الدفع               → payment_method
 *   الحالة                     → ignored
 */
class FlexibleStatementImport implements ToCollection, WithCalculatedFormulas
{
    public int $incomeCount  = 0;
    public int $expenseCount = 0;
    public int $skippedCount = 0;

    /** @var array<string> */
    public array $errors = [];

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $adminId = Auth::guard('admin')->id() ?? Admin::first()?->id;

        // ── 1. Build header → [indices] map from the first row ──────────────
        $colMap = []; // normalised_header => [col_index, ...]
        foreach ($rows->first()->toArray() as $idx => $cell) {
            $h = trim((string) $cell);
            if ($h !== '') {
                $colMap[$h][] = $idx;
            }
        }

        // Helper: return first non-empty value from row for any of the given headers
        $get = function (array $data, array $headers) use ($colMap): mixed {
            foreach ($headers as $h) {
                foreach ($colMap[$h] ?? [] as $idx) {
                    $v = $data[$idx] ?? null;
                    if ($v !== null && trim((string) $v) !== '') {
                        return $v;
                    }
                }
            }
            return null;
        };

        // ── 2. Auto-detect the actual amount column index ────────────────────
        // The Excel export sometimes mislabels columns; المبلغ may contain "Sar"
        // while a النوع duplicate column actually holds the numeric amount.
        $amountColIdx = null;
        foreach ($rows->skip(1)->take(10) as $sampleRow) {
            $d = $sampleRow->toArray();
            // Try every candidate header in priority order
            foreach (['المبلغ', 'النوع', 'المبالغ'] as $candidate) {
                foreach (array_reverse($colMap[$candidate] ?? []) as $idx) {
                    $v = str_replace([',', ' ', "\xc2\xa0"], '', (string) ($d[$idx] ?? ''));
                    if (is_numeric($v) && (float) $v > 0) {
                        $amountColIdx = $idx;
                        break 3;
                    }
                }
            }
        }

        // ── 3. Determine reference vs description split ──────────────────────
        // المرجع may hold a long text description while العملة holds the numeric ref,
        // or vice-versa. Detect by scanning a sample row.
        $referenceColIdx   = null;
        $descriptionColIdx = null;
        foreach ($rows->skip(1)->take(10) as $sampleRow) {
            $d = $sampleRow->toArray();
            foreach (['المرجع', 'العملة', 'البيان'] as $hdr) {
                foreach ($colMap[$hdr] ?? [] as $idx) {
                    $v = trim((string) ($d[$idx] ?? ''));
                    if ($v === '') {
                        continue;
                    }
                    $vClean = str_replace([',', ' '], '', $v);
                    if (is_numeric($vClean)) {
                        $referenceColIdx = $referenceColIdx ?? $idx;
                    } else {
                        $descriptionColIdx = $descriptionColIdx ?? $idx;
                    }
                }
            }
            if ($referenceColIdx !== null || $descriptionColIdx !== null) {
                break;
            }
        }

        // Cache branch lookups to avoid N+1 queries
        $branchCache      = [];
        $incomeTypeCache  = [];
        $expenseTypeCache = [];

        // First النوع column index (type keyword: إيراد / مصروف)
        $typeColIdx = ($colMap['النوع'] ?? [null])[0];

        // Recipient column
        $recipientHeaders    = ['المستلم / ملاحظات', 'المستلم', 'ملاحظات', 'المستلم/ملاحظات'];
        $paymentMethodHeaders = ['طريقة الدفع', 'طريقة الدفع'];
        $dateHeaders          = ['التاريخ', 'تاريخ', 'ت'];
        $branchHeaders        = ['الفرع', 'فرع', 'اسم الفرع'];

        foreach ($rows->skip(1) as $index => $row) {
            $rowNum = $index + 2;
            $data   = $row->toArray();

            $rawDate     = $get($data, $dateHeaders);
            $branchValue = trim((string) $get($data, $branchHeaders));
            $typeName    = $typeColIdx !== null ? trim((string) ($data[$typeColIdx] ?? '')) : '';
            $rawAmount   = $amountColIdx !== null ? ($data[$amountColIdx] ?? null) : null;

            $referenceNumber = $referenceColIdx !== null
                ? (trim((string) ($data[$referenceColIdx] ?? '')) ?: null)
                : null;

            $description = $descriptionColIdx !== null
                ? (trim((string) ($data[$descriptionColIdx] ?? '')) ?: null)
                : null;

            $recipient        = trim((string) $get($data, $recipientHeaders)) ?: null;
            $paymentMethodRaw = trim((string) $get($data, $paymentMethodHeaders)) ?: '';

            // Skip completely empty rows
            if ($branchValue === '' && $typeName === '' && $rawAmount === null) {
                continue;
            }

            // Normalise amount
            $amountStr = trim(str_replace([',', ' ', "\xc2\xa0"], '', (string) $rawAmount));
            $amount    = is_numeric($amountStr) ? (float) $amountStr : null;

            // Skip rows with no usable amount
            if ($amount === null || $amount <= 0) {
                $this->skippedCount++;
                $this->errors[] = "صف {$rowNum}: المبلغ غير صالح ({$rawAmount})";
                continue;
            }

            // Resolve branch — exact → fuzzy (strip ال prefix) → auto-create
            if (! isset($branchCache[$branchValue])) {
                // 1. Exact match
                $branch = Branch::where('name', $branchValue)->first();

                if (! $branch) {
                    // 2. Normalise both sides: strip leading "ال" and compare
                    $normalised = preg_replace('/^ال/', '', $branchValue);
                    $branch = Branch::get()->first(function ($b) use ($branchValue, $normalised) {
                        $dbNorm = preg_replace('/^ال/', '', $b->name);
                        return $dbNorm === $normalised
                            || mb_stripos($b->name, $normalised) !== false
                            || mb_stripos($branchValue, $dbNorm)  !== false;
                    });
                }

                if (! $branch) {
                    // 3. Auto-create the branch so no row is lost
                    // Generate a unique code from the name (e.g. "الوكالات الرحالية" → "branch-XXXX")
                    $slug = mb_substr(preg_replace('/\s+/', '-', $branchValue), 0, 20);
                    $code = $slug . '-' . substr(md5($branchValue), 0, 4);
                    $branch = Branch::create([
                        'name'   => $branchValue,
                        'code'   => $code,
                        'active' => true,
                    ]);
                }

                $branchCache[$branchValue] = $branch;
            }
            $branch = $branchCache[$branchValue];

            // Parse date
            try {
                $date = $rawDate instanceof \DateTimeInterface
                    ? Carbon::instance($rawDate)->toDateString()
                    : Carbon::parse($rawDate)->toDateString();
            } catch (\Exception) {
                $date = now()->toDateString();
            }

            // Normalise payment method
            $methodMap = [
                'cash'          => 'cash',
                'نقدي'          => 'cash',
                'نقد'           => 'cash',
                'bank_transfer' => 'bank_transfer',
                'تحويل'         => 'bank_transfer',
                'تحويل بنكي'    => 'bank_transfer',
                'card'          => 'card',
                'بطاقة'         => 'card',
                'other'         => 'other',
                'أخرى'          => 'other',
            ];
            $paymentMethod = $methodMap[mb_strtolower($paymentMethodRaw)] ?? 'cash';

            // Determine record type by keyword in type name
            $isIncome  = mb_strpos($typeName, 'إيراد') !== false;
            $isExpense = ! $isIncome; // anything that isn't income is treated as an expense

            if ($isIncome) {
                // Resolve income type
                if (! isset($incomeTypeCache[$typeName])) {
                    $incomeTypeCache[$typeName] = IncomeType::where('name', $typeName)->first()
                        ?? IncomeType::where('name', 'like', '%' . mb_substr($typeName, 0, 6) . '%')->first();
                }
                $type = $incomeTypeCache[$typeName];

                if (! $type) {
                    // Auto-create the type so no row is lost
                    $type = IncomeType::firstOrCreate(['name' => $typeName], ['active' => true]);
                    $incomeTypeCache[$typeName] = $type;
                }

                Income::create([
                    'branch_id'        => $branch->id,
                    'income_type_id'   => $type->id,
                    'admin_id'         => $adminId,
                    'amount'           => (float) $amount,
                    'date'             => $date,
                    'payment_method'   => $paymentMethod,
                    'reference_number' => $referenceNumber ?: null,
                    'description'      => $description ?: null,
                    'recipient'        => $recipient,
                ]);

                $this->incomeCount++;

            } elseif ($isExpense) {
                // Resolve expense type
                if (! isset($expenseTypeCache[$typeName])) {
                    $expenseTypeCache[$typeName] = ExpenseType::where('name', $typeName)->first()
                        ?? ExpenseType::where('name', 'like', '%' . mb_substr($typeName, 0, 6) . '%')->first();
                }
                $type = $expenseTypeCache[$typeName];

                if (! $type) {
                    // Auto-create the type so no row is lost
                    $type = ExpenseType::firstOrCreate(['name' => $typeName], ['active' => true]);
                    $expenseTypeCache[$typeName] = $type;
                }

                Expense::create([
                    'branch_id'        => $branch->id,
                    'expense_type_id'  => $type->id,
                    'admin_id'         => $adminId,
                    'amount'           => (float) $amount,
                    'date'             => $date,
                    'payment_method'   => $paymentMethod,
                    'status'           => 'approved',
                    'reference_number' => $referenceNumber ?: null,
                    'description'      => $description ?: null,
                    'recipient'        => $recipient,
                ]);

                $this->expenseCount++;

            }
        }
    }
}
