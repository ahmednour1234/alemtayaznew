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
        <div class="flex items-end gap-2">
            <button class="bg-slate-800 hover:bg-slate-900 text-white text-sm px-5 py-2 rounded-lg flex-1">تصفية</button>
            <a href="{{ request()->fullUrlWithQuery(['assigned_to_me' => request('assigned_to_me') ? '' : '1']) }}"
               class="text-xs px-3 py-2 rounded-lg border whitespace-nowrap {{ request('assigned_to_me') ? 'bg-blue-600 text-white border-blue-600' : 'border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                المسندة لي
            </a>
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
                <td class="px-4 py-3 font-mono text-xs">
                    @if($lead->phone)
                        @php $wa = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $lead->phone); @endphp
                        <a href="{{ $wa }}" target="_blank" class="text-green-600 hover:text-green-800 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            {{ $lead->phone }}
                        </a>
                    @else
                        —
                    @endif
                </td>
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
