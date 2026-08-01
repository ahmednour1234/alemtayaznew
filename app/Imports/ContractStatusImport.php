<?php

namespace App\Imports;

use App\Models\RecruitmentContract;
use App\Services\RecruitmentContractService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * تحديث حالات العقود بالجملة من ملف Excel.
 *
 * يبحث عن العقد برقم التأشيرة، ثم يمرّر التحديث عبر
 * RecruitmentContractService::updateStatus حتى تُسجَّل نفس السجلات
 * (سجل الحالات، سجل النشاط، الإشعارات) تمامًا كالتحديث اليدوي.
 */
class ContractStatusImport implements ToCollection, WithCalculatedFormulas
{
    private int $updated = 0;
    private int $skipped = 0;
    private array $errors = [];

    /** الحقل المنطقي => الكلمات المفتاحية في عنوان العمود */
    private const FIELD_KEYWORDS = [
        'visa_number'  => ['رقم التأشيرة', 'رقم التاشيرة', 'التأشيرة', 'التاشيرة', 'visa'],
        'status'       => ['الحالة', 'رقم الحالة', 'status'],
        'status_date'  => ['تاريخ الحالة', 'التاريخ', 'date'],
        'wa_message'   => ['واتساب', 'رسالة', 'whatsapp', 'message'],
    ];

    /** @var array<string,int> */
    private array $map = [];

    public function __construct(
        private readonly RecruitmentContractService $service,
        /** تخطّي الصف إذا كان العقد على نفس الحالة already */
        private readonly bool $skipUnchanged = true,
    ) {}

    public function collection(Collection $rows)
    {
        $headerRow = $rows->first();
        if ($headerRow === null) {
            return;
        }
        $this->buildMap(array_values($headerRow->toArray()));

        // عمودا التأشيرة والحالة إلزاميان — بدونهما لا يمكن المتابعة
        if (! isset($this->map['visa_number']) || ! isset($this->map['status'])) {
            return;
        }

        $validStatuses = array_keys(RecruitmentContract::statuses());

        foreach ($rows as $i => $row) {
            if ($i < 2) continue;   // تخطّي صف العناوين (0) وصف الشرح (1)
            $rowNum = $i + 1;       // رقم الصف في Excel لرسائل الأخطاء

            $v   = array_values($row->toArray());
            $get = fn (string $f) => isset($this->map[$f]) ? ($v[$this->map[$f]] ?? null) : null;

            $visaNumber = trim((string) $get('visa_number'));
            $statusRaw  = trim((string) $get('status'));
            $dateRaw    = $get('status_date');
            $waMessage  = trim((string) $get('wa_message')) ?: null;

            // تخطّي الصفوف الفارغة تمامًا
            if ($visaNumber === '' && $statusRaw === '') {
                continue;
            }

            if ($visaNumber === '') {
                $this->errors[] = "الصف {$rowNum}: رقم التأشيرة مفقود";
                continue;
            }

            if ($statusRaw === '') {
                $this->errors[] = "الصف {$rowNum}: رقم الحالة مفقود للتأشيرة '{$visaNumber}'";
                continue;
            }

            // التحقق من رقم الحالة
            if (! is_numeric($statusRaw) || ! in_array((int) $statusRaw, $validStatuses, true)) {
                $this->errors[] = "الصف {$rowNum}: رقم الحالة '{$statusRaw}' غير صالح (المسموح 1–15)";
                continue;
            }
            $status = (int) $statusRaw;

            // البحث عن العقد برقم التأشيرة
            $matches = RecruitmentContract::where('visa_number', $visaNumber)->get();

            if ($matches->isEmpty()) {
                $this->errors[] = "الصف {$rowNum}: لا يوجد عقد برقم التأشيرة '{$visaNumber}'";
                continue;
            }

            // رقم تأشيرة مكرر — لا نُحدّث عشوائيًا، نُبلغ المستخدم
            if ($matches->count() > 1) {
                $nums = $matches->pluck('contract_number')->implode('، ');
                $this->errors[] = "الصف {$rowNum}: رقم التأشيرة '{$visaNumber}' مرتبط بأكثر من عقد ({$nums}) — لم يتم التحديث";
                continue;
            }

            $contract = $matches->first();

            // تخطّي العقود التي هي بالفعل على نفس الحالة
            if ($this->skipUnchanged && (int) $contract->current_status === $status) {
                $this->skipped++;
                continue;
            }

            $statusDate = $this->parseDate($dateRaw, $rowNum);
            if ($statusDate === false) {
                continue; // خطأ في التاريخ — تم تسجيله بالفعل
            }

            $this->service->updateStatus(
                $contract,
                $status,
                $statusDate ?? now()->format('Y-m-d'),
                $waMessage
            );

            $this->updated++;
        }
    }

    /**
     * يقبل تاريخ Excel الرقمي أو نصًا بصيغة YYYY-MM-DD.
     * يُعيد null إذا كان فارغًا، أو false عند وجود خطأ.
     */
    private function parseDate(mixed $raw, int $rowNum): string|null|false
    {
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }

        // تاريخ Excel المُسلسل (رقم)
        if (is_numeric($raw)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $raw))->format('Y-m-d');
            } catch (\Throwable) {
                $this->errors[] = "الصف {$rowNum}: تاريخ الحالة غير صالح";
                return false;
            }
        }

        try {
            return Carbon::parse(trim((string) $raw))->format('Y-m-d');
        } catch (\Throwable) {
            $this->errors[] = "الصف {$rowNum}: تاريخ الحالة '{$raw}' غير صالح — استخدم صيغة YYYY-MM-DD";
            return false;
        }
    }

    /** ربط كل حقل منطقي برقم العمود اعتمادًا على نص العنوان — ترتيب الأعمدة غير مهم. */
    private function buildMap(array $headers): void
    {
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
                    if ($nh !== '' && str_contains($nh, $nkw) && ! in_array($idx, $this->map, true)) {
                        $this->map[$field] = $idx;
                        continue 3;
                    }
                }
            }
        }

        if (! isset($this->map['visa_number'])) {
            $this->errors[] = "تعذّر العثور على عمود 'رقم التأشيرة' في الملف — تأكد من أسماء الأعمدة";
        }
        if (! isset($this->map['status'])) {
            $this->errors[] = "تعذّر العثور على عمود 'الحالة' في الملف — تأكد من أسماء الأعمدة";
        }
    }

    public function updatedCount(): int { return $this->updated; }
    public function skippedCount(): int { return $this->skipped; }
    public function importErrors(): array { return $this->errors; }
}
