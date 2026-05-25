@extends('admin.layouts.app')
@section('title', 'ØªØ¹Ø¯ÙŠÙ„ Ù…Ø¯ÙŠØ±')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.settings.admins.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">ØªØ¹Ø¯ÙŠÙ„: {{ $admin->name }}</h2>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.settings.admins.update', $admin->id) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø§Ù„Ø§Ø³Ù… <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $admin->name) }}" required
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø§Ù„Ø¨Ø±ÙŠØ¯ Ø§Ù„Ø¥Ù„ÙƒØªØ±ÙˆÙ†ÙŠ <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $admin->email) }}" required
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">ÙƒÙ„Ù…Ø© Ø§Ù„Ù…Ø±ÙˆØ± Ø§Ù„Ø¬Ø¯ÙŠØ¯Ø© <span class="text-slate-400 text-xs">(Ø§ØªØ±ÙƒÙ‡Ø§ ÙØ§Ø±ØºØ© Ø¥Ø°Ø§ Ù„Ù… ØªØ±ÙŠØ¯ ØªØºÙŠÙŠØ±Ù‡Ø§)</span></label>
                <input type="password" name="password"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">ØªØ£ÙƒÙŠØ¯ ÙƒÙ„Ù…Ø© Ø§Ù„Ù…Ø±ÙˆØ±</label>
                <input type="password" name="password_confirmation"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Ø§Ù„Ø£Ø¯ÙˆØ§Ø±</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($roles as $role)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                               {{ in_array($role->id, old('roles', $admin->roles->pluck('id')->toArray())) ? 'checked' : '' }} class="rounded">
                        {{ $role->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="active" id="active" value="1" {{ old('active', $admin->active) ? 'checked' : '' }} class="rounded">
                <label for="active" class="text-sm font-medium text-slate-700">Ù…Ø¯ÙŠØ± Ù†Ø´Ø·</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg">ØªØ­Ø¯ÙŠØ«</button>
                <a href="{{ route('admin.settings.admins.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-6 py-2.5 rounded-lg">Ø¥Ù„ØºØ§Ø¡</a>
            </div>
        </form>
    </div>
</div>
@endsection

