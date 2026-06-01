<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CombinedIncomeSheetTemplate implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'إيرادات';
    }

    public function headings(): array
    {
        return [
            'branch_code',
            'income_type_name',
            'amount',
            'date',
            'payment_method',
            'reference_number',
            'description',
        ];
    }

    public function array(): array
    {
        return [
            ['BRANCH-001', 'إيراد مبيعات', '1500.00', '2026-01-15', 'cash',          'REF-001', 'مثال إيراد'],
            ['BRANCH-002', 'إيراد خدمات',  '2000.00', '2026-01-20', 'bank_transfer', 'REF-002', ''],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '059669']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 22,
            'C' => 14,
            'D' => 14,
            'E' => 18,
            'F' => 18,
            'G' => 28,
        ];
    }
}
