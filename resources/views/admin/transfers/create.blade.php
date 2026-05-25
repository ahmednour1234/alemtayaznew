@extends('admin.layouts.app')
@section('title', 'Ø¥Ø¶Ø§ÙØ© ØªØ­ÙˆÙŠÙ„ Ù…Ø§Ù„ÙŠ')
@section('content')
<div class="max-w-xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.transfers.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">Ø¥Ø¶Ø§ÙØ© ØªØ­ÙˆÙŠÙ„ Ù…Ø§Ù„ÙŠ</h2>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.transfers.store') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ù…Ù† ÙØ±Ø¹</label>
                    <select name="from_branch_id" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('from_branch_id') border-red-400 @enderror">
                        <option value="">Ø®Ø§Ø±Ø¬ÙŠ / Ø¨Ø¯ÙˆÙ† ÙØ±Ø¹</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('from_branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @error('from_branch_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø¥Ù„Ù‰ ÙØ±Ø¹</label>
                    <select name="to_branch_id" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('to_branch_id') border-red-400 @enderror">
                        <option value="">Ø®Ø§Ø±Ø¬ÙŠ / Ø¨Ø¯ÙˆÙ† ÙØ±Ø¹</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('to_branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @error('to_branch_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø§Ù„Ù…Ø¨Ù„Øº <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('amount') border-red-400 @enderror">
                    @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø§Ù„ØªØ§Ø±ÙŠØ® <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø§Ù„ÙˆØµÙ</label>
                <textarea name="description" rows="2"
                          class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('description') }}</textarea>
            </div>
            <p class="text-xs text-orange-600 bg-orange-50 px-3 py-2 rounded">Ø³ÙŠØªÙ… Ø¥Ø±Ø³Ø§Ù„ Ù‡Ø°Ø§ Ø§Ù„ØªØ­ÙˆÙŠÙ„ Ù„Ù„Ø§Ø¹ØªÙ…Ø§Ø¯ ØªÙ„Ù‚Ø§Ø¦ÙŠØ§Ù‹.</p>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg">Ø­ÙØ¸ ÙˆØ¥Ø±Ø³Ø§Ù„ Ù„Ù„Ø§Ø¹ØªÙ…Ø§Ø¯</button>
                <a href="{{ route('admin.transfers.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-6 py-2.5 rounded-lg">Ø¥Ù„ØºØ§Ø¡</a>
            </div>
        </form>
    </div>
</div>
@endsection

