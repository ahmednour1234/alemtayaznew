@extends('admin.layouts.app')
@section('title', 'تقرير العاملات المؤجَّرة')
@section('content')

<div class="flex justify-between items-center mb-5">
    <div>
        <h2 class="text-xl font-bold text-slate-800">تقرير العاملات المؤجَّرة</h2>
        <p class="text-sm text-slate-500 mt-0.5">العاملات اللاتي تم تأجيرهن لكفلاء/جهات مع تفاصيل العقود والقيم</p>
    </div>
    <span class="bg-cyan-100 text-cyan-700 text-sm font-semibold px-4 py-1.5 rounded-full">
        {{ $rentals->count() }} عاملة مؤجَّرة
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
            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-5 py-2 rounded-lg">عرض</button>
            <a href="{{ route('admin.reports.housing-rentals') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-4 py-2 rounded-lg">مسح</a>
        </div>
    </form>
</div>

{{-- Summary --}}
<div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 mb-5 flex items-center gap-3">
    <span class="text-sm text-slate-500">إجمالي قيمة الإيجارات:</span>
    <span class="text-lg font-bold text-cyan-700">{{ number_format($totalRent, 2) }} ريال</span>
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
                <th class="px-4 py-3 font-semibold text-slate-600">العميل (المستأجر)</th>
                <th class="px-4 py-3 font-semibold text-slate-600">الفرع</th>
                <th class="px-4 py-3 font-semibold text-slate-600">رقم العقد</th>
                <th class="px-4 py-3 font-semibold text-slate-600">قيمة الإيجار</th>
                <th class="px-4 py-3 font-semibold text-slate-600">بداية الإيجار</th>
                <th class="px-4 py-3 font-semibold text-slate-600">انتهاء الإيجار</th>
                <th class="px-4 py-3 font-semibold text-slate-600">العقد</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($rentals as $r)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-800">{{ $r->worker?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $r->worker?->nationality?->name ?? '—' }}</td>
                @php
                    $ws      = \App\Models\HousingAssignment::workerStatuses()[$r->assignment?->worker_status ?? 'normal'] ?? ['label'=>'نظامية','bg'=>'#dcfce7','color'=>'#16a34a'];
                    $inGuar  = $r->assignment?->isInGuaranteePeriod() ?? false;
                @endphp
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
                    <div>{{ $r->client?->name ?? '—' }}</div>
                    @if($r->client?->phone)
                    <a href="tel:{{ $r->client->phone }}" class="text-xs text-slate-400" dir="ltr">{{ $r->client->phone }}</a>
                    @endif
                </td>
                <td class="px-4 py-3 text-slate-500">{{ $r->branch?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $r->contract_number ?? '—' }}</td>
                <td class="px-4 py-3 font-semibold text-cyan-700">{{ number_format($r->rent_value, 2) }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $r->rent_start_date?->format('Y-m-d') ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $r->rent_end_date?->format('Y-m-d') ?? '—' }}</td>
                <td class="px-4 py-3">
                    @if($r->contract_image)
                    <a href="{{ asset('storage/'.$r->contract_image) }}" target="_blank" class="text-blue-600 hover:underline text-xs">عرض</a>
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
