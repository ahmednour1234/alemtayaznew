<?php

namespace App\Imports;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ExpenseImport implements ToModel, WithHeadingRow, WithValidation
{
    /** مطابقة أسماء الفروع (الاختصارات والأسماء التجارية) في مكان واحد. */
    private readonly \App\Services\BranchResolver $branchResolver;

    public function __construct()
    {
        $this->branchResolver = new \App\Services\BranchResolver();
    }

    public function model(array $row)
    {
        $branchName = trim((string) ($row['branch_name'] ?? $row['branch'] ?? $row['المكتب'] ?? $row['مكتب'] ?? $row['الفرع'] ?? $row['فرع'] ?? ''));
        $branchCode = trim((string) ($row['branch_code'] ?? $row['كود_الفرع'] ?? ''));
        $typeName   = trim((string) ($row['type_name'] ?? $row['expense_type_name'] ?? ''));

        $branch = $this->resolveBranch($branchName, $branchCode);

        $type = $typeName !== '' ? ExpenseType::firstOrCreate(['name' => $typeName], ['active' => true]) : null;

        if (! $branch || ! $type) {
            return null;
        }

        $ref = trim((string) ($row['reference_number'] ?? ''));
        if ($ref === '' || $ref === '0') {
            $ref = 'EXP-' . strtoupper(substr(uniqid(), -8));
        }

        return new Expense([
            'branch_id'        => $branch->id,
            'expense_type_id'  => $type->id,
            'admin_id'         => Auth::guard('admin')->id() ?? Admin::first()?->id,
            'amount'           => $row['amount'],
            'date'             => $row['date'],
            'payment_method'   => 'bank_transfer',
            'status'           => 'pending',
            'reference_number' => $ref,
            'description'      => $row['description'] ?? $row['البيان'] ?? $row['byan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'branch_name'       => ['nullable'],
            'branch_code'       => ['nullable'],
            'type_name'         => ['nullable'],
            'expense_type_name' => ['nullable'],
            'amount'            => ['required', 'numeric', 'min:0.01'],
            'date'              => ['required', 'date'],
            'payment_method'    => ['nullable'],
        ];
    }

    /**
     * يستخدم BranchResolver المشترك: أي اختصار في اسم الفرع («الحفر») يُطابق
     * الفرع الصحيح («حفر الباطن») بدل إنشاء فرع مكرّر.
     */
    private function resolveBranch(string $branchName, string $branchCode): ?Branch
    {
        return $this->branchResolver->resolve($branchName, $branchCode);
    }
}
