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

class RecruitmentStatementImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    public int $incomeCount  = 0;
    public int $expenseCount = 0;
    public int $skippedCount = 0;

    /** @var array<int, string> */
    public array $errors = [];

    /** Branch name extracted from the title row (e.g. "شركة الامتياز للاستقدام") */
    private ?string $titleBranch = null;

    // Known brand aliases -> real branch name
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

    /**
     * Row 2 is the real heading (row 1 is the company title like "شركة الامتياز للاستقدام").
     */
    public function headingRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows): void
    {
        $adminId = Auth::guard('admin')->id() ?? Admin::query()->value('id');

        // Extract branch from title row (row 1) by scanning aliases in the first row's values
        if ($this->titleBranch === null && $rows->isNotEmpty()) {
            $firstRow = $rows->first();
            foreach ($firstRow->toArray() as $cell) {
                $cell = trim((string) $cell);
                foreach (self::BRANCH_ALIASES as $alias => $real) {
                    if (mb_strpos($cell, $alias) !== false) {
                        $this->titleBranch = $real;
                        break 2;
                    }
                }
            }
        }

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 3; // heading=row2, data starts row3
            $data = $row->toArray();

            if ($this->isEmptyRow($data)) {
                continue;
            }

            // --- Resolve branch (with title fallback) ---
            $branch = $this->resolveBranch($data);
            if (! $branch) {
                $this->skip($rowNumber, 'Branch was not found.');
                continue;
            }

            // --- Resolve date ---
            $date = $this->resolveDate($data);
            if (! $date) {
                $this->skip($rowNumber, 'Date is invalid.');
                continue;
            }

            // --- Reference / description from contract number + employer ID ---
            $contractNo  = $this->str($data['رقم_العقد']  ?? $data['رقم العقد']  ?? $data['contract_number'] ?? null);
            $employerId  = $this->str($data['هوية_صاحب_العمل'] ?? $data['هوية صاحب العمل'] ?? $data['employer_id'] ?? null);
            $nationality = $this->str($data['الجنسية'] ?? $data['جنسية'] ?? $data['nationality'] ?? null);

            $baseRef  = $contractNo ?: ('ROW-' . $rowNumber);
            $baseDesc = trim(implode(' - ', array_filter([$contractNo, $employerId, $nationality])));

            // === 1. Income: ايراد استقدام ===
            $incomeAmount = $this->amount(
                $data['ايراد_استقدام']   ?? $data['ايراد استقدام']   ??
                $data['إيراد_استقدام']  ?? $data['إيراد استقدام']  ??
                $data['income']          ?? $data['ايراد']           ?? null
            );

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

            // === 2. Expense: تكاليف الاستقدام ===
            $expAmount = $this->amount(
                $data['تكاليف_الاستقدام_مصاريف'] ?? $data['تكاليف الاستقدام مصاريف'] ??
                $data['تكاليف_الاستقدام']         ?? $data['تكاليف الاستقدام']         ??
                $data['تكاليف']                   ?? $data['مصاريف']                   ??
                $data['expense']                   ?? null
            );

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

            // === 3. Expense: مباشرة للعقود الضريبية ===
            $taxAmount = $this->amount(
                $data['مباشرة_للعقود_الضريبية'] ?? $data['مباشرة للعقود الضريبية'] ??
                $data['ضريبة']                   ?? $data['tax']                     ?? null
            );

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

    private function resolveBranch(array $data): ?Branch
    {
        $name = trim((string) (
            $data['الفرع']       ?? $data['فرع']        ??
            $data['المكتب']      ?? $data['مكتب']       ??
            $data['branch_name'] ?? $data['branch']     ?? ''
        ));
        $code = trim((string) ($data['branch_code'] ?? $data['كود_الفرع'] ?? ''));

        // Apply alias mapping
        if (isset(self::BRANCH_ALIASES[$name])) {
            $name = self::BRANCH_ALIASES[$name];
        }

        // If still empty, fall back to branch extracted from title row (row 1 company name)
        if ($name === '' && $code === '' && $this->titleBranch !== null) {
            $name = $this->titleBranch;
        }

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

    private function resolveDate(array $data): ?string
    {
        $value = $data['تاريخ_بداية_العقد'] ?? $data['تاريخ بداية العقد'] ??
                 $data['التاريخ']            ?? $data['تاريخ']             ??
                 $data['date']               ?? null;

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
