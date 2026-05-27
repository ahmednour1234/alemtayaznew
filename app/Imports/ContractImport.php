<?php

namespace App\Imports;

use App\Models\Agent;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\Client;
use App\Models\RecruitmentContract;
use App\Models\Worker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ContractImport implements ToCollection, WithHeadingRow, WithValidation
{
    private int $imported = 0;
    private array $errors = [];

    public function collection(Collection $rows)
    {
        $adminId = Auth::guard('admin')->id() ?? Admin::first()?->id;

        foreach ($rows as $i => $row) {
            $rowNum = $i + 2; // +2: 1 header + 1-based

            // Resolve branch
            $branch = Branch::where('code', trim($row['branch_code'] ?? ''))->first();
            if (! $branch) {
                $this->errors[] = "الصف {$rowNum}: رمز الفرع '{$row['branch_code']}' غير موجود";
                continue;
            }

            // Resolve or skip client
            $client = Client::where('name', trim($row['client_name'] ?? ''))->first();

            // Resolve optional worker & agent
            $worker = Worker::where('name', trim($row['worker_name'] ?? ''))->first();
            $agent  = Agent::where('name',  trim($row['agent_name']  ?? ''))->first();

            $visaType = in_array($row['visa_type'] ?? '', ['domestic', 'rehabilitation'])
                ? $row['visa_type'] : 'domestic';

            $payStatus = in_array($row['payment_status'] ?? '', ['pending', 'partial', 'full'])
                ? $row['payment_status'] : 'pending';

            RecruitmentContract::create([
                'contract_number'    => RecruitmentContract::generateNumber(),
                'branch_id'          => $branch->id,
                'admin_id'           => $adminId,
                'client_id'          => $client?->id,
                'worker_id'          => $worker?->id,
                'agent_id'           => $agent?->id,
                'visa_type'          => $visaType,
                'visa_number'        => $row['visa_number']    ?? null,
                'musaned_number'     => $row['musaned_number'] ?? null,
                'request_date'       => $row['request_date']  ?? now()->format('Y-m-d'),
                'payment_status'     => $payStatus,
                'total_cost'         => is_numeric($row['total_cost'] ?? '') ? $row['total_cost'] : null,
                'notes'              => $row['notes'] ?? null,
                'current_department' => 'customer_service',
                'current_status'     => 1,
                'active'             => true,
            ]);

            $this->imported++;
        }
    }

    public function rules(): array
    {
        return [
            'branch_code'  => ['required'],
            'request_date' => ['required', 'date'],
        ];
    }

    public function importedCount(): int { return $this->imported; }
    public function importErrors(): array { return $this->errors; }
}
