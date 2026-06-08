<?php

namespace App\Console\Commands;

use Barryvdh\DomPDF\Facade\Pdf;
use FontLib\Font;
use Illuminate\Console\Command;

/**
 * يثبّت خط Amiri في مجلد خطوط dompdf حتى تظهر الحروف العربية متّصلة وبالاتجاه الصحيح في ملفات الـ PDF.
 * شغّله مرة واحدة عند نشر المشروع على بيئة جديدة:  php artisan pdf:install-arabic-font
 */
class InstallArabicPdfFont extends Command
{
    protected $signature = 'pdf:install-arabic-font';

    protected $description = 'تثبيت خط Amiri في dompdf لدعم العربية في ملفات الـ PDF';

    public function handle(): int
    {
        $fontDir = Pdf::getDomPDF()->getOptions()->getFontDir();
        if (!is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }

        $variants = [
            'normal' => resource_path('fonts/Amiri-Regular.ttf'),
            'bold'   => resource_path('fonts/Amiri-Bold.ttf'),
        ];

        $manifest = [];
        foreach ($variants as $variant => $src) {
            if (!is_file($src)) {
                $this->error("الخط غير موجود: {$src}");
                return self::FAILURE;
            }

            $prefix = 'amiri_' . $variant;          // اسم الملف داخل مجلد الخطوط
            $dest   = $fontDir . '/' . $prefix;

            $font = Font::load($src);
            if (!$font) {
                $this->error("تعذّر قراءة الخط: {$src}");
                return self::FAILURE;
            }
            $font->parse();
            $font->saveAdobeFontMetrics($dest . '.ufm');
            $font->close();
            copy($src, $dest . '.ttf');

            // مفتاح العائلة يجب أن يكون بأحرف صغيرة حتى يتعرّف عليه dompdf
            $manifest['amiri'][$variant] = $prefix;
            $this->line("  ✓ amiri ({$variant})");
        }

        $installedFile = $fontDir . '/installed-fonts.json';
        $existing = is_readable($installedFile)
            ? (json_decode(file_get_contents($installedFile), true) ?: [])
            : [];
        $existing = array_merge($existing, $manifest);
        file_put_contents($installedFile, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info('تم تثبيت خط Amiri في: ' . $fontDir);
        return self::SUCCESS;
    }
}
