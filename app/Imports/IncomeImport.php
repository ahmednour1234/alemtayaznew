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

    public function model(array $row)
    {
        $branchName = trim((string) ($row['branch_name'] ?? $row['branch'] ?? $row['المكتب'] ?? $row['مكتب'] ?? $row['الفرع'] ?? $row['فرع'] ?? ''));
        $branchCode = trim((string) ($row['branch_code'] ?? $row['كود_الفرع'] ?? ''));
        $typeName   = trim((string) ($row['type_name'] ?? $row['income_type_name'] ?? ''));

        // Apply branch alias mapping
        if (isset(self::BRANCH_ALIASES[$branchName])) {
            $branchName = self::BRANCH_ALIASES[$branchName];
        }

        $branch = $this->resolveBranch($branchName, $branchCode);

        $type = $typeName !== '' ? IncomeType::firstOrCreate(['name' => $typeName], ['active' => true]) : null;

        if (! $branch || ! $type) {
            return null;
        }

        $ref = trim((string) ($row['reference_number'] ?? ''));
        if ($ref === '' || $ref === '0') {
            $ref = 'INC-' . strtoupper(substr(uniqid(), -8));
        }

        return new Income([
            'branch_id'        => $branch->id,
            'income_type_id'   => $type->id,
            'admin_id'         => Auth::guard('admin')->id() ?? Admin::first()?->id,
            'amount'           => $row['amount'],
            'date'             => $row['date'],
            'payment_method'   => 'bank_transfer',
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
            'income_type_name'  => ['nullable'],
            'amount'            => ['required', 'numeric', 'min:0.01'],
            'date'              => ['required', 'date'],
            'payment_method'    => ['nullable'],
        ];
    }

    private function resolveBranch(string $branchName, string $branchCode): ?Branch
    {
        // 1. By exact code
        if ($branchCode !== '') {
            $branch = Branch::where('code', $branchCode)->first();
            if ($branch) return $branch;
        }

        // 2. branch_name value might actually be a code (e.g. HFR-001)
        if ($branchName !== '') {
            $branch = Branch::where('code', $branchName)->first();
            if ($branch) return $branch;
        }

        // 3. By exact name
        if ($branchName !== '') {
            $branch = Branch::where('name', $branchName)->first();
            if ($branch) return $branch;
        }

        // 4. Fuzzy match: normalized + sorted chars + 70% similarity
        if ($branchName !== '') {
            $normInput   = $this->normalizeBranchName($branchName);
            $sortedInput = $this->sortedChars($normInput);

            $this->allBranches ??= Branch::all();
            $branch = $this->allBranches->first(function ($b) use ($normInput, $sortedInput) {
                // also try code fuzzy match
                similar_text($normInput, $b->code ?? '', $pctCode);
                if ($pctCode >= 85) return true;
                $normDb   = $this->normalizeBranchName($b->name);
                // a) normalized exact match (حفر باطن = حفر الباطن)
                if ($normDb === $normInput) return true;
                // b) same chars different order (ليان = لينا)
                if ($this->sortedChars($normDb) === $sortedInput) return true;
                // c) high similarity >= 70% for minor typos
                similar_text($normInput, $normDb, $pct);
                return $pct >= 70;
            });
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

    private function generateBranchCode(): string
    {
        $max = Branch::withTrashed()->max('id') ?? 0;
        return 'BR' . str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }
}
