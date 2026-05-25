<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BranchStatementExport implements FromArray, WithHeadings, WithStyles
{
    public function __construct(private readonly array $data) {}

    public function headings(): array
    {
        return ['التاريخ', 'النوع', 'البيان', 'دائن (دخل)', 'مدين (مصروف)', 'الرصيد'];
    }

    public function array(): array
    {
        $rows    = [];
        $balance = 0;
        $d       = $this->data;

        foreach ($d['incomes'] as $inc) {
            $balance += $inc->amount;
            $rows[] = [$inc->date->format('Y-m-d'), 'دخل', $inc->incomeType?->name, number_format($inc->amount, 2), '-', number_format($balance, 2)];
        }

        foreach ($d['transfers_in'] as $t) {
            $balance += $t->amount;
            $rows[] = [$t->date->format('Y-m-d'), 'تحويل وارد', $t->description, number_format($t->amount, 2), '-', number_format($balance, 2)];
        }

        foreach ($d['expenses'] as $exp) {
            $balance -= $exp->amount;
            $rows[] = [$exp->date->format('Y-m-d'), 'مصروف', $exp->expenseType?->name, '-', number_format($exp->amount, 2), number_format($balance, 2)];
        }

        foreach ($d['transfers_out'] as $t) {
            $balance -= $t->amount;
            $rows[] = [$t->date->format('Y-m-d'), 'تحويل صادر', $t->description, '-', number_format($t->amount, 2), number_format($balance, 2)];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
