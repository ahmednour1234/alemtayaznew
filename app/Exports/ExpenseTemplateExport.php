<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpenseTemplateExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'branch_name',       // اسم الفرع أو كوده (امتياز = الرياض، متميز = عرعر، انجاز = حفر الباطن)
            'type_name',         // نوع المصروف
            'amount',            // المبلغ
            'date',              // التاريخ (YYYY-MM-DD)
            'reference_number',  // رقم المرجع (اتركه فارغاً للتوليد التلقائي)
            'description',       // البيان
            'recipient',         // المستفيد
        ];
    }

    public function array(): array
    {
        return [
            ['امتياز',  'تذكرة سفر',     '3200.00', '2026-04-01', '',         'تذكرة عاملة فهد سعيد - داخل الضمان', 'فهد سعيد'],
            ['امتياز',  'كهرباء',         '1959.23', '2026-04-01', '',         'سداد فاتورة كهرباء سكن الرياض',       'الرياض'],
            ['متميز',   'مطالبة مالية',   '5000.00', '2026-04-01', '',         'مطالبة عميل - عرعر',                   'عرعر'],
            ['انجاز',   'نقليات',          '443.00',  '2026-04-01', '',         'رسوم نقل داخلية - حفر الباطن',        ''],
            ['HFR-001', 'الدريس',         '2500.00', '2026-04-01', 'EXP-0001', 'شحن محطة الدريس',                     'أبو فواز'],
            ['RYD-001', 'مساند تمارا',    '3375.92', '2026-04-01', 'EXP-0002', 'سداد علي تمارا دفعات سداد',           'سعود السبيعي'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header row style
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Instruction row (row 2 = first data row area) notes as comment
        $styles = [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1e3a5f']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];

        // Color example rows alternately
        foreach ([2, 4, 6] as $row) {
            $styles[$row] = ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'f0f9ff']]];
        }
        foreach ([3, 5, 7] as $row) {
            $styles[$row] = ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'fefce8']]];
        }

        return $styles;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // branch_name
            'B' => 24, // type_name
            'C' => 14, // amount
            'D' => 14, // date
            'E' => 20, // reference_number
            'F' => 42, // description
            'G' => 22, // recipient
        ];
    }
}
