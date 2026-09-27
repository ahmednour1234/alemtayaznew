{{--
    شريط سفلي لالتقاط رقم الجوال.

    حقل واحد لا نموذج: الزائر يكتب رقمه ويضغط، ثانيتان وينتهي الأمر. الاسم
    يُؤخذ في المكالمة لا هنا — رقم بلا اسم أنفع من زائر غادر بلا أثر.

    لا يغطّي الشاشة ولا يقطع التصفّح، فيبقى ظاهراً دون أن يُزعج.
--}}
<div x-data="phoneBar()" x-init="init()" x-show="visible" x-cloak
     x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="translate-y-full opacity-0"
     x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="translate-y-0 opacity-100"
     x-transition:leave-end="translate-y-full opacity-0"
     class="fixed inset-x-0 bottom-0 z-[90] print:hidden">

    <div class="cta-band text-white shadow-2xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 sm:py-3.5">

            {{-- بعد الإرسال --}}
            <template x-if="done">
                <div class="flex items-center justify-center gap-2.5 py-1">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <p class="text-sm font-bold">تم استلام رقمك، سنتواصل معك قريباً.</p>
                </div>
            </template>

            <template x-if="! done">
                <div class="flex items-center gap-3">

                    <p class="hidden md:block text-sm font-bold flex-shrink-0">
                        اترك رقمك ونتواصل معك
                        <span class="font-normal text-white/70">— بدون التزام</span>
                    </p>

                    <form @submit.prevent="submit()" class="flex items-center gap-2 flex-1 min-w-0">
                        {{-- حقل فخّ مخفي لصدّ الروبوتات --}}
                        <input type="text" x-model="website" tabindex="-1" autocomplete="off"
                               class="hidden" aria-hidden="true">

                        <input type="tel" x-model="phone" required dir="ltr" maxlength="30"
                               inputmode="tel" autocomplete="tel"
                               placeholder="05xxxxxxxx"
                               aria-label="رقم الجوال"
                               class="flex-1 min-w-0 rounded-xl px-4 py-2.5 text-sm text-slate-800
                                      border-2 border-transparent focus:border-white focus:outline-none">

                        <button type="submit" :disabled="sending"
                                class="flex-shrink-0 inline-flex items-center gap-1.5 bg-gold hover:bg-gold-dark
                                       text-navy font-bold text-sm px-5 py-2.5 rounded-xl transition-colors
                                       disabled:opacity-60 disabled:cursor-not-allowed">
                            <svg x-show="sending" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span x-text="sending ? 'جارٍ…' : 'أرسل'"></span>
                        </button>
                    </form>

                    <button type="button" @click="dismiss()"
                            class="flex-shrink-0 w-8 h-8 rounded-lg hover:bg-white/15 flex items-center justify-center transition-colors"
                            aria-label="إخفاء">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>

            {{-- خطأ الخادم --}}
            <p x-show="error" x-cloak x-text="error"
               class="text-xs text-amber-200 mt-1.5 text-center"></p>
        </div>
    </div>
</div>
