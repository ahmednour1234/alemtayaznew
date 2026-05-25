@extends('admin.layouts.app')
@section('title', 'ØªÙØ§ØµÙŠÙ„ Ø§Ù„Ø¥ÙŠØ±Ø§Ø¯')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.incomes.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">ØªÙØ§ØµÙŠÙ„ Ø§Ù„Ø¥ÙŠØ±Ø§Ø¯</h2>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div><p class="text-xs text-slate-400">Ø§Ù„ÙØ±Ø¹</p><p class="font-medium mt-0.5">{{ $income->branch?->name }}</p></div>
            <div><p class="text-xs text-slate-400">Ù†ÙˆØ¹ Ø§Ù„Ø¯Ø®Ù„</p><p class="font-medium mt-0.5">{{ $income->incomeType?->name }}</p></div>
            <div><p class="text-xs text-slate-400">Ø§Ù„Ù…Ø¨Ù„Øº</p><p class="font-bold text-green-600 text-lg mt-0.5">{{ number_format($income->amount, 2) }} Ø±ÙŠØ§Ù„</p></div>
            <div><p class="text-xs text-slate-400">Ø§Ù„ØªØ§Ø±ÙŠØ®</p><p class="font-medium mt-0.5">{{ $income->date?->format('Y-m-d') }}</p></div>
            <div>
                <p class="text-xs text-slate-400">Ø·Ø±ÙŠÙ‚Ø© Ø§Ù„Ø¯ÙØ¹</p>
                @php $pm = ['cash'=>'Ù†Ù‚Ø¯','bank_transfer'=>'ØªØ­ÙˆÙŠÙ„ Ø¨Ù†ÙƒÙŠ','check'=>'Ø´ÙŠÙƒ','other'=>'Ø£Ø®Ø±Ù‰']; @endphp
                <p class="font-medium mt-0.5">{{ $pm[$income->payment_method] ?? $income->payment_method }}</p>
            </div>
            <div><p class="text-xs text-slate-400">Ø±Ù‚Ù… Ø§Ù„Ù…Ø±Ø¬Ø¹</p><p class="font-medium mt-0.5">{{ $income->reference_number ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-400">Ø£Ø¶ÙŠÙ Ø¨ÙˆØ§Ø³Ø·Ø©</p><p class="font-medium mt-0.5">{{ $income->admin?->name }}</p></div>
            <div><p class="text-xs text-slate-400">ØªØ§Ø±ÙŠØ® Ø§Ù„Ø¥Ø¯Ø®Ø§Ù„</p><p class="font-medium mt-0.5">{{ $income->created_at?->format('Y-m-d H:i') }}</p></div>
        </div>
        @if($income->description)
        <div><p class="text-xs text-slate-400">Ø§Ù„ÙˆØµÙ</p><p class="mt-0.5">{{ $income->description }}</p></div>
        @endif
        @if($income->attachment)
        <div>
            <p class="text-xs text-slate-400">Ø§Ù„Ù…Ø±ÙÙ‚</p>
            <a href="{{ Storage::url($income->attachment) }}" target="_blank" class="text-blue-600 text-sm hover:underline mt-0.5 block">Ø¹Ø±Ø¶ Ø§Ù„Ù…Ø±ÙÙ‚</a>
        </div>
        @endif
        <div class="flex gap-3 pt-4 border-t">
            <a href="{{ route('admin.incomes.edit', $income->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-5 py-2 rounded-lg">ØªØ¹Ø¯ÙŠÙ„</a>
            <a href="{{ route('admin.incomes.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-5 py-2 rounded-lg">Ø±Ø¬ÙˆØ¹</a>
        </div>
    </div>
</div>
@endsection

