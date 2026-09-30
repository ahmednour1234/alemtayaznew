<?php

namespace App\Console\Commands;

use App\Exports\AdminsExport;
use App\Models\Admin;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

/**
 * تصدير مستخدمي لوحة الإدارة إلى ملف إكسل.
 *
 * بلا كلمات المرور — وهي غير قابلة للتصدير أصلاً لأنها مخزّنة مجزّأة
 * (hash) في اتجاه واحد، فلا نصّ لها في قاعدة البيانات.
 */
class ExportAdmins extends Command
{
    protected $signature = 'admins:export
                            {--file=admins.xlsx : اسم الملف}
                            {--active : اقتصر على المستخدمين النشطين}';

    protected $description = 'تصدير مستخدمي لوحة الإدارة إلى إكسل';

    public function handle(): int
    {
        $file = $this->option('file');

        if (! str_ends_with(strtolower($file), '.xlsx')) {
            $file .= '.xlsx';
        }

        $count = Admin::when($this->option('active'), fn ($q) => $q->where('active', true))->count();

        if ($count === 0) {
            $this->warn('لا يوجد مستخدمون للتصدير.');
            return self::SUCCESS;
        }

        // نحدّد القرص صراحةً: القرص الافتراضي قد يكون public فيتغيّر المسار
        // ويتعذّر على المستخدم العثور على الملف.
        Excel::store(new AdminsExport($this->option('active')), $file, 'local');

        // Storage::path يعطي المسار الحقيقي — في لارافل 12 يقع تحت
        // storage/app/private وليس storage/app مباشرة.
        $full = \Illuminate\Support\Facades\Storage::disk('local')->path($file);

        $this->info("تم تصدير {$count} مستخدماً إلى:");
        $this->line('  ' . $full);
        $this->newLine();
        $this->comment('لتحميله عبر المتصفّح:');
        $this->line("  cp {$full} public/{$file}");
        $this->line('  ثمّ افتح: ' . url($file));
        $this->line("  وامسحه بعد التحميل:  rm public/{$file}");
        $this->newLine();
        $this->comment('كلمات المرور غير مُدرَجة: النظام يخزّنها مجزّأة ولا يحتفظ بنصّها.');

        return self::SUCCESS;
    }
}
