<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Lead;
use App\Models\Nationality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/**
 * صفحة «تواصل معنا» — النموذج يُسجَّل كعميل محتمل (Lead) بمصدر «website»
 * ليظهر مباشرةً في لوحة التسويق بدل أن يضيع في بريد.
 */
class ContactController extends Controller
{
    public function show()
    {
        return view('public.contact', [
            'branches'      => Branch::where('active', true)->orderBy('name')->get(),
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
            'branch_id'      => ['nullable', 'integer', 'exists:branches,id'],
            'notes'          => ['nullable', 'string', 'max:2000'],
            // حقل فخّ مخفي: تملؤه الروبوتات فقط
            'website'        => ['nullable', 'size:0'],
        ], [
            'name.required'  => 'الاسم مطلوب.',
            'phone.required' => 'رقم الجوال مطلوب.',
            'website.size'   => 'تعذّر إرسال الطلب.',
        ]);

        unset($data['website']);

        Lead::create($data + [
            'source' => 'website',
            'status' => 'new',
        ]);

        return back()->with('success', 'تم استلام طلبك بنجاح، وسيتواصل معك فريقنا في أقرب وقت.');
    }
}
