@extends('admin.layouts.app')
@section('title', 'تقرير زيارات السكن')
@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <a href="{{ route('admin.housing-visits.index') }}" class="text-slate-500 hover:text-slate-700 text-sm">عودة إلى زيارات السكن</a>
        <h2 class="text-xl font-bold text-slate-800 mt-2">تقرير زيارات السكن</h2>
    </div>
    @can('housing-visits.create')
    <a href="{{ route('admin.housing-visits.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">+ إضافة زيارة</a>
    @endcan
</div>

<div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100 mb-5">
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
            <button class="bg-slate-800 hover:bg-slate-900 text-white text-sm px-5 py-2 rounded-lg flex-1">عرض</button>
            <a href="{{ route('admin.housing-visits.reports') }}" class="border border-slate-200 text-slate-500 text-sm px-4 py-2 rounded-lg">مسح</a>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
    <div class="bg-white border border-slate-100 rounded-xl p-5 shadow-sm">
        <div class="text-xs text-slate-400 mb-2">إجمالي الزيارات</div>
        <div class="text-2xl font-bold text-slate-800">{{ number_format($total) }}</div>
    </div>
    <div class="bg-white border border-slate-100 rounded-xl p-5 shadow-sm">
        <div class="text-xs text-slate-400 mb-2">زيارات موثقة</div>
        <div class="text-2xl font-bold text-green-700">{{ number_format($withDocumentation) }}</div>
    </div>
    <div class="bg-white border border-slate-100 rounded-xl p-5 shadow-sm">
        <div class="text-xs text-slate-400 mb-2">موظفين شاركوا</div>
        <div class="text-2xl font-bold text-blue-700">{{ number_format($visitingEmployees) }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-800 mb-4">توزيع الزيارات حسب الفرع</h3>
        <div class="space-y-3">
            @forelse($byBranch as $row)
            <div class="flex justify-between text-sm border-b border-slate-100 pb-2">
                <span class="text-slate-600">{{ $row->branch?->name ?? '—' }}</span>
                <span class="font-bold text-slate-800">{{ $row->visits_count }}</span>
            </div>
            @empty
            <div class="text-sm text-slate-400">لا توجد بيانات</div>
            @endforelse
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs">
                <tr>
                    <th class="px-4 py-3 text-right font-medium">التاريخ</th>
                    <th class="px-4 py-3 text-right font-medium">السكن</th>
                    <th class="px-4 py-3 text-right font-medium">الفرع</th>
                    <th class="px-4 py-3 text-right font-medium">الموظفين</th>
                    <th class="px-4 py-3 text-right font-medium">التوثيق</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($visits as $visit)
                <tr>
                    <td class="px-4 py-3">{{ $visit->visit_date?->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $visit->housing?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $visit->branch?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $visit->employees->pluck('name')->join('، ') ?: '—' }}</td>
                    <td class="px-4 py-3">
                        @if($visit->documentation || $visit->documentation_file)
                        <span class="bg-green-50 text-green-700 text-xs px-2 py-1 rounded-full">موثق</span>
                        @else
                        <span class="bg-slate-100 text-slate-500 text-xs px-2 py-1 rounded-full">بدون توثيق</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">لا توجد بيانات</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $visits->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
