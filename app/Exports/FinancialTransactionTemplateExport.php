<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialTransactionTemplateExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'record_type',
            'branch_name',
            'type_name',
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
            ['income', 'الرياض', 'إيرادات الاستقدام', '3500.00', '2026-06-02', 'cash', 'REF-001', 'مثال إيراد'],
            ['expense', 'الرياض', 'مصروف رواتب', '1200.00', '2026-06-02', 'bank_transfer', 'REF-002', 'مثال مصروف'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '334155']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 22,
            'C' => 26,
            'D' => 14,
            'E' => 14,
            'F' => 18,
            'G' => 20,
            'H' => 32,
        ];
    }
}
