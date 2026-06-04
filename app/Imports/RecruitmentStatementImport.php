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
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Reads the recruitment statement Excel by COLUMN INDEX (not name),
 * because WithHeadingRow strips Arabic characters via Str::slug().
 *
 * Expected column order (row 1 = headers, row 2+ = data):
 *   0: رقم العقد
 *   1: هوية صاحب العمل
 *   2: الجنسية
 *   3: المهنة
 *   4: الفرع
 *   5: تاريخ بداية العقد
 *   6: ايراد استقدام
 *   7: تكاليف الاستقدام مصاريف
 *   8: مباشرة للعقود الضريبية
 */
class RecruitmentStatementImport implements ToCollection, WithCalculatedFormulas
{
    public int $incomeCount  = 0;
    public int $expenseCount = 0;
    public int $skippedCount = 0;

    /** @var array<int, string> */
    public array $errors = [];

    private const BRANCH_ALIASES = [
        'امتياز'   => 'الرياض',
        'الامتياز' => 'الرياض',
        'متميز'    => 'عرعر',
        'المتميز'  => 'عرعر',
        'انجاز'    => 'حفر الباطن',
        'الانجاز'  => 'حفر الباطن',
        'إنجاز'    => 'حفر الباطن',
        'الإنجاز'  => 'حفر الباطن',
    ];

    // Column indexes
    private const COL_CONTRACT    = 0;
    private const COL_EMPLOYER    = 1;
    private const COL_NATIONALITY = 2;
    private const COL_JOB         = 3;
    private const COL_BRANCH      = 4;
    private const COL_DATE        = 5;
    private const COL_INCOME      = 6;
    private const COL_EXPENSE     = 7;
    private const COL_TAX         = 8;

    public function collection(Collection $rows): void
    {
        $adminId = Auth::guard('admin')->id() ?? Admin::query()->value('id');

        foreach ($rows as $index => $row) {
            // Skip row 1 (headers)
            if ($index === 0) {
                continue;
            }

            $rowNumber = $index + 1;
            $data = $row->values()->toArray(); // indexed array

            if ($this->isEmptyRow($data)) {
                continue;
            }
            }

            // --- Resolve branch (column 4) ---
            $branch = $this->resolveBranch(trim((string) ($data[self::COL_BRANCH] ?? '')));
            if (! $branch) {
                $this->skip($rowNumber, 'Branch was not found.');
                continue;
            }

            // --- Resolve date (column 5) ---
            $date = $this->resolveDate($data[self::COL_DATE] ?? null);
            if (! $date) {
                $this->skip($rowNumber, 'Date is invalid.');
                continue;
            }

            // --- Reference / description ---
            $contractNo  = $this->str($data[self::COL_CONTRACT]    ?? null);
            $employerId  = $this->str($data[self::COL_EMPLOYER]     ?? null);
            $nationality = $this->str($data[self::COL_NATIONALITY]  ?? null);

            $baseRef  = $contractNo ?: ('ROW-' . $rowNumber);
            $baseDesc = trim(implode(' - ', array_filter([$contractNo, $employerId, $nationality])));

            // === 1. Income: ايراد استقدام (column 6) ===
            $incomeAmount = $this->amount($data[self::COL_INCOME] ?? null);

            if ($incomeAmount !== null && $incomeAmount > 0) {
                $incomeType = IncomeType::firstOrCreate(['name' => 'ايرادات الاستقدام'], ['active' => true]);
                Income::create([
                    'branch_id'        => $branch->id,
                    'income_type_id'   => $incomeType->id,
                    'admin_id'         => $adminId,
                    'amount'           => $incomeAmount,
                    'date'             => $date,
                    'payment_method'   => 'bank_transfer',
                    'reference_number' => 'INC-' . $baseRef,
                    'description'      => $baseDesc ?: 'ايراد استقدام',
                ]);
                $this->incomeCount++;
            }

            // === 2. Expense: تكاليف الاستقدام (column 7) ===
            $expAmount = $this->amount($data[self::COL_EXPENSE] ?? null);

            if ($expAmount !== null && $expAmount > 0) {
                $expType = ExpenseType::firstOrCreate(['name' => 'تكاليف الاستقدام'], ['active' => true]);
                Expense::create([
                    'branch_id'        => $branch->id,
                    'expense_type_id'  => $expType->id,
                    'admin_id'         => $adminId,
                    'amount'           => $expAmount,
                    'date'             => $date,
                    'payment_method'   => 'bank_transfer',
                    'status'           => 'pending',
                    'reference_number' => 'EXP-' . $baseRef,
                    'description'      => $baseDesc ?: 'تكاليف الاستقدام',
                ]);
                $this->expenseCount++;
            }

            // === 3. Expense: مباشرة للعقود الضريبية (column 8) ===
            $taxAmount = $this->amount($data[self::COL_TAX] ?? null);

            if ($taxAmount !== null && $taxAmount > 0) {
                $taxType = ExpenseType::firstOrCreate(['name' => 'ضريبة العقود'], ['active' => true]);
                Expense::create([
                    'branch_id'        => $branch->id,
                    'expense_type_id'  => $taxType->id,
                    'admin_id'         => $adminId,
                    'amount'           => $taxAmount,
                    'date'             => $date,
                    'payment_method'   => 'bank_transfer',
                    'status'           => 'pending',
                    'reference_number' => 'TAX-' . $baseRef,
                    'description'      => $baseDesc ?: 'ضريبة العقود',
                ]);
                $this->expenseCount++;
            }
        }
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function resolveBranch(string $raw): ?Branch
    {
        $name = $raw;
        $code = '';

        // Apply alias mapping
        if (isset(self::BRANCH_ALIASES[$name])) {
            $name = self::BRANCH_ALIASES[$name];
        }

        if ($name === '') return null;

        // 1. Exact code
        if ($code !== '') {
            $b = Branch::withTrashed()->where('code', $code)->first();
            if ($b) { if ($b->trashed()) $b->restore(); return $b; }
        }

        // 2. name as code
        if ($name !== '') {
            $b = Branch::withTrashed()->where('code', $name)->first();
            if ($b) { if ($b->trashed()) $b->restore(); return $b; }
        }

        // 3. Exact name
        if ($name !== '') {
            $b = Branch::withTrashed()->where('name', $name)->first();
            if ($b) { if ($b->trashed()) $b->restore(); return $b; }
        }

        // 4. Fuzzy
        if ($name !== '') {
            $normIn  = $this->normalize($name);
            $sortIn  = $this->sortedChars($normIn);
            $b = Branch::withTrashed()->get()->first(function ($br) use ($normIn, $sortIn) {
                similar_text($normIn, $br->code ?? '', $pc);
                if ($pc >= 85) return true;
                $normDb = $this->normalize($br->name);
                if ($normDb === $normIn) return true;
                if ($this->sortedChars($normDb) === $sortIn) return true;
                similar_text($normIn, $normDb, $p);
                return $p >= 70;
            });
            if ($b) { if ($b->trashed()) $b->restore(); return $b; }
        }

        // 5. Create
        if ($name !== '' || $code !== '') {
            $n = $name ?: $code;
            $c = $code ?: ('BR-' . strtoupper(substr(md5($n), 0, 6)));
            try {
                return Branch::firstOrCreate(['code' => $c], ['name' => $n, 'active' => true]);
            } catch (\Throwable) {
                return Branch::firstOrCreate(['name' => $n], ['code' => 'BR-' . strtoupper(substr(md5($n . time()), 0, 6)), 'active' => true]);
            }
        }

        return null;
    }

    private function resolveDate(mixed $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        try {
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->toDateString();
            }
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->toDateString();
            }
            // Strip time part if present (e.g. "2026-05-30 17:46:00")
            $str = trim((string) $value);
            if (preg_match('/(\d{4}-\d{2}-\d{2})/', $str, $m)) {
                return $m[1];
            }
            return Carbon::parse($str)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function amount(mixed $value): ?float
    {
        if ($value === null) return null;
        $v = str_replace([',', ' ', "\xc2\xa0"], '', (string) $value);
        return is_numeric($v) ? (float) $v : null;
    }

    private function str(mixed $value): string
    {
        return trim((string) ($value ?? ''));
    }

    private function normalize(string $name): string
    {
        $words = array_filter(preg_split('/\s+/u', trim($name)));
        $words = array_map(fn($w) => preg_replace('/^ال/u', '', $w), $words);
        return implode(' ', $words);
    }

    private function sortedChars(string $str): string
    {
        $str   = str_replace(' ', '', $str);
        $chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
        sort($chars);
        return implode('', $chars);
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $v) {
            if (trim((string) $v) !== '') return false;
        }
        return true;
    }

    private function skip(int $rowNumber, string $message): void
    {
        $this->skippedCount++;
        $this->errors[] = "Row {$rowNumber}: {$message}";
    }
}
