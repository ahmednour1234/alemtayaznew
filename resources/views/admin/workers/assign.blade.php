@extends('admin.layouts.app')
@section('title', 'ØªØ¹ÙŠÙŠÙ† Ø¹Ø§Ù…Ù„Ø© Ù„Ø¹Ù…ÙŠÙ„')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.workers.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">ØªØ¹ÙŠÙŠÙ† Ø¹Ø§Ù…Ù„Ø© Ù„Ø¹Ù…ÙŠÙ„</h2>
    </div>

    {{-- Already-assigned warning --}}
    @if($existingClient)
    <div class="mb-5 bg-amber-50 border border-amber-300 rounded-xl px-5 py-4 flex gap-3">
        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div>
            <p class="font-semibold text-amber-800 text-sm">Ù‡Ø°Ù‡ Ø§Ù„Ø¹Ø§Ù…Ù„Ø© Ù…ÙØ¹ÙŠÙŽÙ‘Ù†Ø© Ø¨Ø§Ù„ÙØ¹Ù„</p>
            <p class="text-amber-700 text-xs mt-0.5">
                ØªÙ… ØªØ¹ÙŠÙŠÙ†Ù‡Ø§ Ù…Ø³Ø¨Ù‚Ø§Ù‹ Ù„Ù„Ø¹Ù…ÙŠÙ„ <strong>{{ $existingClient->name }}</strong>
                @if($existingClient->phone) ({{ $existingClient->phone }}) @endif.
                Ø¥Ø¹Ø§Ø¯Ø© Ø§Ù„ØªØ¹ÙŠÙŠÙ† Ø³ØªÙ†Ù‚Ù„Ù‡Ø§ Ù„Ø¹Ù…ÙŠÙ„ Ø¢Ø®Ø±.
            </p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Worker info card --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="text-sm font-semibold text-slate-600 mb-4 pb-2 border-b border-slate-100">Ø¨ÙŠØ§Ù†Ø§Øª Ø§Ù„Ø¹Ø§Ù…Ù„Ø©</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Ø§Ù„Ø§Ø³Ù…</dt>
                    <dd class="font-medium text-slate-800">{{ $worker->name ?: 'â€”' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Ø§Ù„Ø¬Ù†Ø³ÙŠØ©</dt>
                    <dd class="font-medium">{{ $worker->nationality?->name ?? 'â€”' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Ø§Ù„Ù…Ù‡Ù†Ø©</dt>
                    <dd class="font-medium">{{ $worker->profession_label }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Ø§Ù„Ø®Ø¨Ø±Ø©</dt>
                    <dd class="font-medium">{{ $worker->experience_label }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Ø§Ù„Ø­Ø§Ù„Ø©</dt>
                    <dd>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                              style="background: {{ $worker->status_bg }}; color: {{ $worker->status_color }}">
                            {{ $worker->status_label }}
                        </span>
                    </dd>
                </div>
                @if($worker->cv_path)
                <div class="pt-2 border-t border-slate-100">
                    <a href="{{ route('admin.workers.cv', $worker->id) }}" target="_blank"
                       class="flex items-center gap-2 text-sm text-red-600 hover:text-red-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Ø¹Ø±Ø¶ Ù…Ù„Ù CV
                    </a>
                </div>
                @endif
            </dl>
        </div>

        {{-- Assign form --}}
        <div class="lg:col-span-2 space-y-5">
            <form action="{{ route('admin.workers.do-assign', $worker->id) }}" method="POST">
                @csrf

                {{-- Select client / lead --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-600">Ø§Ø®ØªØ± Ø§Ù„Ø¹Ù…ÙŠÙ„</h3>
                        <button type="button"
                                onclick="document.getElementById('addClientModal').style.display='flex'"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ø¥Ø¶Ø§ÙØ© Ø¹Ù…ÙŠÙ„ Ø¬Ø¯ÙŠØ¯
                        </button>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø§Ù„Ø¹Ù…ÙŠÙ„ Ø£Ùˆ Ø§Ù„Ø¹Ù…ÙŠÙ„ Ø§Ù„Ù…Ø­ØªÙ…Ù„ <span class="text-red-500">*</span></label>
                        <select name="assignee" id="assigneeSelect" required data-ts-ignore="1"
                                class="w-full border border-slate-300 rounded-lg text-sm @error('assignee') border-red-400 @enderror">
                            <option value="">Ø§Ø¨Ø­Ø« Ø£Ùˆ Ø§Ø®ØªØ±...</option>
                            @if($clients->isNotEmpty())
                            <optgroup label="âœ… Ø¹Ù…Ù„Ø§Ø¡ Ù…Ø¤ÙƒØ¯ÙˆÙ† ({{ $clients->count() }})">
                                @foreach($clients as $client)
                                <option value="client:{{ $client->id }}"
                                        {{ old('assignee') === 'client:'.$client->id ? 'selected' : '' }}>
                                    {{ $client->name }}{{ $client->phone ? ' â€” '.$client->phone : '' }}
                                </option>
                                @endforeach
                            </optgroup>
                            @endif
                            @if($leads->isNotEmpty())
                            <optgroup label="ðŸ”¶ Ø¹Ù…Ù„Ø§Ø¡ Ù…Ø­ØªÙ…Ù„ÙˆÙ† ({{ $leads->count() }})">
                                @foreach($leads as $lead)
                                <option value="lead:{{ $lead->id }}"
                                        {{ old('assignee') === 'lead:'.$lead->id ? 'selected' : '' }}>
                                    {{ $lead->name }}{{ $lead->phone ? ' â€” '.$lead->phone : '' }} (Ù…Ø­ØªÙ…Ù„)
                                </option>
                                @endforeach
                            </optgroup>
                            @endif
                        </select>
                        @error('assignee')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        @if($clients->isEmpty() && $leads->isEmpty())
                        <p class="text-amber-600 text-xs mt-2">Ù„Ø§ ÙŠÙˆØ¬Ø¯ Ø¹Ù…Ù„Ø§Ø¡ Ø£Ùˆ Ø¹Ù…Ù„Ø§Ø¡ Ù…Ø­ØªÙ…Ù„ÙˆÙ†. ÙŠØ±Ø¬Ù‰ Ø¥Ø¶Ø§ÙØ© Ø¹Ù…ÙŠÙ„ Ø£ÙˆÙ„Ø§Ù‹.</p>
                        @endif
                        @if($leads->isNotEmpty())
                        <p class="text-blue-600 text-xs mt-2">Ø§Ø®ØªÙŠØ§Ø± Ø¹Ù…ÙŠÙ„ Ù…Ø­ØªÙ…Ù„ Ø³ÙŠØ­ÙˆÙ„Ù‡ ØªÙ„Ù‚Ø§Ø¦ÙŠØ§Ù‹ Ø¥Ù„Ù‰ Ø¹Ù…ÙŠÙ„ Ù…Ø¤ÙƒØ¯ Ø¹Ù†Ø¯ Ø§Ù„ØªØ¹ÙŠÙŠÙ†.</p>
                        @endif
                    </div>
                </div>

                {{-- Update worker details --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-slate-600 mb-1 pb-2 border-b border-slate-100">ØªØ­Ø¯ÙŠØ« Ø¨ÙŠØ§Ù†Ø§Øª Ø§Ù„Ø¹Ø§Ù…Ù„Ø© <span class="text-slate-400 font-normal text-xs">(Ø§Ø®ØªÙŠØ§Ø±ÙŠ â€” ÙŠÙ…ÙƒÙ† ØªØ±ÙƒÙ‡Ø§ ÙØ§Ø±ØºØ©)</span></h3>
                    <p class="text-xs text-slate-400 mb-4">Ø§Ù„Ø¨ÙŠØ§Ù†Ø§Øª Ø§Ù„ØªØ§Ù„ÙŠØ© Ø³ØªÙØ­Ø¯Ù‘Ø« Ø¥Ø°Ø§ Ù…ÙÙ„Ø¦Øª</p>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Ø§Ù„Ø§Ø³Ù…</label>
                            <input type="text" name="name" value="{{ old('name', $worker->name) }}"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Ø±Ù‚Ù… Ø§Ù„Ø¬ÙˆØ§Ø²</label>
                            <input type="text" name="passport_number" value="{{ old('passport_number', $worker->passport_number) }}"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Ø§Ù„Ù‡Ø§ØªÙ</label>
                            <input type="text" name="phone" value="{{ old('phone', $worker->phone) }}"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Ø§Ù„Ø¹Ù…Ø±</label>
                            <input type="number" name="age" value="{{ old('age', $worker->age) }}" min="18" max="60"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div class="col-span-2 lg:col-span-4">
                            <label class="block text-xs font-medium text-slate-600 mb-1">Ù…Ù„Ø§Ø­Ø¸Ø§Øª</label>
                            <textarea name="notes" rows="2"
                                      class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none">{{ old('notes', $worker->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" @if($clients->isEmpty() && $leads->isEmpty()) disabled @endif
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm px-6 py-2.5 rounded-lg font-medium">
                        ØªØ¹ÙŠÙŠÙ† Ø§Ù„Ø¹Ø§Ù…Ù„Ø© Ù„Ù„Ø¹Ù…ÙŠÙ„
                    </button>
                    <a href="{{ route('admin.workers.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-6 py-2.5 rounded-lg font-medium">Ø¥Ù„ØºØ§Ø¡</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- â”€â”€â”€ Add Client Modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div id="addClientModal"
     style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.45); align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.25); width:100%; max-width:440px; margin:0 16px; overflow:hidden; font-family:'Cairo',sans-serif;">

        {{-- Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 24px; border-bottom:1px solid #f1f5f9;">
            <span style="font-weight:700; font-size:15px; color:#1e293b;">Ø¥Ø¶Ø§ÙØ© Ø¹Ù…ÙŠÙ„ Ø¬Ø¯ÙŠØ¯</span>
            <button type="button"
                    onclick="document.getElementById('addClientModal').style.display='none'"
                    style="background:none; border:none; cursor:pointer; color:#94a3b8; padding:4px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:20px 24px; display:flex; flex-direction:column; gap:16px;">
            <div id="addClientError"
                 style="display:none; background:#fef2f2; color:#b91c1c; font-size:13px; border-radius:8px; padding:10px 14px; border:1px solid #fecaca;"></div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                    Ø§Ø³Ù… Ø§Ù„Ø¹Ù…ÙŠÙ„ <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" id="qcName" placeholder="Ø£Ø¯Ø®Ù„ Ø§Ù„Ø§Ø³Ù… Ø§Ù„ÙƒØ§Ù…Ù„"
                       style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:10px 12px; font-size:13px; font-family:'Cairo',sans-serif; outline:none; box-sizing:border-box;">
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Ø±Ù‚Ù… Ø§Ù„Ø¬ÙˆØ§Ù„</label>
                <input type="text" id="qcPhone" placeholder="05xxxxxxxx"
                       style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:10px 12px; font-size:13px; font-family:'Cairo',sans-serif; outline:none; box-sizing:border-box;">
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Ø±Ù‚Ù… Ø§Ù„Ù‡ÙˆÙŠØ© / Ø§Ù„Ø¥Ù‚Ø§Ù…Ø©</label>
                <input type="text" id="qcNationalId" placeholder="10 Ø£Ø±Ù‚Ø§Ù…"
                       style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:10px 12px; font-size:13px; font-family:'Cairo',sans-serif; outline:none; box-sizing:border-box;">
                <p style="font-size:11px; color:#94a3b8; margin-top:4px;">Ø¥Ø¯Ø®Ø§Ù„ Ø§Ù„Ù‡ÙˆÙŠØ© ÙŠØ­ÙˆÙ‘Ù„ Ø§Ù„Ø¹Ù…ÙŠÙ„ Ø¥Ù„Ù‰ Ø¹Ù…ÙŠÙ„ Ù…Ø¤ÙƒØ¯ ØªÙ„Ù‚Ø§Ø¦ÙŠØ§Ù‹</p>
            </div>
        </div>

        {{-- Footer --}}
        <div style="display:flex; gap:12px; padding:0 24px 20px;">
            <button type="button" id="saveAddClientBtn"
                    style="flex:1; background:#2563eb; color:#fff; border:none; border-radius:8px; padding:11px; font-size:13px; font-weight:600; cursor:pointer; font-family:'Cairo',sans-serif;">
                Ø­ÙØ¸ ÙˆØ¥Ø¶Ø§ÙØ© Ù„Ù„Ù‚Ø§Ø¦Ù…Ø©
            </button>
            <button type="button"
                    onclick="document.getElementById('addClientModal').style.display='none'"
                    style="background:#f1f5f9; color:#475569; border:none; border-radius:8px; padding:11px 20px; font-size:13px; font-weight:600; cursor:pointer; font-family:'Cairo',sans-serif;">
                Ø¥Ù„ØºØ§Ø¡
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    // â”€â”€ Tom Select init for this page â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function initAssignSelect() {
        var el = document.getElementById('assigneeSelect');
        if (!el || el.tomselect) return;
        new TomSelect(el, {
            placeholder: 'Ø§Ø¨Ø­Ø« Ø¨Ø§Ù„Ø§Ø³Ù… Ø£Ùˆ Ø§Ù„Ù‡Ø§ØªÙ...',
            searchField: ['text'],
            allowEmptyOption: true,
            maxOptions: 500,
            render: {
                no_results: function() { return '<div class="no-results">Ù„Ø§ ØªÙˆØ¬Ø¯ Ù†ØªØ§Ø¦Ø¬</div>'; }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAssignSelect);
    } else {
        initAssignSelect();
    }

    // â”€â”€ Quick-add client save â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'saveAddClientBtn') {
            saveQuickClient();
        }
        // Close modal on backdrop click
        var modal = document.getElementById('addClientModal');
        if (e.target === modal) modal.style.display = 'none';
    });

    // Enter key in modal inputs
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            var active = document.activeElement;
            if (active && ['qcName','qcPhone','qcNationalId'].indexOf(active.id) !== -1) {
                saveQuickClient();
            }
        }
    });

    function saveQuickClient() {
        var errBox  = document.getElementById('addClientError');
        var nameEl  = document.getElementById('qcName');
        var phoneEl = document.getElementById('qcPhone');
        var natEl   = document.getElementById('qcNationalId');
        var btn     = document.getElementById('saveAddClientBtn');

        errBox.style.display = 'none';
        var name = nameEl.value.trim();
        if (!name) {
            errBox.textContent = 'Ø§Ø³Ù… Ø§Ù„Ø¹Ù…ÙŠÙ„ Ù…Ø·Ù„ÙˆØ¨.';
            errBox.style.display = 'block';
            nameEl.focus();
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Ø¬Ø§Ø±Ù Ø§Ù„Ø­ÙØ¸...';

        fetch('{{ route('admin.clients.quick-store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                name:           name,
                phone:          phoneEl.value.trim() || null,
                national_id:    natEl.value.trim() || null,
                classification: 'confirmed',
            }),
        })
        .then(function(res) { return res.json().then(function(d) { return { ok: res.ok, data: d }; }); })
        .then(function(result) {
            if (!result.ok) {
                var msgs = result.data.errors
                    ? Object.values(result.data.errors).flat().join(' ')
                    : (result.data.message || 'Ø­Ø¯Ø« Ø®Ø·Ø£. Ø­Ø§ÙˆÙ„ Ù…Ø¬Ø¯Ø¯Ø§Ù‹.');
                errBox.textContent = msgs;
                errBox.style.display = 'block';
                return;
            }
            var d = result.data;
            var label = d.name + (d.phone ? ' â€” ' + d.phone : '');
            var selectEl = document.getElementById('assigneeSelect');
            var ts = selectEl && selectEl.tomselect;
            if (ts) {
                ts.addOption({ value: 'client:' + d.id, text: label });
                ts.setValue('client:' + d.id);
            } else {
                var opt = document.createElement('option');
                opt.value = 'client:' + d.id;
                opt.text  = label;
                opt.selected = true;
                selectEl.appendChild(opt);
            }
            document.getElementById('addClientModal').style.display = 'none';
        })
        .catch(function() {
            errBox.textContent = 'Ø­Ø¯Ø« Ø®Ø·Ø£ ÙÙŠ Ø§Ù„Ø§ØªØµØ§Ù„. Ø­Ø§ÙˆÙ„ Ù…Ø¬Ø¯Ø¯Ø§Ù‹.';
            errBox.style.display = 'block';
        })
        .finally(function() {
            btn.disabled = false;
            btn.textContent = 'Ø­ÙØ¸ ÙˆØ¥Ø¶Ø§ÙØ© Ù„Ù„Ù‚Ø§Ø¦Ù…Ø©';
        });
    }
})();
</script>
@endpush
@endsection
