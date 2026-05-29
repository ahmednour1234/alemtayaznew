<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>متابعة شكوى {{ $complaint->complaint_number }} — شركة الامتياز للاستقدام</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Cairo', sans-serif;
    background: #eef2f7;
    min-height: 100vh;
    padding: 32px 16px;
    color: #1e293b;
}
.page {
    background: #fff;
    max-width: 780px;
    margin: 0 auto;
    border-radius: 4px;
    box-shadow: 0 4px 32px rgba(0,0,0,.12);
    overflow: hidden;
}
.top-bar { background: linear-gradient(90deg, #0f3460 0%, #1a56a0 100%); height: 8px; }
/* ── LETTER HEAD ── */
.letter-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 24px 36px 20px; border-bottom: 2px solid #0f3460; gap: 16px;
}
.company-logo { display: flex; align-items: center; gap: 14px; flex: 1; }
.logo-circle {
    width: 68px; height: 68px; border-radius: 50%;
    border: 2.5px solid #0f3460; padding: 5px; background: #fff; flex-shrink: 0;
}
.lc-inner {
    width: 100%; height: 100%; border-radius: 50%;
    background: linear-gradient(135deg, #0f3460, #1a56a0);
    display: flex; align-items: center; justify-content: center;
}
.company-name-ar { font-size: 18px; font-weight: 900; color: #0f3460; line-height: 1.2; }
.company-name-en { font-size: 10px; font-weight: 700; color: #64748b; letter-spacing: .06em; margin-top: 3px; }
.ticket-badge {
    background: linear-gradient(135deg, #0f3460 0%, #1a56a0 100%);
    color: #fff; border-radius: 10px; padding: 10px 16px; text-align: center; flex-shrink: 0;
}
.ticket-badge .tb-label { font-size: 11px; font-weight: 600; opacity: .85; margin-bottom: 4px; }
.ticket-badge .tb-num   { font-size: 13px; font-weight: 800; letter-spacing: .03em; font-family: 'Courier New', monospace; }
/* ── META ── */
.letter-meta {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 20px 36px 0;
}
.meta-right .rt-label  { color: #64748b; font-size: 13px; }
.meta-right .rt-name   { font-size: 17px; font-weight: 900; color: #0f3460; margin-top: 2px; }
.meta-right .rt-honor  { font-size: 13px; color: #475569; margin-top: 2px; }
.meta-left .ml-label   { font-size: 12px; color: #64748b; }
.meta-left .ml-val     { font-size: 14px; font-weight: 700; color: #0f3460; }
/* ── GREETING / BODY ── */
.greeting  { padding: 16px 36px 0; font-size: 14px; color: #374151; line-height: 2; }
.subject-box {
    margin: 18px 36px;
    background: #f0f5ff; border: 1px solid #bfdbfe; border-right: 4px solid #0f3460;
    border-radius: 8px; padding: 13px 18px;
    display: flex; align-items: center; gap: 12px;
    font-size: 15px; font-weight: 800; color: #0f3460;
}
/* ── STALE ── */
.stale-box {
    margin: 0 36px 18px;
    background: #fff1f2; border: 1px solid #fecdd3; border-right: 4px solid #e11d48;
    border-radius: 8px; padding: 13px 16px;
    display: flex; align-items: flex-start; gap: 12px; font-size: 13px;
}
/* ── GREEN BOX ── */
.assurance-box {
    margin: 0 36px 22px;
    background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
    border-radius: 14px; padding: 22px 24px; color: #fff;
}
.assurance-title {
    text-align: center; font-size: 16px; font-weight: 800; margin-bottom: 18px;
    display: flex; align-items: center; justify-content: center; gap: 10px;
}
.assurance-cols { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; }
.assurance-col {
    background: rgba(255,255,255,.1); border-radius: 10px; padding: 14px 12px; text-align: center;
}
.ac-icon {
    width: 38px; height: 38px; border-radius: 50%;
    background: rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;
}
.ac-title { font-size: 13px; font-weight: 700; margin-bottom: 4px; }
.ac-desc  { font-size: 11px; opacity: .75; line-height: 1.5; }
/* ── TIMELINE ── */
.tl-section { padding: 0 36px 8px; }
.tl-header {
    font-size: 13px; font-weight: 800; color: #0f3460; margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px;
}
.tl-header::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
.tl-list { display: flex; flex-direction: column; }
.tl-item { display: flex; gap: 14px; position: relative; }
.tl-spine { flex-shrink: 0; width: 32px; display: flex; flex-direction: column; align-items: center; padding-top: 4px; position: relative; }
.tl-item:not(:last-child) .tl-spine::after {
    content: ''; position: absolute; top: 36px; width: 2px;
    height: calc(100% - 32px); background: #e2e8f0; z-index: 0;
}
.tl-dot {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 800; z-index: 1; position: relative; flex-shrink: 0;
}
.dot-done   { background: #16a34a; color: #fff; }
.dot-active { background: #f59e0b; color: #fff; }
.dot-wait   { background: #f1f5f9; color: #94a3b8; border: 2px solid #e2e8f0; }
.dot-esc    { background: #dc2626; color: #fff; }
.tl-body { padding: 4px 0 22px 0; flex: 1; }
.tl-title { font-size: 14px; font-weight: 700; color: #0f3460; }
.tl-date  { font-size: 12px; color: #94a3b8; margin-top: 2px; }
/* ── RESOLUTION ── */
.resolution-box {
    margin: 0 36px 20px;
    background: #f0fdf4; border: 1px solid #86efac; border-radius: 10px; padding: 16px 18px;
}
.rb-title { font-size: 13px; font-weight: 800; color: #166534; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
.rb-text  { font-size: 14px; color: #166534; line-height: 1.8; }
/* ── MUSANED ── */
.musaned-row {
    margin: 0 36px 20px;
    background: #fff7ed; border: 1px solid #fed7aa; border-radius: 10px;
    padding: 13px 16px; display: flex; align-items: center; gap: 14px;
}
/* ── CONTACT ── */
.contact-bar {
    margin: 0 36px 24px;
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;
    padding: 14px 18px; display: flex; align-items: center; gap: 12px;
}
/* ── FOOTER ── */
.letter-footer {
    border-top: 1px solid #e2e8f0; padding: 20px 36px;
    display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 20px;
}
.footer-contact { font-size: 11.5px; color: #64748b; line-height: 1.9; }
.fc-row { display: flex; align-items: center; gap: 6px; }
.footer-seal {
    width: 78px; height: 78px; border-radius: 50%; border: 2.5px dashed #0f3460;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    text-align: center; font-size: 9px; font-weight: 700; color: #0f3460; gap: 2px; flex-shrink: 0;
}
.footer-sign { text-align: right; font-size: 12px; color: #475569; line-height: 1.9; }
.footer-sign strong { color: #0f3460; font-size: 13px; font-weight: 900; display: block; }
.bottom-bar { background: linear-gradient(90deg, #0f3460 0%, #1a56a0 100%); height: 6px; }
@keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:.6;} }
@media print { body{background:#fff;padding:0;} .page{box-shadow:none;max-width:100%;border-radius:0;} }
@media (max-width:600px) {
    .letter-head,.letter-meta,.greeting,.tl-section { padding-right:20px;padding-left:20px; }
    .subject-box,.stale-box,.assurance-box,.resolution-box,.musaned-row,.contact-bar,.letter-footer { margin-right:20px;margin-left:20px; }
    .assurance-cols { grid-template-columns:1fr; }
    .letter-footer  { grid-template-columns:1fr; }
    .footer-seal    { display:none; }
}
</style>
</head>
<body>
@php
    $isStale     = in_array($complaint->status, ['new','in_progress']) && $complaint->created_at->lt(now()->subDays(7));
    $isResolved  = in_array($complaint->status, ['resolved','closed']);
    $isEscalated = $complaint->status === 'escalated';
    $clientName  = $complaint->client?->name ?? 'العميل الكريم';
    $subjectLine = $complaint->problem_type_label . ($complaint->branch ? ' – ' . $complaint->branch->name : '');
    $step2 = $complaint->processed_at ? 'done' : ($complaint->status !== 'new' ? 'active' : 'wait');
    $step3 = $isResolved ? 'done' : ($isEscalated ? 'esc' : 'wait');
@endphp

<div class="page">
  <div class="top-bar"></div>

  {{-- Letter Head --}}
  <div class="letter-head">
    <div class="company-logo">
      <img src="{{ asset('1759760768-33.webp') }}" alt="شركة الامتياز للاستقدام"
           style="height:52px;object-fit:contain;">
    </div>
    <div class="ticket-badge">
      <div class="tb-label">متابعة شكوى</div>
      <div class="tb-num">{{ $complaint->complaint_number }}</div>
    </div>
  </div>

  {{-- Meta --}}
  <div class="letter-meta">
    <div class="meta-right">
      <div class="rt-label">السيد/</div>
      <div class="rt-name">{{ $clientName }}</div>
      <div class="rt-honor">المحترم</div>
    </div>
    <div class="meta-left" style="text-align:left;">
      <div class="ml-label">التاريخ:</div>
      <div class="ml-val">{{ $complaint->created_at->format('Y/m/d') }}</div>
    </div>
  </div>

  {{-- Greeting --}}
  <div class="greeting">
    السلام عليكم ورحمة الله وبركاته ...<br><br>
    نشكركم على تواصلكم معنا وإفادتكم بملاحظاتكم. نود أن نطمئنكم بأنه قد تم استلام شكواكم
    برقم (<strong style="color:#0f3460;">{{ $complaint->complaint_number }}</strong>)،
    وتم تحويلها لدراستها ومعالجتها بشكل عاجل.
  </div>

  {{-- Subject --}}
  <div class="subject-box">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
      <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    الموضوع: شكوى بخصوص {{ $subjectLine }}
  </div>

  {{-- Stale --}}
  @if($isStale)
  <div class="stale-box">
    <svg width="20" height="20" fill="none" stroke="#e11d48" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
      <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <div>
      <strong style="color:#9f1239;">شكواك قيد المتابعة — نأسف للتأخير</strong><br>
      <span style="color:#be123c;">مضى على تسجيل هذه الشكوى {{ $daysOpen }} يومًا وفريق الدعم يعمل على حلها بشكل عاجل.</span>
    </div>
  </div>
  @endif

  {{-- Assurance --}}
  <div class="assurance-box">
    <div class="assurance-title">
      <svg width="22" height="22" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
      </svg>
      نطمئنكم أننا نعمل على حل المشكلة
    </div>
    <div class="assurance-cols">
      <div class="assurance-col">
        <div class="ac-icon">
          <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
            <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        <div class="ac-title">فريق متخصص</div>
        <div class="ac-desc">يتم التعامل مع الشكوى من قبل فريق متخصص</div>
      </div>
      <div class="assurance-col">
        <div class="ac-icon">
          <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="ac-title">أولوية قصوى</div>
        <div class="ac-desc">تم إعطاء الشكوى أولوية: {{ $complaint->priority_label }}</div>
      </div>
      <div class="assurance-col">
        <div class="ac-icon">
          <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
            <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
        </div>
        <div class="ac-title">متابعة مستمرة</div>
        <div class="ac-desc">نتابع الشكوى حتى إغلاقها بشكل نهائي</div>
      </div>
    </div>
  </div>

  {{-- Timeline --}}
  <div class="tl-section">
    <div class="tl-header">
      <svg width="15" height="15" fill="none" stroke="#0f3460" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
      </svg>
      مراحل معالجة الشكوى
    </div>
    <div class="tl-list">

      {{-- Step 1 --}}
      <div class="tl-item">
        <div class="tl-spine">
          <div class="tl-dot dot-done">
            <svg width="14" height="14" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
          </div>
        </div>
        <div class="tl-body">
          <div class="tl-title">تم استلام الشكوى</div>
          <div class="tl-date">{{ $complaint->created_at->format('Y/m/d') }}</div>
        </div>
      </div>

      {{-- Step 2 --}}
      <div class="tl-item">
        <div class="tl-spine">
          @if($step2 === 'done')
            <div class="tl-dot dot-done"><svg width="14" height="14" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg></div>
          @elseif($step2 === 'active')
            <div class="tl-dot dot-active" style="animation:pulse 2s infinite;">٢</div>
          @else
            <div class="tl-dot dot-wait">٢</div>
          @endif
        </div>
        <div class="tl-body">
          <div class="tl-title" style="{{ $step2 === 'wait' ? 'color:#94a3b8;' : '' }}">جاري دراسة وتحليل المشكلة</div>
          <div class="tl-date">@if($complaint->processed_at){{ $complaint->processed_at->format('Y/m/d') }}@else —@endif</div>
        </div>
      </div>

      {{-- Step 3 --}}
      <div class="tl-item">
        <div class="tl-spine">
          @if($step3 === 'done')
            <div class="tl-dot dot-done"><svg width="14" height="14" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg></div>
          @elseif($step3 === 'esc')
            <div class="tl-dot dot-esc" style="font-size:16px;font-weight:900;">!</div>
          @else
            <div class="tl-dot dot-wait">٣</div>
          @endif
        </div>
        <div class="tl-body">
          <div class="tl-title" style="{{ $step3 === 'wait' ? 'color:#94a3b8;' : '' }}">
            @if($step3 === 'esc') تم تصعيد الشكوى للإدارة العليا
            @elseif($step3 === 'done') تم الحل والإغلاق
            @else جاري الحل والتواصل معكم @endif
          </div>
          <div class="tl-date">@if($complaint->resolved_at){{ $complaint->resolved_at->format('Y/m/d') }}@else —@endif</div>
        </div>
      </div>

    </div>
  </div>

  {{-- Resolution text --}}
  @if($complaint->resolution && $isResolved)
  <div class="resolution-box">
    <div class="rb-title">
      <svg width="15" height="15" fill="none" stroke="#166534" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      نتيجة المعالجة
    </div>
    <div class="rb-text">{{ $complaint->resolution }}</div>
  </div>
  @endif

  {{-- Musaned --}}
  @if($complaint->on_musaned && $complaint->musaned_number)
  <div class="musaned-row">
    <div style="width:40px;height:40px;background:#fed7aa;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg width="20" height="20" fill="none" stroke="#c2410c" stroke-width="2" viewBox="0 0 24 24">
        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
    </div>
    <div>
      <div style="font-size:12px;font-weight:800;color:#9a3412;">مسجلة على منصة مساند</div>
      <div style="font-size:13px;color:#7c2d12;margin-top:2px;">رقم البلاغ: <strong style="font-family:'Courier New',monospace;">{{ $complaint->musaned_number }}</strong></div>
    </div>
  </div>
  @endif

  {{-- Image attachments --}}
  @php $images = $complaint->attachments->filter(fn($a) => $a->is_image); @endphp
  @if($images->isNotEmpty())
  <div style="padding:0 36px 20px;">
    <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">مستندات مرفقة</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:8px;">
      @foreach($images as $img)
      <a href="{{ $img->url }}" target="_blank" style="border-radius:8px;overflow:hidden;display:block;border:1px solid #e2e8f0;">
        <img src="{{ $img->url }}" alt="{{ $img->original_name }}" style="width:100%;height:80px;object-fit:cover;display:block;">
      </a>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Contact note --}}
  <div class="contact-bar">
    <div style="width:40px;height:40px;background:#dbeafe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg width="20" height="20" fill="none" stroke="#1d4ed8" stroke-width="2" viewBox="0 0 24 24">
        <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
      </svg>
    </div>
    <div>
      <div style="font-size:13px;font-weight:700;color:#1e3a5f;">في حال وجود أي استفسار إضافي لا تترددوا في التواصل معنا:</div>
      <div style="font-size:12px;color:#475569;margin-top:2px;">نحن هنا لخدمتكم</div>
    </div>
  </div>

  {{-- Footer --}}
  <div class="letter-footer">
    <div class="footer-contact">
      <div class="fc-row">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
        9200 12345
      </div>
      <div class="fc-row">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        info@alimtiyaz.com
      </div>
      <div class="fc-row">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
        www.alimtiyaz.com
      </div>
    </div>
    <div class="footer-seal">
      <img src="{{ asset('1759760768-33.webp') }}" alt="شركة الامتياز للاستقدام"
           style="width:64px;height:64px;object-fit:contain;border-radius:50%;background:#fff;padding:4px;">
    </div>
    <div class="footer-sign">
      مع خالص الشكر والتقدير
      <strong>إدارة خدمة العملاء</strong>
      شركة الامتياز للاستقدام
    </div>
  </div>

  <div class="bottom-bar"></div>
</div>
</body>
</html>
