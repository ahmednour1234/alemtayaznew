@extends('admin.layouts.app')

@section('title', 'Ù„ÙˆØ­Ø© Ø§Ù„ØªØ­ÙƒÙ…')

@section('content')
<!-- Stat Cards -->
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">

    <div class="bg-white rounded-xl p-4 shadow-sm border-r-4 border-green-500 col-span-2 sm:col-span-1">
        <p class="text-xs text-slate-500">Ø¥Ø¬Ù…Ø§Ù„ÙŠ Ø§Ù„Ø¯Ø®Ù„</p>
        <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['total_income'], 2) }}</p>
        <p class="text-xs text-slate-400 mt-1">Ø±ÙŠØ§Ù„ Ø³Ø¹ÙˆØ¯ÙŠ</p>
    </div>

    <div class="bg-white rounded-xl p-4 shadow-sm border-r-4 border-red-500 col-span-2 sm:col-span-1">
        <p class="text-xs text-slate-500">Ø¥Ø¬Ù…Ø§Ù„ÙŠ Ø§Ù„Ù…ØµØ§Ø±ÙŠÙ</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['total_expenses'], 2) }}</p>
        <p class="text-xs text-slate-400 mt-1">Ø§Ù„Ù…Ø¹ØªÙ…Ø¯Ø© ÙÙ‚Ø·</p>
    </div>

    <div class="bg-white rounded-xl p-4 shadow-sm border-r-4 border-blue-500 col-span-2 sm:col-span-1">
        <p class="text-xs text-slate-500">ØµØ§ÙÙŠ Ø§Ù„Ø±Ø¨Ø­</p>
        <p class="text-2xl font-bold {{ $stats['net_profit'] >= 0 ? 'text-blue-600' : 'text-red-600' }} mt-1">
            {{ number_format($stats['net_profit'], 2) }}
        </p>
        <p class="text-xs text-slate-400 mt-1">Ø±ÙŠØ§Ù„ Ø³Ø¹ÙˆØ¯ÙŠ</p>
    </div>

    <div class="bg-white rounded-xl p-4 shadow-sm border-r-4 border-purple-500">
        <p class="text-xs text-slate-500">Ø§Ù„ÙØ±ÙˆØ¹ Ø§Ù„Ù†Ø´Ø·Ø©</p>
        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['branch_count'] }}</p>
        <p class="text-xs text-slate-400 mt-1">ÙØ±Ø¹</p>
    </div>

    <div class="bg-white rounded-xl p-4 shadow-sm border-r-4 border-orange-500">
        <p class="text-xs text-slate-500">Ù…ØµØ§Ø±ÙŠÙ Ù…Ø¹Ù„Ù‚Ø©</p>
        <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['pending_expenses'] }}</p>
        <p class="text-xs text-slate-400 mt-1">ØªÙ†ØªØ¸Ø± Ø§Ù„Ù…ÙˆØ§ÙÙ‚Ø©</p>
    </div>

    <div class="bg-white rounded-xl p-4 shadow-sm border-r-4 border-yellow-500">
        <p class="text-xs text-slate-500">ØªØ­ÙˆÙŠÙ„Ø§Øª Ù…Ø¹Ù„Ù‚Ø©</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['pending_transfers'] }}</p>
        <p class="text-xs text-slate-400 mt-1">ØªÙ†ØªØ¸Ø± Ø§Ù„Ù…ÙˆØ§ÙÙ‚Ø©</p>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <!-- Income vs Expense Chart -->
    <div class="bg-white rounded-xl p-5 shadow-sm">
        <h3 class="font-semibold text-slate-700 mb-4">Ø§Ù„Ø¯Ø®Ù„ Ù…Ù‚Ø§Ø¨Ù„ Ø§Ù„Ù…ØµØ§Ø±ÙŠÙ ({{ now()->year }})</h3>
        <canvas id="incomeExpenseChart" height="200"></canvas>
    </div>

    <!-- Branch Comparison Chart -->
    <div class="bg-white rounded-xl p-5 shadow-sm">
        <h3 class="font-semibold text-slate-700 mb-4">Ù…Ù‚Ø§Ø±Ù†Ø© Ø§Ù„ÙØ±ÙˆØ¹ ({{ now()->year }})</h3>
        <canvas id="branchChart" height="200"></canvas>
    </div>
</div>

<!-- Recent Tables Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <!-- Recent Incomes -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-slate-700">Ø£Ø­Ø¯Ø« Ø§Ù„Ø¥ÙŠØ±Ø§Ø¯Ø§Øª</h3>
            <a href="{{ route('admin.incomes.index') }}" class="text-xs text-blue-600 hover:underline">Ø¹Ø±Ø¶ Ø§Ù„ÙƒÙ„</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs">
                    <tr>
                        <th class="px-4 py-2 text-right">Ø§Ù„ÙØ±Ø¹</th>
                        <th class="px-4 py-2 text-right">Ø§Ù„Ù†ÙˆØ¹</th>
                        <th class="px-4 py-2 text-right">Ø§Ù„Ù…Ø¨Ù„Øº</th>
                        <th class="px-4 py-2 text-right">Ø§Ù„ØªØ§Ø±ÙŠØ®</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentIncomes as $income)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2.5 text-xs">{{ $income->branch?->name }}</td>
                        <td class="px-4 py-2.5 text-xs">{{ $income->incomeType?->name }}</td>
                        <td class="px-4 py-2.5 font-semibold text-green-600">{{ number_format($income->amount, 2) }}</td>
                        <td class="px-4 py-2.5 text-xs text-slate-400">{{ $income->date?->format('Y-m-d') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400 text-xs">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¥ÙŠØ±Ø§Ø¯Ø§Øª</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Expenses -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-slate-700">Ø£Ø­Ø¯Ø« Ø§Ù„Ù…ØµØ§Ø±ÙŠÙ</h3>
            <a href="{{ route('admin.expenses.index') }}" class="text-xs text-blue-600 hover:underline">Ø¹Ø±Ø¶ Ø§Ù„ÙƒÙ„</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs">
                    <tr>
                        <th class="px-4 py-2 text-right">Ø§Ù„ÙØ±Ø¹</th>
                        <th class="px-4 py-2 text-right">Ø§Ù„Ù†ÙˆØ¹</th>
                        <th class="px-4 py-2 text-right">Ø§Ù„Ù…Ø¨Ù„Øº</th>
                        <th class="px-4 py-2 text-right">Ø§Ù„Ø­Ø§Ù„Ø©</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentExpenses as $expense)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2.5 text-xs">{{ $expense->branch?->name }}</td>
                        <td class="px-4 py-2.5 text-xs">{{ $expense->expenseType?->name }}</td>
                        <td class="px-4 py-2.5 font-semibold text-red-600">{{ number_format($expense->amount, 2) }}</td>
                        <td class="px-4 py-2.5">
                            @if($expense->status === 'approved')
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Ù…Ø¹ØªÙ…Ø¯</span>
                            @elseif($expense->status === 'pending')
                                <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs">Ù…Ø¹Ù„Ù‚</span>
                            @else
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">Ù…Ø±ÙÙˆØ¶</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400 text-xs">Ù„Ø§ ØªÙˆØ¬Ø¯ Ù…ØµØ§Ø±ÙŠÙ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pending Approvals -->
@if($pendingExpenses->count() || $pendingTransfers->count())
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b bg-orange-50">
        <h3 class="font-semibold text-orange-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
            Ø·Ù„Ø¨Ø§Øª Ø§Ù„Ù…ÙˆØ§ÙÙ‚Ø© Ø§Ù„Ù…Ø¹Ù„Ù‚Ø© ({{ $pendingExpenses->count() + $pendingTransfers->count() }})
        </h3>
    </div>
    <div class="p-4 space-y-2">
        @foreach($pendingExpenses->take(5) as $expense)
        <div class="flex items-center justify-between bg-orange-50 rounded-lg px-4 py-2.5">
            <span class="text-sm">Ù…ØµØ±ÙˆÙ: {{ $expense->branch?->name }} - {{ number_format($expense->amount,2) }} Ø±ÙŠØ§Ù„</span>
            <div class="flex gap-2">
                <form action="{{ route('admin.expenses.approve', $expense->id) }}" method="POST">
                    @csrf <button class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Ù…ÙˆØ§ÙÙ‚Ø©</button>
                </form>
                <a href="{{ route('admin.expenses.show', $expense->id) }}" class="text-xs bg-slate-200 text-slate-700 px-3 py-1 rounded hover:bg-slate-300">Ø¹Ø±Ø¶</a>
            </div>
        </div>
        @endforeach
        @foreach($pendingTransfers->take(5) as $transfer)
        <div class="flex items-center justify-between bg-yellow-50 rounded-lg px-4 py-2.5">
            <span class="text-sm">ØªØ­ÙˆÙŠÙ„: {{ $transfer->fromBranch?->name }} â† {{ $transfer->toBranch?->name }} - {{ number_format($transfer->amount,2) }} Ø±ÙŠØ§Ù„</span>
            <div class="flex gap-2">
                <form action="{{ route('admin.transfers.approve', $transfer->id) }}" method="POST">
                    @csrf <button class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Ù…ÙˆØ§ÙÙ‚Ø©</button>
                </form>
                <a href="{{ route('admin.transfers.show', $transfer->id) }}" class="text-xs bg-slate-200 text-slate-700 px-3 py-1 rounded hover:bg-slate-300">Ø¹Ø±Ø¶</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
const chartData = @json($chartData);
const branchData = @json($branchChart);

// Income vs Expense
new Chart(document.getElementById('incomeExpenseChart'), {
    type: 'bar',
    data: {
        labels: chartData.months,
        datasets: [
            { label: 'Ø§Ù„Ø¯Ø®Ù„', data: chartData.incomes, backgroundColor: 'rgba(34,197,94,0.7)', borderColor: '#16a34a', borderWidth: 1 },
            { label: 'Ø§Ù„Ù…ØµØ§Ø±ÙŠÙ', data: chartData.expenses, backgroundColor: 'rgba(239,68,68,0.7)', borderColor: '#dc2626', borderWidth: 1 }
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
});

// Branch Comparison
new Chart(document.getElementById('branchChart'), {
    type: 'bar',
    data: {
        labels: branchData.labels,
        datasets: [
            { label: 'Ø§Ù„Ø¯Ø®Ù„', data: branchData.incomes, backgroundColor: 'rgba(59,130,246,0.7)' },
            { label: 'Ø§Ù„Ù…ØµØ§Ø±ÙŠÙ', data: branchData.expenses, backgroundColor: 'rgba(168,85,247,0.7)' }
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
});
</script>
@endpush

