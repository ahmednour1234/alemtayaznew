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
        <h3 class="text-sm font-semibold text-slate-700">
            العملاء المحتملون
            @php $unassignedCount = $campaign->leads()->whereNull('assigned_admin_id')->whereIn('status',['new','in_progress'])->count(); @endphp
            @if($unassignedCount > 0)
                <span class="ms-2 px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs font-medium">{{ $unassignedCount }} غير موزّع</span>
            @endif
        </h3>
        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('admin.marketing.campaigns.reassign-unassigned', $campaign) }}">
                @csrf
                <button type="submit"
                        onclick="return confirm('إعادة توزيع جميع العملاء المحتملين على موظفي خدمة العملاء؟')"
                        class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg shadow-sm transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 3M21 7.5H7.5" />
                    </svg>
                    إعادة توزيع
                </button>
            </form>
            <a href="{{ route('admin.marketing.leads.index', ['campaign_id' => $campaign->id]) }}"
               class="text-xs text-blue-600 hover:underline">عرض الكل</a>
        </div>
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
                <td class="px-4 py-2.5 font-mono text-xs">
                    @if($lead->phone)
                        @php $wa = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $lead->phone); @endphp
                        <a href="{{ $wa }}" target="_blank" class="text-green-600 hover:text-green-800 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            {{ $lead->phone }}
                        </a>
                    @else —
                    @endif
                </td>
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
