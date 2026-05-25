@extends('admin.layouts.app')
@section('title', 'Ø§Ù„Ø¥ÙŠØ±Ø§Ø¯Ø§Øª')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">Ø§Ù„Ø¥ÙŠØ±Ø§Ø¯Ø§Øª</h2>
    <div class="flex gap-2">
        <a href="{{ route('admin.incomes.export', request()->query()) }}"
           class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-2 rounded-lg flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            ØªØµØ¯ÙŠØ± Excel
        </a>
        <a href="{{ route('admin.incomes.template') }}"
           class="bg-slate-600 hover:bg-slate-700 text-white text-sm px-3 py-2 rounded-lg flex items-center gap-1">
            Ù‚Ø§Ù„Ø¨ Ø§Ù„Ø§Ø³ØªÙŠØ±Ø§Ø¯
        </a>
        <button onclick="document.getElementById('importModal').classList.remove('hidden')"
                class="bg-purple-600 hover:bg-purple-700 text-white text-sm px-3 py-2 rounded-lg">Ø§Ø³ØªÙŠØ±Ø§Ø¯</button>
        <a href="{{ route('admin.incomes.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ø¥Ø¶Ø§ÙØ©
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl p-4 shadow-sm mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-slate-500 mb-1">Ø§Ù„ÙØ±Ø¹</label>
            <select name="branch_id" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Ø§Ù„ÙƒÙ„</option>
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-500 mb-1">Ù†ÙˆØ¹ Ø§Ù„Ø¯Ø®Ù„</label>
            <select name="income_type_id" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Ø§Ù„ÙƒÙ„</option>
                @foreach($incomeTypes as $type)
                <option value="{{ $type->id }}" {{ request('income_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-500 mb-1">Ù…Ù† ØªØ§Ø±ÙŠØ®</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-xs text-slate-500 mb-1">Ø¥Ù„Ù‰ ØªØ§Ø±ÙŠØ®</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <button type="submit" class="bg-slate-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-slate-800">Ø¨Ø­Ø«</button>
        <a href="{{ route('admin.incomes.index') }}" class="text-sm text-slate-500 hover:underline self-center">Ù…Ø³Ø­</a>
    </form>
</div>

<!-- Total -->
@if($total > 0)
<div class="bg-green-50 border border-green-200 rounded-lg px-4 py-3 mb-4 flex justify-between items-center">
    <span class="text-sm text-green-700">Ø¥Ø¬Ù…Ø§Ù„ÙŠ Ø§Ù„Ø¥ÙŠØ±Ø§Ø¯Ø§Øª Ø§Ù„Ù…ØµÙØ§Ø©:</span>
    <span class="font-bold text-green-700 text-lg">{{ number_format($total, 2) }} Ø±ÙŠØ§Ù„</span>
</div>
@endif

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs border-b">
                <tr>
                    <th class="px-4 py-3 text-right">#</th>
                    <th class="px-4 py-3 text-right">Ø§Ù„ÙØ±Ø¹</th>
                    <th class="px-4 py-3 text-right">Ø§Ù„Ù†ÙˆØ¹</th>
                    <th class="px-4 py-3 text-right">Ø§Ù„Ù…Ø¨Ù„Øº</th>
                    <th class="px-4 py-3 text-right">Ø§Ù„ØªØ§Ø±ÙŠØ®</th>
                    <th class="px-4 py-3 text-right">Ø·Ø±ÙŠÙ‚Ø© Ø§Ù„Ø¯ÙØ¹</th>
                    <th class="px-4 py-3 text-right">Ø§Ù„Ù…Ø±Ø¬Ø¹</th>
                    <th class="px-4 py-3 text-right">Ø§Ù„Ø¥Ø¬Ø±Ø§Ø¡Ø§Øª</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($incomes as $income)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-400">{{ $incomes->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3">{{ $income->branch?->name }}</td>
                    <td class="px-4 py-3">{{ $income->incomeType?->name }}</td>
                    <td class="px-4 py-3 font-semibold text-green-600">{{ number_format($income->amount, 2) }}</td>
                    <td class="px-4 py-3">{{ $income->date?->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-xs">
                        @php $pm = ['cash'=>'Ù†Ù‚Ø¯','bank_transfer'=>'ØªØ­ÙˆÙŠÙ„ Ø¨Ù†ÙƒÙŠ','check'=>'Ø´ÙŠÙƒ','other'=>'Ø£Ø®Ø±Ù‰']; @endphp
                        {{ $pm[$income->payment_method] ?? $income->payment_method }}
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-400">{{ $income->reference_number ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.incomes.show', $income->id) }}" class="text-slate-500 hover:text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('admin.incomes.edit', $income->id) }}" class="text-slate-500 hover:text-yellow-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.incomes.destroy', $income->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Ø­Ø°Ù Ù‡Ø°Ø§ Ø§Ù„Ø¥ÙŠØ±Ø§Ø¯ØŸ')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-500 hover:text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¥ÙŠØ±Ø§Ø¯Ø§Øª</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($incomes->hasPages())
    <div class="px-4 py-3 border-t">{{ $incomes->withQueryString()->links() }}</div>
    @endif
</div>

<!-- Trashed -->
@if($trashed->isNotEmpty())
<div x-data="{ open: false }" class="mt-6">
    <button @click="open = !open" class="text-sm text-slate-500 hover:text-red-600">Ø§Ù„Ù…Ø­Ø°ÙˆÙØ© ({{ $trashed->count() }})</button>
    <div x-show="open" class="mt-3 bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-red-50 text-xs text-slate-500 border-b">
                <tr>
                    <th class="px-4 py-2 text-right">Ø§Ù„ÙØ±Ø¹</th>
                    <th class="px-4 py-2 text-right">Ø§Ù„Ù…Ø¨Ù„Øº</th>
                    <th class="px-4 py-2 text-right">Ø§Ù„ØªØ§Ø±ÙŠØ®</th>
                    <th class="px-4 py-2 text-right">Ø§Ø³ØªØ¹Ø§Ø¯Ø©</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($trashed as $income)
                <tr>
                    <td class="px-4 py-2 text-slate-400">{{ $income->branch?->name }}</td>
                    <td class="px-4 py-2 text-slate-400">{{ number_format($income->amount, 2) }}</td>
                    <td class="px-4 py-2 text-xs text-slate-400">{{ $income->date?->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">
                        <form action="{{ route('admin.incomes.restore', $income->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs text-green-600 hover:underline">Ø§Ø³ØªØ¹Ø§Ø¯Ø©</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Ø§Ø³ØªÙŠØ±Ø§Ø¯ Ø¥ÙŠØ±Ø§Ø¯Ø§Øª Ù…Ù† Excel</h3>
        <form action="{{ route('admin.incomes.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Ù…Ù„Ù Excel</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-slate-400 mt-1">
                    <a href="{{ route('admin.incomes.template') }}" class="text-blue-600 hover:underline">ØªØ­Ù…ÙŠÙ„ Ø§Ù„Ù‚Ø§Ù„Ø¨</a>
                </p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-sm px-5 py-2 rounded-lg">Ø§Ø³ØªÙŠØ±Ø§Ø¯</button>
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="bg-slate-200 text-slate-700 text-sm px-5 py-2 rounded-lg">Ø¥Ù„ØºØ§Ø¡</button>
            </div>
        </form>
    </div>
</div>

@endsection

