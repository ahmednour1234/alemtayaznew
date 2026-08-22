@php
    $S = fn(string $k) => \App\Models\SiteSetting::value($k);
    $popupNationalities = \App\Models\Nationality::where('active', true)->orderBy('name')->get(['id', 'name']);
@endphp

{{--
    نافذة طلب سريع تظهر للزائر بعد ثوانٍ من دخوله.
    تُخزَّن حالة الإغلاق في localStorage فلا تتكرر مع كل تصفّح،
    وتُتخطّى تماماً في صفحة «تواصل معنا» لأن النموذج معروض فيها أصلاً.
--}}
<div x-data="leadPopup()" x-init="init()">
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-navy-dark/70 backdrop-blur-sm"
         @click.self="close()"
         @keydown.escape.window="close()"
         role="dialog" aria-modal="true" aria-labelledby="lead-popup-title">

        <div x-show="open"
             x-transition:enter="transition ease-out duration-300 delay-75"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden max-h-[92vh] overflow-y-auto">

            {{-- ترويسة --}}
            <div class="cta-band text-white px-6 sm:px-8 py-6 relative">
                <button type="button" @click="close()"
                        class="absolute top-4 end-4 w-8 h-8 rounded-full bg-white/15 hover:bg-white/25 flex items-center justify-center transition-colors"
                        aria-label="إغلاق">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <h2 id="lead-popup-title" class="text-xl sm:text-2xl font-extrabold pe-10">اطلب عاملتك الآن</h2>
                <p class="text-white/80 text-sm mt-2">
                    اترك بياناتك وسيتواصل معك فريقنا خلال وقت قصير.
                </p>
            </div>

            {{-- رسالة النجاح --}}
            <template x-if="done">
                <div class="px-6 sm:px-8 py-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-extrabold text-navy text-lg">تم استلام طلبك</h3>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                        شكراً لك، سيتواصل معك فريقنا في أقرب وقت.
                    </p>
                    <button type="button" @click="close()"
                            class="mt-6 bg-navy hover:bg-navy-light text-white text-sm font-bold px-8 py-3 rounded-xl transition-colors">
                        إغلاق
                    </button>
                </div>
            </template>

            {{-- النموذج --}}
            <form x-show="! done" @submit.prevent="submit()" class="px-6 sm:px-8 py-6 space-y-4">
                {{-- حقل فخّ مخفي لصدّ الروبوتات --}}
                <input type="text" x-model="form.website" tabindex="-1" autocomplete="off"
                       class="hidden" aria-hidden="true">

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        الاسم <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="form.name" required maxlength="255"
                           placeholder="الاسم الكامل"
                           class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        رقم الجوال <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" x-model="form.phone" required dir="ltr" maxlength="30"
                           placeholder="05xxxxxxxx"
                           class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">المدينة</label>
                        <input type="text" x-model="form.city" maxlength="100"
                               placeholder="الرياض"
                               class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">الجنسية المطلوبة</label>
                        <select x-model="form.nationality_id"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy">
                            <option value="">غير محدد</option>
                            @foreach($popupNationalities as $nat)
                            <option value="{{ $nat->id }}">{{ $nat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        الخدمة المطلوبة <span class="text-red-500">*</span>
                    </label>
                    <select x-model="form.service" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy">
                        <option value="">اختر الخدمة</option>
                        @foreach(\App\Http\Controllers\PublicSite\ContactController::SERVICES as $srv)
                        <option value="{{ $srv }}">{{ $srv }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">تفاصيل إضافية</label>
                    <textarea x-model="form.notes" rows="2" maxlength="2000"
                              placeholder="اكتب أي تفاصيل تساعدنا في خدمتك..."
                              class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy"></textarea>
                </div>

                {{-- أخطاء الخادم --}}
                <template x-if="errors.length">
                    <div class="bg-red-50 border border-red-200 rounded-xl p-3">
                        <ul class="text-xs text-red-700 space-y-1 list-disc list-inside">
                            <template x-for="err in errors" :key="err">
                                <li x-text="err"></li>
                            </template>
                        </ul>
                    </div>
                </template>

                <button type="submit" :disabled="sending"
                        class="btn-glow w-full inline-flex items-center justify-center gap-2 bg-gold hover:bg-gold-dark
                               text-white font-bold py-4 rounded-xl transition-all duration-300
                               disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg x-show="sending" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="sending ? 'جارٍ الإرسال...' : 'إرسال الطلب'"></span>
                </button>

                <p class="text-[11px] text-slate-400 text-center">
                    بياناتك تُستخدم للتواصل معك بخصوص طلبك فقط.
                </p>
            </form>
        </div>
    </div>
</div>
