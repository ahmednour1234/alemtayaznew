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
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class FinancialTransactionImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    public int $incomeCount = 0;
    public int $expenseCount = 0;
    public int $skippedCount = 0;

    /** @var array<int, string> */
    public array $errors = [];

    /**
     * In-memory caches. Without these every row re-queried the database —
     * the branch fuzzy match alone loaded the whole branches table per row,
     * which is what pushed large files past the nginx timeout (504).
     * مطابقة الفروع صارت داخل BranchResolver وهو يحتفظ بذاكرته الخاصة.
     */
    private readonly \App\Services\BranchResolver $branchResolver;

    /** @var array<string, int> */
    private array $incomeTypeCache = [];

    /** @var array<string, int> */
    private array $expenseTypeCache = [];

    public function __construct()
    {
        $this->branchResolver = new \App\Services\BranchResolver();
    }

    /** Rows buffered for bulk insert. */
    private const BATCH_SIZE = 500;

    /** @var array<int, array<string, mixed>> */
    private array $incomeBuffer = [];

    /** @var array<int, array<string, mixed>> */
    private array $expenseBuffer = [];

    public function collection(Collection $rows): void
    {
        // Large sheets legitimately take a while; don't let PHP kill the run
        // half-way and leave the import partially applied.
        @set_time_limit(0);

        $adminId = Auth::guard('admin')->id() ?? Admin::query()->value('id');
        // insert() bypasses Eloquent, so timestamps must be set explicitly.
        $now = now();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $data = $row->toArray();

            if ($this->isEmptyRow($data)) {
                continue;
            }

            $recordType = $this->recordType($data);
            // If no record_type column → assume expense (expense-only template)
            if (! $recordType) {
                $recordType = 'expense';
            }

            $branch = $this->branch($data);
            if (! $branch) {
                $this->skip($rowNumber, 'Branch was not found. Use branch_name or branch_code.');
                continue;
            }

            $amount = $this->amount($data['amount'] ?? $data['المبلغ'] ?? $data['مبلغ'] ?? null);
            if ($amount === null || $amount <= 0) {
                $this->skip($rowNumber, 'Amount must be greater than zero.');
                continue;
            }

            $date = $this->date($data['date'] ?? $data['التاريخ'] ?? $data['تاريخ'] ?? null);
            if (! $date) {
                $this->skip($rowNumber, 'Date is invalid.');
                continue;
            }

            if ($recordType === 'income') {
                $typeName = trim((string) ($data['type_name'] ?? $data['income_type_name'] ?? $data['النوع'] ?? $data['نوع'] ?? ''));
                $typeId   = $typeName !== '' ? $this->incomeTypeId($typeName) : null;

                if (! $typeId) {
                    $this->skip($rowNumber, 'Income type is required.');
                    continue;
                }

                $this->incomeBuffer[] = [
                    'branch_id' => $branch->id,
                    'income_type_id' => $typeId,
                    'admin_id' => $adminId,
                    'amount' => $amount,
                    'date' => $date,
                    'payment_method' => $this->paymentMethod($data['payment_method'] ?? null),
                    'reference_number' => $this->resolveReference($data['reference_number'] ?? null, 'INC'),
                    'description' => $this->nullableString($data['description'] ?? $data['البيان'] ?? $data['byan'] ?? null),
                    'recipient' => $this->nullableString($data['recipient'] ?? $data['المستفيد'] ?? null),
                    'notes' => $this->nullableString($data['notes'] ?? $data['ملاحظات'] ?? null),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $this->incomeCount++;
                $this->flushIfFull();
                continue;
            }

            $typeName = trim((string) ($data['type_name'] ?? $data['expense_type_name'] ?? $data['النوع'] ?? $data['نوع'] ?? ''));
            $typeId   = $typeName !== '' ? $this->expenseTypeId($typeName) : null;

            if (! $typeId) {
                $this->skip($rowNumber, 'Expense type is required.');
                continue;
            }

            $this->expenseBuffer[] = [
                'branch_id' => $branch->id,
                'expense_type_id' => $typeId,
                'admin_id' => $adminId,
                'amount' => $amount,
                'date' => $date,
                'payment_method' => $this->paymentMethod($data['payment_method'] ?? null),
                'status' => 'pending',
                'reference_number' => $this->resolveReference($data['reference_number'] ?? null, 'EXP'),
                'description' => $this->nullableString($data['description'] ?? $data['البيان'] ?? $data['byan'] ?? null),
                'recipient' => $this->nullableString($data['recipient'] ?? $data['المستفيد'] ?? null),
                'notes' => $this->nullableString($data['notes'] ?? $data['ملاحظات'] ?? null),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $this->expenseCount++;
            $this->flushIfFull();
        }

        // Write whatever is left in the buffers.
        $this->flush();
    }

    /** Write buffered rows once they reach the batch size. */
    private function flushIfFull(): void
    {
        if (count($this->incomeBuffer) >= self::BATCH_SIZE || count($this->expenseBuffer) >= self::BATCH_SIZE) {
            $this->flush();
        }
    }

    /** Bulk-insert buffered rows — one query per batch instead of one per row. */
    private function flush(): void
    {
        if ($this->incomeBuffer) {
            $rows = $this->incomeBuffer;
            $this->incomeBuffer = [];
            $this->insertBatch(Income::class, $rows, $this->incomeCount);
        }

        if ($this->expenseBuffer) {
            $rows = $this->expenseBuffer;
            $this->expenseBuffer = [];
            $this->insertBatch(Expense::class, $rows, $this->expenseCount);
        }
    }

    /**
     * Insert a batch in one query. If the batch fails (one bad row would
     * otherwise discard all of them) retry row by row so the good rows still
     * land and only the genuinely broken ones are reported as skipped.
     *
     * @param  class-string  $model
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function insertBatch(string $model, array $rows, int &$counter): void
    {
        try {
            $model::insert($rows);
            return;
        } catch (\Throwable) {
            // fall through to per-row retry
        }

        foreach ($rows as $row) {
            try {
                $model::insert([$row]);
            } catch (\Throwable $e) {
                $counter--;
                $this->skippedCount++;
                $ref = $row['reference_number'] ?? '—';
                $this->errors[] = "Reference {$ref}: could not be saved ({$e->getMessage()})";
            }
        }
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function recordType(array $row): ?string
    {
        $value = mb_strtolower(trim((string) ($row['record_type'] ?? $row['transaction_type'] ?? '')));
        $typeName = mb_strtolower(trim((string) ($row['type_name'] ?? $row['income_type_name'] ?? $row['expense_type_name'] ?? '')));

        if ($value === '' && array_key_exists('income_type_name', $row)) {
            return 'income';
        }

        if ($value === '' && array_key_exists('expense_type_name', $row)) {
            return 'expense';
        }

        if ($value === '' && (str_contains($typeName, 'ايراد') || str_contains($typeName, 'إيراد'))) {
            return 'income';
        }

        if ($value === '' && str_contains($typeName, 'مصروف')) {
            return 'expense';
        }

        return match ($value) {
            'income', 'incomes', 'revenue', 'revenues', 'ايراد', 'إيراد', 'الايراد', 'الإيراد', 'الايرادات', 'الإيرادات' => 'income',
            'expense', 'expenses', 'cost', 'costs', 'مصروف', 'مصروفات', 'المصروفات' => 'expense',
            default => null,
        };
    }

    /** Resolve an income type id, creating it once and caching by name. */
    private function incomeTypeId(string $name): ?int
    {
        if (! isset($this->incomeTypeCache[$name])) {
            $this->incomeTypeCache[$name] = IncomeType::firstOrCreate(
                ['name' => $name],
                ['active' => true]
            )->id;
        }

        return $this->incomeTypeCache[$name];
    }

    /** Resolve an expense type id, creating it once and caching by name. */
    private function expenseTypeId(string $name): ?int
    {
        if (! isset($this->expenseTypeCache[$name])) {
            $this->expenseTypeCache[$name] = ExpenseType::firstOrCreate(
                ['name' => $name],
                ['active' => true]
            )->id;
        }

        return $this->expenseTypeCache[$name];
    }

    /**
     * يستخدم BranchResolver المشترك: أي اختصار في اسم الفرع («الحفر») يُطابق
     * الفرع الصحيح («حفر الباطن») بدل إنشاء فرع مكرّر.
     */
    private function branch(array $row): ?Branch
    {
        $name = trim((string) ($row['branch_name'] ?? $row['branch'] ?? $row['المكتب'] ?? $row['مكتب'] ?? $row['الفرع'] ?? $row['فرع'] ?? ''));
        $code = trim((string) ($row['branch_code'] ?? $row['كود_الفرع'] ?? ''));

        return $this->branchResolver->resolve($name, $code);
    }

    private function amount(mixed $value): ?float
    {
        $amount = str_replace([',', ' ', "\xc2\xa0"], '', (string) $value);

        return is_numeric($amount) ? (float) $amount : null;
    }

    private function date(mixed $value): ?string
    {
        try {
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->toDateString();
            }

            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->toDateString();
            }

            if (trim((string) $value) === '') {
                return null;
            }

            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function paymentMethod(mixed $value): string
    {
        $v = mb_strtolower(trim((string) $value));
        if (in_array($v, ['bank_transfer', 'bank transfer', 'transfer', "\u062a\u062d\u0648\u064a\u0644", "\u062a\u062d\u0648\u064a\u0644 \u0628\u0646\u0643\u064a"])) {
            return 'bank_transfer';
        }
        if (in_array($v, ['card', 'visa', "\u0628\u0637\u0627\u0642\u0629", "\u0643\u0627\u0631\u062a"])) {
            return 'card';
        }
        if (in_array($v, ['other', "\u0627\u062e\u0631\u0649", "\u0623\u062e\u0631\u0649"])) {
            return 'other';
        }
        if (in_array($v, ['cash', "\u0646\u0642\u062f", "\u0646\u0642\u062f\u064a"])) {
            return 'cash';
        }
        return 'bank_transfer';
    }

    private function resolveReference(mixed $value, string $prefix = 'REF'): string
    {
        $str = trim((string) $value);
        if ($str !== '' && $str !== '0') {
            return $str;
        }
        return $prefix . '-' . strtoupper(substr(uniqid(), -8));
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function skip(int $rowNumber, string $message): void
    {
        $this->skippedCount++;
        $this->errors[] = "Row {$rowNumber}: {$message}";
    }
}
