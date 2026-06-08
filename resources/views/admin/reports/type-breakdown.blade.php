@extends('admin.layouts.app')
@section('title', 'تقرير البنود المالية')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">تقرير الإيرادات والمصروفات حسب البند</h2>
    @if(isset($report))
    <div class="flex gap-2">
        <a href="{{ route('admin.reports.type-breakdown.excel', request()->query()) }}"
           class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg">تصدير Excel</a>
        <a href="{{ route('admin.reports.type-breakdown.pdf', request()->query()) }}"
           class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-lg">تصدير PDF</a>
    </div>
    @endif
</div>

{{-- نموذج الفلترة --}}
<div class="bg-white rounded-xl p-5 shadow-sm mb-6">
    <form method="GET" class="space-y-4">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">الفروع</label>
                <div class="grid grid-cols-2 gap-2 bg-slate-50 rounded-lg p-3">
                    @foreach($branches as $branch)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}"
                               {{ in_array($branch->id, request('branch_ids', [])) ? 'checked' : '' }} class="rounded">
                        {{ $branch->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">من تاريخ</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2.5 rounded-lg">عرض التقرير</button>
    </form>
</div>

@if(isset($report))

{{-- بطاقات ملخص --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5 border-r-4 border-green-500">
        <p class="text-sm text-slate-500">إجمالي الإيرادات</p>
        <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($report['income_total'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-r-4 border-red-500">
        <p class="text-sm text-slate-500">إجمالي المصروفات</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($report['expense_total'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-r-4 {{ $report['net'] >= 0 ? 'border-blue-500' : 'border-orange-500' }}">
        <p class="text-sm text-slate-500">الصافي</p>
        <p class="text-2xl font-bold mt-1 {{ $report['net'] >= 0 ? 'text-blue-700' : 'text-orange-700' }}">{{ number_format($report['net'], 2) }}</p>
    </div>
</div>

{{-- جدول الإيرادات حسب البند --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-4 py-3 bg-green-50 border-b font-semibold text-green-800">الإيرادات حسب البند</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs border-b">
                <tr>
                    <th class="px-4 py-3 text-right">البند</th>
                    <th class="px-4 py-3 text-right">عدد العمليات</th>
                    <th class="px-4 py-3 text-right">الإجمالي</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($report['income_rows'] as $row)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium">{{ $row['name'] }}</td>
                    <td class="px-4 py-3">{{ number_format($row['count']) }}</td>
                    <td class="px-4 py-3 font-semibold text-green-600">{{ number_format($row['total'], 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-4 text-center text-slate-400">لا توجد إيرادات</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-green-50 text-xs font-bold border-t">
                <tr>
                    <td class="px-4 py-2">الإجمالي</td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2 text-green-700">{{ number_format($report['income_total'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- جدول المصروفات حسب البند --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-4 py-3 bg-red-50 border-b font-semibold text-red-800">المصروفات حسب البند (المعتمدة)</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs border-b">
                <tr>
                    <th class="px-4 py-3 text-right">البند</th>
                    <th class="px-4 py-3 text-right">عدد العمليات</th>
                    <th class="px-4 py-3 text-right">الإجمالي</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($report['expense_rows'] as $row)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium">{{ $row['name'] }}</td>
                    <td class="px-4 py-3">{{ number_format($row['count']) }}</td>
                    <td class="px-4 py-3 font-semibold text-red-600">{{ number_format($row['total'], 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-4 text-center text-slate-400">لا توجد مصروفات</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-red-50 text-xs font-bold border-t">
                <tr>
                    <td class="px-4 py-2">الإجمالي</td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2 text-red-700">{{ number_format($report['expense_total'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endif

@endsection
