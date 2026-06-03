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
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
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
                <td class="px-4 py-3">
                    @php
                        $reasonLabels = [
                            'sponsorship_transfer' => ['label' => 'نقل كفالة', 'bg' => '#ede9fe', 'color' => '#7c3aed'],
                            'deportation'          => ['label' => 'تسفير',      'bg' => '#fee2e2', 'color' => '#b91c1c'],
                            'handover'             => ['label' => 'تسليم',      'bg' => '#dcfce7', 'color' => '#16a34a'],
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
                        @if(! $a->check_out_date)
                        <form method="POST" action="{{ route('admin.housing-assignments.checkout', $a->id) }}"
                              x-data="{ open: false }">
                            @csrf @method('PATCH')
                            <button type="button" @click="open = true"
                                    class="text-amber-600 hover:underline text-xs">مغادرة</button>
                            <div x-show="open" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
                                 @click.self="open = false">
                                <div class="bg-white rounded-xl p-6 w-80 shadow-xl">
                                    <h3 class="font-semibold text-slate-800 mb-4">تسجيل مغادرة — {{ $a->worker?->name }}</h3>
                                    <input type="date" name="check_out_date" required
                                           value="{{ date('Y-m-d') }}"
                                           class="w-full border rounded-lg px-3 py-2 text-sm mb-3">
                                    <textarea name="notes" placeholder="ملاحظات (اختياري)" rows="2"
                                              class="w-full border rounded-lg px-3 py-2 text-sm mb-4"></textarea>
                                    <div class="flex gap-2 justify-end">
                                        <button type="button" @click="open = false"
                                                class="text-slate-500 text-sm px-4 py-2">إلغاء</button>
                                        <button type="submit"
                                                class="bg-amber-600 text-white text-sm px-4 py-2 rounded-lg">تأكيد</button>
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
