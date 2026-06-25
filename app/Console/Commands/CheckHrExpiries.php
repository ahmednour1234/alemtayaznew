<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\EmployeeMedicalInsurance;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckHrExpiries extends Command
{
    protected $signature   = 'hr:check-expiries {--days=30 : عدد الأيام قبل الانتهاء للتنبيه}';
    protected $description = 'تنبيهات قبل تجديد الإقامة، انتهاء فترة التجربة، انتهاء التأمين الطبي، وانتهاء وثائق الموظفين';

    public function __construct(private readonly NotificationService $notifications)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $days  = (int) $this->option('days');
        $today = now()->startOfDay();
        $limit = $today->copy()->addDays($days);
        $sent  = 0;

        // ── تجديد الإقامة (خلال المدة أو منتهية) ──────────────────────────────
        $employees = Employee::where('active', true)
            ->whereNotNull('iqama_expiry_date')
            ->whereDate('iqama_expiry_date', '<=', $limit)
            ->get();

        foreach ($employees as $emp) {
            $d    = $emp->iqama_days_left;
            $when = $emp->iqama_expiry_date->format('Y-m-d');
            $body = $d < 0
                ? "انتهت إقامة الموظف {$emp->name} في {$when} (منذ " . abs($d) . " يوم)."
                : "إقامة الموظف {$emp->name} تنتهي في {$when} (باقي {$d} يوم).";

            $this->notifications->notify(
                'employee_iqama_expiry',
                'تنبيه تجديد إقامة',
                $body,
                route('admin.hr.employees.show', $emp->id),
                $emp->branch_id ? [$emp->branch_id] : []
            );
            $sent++;
        }

        // ── انتهاء فترة التجربة ──────────────────────────────────────────────
        $probation = Employee::where('active', true)
            ->where('status', 'probation')
            ->whereNotNull('probation_end_date')
            ->whereDate('probation_end_date', '<=', $limit)
            ->get();

        foreach ($probation as $emp) {
            $when = $emp->probation_end_date->format('Y-m-d');
            $this->notifications->notify(
                'employee_probation_ending',
                'انتهاء فترة التجربة',
                "تنتهي فترة تجربة الموظف {$emp->name} في {$when} — يلزم اتخاذ قرار التثبيت.",
                route('admin.hr.employees.show', $emp->id),
                $emp->branch_id ? [$emp->branch_id] : []
            );
            $sent++;
        }

        // ── انتهاء التأمين الطبي ──────────────────────────────────────────────
        $insurances = EmployeeMedicalInsurance::with('employee')
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', $limit)
            ->get();

        foreach ($insurances as $ins) {
            $when = $ins->end_date->format('Y-m-d');
            $name = $ins->employee?->name ?? '';
            $this->notifications->notify(
                'employee_insurance_expiry',
                'انتهاء تأمين طبي',
                "تأمين الموظف {$name} لدى {$ins->provider} ينتهي في {$when}.",
                route('admin.hr.insurances.index'),
                $ins->employee?->branch_id ? [$ins->employee->branch_id] : []
            );
            $sent++;
        }

        // ── انتهاء وثائق ─────────────────────────────────────────────────────
        $docs = EmployeeDocument::with('employee')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', $limit)
            ->get();

        foreach ($docs as $doc) {
            $when = $doc->expiry_date->format('Y-m-d');
            $who  = $doc->employee ? ' للموظف ' . $doc->employee->name : '';
            $this->notifications->notify(
                'employee_document_expiry',
                'انتهاء وثيقة',
                "الوثيقة «{$doc->title}»{$who} تنتهي في {$when}.",
                route('admin.hr.documents.index'),
                $doc->branch_id ? [$doc->branch_id] : []
            );
            $sent++;
        }

        $this->info("HR expiry notifications sent: {$sent}");
    }
}
