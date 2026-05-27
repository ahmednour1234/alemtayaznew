@extends('admin.layouts.app')
@section('title', 'عقد استقدام جديد')
@section('content')
<div class="w-full">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.contracts.index') }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-blue-600 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800">عقد استقدام جديد</h2>
                <p class="text-slate-400 text-xs mt-0.5">أدخل بيانات العقد الكاملة</p>
            </div>
        </div>
        <a href="{{ route('admin.contracts.index') }}"
           class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm transition">
            إلغاء
        </a>
    </div>

    <form action="{{ route('admin.contracts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- ── بيانات العقد ──────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                </span>
                بيانات العقد
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Client --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        العميل
                        <a href="{{ route('admin.clients.create') }}" target="_blank" class="text-blue-500 hover:text-blue-700 mr-1 font-normal text-xs">
                            <span class="inline-flex items-center gap-1">+ إضافة عميل جديد</span>
                        </a>
                    </label>
                    <select name="client_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition @error('client_id') border-red-400 @enderror">
                        <option value="">— اختر العميل —</option>
                        @foreach($clients as $cl)
                        <option value="{{ $cl->id }}" {{ old('client_id') == $cl->id ? 'selected' : '' }}>{{ $cl->name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Branch --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">الفرع <span class="text-red-500">*</span></label>
                    <select name="branch_id" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition @error('branch_id') border-red-400 @enderror">
                        <option value="">— اختر الفرع —</option>
                        @foreach($branches as $br)
                        <option value="{{ $br->id }}" {{ old('branch_id', $defaultBranch) == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Request date --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">تاريخ الطلب (ميلادي) <span class="text-red-500">*</span></label>
                    <input type="date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}" required
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>

                {{-- Current department --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">العقد عند القسم</label>
                    <select name="current_department" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        @foreach($departments as $key => $label)
                        <option value="{{ $key }}" {{ old('current_department', 'customer_service') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ── بيانات التأشيرة ────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </span>
                بيانات التأشيرة
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">نوع التأشيرة <span class="text-red-500">*</span></label>
                    <select name="visa_type" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— اختر —</option>
                        @foreach($visaTypes as $key => $label)
                        <option value="{{ $key }}" {{ old('visa_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">صورة التأشيرة</label>
                    <input type="file" name="visa_image" accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    <p class="text-slate-400 text-xs mt-1">JPG / PNG / PDF — حد أقصى 5 ميجا</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">رقم التأشيرة</label>
                    <input type="text" name="visa_number" value="{{ old('visa_number') }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">محطة الوصول</label>
                    <select name="arrival_airport_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— اختر —</option>
                        @foreach($airports as $ap)
                        <option value="{{ $ap->id }}" {{ old('arrival_airport_id') == $ap->id ? 'selected' : '' }}>{{ $ap->name }} ({{ $ap->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">محطة القدوم</label>
                    <select name="departure_airport_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— اختر —</option>
                        @foreach($airports as $ap)
                        <option value="{{ $ap->id }}" {{ old('departure_airport_id') == $ap->id ? 'selected' : '' }}>{{ $ap->name }} ({{ $ap->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">محطة الاستلام</label>
                    <select name="delivery_airport_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— اختر —</option>
                        @foreach($airports as $ap)
                        <option value="{{ $ap->id }}" {{ old('delivery_airport_id') == $ap->id ? 'selected' : '' }}>{{ $ap->name }} ({{ $ap->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ── بيانات مساند ──────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-purple-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
                بيانات مساند
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">رقم عقد مساند</label>
                    <input type="text" name="musaned_number" value="{{ old('musaned_number') }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    <p class="text-slate-400 text-xs mt-1">يُستخدم للتتبع العام من قِبَل العميل</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">تاريخ عقد مساند</label>
                    <input type="date" name="musaned_date" value="{{ old('musaned_date') }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">ملف عقد مساند</label>
                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center hover:border-blue-300 transition cursor-pointer" onclick="document.getElementById('musaned_file').click()">
                        <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-sm text-slate-400">اسحب وادرج ملفاتك أو <span class="text-blue-500 font-medium">تصفح</span></p>
                        <p class="text-xs text-slate-400 mt-1">PDF / JPG / PNG — حد أقصى 10 ميجا</p>
                        <input type="file" id="musaned_file" name="musaned_file" accept=".jpg,.jpeg,.png,.pdf" class="hidden">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── قسم التنسيق ───────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-orange-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </span>
                قسم التنسيق
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        العاملة
                        <a href="{{ route('admin.workers.create') }}" target="_blank" class="text-blue-500 hover:text-blue-700 mr-1 font-normal text-xs">+ إضافة عاملة</a>
                    </label>
                    <select name="worker_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— اختر عاملة —</option>
                        @foreach($workers as $w)
                        <option value="{{ $w->id }}" {{ old('worker_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">رقم التوثيق الالكتروني بمساند</label>
                    <input type="text" name="e_doc_number" value="{{ old('e_doc_number') }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">الوكيل</label>
                    <select name="agent_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— اختر وكيل —</option>
                        @foreach($agents as $ag)
                        <option value="{{ $ag->id }}" {{ old('agent_id') == $ag->id ? 'selected' : '' }}>{{ $ag->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Status tracker --}}
            <div>
                <h4 class="text-sm font-semibold text-slate-600 mb-3">الحالة</h4>
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <div class="grid grid-cols-12 bg-slate-50 border-b border-slate-200 px-4 py-2 text-xs font-semibold text-slate-500">
                        <div class="col-span-1 text-center">#</div>
                        <div class="col-span-6">الحالة</div>
                        <div class="col-span-2 text-center">المدة المتوقعة</div>
                        <div class="col-span-3">التاريخ</div>
                    </div>
                    @foreach($statuses as $num => $st)
                    <div class="grid grid-cols-12 items-center px-4 py-2.5 border-b border-slate-50 {{ $num === 1 ? 'bg-blue-50' : '' }} hover:bg-slate-50 transition">
                        <div class="col-span-1 text-center">
                            <span class="w-6 h-6 inline-flex items-center justify-center rounded-full {{ $num === 1 ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-500' }} text-xs font-bold">{{ $num }}</span>
                        </div>
                        <div class="col-span-6 text-sm text-slate-700">{{ $st['label'] }}</div>
                        <div class="col-span-2 text-center text-xs text-slate-400">
                            {{ $st['days'] ? $st['days'] . ' أيام' : '—' }}
                        </div>
                        <div class="col-span-3">
                            <input type="date" name="status_dates[{{ $num }}]" value="{{ old("status_dates.{$num}") }}"
                                   class="w-full border border-slate-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── قسم الحسابات ──────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                قسم الحسابات
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">حالة الدفع</label>
                    <select name="payment_status" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        @foreach($payStatuses as $key => $label)
                        <option value="{{ $key }}" {{ old('payment_status', 'pending') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">إجمالي التكلفة</label>
                    <div class="relative">
                        <input type="number" name="total_cost" value="{{ old('total_cost') }}" step="0.01" min="0"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition pl-12">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">ر.س</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── تواريخ ───────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
                التواريخ
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">تاريخ الوصول</label>
                    <input type="date" name="arrival_date" value="{{ old('arrival_date') }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">تاريخ نهاية التجربة</label>
                    <input type="date" name="trial_end_date" value="{{ old('trial_end_date') }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">تاريخ نهاية العقد</label>
                    <input type="date" name="contract_end_date" value="{{ old('contract_end_date') }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
            </div>
        </div>

        {{-- ── بيانات مساندة ─────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100">ملاحظات وتقييم</h3>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">ملاحظات</label>
                    <textarea name="notes" rows="3" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition resize-none">{{ old('notes') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">رسالة نصية للعميل</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="client_sms" value="1" {{ old('client_sms') == '1' ? 'checked' : '' }} class="accent-blue-600"> نعم
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="client_sms" value="0" {{ old('client_sms', '0') == '0' ? 'checked' : '' }} class="accent-blue-600"> لا
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">تقييم العميل</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="client_rating" value="1" {{ old('client_rating') == '1' ? 'checked' : '' }} class="accent-blue-600"> نعم
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="client_rating" value="0" {{ old('client_rating', '0') == '0' ? 'checked' : '' }} class="accent-blue-600"> لا
                            </label>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">صورة إثبات التقييم</label>
                    <input type="file" name="rating_image" accept=".jpg,.jpeg,.png"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3 pb-6">
            <a href="{{ route('admin.contracts.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-6 py-2.5 rounded-xl">إلغاء</a>
            <button type="submit" name="action" value="save" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-xl shadow">إضافة</button>
            <button type="submit" name="action" value="save_more" class="bg-green-600 hover:bg-green-700 text-white text-sm px-6 py-2.5 rounded-xl shadow">إضافة وبدء إضافة المزيد</button>
        </div>
    </form>
</div>
@endsection
