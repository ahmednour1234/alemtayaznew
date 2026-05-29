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
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
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
                <th class="px-4 py-3 text-right font-medium">السكن</th>
                <th class="px-4 py-3 text-right font-medium">الفرع</th>
                <th class="px-4 py-3 text-right font-medium">تاريخ الدخول</th>
                <th class="px-4 py-3 text-right font-medium">تاريخ المغادرة</th>
                <th class="px-4 py-3 text-right font-medium">مدة الإقامة</th>
                <th class="px-4 py-3 text-right font-medium">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($assignments as $a)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3">
                    <div class="font-medium text-slate-800">{{ $a->worker?->name ?? '—' }}</div>
                    <div class="text-xs text-slate-400">{{ $a->worker?->nationality?->name ?? '' }}</div>
                </td>
                <td class="px-4 py-3 text-slate-700">{{ $a->housing?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $a->branch?->name ?? '—' }}</td>
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
                    <div class="flex gap-2">
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
            <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">لا توجد سجلات</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-100">
        {{ $assignments->withQueryString()->links() }}
    </div>
</div>
@endsection
