@extends('admin.layouts.app')
@section('title', 'تقرير التسويات')
@section('content')

<div class="flex justify-between items-center mb-5">
    <div>
        <h2 class="text-xl font-bold text-slate-800">تقرير التسويات</h2>
        <p class="text-sm text-slate-500 mt-0.5">سجلات التسويات المالية والعقدية والنزاعات للعاملات</p>
    </div>
    <span class="bg-emerald-100 text-emerald-700 text-sm font-semibold px-4 py-1.5 rounded-full">
        {{ $settlements->count() }} تسوية
    </span>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        @if($branches->isNotEmpty())
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">الفرع</label>
            <select name="branch_id" class="border border-slate-200 rounded-lg px-3 py-2 text-sm min-w-44">
                <option value="">— كل الفروع —</option>
                @foreach($branches as $br)
                <option value="{{ $br->id }}" {{ $branchId == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">من تاريخ</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">إلى تاريخ</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-5 py-2 rounded-lg">عرض</button>
            <a href="{{ route('admin.reports.housing-settlements') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-4 py-2 rounded-lg">مسح</a>
        </div>
    </form>
</div>

{{-- Summary --}}
<div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 mb-5 flex items-center gap-3">
    <span class="text-sm text-slate-500">إجمالي مبالغ التسويات:</span>
    <span class="text-lg font-bold text-emerald-700">{{ number_format($totalSettlement, 2) }} ريال</span>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-x-auto">
    <table class="w-full text-sm text-right">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-4 py-3 font-semibold text-slate-600">العاملة</th>
                <th class="px-4 py-3 font-semibold text-slate-600">الجنسية</th>
                <th class="px-4 py-3 font-semibold text-slate-600">حالة العمالة</th>
                <th class="px-4 py-3 font-semibold text-slate-600">الضمان</th>
                <th class="px-4 py-3 font-semibold text-slate-600">العميل</th>
                <th class="px-4 py-3 font-semibold text-slate-600">الفرع</th>
                <th class="px-4 py-3 font-semibold text-slate-600">رقم المرجع</th>
                <th class="px-4 py-3 font-semibold text-slate-600">نوع التسوية</th>
                <th class="px-4 py-3 font-semibold text-slate-600">مبلغ التسوية</th>
                <th class="px-4 py-3 font-semibold text-slate-600">تاريخ التسوية</th>
                <th class="px-4 py-3 font-semibold text-slate-600">المستند</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($settlements as $s)
            @php
                $types   = \App\Models\HousingSettlement::types();
                $ws      = \App\Models\HousingAssignment::workerStatuses()[$s->assignment?->worker_status ?? 'normal'] ?? ['label'=>'نظامية','bg'=>'#dcfce7','color'=>'#16a34a'];
                $inGuar  = $s->assignment?->isInGuaranteePeriod() ?? false;
            @endphp
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-800">{{ $s->worker?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $s->worker?->nationality?->name ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span style="background:{{ $ws['bg'] }};color:{{ $ws['color'] }};font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;display:inline-block;">{{ $ws['label'] }}</span>
                </td>
                <td class="px-4 py-3">
                    @if($inGuar)
                    <span style="background:#ede9fe;color:#7c3aed;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;display:inline-block;">⏱ ضمان</span>
                    @else
                    <span class="text-slate-300 text-xs">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-slate-700">
                    <div>{{ $s->client?->name ?? '—' }}</div>
                    @if($s->client?->phone)
                    <a href="tel:{{ $s->client->phone }}" class="text-xs text-slate-400" dir="ltr">{{ $s->client->phone }}</a>
                    @endif
                </td>
                <td class="px-4 py-3 text-slate-500">{{ $s->branch?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $s->reference_number ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="inline-block bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-full">
                        {{ $types[$s->settlement_type] ?? $s->settlement_type ?? '—' }}
                    </span>
                </td>
                <td class="px-4 py-3 font-semibold text-emerald-700">{{ number_format($s->settlement_amount, 2) }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $s->settlement_date?->format('Y-m-d') ?? '—' }}</td>
                <td class="px-4 py-3">
                    @if($s->document_image)
                    <a href="{{ file_url($s->document_image) }}" target="_blank" class="text-blue-600 hover:underline text-xs">عرض</a>
                    @else
                    <span class="text-slate-300 text-xs">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="11" class="px-4 py-10 text-center text-slate-400">لا توجد سجلات</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
