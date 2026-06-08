<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TypeBreakdownExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $data) {}

    public function title(): string
    {
        return 'الإيرادات والمصروفات حسب البند';
    }

    public function headings(): array
    {
        return ['النوع', 'البند', 'عدد العمليات', 'الإجمالي'];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->data['income_rows'] as $r) {
            $rows[] = ['إيراد', $r['name'], $r['count'], number_format($r['total'], 2)];
        }
        $rows[] = ['', 'إجمالي الإيرادات', '', number_format($this->data['income_total'], 2)];
        $rows[] = ['', '', '', ''];

        foreach ($this->data['expense_rows'] as $r) {
            $rows[] = ['مصروف', $r['name'], $r['count'], number_format($r['total'], 2)];
        }
        $rows[] = ['', 'إجمالي المصروفات', '', number_format($this->data['expense_total'], 2)];
        $rows[] = ['', '', '', ''];
        $rows[] = ['', 'الصافي', '', number_format($this->data['net'], 2)];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
