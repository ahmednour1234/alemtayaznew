<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ContractTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            // سطر الشرح والقيم المقبولة
            [
                'نص حر — الاسم الكامل للعميل',
                'كود الفرع الموجود بالنظام',
                'domestic = عمالة منزلية | rehabilitation = تأهيل شامل',
                'رقم التأشيرة (اختياري)',
                'رقم عقد مساند (اختياري)',
                'صيغة التاريخ: YYYY-MM-DD',
                'اسم العاملة (اختياري)',
                'اسم الوكيل (اختياري)',
                'pending = معلق | partial = جزئي | full = كامل',
                'رقم (اختياري) — مثال: 5000',
                'ملاحظات نصية (اختياري)',
                'تاريخ وصول العاملة — YYYY-MM-DD (اختياري)',
                '1=جديد | 2=موافقة سفارة | 3=انتظار مكتب | 4=قبول مكتب | 5=انتظار ابروف | 6=قبول العقد | 7=إرسال تأشيرة | 8=تم التأشير | 9=إلغاء | 10=تصريح سفر | 11=انتظار حجز | 12=معاد الوصول | 13=تم الاستلام | 14=رجيع | 15=هروب',
                'جنسية العاملة كما هي في النظام — مثال: إثيوبية | فلبينية | إندونيسية (اختياري)',
                'محسوب تلقائياً: تاريخ الوصول + 3 أشهر (اختياري)',
                'محسوب تلقائياً: تاريخ الوصول + سنتين (اختياري)',
            ],
            // سطر المثال
            [
                'أحمد محمد علي',
                'BRANCH-001',
                'domestic',
                'V-123456',
                'MS-2026-001',
                '2026-01-15',
                'فاطمة خالد',
                'محمد الوكيل',
                'pending',
                '5500',
                'عميل مميز — يرجى المتابعة',
                '2026-03-01',
                '1',
                'إثيوبية',
                '2026-06-01',
                '2028-03-01',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'اسم العميل *',
            'كود الفرع *',
            'نوع التأشيرة *',
            'رقم التأشيرة',
            'رقم مساند',
            'تاريخ الطلب * (YYYY-MM-DD)',
            'اسم العاملة',
            'اسم الوكيل',
            'حالة الدفع *',
            'إجمالي التكلفة (ر.س)',
            'ملاحظات',
            'تاريخ الوصول (YYYY-MM-DD)',
            'الحالة (1–15)',
            'جنسية العاملة',
            'انتهاء التدريب (YYYY-MM-DD)',
            'انتهاء الضمان (YYYY-MM-DD)',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, 'B' => 20, 'C' => 36, 'D' => 18,
            'E' => 18, 'F' => 26, 'G' => 20, 'H' => 20,
            'I' => 32, 'J' => 22, 'K' => 28, 'L' => 26, 'M' => 90, 'N' => 26,
            'O' => 28, 'P' => 28,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);
        // سطر الشرح — خلفية رمادية
        $sheet->getStyle('A2:P2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '475569']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F1F5F9']],
            'alignment' => ['wrapText' => true],
        ]);
        // سطر المثال — خلفية صفراء فاتحة
        $sheet->getStyle('A3:P3')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '1E3A5F']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FEF9C3']],
        ]);
        // تمييز عمودي التواريخ المحسوبة
        $sheet->getStyle('O1:P1')->applyFromArray([
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '065F46']],
        ]);
        $sheet->getStyle('O3:P3')->applyFromArray([
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D1FAE5']],
            'font' => ['color' => ['rgb' => '065F46'], 'italic' => true],
        ]);

        // تثبيت ارتفاع صفوف الشرح
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(40);

        return [];
    }
}
