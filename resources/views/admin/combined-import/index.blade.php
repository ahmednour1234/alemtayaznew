@extends('admin.layouts.app')
@section('title', 'استيراد الإيرادات والمصروفات')

@push('styles')
<style>
.imp-page {
    direction: rtl;
    font-family: Cairo, sans-serif;
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 0 60px;
}
.imp-hero {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
    border-radius: 24px;
    padding: 48px 52px 44px;
    margin-bottom: 36px;
    position: relative;
    overflow: hidden;
    color: #fff;
}
.imp-hero::before {
    content: '';
    position: absolute;
    top: -60px; left: -60px;
    width: 280px; height: 280px;
    background: rgba(255,255,255,.07);
    border-radius: 50%;
    pointer-events: none;
}
.imp-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; right: -40px;
    width: 320px; height: 320px;
    background: rgba(255,255,255,.05);
    border-radius: 50%;
    pointer-events: none;
}
.imp-hero-label { font-size:12px; font-weight:800; letter-spacing:.08em; color:#93c5fd; text-transform:uppercase; margin:0 0 10px; }
.imp-hero-title { font-size:30px; font-weight:900; margin:0 0 10px; line-height:1.25; }
.imp-hero-sub   { font-size:14.5px; color:#bfdbfe; margin:0; font-weight:500; }
.imp-hero-badge {
    position: absolute;
    left: 52px; top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,.15);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 20px;
    padding: 18px 26px;
    text-align: center;
    color: #fff;
    min-width: 120px;
}
.imp-hero-badge-num { font-size:34px; font-weight:900; line-height:1; }
.imp-hero-badge-txt { font-size:11px; color:#bfdbfe; margin-top:4px; }

.imp-alert { border-radius:14px; padding:16px 20px; margin-bottom:24px; display:flex; align-items:flex-start; gap:12px; }
.imp-alert-success { background:#f0fdf4; border:1.5px solid #86efac; color:#15803d; }
.imp-alert-warn    { background:#fffbeb; border:1.5px solid #fcd34d; color:#92400e; }
.imp-alert-error   { background:#fff1f2; border:1.5px solid #fca5a5; color:#b91c1c; }
.imp-alert-icon    { flex-shrink:0; margin-top:1px; }
.imp-alert-body    { flex:1; }
.imp-alert-title   { font-size:14px; font-weight:800; margin:0 0 4px; }
.imp-alert-list    { margin:0; padding-right:16px; font-size:13px; line-height:1.7; }

.imp-card {
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 22px;
    box-shadow: 0 4px 32px rgba(15,23,42,.08);
    overflow: hidden;
    margin-bottom: 28px;
    animation: fadeIn .35s ease;
}
.imp-card.primary { border-color:#2563eb; box-shadow:0 6px 40px rgba(37,99,235,.14); }
.imp-card-header {
    background: linear-gradient(135deg,#eff6ff 0%,#fff 100%);
    border-bottom: 1.5px solid #dbeafe;
    padding: 22px 30px;
    display: flex; align-items: center; gap: 14px;
}
.imp-card-header-icon {
    width:50px; height:50px;
    background: linear-gradient(135deg,#2563eb,#1d4ed8);
    border-radius: 16px;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
    box-shadow: 0 4px 14px rgba(37,99,235,.3);
}
.imp-card-header-title { font-size:18px; font-weight:900; color:#1e293b; margin:0 0 3px; }
.imp-card-header-sub   { font-size:12.5px; color:#64748b; margin:0; }
.imp-card-body { padding:30px; }

.imp-drop {
    border: 2.5px dashed #bfdbfe;
    border-radius: 18px;
    padding: 52px 24px;
    text-align: center;
    background: linear-gradient(180deg,#f8fbff,#eff6ff);
    cursor: pointer;
    position: relative;
    transition: .22s;
}
.imp-drop:hover, .imp-drop.dragover { border-color:#2563eb; background:linear-gradient(180deg,#eff6ff,#dbeafe); }
.imp-drop input[type="file"] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
.imp-drop-icon {
    width:72px; height:72px;
    background: linear-gradient(135deg,#2563eb,#1d4ed8);
    border-radius: 22px;
    display:flex; align-items:center; justify-content:center;
    margin: 0 auto 18px;
    box-shadow: 0 6px 20px rgba(37,99,235,.25);
}
.imp-drop-title { font-size:17px; font-weight:900; color:#1e293b; margin:0 0 7px; }
.imp-drop-sub   { font-size:13px; color:#94a3b8; margin:0; }

.imp-file-info {
    display:none; align-items:center; gap:12px;
    background:#f0fdf4; border:1.5px solid #86efac;
    border-radius:12px; padding:14px 18px; margin-top:16px;
}
.imp-file-info.show { display:flex; }
.imp-file-info-name  { font-size:13.5px; font-weight:700; color:#15803d; flex:1; }
.imp-file-info-clear { background:none; border:none; cursor:pointer; color:#94a3b8; padding:0; display:flex; }

.imp-cols-grid {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 12px;
    margin: 0; padding: 0; list-style: none;
}
.imp-col-item {
    background:#f8fafc; border:1px solid #f1f5f9;
    border-radius:14px; padding:14px 16px;
    display:flex; align-items:flex-start; gap:12px;
}
.imp-col-letter {
    width:34px; height:34px;
    background: linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff; border-radius:10px;
    font-size:15px; font-weight:900;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
}
.imp-col-letter.skip { background:linear-gradient(135deg,#94a3b8,#64748b); }
.imp-col-label { font-size:13px; font-weight:800; color:#1e293b; margin:0 0 2px; }
.imp-col-desc  { font-size:11.5px; color:#64748b; margin:0; }
.imp-col-required { display:inline-block; background:#fee2e2; color:#b91c1c; border-radius:6px; padding:1px 7px; font-size:10.5px; font-weight:700; margin-top:4px; }
.imp-col-optional { display:inline-block; background:#f1f5f9; color:#64748b; border-radius:6px; padding:1px 7px; font-size:10.5px; font-weight:700; margin-top:4px; }

.imp-note {
    background:linear-gradient(135deg,#f0fdf4,#dcfce7);
    border:1.5px solid #86efac; border-radius:14px;
    padding:16px 20px; font-size:13px; color:#166534;
    line-height:1.7; margin-top:20px;
}
.imp-note strong { color:#14532d; }

.imp-btn-primary {
    background: linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff; border:0; border-radius:14px;
    padding:15px 36px; font-size:15px; font-weight:900;
    cursor:pointer; font-family:Cairo,sans-serif;
    display:inline-flex; align-items:center; gap:10px;
    transition:.2s; box-shadow:0 4px 16px rgba(37,99,235,.3);
    width:100%; justify-content:center;
}
.imp-btn-primary:hover { box-shadow:0 6px 22px rgba(37,99,235,.4); transform:translateY(-1px); }
.imp-btn-primary:disabled { opacity:.7; cursor:not-allowed; transform:none; }

.imp-btn-ghost {
    background:#fff; color:#475569; border:1.5px solid #e2e8f0;
    border-radius:14px; padding:13px 28px; font-size:14px; font-weight:700;
    cursor:pointer; font-family:Cairo,sans-serif;
    display:inline-flex; align-items:center; gap:8px;
    text-decoration:none; transition:.2s;
}
.imp-btn-ghost:hover { background:#f8fafc; border-color:#cbd5e1; }

.imp-btn-green {
    background:linear-gradient(135deg,#16a34a,#15803d);
    color:#fff; border:0; border-radius:14px;
    padding:13px 28px; font-size:14px; font-weight:800;
    cursor:pointer; font-family:Cairo,sans-serif;
    display:inline-flex; align-items:center; gap:8px;
    text-decoration:none; transition:.2s;
    box-shadow:0 3px 12px rgba(22,163,74,.25);
}
.imp-btn-green:hover { opacity:.9; }

.imp-details {
    background:#fff; border:1.5px solid #e2e8f0;
    border-radius:18px; overflow:hidden; margin-bottom:28px;
}
.imp-details-summary {
    padding:20px 28px;
    display:flex; align-items:center; gap:14px;
    cursor:pointer; user-select:none; list-style:none;
    color:#475569; font-size:15px; font-weight:800;
}
.imp-details-summary:hover { background:#f8fafc; }
.imp-details-body {
    padding:28px; background:#fafbfc;
    border-top:1.5px solid #f1f5f9;
}
.imp-tpl-grid { display:grid; grid-template-columns:1fr 1fr; gap:24px; align-items:start; }
.imp-tbl { width:100%; border-collapse:collapse; font-size:13px; }
.imp-tbl th { background:#f8fafc; color:#64748b; font-weight:800; padding:10px 14px; text-align:right; border-bottom:1px solid #f1f5f9; }
.imp-tbl td { padding:10px 14px; color:#334155; border-bottom:1px solid #f8fafc; }
.imp-tbl tr:last-child td { border-bottom:none; }
.imp-tbl code { background:#f1f5f9; border-radius:6px; padding:1px 6px; font-size:12px; color:#2563eb; }
.imp-badge-green { display:inline-block; background:#dcfce7; color:#15803d; border-radius:999px; padding:4px 12px; font-size:11.5px; font-weight:800; }
.imp-badge-red   { display:inline-block; background:#fee2e2; color:#b91c1c; border-radius:999px; padding:4px 12px; font-size:11.5px; font-weight:800; }

.imp-back {
    display:inline-flex; align-items:center; gap:8px;
    background:#f8fafc; color:#475569;
    border:1.5px solid #e2e8f0; border-radius:14px;
    padding:13px 26px; font-size:14px; font-weight:700;
    text-decoration:none; transition:.2s; font-family:Cairo,sans-serif;
}
.imp-back:hover { background:#f1f5f9; color:#1e293b; }

@keyframes spin    { to { transform:rotate(360deg); } }
@keyframes fadeIn  { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:none; } }
</style>
@endpush

@section('content')
<div class="imp-page">

    {{-- Hero --}}
    <div class="imp-hero">
        <p class="imp-hero-label">إدارة البيانات المالية</p>
        <h1 class="imp-hero-title">استيراد الإيرادات والمصروفات</h1>
        <p class="imp-hero-sub">ارفع ملف Excel من تقارير نظامك أو استخدم القالب الجاهز — يتم التعرف على النوع تلقائياً</p>
        <div class="imp-hero-badge">
            <div class="imp-hero-badge-num">2</div>
            <div class="imp-hero-badge-txt">طريقة استيراد</div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="imp-alert imp-alert-success">
        <div class="imp-alert-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></div>
        <div class="imp-alert-body"><p class="imp-alert-title">تم الاستيراد بنجاح</p><p style="margin:0;font-size:13.5px;">{{ session('success') }}</p></div>
    </div>
    @endif

    @if(session('import_warnings'))
    <div class="imp-alert imp-alert-warn">
        <div class="imp-alert-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
        <div class="imp-alert-body">
            <p class="imp-alert-title">تنبيهات — صفوف تم تجاهلها</p>
            <ul class="imp-alert-list">@foreach(session('import_warnings') as $w)<li>{{ $w }}</li>@endforeach</ul>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="imp-alert imp-alert-error">
        <div class="imp-alert-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
        <div class="imp-alert-body">
            <p class="imp-alert-title">حدثت أخطاء أثناء الاستيراد</p>
            <ul class="imp-alert-list">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    </div>
    @endif

    {{-- Primary card: Flexible import --}}
    <div class="imp-card primary">
        <div class="imp-card-header">
            <div class="imp-card-header-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            </div>
            <div style="flex:1;">
                <h2 class="imp-card-header-title">استيراد من تقرير النظام</h2>
                <p class="imp-card-header-sub">ملف Excel بعناوين عربية — يتعرف تلقائياً على الإيرادات والمصروفات من عمود النوع</p>
            </div>
            <span style="background:linear-gradient(90deg,#2563eb,#1d4ed8);color:#fff;border-radius:999px;padding:6px 18px;font-size:12px;font-weight:900;white-space:nowrap;box-shadow:0 2px 10px rgba(37,99,235,.3);">موصى به ⭐</span>
        </div>
        <div class="imp-card-body">
            <div style="display:grid;grid-template-columns:1fr 1.1fr;gap:32px;align-items:start;">
                <div>
                    <form action="{{ route('admin.dashboard.import-flexible.store') }}" method="POST" enctype="multipart/form-data" id="flexForm">
                        @csrf
                        <div class="imp-drop" id="flexZone">
                            <input type="file" name="file" id="flexInput" accept=".xlsx,.xls" required>
                            <div class="imp-drop-icon">
                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="12" y2="12"/><line x1="15" y1="15" x2="12" y2="12"/></svg>
                            </div>
                            <p class="imp-drop-title">اسحب ملف تقرير النظام هنا</p>
                            <p class="imp-drop-sub">xlsx أو xls — بحد أقصى 10 ميجا</p>
                        </div>
                        <div class="imp-file-info" id="flexInfo">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <span class="imp-file-info-name" id="flexFileName">—</span>
                            <button type="button" class="imp-file-info-clear" onclick="clearFlexFile()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                        <div style="margin-top:20px;">
                            <button type="submit" class="imp-btn-primary" id="flexSubmitBtn">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                بدء الاستيراد التلقائي
                            </button>
                        </div>
                    </form>
                </div>
                <div>
                    <p style="font-size:14px;font-weight:900;color:#1e293b;margin:0 0 16px;">ترتيب الأعمدة في ملف النظام:</p>
                    <ul class="imp-cols-grid">
                        <li class="imp-col-item">
                            <div class="imp-col-letter">A</div>
                            <div><p class="imp-col-label">التاريخ</p><p class="imp-col-desc">تاريخ العملية</p><span class="imp-col-required">مطلوب</span></div>
                        </li>
                        <li class="imp-col-item">
                            <div class="imp-col-letter">B</div>
                            <div><p class="imp-col-label">الفرع</p><p class="imp-col-desc">اسم الفرع</p><span class="imp-col-required">مطلوب</span></div>
                        </li>
                        <li class="imp-col-item">
                            <div class="imp-col-letter">C</div>
                            <div><p class="imp-col-label">النوع</p><p class="imp-col-desc">كلمة <strong>إيرادات</strong> أو <strong>مصاريف</strong></p><span class="imp-col-required">مطلوب</span></div>
                        </li>
                        <li class="imp-col-item">
                            <div class="imp-col-letter skip">D</div>
                            <div><p class="imp-col-label" style="color:#94a3b8;">يُتجاهل</p><p class="imp-col-desc">—</p></div>
                        </li>
                        <li class="imp-col-item">
                            <div class="imp-col-letter">E</div>
                            <div><p class="imp-col-label">المبلغ</p><p class="imp-col-desc">رقم</p><span class="imp-col-required">مطلوب</span></div>
                        </li>
                        <li class="imp-col-item">
                            <div class="imp-col-letter skip">F</div>
                            <div><p class="imp-col-label" style="color:#94a3b8;">يُتجاهل</p><p class="imp-col-desc">—</p></div>
                        </li>
                        <li class="imp-col-item">
                            <div class="imp-col-letter">G</div>
                            <div><p class="imp-col-label">المرجع</p><p class="imp-col-desc">رقم المستند</p><span class="imp-col-optional">اختياري</span></div>
                        </li>
                        <li class="imp-col-item">
                            <div class="imp-col-letter skip">H</div>
                            <div><p class="imp-col-label" style="color:#94a3b8;">يُتجاهل</p><p class="imp-col-desc">—</p></div>
                        </li>
                        <li class="imp-col-item">
                            <div class="imp-col-letter">I</div>
                            <div><p class="imp-col-label">طريقة الدفع</p><p class="imp-col-desc">تُكمَّل تلقائياً</p><span class="imp-col-optional">اختياري</span></div>
                        </li>
                    </ul>
                    <div class="imp-note">
                        <strong>ملاحظة:</strong> إذا لم يوجد نوع الإيراد أو المصروف في النظام سيتم إنشاؤه تلقائياً.
                        الصفوف التي لا تحتوي "<strong>إيرادات</strong>" أو "<strong>مصاريف</strong>" في عمود النوع تُتجاهل مع تقرير.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary: Template-based import --}}
    <details class="imp-details">
        <summary class="imp-details-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <span style="flex:1;">استيراد من القالب — ملف بشيتين (إيرادات + مصروفات) بعناوين إنجليزية</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </summary>
        <div class="imp-details-body">
            <div class="imp-tpl-grid">
                <div>
                    <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:18px;padding:24px;">
                        <h3 style="font-size:16px;font-weight:900;color:#1e293b;margin:0 0 18px;display:flex;align-items:center;gap:10px;">
                            <span style="width:36px;height:36px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </span>
                            رفع ملف القالب
                        </h3>
                        <form action="{{ route('admin.dashboard.import-statement.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
                            @csrf
                            <div class="imp-drop" id="dropZone" style="padding:36px 20px;">
                                <input type="file" name="file" id="fileInput" accept=".xlsx,.xls" required>
                                <div class="imp-drop-icon" style="width:58px;height:58px;border-radius:18px;">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="12" y2="12"/><line x1="15" y1="15" x2="12" y2="12"/></svg>
                                </div>
                                <p class="imp-drop-title" style="font-size:15px;">اسحب الملف هنا أو انقر للاختيار</p>
                                <p class="imp-drop-sub">xlsx أو xls فقط — بحد أقصى 10 ميجا</p>
                            </div>
                            <div class="imp-file-info" id="fileInfo">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                <span class="imp-file-info-name" id="fileName">—</span>
                                <button type="button" class="imp-file-info-clear" onclick="clearFile()">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                            <div style="display:flex;gap:12px;margin-top:18px;flex-wrap:wrap;">
                                <button type="submit" class="imp-btn-primary" id="submitBtn" style="flex:1;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    بدء الاستيراد
                                </button>
                                <a href="{{ route('admin.dashboard.import-statement.template') }}" class="imp-btn-green">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    تحميل القالب
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;gap:18px;">
                    <div style="background:#fff;border:1.5px solid #dcfce7;border-radius:16px;overflow:hidden;">
                        <div style="background:linear-gradient(90deg,#f0fdf4,#fff);border-bottom:1px solid #dcfce7;padding:14px 20px;display:flex;align-items:center;gap:10px;">
                            <span style="width:32px;height:32px;background:#dcfce7;border-radius:10px;display:flex;align-items:center;justify-content:center;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/></svg></span>
                            <span class="imp-badge-green">شيت إيرادات</span>
                        </div>
                        <table class="imp-tbl">
                            <thead><tr><th>اسم العمود</th><th>الوصف</th><th>إلزامي؟</th></tr></thead>
                            <tbody>
                                <tr><td><code>branch_code</code></td><td>رمز الفرع</td><td><span class="imp-col-required">مطلوب</span></td></tr>
                                <tr><td><code>income_type_name</code></td><td>اسم نوع الإيراد</td><td><span class="imp-col-required">مطلوب</span></td></tr>
                                <tr><td><code>amount</code></td><td>المبلغ (رقم)</td><td><span class="imp-col-required">مطلوب</span></td></tr>
                                <tr><td><code>date</code></td><td>التاريخ (YYYY-MM-DD)</td><td><span class="imp-col-required">مطلوب</span></td></tr>
                                <tr><td><code>payment_method</code></td><td>cash / bank_transfer / card / other</td><td><span class="imp-col-required">مطلوب</span></td></tr>
                                <tr><td><code>reference_number</code></td><td>رقم المرجع</td><td><span class="imp-col-optional">اختياري</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="background:#fff;border:1.5px solid #fecdd3;border-radius:16px;overflow:hidden;">
                        <div style="background:linear-gradient(90deg,#fff1f2,#fff);border-bottom:1px solid #fecdd3;padding:14px 20px;display:flex;align-items:center;gap:10px;">
                            <span style="width:32px;height:32px;background:#fee2e2;border-radius:10px;display:flex;align-items:center;justify-content:center;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#b91c1c" stroke-width="2"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"/></svg></span>
                            <span class="imp-badge-red">شيت مصروفات</span>
                        </div>
                        <table class="imp-tbl">
                            <thead><tr><th>اسم العمود</th><th>الوصف</th><th>إلزامي؟</th></tr></thead>
                            <tbody>
                                <tr><td><code>branch_code</code></td><td>رمز الفرع</td><td><span class="imp-col-required">مطلوب</span></td></tr>
                                <tr><td><code>expense_type_name</code></td><td>اسم نوع المصروف</td><td><span class="imp-col-required">مطلوب</span></td></tr>
                                <tr><td><code>amount</code></td><td>المبلغ (رقم)</td><td><span class="imp-col-required">مطلوب</span></td></tr>
                                <tr><td><code>date</code></td><td>التاريخ (YYYY-MM-DD)</td><td><span class="imp-col-required">مطلوب</span></td></tr>
                                <tr><td><code>payment_method</code></td><td>cash / bank_transfer / card / other</td><td><span class="imp-col-required">مطلوب</span></td></tr>
                                <tr><td><code>reference_number</code></td><td>رقم المرجع</td><td><span class="imp-col-optional">اختياري</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </details>

    <a href="{{ route('admin.dashboard') }}" class="imp-back">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        العودة للوحة التحكم
    </a>

</div>
@endsection

@push('scripts')
<script>
const flexInput = document.getElementById('flexInput');
const flexZone  = document.getElementById('flexZone');
const flexInfo  = document.getElementById('flexInfo');
if (flexInput) {
    flexInput.addEventListener('change', () => showFlexFile(flexInput.files[0]));
    ['dragover','dragenter'].forEach(e => flexZone.addEventListener(e, ev => { ev.preventDefault(); flexZone.classList.add('dragover'); }));
    ['dragleave','drop'].forEach(e  => flexZone.addEventListener(e, ev => { ev.preventDefault(); flexZone.classList.remove('dragover'); }));
    flexZone.addEventListener('drop', ev => { const f = ev.dataTransfer.files[0]; if (f) { flexInput.files = ev.dataTransfer.files; showFlexFile(f); } });
}
function showFlexFile(f) {
    if (!f) return;
    document.getElementById('flexFileName').textContent = f.name + ' (' + (f.size/1024).toFixed(1) + ' KB)';
    flexInfo.classList.add('show');
}
function clearFlexFile() {
    flexInput.value = '';
    flexInfo.classList.remove('show');
    document.getElementById('flexFileName').textContent = '—';
}
document.getElementById('flexForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('flexSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 1s linear infinite"><polyline points="20 6 9 17 4 12"/></svg> جاري الاستيراد...';
});

const fileInput = document.getElementById('fileInput');
const dropZone  = document.getElementById('dropZone');
const fileInfo  = document.getElementById('fileInfo');
if (fileInput) {
    fileInput.addEventListener('change', () => showFile(fileInput.files[0]));
    ['dragover','dragenter'].forEach(e => dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.add('dragover'); }));
    ['dragleave','drop'].forEach(e  => dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.remove('dragover'); }));
    dropZone.addEventListener('drop', ev => { const f = ev.dataTransfer.files[0]; if (f) { fileInput.files = ev.dataTransfer.files; showFile(f); } });
}
function showFile(f) {
    if (!f) return;
    document.getElementById('fileName').textContent = f.name + ' (' + (f.size/1024).toFixed(1) + ' KB)';
    fileInfo.classList.add('show');
}
function clearFile() {
    fileInput.value = '';
    fileInfo.classList.remove('show');
    document.getElementById('fileName').textContent = '—';
}
document.getElementById('importForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 1s linear infinite"><polyline points="20 6 9 17 4 12"/></svg> جاري الاستيراد...';
});
</script>
@endpush