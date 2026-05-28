@extends('admin.layouts.app')
@section('title', 'تقارير التسويق')
@section('content')

<h2 class="text-xl font-bold text-slate-800 mb-5">تقارير الحملات التسويقية</h2>

<!-- Totals -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-xs text-slate-400">إجمالي الميزانية</p>
        <p class="text-2xl font-bold text-slate-800">{{ number_format($totals['budget'], 0) }} <span class="text-sm text-slate-400">ر.س</span></p>
    </div>
    <div class="bg-blue-50 rounded-xl p-4">
        <p class="text-xs text-blue-500">إجمالي العملاء المحتملين</p>
        <p class="text-2xl font-bold text-blue-700">{{ $totals['leads'] }}</p>
    </div>
    <div class="bg-amber-50 rounded-xl p-4">
        <p class="text-xs text-amber-600">المحوَّلون لعملاء</p>
        <p class="text-2xl font-bold text-amber-700">{{ $totals['converted'] }}</p>
    </div>
    <div class="bg-green-50 rounded-xl p-4">
        <p class="text-xs text-green-600">عقود تم إبرامها</p>
        <p class="text-2xl font-bold text-green-700">{{ $totals['contracts'] }}</p>
    </div>
</div>

<!-- Per campaign table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-100">
    <table class="w-full text-sm text-right">
        <thead class="bg-slate-50 text-xs text-slate-500">
            <tr>
                <th class="px-4 py-3 font-medium">الحملة</th>
                <th class="px-4 py-3 font-medium">الفرع</th>
                <th class="px-4 py-3 font-medium">الميزانية</th>
                <th class="px-4 py-3 font-medium">العملاء</th>
                <th class="px-4 py-3 font-medium">قيد المتابعة</th>
                <th class="px-4 py-3 font-medium">المُحوَّلون</th>
                <th class="px-4 py-3 font-medium">العقود</th>
                <th class="px-4 py-3 font-medium">تكلفة العميل</th>
                <th class="px-4 py-3 font-medium">تكلفة العقد</th>
                <th class="px-4 py-3 font-medium">معدل التحويل</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($campaigns as $c)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-800">{{ $c->name }}</td>
                <td class="px-4 py-3 text-slate-600 text-xs">{{ $c->branch?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $c->budget ? number_format($c->budget, 0) . ' ر.س' : '—' }}</td>
                <td class="px-4 py-3 text-center"><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $c->leads_count }}</span></td>
                <td class="px-4 py-3 text-center text-amber-700">{{ $c->in_progress_count }}</td>
                <td class="px-4 py-3 text-center"><span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $c->converted_count }}</span></td>
                <td class="px-4 py-3 text-center"><span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $c->contracts_count }}</span></td>
                <td class="px-4 py-3 text-slate-600 text-xs">{{ $c->cost_per_lead ? number_format($c->cost_per_lead, 0) . ' ر.س' : '—' }}</td>
                <td class="px-4 py-3 text-slate-600 text-xs">{{ $c->cost_per_contract ? number_format($c->cost_per_contract, 0) . ' ر.س' : '—' }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="text-xs font-semibold {{ $c->conversion_rate >= 20 ? 'text-green-600' : ($c->conversion_rate >= 10 ? 'text-amber-600' : 'text-red-600') }}">
                        {{ $c->conversion_rate }}%
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" class="px-4 py-10 text-center text-slate-400 text-sm">لا توجد حملات</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
