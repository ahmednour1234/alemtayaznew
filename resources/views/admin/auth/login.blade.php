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

        /* ── Hero panel (appears on RIGHT in RTL) ── */
        .hero-panel {
            background: #faf7f0;
            position: relative;
            overflow: hidden;
        }

        /* Subtle golden dot grid – upper-left corner */
        .hero-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 240px;
            height: 240px;
            background-image: radial-gradient(circle, #c9a84c2e 1.6px, transparent 1.6px);
            background-size: 20px 20px;
            pointer-events: none;
        }

        /* ── Form side (appears on LEFT in RTL) ── */
        .form-side {
            background: #f5f3ee;
        }

        /* ── Login card ── */
        .login-card {
            background: #ffffff;
            border: 1px solid #ede8dd;
            border-radius: 22px;
            box-shadow: 0 10px 50px rgba(0,0,0,.07), 0 2px 10px rgba(0,0,0,.04);
            padding: 44px 48px 36px;
            width: 100%;
            max-width: 460px;
        }

        /* ── Input fields ── */
        .field { position: relative; }
        .field input {
            width: 100%;
            background: #f8f6f1;
            border: 1.5px solid #e5dfd4;
            border-radius: 10px;
            padding: 12px 44px 12px 14px;
            font-size: 13.5px;
            font-family: 'Cairo', sans-serif;
            color: #1e293b;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            direction: rtl;
        }
        .field input:focus {
            border-color: #c9a84c;
            box-shadow: 0 0 0 3px rgba(201,168,76,.13);
            background: #fff;
        }
        .field input::placeholder { color: #bfb89e; }
        .f-icon {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #c9a84c;
            pointer-events: none;
        }
        .f-eye {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #b0bac4;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            line-height: 0;
        }
        .f-eye:hover { color: #64748b; }
        .field input.with-eye { padding-left: 38px; }

        /* ── Login button ── */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #1a2744 0%, #122240 60%, #0d1c38 100%);
            color: #fff;
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            font-size: 15.5px;
            padding: 13px 16px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 18px rgba(18,34,64,.38);
            transition: opacity .18s, transform .12s, box-shadow .18s;
            letter-spacing: .3px;
        }
        .btn-login:hover { opacity: .91; transform: translateY(-1px); box-shadow: 0 6px 24px rgba(18,34,64,.46); }
        .btn-login:active { transform: translateY(0); }

        /* ── WhatsApp support button ── */
        .btn-support {
            width: 100%;
            background: #fff;
            color: #374151;
            font-family: 'Cairo', sans-serif;
            font-weight: 600;
            font-size: 14px;
            padding: 12px 16px;
            border-radius: 10px;
            border: 1.5px solid #e2ddd4;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: background .2s, border-color .2s;
        }
        .btn-support:hover { background: #f0fdf4; border-color: #86efac; }

        /* ── Feature items ── */
        .feat-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 6px;
        }
        .feat-icon-wrap {
            width: 52px;
            height: 52px;
            background: rgba(201,168,76,.10);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 4px;
            flex-shrink: 0;
        }

        /* ── Float animation ── */
        @keyframes floatY { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        .fl1 { animation: floatY 4.2s ease-in-out infinite; }
    </style>
</head>
<body style="margin:0;min-height:100vh;display:flex;align-items:stretch">

<div style="display:flex;width:100%;min-height:100vh">

    {{-- ══════════ HERO — appears on RIGHT in RTL ══════════ --}}
    <div class="hero-panel hidden lg:flex lg:w-[57%] flex-col py-10 px-14 gap-6">

        {{-- Logo --}}
        <div class="relative z-10">
            <img src="{{ asset('1759760768-33.png') }}" alt="شركة الامتياز للاستقدام">
        </div>

        {{-- Heading + description + features + illustration --}}
        <div class="relative z-10 flex-1 flex flex-col justify-center">

            <h1 style="font-size:28px;font-weight:900;color:#0f172a;margin:0 0 14px;line-height:1.45">
                حلول متكاملة لاستقدام العمالة المنزلية
            </h1>
            <p style="font-size:14px;color:#6b7280;margin:0 0 34px;line-height:1.85;max-width:440px">
                نوفر لك أفضل الخدمات لاستقدام العمالة المنزلية<br>
                بسهولة وأمان وبأفضل الأسعار
            </p>

            {{-- 3 Feature columns --}}
            <div style="display:flex;gap:20px;margin-bottom:44px">

                {{-- موثوق وأمن --}}
                <div class="feat-item">
                    <div class="feat-icon-wrap">
                        <svg width="24" height="24" fill="none" stroke="#c9a84c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <span style="font-size:13px;font-weight:800;color:#1a2744">موثوق وأمن</span>
                    <span style="font-size:11.5px;color:#9ca3af;line-height:1.5">صمان كامل على جميع<br>الخدمات</span>
                </div>

                {{-- عمالة مدربة --}}
                <div class="feat-item">
                    <div class="feat-icon-wrap">
                        <svg width="24" height="24" fill="none" stroke="#c9a84c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                            <path d="M16 3.13a4 4 0 010 7.75"/>
                        </svg>
                    </div>
                    <span style="font-size:13px;font-weight:800;color:#1a2744">عمالة مدربة</span>
                    <span style="font-size:11.5px;color:#9ca3af;line-height:1.5">عمالة منزلية مدربة<br>ومؤهلة</span>
                </div>

                {{-- سرعة في الإنجاز --}}
                <div class="feat-item">
                    <div class="feat-icon-wrap">
                        <svg width="24" height="24" fill="none" stroke="#c9a84c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <span style="font-size:13px;font-weight:800;color:#1a2744">سرعة في الإنجاز</span>
                    <span style="font-size:11.5px;color:#9ca3af;line-height:1.5">إجراءات سريعة<br>وبسيطة</span>
                </div>

            </div>

            {{-- Laptop + furniture illustration --}}
            <div class="fl1" style="position:relative;height:210px">

                {{-- Laptop SVG (dark navy screen with app UI) --}}
                <div style="position:absolute;bottom:0;right:50px">
                    <svg width="290" height="200" viewBox="0 0 290 200" fill="none">
                        <rect x="30" y="0" width="230" height="150" rx="10" fill="#dde3ed"/>
                        <rect x="34" y="4" width="222" height="142" rx="8" fill="#1a2744"/>
                        <rect x="38" y="8" width="214" height="134" rx="6" fill="#0f1c38"/>
                        <rect x="38" y="8" width="214" height="30" rx="6" fill="#1e3a6e" opacity="0.85"/>
                        <text x="145" y="28" text-anchor="middle" font-size="9" fill="#c9a84c" font-family="Cairo, sans-serif" font-weight="700">شركة الامتياز للاستقدام</text>
                        <line x1="38" y1="38" x2="252" y2="38" stroke="#253d70" stroke-width="0.8"/>
                        <rect x="48" y="46" width="90" height="8" rx="4" fill="#253d70" opacity="0.7"/>
                        <rect x="48" y="60" width="195" height="5" rx="2.5" fill="#253d70" opacity="0.4"/>
                        <rect x="48" y="70" width="160" height="5" rx="2.5" fill="#253d70" opacity="0.35"/>
                        <rect x="48" y="82" width="45" height="26" rx="5" fill="#c9a84c" opacity="0.25"/>
                        <rect x="100" y="82" width="45" height="26" rx="5" fill="#3b82f6" opacity="0.18"/>
                        <rect x="152" y="82" width="45" height="26" rx="5" fill="#10b981" opacity="0.18"/>
                        <rect x="68" y="116" width="30" height="24" rx="6" fill="#253d70"/>
                        <rect x="108" y="116" width="30" height="24" rx="6" fill="#253d70"/>
                        <rect x="148" y="116" width="30" height="24" rx="6" fill="#253d70"/>
                        <rect x="188" y="116" width="30" height="24" rx="6" fill="#253d70"/>
                        <circle cx="83" cy="123" r="4" fill="#c9a84c" opacity="0.9"/>
                        <path d="M77 131 Q83 127 89 131" stroke="#c9a84c" stroke-width="1.5" stroke-linecap="round" fill="none" opacity="0.9"/>
                        <circle cx="120" cy="122" r="3.5" fill="#94a3b8" opacity="0.75"/>
                        <circle cx="128" cy="121" r="3" fill="#94a3b8" opacity="0.6"/>
                        <path d="M115 130 Q120 127 125 130" stroke="#94a3b8" stroke-width="1.4" stroke-linecap="round" fill="none" opacity="0.75"/>
                        <rect x="157" y="118" width="12" height="16" rx="2" fill="#94a3b8" opacity="0.6"/>
                        <line x1="159" y1="122" x2="167" y2="122" stroke="#0f1c38" stroke-width="1" opacity="0.5"/>
                        <line x1="159" y1="125" x2="167" y2="125" stroke="#0f1c38" stroke-width="1" opacity="0.5"/>
                        <path d="M200 118 L210 118 L210 125 Q205 130 200 125 Z" fill="#94a3b8" opacity="0.6"/>
                        <rect x="28" y="148" width="234" height="8" rx="4" fill="#c8d0de"/>
                        <rect x="10" y="154" width="270" height="15" rx="5" fill="#dde3ed"/>
                        <line x1="20" y1="158" x2="270" y2="158" stroke="#c8d0de" stroke-width="1" opacity="0.6"/>
                        <rect x="110" y="161" width="70" height="4" rx="2" fill="#c8d0de" opacity="0.6"/>
                        <ellipse cx="145" cy="173" rx="120" ry="9" fill="#c8d0de" opacity="0.3"/>
                    </svg>
                </div>

                {{-- Armchair SVG --}}
                <div style="position:absolute;bottom:0;left:0">
                    <svg width="135" height="165" viewBox="0 0 135 165" fill="none">
                        <rect x="20" y="28" width="95" height="72" rx="13" fill="url(#cg)" />
                        <rect x="8" y="72" width="22" height="46" rx="9" fill="#d4c9aa"/>
                        <rect x="105" y="72" width="22" height="46" rx="9" fill="#d4c9aa"/>
                        <rect x="18" y="97" width="99" height="40" rx="11" fill="#e2d7bc"/>
                        <ellipse cx="67" cy="104" rx="36" ry="8" fill="#ece3cc" opacity="0.5"/>
                        <rect x="22" y="133" width="11" height="24" rx="4" fill="#c9b887"/>
                        <rect x="102" y="133" width="11" height="24" rx="4" fill="#c9b887"/>
                        <path d="M30 115 Q67 111 105 115" stroke="#c9b887" stroke-width="1" stroke-dasharray="3 3" fill="none" opacity="0.6"/>
                        <defs>
                            <linearGradient id="cg" x1="20" y1="28" x2="115" y2="100" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#f0e8d0"/>
                                <stop offset="1" stop-color="#d4c9aa"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>

                {{-- Plant SVG --}}
                <div style="position:absolute;bottom:0;right:0">
                    <svg width="72" height="135" viewBox="0 0 72 135" fill="none">
                        <line x1="36" y1="95" x2="36" y2="48" stroke="#5a7a4a" stroke-width="3"/>
                        <ellipse cx="18" cy="63" rx="17" ry="9" fill="#5cb85c" transform="rotate(-25 18 63)" opacity=".9"/>
                        <ellipse cx="16" cy="60" rx="13" ry="7" fill="#3e9a3e" transform="rotate(-25 16 60)" opacity=".8"/>
                        <ellipse cx="54" cy="58" rx="17" ry="9" fill="#5cb85c" transform="rotate(25 54 58)" opacity=".9"/>
                        <ellipse cx="56" cy="55" rx="13" ry="7" fill="#2e8b2e" transform="rotate(25 56 55)" opacity=".8"/>
                        <ellipse cx="36" cy="46" rx="11" ry="17" fill="#7dcf7d" opacity=".85"/>
                        <ellipse cx="36" cy="42" rx="8" ry="13" fill="#5cb85c" opacity=".9"/>
                        <path d="M18 95 Q16 113 25 123 Q36 129 47 123 Q56 113 54 95Z" fill="#d8cfc0"/>
                        <path d="M18 95 Q16 110 25 118 Q36 123 47 118 Q56 110 54 95Z" fill="#c8bfaf"/>
                        <ellipse cx="36" cy="95" rx="19" ry="5.5" fill="#cec5b5"/>
                        <ellipse cx="36" cy="95" rx="16" ry="3.5" fill="#6b4c2a" opacity=".35"/>
                    </svg>
                </div>

            </div>
        </div>

    </div>

    {{-- ══════════ FORM — appears on LEFT in RTL ══════════ --}}
    <div class="form-side flex-1 lg:w-[43%] flex flex-col min-h-screen">

        {{-- Centered login card --}}
        <div class="flex-1 flex items-center justify-center px-6 py-6">
            <div class="login-card">

                {{-- Logo in card --}}
                <div style="display:flex;flex-direction:column;align-items:center;margin-bottom:26px">
                    <div style="margin-bottom:16px">
                        <img src="{{ asset('1759760768-33.png') }}" alt="شركة الامتياز للاستقدام">
                    </div>
                    <h2 style="font-size:26px;font-weight:900;color:#0f172a;margin:0 0 7px">مرحباً بك</h2>
                    <p style="font-size:13px;color:#9ca3af;margin:0;text-align:center;line-height:1.7">
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

                    {{-- اسم المستخدم --}}
                    <div style="margin-bottom:17px">
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

                    {{-- كلمة المرور --}}
                    <div style="margin-bottom:17px">
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
                        @error('password')
                        <p style="color:#ef4444;font-size:12px;margin:5px 0 0;display:flex;align-items:center;gap:4px">
                            <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- تذكرني + نسيت كلمة المرور --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px">
                        <label style="display:flex;align-items:center;gap:7px;cursor:pointer;font-size:13px;color:#4b5563;user-select:none">
                            <input type="checkbox" name="remember" id="remember"
                                   style="width:15px;height:15px;border-radius:4px;accent-color:#c9a84c;cursor:pointer">
                            تذكرني
                        </label>
                        <a href="#" style="font-size:13px;color:#c9a84c;font-weight:600;text-decoration:none"
                           onmouseover="this.style.color='#a88830'" onmouseout="this.style.color='#c9a84c'">
                            نسيت كلمة المرور؟
                        </a>
                    </div>

                    {{-- زر تسجيل الدخول --}}
                    <button type="submit" class="btn-login">
                        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                        تسجيل الدخول
                    </button>
                </form>

                {{-- فاصل --}}
                <div style="display:flex;align-items:center;gap:12px;margin:20px 0">
                    <div style="flex:1;height:1px;background:#ede8dd"></div>
                    <span style="color:#d1cbbf;font-size:12px;font-weight:700">أو</span>
                    <div style="flex:1;height:1px;background:#ede8dd"></div>
                </div>

                {{-- زر التواصل عبر واتساب --}}
                <a href="https://wa.me/966575899784" target="_blank" rel="noopener noreferrer" class="btn-support">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#25d366">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    تواصل مع الدعم
                </a>

            </div>
        </div>

        {{-- تذييل الصفحة --}}
        <div style="padding:14px 32px;text-align:center">
            <p style="font-size:11.5px;color:#a89b84;margin:0">
                جميع الحقوق محفوظة &copy; {{ date('Y') }} <a href="#" style="color:#c9a84c;text-decoration:none;font-weight:600">شركة الامتياز للاستقدام</a>
            </p>
        </div>

    </div>

</div>
</body>
</html>
