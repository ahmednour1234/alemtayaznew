@extends('admin.layouts.app')
@section('title', 'تقرير العمالة المستلمة')
@section('content')

<div class="flex justify-between items-center mb-5">
    <div>
        <h2 class="text-xl font-bold text-slate-800">تقرير العمالة المستلمة</h2>
        <p class="text-sm text-slate-500 mt-0.5">العقود التي وصلت إلى مرحلة «تم الاستلام» (المرحلة 13)</p>
    </div>
    <span class="bg-green-100 text-green-700 text-sm font-semibold px-4 py-1.5 rounded-full">
        {{ $contracts->count() }} عقد
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
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">تاريخ الاستلام من</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">إلى</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg">عرض</button>
            <a href="{{ route('admin.reports.contracts-received') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-4 py-2 rounded-lg">مسح</a>
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
                <th class="px-4 py-3 font-semibold text-slate-600">تاريخ الطلب</th>
                <th class="px-4 py-3 font-semibold text-slate-600">تاريخ الاستلام</th>
                <th class="px-4 py-3 font-semibold text-slate-600">الدفع</th>
                <th class="px-4 py-3 font-semibold text-slate-600">إجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($contracts as $c)
            @php
                $receivedHistory = $c->statusHistories->firstWhere('status', 13);
                $payColors = ['full' => 'bg-green-100 text-green-700', 'partial' => 'bg-yellow-100 text-yellow-700', 'pending' => 'bg-slate-100 text-slate-500'];
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
                <td class="px-4 py-3 text-slate-400 text-xs">{{ $c->request_date?->format('Y/m/d') ?? '—' }}</td>
                <td class="px-4 py-3 text-green-600 text-xs font-medium">
                    {{ $receivedHistory?->status_date?->format('Y/m/d') ?? '—' }}
                </td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2.5 py-1 rounded-full text-xs {{ $payColors[$c->payment_status] ?? '' }}">
                        {{ $c->payment_label }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.contracts.show', $c->id) }}" class="text-blue-600 hover:text-blue-800 text-xs">عرض</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-4 py-10 text-center text-slate-400 text-sm">لا توجد عقود مستلمة بهذه الفلاتر</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
