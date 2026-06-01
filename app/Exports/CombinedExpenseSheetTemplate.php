<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CombinedExpenseSheetTemplate implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'مصروفات';
    }

    public function headings(): array
    {
        return [
            'branch_code',
            'expense_type_name',
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
            ['BRANCH-001', 'مصروف رواتب',   '5000.00', '2026-01-15', 'bank_transfer', 'REF-010', 'مثال مصروف'],
            ['BRANCH-002', 'مصروف إيجار',   '3000.00', '2026-01-20', 'cash',          'REF-011', ''],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => 'dc2626']],
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
