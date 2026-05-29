@extends('admin.layouts.app')
@section('title', 'تقارير نقل الكفالة')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-slate-800">تقارير نقل الكفالة</h2>
    <a href="{{ route('admin.sponsorship-transfers.index') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h8"/>
        </svg>
        قائمة العقود
    </a>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl p-4 shadow-sm mb-6 border border-slate-100">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
        @unless($branchId)
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الفرع</label>
            <div class="relative">
                <select name="branch_id"
                        class="w-full appearance-none border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-300 transition">
                    <option value="">الكل</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </span>
            </div>
        </div>
        @endunless
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">حالة الدفع</label>
            <div class="relative">
                <select name="payment_status"
                        class="w-full appearance-none border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-300 transition">
                    <option value="">الكل</option>
                    @foreach($paymentLabels as $val => $lbl)
                    <option value="{{ $val }}" {{ request('payment_status') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </span>
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">من تاريخ</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-300 transition">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">إلى تاريخ</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-300 transition">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit"
                    class="flex-1 bg-slate-800 hover:bg-slate-900 text-white text-sm px-5 py-2 rounded-lg transition flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
                بحث
            </button>
            @if(request()->hasAny(['branch_id','payment_status','date_from','date_to']))
            <a href="{{ route('admin.sponsorship-transfers.reports') }}"
               class="text-xs text-slate-400 hover:text-slate-600 whitespace-nowrap flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                مسح
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
        <p class="text-xs text-slate-500 mb-1">إجمالي العقود</p>
        <p class="text-2xl font-bold text-slate-800">{{ $total }}</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
        <p class="text-xs text-slate-500 mb-1">إجمالي الرسوم</p>
        <p class="text-2xl font-bold text-emerald-600">{{ number_format($totalFees, 2) }} <span class="text-sm font-normal text-slate-400">ر.س</span></p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
        <p class="text-xs text-slate-500 mb-1">رسوم الخدمة</p>
        <p class="text-2xl font-bold text-blue-600">{{ number_format($totalServiceFee, 2) }} <span class="text-sm font-normal text-slate-400">ر.س</span></p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
        <p class="text-xs text-slate-500 mb-1">إجمالي الخسائر</p>
        <p class="text-2xl font-bold text-red-500">{{ number_format($totalLoss, 2) }} <span class="text-sm font-normal text-slate-400">ر.س</span></p>
    </div>
</div>

{{-- Breakdown panels --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    {{-- By Status --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700">توزيع حسب الحالة</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs text-slate-500">
                <tr>
                    <th class="px-4 py-2.5 text-right font-medium">الحالة</th>
                    <th class="px-4 py-2.5 text-center font-medium">العدد</th>
                    <th class="px-4 py-2.5 text-center font-medium">النسبة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($statuses as $key => $status)
                @php $cnt = $statusCounts[$key] ?? 0; $pct = $total ? round($cnt/$total*100) : 0; @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2.5">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium"
                              style="color:{{ $status['color'] }}">
                            <span class="w-2 h-2 rounded-full inline-block" style="background:{{ $status['color'] }}"></span>
                            {{ $status['label'] }}
                        </span>
                    </td>
                    <td class="px-4 py-2.5 text-center font-semibold text-slate-700">{{ $cnt }}</td>
                    <td class="px-4 py-2.5 text-center text-slate-400 text-xs">{{ $pct }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- By Payment Status --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700">توزيع حسب حالة الدفع</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs text-slate-500">
                <tr>
                    <th class="px-4 py-2.5 text-right font-medium">حالة الدفع</th>
                    <th class="px-4 py-2.5 text-center font-medium">العدد</th>
                    <th class="px-4 py-2.5 text-center font-medium">النسبة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($paymentLabels as $key => $lbl)
                @php
                    $cnt = $paymentCounts[$key] ?? 0;
                    $pct = $total ? round($cnt/$total*100) : 0;
                    $pColors = ['pending'=>'#f59e0b','partial'=>'#3b82f6','full'=>'#16a34a'];
                    $c = $pColors[$key] ?? '#64748b';
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2.5">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium" style="color:{{ $c }}">
                            <span class="w-2 h-2 rounded-full inline-block" style="background:{{ $c }}"></span>
                            {{ $lbl }}
                        </span>
                    </td>
                    <td class="px-4 py-2.5 text-center font-semibold text-slate-700">{{ $cnt }}</td>
                    <td class="px-4 py-2.5 text-center text-slate-400 text-xs">{{ $pct }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- By Department --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700">توزيع حسب القسم</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs text-slate-500">
                <tr>
                    <th class="px-4 py-2.5 text-right font-medium">القسم</th>
                    <th class="px-4 py-2.5 text-center font-medium">العدد</th>
                    <th class="px-4 py-2.5 text-center font-medium">النسبة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($deptLabels as $key => $lbl)
                @php
                    $cnt = $deptCounts[$key] ?? 0;
                    $pct = $total ? round($cnt/$total*100) : 0;
                    $dColors = ['customer_service'=>'#6366f1','accounts'=>'#c9a84c'];
                    $c = $dColors[$key] ?? '#64748b';
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2.5">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium" style="color:{{ $c }}">
                            <span class="w-2 h-2 rounded-full inline-block" style="background:{{ $c }}"></span>
                            {{ $lbl }}
                        </span>
                    </td>
                    <td class="px-4 py-2.5 text-center font-semibold text-slate-700">{{ $cnt }}</td>
                    <td class="px-4 py-2.5 text-center text-slate-400 text-xs">{{ $pct }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- By Branch Table --}}
@unless($branchId)
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-700">تفصيل حسب الفرع</h3>
        <span class="text-xs text-slate-400">{{ $byBranch->count() }} فروع</span>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs">
            <tr>
                <th class="px-4 py-3 text-right font-medium">الفرع</th>
                <th class="px-4 py-3 text-center font-medium">عدد العقود</th>
                <th class="px-4 py-3 text-center font-medium">إجمالي الرسوم</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($byBranch as $row)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-800">{{ $row->branch?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-center text-slate-600">{{ $row->cnt }}</td>
                <td class="px-4 py-3 text-center font-semibold text-emerald-600">{{ number_format($row->fees, 2) }} <span class="text-xs font-normal text-slate-400">ر.س</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-4 py-10 text-center text-slate-400">لا توجد بيانات</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endunless

@endsection