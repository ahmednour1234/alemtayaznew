{{--
    نموذج الطلب وحده.
    مشترك بين صفحة «اطلب الآن» وأي مكان يحتاج النموذج نفسه، فيبقى
    معرّفاً في ملف واحد ولا يتفرّع نسختان تختلفان مع الوقت.
--}}
@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
    <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<h2 class="text-xl font-extrabold text-navy mb-1">أرسل طلبك</h2>
<p class="text-xs text-slate-500 mb-6">الحقول المعلّمة بـ <span class="text-red-500">*</span> مطلوبة.</p>

<form method="POST" action="{{ route('site.contact.store') }}" class="space-y-5">
    @csrf

    {{-- حقل فخّ مخفي لصدّ الروبوتات — يُترك فارغاً دائماً --}}
    <input type="text" name="website" value="" tabindex="-1" autocomplete="off"
           class="hidden" aria-hidden="true">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                الاسم <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy @error('name') border-red-400 @else border-slate-300 @enderror">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                رقم الجوال <span class="text-red-500">*</span>
            </label>
            <input type="tel" name="phone" value="{{ old('phone') }}" required dir="ltr"
                   placeholder="05xxxxxxxx"
                   class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy @error('phone') border-red-400 @else border-slate-300 @enderror">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">المدينة</label>
            <input type="text" name="city" value="{{ old('city') }}"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">الجنسية المطلوبة</label>
            <select name="nationality_id"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy">
                <option value="">غير محدد</option>
                @foreach($nationalities as $nat)
                <option value="{{ $nat->id }}" @selected(old('nationality_id') == $nat->id)>{{ $nat->display_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                الخدمة المطلوبة <span class="text-red-500">*</span>
            </label>
            <select name="service" required
                    class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy @error('service') border-red-400 @else border-slate-300 @enderror">
                <option value="">اختر الخدمة</option>
                @foreach(\App\Http\Controllers\PublicSite\ContactController::SERVICES as $srv)
                <option value="{{ $srv }}" @selected(old('service') === $srv)>{{ $srv }}</option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">الفرع الأقرب لك</label>
            <select name="branch_id"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy">
                <option value="">غير محدد</option>
                @foreach($branches as $b)
                <option value="{{ $b->id }}" @selected(old('branch_id') == $b->id)>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">تفاصيل الطلب</label>
        <textarea name="notes" rows="4"
                  placeholder="اكتب استفسارك أو تفاصيل طلبك..."
                  class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy">{{ old('notes', $prefill) }}</textarea>
    </div>

    <button type="submit"
            class="w-full sm:w-auto bg-navy hover:bg-navy-light text-white font-bold px-8 py-3 rounded-xl transition-colors">
        إرسال الطلب
    </button>
</form>
