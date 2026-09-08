@php
    $fallbackConf = ['dir' => 'rtl', 'font_stack' => ['Cairo', 'sans-serif'],
                     'google_font' => 'Cairo:wght@300;400;500;600;700;800'];
    $locales    = config('locales.supported') ?: ['ar' => $fallbackConf];
    $currentLoc = app()->getLocale();
    if (! isset($locales[$currentLoc])) {
        $currentLoc = config('locales.default', 'ar');
    }
    $locConf = $locales[$currentLoc] ?? $fallbackConf;
    $dir     = $locConf['dir'] ?? 'rtl';
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLoc }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('cv-panel.auth.title') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: {
                primary: { DEFAULT: '#c9a84c', light: '#fdf8e8', dark: '#a88830' },
                navy:    { DEFAULT: '#0f2547', dark: '#081729', light: '#1c3b6e' },
                ink:     { DEFAULT: '#0f172a', muted: '#64748b' },
            },
            fontFamily: { sans: @json($locConf['font_stack']) },
        }}}
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family={{ $locConf['google_font'] }}&display=swap" rel="stylesheet">
</head>
<body class="font-sans bg-navy-dark min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">

    <div class="text-center mb-7">
        <span class="inline-flex w-14 h-14 rounded-2xl bg-primary/15 items-center justify-center mb-4">
            <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </span>
        <h1 class="text-white text-xl font-extrabold">{{ __('cv-panel.title') }}</h1>
        <p class="text-white/60 text-sm mt-1.5">{{ __('cv-panel.auth.subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('cv-panel.login.post') }}"
          class="bg-white rounded-2xl shadow-2xl p-6 sm:p-7 space-y-4">
        @csrf

        @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3">
            <ul class="text-sm text-red-800 space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div>
            <label class="block text-xs font-bold text-ink-muted mb-1.5">{{ __('cv-panel.auth.email') }}</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus dir="ltr"
                   class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
        </div>

        <div>
            <label class="block text-xs font-bold text-ink-muted mb-1.5">{{ __('cv-panel.auth.password') }}</label>
            <input type="password" name="password" required dir="ltr"
                   class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
        </div>

        <label class="flex items-center gap-2 text-sm text-ink-muted cursor-pointer">
            <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-primary focus:ring-primary/40">
            {{ __('cv-panel.auth.remember') }}
        </label>

        <button type="submit"
                class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3.5 rounded-xl transition-colors">
            {{ __('cv-panel.auth.submit') }}
        </button>
    </form>
</div>

</body>
</html>
