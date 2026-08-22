@extends('public.layouts.app')
@php
    $S = fn(string $k) => \App\Models\SiteSetting::value($k);
    // لو جاء الزائر من صفحة عاملة معيّنة نضع رقمها في الملاحظات تلقائياً
    $prefill = request('worker') ? 'استفسار عن العاملة رقم ' . (int) request('worker') : '';
@endphp
@section('title', 'تواصل معنا — ' . $S('company_name'))

@section('content')

<section class="hero-grad text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-3xl sm:text-4xl font-extrabold">تواصل معنا</h1>
        <p class="text-white/75 mt-3 max-w-2xl leading-relaxed">
            أرسل طلبك أو استفسارك وسيتواصل معك فريقنا في أقرب وقت.
        </p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 py-14">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ══ النموذج ══ --}}
        <div class="lg:col-span-2">
            <div class="reveal bg-white rounded-2xl border border-slate-200 p-7">

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
                                <option value="{{ $nat->id }}" @selected(old('nationality_id') == $nat->id)>{{ $nat->name }}</option>
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
            </div>
        </div>

        {{-- ══ بيانات التواصل ══ --}}
        <aside class="space-y-5">
            <div class="reveal bg-white rounded-2xl border border-slate-200 p-6">
                <h3 class="font-bold text-navy text-sm mb-4">بيانات التواصل</h3>
                <ul class="space-y-4 text-sm text-slate-600">
                    @if($S('phone'))
                    <li class="flex items-start gap-3">
                        <span class="w-9 h-9 rounded-lg bg-navy/5 text-navy flex items-center justify-center flex-shrink-0">
                            <x-icon name="24_phone" class="w-5 h-5" />
                        </span>
                        <div>
                            <p class="text-xs text-slate-400">الهاتف</p>
                            <a href="tel:{{ $S('phone') }}" dir="ltr" class="font-semibold hover:text-navy">{{ $S('phone') }}</a>
                        </div>
                    </li>
                    @endif

                    @if($S('email'))
                    <li class="flex items-start gap-3">
                        <span class="w-9 h-9 rounded-lg bg-navy/5 text-navy flex items-center justify-center flex-shrink-0">
                            <x-icon name="26_email" class="w-5 h-5" />
                        </span>
                        <div>
                            <p class="text-xs text-slate-400">البريد الإلكتروني</p>
                            <a href="mailto:{{ $S('email') }}" dir="ltr" class="font-semibold hover:text-navy break-all">{{ $S('email') }}</a>
                        </div>
                    </li>
                    @endif

                    @if($S('address'))
                    <li class="flex items-start gap-3">
                        <span class="w-9 h-9 rounded-lg bg-navy/5 text-navy flex items-center justify-center flex-shrink-0">
                            <x-icon name="27_location" class="w-5 h-5" />
                        </span>
                        <div>
                            <p class="text-xs text-slate-400">العنوان</p>
                            <p class="font-semibold">{{ $S('address') }}</p>
                        </div>
                    </li>
                    @endif

                    @if($S('working_hours'))
                    <li class="flex items-start gap-3">
                        <span class="w-9 h-9 rounded-lg bg-navy/5 text-navy flex items-center justify-center flex-shrink-0">
                            <x-icon name="30_clock_24_7" class="w-5 h-5" />
                        </span>
                        <div>
                            <p class="text-xs text-slate-400">ساعات العمل</p>
                            <p class="font-semibold">{{ $S('working_hours') }}</p>
                        </div>
                    </li>
                    @endif
                </ul>

                @if($S('whatsapp'))
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $S('whatsapp')) }}" target="_blank" rel="noopener"
                   class="block w-full text-center mt-5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2.5 rounded-lg transition-colors">
                    تواصل عبر واتساب
                </a>
                @endif
            </div>

            @if($S('map_embed'))
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="aspect-video">{!! $S('map_embed') !!}</div>
            </div>
            @endif
        </aside>

    </div>
</section>

@endsection
