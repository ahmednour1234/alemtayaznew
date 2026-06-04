<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RecruitmentStatementTemplateExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles, WithTitle
{
    public function title(): string
    {
        return 'كشف الاستقدام';
    }

    public function headings(): array
    {
        return [
            'رقم العقد',
            'هوية صاحب العمل',
            'الجنسية',
            'المهنة',
            'الفرع',
            'تاريخ بداية العقد',
            'ايراد استقدام',
            'تكاليف الاستقدام مصاريف',
            'مباشرة للعقود الضريبية',
        ];
    }

    public function array(): array
    {
        return [
            ['13155973', '1106644873', 'اثيوبيا', 'عاملة منزلية', 'امتياز',  '2026-05-30', 3418.85, 2250, 16.5],
            ['13155877', '1037210349', 'كينيا',   'عاملة منزلية', 'امتياز',  '2026-05-30', 5974.83, 3750, 112.05],
            ['13159575', '1083989036', 'بنجلاديش','عاملة منزلية', 'امتياز',  '2026-05-31', 7604.32, 4500, 53],
            ['13155929', '1109992998', 'اثيوبيا', 'عاملة منزلية', 'انجاز',   '2026-06-01', 4023.75, 2250, 16.5],
            ['13166376', '1080010091', 'كينيا',   'عاملة منزلية', 'انجاز',   '2026-06-01', 6075.05, 3750, 112.05],
            ['13175191', '1028158630', 'اثيوبيا', 'عاملة منزلية', 'متميز',   '2026-06-02', 4029.5,  2250, 16.5],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getRowDimension(1)->setRowHeight(30);

        $styles = [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1e3a5f']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];

        // Alternate row colors
        foreach ([2, 4, 6] as $r) {
            $styles[$r] = ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'f0fdf4']]];
        }
        foreach ([3, 5, 7] as $r) {
            $styles[$r] = ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'fffbeb']]];
        }

        return $styles;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14, // رقم العقد
            'B' => 18, // هوية صاحب العمل
            'C' => 14, // الجنسية
            'D' => 18, // المهنة
            'E' => 14, // الفرع
            'F' => 22, // تاريخ بداية العقد
            'G' => 18, // ايراد استقدام
            'H' => 24, // تكاليف الاستقدام مصاريف
            'I' => 26, // مباشرة للعقود الضريبية
        ];
    }
}
