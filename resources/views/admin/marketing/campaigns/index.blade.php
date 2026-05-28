@extends('admin.layouts.app')
@section('title', 'الحملات التسويقية')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">الحملات التسويقية</h2>
    <a href="{{ route('admin.marketing.campaigns.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        حملة جديدة
    </a>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl p-5 shadow-sm mb-4 border border-slate-100">
    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث باسم الحملة..."
               class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        <button class="bg-slate-800 hover:bg-slate-900 text-white text-sm px-5 rounded-lg">بحث</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-100">
    <table class="w-full text-sm text-right">
        <thead class="bg-slate-50 text-xs text-slate-500 uppercase">
            <tr>
                <th class="px-4 py-3 font-medium">اسم الحملة</th>
                <th class="px-4 py-3 font-medium">الفرع</th>
                <th class="px-4 py-3 font-medium">الميزانية</th>
                <th class="px-4 py-3 font-medium">العملاء المحتملون</th>
                <th class="px-4 py-3 font-medium">المُحوّلون</th>
                <th class="px-4 py-3 font-medium">الفترة</th>
                <th class="px-4 py-3 font-medium">الحالة</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($campaigns as $c)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-800">{{ $c->name }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $c->branch?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $c->budget ? number_format($c->budget, 0) . ' ر.س' : '—' }}</td>
                <td class="px-4 py-3"><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $c->leads_count }}</span></td>
                <td class="px-4 py-3"><span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $c->converted_count }}</span></td>
                <td class="px-4 py-3 text-xs text-slate-500">
                    @if($c->start_date) {{ $c->start_date->format('Y-m-d') }} @else — @endif
                    @if($c->end_date) → {{ $c->end_date->format('Y-m-d') }} @endif
                </td>
                <td class="px-4 py-3">
                    @if($c->active)
                        <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-xs">نشطة</span>
                    @else
                        <span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full text-xs">متوقفة</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-left">
                    <a href="{{ route('admin.marketing.campaigns.show', $c) }}"
                       class="text-blue-600 hover:underline text-xs">عرض</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-10 text-center text-slate-400 text-sm">لا توجد حملات بعد</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $campaigns->links() }}</div>
@endsection
