@extends('admin.layouts.app')
@section('title', 'عقود نقل الكفالة')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-slate-800">عقود نقل الكفالة</h2>
    @can('sponsorship-transfers.create')
    <a href="{{ route('admin.sponsorship-transfers.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        عقد جديد
    </a>
    @endcan
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl p-4 shadow-sm mb-4 border border-slate-100">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم العقد / اسم العاملة"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
        @php $_me = Auth::guard('admin')->user(); @endphp
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الفرع</label>
            @if($_me->isBranchAdmin())
                {{-- Branch-restricted: show locked badge --}}
                <div class="w-full border border-blue-200 bg-blue-50 rounded-lg px-3 py-2 text-sm text-blue-700 font-medium flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    {{ $_me->branch?->name ?? 'فرعي' }}
                </div>
            @else
                {{-- Super-admin / unrestricted: show dropdown --}}
                <select name="branch_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">الكل</option>
                    @foreach($branches as $br)
                    <option value="{{ $br->id }}" {{ request('branch_id') == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                    @endforeach
                </select>
            @endif
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">القسم</label>
            <select name="current_department" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach(\App\Models\SponsorshipTransfer::departments() as $val => $label)
                <option value="{{ $val }}" {{ request('current_department') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الحالة</label>
            <select name="current_status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach(\App\Models\SponsorshipTransfer::statuses() as $val => $label)
                <option value="{{ $val }}" {{ request('current_status') == $val ? 'selected' : '' }}>{{ $label['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">حالة الدفع</label>
            <select name="payment_status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach(\App\Models\SponsorshipTransfer::paymentStatuses() as $val => $label)
                <option value="{{ $val }}" {{ request('payment_status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button class="bg-slate-800 hover:bg-slate-900 text-white text-sm px-5 py-2 rounded-lg w-full">تصفية</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs">
            <tr>
                <th class="px-4 py-3 text-right font-medium">رقم العقد</th>
                <th class="px-4 py-3 text-right font-medium">العاملة</th>
                <th class="px-4 py-3 text-right font-medium">من عميل</th>
                <th class="px-4 py-3 text-right font-medium">إلى عميل</th>
                <th class="px-4 py-3 text-right font-medium">القسم</th>
                <th class="px-4 py-3 text-right font-medium">الحالة</th>
                <th class="px-4 py-3 text-right font-medium">الرسوم</th>
                <th class="px-4 py-3 text-right font-medium">إجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($transfers as $t)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $t->contract_number }}</td>
                <td class="px-4 py-3 font-medium text-slate-800">{{ $t->worker?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $t->fromClient?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-400">{{ $t->toClient?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $t->department_label }}</td>
                <td class="px-4 py-3">
                    <span class="inline-block text-xs px-2 py-0.5 rounded-full"
                          style="background:{{ $t->status_color }}20; color:{{ $t->status_color }}">
                        {{ $t->status_label }}
                    </span>
                </td>
                <td class="px-4 py-3 text-slate-700">{{ number_format($t->total_fees) }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.sponsorship-transfers.show', $t->id) }}" class="text-blue-600 hover:underline text-xs">تفاصيل</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-10 text-center text-slate-400">لا توجد عقود</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-100">
        {{ $transfers->withQueryString()->links() }}
    </div>
</div>
@endsection
