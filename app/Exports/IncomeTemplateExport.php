<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IncomeTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['BRANCH-001', 'إيراد مبيعات', '1500.00', '2026-01-15', 'cash', 'REF-001', 'وصف اختياري'],
        ];
    }

    public function headings(): array
    {
        return ['branch_code', 'income_type_name', 'amount', 'date', 'payment_method', 'reference_number', 'description'];
    }
}
