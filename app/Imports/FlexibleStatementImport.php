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
 * Columns (by index):
 *   0 = التاريخ      (date)
 *   1 = الفرع        (branch name)
 *   2 = النوع        (type name — contains keyword "إيراد" or "مصروف"/"مصاريف")
 *   3 = النوع (2nd)  (ignored — may be duplicate)
 *   4 = المبلغ       (amount)
 *   5 = العملة       (ignored)
 *   6 = المرجع       (reference_number)
 *   7 = المستلم      (ignored)
 *   8 = طريقة الدفع  (payment_method — defaults to cash if empty)
 *   9 = الحالة       (ignored)
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
        $adminId = Auth::guard('admin')->id() ?? Admin::first()?->id;

        // Cache branch lookups to avoid N+1 queries
        $branchCache      = [];
        $incomeTypeCache  = [];
        $expenseTypeCache = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 1;

            // Skip header row(s): first row, or any row where column 4 (amount) is non-numeric
            if ($rowNum === 1) {
                continue;
            }

            $rawDate          = $row[0] ?? null;
            $branchValue      = trim((string) ($row[1] ?? ''));
            $typeName         = trim((string) ($row[2] ?? ''));
            $amount           = $row[4] ?? null;
            $reference        = trim((string) ($row[6] ?? ''));
            $paymentMethodRaw = trim((string) ($row[8] ?? ''));

            // Skip completely empty rows
            if ($branchValue === '' && $typeName === '' && $amount === null) {
                continue;
            }

            // Skip rows with no usable amount
            if (! is_numeric($amount) || (float) $amount <= 0) {
                $this->skippedCount++;
                $this->errors[] = "صف {$rowNum}: المبلغ غير صالح ({$amount})";
                continue;
            }

            // Resolve branch (by name or code)
            if (! isset($branchCache[$branchValue])) {
                $branchCache[$branchValue] = Branch::where('name', $branchValue)->first()
                    ?? Branch::where('name', 'like', '%' . mb_substr($branchValue, 0, 4) . '%')->first()
                    ?? Branch::where('code', $branchValue)->first();
            }
            $branch = $branchCache[$branchValue];

            if (! $branch) {
                $this->skippedCount++;
                $this->errors[] = "صف {$rowNum}: الفرع \"{$branchValue}\" غير موجود";
                continue;
            }

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
            $isExpense = mb_strpos($typeName, 'مصروف') !== false
                      || mb_strpos($typeName, 'مصاريف') !== false;

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
                    'reference_number' => $reference ?: null,
                    'description'      => null,
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
                    'reference_number' => $reference ?: null,
                    'description'      => null,
                ]);

                $this->expenseCount++;

            } else {
                $this->skippedCount++;
                $this->errors[] = "صف {$rowNum}: لا يمكن تحديد نوع السجل من \"{$typeName}\"";
            }
        }
    }
}
