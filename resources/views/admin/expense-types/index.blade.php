@extends('admin.layouts.app')
@section('title', 'Ø£Ù†ÙˆØ§Ø¹ Ø§Ù„Ù…ØµØ§Ø±ÙŠÙ')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-slate-800">Ø£Ù†ÙˆØ§Ø¹ Ø§Ù„Ù…ØµØ§Ø±ÙŠÙ</h2>
    <a href="{{ route('admin.expense-types.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ø¥Ø¶Ø§ÙØ© Ù†ÙˆØ¹
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs border-b">
            <tr>
                <th class="px-4 py-3 text-right">#</th>
                <th class="px-4 py-3 text-right">Ø§Ù„Ø§Ø³Ù…</th>
                <th class="px-4 py-3 text-right">Ø§Ù„ÙˆØµÙ</th>
                <th class="px-4 py-3 text-right">Ø§Ù„Ø­Ø§Ù„Ø©</th>
                <th class="px-4 py-3 text-right">Ø§Ù„Ø¥Ø¬Ø±Ø§Ø¡Ø§Øª</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($types as $type)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-400">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-medium">{{ $type->name }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $type->description ?? '-' }}</td>
                <td class="px-4 py-3">
                    @if($type->active)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Ù†Ø´Ø·</span>
                    @else
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">ØºÙŠØ± Ù†Ø´Ø·</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.expense-types.edit', $type->id) }}" class="text-slate-500 hover:text-yellow-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('admin.expense-types.toggle-active', $type->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-slate-500 hover:text-purple-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                            </button>
                        </form>
                        <form action="{{ route('admin.expense-types.destroy', $type->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Ø­Ø°Ù Ù‡Ø°Ø§ Ø§Ù„Ù†ÙˆØ¹ØŸ')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-500 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø£Ù†ÙˆØ§Ø¹ Ù…ØµØ§Ø±ÙŠÙ</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($types->hasPages())
    <div class="px-4 py-3 border-t">{{ $types->withQueryString()->links() }}</div>
    @endif
</div>

@if($trashed->isNotEmpty())
<div x-data="{ open: false }" class="mt-6">
    <button @click="open = !open" class="text-sm text-slate-500 hover:text-red-600 flex items-center gap-1">
        Ø§Ù„Ù…Ø­Ø°ÙˆÙØ© ({{ $trashed->count() }})
    </button>
    <div x-show="open" class="mt-3 bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-red-50 text-xs text-slate-500 border-b">
                <tr>
                    <th class="px-4 py-2 text-right">Ø§Ù„Ø§Ø³Ù…</th>
                    <th class="px-4 py-2 text-right">ØªØ§Ø±ÙŠØ® Ø§Ù„Ø­Ø°Ù</th>
                    <th class="px-4 py-2 text-right">Ø§Ø³ØªØ¹Ø§Ø¯Ø©</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($trashed as $type)
                <tr>
                    <td class="px-4 py-2 line-through text-slate-400">{{ $type->name }}</td>
                    <td class="px-4 py-2 text-xs text-slate-400">{{ $type->deleted_at?->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">
                        <form action="{{ route('admin.expense-types.restore', $type->id) }}" method="POST" class="inline">
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

@endsection

