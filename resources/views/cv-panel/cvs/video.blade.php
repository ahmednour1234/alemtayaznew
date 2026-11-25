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
        <div class="flex items-center justify-between mb-3">
            <p class="font-bold text-sm">الفيديو الحالي</p>
            <form method="POST" action="{{ route('cv-panel.cvs.video.update', $worker->id) }}"
                  onsubmit="return confirm('متأكد إنك عايز تحذف الفيديو؟ مش هيظهر للعميل تاني.')">
                @csrf
                <input type="hidden" name="remove" value="1">
                <button class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 px-3 py-2 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    حذف الفيديو
                </button>
            </form>
        </div>
        @if($worker->video_path)
        <video src="{{ route('cv-panel.cvs.video.file', $worker->id) }}" controls preload="metadata"
               class="w-full max-h-96 rounded-xl bg-black"></video>
        @endif
        @if($worker->video_url)
        <a href="{{ $worker->video_url }}" target="_blank" rel="noopener"
           class="block mt-2 text-sm text-navy hover:underline break-all" dir="ltr">{{ $worker->video_url }}</a>
        @endif

    </div>
    @endif

    <form method="POST" action="{{ route('cv-panel.cvs.video.update', $worker->id) }}" enctype="multipart/form-data"
          x-data="videoUpload()" @submit="submit($event)"
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
            <label class="block text-xs font-semibold text-ink-muted mb-1.5">ارفع ملف فيديو (حتى 500 ميجا — بيترفع على أجزاء)</label>
            <input type="file" x-ref="file" accept="video/*"
                   class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm file:me-3 file:py-1.5 file:px-4
                          file:rounded-lg file:border-0 file:bg-navy file:text-white file:text-xs file:font-bold">
        </div>

        <div x-show="total" x-cloak>
            <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-primary transition-all" :style="'width:' + pct + '%'"></div>
            </div>
            <p class="text-xs text-ink-muted mt-1" x-text="pct + '%'"></p>
        </div>
        <p x-show="error" x-cloak x-text="error" class="text-sm font-bold text-red-600"></p>

        <button type="submit" :disabled="sending"
                class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 rounded-xl transition-colors disabled:opacity-60">
            <span x-show="!sending">حفظ الفيديو</span>
            <span x-show="sending" x-cloak>جاري الرفع...</span>
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
function videoUpload() {
    return {
        sending: false, total: 0, done: 0, error: '',
        get pct() { return this.total ? Math.round(this.done / this.total * 100) : 0; },

        async submit(e) {
            const file = this.$refs.file.files[0];
            // بدون ملف: نسيب الفورم يحفظ الرابط عادي
            if (!file) { this.sending = true; return; }
            e.preventDefault();

            const CHUNK = {{ \App\Http\Controllers\CvPanel\VideoController::CHUNK_BYTES }};
            const MAX   = {{ \App\Http\Controllers\CvPanel\VideoController::MAX_BYTES }};
            if (file.size > MAX) { this.error = 'الفيديو أكبر من 500 ميجا — ارفعه على Google Drive وحط الرابط.'; return; }

            this.sending = true; this.error = '';
            this.total = Math.ceil(file.size / CHUNK); this.done = 0;
            const uploadId = Array.from(crypto.getRandomValues(new Uint8Array(16)), b => b.toString(16).padStart(2, '0')).join('');
            const token = document.querySelector('meta[name=csrf-token]').content;

            try {
                // لو فيه رابط متكتب نحفظه الأول
                const url = this.$el.querySelector('[name=video_url]').value.trim();
                if (url) {
                    const fd = new FormData(); fd.append('_token', token); fd.append('video_url', url);
                    await fetch(this.$el.action, { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } });
                }

                for (let i = 0; i < this.total; i++) {
                    const fd = new FormData();
                    fd.append('_token', token);
                    fd.append('upload_id', uploadId);
                    fd.append('index', i);
                    fd.append('total', this.total);
                    fd.append('name', file.name);
                    fd.append('chunk', file.slice(i * CHUNK, (i + 1) * CHUNK), 'chunk');

                    // إعادة محاولة لكل قطعة لو النت فصل لحظة
                    let res, json = {};
                    for (let attempt = 0; attempt < 3; attempt++) {
                        try {
                            res = await fetch(@js(route('cv-panel.cvs.video.chunk', $worker->id)), {
                                method: 'POST', body: fd, headers: { 'Accept': 'application/json' },
                            });
                            json = await res.json().catch(() => ({}));
                            if (res.ok || res.status === 422) break;
                        } catch (_) {}
                        await new Promise(r => setTimeout(r, 1500));
                    }
                    if (!res || !res.ok) {
                        const errs = json.errors ? Object.values(json.errors).flat() : [];
                        throw new Error(errs[0] || json.message || 'فشل الرفع');
                    }
                    this.done = i + 1;
                }
                window.location.reload();
            } catch (err) {
                this.error = err.message;
                this.sending = false;
            }
        },
    };
}
</script>
@endpush
