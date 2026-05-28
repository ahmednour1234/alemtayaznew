@extends('admin.layouts.app')
@section('title', 'العملاء المحتملون')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">العملاء المحتملون</h2>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<!-- Filters -->
<div class="bg-white rounded-xl p-5 shadow-sm mb-4 border border-slate-100">
    <form method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم أو جوال"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الحالة</label>
            <select name="status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach($statuses as $k => $s)
                    <option value="{{ $k }}" {{ request('status') === $k ? 'selected' : '' }}>{{ $s['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الحملة</label>
            <select name="campaign_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach($campaigns as $c)
                    <option value="{{ $c->id }}" {{ request('campaign_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الفرع</label>
            <select name="branch_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button class="bg-slate-800 hover:bg-slate-900 text-white text-sm px-5 py-2 rounded-lg w-full">تصفية</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-100">
    <table class="w-full text-sm text-right">
        <thead class="bg-slate-50 text-xs text-slate-500">
            <tr>
                <th class="px-4 py-3 font-medium">الاسم</th>
                <th class="px-4 py-3 font-medium">الجوال</th>
                <th class="px-4 py-3 font-medium">المدينة</th>
                <th class="px-4 py-3 font-medium">الجنسية</th>
                <th class="px-4 py-3 font-medium">الحملة</th>
                <th class="px-4 py-3 font-medium">الفرع</th>
                <th class="px-4 py-3 font-medium">آخر مكالمة</th>
                <th class="px-4 py-3 font-medium">الحالة</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($leads as $lead)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-800">{{ $lead->name }}</td>
                <td class="px-4 py-3 text-slate-600 font-mono text-xs">{{ $lead->phone ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $lead->city ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $lead->nationality?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600 text-xs">{{ $lead->campaign?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600 text-xs">{{ $lead->branch?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-xs text-slate-500">
                    {{ $lead->last_contacted_at?->diffForHumans() ?? '—' }}
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statuses[$lead->status]['color'] }}">
                        {{ $statuses[$lead->status]['label'] }}
                    </span>
                </td>
                <td class="px-4 py-3 text-left">
                    <a href="{{ route('admin.marketing.leads.show', $lead) }}" class="text-blue-600 hover:underline text-xs">عرض</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="px-4 py-10 text-center text-slate-400 text-sm">لا يوجد عملاء محتملون</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $leads->links() }}</div>
@endsection
