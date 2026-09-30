@extends('cv-panel.layout')
@section('title', __('cv-panel.guide.title'))

@section('content')

@php
    /*
     * أقسام الدليل: لكل قسم مفتاح عنوانه وبنوده ومن يراه.
     * تُقرأ النصوص من ملف اللغة فتُترجم مع اللوحة، ولا يظهر للمستخدم
     * إلّا ما يخصّ دوره.
     */
    $sections = [
        [
            'key'     => 'roles',
            'icon'    => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            'items'   => ['role_coord', 'role_agent', 'role_mgr'],
            'show'    => true,
        ],
        [
            'key'     => 'upload',
            'icon'    => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12',
            'items'   => ['upload_1', 'upload_2', 'upload_3', 'upload_4', 'upload_5'],
            'show'    => $isCoordinator,
        ],
        [
            'key'     => 'public',
            'icon'    => 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM3.6 9h16.8M3.6 15h16.8M12 3a15 15 0 010 18M12 3a15 15 0 000 18',
            'items'   => ['public_1', 'public_2', 'public_3'],
            'show'    => true,
        ],
        [
            'key'     => 'reserve',
            'icon'    => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
            'items'   => ['reserve_1', 'reserve_2', 'reserve_3', 'reserve_4', 'reserve_5'],
            'show'    => $isAgent,
        ],
        [
            'key'     => 'after',
            'icon'    => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'items'   => ['after_1', 'after_2', 'after_3', 'after_4'],
            'show'    => $isAgent,
        ],
        [
            'key'     => 'track',
            'icon'    => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
            'items'   => ['track_1', 'track_2', 'track_3'],
            'show'    => $isCoordinator,
        ],
        [
            'key'     => 'delete',
            'icon'    => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
            'items'   => ['delete_1', 'delete_2', 'delete_3', 'delete_4'],
            'show'    => $isCoordinator,
        ],
        [
            'key'     => 'users',
            'icon'    => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'items'   => ['users_1', 'users_2', 'users_3'],
            'show'    => $isManager,
        ],
    ];
@endphp

<div class="max-w-4xl">

    <h1 class="font-extrabold text-lg sm:text-xl mb-1.5">{{ __('cv-panel.guide.title') }}</h1>
    <p class="text-sm text-ink-muted mb-6">{{ __('cv-panel.guide.intro') }}</p>

    <div class="space-y-4">
        @foreach($sections as $section)
        @continue(! $section['show'])

        <section class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex-shrink-0 w-9 h-9 rounded-xl bg-primary-light text-primary-dark flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $section['icon'] }}"/>
                    </svg>
                </span>
                <h2 class="font-extrabold text-base">{{ __('cv-panel.guide.' . $section['key'] . '_title') }}</h2>
            </div>

            @if($section['key'] === 'roles')
            <p class="text-sm text-ink-muted mb-3">{{ __('cv-panel.guide.roles_intro') }}</p>
            @endif

            <ol class="space-y-2.5">
                @foreach($section['items'] as $i => $item)
                <li class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-slate-100 text-ink-muted
                                 text-[11px] font-bold flex items-center justify-center mt-0.5">
                        {{ $i + 1 }}
                    </span>
                    <span class="text-sm leading-relaxed">{{ __('cv-panel.guide.' . $item) }}</span>
                </li>
                @endforeach
            </ol>
        </section>
        @endforeach

        {{-- ملاحظات عامة تخصّ الجميع --}}
        <section class="bg-amber-50 rounded-2xl border border-amber-200 p-5 sm:p-6">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex-shrink-0 w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <h2 class="font-extrabold text-base text-amber-900">{{ __('cv-panel.guide.tips_title') }}</h2>
            </div>

            <ul class="space-y-2.5">
                @foreach(['tip_1', 'tip_2', 'tip_3', 'tip_4'] as $tip)
                <li class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-amber-500 mt-2"></span>
                    <span class="text-sm text-amber-900 leading-relaxed">{{ __('cv-panel.guide.' . $tip) }}</span>
                </li>
                @endforeach
            </ul>
        </section>
    </div>
</div>

@endsection
