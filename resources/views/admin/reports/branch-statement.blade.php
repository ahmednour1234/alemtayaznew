@extends('admin.layouts.app')
@section('title', 'ÙƒØ´Ù Ø­Ø³Ø§Ø¨ Ø§Ù„ÙØ±Ø¹')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">ÙƒØ´Ù Ø­Ø³Ø§Ø¨ Ø§Ù„ÙØ±Ø¹</h2>
    @if(isset($report))
    <a href="{{ route('admin.reports.branch-statement.export', request()->query()) }}"
       class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg">ØªØµØ¯ÙŠØ± Excel</a>
    @endif
</div>

<!-- Filter Form -->
<div class="bg-white rounded-xl p-5 shadow-sm mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø§Ù„ÙØ±Ø¹ <span class="text-red-500">*</span></label>
            <select name="branch_id" required class="border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 min-w-48">
                <option value="">Ø§Ø®ØªØ± Ø§Ù„ÙØ±Ø¹</option>
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Ù…Ù† ØªØ§Ø±ÙŠØ®</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø¥Ù„Ù‰ ØªØ§Ø±ÙŠØ®</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2.5 rounded-lg">Ø¹Ø±Ø¶ Ø§Ù„ÙƒØ´Ù</button>
    </form>
</div>

@if(isset($report))

<!-- Summary Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
        <p class="text-xs text-green-600">Ø¥Ø¬Ù…Ø§Ù„ÙŠ Ø§Ù„Ø¯Ø®Ù„</p>
        <p class="text-xl font-bold text-green-700 mt-1">{{ number_format($report['totals']['total_income'], 2) }}</p>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
        <p class="text-xs text-red-600">Ø¥Ø¬Ù…Ø§Ù„ÙŠ Ø§Ù„Ù…ØµØ§Ø±ÙŠÙ</p>
        <p class="text-xl font-bold text-red-700 mt-1">{{ number_format($report['totals']['total_expenses'], 2) }}</p>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
        <p class="text-xs text-blue-600">Ø§Ù„ØªØ­ÙˆÙŠÙ„Ø§Øª Ø§Ù„ÙˆØ§Ø±Ø¯Ø©</p>
        <p class="text-xl font-bold text-blue-700 mt-1">{{ number_format($report['totals']['transfers_in'], 2) }}</p>
    </div>
    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 text-center">
        <p class="text-xs text-purple-600">Ø§Ù„ØªØ­ÙˆÙŠÙ„Ø§Øª Ø§Ù„ØµØ§Ø¯Ø±Ø©</p>
        <p class="text-xl font-bold text-purple-700 mt-1">{{ number_format($report['totals']['transfers_out'], 2) }}</p>
    </div>
</div>

<!-- Net Balance -->
@php $net = $report['totals']['total_income'] + $report['totals']['transfers_in'] - $report['totals']['total_expenses'] - $report['totals']['transfers_out']; @endphp
<div class="bg-white rounded-xl p-4 shadow-sm mb-6 flex justify-between items-center">
    <span class="font-semibold text-slate-700">Ø§Ù„Ø±ØµÙŠØ¯ Ø§Ù„ØµØ§ÙÙŠ</span>
    <span class="text-2xl font-bold {{ $net >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($net, 2) }} Ø±ÙŠØ§Ù„</span>
</div>

<!-- Incomes Table -->
@if($report['incomes']->isNotEmpty())
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-3 border-b bg-green-50">
        <h3 class="font-semibold text-green-700">Ø§Ù„Ø¥ÙŠØ±Ø§Ø¯Ø§Øª</h3>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs border-b">
            <tr>
                <th class="px-4 py-2 text-right">Ø§Ù„ØªØ§Ø±ÙŠØ®</th>
                <th class="px-4 py-2 text-right">Ø§Ù„Ù†ÙˆØ¹</th>
                <th class="px-4 py-2 text-right">Ø§Ù„Ù…Ø¨Ù„Øº</th>
                <th class="px-4 py-2 text-right">Ø·Ø±ÙŠÙ‚Ø© Ø§Ù„Ø¯ÙØ¹</th>
                <th class="px-4 py-2 text-right">Ø§Ù„Ù…Ø±Ø¬Ø¹</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($report['incomes'] as $income)
            <tr class="hover:bg-green-50">
                <td class="px-4 py-2">{{ $income->date?->format('Y-m-d') }}</td>
                <td class="px-4 py-2">{{ $income->incomeType?->name }}</td>
                <td class="px-4 py-2 font-semibold text-green-600">{{ number_format($income->amount, 2) }}</td>
                <td class="px-4 py-2 text-xs">
                    @php $pm = ['cash'=>'Ù†Ù‚Ø¯','bank_transfer'=>'ØªØ­ÙˆÙŠÙ„ Ø¨Ù†ÙƒÙŠ','check'=>'Ø´ÙŠÙƒ','other'=>'Ø£Ø®Ø±Ù‰']; @endphp
                    {{ $pm[$income->payment_method] ?? $income->payment_method }}
                </td>
                <td class="px-4 py-2 text-xs text-slate-400">{{ $income->reference_number ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- Expenses Table -->
@if($report['expenses']->isNotEmpty())
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-3 border-b bg-red-50">
        <h3 class="font-semibold text-red-700">Ø§Ù„Ù…ØµØ§Ø±ÙŠÙ (Ø§Ù„Ù…Ø¹ØªÙ…Ø¯Ø©)</h3>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs border-b">
            <tr>
                <th class="px-4 py-2 text-right">Ø§Ù„ØªØ§Ø±ÙŠØ®</th>
                <th class="px-4 py-2 text-right">Ø§Ù„Ù†ÙˆØ¹</th>
                <th class="px-4 py-2 text-right">Ø§Ù„Ù…Ø¨Ù„Øº</th>
                <th class="px-4 py-2 text-right">Ø§Ù„ÙˆØµÙ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($report['expenses'] as $expense)
            <tr class="hover:bg-red-50">
                <td class="px-4 py-2">{{ $expense->date?->format('Y-m-d') }}</td>
                <td class="px-4 py-2">{{ $expense->expenseType?->name }}</td>
                <td class="px-4 py-2 font-semibold text-red-600">{{ number_format($expense->amount, 2) }}</td>
                <td class="px-4 py-2 text-xs text-slate-400">{{ Str::limit($expense->description, 40) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- Transfers Table -->
@if($report['transfers_in']->isNotEmpty() || $report['transfers_out']->isNotEmpty())
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-3 border-b bg-blue-50">
        <h3 class="font-semibold text-blue-700">Ø§Ù„ØªØ­ÙˆÙŠÙ„Ø§Øª</h3>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs border-b">
            <tr>
                <th class="px-4 py-2 text-right">Ø§Ù„ØªØ§Ø±ÙŠØ®</th>
                <th class="px-4 py-2 text-right">Ø§Ù„Ù†ÙˆØ¹</th>
                <th class="px-4 py-2 text-right">Ø§Ù„Ø¬Ù‡Ø©</th>
                <th class="px-4 py-2 text-right">Ø§Ù„Ù…Ø¨Ù„Øº</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($report['transfers_in'] as $t)
            <tr class="hover:bg-blue-50">
                <td class="px-4 py-2">{{ $t->date?->format('Y-m-d') }}</td>
                <td class="px-4 py-2"><span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">ÙˆØ§Ø±Ø¯</span></td>
                <td class="px-4 py-2">{{ $t->fromBranch?->name ?? 'Ø®Ø§Ø±Ø¬ÙŠ' }}</td>
                <td class="px-4 py-2 font-semibold text-blue-600">{{ number_format($t->amount, 2) }}</td>
            </tr>
            @endforeach
            @foreach($report['transfers_out'] as $t)
            <tr class="hover:bg-purple-50">
                <td class="px-4 py-2">{{ $t->date?->format('Y-m-d') }}</td>
                <td class="px-4 py-2"><span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded">ØµØ§Ø¯Ø±</span></td>
                <td class="px-4 py-2">{{ $t->toBranch?->name ?? 'Ø®Ø§Ø±Ø¬ÙŠ' }}</td>
                <td class="px-4 py-2 font-semibold text-purple-600">{{ number_format($t->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endif

@endsection

