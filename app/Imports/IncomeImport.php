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

        $branch = $this->resolveBranch($branchName, $branchCode);

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

    private function resolveBranch(string $branchName, string $branchCode): ?Branch
    {
        // 1. By exact code
        if ($branchCode !== '') {
            $branch = Branch::where('code', $branchCode)->first();
            if ($branch) return $branch;
        }

        // 2. By exact name
        if ($branchName !== '') {
            $branch = Branch::where('name', $branchName)->first();
            if ($branch) return $branch;
        }

        // 3. By normalized name (ignore ال prefix on each word)
        if ($branchName !== '') {
            $normalized = $this->normalizeBranchName($branchName);
            $branch = Branch::all()->first(
                fn($b) => $this->normalizeBranchName($b->name) === $normalized
            );
            if ($branch) return $branch;
        }

        // 4. Not found — create new branch
        if ($branchName !== '' || $branchCode !== '') {
            $name = $branchName ?: $branchCode;
            $code = $branchCode ?: $this->generateBranchCode();
            return Branch::create(['name' => $name, 'code' => $code, 'active' => true]);
        }

        return null;
    }

    private function normalizeBranchName(string $name): string
    {
        $words = array_filter(preg_split('/\s+/', trim($name)));
        $words = array_map(fn($w) => preg_replace('/^ال/', '', $w), $words);
        return implode(' ', $words);
    }

    private function generateBranchCode(): string
    {
        $max = Branch::withTrashed()->max('id') ?? 0;
        return 'BR' . str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }
}
