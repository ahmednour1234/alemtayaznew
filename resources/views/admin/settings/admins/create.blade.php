@extends('admin.layouts.app')
@section('title', 'إضا�ة مدير')
@section('content')
<div class="w-full">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.settings.admins.index') }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-300 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800">إضا�ة مدير جديد</h2>
                <p class="text-slate-400 text-xs mt-0.5">أدخل بيانات المدير الجديد</p>
            </div>
        </div>
        <a href="{{ route('admin.settings.admins.index') }}"
           class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
            إلغاء
        </a>
    </div>

    <form action="{{ route('admin.settings.admins.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Main fields --}}
            <div class="xl:col-span-2 space-y-5">

                {{-- Basic info card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-blue-50 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        البيانات الأساسية
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5">الاسم <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="أدخل الاسم الكامل"
                                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition @error('name') border-red-400 bg-red-50 @enderror">
                            @error('name')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5">البريد الإلكتروني <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="example@domain.com"
                                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition @error('email') border-red-400 bg-red-50 @enderror">
                            @error('email')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5">كلمة المرور <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required placeholder="••••••••"
                                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5">تأكيد كلمة المرور <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" required placeholder="••••••••"
                                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                        </div>
                    </div>
                </div>

                {{-- Roles card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-purple-50 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </span>
                        الأدوار والصلاحيات
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($roles as $role)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-blue-50 hover:border-blue-200 cursor-pointer transition group">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                   {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded accent-blue-600">
                            <span class="text-sm font-medium text-slate-600 group-hover:text-blue-700">{{ $role->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('roles')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Side card: Status + Actions --}}
            <div class="space-y-5">

                {{-- Status card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h3 class="text-sm font-bold text-slate-700 mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-green-50 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        الحالة
                    </h3>
                    <label class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-green-50 hover:border-green-200 transition">
                        <span class="text-sm font-medium text-slate-700">مدير نشط</span>
                        <input type="checkbox" name="active" id="active" value="1"
                               {{ old('active', '1') ? 'checked' : '' }}
                               class="w-4 h-4 rounded accent-green-600">
                    </label>
                </div>

                {{-- Actions card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h3 class="text-sm font-bold text-slate-700 mb-4 pb-3 border-b border-slate-100">الإجراءات</h3>
                    <div class="space-y-3">
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-5 py-3 rounded-xl shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7"/>
                            </svg>
                            ح�ظ المدير
                        </button>
                        <a href="{{ route('admin.settings.admins.index') }}"
                           class="w-full flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm px-5 py-3 rounded-xl transition">
                            إلغاء
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

