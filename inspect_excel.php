<?php
require __DIR__ . '/vendor/autoload.php';
$path = 'C:/Users/ahmednour/Downloads/عقود-200-مع-اكواد-الفروع.xlsx';
if (!file_exists($path)) { die("الملف غير موجود في Downloads\n"); }
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
$reader->setReadDataOnly(true);
$sheet  = $reader->load($path)->getActiveSheet();
echo "إجمالي الصفوف: " . $sheet->getHighestRow() . "\n";
echo "إجمالي الأعمدة: " . $sheet->getHighestColumn() . "\n";
for ($r = 1; $r <= 4; $r++) {
    echo "=== صف $r ===\n";
    foreach (['A','B','C','D','E','F'] as $c) {
        $v = $sheet->getCell($c.$r)->getValue();
        if ($v !== null && $v !== '') echo "  $c: $v\n";
    }
}
