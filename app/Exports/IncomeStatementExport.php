<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncomeStatementExport implements FromArray, WithHeadings, WithStyles
{
    public function __construct(private readonly array $data) {}

    public function headings(): array
    {
        return ['الفرع', 'إجمالي الدخل', 'إجمالي المصاريف المعتمدة', 'صافي الربح', 'المصاريف المعلقة'];
    }

    public function array(): array
    {
        return $this->data['rows']->map(fn($r) => [
            $r['branch']->name,
            number_format($r['total_income'], 2),
            number_format($r['total_expenses'], 2),
            number_format($r['net_profit'], 2),
            number_format($r['pending_expenses'], 2),
        ])->toArray();
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
