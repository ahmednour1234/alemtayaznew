<?php

namespace App\Services;

use App\Models\Branch;
use Illuminate\Support\Collection;

/**
 * مُطابِق أسماء الفروع الموحّد لكل عمليات الاستيراد.
 *
 * الهدف: أي اختصار أو نقص في اسم الفرع يجب أن يُطابق الفرع الصحيح بدل
 * إنشاء فرع مكرّر. مثال: «الحفر» و«حفر» و«حفر الباطن» كلها فرع واحد.
 *
 * ترتيب المطابقة من الأقوى إلى الأضعف، ولا يُنشأ فرع جديد إلا بعد فشل الكل.
 */
class BranchResolver
{
    /** أسماء تجارية معروفة → اسم الفرع الحقيقي */
    private const ALIASES = [
        'امتياز'   => 'الرياض',
        'الامتياز' => 'الرياض',
        'متميز'    => 'عرعر',
        'المتميز'  => 'عرعر',
        'انجاز'    => 'حفر الباطن',
        'الانجاز'  => 'حفر الباطن',
        'إنجاز'    => 'حفر الباطن',
        'الإنجاز'  => 'حفر الباطن',
    ];

    /** @var Collection<int, Branch>|null قائمة الفروع محمّلة مرة واحدة */
    private ?Collection $branches = null;

    /** @var array<string, Branch|null> ذاكرة نتائج سابقة */
    private array $cache = [];

    /**
     * يُعيد الفرع المطابق للاسم/الكود، وينشئ فرعاً جديداً فقط كملاذ أخير.
     */
    public function resolve(?string $name, ?string $code = null, bool $createIfMissing = true): ?Branch
    {
        $name = trim((string) $name);
        $code = trim((string) $code);

        if ($name === '' && $code === '') {
            return null;
        }

        $key = $name . '|' . $code . '|' . ($createIfMissing ? '1' : '0');
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        return $this->cache[$key] = $this->lookup($name, $code, $createIfMissing);
    }

    private function lookup(string $name, string $code, bool $createIfMissing): ?Branch
    {
        // الأسماء التجارية المعروفة
        if ($name !== '' && isset(self::ALIASES[$name])) {
            $name = self::ALIASES[$name];
        }

        // 1) كود صريح مطابق تماماً
        if ($code !== '' && $branch = $this->byCode($code)) {
            return $branch;
        }

        // 2) خانة الاسم قد تحمل كوداً (مثل HFR-001)
        if ($name !== '' && $branch = $this->byCode($name)) {
            return $branch;
        }

        // 3) اسم مطابق تماماً
        if ($name !== '') {
            $branch = $this->all()->first(fn ($b) => trim((string) $b->name) === $name);
            if ($branch) {
                return $this->restored($branch);
            }
        }

        // 4) مطابقة تقريبية
        if ($name !== '' && $branch = $this->fuzzy($name)) {
            return $branch;
        }

        if (! $createIfMissing) {
            return null;
        }

        // 5) لم يُعثر عليه — أنشئ فرعاً جديداً
        return $this->create($name, $code);
    }

    private function byCode(string $code): ?Branch
    {
        $branch = $this->all()->first(
            fn ($b) => $b->code !== null && strcasecmp(trim($b->code), $code) === 0
        );

        return $branch ? $this->restored($branch) : null;
    }

    /**
     * المطابقة التقريبية — وهنا يُعالَج الاختصار:
     * «الحفر» → «حفر الباطن» لأن الأولى بداية الثانية.
     */
    private function fuzzy(string $name): ?Branch
    {
        $input  = $this->normalize($name);
        $sorted = $this->sortedChars($input);

        if ($input === '') {
            return null;
        }

        $inputWords = preg_split('/\s+/u', $input) ?: [];
        $best       = null;
        $bestScore  = 0.0;

        foreach ($this->all() as $branch) {
            $db = $this->normalize((string) $branch->name);
            if ($db === '') {
                continue;
            }

            // أ) تطابق بعد التطبيع: «حفر باطن» = «حفر الباطن»
            if ($db === $input) {
                return $this->restored($branch);
            }

            // ب) نفس الحروف بترتيب مختلف: «ليان» = «لينا»
            if ($this->sortedChars($db) === $sorted) {
                return $this->restored($branch);
            }

            // ج) الاختصار: المُدخل بداية اسم الفرع أو العكس.
            //    «الحفر» → «حفر الباطن»، و«حفر» → «حفر الباطن».
            //    نشترط أن يكون التطابق على حدود كلمة كاملة حتى لا تُطابق
            //    «الرياض» كلمةً تبدأ بنفس الحروف مصادفةً.
            $dbWords = preg_split('/\s+/u', $db) ?: [];
            if ($this->isPrefixOfWords($inputWords, $dbWords) || $this->isPrefixOfWords($dbWords, $inputWords)) {
                // كلما زاد عدد الكلمات المشتركة زادت الثقة
                $score = 90 + count($inputWords);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best      = $branch;
                }
                continue;
            }

            // د) تشابه عالٍ للأخطاء الإملائية البسيطة
            similar_text($input, $db, $pct);
            if ($pct >= 70 && $pct > $bestScore) {
                $bestScore = $pct;
                $best      = $branch;
            }

            // هـ) تشابه الكود
            if ($branch->code) {
                similar_text($input, $this->normalize($branch->code), $pctCode);
                if ($pctCode >= 85 && $pctCode > $bestScore) {
                    $bestScore = $pctCode;
                    $best      = $branch;
                }
            }
        }

        return $best ? $this->restored($best) : null;
    }

    /** هل $needle بداية $haystack على مستوى الكلمات الكاملة؟ */
    private function isPrefixOfWords(array $needle, array $haystack): bool
    {
        if (! $needle || count($needle) >= count($haystack)) {
            return false;
        }

        foreach ($needle as $i => $word) {
            if (($haystack[$i] ?? null) !== $word) {
                return false;
            }
        }

        return true;
    }

    /** إزالة «ال» التعريف وتوحيد الهمزات والتاء المربوطة والمسافات. */
    private function normalize(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/[أإآٱ]/u', 'ا', $name);
        $name = str_replace(['ى', 'ة', 'ـ'], ['ي', 'ه', ''], $name);
        $name = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $name); // التشكيل

        $words = array_filter(preg_split('/\s+/u', $name) ?: []);
        $words = array_map(fn ($w) => preg_replace('/^ال/u', '', $w), $words);

        return implode(' ', array_filter($words));
    }

    private function sortedChars(string $str): string
    {
        $chars = preg_split('//u', str_replace(' ', '', $str), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        sort($chars);

        return implode('', $chars);
    }

    private function create(string $name, string $code): ?Branch
    {
        $name = $name ?: $code;
        $code = $code ?: 'BR-' . strtoupper(substr(md5($name), 0, 6));

        try {
            $branch = Branch::firstOrCreate(['code' => $code], ['name' => $name, 'active' => true]);
        } catch (\Throwable) {
            try {
                $branch = Branch::firstOrCreate(
                    ['name' => $name],
                    ['code' => 'BR-' . strtoupper(substr(md5($name . microtime()), 0, 6)), 'active' => true]
                );
            } catch (\Throwable) {
                return null;
            }
        }

        // الفرع الجديد يجب أن تراه الصفوف التالية
        $this->branches?->push($branch);

        return $branch;
    }

    /** يُعيد تفعيل الفرع المحذوف ناعماً عند مطابقته. */
    private function restored(Branch $branch): Branch
    {
        if (method_exists($branch, 'trashed') && $branch->trashed()) {
            $branch->restore();
        }

        return $branch;
    }

    /** تُحمّل الفروع مرة واحدة لكل عملية استيراد (بما فيها المحذوفة ناعماً). */
    private function all(): Collection
    {
        return $this->branches ??= Branch::withTrashed()->get();
    }
}
