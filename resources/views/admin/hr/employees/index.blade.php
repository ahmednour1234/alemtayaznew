@extends('admin.layouts.app')
@section('title', 'الموظفين')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-slate-800">الموظفين</h2>
    @can('employees.create')
    <a href="{{ route('admin.hr.employees.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        موظف جديد
    </a>
    @endcan
</div>

<div class="bg-white rounded-xl p-4 shadow-sm mb-4 border border-slate-100">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="الاسم / رقم الموظف / الإقامة"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
        @php $_me = Auth::guard('admin')->user(); @endphp
        @unless($_me->isBranchAdmin())
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الفرع</label>
            <select name="branch_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach($branches as $br)
                <option value="{{ $br->id }}" {{ request('branch_id') == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                @endforeach
            </select>
        </div>
        @endunless
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الحالة</label>
            <select name="status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach(\App\Models\Employee::statuses() as $val => $meta)
                <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">نقل الكفالة</label>
            <select name="transferred_in" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                <option value="1" {{ request('transferred_in') === '1' ? 'selected' : '' }}>نُقل إلينا</option>
                <option value="0" {{ request('transferred_in') === '0' ? 'selected' : '' }}>لا</option>
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
                <th class="px-4 py-3 text-right font-medium">الاسم</th>
                <th class="px-4 py-3 text-right font-medium">المسمى</th>
                <th class="px-4 py-3 text-right font-medium">رقم الإقامة</th>
                <th class="px-4 py-3 text-right font-medium">تجديد الإقامة</th>
                <th class="px-4 py-3 text-right font-medium">الحالة</th>
                <th class="px-4 py-3 text-right font-medium">نُقل إلينا</th>
                <th class="px-4 py-3 text-right font-medium">إجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($employees as $e)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-800">
                    {{ $e->name }}
                    @if($e->employee_no)<span class="text-xs text-slate-400 font-mono block">{{ $e->employee_no }}</span>@endif
                </td>
                <td class="px-4 py-3 text-slate-500">{{ $e->job_title ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $e->iqama_number ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600">
                    @if($e->iqama_expiry_date)
                        {{ $e->iqama_expiry_date->format('Y-m-d') }}
                        @php $d = $e->iqama_days_left; @endphp
                        @if($d !== null && $d <= 30)
                            <span class="block text-xs {{ $d < 0 ? 'text-red-600' : 'text-amber-600' }}">
                                {{ $d < 0 ? 'منتهية منذ ' . abs($d) . ' يوم' : 'باقي ' . $d . ' يوم' }}
                            </span>
                        @endif
                    @else — @endif
                </td>
                <td class="px-4 py-3">
                    <span class="inline-block text-xs px-2 py-0.5 rounded-full"
                          style="background:{{ $e->status_color }}20; color:{{ $e->status_color }}">{{ $e->status_label }}</span>
                </td>
                <td class="px-4 py-3 text-slate-500">{{ $e->sponsorship_transferred_in ? 'نعم' : '—' }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.hr.employees.show', $e->id) }}" class="text-blue-600 hover:underline text-xs">تفاصيل</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">لا يوجد موظفون</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-100">{{ $employees->links() }}</div>
</div>
@endsection
