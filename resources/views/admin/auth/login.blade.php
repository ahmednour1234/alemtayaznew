<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام شركة الامتياز للاستقدام</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
        *, *::before, *::after { font-family: 'Cairo', sans-serif; box-sizing: border-box; }

        /* ── Left hero panel ── */
        .hero-panel {
            background: linear-gradient(145deg, #c8dcef 0%, #dce8f5 30%, #eaf2fb 60%, #f4f8fd 100%);
            position: relative;
            overflow: hidden;
        }
        /* Light rays */
        .hero-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(
                -48deg,
                transparent 0px,
                transparent 55px,
                rgba(255,255,255,.45) 55px,
                rgba(255,255,255,.45) 100px
            );
            pointer-events: none;
        }
        /* Bottom soft glow */
        .hero-panel::after {
            content: '';
            position: absolute;
            bottom: -60px;
            left: -60px;
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(37,99,235,.08), transparent 65%);
            pointer-events: none;
        }

        /* ── Right form area ── */
        .form-side {
            background: #f8faff;
        }

        /* ── Login card ── */
        .login-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 24px;
            box-shadow: 0 12px 60px rgba(37,99,235,.09), 0 3px 12px rgba(0,0,0,.05);
            padding: 48px 48px 36px;
            width: 100%;
            max-width: 480px;
        }

        /* ── Field ── */
        .field { position: relative; }
        .field input {
            width: 100%;
            background: #f7f9ff;
            border: 1.5px solid #e4e9f2;
            border-radius: 10px;
            padding: 11px 42px 11px 14px;
            font-size: 13.5px;
            font-family: 'Cairo', sans-serif;
            color: #1e293b;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            direction: rtl;
        }
        .field input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.10);
            background: #fff;
        }
        .field input::placeholder { color: #b8c4d4; }
        .f-icon {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #c0ccd8;
            pointer-events: none;
        }
        .f-eye {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #c0ccd8;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            line-height: 0;
        }
        .f-eye:hover { color: #64748b; }
        .field input.with-eye { padding-left: 38px; }

        /* ── Buttons ── */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 60%, #3b82f6 100%);
            color: #fff;
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            font-size: 15px;
            padding: 12px 16px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(37,99,235,.35);
            transition: opacity .18s, transform .12s, box-shadow .18s;
        }
        .btn-login:hover { opacity: .92; transform: translateY(-1px); box-shadow: 0 6px 22px rgba(37,99,235,.42); }
        .btn-login:active { transform: translateY(0); }

        .btn-support {
            width: 100%;
            background: #fff;
            color: #374151;
            font-family: 'Cairo', sans-serif;
            font-weight: 600;
            font-size: 14px;
            padding: 11px 16px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: background .2s, border-color .2s;
        }
        .btn-support:hover { background: #f0fdf4; border-color: #86efac; }

        /* ── Animations ── */
        @keyframes floatY   { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-9px)} }
        @keyframes floatY2  { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        .fl1 { animation: floatY  4.2s ease-in-out infinite; }
        .fl2 { animation: floatY2 5s   ease-in-out infinite .7s; }
    </style>
</head>
<body style="margin:0;background:#f0f4f8;min-height:100vh;display:flex;align-items:stretch">

<div style="display:flex;width:100%;min-height:100vh">

    {{-- ══════════ LEFT — HERO ══════════ --}}
    <div class="hero-panel hidden lg:flex lg:w-[44%] flex-col justify-between py-10 px-12">

        <!-- Logo + Title -->
        <div class="relative z-10">
            <!-- Building logo SVG -->
            <div class="mb-8">
                <svg width="68" height="76" viewBox="0 0 68 76" fill="none">
                    <!-- Main building body -->
                    <rect x="12" y="28" width="44" height="44" rx="3" fill="url(#bgrad)"/>
                    <!-- Tower left -->
                    <rect x="8"  y="16" width="18" height="56" rx="3" fill="url(#bgrad2)"/>
                    <!-- Tower right -->
                    <rect x="42" y="22" width="18" height="50" rx="3" fill="url(#bgrad3)"/>
                    <!-- Windows left tower -->
                    <rect x="12" y="22" width="5" height="5" rx="1" fill="white" opacity=".7"/>
                    <rect x="20" y="22" width="5" height="5" rx="1" fill="white" opacity=".7"/>
                    <rect x="12" y="31" width="5" height="5" rx="1" fill="white" opacity=".5"/>
                    <rect x="20" y="31" width="5" height="5" rx="1" fill="white" opacity=".5"/>
                    <!-- Windows right tower -->
                    <rect x="45" y="28" width="5" height="5" rx="1" fill="white" opacity=".7"/>
                    <rect x="53" y="28" width="5" height="5" rx="1" fill="white" opacity=".7"/>
                    <rect x="45" y="37" width="5" height="5" rx="1" fill="white" opacity=".5"/>
                    <rect x="53" y="37" width="5" height="5" rx="1" fill="white" opacity=".5"/>
                    <!-- Door -->
                    <rect x="27" y="56" width="14" height="16" rx="2" fill="white" opacity=".6"/>
                    <!-- Curved top arch on door -->
                    <path d="M27 63 Q34 56 41 63" fill="white" opacity=".3"/>
                    <!-- Arabic ن shape at top -->
                    <path d="M22 12 Q34 2 46 12 Q40 8 34 8 Q28 8 22 12Z" fill="#1d4ed8" opacity=".9"/>
                    <defs>
                        <linearGradient id="bgrad" x1="12" y1="28" x2="56" y2="72" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#2563eb"/>
                            <stop offset="1" stop-color="#1e40af"/>
                        </linearGradient>
                        <linearGradient id="bgrad2" x1="8" y1="16" x2="26" y2="72" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#3b82f6"/>
                            <stop offset="1" stop-color="#1d4ed8"/>
                        </linearGradient>
                        <linearGradient id="bgrad3" x1="42" y1="22" x2="60" y2="72" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#60a5fa"/>
                            <stop offset="1" stop-color="#2563eb"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            <h1 style="font-size:34px;font-weight:900;color:#1e293b;line-height:1.25;margin:0 0 10px">
                شركة <span style="color:#2563eb">الامتياز</span> للاستقدام
            </h1>
            <p style="color:#64748b;font-size:15px;font-weight:400;margin:0">
                حلول متكاملة لاستقدام العمالة المنزلية
            </p>
        </div>

        <!-- Desk illustration -->
        <div class="relative z-10 flex items-end justify-center flex-1 pt-8 pb-2">
            <div class="relative">
                <!-- Laptop -->
                <div class="fl1" style="display:inline-block">
                    <svg width="220" height="150" viewBox="0 0 220 150" fill="none">
                        <!-- Screen back -->
                        <rect x="30" y="10" width="160" height="105" rx="8" fill="#d1d9e6"/>
                        <!-- Screen bezel -->
                        <rect x="34" y="14" width="152" height="97" rx="6" fill="#e8edf5"/>
                        <!-- Screen content (light gradient) -->
                        <rect x="38" y="18" width="144" height="89" rx="4"
                              fill="url(#scrgrad)"/>
                        <!-- Window decorations on screen -->
                        <rect x="42" y="22" width="60" height="4" rx="2" fill="white" opacity=".7"/>
                        <rect x="42" y="30" width="136" height="3" rx="1.5" fill="white" opacity=".4"/>
                        <rect x="42" y="37" width="100" height="3" rx="1.5" fill="white" opacity=".4"/>
                        <rect x="42" y="44" width="120" height="3" rx="1.5" fill="white" opacity=".3"/>
                        <rect x="42" y="54" width="30" height="20" rx="4" fill="#3b82f6" opacity=".5"/>
                        <rect x="76" y="54" width="30" height="20" rx="4" fill="#10b981" opacity=".4"/>
                        <rect x="110" y="54" width="30" height="20" rx="4" fill="#f59e0b" opacity=".4"/>
                        <!-- Hinge -->
                        <rect x="28" y="112" width="164" height="6" rx="3" fill="#c0c8d6"/>
                        <!-- Base -->
                        <rect x="14" y="116" width="192" height="10" rx="4" fill="#d1d9e6"/>
                        <!-- Keyboard row illusion -->
                        <rect x="22" y="118" width="176" height="2" rx="1" fill="#b8c4d0" opacity=".5"/>
                        <!-- Trackpad -->
                        <rect x="80" y="121" width="60" height="2" rx="1" fill="#b8c4d0" opacity=".5"/>
                        <!-- Bottom stand -->
                        <ellipse cx="110" cy="128" rx="90" ry="8" fill="#c8d2e0" opacity=".4"/>
                        <defs>
                            <linearGradient id="scrgrad" x1="38" y1="18" x2="182" y2="107" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#dbeafe"/>
                                <stop offset=".5" stop-color="#eff6ff"/>
                                <stop offset="1" stop-color="#f0f9ff"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>

                <!-- Plant pot -->
                <div class="fl2" style="display:inline-block;position:absolute;bottom:14px;right:-60px">
                    <svg width="90" height="120" viewBox="0 0 90 120" fill="none">
                        <!-- Stem -->
                        <line x1="45" y1="75" x2="45" y2="30" stroke="#6b8f5e" stroke-width="2.5"/>
                        <!-- Left leaf -->
                        <ellipse cx="28" cy="48" rx="18" ry="10" fill="#4ade80" transform="rotate(-30 28 48)" opacity=".9"/>
                        <ellipse cx="24" cy="44" rx="14" ry="8"  fill="#22c55e" transform="rotate(-30 24 44)" opacity=".8"/>
                        <!-- Right leaf -->
                        <ellipse cx="62" cy="42" rx="18" ry="10" fill="#4ade80" transform="rotate(30 62 42)" opacity=".9"/>
                        <ellipse cx="66" cy="38" rx="14" ry="8"  fill="#16a34a" transform="rotate(30 66 38)" opacity=".8"/>
                        <!-- Center top leaves -->
                        <ellipse cx="45" cy="28" rx="12" ry="18" fill="#86efac" opacity=".8"/>
                        <ellipse cx="45" cy="24" rx="9"  rx="9" ry="14" fill="#4ade80" opacity=".9"/>
                        <!-- Pot body -->
                        <path d="M24 75 Q22 95 30 105 Q45 112 60 105 Q68 95 66 75Z" fill="#e2e8f0"/>
                        <path d="M24 75 Q22 88 30 98 Q45 104 60 98 Q68 88 66 75Z" fill="#cbd5e1"/>
                        <!-- Pot rim -->
                        <ellipse cx="45" cy="75" rx="22" ry="6" fill="#d1d9e6"/>
                        <!-- Soil -->
                        <ellipse cx="45" cy="75" rx="19" ry="4" fill="#92400e" opacity=".4"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════ RIGHT — FORM ══════════ --}}
    <div class="form-side flex-1 lg:w-[56%] flex flex-col min-h-screen">

        <!-- Centered login card -->
        <div class="flex-1 flex items-center justify-center px-6 py-4">
            <div class="login-card">

                <!-- Logo in card -->
                <div style="display:flex;flex-direction:column;align-items:center;margin-bottom:24px">
                    <div style="width:60px;height:60px;background:linear-gradient(145deg,#eff6ff,#dbeafe);border:1.5px solid #bfdbfe;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;box-shadow:0 2px 12px rgba(59,130,246,.12)">
                        <svg width="30" height="34" viewBox="0 0 68 76" fill="none">
                            <rect x="12" y="28" width="44" height="44" rx="3" fill="url(#bgrad_c)"/>
                            <rect x="8"  y="16" width="18" height="56" rx="3" fill="url(#bgrad2_c)"/>
                            <rect x="42" y="22" width="18" height="50" rx="3" fill="url(#bgrad3_c)"/>
                            <rect x="27" y="56" width="14" height="16" rx="2" fill="white" opacity=".7"/>
                            <defs>
                                <linearGradient id="bgrad_c"  x1="12" y1="28" x2="56" y2="72" gradientUnits="userSpaceOnUse"><stop stop-color="#2563eb"/><stop offset="1" stop-color="#1e40af"/></linearGradient>
                                <linearGradient id="bgrad2_c" x1="8"  y1="16" x2="26" y2="72" gradientUnits="userSpaceOnUse"><stop stop-color="#3b82f6"/><stop offset="1" stop-color="#1d4ed8"/></linearGradient>
                                <linearGradient id="bgrad3_c" x1="42" y1="22" x2="60" y2="72" gradientUnits="userSpaceOnUse"><stop stop-color="#60a5fa"/><stop offset="1" stop-color="#2563eb"/></linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <h2 style="font-size:24px;font-weight:900;color:#0f172a;margin:0 0 6px">مرحباً بك</h2>
                    <p style="font-size:13px;color:#94a3b8;margin:0;text-align:center;line-height:1.6">
                        قم بتسجيل الدخول للوصول إلى لوحة التحكم
                    </p>
                </div>

                @if(session('error'))
                <div style="margin-bottom:16px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 14px;border-radius:10px;font-size:13px;display:flex;align-items:center;gap:8px">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('admin.login.post') }}" method="POST">
                    @csrf

                    <!-- Email -->
                    <div style="margin-bottom:16px">
                        <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:7px">اسم المستخدم</label>
                        <div class="field">
                            <svg class="f-icon" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   placeholder="أدخل اسم المستخدم" autocomplete="email">
                        </div>
                        @error('email')
                        <p style="color:#ef4444;font-size:12px;margin:5px 0 0;display:flex;align-items:center;gap:4px">
                            <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div style="margin-bottom:16px">
                        <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:7px">كلمة المرور</label>
                        <div class="field" x-data="{ show: false }">
                            <svg class="f-icon" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                            <input :type="show ? 'text' : 'password'" name="password" required
                                   placeholder="أدخل كلمة المرور"
                                   autocomplete="current-password"
                                   class="with-eye">
                            <button type="button" class="f-eye" @click="show = !show">
                                <svg x-show="!show" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg x-show="show" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember + Forgot -->
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
                        <label style="display:flex;align-items:center;gap:7px;cursor:pointer;font-size:13px;color:#4b5563;user-select:none">
                            <input type="checkbox" name="remember" id="remember"
                                   style="width:15px;height:15px;border-radius:4px;accent-color:#2563eb;cursor:pointer">
                            تذكرني
                        </label>
                        <a href="#" style="font-size:13px;color:#2563eb;font-weight:600;text-decoration:none"
                           onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#2563eb'">
                            نسيت كلمة المرور؟
                        </a>
                    </div>

                    <button type="submit" class="btn-login">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                        تسجيل الدخول
                    </button>
                </form>

                <!-- Divider -->
                <div style="display:flex;align-items:center;gap:12px;margin:18px 0">
                    <div style="flex:1;height:1px;background:#f0f0f5"></div>
                    <span style="color:#cbd5e1;font-size:12px;font-weight:600">أو</span>
                    <div style="flex:1;height:1px;background:#f0f0f5"></div>
                </div>

                <!-- WhatsApp support -->
                <a href="https://wa.me/966575899784" target="_blank" rel="noopener noreferrer" class="btn-support">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#25d366">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    تواصل مع الدعم
                </a>

            </div>
        </div>

        <!-- Footer -->
        <div style="padding:16px 32px;text-align:center">
            <p style="font-size:11.5px;color:#94a3b8;margin:0">
                جميع الحقوق محفوظة &copy; {{ date('Y') }} شركة الامتياز للاستقدام
            </p>
        </div>
    </div>

</div>
</body>
</html>

