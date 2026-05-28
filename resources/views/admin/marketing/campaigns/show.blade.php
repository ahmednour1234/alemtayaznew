@extends('admin.layouts.app')
@section('title', $campaign->name)
@section('content')

<div class="flex items-center gap-3 mb-5">
    <a href="{{ route('admin.marketing.campaigns.index') }}" class="text-slate-400 hover:text-slate-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
    <div class="flex-1">
        <h2 class="text-xl font-bold text-slate-800">{{ $campaign->name }}</h2>
        <p class="text-xs text-slate-500 mt-0.5">
            @if($campaign->branch) فرع: {{ $campaign->branch->name }} • @endif
            أنشأها: {{ $campaign->admin?->name }}
        </p>
    </div>
    <a href="{{ route('admin.marketing.campaigns.edit', $campaign) }}"
       class="bg-amber-500 hover:bg-amber-600 text-white text-sm px-4 py-2 rounded-lg">تعديل</a>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
    @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
</div>
@endif

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-5">
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-xs text-slate-400">إجمالي</p>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-blue-50 rounded-xl p-4">
        <p class="text-xs text-blue-500">جدد</p>
        <p class="text-2xl font-bold text-blue-700">{{ $stats['new'] }}</p>
    </div>
    <div class="bg-amber-50 rounded-xl p-4">
        <p class="text-xs text-amber-600">قيد المتابعة</p>
        <p class="text-2xl font-bold text-amber-700">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="bg-green-50 rounded-xl p-4">
        <p class="text-xs text-green-600">مُحوّلون</p>
        <p class="text-2xl font-bold text-green-700">{{ $stats['converted'] }}</p>
    </div>
    <div class="bg-slate-50 rounded-xl p-4">
        <p class="text-xs text-slate-500">مؤرشف</p>
        <p class="text-2xl font-bold text-slate-700">{{ $stats['archived'] }}</p>
    </div>
</div>

<!-- Sheet Import -->
<div class="bg-white rounded-xl shadow-sm p-5 mb-5 border border-slate-100">
    <h3 class="text-base font-semibold text-slate-700 mb-3">استيراد من Google Sheets</h3>
    <form method="POST" action="{{ route('admin.marketing.campaigns.import-sheet', $campaign) }}" class="flex gap-2">
        @csrf
        <input type="url" name="sheet_url" value="{{ $campaign->sheet_url }}" required
               placeholder="https://docs.google.com/spreadsheets/d/..."
               class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-5 rounded-lg">جلب البيانات</button>
    </form>
    <p class="text-xs text-slate-400 mt-2">الأعمدة المتوقعة: A=الاسم, B=الجوال, C=المدينة, D=الجنسية. يجب أن يكون الشيت مشاركاً للعموم.</p>
</div>

<!-- Leads list -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-100">
    <div class="px-5 py-3 border-b border-slate-100 flex justify-between items-center">
        <h3 class="text-sm font-semibold text-slate-700">العملاء المحتملون</h3>
        <a href="{{ route('admin.marketing.leads.index', ['campaign_id' => $campaign->id]) }}"
           class="text-xs text-blue-600 hover:underline">عرض الكل</a>
    </div>
    @php $statuses = \App\Models\Lead::statuses(); @endphp
    <table class="w-full text-sm text-right">
        <thead class="bg-slate-50 text-xs text-slate-500">
            <tr>
                <th class="px-4 py-2 font-medium">الاسم</th>
                <th class="px-4 py-2 font-medium">الجوال</th>
                <th class="px-4 py-2 font-medium">المدينة</th>
                <th class="px-4 py-2 font-medium">الجنسية</th>
                <th class="px-4 py-2 font-medium">المُسؤول</th>
                <th class="px-4 py-2 font-medium">الحالة</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($campaign->leads->take(20) as $lead)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-2.5 font-medium text-slate-800">{{ $lead->name }}</td>
                <td class="px-4 py-2.5 text-slate-600 font-mono text-xs">{{ $lead->phone ?? '—' }}</td>
                <td class="px-4 py-2.5 text-slate-600">{{ $lead->city ?? '—' }}</td>
                <td class="px-4 py-2.5 text-slate-600">{{ $lead->nationality?->name ?? '—' }}</td>
                <td class="px-4 py-2.5 text-slate-600">{{ $lead->assignedAdmin?->name ?? '—' }}</td>
                <td class="px-4 py-2.5">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statuses[$lead->status]['color'] }}">
                        {{ $statuses[$lead->status]['label'] }}
                    </span>
                </td>
                <td class="px-4 py-2.5 text-left">
                    <a href="{{ route('admin.marketing.leads.show', $lead) }}" class="text-blue-600 hover:underline text-xs">عرض</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400 text-sm">لا يوجد عملاء محتملون بعد. استورد من Google Sheets أعلاه.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
