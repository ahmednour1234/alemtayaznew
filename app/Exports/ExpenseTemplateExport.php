<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpenseTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['BRANCH-001', 'مصروف رواتب', '5000.00', '2026-01-15', 'bank_transfer', 'REF-001', 'وصف اختياري'],
        ];
    }

    public function headings(): array
    {
        return ['branch_code', 'expense_type_name', 'amount', 'date', 'payment_method', 'reference_number', 'description'];
    }
}
