@extends('admin.layouts.app')
@section('title', 'تعيينات السكن')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-slate-800">تعيينات السكن</h2>
    @can('housing-assignments.create')
    <a href="{{ route('admin.housing-assignments.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        تسكين عاملة
    </a>
    @endcan
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<!-- Filters -->
<div class="bg-white rounded-xl p-4 shadow-sm mb-4 border border-slate-100">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">بحث عاملة</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم العاملة"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">السكن</label>
            <select name="housing_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach($housings as $h)
                <option value="{{ $h->id }}" {{ request('housing_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">حالة العمالة</label>
            <select name="worker_status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                <option value="normal"  {{ request('worker_status') === 'normal'  ? 'selected' : '' }}>نظامية</option>
                <option value="escaped" {{ request('worker_status') === 'escaped' ? 'selected' : '' }}>هاربة</option>
                <option value="sick"    {{ request('worker_status') === 'sick'    ? 'selected' : '' }}>مريضة</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">سبب السكن</label>
            <select name="reason" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                <option value="sponsorship_transfer" {{ request('reason') === 'sponsorship_transfer' ? 'selected' : '' }}>نقل كفالة</option>
                <option value="deportation"          {{ request('reason') === 'deportation'          ? 'selected' : '' }}>تسفير</option>
                <option value="handover"             {{ request('reason') === 'handover'             ? 'selected' : '' }}>تسليم</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الحالة</label>
            <select name="active" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>مقيم حاليًا</option>
                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>غادر</option>
            </select>
        </div>
        <div class="flex items-end">
            <button class="bg-slate-800 hover:bg-slate-900 text-white text-sm px-5 py-2 rounded-lg w-full">تصفية</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs">
            <tr>
                <th class="px-4 py-3 text-right font-medium">العاملة</th>
                <th class="px-4 py-3 text-right font-medium">الكفيل</th>
                <th class="px-4 py-3 text-right font-medium">السكن</th>
                <th class="px-4 py-3 text-right font-medium">الفرع</th>
                <th class="px-4 py-3 text-right font-medium">حالة العمالة</th>
                <th class="px-4 py-3 text-right font-medium">سبب السكن</th>
                <th class="px-4 py-3 text-right font-medium">تاريخ الدخول</th>
                <th class="px-4 py-3 text-right font-medium">تاريخ المغادرة</th>
                <th class="px-4 py-3 text-right font-medium">مدة الإقامة</th>
                <th class="px-4 py-3 text-right font-medium">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($assignments as $a)
            <tr class="hover:bg-slate-50" x-data="{ show: false }">
                {{-- View modal --}}
                <template x-teleport="body">
                <div x-show="show" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                     @keydown.escape.window="show = false">
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="show = false"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10 overflow-hidden">
                        {{-- Header --}}
                        <div style="background:linear-gradient(135deg,#0f172a,#1e293b);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;">
                            <div class="flex items-center gap-3">
                                <div style="width:36px;height:36px;border-radius:9px;background:rgba(201,168,76,.2);display:flex;align-items:center;justify-content:center;">
                                    <svg width="18" height="18" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                </div>
                                <div>
                                    <div style="color:#c9a84c;font-size:10px;font-weight:600;">تعيين سكن</div>
                                    <div style="color:#e2e8f0;font-size:14px;font-weight:700;">{{ $a->worker?->name ?? '—' }}</div>
                                </div>
                            </div>
                            <button @click="show = false" style="color:#94a3b8;background:none;border:none;cursor:pointer;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        {{-- Body --}}
                        <div class="p-5 space-y-3 text-sm">
                            @php
                                $reasonLabels2 = [
                                    'sponsorship_transfer' => ['label' => 'نقل كفالة', 'bg' => '#ede9fe', 'color' => '#7c3aed'],
                                    'deportation'          => ['label' => 'تسفير',      'bg' => '#fee2e2', 'color' => '#b91c1c'],
                                    'handover'             => ['label' => 'تسليم',      'bg' => '#dcfce7', 'color' => '#16a34a'],
                                    'rental'               => ['label' => 'تأجير',      'bg' => '#cffafe', 'color' => '#0891b2'],
                                    'settlement'           => ['label' => 'تسوية',      'bg' => '#fef3c7', 'color' => '#b45309'],
                                ];
                                $rl = $reasonLabels2[$a->reason] ?? null;
                            @endphp
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-slate-50 rounded-xl p-3">
                                    <div class="text-xs text-slate-400 mb-1">العاملة</div>
                                    <div class="font-semibold text-slate-800">{{ $a->worker?->name ?? '—' }}</div>
                                    @if($a->worker?->nationality)
                                    <span style="background:#e0f2fe;color:#0369a1;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;display:inline-block;margin-top:3px;">{{ $a->worker->nationality->name }}</span>
                                    @endif
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3">
                                    <div class="text-xs text-slate-400 mb-1">السكن</div>
                                    <div class="font-semibold text-slate-800">{{ $a->housing?->name ?? '—' }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $a->branch?->name ?? '—' }}</div>
                                </div>
                                @php $sponsor = $a->worker?->client ?? $a->worker?->latestContract?->client; @endphp
                                <div class="bg-slate-50 rounded-xl p-3 col-span-2">
                                    <div class="text-xs text-slate-400 mb-1">الكفيل</div>
                                    @if($sponsor)
                                        <div class="font-semibold text-slate-800">{{ $sponsor->name }}</div>
                                        @if($sponsor->phone)
                                        <a href="tel:{{ $sponsor->phone }}" class="text-xs text-blue-600" dir="ltr">{{ $sponsor->phone }}</a>
                                        @endif
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3">
                                    <div class="text-xs text-slate-400 mb-1">سبب السكن</div>
                                    @if($rl)
                                        <span style="background:{{ $rl['bg'] }};color:{{ $rl['color'] }};font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;display:inline-block;">{{ $rl['label'] }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3">
                                    <div class="text-xs text-slate-400 mb-1">الحالة</div>
                                    @if($a->check_out_date)
                                        <span class="text-slate-500 font-medium">غادر</span>
                                    @else
                                        <span style="background:#dcfce7;color:#16a34a;font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;display:inline-block;">مقيم</span>
                                    @endif
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3">
                                    <div class="text-xs text-slate-400 mb-1">تاريخ الدخول</div>
                                    <div class="font-medium text-slate-700">{{ $a->check_in_date }}</div>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3">
                                    <div class="text-xs text-slate-400 mb-1">تاريخ المغادرة</div>
                                    <div class="font-medium text-slate-700">{{ $a->check_out_date ?? '—' }}</div>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3 col-span-2">
                                    <div class="text-xs text-slate-400 mb-1">مدة الإقامة</div>
                                    <div class="font-bold text-slate-800">{{ $a->days_stayed }} يوم</div>
                                </div>
                            </div>
                            @if($a->notes)
                            <div class="bg-amber-50 border border-amber-100 rounded-xl p-3">
                                <div class="text-xs text-amber-600 font-semibold mb-1">ملاحظات</div>
                                <div class="text-sm text-slate-700">{{ $a->notes }}</div>
                            </div>
                            @endif
                        </div>
                        <div class="px-5 pb-4 flex justify-end">
                            <button @click="show = false"
                                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-5 py-2 rounded-xl transition">إغلاق</button>
                        </div>
                    </div>
                </div>
                </template>
                <td class="px-4 py-3">
                    <div class="font-medium text-slate-800">{{ $a->worker?->name ?? '—' }}</div>
                    @if($a->worker?->nationality)
                    <span style="background:#e0f2fe;color:#0369a1;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;display:inline-block;margin-top:2px;">{{ $a->worker->nationality->name }}</span>
                    @endif
                </td>
                @php $sponsor = $a->worker?->client ?? $a->worker?->latestContract?->client; @endphp
                <td class="px-4 py-3">
                    @if($sponsor)
                        <div class="font-medium text-slate-800">{{ $sponsor->name }}</div>
                        @if($sponsor->phone)
                        <a href="tel:{{ $sponsor->phone }}" class="text-xs text-slate-500 hover:text-blue-600" dir="ltr">{{ $sponsor->phone }}</a>
                        @endif
                    @else
                        <span class="text-slate-300 text-xs">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-slate-700">{{ $a->housing?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $a->branch?->name ?? '—' }}</td>
                {{-- حالة العمالة + فترة الضمان --}}
                <td class="px-4 py-3">
                    @php
                        $ws = \App\Models\HousingAssignment::workerStatuses()[$a->worker_status ?? 'normal'] ?? ['label'=>'نظامية','bg'=>'#dcfce7','color'=>'#16a34a'];
                    @endphp
                    <span style="background:{{ $ws['bg'] }};color:{{ $ws['color'] }};font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;display:inline-block;">{{ $ws['label'] }}</span>
                    @if($a->isActive() && $a->isInGuaranteePeriod())
                    <span style="background:#ede9fe;color:#7c3aed;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;display:inline-block;margin-top:3px;" title="وصلت منذ أقل من 3 أشهر">⏱ ضمان</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @php
                        $reasonLabels = [
                            'sponsorship_transfer' => ['label' => 'نقل كفالة', 'bg' => '#ede9fe', 'color' => '#7c3aed'],
                            'deportation'          => ['label' => 'تسفير',      'bg' => '#fee2e2', 'color' => '#b91c1c'],
                            'handover'             => ['label' => 'تسليم',      'bg' => '#dcfce7', 'color' => '#16a34a'],
                            'rental'               => ['label' => 'تأجير',      'bg' => '#cffafe', 'color' => '#0891b2'],
                            'settlement'           => ['label' => 'تسوية',      'bg' => '#fef3c7', 'color' => '#b45309'],
                        ];
                        $r = $reasonLabels[$a->reason] ?? null;
                    @endphp
                    @if($r)
                        <span style="background:{{ $r['bg'] }};color:{{ $r['color'] }};font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;display:inline-block;">{{ $r['label'] }}</span>
                    @elseif($a->reason)
                        <span class="text-xs text-slate-500">{{ $a->reason }}</span>
                    @else
                        <span class="text-slate-300 text-xs">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-slate-700">{{ $a->check_in_date }}</td>
                <td class="px-4 py-3">
                    @if($a->check_out_date)
                        <span class="text-slate-500">{{ $a->check_out_date }}</span>
                    @else
                        <span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">مقيم</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-slate-500">{{ $a->days_stayed }} يوم</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2 items-center">
                        <button @click="show = true"
                                class="text-blue-600 hover:underline text-xs">عرض</button>
                        @can('housing-assignments.edit')
                        {{-- Edit modal --}}
                        <div x-data="{ editOpen: false }">
                            <button type="button" @click="editOpen = true"
                                    class="text-indigo-600 hover:underline text-xs">تعديل</button>
                            <div x-show="editOpen" x-cloak
                                 class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4 overflow-y-auto"
                                 @click.self="editOpen = false" @keydown.escape.window="editOpen = false">
                                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md my-8">
                                    <div class="px-6 pt-5 pb-3 border-b border-slate-100">
                                        <h3 class="font-bold text-slate-800">تعديل التسكين — {{ $a->worker?->name }}</h3>
                                    </div>
                                    <form method="POST" action="{{ route('admin.housing-assignments.update', $a->id) }}">
                                        @csrf @method('PATCH')
                                        <div class="p-6 space-y-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">السكن <span class="text-red-500">*</span></label>
                                                <select name="housing_id" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                    @foreach($housings as $h)
                                                    <option value="{{ $h->id }}" {{ $a->housing_id == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @if($branches)
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">الفرع <span class="text-red-500">*</span></label>
                                                <select name="branch_id" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                    @foreach($branches as $br)
                                                    <option value="{{ $br->id }}" {{ $a->branch_id == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @else
                                            <input type="hidden" name="branch_id" value="{{ $a->branch_id }}">
                                            @endif
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">تاريخ الدخول <span class="text-red-500">*</span></label>
                                                    <input type="date" name="check_in_date" required
                                                           value="{{ $a->check_in_date?->format('Y-m-d') }}"
                                                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">تاريخ المغادرة المتوقع</label>
                                                    <input type="date" name="expected_check_out_date"
                                                           value="{{ $a->expected_check_out_date?->format('Y-m-d') }}"
                                                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">سبب السكن</label>
                                                    <select name="reason" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                        <option value="">—</option>
                                                        <option value="sponsorship_transfer" {{ $a->reason === 'sponsorship_transfer' ? 'selected' : '' }}>نقل كفالة</option>
                                                        <option value="deportation"          {{ $a->reason === 'deportation'          ? 'selected' : '' }}>تسفير</option>
                                                        <option value="handover"             {{ $a->reason === 'handover'             ? 'selected' : '' }}>تسليم</option>
                                                        <option value="rental"               {{ $a->reason === 'rental'               ? 'selected' : '' }}>تأجير</option>
                                                        <option value="settlement"           {{ $a->reason === 'settlement'           ? 'selected' : '' }}>تسوية</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">حالة العمالة</label>
                                                    <select name="worker_status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                        <option value="normal"  {{ ($a->worker_status ?? 'normal') === 'normal'  ? 'selected' : '' }}>نظامية</option>
                                                        <option value="escaped" {{ $a->worker_status === 'escaped' ? 'selected' : '' }}>هاربة</option>
                                                        <option value="sick"    {{ $a->worker_status === 'sick'    ? 'selected' : '' }}>مريضة</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">ملاحظات</label>
                                                <textarea name="notes" rows="2"
                                                          class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">{{ $a->notes }}</textarea>
                                            </div>
                                        </div>
                                        <div class="px-6 py-4 border-t border-slate-100 flex gap-2 justify-end">
                                            <button type="button" @click="editOpen = false"
                                                    class="text-slate-500 text-sm px-4 py-2">إلغاء</button>
                                            <button type="submit"
                                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-5 py-2 rounded-lg">حفظ التعديلات</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endcan
                        @if(! $a->check_out_date)
                        <form method="POST" action="{{ route('admin.housing-assignments.checkout', $a->id) }}"
                              enctype="multipart/form-data"
                              x-data="{ open: false, disp: '{{ $a->reason === 'rental' ? 'rental' : '' }}' }">
                            @csrf @method('PATCH')
                            <button type="button" @click="open = true"
                                    class="text-amber-600 hover:underline text-xs">مغادرة</button>
                            <div x-show="open" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4 overflow-y-auto"
                                 @click.self="open = false" @keydown.escape.window="open = false">
                                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md my-8">
                                    <div class="px-6 pt-5 pb-3 border-b border-slate-100">
                                        <h3 class="font-bold text-slate-800">تسجيل مغادرة — {{ $a->worker?->name }}</h3>
                                        <p class="text-xs text-slate-500 mt-0.5">حدد وجهة العاملة عند المغادرة وسجّل بياناتها.</p>
                                    </div>
                                    <div class="p-6 space-y-4 max-h-[65vh] overflow-y-auto">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">تاريخ المغادرة <span class="text-red-500">*</span></label>
                                            <input type="date" name="check_out_date" required value="{{ date('Y-m-d') }}"
                                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                        </div>

                                        {{-- اختيار الوجهة --}}
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">الوجهة</label>
                                            <div class="grid grid-cols-3 gap-2">
                                                <button type="button" @click="disp = ''"
                                                        :class="disp === '' ? 'border-slate-800 bg-slate-50 text-slate-800' : 'border-slate-200 text-slate-500'"
                                                        class="border rounded-lg py-2 text-xs font-semibold transition">مغادرة عادية</button>
                                                <button type="button" @click="disp = 'rental'"
                                                        :class="disp === 'rental' ? 'border-cyan-600 bg-cyan-50 text-cyan-700' : 'border-slate-200 text-slate-500'"
                                                        class="border rounded-lg py-2 text-xs font-semibold transition">تأجير</button>
                                                <button type="button" @click="disp = 'settlement'"
                                                        :class="disp === 'settlement' ? 'border-amber-600 bg-amber-50 text-amber-700' : 'border-slate-200 text-slate-500'"
                                                        class="border rounded-lg py-2 text-xs font-semibold transition">تسوية</button>
                                            </div>
                                            <input type="hidden" name="disposition" :value="disp">
                                        </div>

                                        {{-- بيانات التأجير --}}
                                        <div x-show="disp === 'rental'" x-cloak class="space-y-3 border border-cyan-100 bg-cyan-50/40 rounded-xl p-3">
                                            <div class="text-xs font-bold text-cyan-700">بيانات التأجير</div>
                                            <div>
                                                <label class="block text-xs text-slate-500 mb-1">العميل (المستأجر) <span class="text-red-500">*</span></label>
                                                <select name="rental_client_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                    <option value="">اختر عميل</option>
                                                    @foreach($clients as $c)
                                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-xs text-slate-500 mb-1">رقم العقد</label>
                                                    <input type="text" name="rental_contract_number" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-slate-500 mb-1">قيمة الإيجار</label>
                                                    <input type="number" step="0.01" min="0" name="rent_value" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-slate-500 mb-1">بداية الإيجار</label>
                                                    <input type="date" name="rent_start_date" value="{{ date('Y-m-d') }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-slate-500 mb-1">انتهاء الإيجار</label>
                                                    <input type="date" name="rent_end_date" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-slate-500 mb-1">صورة العقد</label>
                                                <input type="file" name="rental_contract_image" accept="image/*" class="w-full text-xs">
                                            </div>
                                            <textarea name="rental_notes" rows="2" placeholder="ملاحظات التأجير" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm"></textarea>
                                        </div>

                                        {{-- بيانات التسوية --}}
                                        <div x-show="disp === 'settlement'" x-cloak class="space-y-3 border border-amber-100 bg-amber-50/40 rounded-xl p-3">
                                            <div class="text-xs font-bold text-amber-700">بيانات التسوية</div>
                                            <div>
                                                <label class="block text-xs text-slate-500 mb-1">العميل <span class="text-red-500">*</span></label>
                                                <select name="settlement_client_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                    <option value="">اختر عميل</option>
                                                    @foreach($clients as $c)
                                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-xs text-slate-500 mb-1">مبلغ التسوية</label>
                                                    <input type="number" step="0.01" min="0" name="settlement_amount" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-slate-500 mb-1">نوع التسوية</label>
                                                    <select name="settlement_type" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                        <option value="">—</option>
                                                        @foreach(\App\Models\HousingSettlement::types() as $val => $label)
                                                        <option value="{{ $val }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-slate-500 mb-1">الرقم المرجعي</label>
                                                    <input type="text" name="settlement_reference" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-slate-500 mb-1">تاريخ التسوية</label>
                                                    <input type="date" name="settlement_date" value="{{ date('Y-m-d') }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-slate-500 mb-1">صورة المستند</label>
                                                <input type="file" name="settlement_document_image" accept="image/*" class="w-full text-xs">
                                            </div>
                                            <textarea name="settlement_notes" rows="2" placeholder="ملاحظات التسوية" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm"></textarea>
                                        </div>

                                        <textarea name="notes" placeholder="ملاحظات عامة (اختياري)" rows="2"
                                                  class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm"></textarea>
                                    </div>
                                    <div class="px-6 py-4 border-t border-slate-100 flex gap-2 justify-end">
                                        <button type="button" @click="open = false"
                                                class="text-slate-500 text-sm px-4 py-2">إلغاء</button>
                                        <button type="submit"
                                                class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-5 py-2 rounded-lg">تأكيد المغادرة</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endif
                        @can('housing-assignments.delete')
                        <form method="POST" action="{{ route('admin.housing-assignments.destroy', $a->id) }}"
                              onsubmit="return confirm('حذف هذا السجل؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline text-xs">حذف</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="px-4 py-10 text-center text-slate-400">لا توجد سجلات</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-100">
        {{ $assignments->withQueryString()->links() }}
    </div>
</div>
@endsection
