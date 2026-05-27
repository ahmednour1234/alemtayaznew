@extends('admin.layouts.app')
@section('title', 'تعديل عقد ' . $contract->contract_number)
@section('content')
<div class="w-full">

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.contracts.show', $contract->id) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-blue-600 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800">تعديل: <span class="font-mono">{{ $contract->contract_number }}</span></h2>
            </div>
        </div>
        <a href="{{ route('admin.contracts.show', $contract->id) }}" class="text-sm text-slate-500 hover:text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm transition">إلغاء</a>
    </div>

    <form action="{{ route('admin.contracts.update', $contract->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- ── بيانات العقد ──────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100">بيانات العقد</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">العميل
                        <a href="{{ route('admin.clients.create') }}" target="_blank" class="text-blue-500 text-xs font-normal mr-1">+ إضافة</a>
                    </label>
                    <select name="client_id" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">— اختر العميل —</option>
                        @foreach($clients as $cl)
                        <option value="{{ $cl->id }}" {{ old('client_id', $contract->client_id) == $cl->id ? 'selected' : '' }}>{{ $cl->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">الفرع <span class="text-red-500">*</span></label>
                    <select name="branch_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @foreach($branches as $br)
                        <option value="{{ $br->id }}" {{ old('branch_id', $contract->branch_id) == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">تاريخ الطلب <span class="text-red-500">*</span></label>
                    <input type="date" name="request_date" value="{{ old('request_date', $contract->request_date?->format('Y-m-d')) }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">العقد عند القسم</label>
                    <select name="current_department" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @foreach($departments as $key => $label)
                        <option value="{{ $key }}" {{ old('current_department', $contract->current_department) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ── بيانات التأشيرة ────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100">بيانات التأشيرة</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">نوع التأشيرة</label>
                    <select name="visa_type" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">— اختر —</option>
                        @foreach($visaTypes as $key => $label)
                        <option value="{{ $key }}" {{ old('visa_type', $contract->visa_type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">صورة التأشيرة</label>
                    @if($contract->visa_image)
                    <div class="mb-2"><a href="{{ Storage::url($contract->visa_image) }}" target="_blank" class="text-blue-600 text-xs hover:underline">الملف الحالي</a></div>
                    @endif
                    <input type="file" name="visa_image" accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">رقم التأشيرة</label>
                    <input type="text" name="visa_number" value="{{ old('visa_number', $contract->visa_number) }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">محطة الوصول</label>
                    <select name="arrival_airport_id" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">— اختر —</option>
                        @foreach($airports as $ap)
                        <option value="{{ $ap->id }}" {{ old('arrival_airport_id', $contract->arrival_airport_id) == $ap->id ? 'selected' : '' }}>{{ $ap->name }} ({{ $ap->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">محطة القدوم</label>
                    <select name="departure_airport_id" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">— اختر —</option>
                        @foreach($airports as $ap)
                        <option value="{{ $ap->id }}" {{ old('departure_airport_id', $contract->departure_airport_id) == $ap->id ? 'selected' : '' }}>{{ $ap->name }} ({{ $ap->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">محطة الاستلام</label>
                    <select name="delivery_airport_id" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">— اختر —</option>
                        @foreach($airports as $ap)
                        <option value="{{ $ap->id }}" {{ old('delivery_airport_id', $contract->delivery_airport_id) == $ap->id ? 'selected' : '' }}>{{ $ap->name }} ({{ $ap->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ── بيانات مساند ──────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100">بيانات مساند</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">رقم عقد مساند</label>
                    <input type="text" name="musaned_number" value="{{ old('musaned_number', $contract->musaned_number) }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">تاريخ عقد مساند</label>
                    <input type="date" name="musaned_date" value="{{ old('musaned_date', $contract->musaned_date?->format('Y-m-d')) }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">ملف عقد مساند</label>
                    @if($contract->musaned_file)
                    <div class="mb-2"><a href="{{ Storage::url($contract->musaned_file) }}" target="_blank" class="text-blue-600 text-xs hover:underline">الملف الحالي</a></div>
                    @endif
                    <input type="file" name="musaned_file" accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        {{-- ── قسم التنسيق ───────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100">قسم التنسيق</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">العاملة
                        <a href="{{ route('admin.workers.create') }}" target="_blank" class="text-blue-500 text-xs font-normal mr-1">+ إضافة</a>
                    </label>
                    <select name="worker_id" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">— اختر عاملة —</option>
                        @foreach($workers as $w)
                        <option value="{{ $w->id }}" {{ old('worker_id', $contract->worker_id) == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">رقم التوثيق الالكتروني بمساند</label>
                    <input type="text" name="e_doc_number" value="{{ old('e_doc_number', $contract->e_doc_number) }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">الوكيل</label>
                    <select name="agent_id" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">— اختر وكيل —</option>
                        @foreach($agents as $ag)
                        <option value="{{ $ag->id }}" {{ old('agent_id', $contract->agent_id) == $ag->id ? 'selected' : '' }}>{{ $ag->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Status tracker --}}
            <h4 class="text-sm font-semibold text-slate-600 mb-3">الحالة</h4>
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="grid grid-cols-12 bg-slate-50 border-b border-slate-200 px-4 py-2 text-xs font-semibold text-slate-500">
                    <div class="col-span-1 text-center">الحالة</div>
                    <div class="col-span-5">المرحلة</div>
                    <div class="col-span-2 text-center">المدة المتوقعة</div>
                    <div class="col-span-3">التاريخ</div>
                    <div class="col-span-1 text-center">نشطة</div>
                </div>
                @foreach($statuses as $num => $st)
                @php $h = $historyMap->get($num); $isCurrent = $num === $contract->current_status; @endphp
                <div class="grid grid-cols-12 items-center px-4 py-2.5 border-b border-slate-50 {{ $isCurrent ? 'bg-blue-50' : '' }} hover:bg-slate-50 transition">
                    <div class="col-span-1 text-center">
                        <span class="w-6 h-6 inline-flex items-center justify-center rounded-full {{ $isCurrent ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-500' }} text-xs font-bold">{{ $num }}</span>
                    </div>
                    <div class="col-span-5 text-sm text-slate-700">{{ $st['label'] }}</div>
                    <div class="col-span-2 text-center text-xs text-slate-400">{{ $st['days'] ? $st['days'] . ' أيام' : '—' }}</div>
                    <div class="col-span-3">
                        <input type="date" name="status_dates[{{ $num }}]"
                               value="{{ old("status_dates.{$num}", $h?->status_date?->format('Y-m-d')) }}"
                               class="w-full border border-slate-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400">
                    </div>
                    <div class="col-span-1 text-center">
                        <input type="radio" name="update_status" value="{{ $num }}" {{ $isCurrent ? 'checked' : '' }} class="accent-blue-600">
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-4">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">رسالة واتساب عند الحفظ (اختياري)</label>
                <input type="text" name="whatsapp_message" placeholder="رسالة تُرسل للعميل عبر واتساب..."
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
        </div>

        {{-- ── قسم الحسابات ──────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100">قسم الحسابات</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">حالة الدفع</label>
                    <select name="payment_status" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @foreach($payStatuses as $key => $label)
                        <option value="{{ $key }}" {{ old('payment_status', $contract->payment_status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">إجمالي التكلفة</label>
                    <div class="relative">
                        <input type="number" name="total_cost" value="{{ old('total_cost', $contract->total_cost) }}" step="0.01" min="0"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 pl-12">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">ر.س</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── التواريخ ─────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100">التواريخ</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">تاريخ الوصول</label>
                    <input type="date" name="arrival_date" value="{{ old('arrival_date', $contract->arrival_date?->format('Y-m-d')) }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">تاريخ نهاية التجربة</label>
                    <input type="date" name="trial_end_date" value="{{ old('trial_end_date', $contract->trial_end_date?->format('Y-m-d')) }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">تاريخ نهاية العقد</label>
                    <input type="date" name="contract_end_date" value="{{ old('contract_end_date', $contract->contract_end_date?->format('Y-m-d')) }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
            </div>
        </div>

        {{-- ── ملاحظات وتقييم ───────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100">ملاحظات وتقييم</h3>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">ملاحظات</label>
                    <textarea name="notes" rows="3" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none">{{ old('notes', $contract->notes) }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">رسالة نصية للعميل</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="radio" name="client_sms" value="1" {{ old('client_sms', $contract->client_sms) ? 'checked' : '' }} class="accent-blue-600"> نعم</label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="radio" name="client_sms" value="0" {{ !old('client_sms', $contract->client_sms) ? 'checked' : '' }} class="accent-blue-600"> لا</label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">تقييم العميل</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="radio" name="client_rating" value="1" {{ old('client_rating', $contract->client_rating) ? 'checked' : '' }} class="accent-blue-600"> نعم</label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="radio" name="client_rating" value="0" {{ !old('client_rating', $contract->client_rating) ? 'checked' : '' }} class="accent-blue-600"> لا</label>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">صورة إثبات التقييم</label>
                    @if($contract->rating_image)
                    <div class="mb-2"><a href="{{ Storage::url($contract->rating_image) }}" target="_blank" class="text-blue-600 text-xs hover:underline">الصورة الحالية</a></div>
                    @endif
                    <input type="file" name="rating_image" accept=".jpg,.jpeg,.png"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pb-6">
            <a href="{{ route('admin.contracts.show', $contract->id) }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-6 py-2.5 rounded-xl">إلغاء</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-xl shadow">تحديث العقد</button>
        </div>
    </form>
</div>
@endsection
