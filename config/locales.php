<?php

return [

    /*
    |--------------------------------------------------------------------------
    | اللغات المدعومة | Supported Locales
    |--------------------------------------------------------------------------
    |
    | native : اسم اللغة بلغتها (يظهر في مبدّل اللغة)
    | dir    : اتجاه الكتابة — rtl للعربية، ltr لغيرها
    | font   : عائلة الخط المناسبة للأبجدية
    |
    */

    'supported' => [
        'ar' => [
            'native'      => 'العربية',
            'flag'        => '🇸🇦',
            'dir'         => 'rtl',
            'font_stack'  => ['Cairo', 'sans-serif'],
            'google_font' => 'Cairo:wght@300;400;500;600;700;800',
        ],
        'en' => [
            'native'      => 'English',
            'flag'        => '🇬🇧',
            'dir'         => 'ltr',
            'font_stack'  => ['Inter', 'Cairo', 'sans-serif'],
            'google_font' => 'Inter:wght@300;400;500;600;700;800',
        ],
        'fil' => [
            'native'      => 'Filipino',
            'flag'        => '🇵🇭',
            'dir'         => 'ltr',
            'font_stack'  => ['Inter', 'Cairo', 'sans-serif'],
            'google_font' => 'Inter:wght@300;400;500;600;700;800',
        ],
        'si' => [
            'native'      => 'සිංහල',
            'flag'        => '🇱🇰',
            'dir'         => 'ltr',
            // السنهالية تحتاج خطاً خاصاً وإلا ظهرت مربعات فارغة
            'font_stack'  => ['Noto Sans Sinhala', 'Inter', 'sans-serif'],
            'google_font' => 'Noto+Sans+Sinhala:wght@300;400;500;600;700&family=Inter:wght@400;600',
        ],
    ],

    /** اللغة الافتراضية عند أول زيارة. */
    'default' => 'ar',

];
