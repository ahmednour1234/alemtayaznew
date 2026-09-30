<?php

namespace App\Exports;

use App\Models\Admin;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * تصدير مستخدمي لوحة الإدارة.
 *
 * بلا كلمات المرور: النظام يخزّنها مجزّأة (hash) في اتجاه واحد، فلا وجود
 * لنصّها الأصلي في قاعدة البيانات أصلاً. من نسي كلمته تُضبط له واحدة جديدة
 * من شاشة المستخدمين.
 */
class AdminsExport implements FromArray, WithHeadings, WithStyles
{
    public function __construct(private readonly bool $activeOnly = false) {}

    public function headings(): array
    {
        return [
            'الرقم',
            'الاسم',
            'البريد الإلكتروني',
            'القسم',
            'الفرع',
            'الأدوار',
            'الحالة',
            'تاريخ الإنشاء',
        ];
    }

    public function array(): array
    {
        $departments = Admin::departments();

        return Admin::with(['branch', 'roles'])
            ->when($this->activeOnly, fn ($q) => $q->where('active', true))
            ->orderBy('name')
            ->get()
            ->map(fn (Admin $a) => [
                $a->id,
                $a->name,
                $a->email,
                $departments[$a->department] ?? ($a->department ?: '—'),
                $a->branch?->name ?? '—',
                $a->roles->pluck('name')->implode('، ') ?: '—',
                $a->active ? 'نشط' : 'موقوف',
                $a->created_at?->format('Y-m-d'),
            ])
            ->all();
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
