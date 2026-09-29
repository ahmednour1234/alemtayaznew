@extends('cv-panel.layout')
@section('title', __('cv-panel.users.title'))

@section('content')

<h1 class="font-extrabold text-lg sm:text-xl mb-1.5">{{ __('cv-panel.users.title') }}</h1>
<p class="text-sm text-ink-muted mb-6">{{ __('cv-panel.users.intro') }}</p>

{{-- ══ إضافة مستخدم ══ --}}
<form method="POST" action="{{ route('cv-panel.users.store') }}"
      class="bg-white rounded-2xl border border-slate-200 p-5 mb-6">
    @csrf
    <p class="text-sm font-extrabold mb-4">{{ __('cv-panel.users.add') }}</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <input type="text" name="name" value="{{ old('name') }}" required maxlength="255"
               placeholder="{{ __('cv-panel.users.name') }}"
               class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">

        <input type="email" name="email" value="{{ old('email') }}" required maxlength="255" dir="ltr"
               placeholder="{{ __('cv-panel.users.email') }}"
               class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">

        <input type="password" name="password" required minlength="8" dir="ltr"
               placeholder="{{ __('cv-panel.users.password') }}"
               class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">

        <select name="department" required
                class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            @foreach($roles as $key => $label)
            <option value="{{ $key }}" @selected(old('department') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit"
            class="mt-4 bg-primary hover:bg-primary-dark text-white text-sm font-bold px-6 py-2.5 rounded-xl transition-colors">
        {{ __('cv-panel.users.add') }}
    </button>
</form>

{{-- ══ القائمة ══ --}}
@if($users->isEmpty())
<div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-sm text-ink-muted">
    {{ __('cv-panel.users.empty') }}
</div>
@else
<div class="space-y-3">
    @foreach($users as $user)
    <form method="POST" action="{{ route('cv-panel.users.update', $user->id) }}"
          class="bg-white rounded-2xl border border-slate-200 p-5">
        @csrf
        @method('PUT')

        <div class="flex items-center justify-between gap-3 mb-4">
            <div class="min-w-0">
                <p class="font-extrabold truncate">{{ $user->name }}</p>
                <p class="text-xs text-ink-muted mt-0.5">
                    {{ $user->department_label }}
                    <span class="mx-1">·</span>
                    {{ $user->branch?->name ?? '—' }}
                </p>
            </div>

            <span class="flex-shrink-0 inline-block px-2.5 py-1 rounded-lg text-xs font-bold
                         {{ $user->active ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                {{ $user->active ? __('cv-panel.users.active') : __('cv-panel.users.inactive') }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <input type="text" name="name" value="{{ $user->name }}" required maxlength="255"
                   class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">

            <input type="email" name="email" value="{{ $user->email }}" required maxlength="255" dir="ltr"
                   class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">

            <input type="password" name="password" minlength="8" dir="ltr"
                   placeholder="{{ __('cv-panel.users.password_keep') }}"
                   class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">

            <select name="department" required
                    class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                @foreach($roles as $key => $label)
                <option value="{{ $key }}" @selected($user->department === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2 mt-4">
            <button type="submit"
                    class="bg-primary hover:bg-primary-dark text-white text-sm font-bold px-6 py-2.5 rounded-xl transition-colors">
                {{ __('cv-panel.users.save') }}
            </button>

            {{-- التعطيل زرّ مستقل: إجراء مختلف عن حفظ البيانات --}}
            <button type="submit" form="toggle-{{ $user->id }}"
                    class="text-sm font-bold px-4 py-2.5 rounded-xl transition-colors
                           {{ $user->active ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }}">
                {{ $user->active ? __('cv-panel.users.disable') : __('cv-panel.users.enable') }}
            </button>
        </div>
    </form>

    {{-- نموذج التعطيل خارج نموذج التعديل: لا تتداخل النماذج في HTML --}}
    <form id="toggle-{{ $user->id }}" method="POST"
          action="{{ route('cv-panel.users.toggle', $user->id) }}" class="hidden">
        @csrf
    </form>
    @endforeach
</div>
@endif

@endsection
