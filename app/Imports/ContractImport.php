<?php

namespace App\Imports;

use App\Models\Agent;
use App\Models\Admin;
use App\Models\Airport;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Nationality;
use App\Models\RecruitmentContract;
use App\Models\Worker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ContractImport implements ToCollection, WithCalculatedFormulas
{
    private int $imported = 0;
    private int $updated  = 0;
    private array $errors = [];

    /**
     * Logical field => list of keywords to look for in the header cell.
     * The first column whose header contains any of these keywords wins.
     * Order of columns in the sheet does NOT matter — only the header text.
     */
    private const FIELD_KEYWORDS = [
        'client'             => ['اسم العميل', 'العميل', 'client'],
        'branch_code'        => ['كود الفرع', 'رمز الفرع', 'الفرع', 'branch'],
        'visa_type'          => ['نوع التأشيرة', 'نوع التاشيرة', 'visa_type', 'visa type'],
        'visa_number'        => ['رقم التأشيرة', 'رقم التاشيرة', 'visa_number', 'visa number'],
        'musaned'            => ['مساند', 'musaned'],
        'request_date'       => ['تاريخ الطلب', 'request'],
        'worker'             => ['اسم العاملة', 'العاملة', 'worker'],
        'agent'              => ['اسم الوكيل', 'الوكيل', 'agent'],
        'payment_status'     => ['حالة الدفع', 'الدفع', 'payment'],
        'total_cost'         => ['اجمالي التكلفة', 'إجمالي التكلفة', 'التكلفة', 'cost'],
        'notes'              => ['ملاحظات', 'notes'],
        'arrival_date'       => ['تاريخ الوصول', 'arrival_date', 'arrival date'],
        'status'             => ['الحالة', 'status'],
        'nationality'        => ['الجنسية', 'جنسية', 'nationality'],
        'trial_end'          => ['انتهاء التدريب', 'التدريب', 'trial'],
        'contract_end'       => ['انتهاء الضمان', 'الضمان', 'contract_end', 'contract end'],
        'passport_number'    => ['جواز', 'passport'],
        'client_national_id' => ['هوية العميل', 'هوية', 'اقامة', 'إقامة', 'national_id', 'national id'],
        'arrival_airport'    => ['محطة الوصول', 'المطار', 'مطار', 'airport'],
    ];

    /** @var array<string,int> resolved logical field => column index */
    private array $map = [];

    public function collection(Collection $rows)
    {
        $adminId = Auth::guard('admin')->id() ?? Admin::first()?->id;

        // Row 1 (index 0) is the header row, row 2 (index 1) is the description.
        // Build the field => column-index map from the header row, then read data from row 3.
        $headerRow = $rows->first();
        if ($headerRow === null) return;
        $this->buildMap(array_values($headerRow->toArray()));

        foreach ($rows as $i => $row) {
            if ($i < 2) continue;       // skip header (0) and description (1) rows
            $rowNum = $i + 1;            // 1-based Excel row number for error messages

            $v = array_values($row->toArray());
            $get = fn (string $field) => isset($this->map[$field]) ? ($v[$this->map[$field]] ?? null) : null;

            $clientName       = trim((string) $get('client'));
            $branchCode       = trim((string) $get('branch_code'));
            $visaTypeRaw      = trim((string) $get('visa_type'));
            $visaNumber       = $get('visa_number');
            $musaned          = $get('musaned');
            $reqDate          = $get('request_date');
            $workerName       = trim((string) $get('worker'));
            $agentName        = trim((string) $get('agent'));
            $payRaw           = trim((string) $get('payment_status'));
            $totalCost        = $get('total_cost');
            $notes            = $get('notes');
            $arrivalRaw       = $get('arrival_date');
            $statusRaw        = $get('status');
            $nationalityName  = trim((string) $get('nationality'));
            $trialEndRaw      = $get('trial_end');
            $contractEndRaw   = $get('contract_end');
            $passportNumber   = trim((string) $get('passport_number')) ?: null;
            $clientNationalId = trim((string) $get('client_national_id')) ?: null;
            $airportName      = trim((string) $get('arrival_airport')) ?: null;

            // Skip completely empty rows
            if ($branchCode === '' && $clientName === '') continue;

            // Resolve branch (required)
            $branch = Branch::where('code', $branchCode)->first();
            if (! $branch) {
                $this->errors[] = "الصف {$rowNum}: رمز الفرع '{$branchCode}' غير موجود";
                continue;
            }

            // Resolve nationality with multi-level fuzzy matching
            $nationality = null;
            if ($nationalityName !== '') {
                // 1) Exact match
                $nationality = Nationality::where('name', $nationalityName)->first();

                if (! $nationality) {
                    // Helper: normalise Arabic text for comparison
                    // - unify hamza variants (أإآٱ → ا) and alef-maqsura (ى → ي)
                    // - unify teh-marbuta (ة → ه)
                    // - strip tatweel, diacritics, and all spaces
                    // - strip common suffixes (ية / يا / ي / ه) to get the root
                    $normalise = function (string $s): string {
                        $s = trim($s);
                        $s = preg_replace('/[أإآٱ]/u', 'ا', $s);
                        $s = str_replace(['ى', 'ة', 'ـ'], ['ي', 'ه', ''], $s);
                        $s = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $s); // tashkeel
                        $s = preg_replace('/\s+/u', '', $s);                          // drop spaces
                        $s = preg_replace('/(يه|يا|ي|ه)$/u', '', $s);                 // strip suffixes
                        return $s;
                    };

                    $normInput = $normalise($nationalityName);

                    // 2) Hamza + suffix-stripped match (exact, then containment both ways)
                    $allNats = Nationality::all();
                    $nationality = $allNats->first(
                        fn ($n) => $normalise($n->name) === $normInput
                    );
                    if (! $nationality && $normInput !== '') {
                        $nationality = $allNats->first(function ($n) use ($normalise, $normInput) {
                            $nn = $normalise($n->name);
                            return $nn !== '' && (str_contains($nn, $normInput) || str_contains($normInput, $nn));
                        });
                    }
                }

                if (! $nationality) {
                    // 3) similar_text fuzzy match — pick the closest name above 70% similarity
                    $allNats = $allNats ?? Nationality::all();
                    $best = null;
                    $bestScore = 0;
                    foreach ($allNats as $n) {
                        similar_text($nationalityName, $n->name, $pct);
                        if ($pct > $bestScore) {
                            $bestScore = $pct;
                            $best = $n;
                        }
                    }
                    if ($bestScore >= 60) {
                        $nationality = $best;
                    }
                }
            }

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
                // Update national_id if provided, missing on this client, and not taken by another client.
                // Reading the encrypted national_id can throw DecryptException if the stored value
                // was encrypted with a different APP_KEY or is corrupted — treat that as "missing".
                $existingNationalId = null;
                try {
                    $existingNationalId = $client->national_id;
                } catch (\Throwable) {
                    $existingNationalId = null;
                }
                if ($clientNationalId && ! $existingNationalId) {
                    try {
                        $takenByOther = Client::wherePii('national_id', $clientNationalId)
                            ->where('id', '!=', $client->id)
                            ->exists();
                        if (! $takenByOther) {
                            $client->update(['national_id' => $clientNationalId]);
                        }
                    } catch (\Throwable $e) {
                        $this->errors[] = "الصف {$rowNum}: تعذّر تحديث رقم هوية العميل";
                    }
                }
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
                    $worker = Worker::wherePii('passport_number', $passportNumber)->first();
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

            // Resolve arrival airport — use column S value, default to مطار الملك خالد الدولي
            $arrivalAirport = null;
            if ($airportName) {
                $arrivalAirport = Airport::where('name', $airportName)
                    ->orWhere('code', strtoupper($airportName))
                    ->first();
            }
            if (! $arrivalAirport) {
                $arrivalAirport = Airport::where('name', 'like', '%خالد%')
                    ->orWhere('code', 'RUH')
                    ->first();
            }

            $visaType = in_array($visaTypeRaw, ['domestic', 'rehabilitation']) ? $visaTypeRaw : 'domestic';

            // Payment status — supports English and Arabic values
            $payStatus = match (true) {
                in_array($payRaw, ['full', 'paid'])                                                     => 'full',
                str_contains($payRaw, 'مدفوع') || str_contains($payRaw, 'مكتمل') || str_contains($payRaw, 'كامل') => 'full',
                in_array($payRaw, ['partial'])                                                          => 'partial',
                str_contains($payRaw, 'جزئي')                                                         => 'partial',
                default                                                                                 => 'pending',
            };

            // Arrival date — handle Excel serial numbers
            $arrivalDate = null;
            if (! empty($arrivalRaw)) {
                try {
                    if (is_numeric($arrivalRaw)) {
                        $arrivalDate = \Carbon\Carbon::instance(ExcelDate::excelToDateTimeObject((float) $arrivalRaw))->format('Y-m-d');
                    } else {
                        $arrivalDate = \Carbon\Carbon::parse($arrivalRaw)->format('Y-m-d');
                    }
                } catch (\Throwable) {}
            }

            // Trial end date — handle Excel serial numbers
            $trialEndDate = null;
            if (! empty($trialEndRaw)) {
                try {
                    if (is_numeric($trialEndRaw)) {
                        $trialEndDate = \Carbon\Carbon::instance(ExcelDate::excelToDateTimeObject((float) $trialEndRaw))->format('Y-m-d');
                    } else {
                        $trialEndDate = \Carbon\Carbon::parse($trialEndRaw)->format('Y-m-d');
                    }
                } catch (\Throwable) {}
            }
            if (! $trialEndDate && $arrivalDate) {
                $trialEndDate = \Carbon\Carbon::parse($arrivalDate)->addMonths(3)->format('Y-m-d');
            }

            // Contract end date — handle Excel serial numbers
            $contractEndDate = null;
            if (! empty($contractEndRaw)) {
                try {
                    if (is_numeric($contractEndRaw)) {
                        $contractEndDate = \Carbon\Carbon::instance(ExcelDate::excelToDateTimeObject((float) $contractEndRaw))->format('Y-m-d');
                    } else {
                        $contractEndDate = \Carbon\Carbon::parse($contractEndRaw)->format('Y-m-d');
                    }
                } catch (\Throwable) {}
            }
            if (! $contractEndDate && $arrivalDate) {
                $contractEndDate = \Carbon\Carbon::parse($arrivalDate)->addYears(2)->format('Y-m-d');
            }

            // Status 1-15
            $statusVal = $statusRaw !== null ? (int) $statusRaw : 1;
            if (! array_key_exists($statusVal, RecruitmentContract::statuses())) {
                $statusVal = 1;
            }

            // Request date fallback — handle Excel serial numbers
            $requestDate = null;
            if (! empty($reqDate)) {
                try {
                    if (is_numeric($reqDate)) {
                        $requestDate = \Carbon\Carbon::instance(ExcelDate::excelToDateTimeObject((float) $reqDate))->format('Y-m-d');
                    } else {
                        $requestDate = \Carbon\Carbon::parse($reqDate)->format('Y-m-d');
                    }
                } catch (\Throwable) {}
            }
            $requestDate ??= now()->format('Y-m-d');

            // ── هل العقد موجود مسبقاً؟ ───────────────────────────────────────
            // نبحث بالتأشيرة ثم مساند ثم جواز العاملة. لو وُجد نُحدّثه بدل
            // إنشاء نسخة مكررة، فالاستيراد المتكرر لا ينتج عقوداً مضاعفة.
            $existing = $this->findExisting($visaNumber ?: null, $musaned ?: null, $worker?->id);

            $payload = [
                'branch_id'             => $branch->id,
                'client_id'             => $client?->id,
                'worker_id'             => $worker?->id,
                'agent_id'              => $agent?->id,
                'visa_type'             => $visaType,
                'visa_number'           => $visaNumber ?: null,
                'musaned_number'        => $musaned    ?: null,
                'request_date'          => $requestDate,
                'payment_status'        => $payStatus,
                'total_cost'            => is_numeric($totalCost) ? $totalCost : null,
                'notes'                 => $notes ?: null,
                'origin_nationality_id' => $nationality?->id,
                'arrival_airport_id'    => $arrivalAirport?->id,
                'arrival_date'          => $arrivalDate,
                'trial_end_date'        => $trialEndDate,
                'contract_end_date'     => $contractEndDate,
                'current_status'        => $statusVal,
                'active'                => true,
            ];

            if ($existing) {
                // تحديث: لا نكتب فوق قيمة موجودة بقيمة فارغة من الملف،
                // ولا نُرجع العقد لقسم سابق — القسم يتقدّم فقط.
                $update = array_filter(
                    $payload,
                    fn ($val) => $val !== null && $val !== ''
                );

                // الحالة تتقدّم فقط — لا تتراجع لمرحلة أقدم
                if ($statusVal < $existing->current_status) {
                    unset($update['current_status']);
                }

                $existing->update($update);
                $this->updated++;
            } else {
                RecruitmentContract::create($payload + [
                    'contract_number'    => RecruitmentContract::generateNumber(),
                    'admin_id'           => $adminId,
                    'current_department' => $payStatus === 'full' ? 'coordination' : 'customer_service',
                ]);
                $this->imported++;
            }

            // Mark worker as assigned and link to client
            if ($worker) {
                $worker->update([
                    'status'    => 'assigned',
                    'client_id' => $client?->id,
                ]);
            }
        }
    }

    /**
     * Resolve each logical field to a column index by matching the header text
     * against FIELD_KEYWORDS. Column order is irrelevant — only the header name matters.
     */
    private function buildMap(array $headers): void
    {
        // Normalise each header cell once (lowercase + unify hamza/teh-marbuta + trim)
        $norm = function (string $s): string {
            $s = trim(mb_strtolower($s));
            $s = preg_replace('/[أإآٱ]/u', 'ا', $s);
            $s = str_replace(['ة', 'ـ', '*'], ['ه', '', ''], $s);
            return trim(preg_replace('/\s+/u', ' ', $s));
        };

        $normHeaders = [];
        foreach ($headers as $idx => $h) {
            $normHeaders[$idx] = $norm((string) $h);
        }

        foreach (self::FIELD_KEYWORDS as $field => $keywords) {
            foreach ($keywords as $kw) {
                $nkw = $norm($kw);
                foreach ($normHeaders as $idx => $nh) {
                    if ($nh !== '' && str_contains($nh, $nkw)) {
                        $this->map[$field] = $idx;
                        continue 3; // field resolved — move to next field
                    }
                }
            }
        }

        if (! isset($this->map['branch_code'])) {
            $this->errors[] = "تعذّر العثور على عمود 'كود الفرع' في الملف — تأكد من أسماء الأعمدة";
        }
    }

    /**
     * يبحث عن عقد قائم لتحديثه بدل إنشاء نسخة مكررة.
     *
     * الأولوية: رقم التأشيرة ← رقم مساند ← عقد العاملة النشط.
     * الأولان معرّفان فريدان للعقد، والثالث احتياطي حين يخلو الملف منهما.
     */
    private function findExisting(?string $visaNumber, ?string $musaned, ?int $workerId): ?RecruitmentContract
    {
        if ($visaNumber) {
            $found = RecruitmentContract::where('visa_number', $visaNumber)->first();
            if ($found) return $found;
        }

        if ($musaned) {
            $found = RecruitmentContract::where('musaned_number', $musaned)->first();
            if ($found) return $found;
        }

        // احتياطي: أحدث عقد لنفس العاملة (العاملة تُطابَق بالجواز أعلاه)
        if ($workerId) {
            return RecruitmentContract::where('worker_id', $workerId)->latest('id')->first();
        }

        return null;
    }

    public function importedCount(): int { return $this->imported; }
    public function updatedCount(): int  { return $this->updated; }
    public function importErrors(): array { return $this->errors; }
}
