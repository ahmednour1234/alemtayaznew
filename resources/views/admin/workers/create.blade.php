@extends('admin.layouts.app')
@section('title', __('workers.add'))
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.workers.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">{{ __('workers.fields.create_title') }}</h2>
    </div>

    <form action="{{ route('admin.workers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- ── Duplicate CV warning ───────────────────────────────────────── --}}
        @if(session('cv_duplicate_warning'))
        <div class="bg-amber-50 border border-amber-300 rounded-xl p-4 flex flex-col gap-3">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-amber-800">{{ __('workers.duplicate.title') }}</p>
                    <p class="text-xs text-amber-700 mt-0.5">
                        @if(session('cv_duplicate_name'))
                            {{ __('workers.duplicate.body_named', ['name' => session('cv_duplicate_name')]) }}
                        @else
                            {{ __('workers.duplicate.body') }}
                        @endif
                        <a href="{{ route('admin.workers.show', session('cv_duplicate_id')) }}" target="_blank" class="underline text-amber-800">{{ __('workers.duplicate.view') }}</a>
                    </p>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" name="force_upload" value="1"
                        class="bg-amber-600 hover:bg-amber-700 text-white text-xs px-4 py-2 rounded-lg font-medium">
                    {{ __('workers.duplicate.force') }}
                </button>
                <a href="{{ route('admin.workers.create') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs px-4 py-2 rounded-lg font-medium">{{ __('workers.fields.cancel') }}</a>
            </div>
        </div>
        @endif
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-600 mb-4 pb-2 border-b border-slate-100">{{ __('workers.fields.basic_data') }}</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('workers.fields.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('workers.fields.passport') }}</label>
                    <input type="text" name="passport_number" value="{{ old('passport_number') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('workers.fields.nationality') }} <span class="text-red-500">*</span></label>
                    <select name="nationality_id"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">{{ __('workers.fields.choose') }}</option>
                        @foreach($nationalities as $nat)
                        <option value="{{ $nat->id }}" {{ old('nationality_id') == $nat->id ? 'selected' : '' }}>{{ $nat->display_name }}</option>
                        @endforeach
                    </select>
                    @error('nationality_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('workers.fields.profession') }} <span class="text-red-500">*</span></label>
                    <select name="profession"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">{{ __('workers.fields.choose') }}</option>
                        @foreach($professions as $key => $label)
                        <option value="{{ $key }}" {{ old('profession') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('workers.fields.gender') }}</label>
                    <select name="gender"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">{{ __('workers.fields.choose') }}</option>
                        @foreach($genders as $key => $label)
                        <option value="{{ $key }}" {{ old('gender') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('workers.fields.experience') }}</label>
                    <select name="experience"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">{{ __('workers.fields.choose') }}</option>
                        @foreach($experiences as $key => $label)
                        <option value="{{ $key }}" {{ old('experience') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('workers.fields.religion') }}</label>
                    <select name="religion"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">{{ __('workers.fields.choose') }}</option>
                        @foreach($religions as $key => $label)
                        <option value="{{ $key }}" {{ old('religion') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('workers.fields.age') }}</label>
                    <input type="number" name="age" value="{{ old('age') }}" min="18" max="60"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('workers.fields.phone1') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('workers.fields.status') }}</label>
                    <select name="status"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @php($current = old('status', 'available'))
                        @foreach(__('workers.statuses') as $key => $label)
                        <option value="{{ $key }}" @selected($current === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @unless(Auth::guard('admin')->user()->isBranchAdmin())
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('workers.fields.branch') }}</label>
                    <select name="branch_id"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">{{ __('workers.fields.no_branch') }}</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endunless
                <div class="col-span-2 lg:col-span-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('workers.fields.cv_upload') }}</label>
                    <input type="file" name="cv" accept=".pdf"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('cv') border-red-400 @enderror">
                    @error('cv')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-2 lg:col-span-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('workers.fields.passport_upload') }}</label>
                    <input type="file" name="passport_image" accept="image/*"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('passport_image') border-red-400 @enderror">
                    <p class="text-xs text-slate-400 mt-1">{{ __('workers.fields.max_size_short') }}</p>
                    @error('passport_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-2 lg:col-span-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('workers.fields.notes') }}</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg font-medium">{{ __('workers.fields.submit') }}</button>
            <a href="{{ route('admin.workers.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-6 py-2.5 rounded-lg font-medium">{{ __('workers.fields.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
