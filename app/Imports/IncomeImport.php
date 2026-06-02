<?php

namespace App\Imports;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Income;
use App\Models\IncomeType;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class IncomeImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $branchName = trim((string) ($row['branch_name'] ?? ''));
        $branchCode = trim((string) ($row['branch_code'] ?? ''));
        $typeName   = trim((string) ($row['type_name'] ?? $row['income_type_name'] ?? ''));

        $branch = $branchName !== '' ? Branch::where('name', $branchName)->first() : null;
        if (! $branch && $branchCode !== '') {
            $branch = Branch::where('code', $branchCode)->first();
        }

        $type = $typeName !== '' ? IncomeType::firstOrCreate(['name' => $typeName], ['active' => true]) : null;

        if (! $branch || ! $type) {
            return null;
        }

        return new Income([
            'branch_id'        => $branch->id,
            'income_type_id'   => $type->id,
            'admin_id'         => Auth::guard('admin')->id() ?? Admin::first()?->id,
            'amount'           => $row['amount'],
            'date'             => $row['date'],
            'payment_method'   => in_array($row['payment_method'], ['cash', 'bank_transfer', 'card', 'other'])
                                    ? $row['payment_method'] : 'cash',
            'reference_number' => $row['reference_number'] ?? null,
            'description'      => $row['description'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'branch_name'       => ['nullable'],
            'branch_code'       => ['nullable'],
            'type_name'         => ['nullable'],
            'income_type_name'  => ['nullable'],
            'amount'            => ['required', 'numeric', 'min:0.01'],
            'date'              => ['required', 'date'],
            'payment_method'    => ['required'],
        ];
    }
}
