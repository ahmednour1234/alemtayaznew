@extends('admin.layouts.app')
@section('title', 'ØªÙØ§ØµÙŠÙ„ Ø§Ù„ØªØ­ÙˆÙŠÙ„')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.transfers.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">ØªÙØ§ØµÙŠÙ„ Ø§Ù„ØªØ­ÙˆÙŠÙ„</h2>
        @if($transfer->status === 'approved')
            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Ù…Ø¹ØªÙ…Ø¯</span>
        @elseif($transfer->status === 'pending')
            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs">Ù…Ø¹Ù„Ù‚</span>
        @else
            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">Ù…Ø±ÙÙˆØ¶</span>
        @endif
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div><p class="text-xs text-slate-400">Ù…Ù† ÙØ±Ø¹</p><p class="font-medium mt-0.5">{{ $transfer->fromBranch?->name ?? 'Ø®Ø§Ø±Ø¬ÙŠ' }}</p></div>
            <div><p class="text-xs text-slate-400">Ø¥Ù„Ù‰ ÙØ±Ø¹</p><p class="font-medium mt-0.5">{{ $transfer->toBranch?->name ?? 'Ø®Ø§Ø±Ø¬ÙŠ' }}</p></div>
            <div><p class="text-xs text-slate-400">Ø§Ù„Ù…Ø¨Ù„Øº</p><p class="font-bold text-blue-600 text-lg mt-0.5">{{ number_format($transfer->amount, 2) }} Ø±ÙŠØ§Ù„</p></div>
            <div><p class="text-xs text-slate-400">Ø§Ù„ØªØ§Ø±ÙŠØ®</p><p class="font-medium mt-0.5">{{ $transfer->date?->format('Y-m-d') }}</p></div>
            @if($transfer->approved_at)
            <div><p class="text-xs text-slate-400">ØªØ§Ø±ÙŠØ® Ø§Ù„Ø§Ø¹ØªÙ…Ø§Ø¯</p><p class="font-medium mt-0.5">{{ $transfer->approved_at?->format('Y-m-d H:i') }}</p></div>
            <div><p class="text-xs text-slate-400">Ø§Ø¹ØªÙ…Ø¯ Ø¨ÙˆØ§Ø³Ø·Ø©</p><p class="font-medium mt-0.5">{{ $transfer->approver?->name }}</p></div>
            @endif
        </div>
        @if($transfer->rejection_reason)
        <div class="bg-red-50 rounded-lg p-3">
            <p class="text-xs text-red-500">Ø³Ø¨Ø¨ Ø§Ù„Ø±ÙØ¶</p>
            <p class="text-sm mt-0.5">{{ $transfer->rejection_reason }}</p>
        </div>
        @endif
        @if($transfer->description)
        <div><p class="text-xs text-slate-400">Ø§Ù„ÙˆØµÙ</p><p class="mt-0.5">{{ $transfer->description }}</p></div>
        @endif
        <div class="flex flex-wrap gap-3 pt-4 border-t" x-data="{ rejectModal: false }">
            @if($transfer->isPending())
            <form action="{{ route('admin.transfers.approve', $transfer->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm px-5 py-2 rounded-lg">Ø§Ø¹ØªÙ…Ø§Ø¯</button>
            </form>
            <button @click="rejectModal = true" class="bg-red-600 hover:bg-red-700 text-white text-sm px-5 py-2 rounded-lg">Ø±ÙØ¶</button>
            <a href="{{ route('admin.transfers.edit', $transfer->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-5 py-2 rounded-lg">ØªØ¹Ø¯ÙŠÙ„</a>
            @endif
            <a href="{{ route('admin.transfers.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-5 py-2 rounded-lg">Ø±Ø¬ÙˆØ¹</a>

            <div x-show="rejectModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
                    <h3 class="text-lg font-semibold mb-4">Ø³Ø¨Ø¨ Ø§Ù„Ø±ÙØ¶</h3>
                    <form action="{{ route('admin.transfers.reject', $transfer->id) }}" method="POST">
                        @csrf
                        <textarea name="rejection_reason" rows="3" required placeholder="Ø£Ø¯Ø®Ù„ Ø³Ø¨Ø¨ Ø§Ù„Ø±ÙØ¶..."
                                  class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm mb-4"></textarea>
                        <div class="flex gap-3">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm px-5 py-2 rounded-lg">Ø±ÙØ¶</button>
                            <button type="button" @click="rejectModal = false" class="bg-slate-200 text-slate-700 text-sm px-5 py-2 rounded-lg">Ø¥Ù„ØºØ§Ø¡</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

