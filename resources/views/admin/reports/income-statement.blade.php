@extends('admin.layouts.app')
@section('title', 'قائمة الدخل')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">قائمة الدخل بين ال�روع</h2>
    @if(isset($report))
    <a href="{{ route('admin.reports.income-statement.export', request()->query()) }}"
       class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg">تصدير Excel</a>
    @endif
</div>

<!-- Filter Form -->
<div class="bg-white rounded-xl p-5 shadow-sm mb-6">
    <form method="GET" class="space-y-4">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">ال�روع</label>
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

@if(isset($report) && count($report) > 0)

<!-- Comparison Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs border-b">
                <tr>
                    <th class="px-4 py-3 text-right">ال�رع</th>
                    <th class="px-4 py-3 text-right">إجمالي الدخل</th>
                    <th class="px-4 py-3 text-right">إجمالي المصاري�</th>
                    <th class="px-4 py-3 text-right">التحويلات الواردة</th>
                    <th class="px-4 py-3 text-right">التحويلات الصادرة</th>
                    <th class="px-4 py-3 text-right">صا�ي الربح</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($report as $row)
                @php $net = $row['income'] + $row['transfers_in'] - $row['expenses'] - $row['transfers_out']; @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium">{{ $row['branch'] }}</td>
                    <td class="px-4 py-3 font-semibold text-green-600">{{ number_format($row['income'], 2) }}</td>
                    <td class="px-4 py-3 font-semibold text-red-600">{{ number_format($row['expenses'], 2) }}</td>
                    <td class="px-4 py-3 text-blue-600">{{ number_format($row['transfers_in'], 2) }}</td>
                    <td class="px-4 py-3 text-purple-600">{{ number_format($row['transfers_out'], 2) }}</td>
                    <td class="px-4 py-3 font-bold {{ $net >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        {{ number_format($net, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-100 text-xs font-bold border-t">
                <tr>
                    <td class="px-4 py-2">الإجمالي</td>
                    <td class="px-4 py-2 text-green-700">{{ number_format(collect($report)->sum('income'), 2) }}</td>
                    <td class="px-4 py-2 text-red-700">{{ number_format(collect($report)->sum('expenses'), 2) }}</td>
                    <td class="px-4 py-2 text-blue-700">{{ number_format(collect($report)->sum('transfers_in'), 2) }}</td>
                    <td class="px-4 py-2 text-purple-700">{{ number_format(collect($report)->sum('transfers_out'), 2) }}</td>
                    <td class="px-4 py-2 {{ (collect($report)->sum('income') + collect($report)->sum('transfers_in') - collect($report)->sum('expenses') - collect($report)->sum('transfers_out')) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        {{ number_format(collect($report)->sum('income') + collect($report)->sum('transfers_in') - collect($report)->sum('expenses') - collect($report)->sum('transfers_out'), 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Chart -->
<div class="bg-white rounded-xl shadow-sm p-5">
    <h3 class="font-semibold text-slate-700 mb-4">مقارنة بيانية</h3>
    <canvas id="incomeStatChart" height="120"></canvas>
</div>

@push('scripts')
<script>
const reportData = @json($report);
new Chart(document.getElementById('incomeStatChart'), {
    type: 'bar',
    data: {
        labels: reportData.map(r => r.branch),
        datasets: [
            { label: 'الدخل', data: reportData.map(r => r.income), backgroundColor: 'rgba(34,197,94,0.7)' },
            { label: 'المصاري�', data: reportData.map(r => r.expenses), backgroundColor: 'rgba(239,68,68,0.7)' },
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
});
</script>
@endpush

@endif

@endsection

