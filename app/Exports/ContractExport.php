<?php

namespace App\Exports;

use App\Models\RecruitmentContract;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContractExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private readonly array $filters = []) {}

    public function query()
    {
        return RecruitmentContract::query()
            ->with(['client', 'branch', 'worker', 'agent'])
            ->when(! empty($this->filters['branch_id']),      fn($q) => $q->where('branch_id', $this->filters['branch_id']))
            ->when(! empty($this->filters['department']),     fn($q) => $q->where('current_department', $this->filters['department']))
            ->when(! empty($this->filters['status']),         fn($q) => $q->where('current_status', $this->filters['status']))
            ->when(! empty($this->filters['payment_status']), fn($q) => $q->where('payment_status', $this->filters['payment_status']))
            ->latest();
    }

    public function headings(): array
    {
        return [
            'رقم العقد', 'العميل', 'هاتف العميل', 'الفرع', 'العاملة', 'الوكيل',
            'نوع التأشيرة', 'رقم التأشيرة', 'رقم مساند', 'تاريخ مساند',
            'رقم التوثيق الإلكتروني', 'القسم الحالي', 'الحالة الحالية',
            'حالة الدفع', 'إجمالي التكلفة (ر.س)',
            'تاريخ الطلب', 'تاريخ الوصول', 'تاريخ نهاية التجربة', 'تاريخ نهاية العقد',
            'رسالة نصية للعميل', 'تقييم العميل', 'ملاحظات',
        ];
    }

    public function map($c): array
    {
        $statuses  = RecruitmentContract::statuses();
        $depts     = RecruitmentContract::departments();
        $visaTypes = RecruitmentContract::visaTypes();
        $payStats  = RecruitmentContract::paymentStatuses();

        return [
            $c->contract_number,
            $c->client?->name,
            $c->client?->phone,
            $c->branch?->name,
            $c->worker?->name,
            $c->agent?->name,
            $visaTypes[$c->visa_type]               ?? $c->visa_type,
            $c->visa_number,
            $c->musaned_number,
            $c->musaned_date?->format('Y-m-d'),
            $c->e_doc_number,
            $depts[$c->current_department]          ?? $c->current_department,
            $statuses[$c->current_status]['label']  ?? $c->current_status,
            $payStats[$c->payment_status]           ?? $c->payment_status,
            $c->total_cost,
            $c->request_date?->format('Y-m-d'),
            $c->arrival_date?->format('Y-m-d'),
            $c->trial_end_date?->format('Y-m-d'),
            $c->contract_end_date?->format('Y-m-d'),
            $c->client_sms    ? 'نعم' : 'لا',
            $c->client_rating ? 'نعم' : 'لا',
            $c->notes,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
