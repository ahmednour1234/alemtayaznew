<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Lead;
use App\Models\Nationality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;

/**
 * صفحة «تواصل معنا» — النموذج يُسجَّل كعميل محتمل (Lead) بمصدر «website»
 * ليظهر مباشرةً في لوحة التسويق بدل أن يضيع في بريد.
 */
class ContactController extends Controller
{
    /** الخدمات المتاحة للاختيار في نماذج الطلب. */
    public const SERVICES = [
        'استقدام عمالة منزلية',
        'استقدام سائق',
        'نقل الخدمات (نقل كفالة)',
        'تجديد الاستقدام',
        'استفسار عام',
    ];

    public function show()
    {
        return view('public.contact', [
            // الفروع المعلَّمة للعرض العام فقط — الفروع الإدارية لا تُعرض للعميل
            'branches'      => Branch::where('active', true)
                ->where('public', true)
                ->orderBy('name')
                ->get(),
            'nationalities' => Nationality::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        // حدّ بسيط لمنع إغراق الجدول برسائل آلية
        $key = 'contact:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'تم استقبال عدة طلبات من جهازك. يرجى المحاولة بعد قليل.']);
        }
        RateLimiter::hit($key, 600);

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:30'],
            'city'           => ['nullable', 'string', 'max:100'],
            'nationality_id' => ['nullable', 'integer', 'exists:nationalities,id'],
            'branch_id'      => [
                'nullable', 'integer',
                // نمنع تمرير معرّف فرع غير معروض عبر تعديل النموذج يدوياً
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('active', true)->where('public', true)),
            ],
            'service'        => ['required', 'string', Rule::in(self::SERVICES)],
            'notes'          => ['nullable', 'string', 'max:2000'],
            // حقل فخّ مخفي: تملؤه الروبوتات فقط
            'website'        => ['nullable', 'size:0'],
        ], [
            'name.required'    => 'الاسم مطلوب.',
            'phone.required'   => 'رقم الجوال مطلوب.',
            'service.required' => 'اختيار الخدمة مطلوب.',
            'service.in'       => 'الخدمة المختارة غير صحيحة.',
            'website.size'     => 'تعذّر إرسال الطلب.',
        ]);

        unset($data['website']);

        // لا يوجد عمود مستقل للخدمة، فنضمّها في الملاحظات ليراها فريق التسويق
        $service = $data['service'];
        unset($data['service']);
        $data['notes'] = 'الخدمة المطلوبة: ' . $service
            . (! empty($data['notes']) ? "
" . $data['notes'] : '');

        Lead::create($data + [
            'source' => 'website',
            'status' => 'new',
        ]);

        return back()->with('success', 'تم استلام طلبك بنجاح، وسيتواصل معك فريقنا في أقرب وقت.');
    }

    /**
     * استقبال الطلب السريع من النافذة المنبثقة (JSON).
     * منفصل عن store() لأن الاستجابة هنا JSON لا إعادة توجيه.
     */
    public function quickLead(Request $request)
    {
        $key = 'quick-lead:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'تم استقبال عدة طلبات من جهازك. يرجى المحاولة بعد قليل.',
            ], 429);
        }
        RateLimiter::hit($key, 600);

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:30'],
            'city'           => ['nullable', 'string', 'max:100'],
            'nationality_id' => ['nullable', 'integer', 'exists:nationalities,id'],
            'service'        => ['required', 'string', Rule::in(self::SERVICES)],
            'notes'          => ['nullable', 'string', 'max:2000'],
            'website'        => ['nullable', 'size:0'],
        ], [
            'name.required'    => 'الاسم مطلوب.',
            'phone.required'   => 'رقم الجوال مطلوب.',
            'service.required' => 'اختيار الخدمة مطلوب.',
            'service.in'       => 'الخدمة المختارة غير صحيحة.',
            'website.size'     => 'تعذّر إرسال الطلب.',
        ]);

        // لا يوجد عمود مستقل للخدمة، فنضمّها في الملاحظات ليراها فريق التسويق
        $notes = 'الخدمة المطلوبة: ' . $data['service'];
        if (! empty($data['notes'])) {
            $notes .= "
" . $data['notes'];
        }

        Lead::create([
            'name'           => $data['name'],
            'phone'          => $data['phone'],
            'city'           => $data['city'] ?? null,
            'nationality_id' => $data['nationality_id'] ?? null,
            'notes'          => $notes,
            'source'         => 'website_popup',
            'status'         => 'new',
        ]);

        return response()->json(['ok' => true]);
    }
}
