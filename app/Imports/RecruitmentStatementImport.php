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

    /** Branch list cached for the whole import — reloading it per row caused 504 timeouts. */
    private ?\Illuminate\Support\Collection $allBranches = null;

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

    // Column indexes (0-based) — matches the actual uploaded file format:
    // 0=رقم العقد, 1=هوية صاحب العمل, 2=رقم الصادر(تأشيرة), 3=المهنة, 4=الجنسية,
    // 5=ايراد استقدام, 6=تكاليف الاستقدام, 7=مصاريف مباشرة للعقود,
    // 8=الضريبي, 9=تاريخ بداية العقد, 10=الفرع (branch)
    private const COL_CONTRACT    = 0;  // رقم العقد
    private const COL_EMPLOYER    = 1;  // هوية صاحب العمل
    private const COL_VISA        = 2;  // رقم التأشيرة (رقم الصادر)
    private const COL_JOB         = 3;  // المهنة
    private const COL_NATIONALITY = 4;  // الجنسية
    private const COL_INCOME      = 5;  // ايراد استقدام
    private const COL_EXPENSE     = 6;  // تكاليف الاستقدام
    private const COL_EXPENSE2    = 7;  // مصاريف مباشرة للعقود
    private const COL_TAX         = 8;  // الضريبي
    private const COL_DATE        = 9;  // تاريخ بداية العقد
    private const COL_BRANCH      = 10; // الفرع (branch) — اسم أو رقم كود الفرع

    public function collection(Collection $rows): void
    {
        $adminId = Auth::guard('admin')->id() ?? Admin::query()->value('id');

        foreach ($rows as $index => $row) {
            // Row 0 = headers — capture and show them for debugging
            if ($index === 0) {
                $headers = array_values($row->toArray());
                foreach ($headers as $col => $name) {
                    $this->errors[] = "Col {$col}: " . trim((string) $name);
                }
                $this->skippedCount++;
                continue;
            }

            $rowNumber = $index + 1;
            $data = array_values($row->toArray());

            if ($this->isEmptyRow($data)) {
                continue;
            }

            // --- Branch (column 4) ---
            $branch = $this->resolveBranch(trim((string) ($data[self::COL_BRANCH] ?? '')));
            if (! $branch) {
                $this->skip($rowNumber, 'Branch was not found.');
                continue;
            }

            // --- Date (column 5) ---
            $date = $this->resolveDate($data[self::COL_DATE] ?? null);
            if (! $date) {
                $this->skip($rowNumber, 'Date is invalid.');
                continue;
            }

            // --- Reference / description ---
            $contractNo  = $this->str($data[self::COL_CONTRACT]    ?? null);
            $visaNo      = $this->str($data[self::COL_VISA]         ?? null);
            $nationality = $this->str($data[self::COL_NATIONALITY]  ?? null);
            $baseRef     = $contractNo ?: ('ROW-' . $rowNumber);
            // البيان: رقم العقد - رقم التأشيرة - الجنسية
            $baseDesc = trim(implode(' - ', array_filter([$contractNo, $visaNo, $nationality])));

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

            // === 3. Expense: مصاريف مباشرة للعقود (column 7) ===
            $exp2Amount = $this->amount($data[self::COL_EXPENSE2] ?? null);
            if ($exp2Amount !== null && $exp2Amount > 0) {
                $exp2Type = ExpenseType::firstOrCreate(['name' => 'مصاريف مباشرة للعقود'], ['active' => true]);
                Expense::create([
                    'branch_id'        => $branch->id,
                    'expense_type_id'  => $exp2Type->id,
                    'admin_id'         => $adminId,
                    'amount'           => $exp2Amount,
                    'date'             => $date,
                    'payment_method'   => 'bank_transfer',
                    'status'           => 'pending',
                    'reference_number' => 'EXP2-' . $baseRef,
                    'description'      => $baseDesc ?: 'مصاريف مباشرة للعقود',
                ]);
                $this->expenseCount++;
            }

            // === 4. Expense: الضريبي (column 8) ===
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

    // -------------------------------------------------------------------------

    private function resolveBranch(string $raw): ?Branch
    {
        $name = $raw;

        if (isset(self::BRANCH_ALIASES[$name])) {
            $name = self::BRANCH_ALIASES[$name];
        }

        if ($name === '') {
            return null;
        }

        // 1. Name as code
        $b = Branch::withTrashed()->where('code', $name)->first();
        if ($b) { if ($b->trashed()) $b->restore(); return $b; }

        // 2. Exact name
        $b = Branch::withTrashed()->where('name', $name)->first();
        if ($b) { if ($b->trashed()) $b->restore(); return $b; }

        // 3. Fuzzy
        $normIn = $this->normalize($name);
        $sortIn = $this->sortedChars($normIn);
        $this->allBranches ??= Branch::withTrashed()->get();
        $b = $this->allBranches->first(function ($br) use ($normIn, $sortIn) {
            $normDb = $this->normalize($br->name);
            if ($normDb === $normIn) return true;
            if ($this->sortedChars($normDb) === $sortIn) return true;
            similar_text($normIn, $normDb, $p);
            return $p >= 70;
        });
        if ($b) { if ($b->trashed()) $b->restore(); return $b; }

        // 4. Create new branch
        $code = 'BR-' . strtoupper(substr(md5($name), 0, 6));
        return Branch::firstOrCreate(['name' => $name], ['code' => $code, 'active' => true]);
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
