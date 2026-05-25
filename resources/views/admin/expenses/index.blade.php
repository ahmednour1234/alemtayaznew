@extends('admin.layouts.app')
@section('title', 'Ø§Ù„Ù…ØµØ§Ø±ÙŠÙ')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">Ø§Ù„Ù…ØµØ§Ø±ÙŠÙ</h2>
    <div class="flex gap-2">
        <a href="{{ route('admin.expenses.export', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-2 rounded-lg">ØªØµØ¯ÙŠØ± Excel</a>
        <a href="{{ route('admin.expenses.import-template') }}" class="bg-slate-600 hover:bg-slate-700 text-white text-sm px-3 py-2 rounded-lg">Ù‚Ø§Ù„Ø¨ Ø§Ù„Ø§Ø³ØªÙŠØ±Ø§Ø¯</a>
        <button onclick="document.getElementById('importModal').classList.remove('hidden')"
                class="bg-purple-600 hover:bg-purple-700 text-white text-sm px-3 py-2 rounded-lg">Ø§Ø³ØªÙŠØ±Ø§Ø¯</button>
        <a href="{{ route('admin.expenses.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-1">
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
            <label class="block text-xs text-slate-500 mb-1">Ø§Ù„Ø­Ø§Ù„Ø©</label>
            <select name="status" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Ø§Ù„ÙƒÙ„</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Ù…Ø¹Ù„Ù‚</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Ù…Ø¹ØªÙ…Ø¯</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ù…Ø±ÙÙˆØ¶</option>
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
        <a href="{{ route('admin.expenses.index') }}" class="text-sm text-slate-500 hover:underline self-center">Ù…Ø³Ø­</a>
    </form>
</div>

<!-- Totals by Status -->
<div class="grid grid-cols-3 gap-3 mb-4">
    <div class="bg-orange-50 border border-orange-200 rounded-lg px-4 py-2.5 text-center">
        <p class="text-xs text-orange-600">Ù…Ø¹Ù„Ù‚</p>
        <p class="font-bold text-orange-700">{{ number_format($totals['pending'] ?? 0, 2) }}</p>
    </div>
    <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2.5 text-center">
        <p class="text-xs text-green-600">Ù…Ø¹ØªÙ…Ø¯</p>
        <p class="font-bold text-green-700">{{ number_format($totals['approved'] ?? 0, 2) }}</p>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-2.5 text-center">
        <p class="text-xs text-red-600">Ù…Ø±ÙÙˆØ¶</p>
        <p class="font-bold text-red-700">{{ number_format($totals['rejected'] ?? 0, 2) }}</p>
    </div>
</div>

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
                    <th class="px-4 py-3 text-right">Ø§Ù„Ø­Ø§Ù„Ø©</th>
                    <th class="px-4 py-3 text-right">Ø§Ù„Ø¥Ø¬Ø±Ø§Ø¡Ø§Øª</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($expenses as $expense)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-400">{{ $expenses->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3">{{ $expense->branch?->name }}</td>
                    <td class="px-4 py-3">{{ $expense->expenseType?->name }}</td>
                    <td class="px-4 py-3 font-semibold text-red-600">{{ number_format($expense->amount, 2) }}</td>
                    <td class="px-4 py-3">{{ $expense->date?->format('Y-m-d') }}</td>
                    <td class="px-4 py-3">
                        @if($expense->status === 'approved')
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Ù…Ø¹ØªÙ…Ø¯</span>
                        @elseif($expense->status === 'pending')
                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs">Ù…Ø¹Ù„Ù‚</span>
                        @else
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">Ù…Ø±ÙÙˆØ¶</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.expenses.show', $expense->id) }}" class="text-slate-500 hover:text-blue-600" title="Ø¹Ø±Ø¶">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @if($expense->isPending())
                            <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="text-slate-500 hover:text-yellow-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.expenses.approve', $expense->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-green-600 hover:underline">Ù…ÙˆØ§ÙÙ‚Ø©</button>
                            </form>
                            @endif
                            <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Ø­Ø°Ù Ù‡Ø°Ø§ Ø§Ù„Ù…ØµØ±ÙˆÙØŸ')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-500 hover:text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Ù„Ø§ ØªÙˆØ¬Ø¯ Ù…ØµØ§Ø±ÙŠÙ</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
    <div class="px-4 py-3 border-t">{{ $expenses->withQueryString()->links() }}</div>
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
                    <th class="px-4 py-2 text-right">Ø§Ø³ØªØ¹Ø§Ø¯Ø©</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($trashed as $expense)
                <tr>
                    <td class="px-4 py-2 text-slate-400">{{ $expense->branch?->name }}</td>
                    <td class="px-4 py-2 text-slate-400">{{ number_format($expense->amount, 2) }}</td>
                    <td class="px-4 py-2">
                        <form action="{{ route('admin.expenses.restore', $expense->id) }}" method="POST" class="inline">
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
        <h3 class="text-lg font-semibold mb-4">Ø§Ø³ØªÙŠØ±Ø§Ø¯ Ù…ØµØ§Ø±ÙŠÙ Ù…Ù† Excel</h3>
        <form action="{{ route('admin.expenses.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
            <div class="flex gap-3">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-sm px-5 py-2 rounded-lg">Ø§Ø³ØªÙŠØ±Ø§Ø¯</button>
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="bg-slate-200 text-slate-700 text-sm px-5 py-2 rounded-lg">Ø¥Ù„ØºØ§Ø¡</button>
            </div>
        </form>
    </div>
</div>

@endsection

