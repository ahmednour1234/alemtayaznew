@extends('admin.layouts.app')
@section('title', 'تقرير العقود المتأخرة')
@section('content')

<div class="flex justify-between items-center mb-5">
    <div>
        <h2 class="text-xl font-bold text-slate-800">تقرير العقود المتأخرة</h2>
        <p class="text-sm text-slate-500 mt-0.5">العقود التي تجاوزت المهلة المتوقعة في أي مرحلة من المراحل الخمس عشرة</p>
    </div>
    <span class="bg-red-100 text-red-700 text-sm font-semibold px-4 py-1.5 rounded-full">
        {{ $contracts->count() }} عقد متأخر
    </span>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        @auth('admin')
        @if(! Auth::guard('admin')->user()->isBranchAdmin())
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">الفرع</label>
            <select name="branch_id" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 min-w-44">
                <option value="">— كل الفروع —</option>
                @foreach($branches as $br)
                <option value="{{ $br->id }}" {{ $branchId == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        @endauth
        <div class="flex gap-2">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm px-5 py-2 rounded-lg">عرض</button>
            <a href="{{ route('admin.reports.contracts-delayed') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-4 py-2 rounded-lg">مسح</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-x-auto">
    <table class="w-full text-sm text-right">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-4 py-3 font-semibold text-slate-600">رقم العقد</th>
                <th class="px-4 py-3 font-semibold text-slate-600">العميل</th>
                <th class="px-4 py-3 font-semibold text-slate-600">العاملة</th>
                <th class="px-4 py-3 font-semibold text-slate-600">الجنسية</th>
                <th class="px-4 py-3 font-semibold text-slate-600">الفرع</th>
                <th class="px-4 py-3 font-semibold text-slate-600">الحالة الحالية</th>
                <th class="px-4 py-3 font-semibold text-slate-600">الأيام المتوقعة</th>
                <th class="px-4 py-3 font-semibold text-slate-600">الأيام الفعلية</th>
                <th class="px-4 py-3 font-semibold text-slate-600">التأخير</th>
                <th class="px-4 py-3 font-semibold text-slate-600">إجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($contracts as $c)
            @php
                $delayColor = $c->delay_days >= 14
                    ? 'bg-red-600 text-white'
                    : ($c->delay_days >= 7 ? 'bg-orange-500 text-white' : 'bg-yellow-400 text-slate-800');
            @endphp
            <tr class="hover:bg-slate-50 transition">
                <td class="px-4 py-3">
                    <a href="{{ route('admin.contracts.show', $c->id) }}" class="font-mono text-blue-600 hover:underline">{{ $c->contract_number }}</a>
                    @if($c->musaned_number)
                    <div class="text-xs text-slate-400">مساند: {{ $c->musaned_number }}</div>
                    @endif
                </td>
                <td class="px-4 py-3 font-medium text-slate-700">{{ $c->client->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $c->worker->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500 text-xs">{{ $c->worker->nationality->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $c->branch->name ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2.5 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                        {{ $c->status_label }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center text-slate-500">{{ $c->expected_days }} يوم</td>
                <td class="px-4 py-3 text-center text-slate-600 font-medium">{{ $c->days_in_status }} يوم</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold {{ $delayColor }}">
                        +{{ $c->delay_days }} يوم
                    </span>
                </td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.contracts.show', $c->id) }}" class="text-blue-600 hover:text-blue-800 text-xs">عرض</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="px-4 py-10 text-center text-slate-400 text-sm">لا توجد عقود متأخرة — كل العقود في موعدها ✓</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Legend --}}
@if($contracts->isNotEmpty())
<div class="flex gap-4 mt-4 text-xs text-slate-500">
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span> تأخير 2–6 أيام</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-orange-500 inline-block"></span> تأخير 7–13 يوم</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-600 inline-block"></span> تأخير 14+ يوم (حرج)</span>
</div>
@endif

@endsection
