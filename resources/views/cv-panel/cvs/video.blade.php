@extends('cv-panel.layout')
@section('title', 'فيديو العاملة')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('cv-panel.cvs.index') }}"
       class="text-sm font-bold text-ink-muted hover:text-ink">→ رجوع</a>

    <h1 class="font-extrabold text-lg sm:text-xl mt-3 mb-1">فيديو العاملة</h1>
    <p class="text-sm text-ink-muted mb-5">
        #{{ $worker->id }} · {{ $worker->name }} · {{ $worker->nationality?->display_name ?? '—' }}
    </p>

    @if($errors->any())
    <div class="mb-4 rounded-xl bg-red-50 text-red-700 text-sm px-4 py-3">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
    @endif

    {{-- الفيديو الحالي --}}
    @if($worker->video_path || $worker->video_url)
    <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-5">
        <p class="font-bold text-sm mb-3">الفيديو الحالي</p>
        @if($worker->video_path)
        <video src="{{ route('cv-panel.cvs.video.file', $worker->id) }}" controls preload="metadata"
               class="w-full max-h-96 rounded-xl bg-black"></video>
        @endif
        @if($worker->video_url)
        <a href="{{ $worker->video_url }}" target="_blank" rel="noopener"
           class="block mt-2 text-sm text-navy hover:underline break-all" dir="ltr">{{ $worker->video_url }}</a>
        @endif

        <form method="POST" action="{{ route('cv-panel.cvs.video.update', $worker->id) }}" class="mt-3"
              onsubmit="return confirm('حذف الفيديو؟')">
            @csrf
            <input type="hidden" name="remove" value="1">
            <button class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-600 hover:text-white px-3 py-2 rounded-lg">حذف الفيديو</button>
        </form>
    </div>
    @endif

    <form method="POST" action="{{ route('cv-panel.cvs.video.update', $worker->id) }}" enctype="multipart/form-data"
          x-data="{ sending: false }" @submit="sending = true"
          class="bg-white rounded-2xl border border-slate-200 p-5 space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-ink-muted mb-1.5">رابط الفيديو (Google Drive / YouTube / ...)</label>
            <input type="url" name="video_url" dir="ltr" value="{{ old('video_url', $worker->video_url) }}"
                   placeholder="https://drive.google.com/file/d/..."
                   class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            <p class="text-xs text-ink-muted mt-1.5">
                في Google Drive: «مشاركة» ← «أي شخص لديه الرابط» — وإلا العميل مش هيقدر يفتحه.
            </p>
        </div>

        <div class="flex items-center gap-3 text-xs text-ink-muted">
            <span class="flex-1 h-px bg-slate-200"></span> أو <span class="flex-1 h-px bg-slate-200"></span>
        </div>

        <div>
            <label class="block text-xs font-semibold text-ink-muted mb-1.5">ارفع ملف فيديو (حتى 100 ميجا)</label>
            <input type="file" name="video_file" accept="video/*"
                   class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm file:me-3 file:py-1.5 file:px-4
                          file:rounded-lg file:border-0 file:bg-navy file:text-white file:text-xs file:font-bold">
        </div>

        <button type="submit" :disabled="sending"
                class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 rounded-xl transition-colors disabled:opacity-60">
            <span x-show="!sending">حفظ الفيديو</span>
            <span x-show="sending" x-cloak>جاري الرفع...</span>
        </button>
    </form>
</div>
@endsection
