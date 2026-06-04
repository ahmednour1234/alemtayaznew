<?php

namespace App\Imports;

use App\Models\Agent;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Nationality;
use App\Models\RecruitmentContract;
use App\Models\Worker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ContractImport implements ToCollection, WithStartRow
{
    private int $imported = 0;
    private array $errors = [];

    /**
     * Skip row 1 (headings) and row 2 (description) — data starts at row 3.
     */
    public function startRow(): int { return 3; }

    public function collection(Collection $rows)
    {
        $adminId = Auth::guard('admin')->id() ?? Admin::first()?->id;

        foreach ($rows as $i => $row) {
            $rowNum = $i + 3; // row 3 is the first data row

            // All columns read by numeric index — avoids Arabic slug issues
            // A=0 client | B=1 branch_code | C=2 visa_type | D=3 visa_number
            // E=4 musaned | F=5 request_date | G=6 worker | H=7 agent
            // I=8 payment_status | J=9 total_cost | K=10 notes
            // L=11 arrival_date | M=12 status | N=13 nationality
            // O=14 trial_end | P=15 contract_end | Q=16 passport_number
            $v = array_values($row->toArray());

            $clientName      = trim($v[0]  ?? '');
            $branchCode      = trim($v[1]  ?? '');
            $visaTypeRaw     = trim($v[2]  ?? '');
            $visaNumber      = $v[3]  ?? null;
            $musaned         = $v[4]  ?? null;
            $reqDate         = $v[5]  ?? null;
            $workerName      = trim($v[6]  ?? '');
            $agentName       = trim($v[7]  ?? '');
            $payRaw          = trim($v[8]  ?? '');
            $totalCost       = $v[9]  ?? null;
            $notes           = $v[10] ?? null;
            $arrivalRaw      = $v[11] ?? null;
            $statusRaw       = $v[12] ?? null;
            $nationalityName = trim($v[13] ?? '');
            $trialEndRaw     = $v[14] ?? null;
            $contractEndRaw  = $v[15] ?? null;
            $passportNumber  = trim($v[16] ?? '') ?: null;   // Q — رقم جواز العاملة (اختياري)

            // Skip completely empty rows
            if ($branchCode === '' && $clientName === '') continue;

            // Resolve branch (required)
            $branch = Branch::where('code', $branchCode)->first();
            if (! $branch) {
                $this->errors[] = "الصف {$rowNum}: رمز الفرع '{$branchCode}' غير موجود";
                continue;
            }

            // Resolve nationality (optional)
            $nationality = $nationalityName !== ''
                ? Nationality::where('name', $nationalityName)->first()
                : null;

            // Auto-create client if not found (classification = confirmed)
            $client = null;
            if ($clientName !== '') {
                $client = Client::firstOrCreate(
                    ['name' => $clientName, 'branch_id' => $branch->id],
                    [
                        'classification' => 'confirmed',
                        'admin_id'       => $adminId,
                        'active'         => true,
                    ]
                );
            }

            // Auto-create agent if not found
            $agent = null;
            if ($agentName !== '') {
                $agent = Agent::firstOrCreate(
                    ['name' => $agentName],
                    ['active' => true]
                );
            }

            // Auto-create worker if not found; update nationality if missing
            $worker = null;
            if ($workerName !== '' || $passportNumber !== null) {
                // Match by passport number first (most reliable), then by name
                if ($passportNumber !== null) {
                    $worker = Worker::where('passport_number', $passportNumber)->first();
                }
                if (! $worker && $workerName !== '') {
                    $worker = Worker::where('name', $workerName)->first();
                }
                if (! $worker) {
                    $worker = Worker::create([
                        'name'            => $workerName ?: ('عاملة-' . ($passportNumber ?? uniqid())),
                        'passport_number' => $passportNumber,
                        'nationality_id'  => $nationality?->id,
                        'branch_id'       => $branch->id,
                        'admin_id'        => $adminId,
                        'active'          => true,
                    ]);
                } else {
                    // Update missing fields on existing worker
                    $updates = [];
                    if ($passportNumber && ! $worker->passport_number) {
                        $updates['passport_number'] = $passportNumber;
                    }
                    if ($nationality && ! $worker->nationality_id) {
                        $updates['nationality_id'] = $nationality->id;
                    }
                    if ($updates) {
                        $worker->update($updates);
                    }
                }
            }

            $visaType  = in_array($visaTypeRaw, ['domestic', 'rehabilitation']) ? $visaTypeRaw : 'domestic';
            $payStatus = in_array($payRaw, ['pending', 'partial', 'full']) ? $payRaw : 'pending';

            // Arrival date
            $arrivalDate = null;
            if (! empty($arrivalRaw)) {
                try { $arrivalDate = \Carbon\Carbon::parse($arrivalRaw)->format('Y-m-d'); } catch (\Throwable) {}
            }

            // Trial end date: from column O if provided, else arrival_date + 3 months
            $trialEndDate = null;
            if (! empty($trialEndRaw)) {
                try { $trialEndDate = \Carbon\Carbon::parse($trialEndRaw)->format('Y-m-d'); } catch (\Throwable) {}
            }
            if (! $trialEndDate && $arrivalDate) {
                $trialEndDate = \Carbon\Carbon::parse($arrivalDate)->addMonths(3)->format('Y-m-d');
            }

            // Contract end date: from column P if provided, else arrival_date + 2 years
            $contractEndDate = null;
            if (! empty($contractEndRaw)) {
                try { $contractEndDate = \Carbon\Carbon::parse($contractEndRaw)->format('Y-m-d'); } catch (\Throwable) {}
            }
            if (! $contractEndDate && $arrivalDate) {
                $contractEndDate = \Carbon\Carbon::parse($arrivalDate)->addYears(2)->format('Y-m-d');
            }

            // Status 1-15
            $statusVal = $statusRaw !== null ? (int) $statusRaw : 1;
            if (! array_key_exists($statusVal, RecruitmentContract::statuses())) {
                $statusVal = 1;
            }

            // Request date fallback
            $requestDate = null;
            if (! empty($reqDate)) {
                try { $requestDate = \Carbon\Carbon::parse($reqDate)->format('Y-m-d'); } catch (\Throwable) {}
            }
            $requestDate ??= now()->format('Y-m-d');

            RecruitmentContract::create([
                'contract_number'    => RecruitmentContract::generateNumber(),
                'branch_id'          => $branch->id,
                'admin_id'           => $adminId,
                'client_id'          => $client?->id,
                'worker_id'          => $worker?->id,
                'agent_id'           => $agent?->id,
                'visa_type'          => $visaType,
                'visa_number'        => $visaNumber ?: null,
                'musaned_number'     => $musaned    ?: null,
                'request_date'       => $requestDate,
                'payment_status'     => $payStatus,
                'total_cost'         => is_numeric($totalCost) ? $totalCost : null,
                'notes'              => $notes ?: null,
                'origin_nationality_id' => $nationality?->id,
                'arrival_date'       => $arrivalDate,
                'trial_end_date'     => $trialEndDate,
                'contract_end_date'  => $contractEndDate,
                'current_department' => $payStatus === 'full' ? 'coordination' : 'customer_service',
                'current_status'     => $statusVal,
                'active'             => true,
            ]);

            $this->imported++;

            // Mark worker as assigned and link to client
            if ($worker) {
                $worker->update([
                    'status'    => 'assigned',
                    'client_id' => $client?->id,
                ]);
            }
        }
    }

    public function importedCount(): int { return $this->imported; }
    public function importErrors(): array { return $this->errors; }
}
