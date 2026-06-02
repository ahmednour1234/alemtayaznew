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

    public function collection(Collection $rows): void
    {
        $adminId = Auth::guard('admin')->id() ?? Admin::query()->value('id');

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $data = $row->toArray();

            if ($this->isEmptyRow($data)) {
                continue;
            }

            $recordType = $this->recordType($data);
            if (! $recordType) {
                $this->skip($rowNumber, 'record_type must be income/expense or ايراد/مصروف.');
                continue;
            }

            $branch = $this->branch($data);
            if (! $branch) {
                $this->skip($rowNumber, 'Branch was not found. Use branch_name or branch_code.');
                continue;
            }

            $amount = $this->amount($data['amount'] ?? null);
            if ($amount === null || $amount <= 0) {
                $this->skip($rowNumber, 'Amount must be greater than zero.');
                continue;
            }

            $date = $this->date($data['date'] ?? null);
            if (! $date) {
                $this->skip($rowNumber, 'Date is invalid.');
                continue;
            }

            if ($recordType === 'income') {
                $typeName = trim((string) ($data['type_name'] ?? $data['income_type_name'] ?? ''));
                $type = $typeName !== '' ? IncomeType::firstOrCreate(['name' => $typeName], ['active' => true]) : null;

                if (! $type) {
                    $this->skip($rowNumber, 'Income type is required.');
                    continue;
                }

                Income::create([
                    'branch_id' => $branch->id,
                    'income_type_id' => $type->id,
                    'admin_id' => $adminId,
                    'amount' => $amount,
                    'date' => $date,
                    'payment_method' => $this->paymentMethod($data['payment_method'] ?? null),
                    'reference_number' => $this->nullableString($data['reference_number'] ?? null),
                    'description' => $this->nullableString($data['description'] ?? null),
                ]);

                $this->incomeCount++;
                continue;
            }

            $typeName = trim((string) ($data['type_name'] ?? $data['expense_type_name'] ?? ''));
            $type = $typeName !== '' ? ExpenseType::firstOrCreate(['name' => $typeName], ['active' => true]) : null;

            if (! $type) {
                $this->skip($rowNumber, 'Expense type is required.');
                continue;
            }

            Expense::create([
                'branch_id' => $branch->id,
                'expense_type_id' => $type->id,
                'admin_id' => $adminId,
                'amount' => $amount,
                'date' => $date,
                'payment_method' => $this->paymentMethod($data['payment_method'] ?? null),
                'status' => 'pending',
                'reference_number' => $this->nullableString($data['reference_number'] ?? null),
                'description' => $this->nullableString($data['description'] ?? null),
            ]);

            $this->expenseCount++;
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

    private function branch(array $row): ?Branch
    {
        $branchName = trim((string) ($row['branch_name'] ?? ''));
        $branchCode = trim((string) ($row['branch_code'] ?? ''));

        if ($branchName !== '') {
            return Branch::where('name', $branchName)->first()
                ?? Branch::where('name', 'like', '%' . $branchName . '%')->first();
        }

        if ($branchCode !== '') {
            return Branch::where('code', $branchCode)->first();
        }

        return null;
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
        $value = mb_strtolower(trim((string) $value));

        return match ($value) {
            'bank_transfer', 'bank transfer', 'transfer', 'تحويل', 'تحويل بنكي' => 'bank_transfer',
            'card', 'visa', 'بطاقة', 'كارت' => 'card',
            'other', 'اخرى', 'أخرى' => 'other',
            'cash', 'نقد', 'نقدي' => 'cash',
            default => 'cash',
        };
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
