@extends('admin.layouts.app')
@section('title', 'ØªØ¹Ø¯ÙŠÙ„ Ù†ÙˆØ¹ Ù…ØµØ±ÙˆÙ')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.expense-types.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">ØªØ¹Ø¯ÙŠÙ„: {{ $type->name }}</h2>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.expense-types.update', $type->id) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø§Ù„Ø§Ø³Ù… <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $type->name) }}" required
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø§Ù„ÙˆØµÙ</label>
                <textarea name="description" rows="3"
                          class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('description', $type->description) }}</textarea>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="active" id="active" value="1" {{ old('active', $type->active) ? 'checked' : '' }} class="rounded">
                <label for="active" class="text-sm font-medium text-slate-700">Ù†Ø´Ø·</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg">ØªØ­Ø¯ÙŠØ«</button>
                <a href="{{ route('admin.expense-types.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-6 py-2.5 rounded-lg">Ø¥Ù„ØºØ§Ø¡</a>
            </div>
        </form>
    </div>
</div>
@endsection

