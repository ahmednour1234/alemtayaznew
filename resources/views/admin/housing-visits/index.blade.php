@extends('admin.layouts.app')
@section('title', 'زيارات السكن')
@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">زيارات السكن</h2>
        <p class="text-sm text-slate-400 mt-1">توثيق زيارات السكن وملاحظات موظفين الفرع</p>
    </div>
    <div class="flex gap-2">
        @can('housing-visits.reports')
        <a href="{{ route('admin.housing-visits.reports') }}" class="border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm px-4 py-2 rounded-lg">التقرير</a>
        @endcan
        @can('housing-visits.create')
        <a href="{{ route('admin.housing-visits.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">+ إضافة زيارة</a>
        @endcan
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100 mb-4">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3">
        @if(!$branchId)
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">الفرع</label>
            <select name="branch_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">السكن</label>
            <select name="housing_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach($housings as $housing)
                <option value="{{ $housing->id }}" @selected(request('housing_id') == $housing->id)>{{ $housing->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">الموظف</label>
            <select name="employee_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach($employees as $employee)
                <option value="{{ $employee->id }}" @selected(request('employee_id') == $employee->id)>{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">من تاريخ</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">إلى تاريخ</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>

        <div class="flex items-end gap-2">
            <button class="bg-slate-800 hover:bg-slate-900 text-white text-sm px-5 py-2 rounded-lg flex-1">بحث</button>
            <a href="{{ route('admin.housing-visits.index') }}" class="border border-slate-200 text-slate-500 text-sm px-4 py-2 rounded-lg">مسح</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs">
            <tr>
                <th class="px-4 py-3 text-right font-medium">التاريخ</th>
                <th class="px-4 py-3 text-right font-medium">السكن</th>
                <th class="px-4 py-3 text-right font-medium">الفرع</th>
                <th class="px-4 py-3 text-right font-medium">موظفين الزيارة</th>
                <th class="px-4 py-3 text-right font-medium">التوثيق</th>
                <th class="px-4 py-3 text-right font-medium">إجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($visits as $visit)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-700">{{ $visit->visit_date?->format('Y-m-d') }}</td>
                <td class="px-4 py-3 font-semibold text-slate-800">{{ $visit->housing?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $visit->branch?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600">
                    {{ $visit->employees->pluck('name')->join('، ') ?: '—' }}
                </td>
                <td class="px-4 py-3">
                    @if($visit->documentation || $visit->documentation_file)
                    <span class="bg-green-50 text-green-700 text-xs px-2 py-1 rounded-full">موثق</span>
                    @else
                    <span class="bg-slate-100 text-slate-500 text-xs px-2 py-1 rounded-full">بدون توثيق</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.housing-visits.show', $visit) }}" class="text-blue-600 hover:underline text-xs">تفاصيل</a>
                        @can('housing-visits.edit')
                        <a href="{{ route('admin.housing-visits.edit', $visit) }}" class="text-amber-600 hover:underline text-xs">تعديل</a>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">لا توجد زيارات سكن</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-100">
        {{ $visits->withQueryString()->links() }}
    </div>
</div>
@endsection
