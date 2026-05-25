@extends('admin.layouts.app')
@section('title', 'ØªÙØ§ØµÙŠÙ„ Ø§Ù„ÙØ±Ø¹')
@section('content')

<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.branches.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">{{ $branch->name }}</h2>
        @if($branch->active)
            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Ù†Ø´Ø·</span>
        @else
            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">ØºÙŠØ± Ù†Ø´Ø·</span>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-slate-400">Ø§Ø³Ù… Ø§Ù„ÙØ±Ø¹</p>
                <p class="font-medium mt-0.5">{{ $branch->name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Ø§Ù„Ø±Ù…Ø²</p>
                <p class="font-mono font-medium mt-0.5 text-blue-600">{{ $branch->code }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Ø§Ù„Ù…Ø¯ÙŠÙ†Ø©</p>
                <p class="font-medium mt-0.5">{{ $branch->city ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Ø§Ù„Ù…Ø¯ÙŠØ±</p>
                <p class="font-medium mt-0.5">{{ $branch->manager_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Ø§Ù„Ù‡Ø§ØªÙ</p>
                <p class="font-medium mt-0.5">{{ $branch->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">ØªØ§Ø±ÙŠØ® Ø§Ù„Ø¥Ù†Ø´Ø§Ø¡</p>
                <p class="font-medium mt-0.5">{{ $branch->created_at?->format('Y-m-d') }}</p>
            </div>
        </div>
        @if($branch->address)
        <div>
            <p class="text-xs text-slate-400">Ø§Ù„Ø¹Ù†ÙˆØ§Ù†</p>
            <p class="font-medium mt-0.5">{{ $branch->address }}</p>
        </div>
        @endif

        <div class="flex gap-3 pt-4 border-t">
            <a href="{{ route('admin.branches.edit', $branch->id) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-5 py-2 rounded-lg">ØªØ¹Ø¯ÙŠÙ„</a>
            <a href="{{ route('admin.branches.index') }}"
               class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-5 py-2 rounded-lg">Ø±Ø¬ÙˆØ¹</a>
        </div>
    </div>
</div>
@endsection

