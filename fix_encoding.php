<?php
/**
 * Fix double-encoded Arabic UTF-8 in blade files.
 *
 * Root cause: Arabic UTF-8 bytes were misread as Windows-1252 characters
 * and re-saved as UTF-8, producing "Ù„" instead of "ل".
 *
 * Fix: for each Unicode code point in the garbled string, write back its raw byte
 * value.  Code points 0x00-0xFF map directly.  The 5 Windows-1252 undefined
 * slots (0x81, 0x8D, 0x8F, 0x90, 0x9D) fall in 0x00-0xFF so they also map
 * correctly -- this is what iconv('IGNORE') silently discarded before.
 *
 * Safety: skip any file that already contains correct Arabic (U+0600-U+06FF).
 */

function cpToByte(int $cp): ?string
{
    if ($cp <= 0xFF) {
        return chr($cp);
    }
    static $w1252 = [
        0x20AC=>0x80,0x201A=>0x82,0x0192=>0x83,0x201E=>0x84,
        0x2026=>0x85,0x2020=>0x86,0x2021=>0x87,0x02C6=>0x88,
        0x2030=>0x89,0x0160=>0x8A,0x2039=>0x8B,0x0152=>0x8C,
        0x017D=>0x8E,0x2018=>0x91,0x2019=>0x92,0x201C=>0x93,
        0x201D=>0x94,0x2022=>0x95,0x2013=>0x96,0x2014=>0x97,
        0x02DC=>0x98,0x2122=>0x99,0x0161=>0x9A,0x203A=>0x9B,
        0x0153=>0x9C,0x017E=>0x9E,0x0178=>0x9F,
    ];
    return isset($w1252[$cp]) ? chr($w1252[$cp]) : null;
}

function fixContent(string $content): string
{
    $out = '';
    $len = mb_strlen($content, 'UTF-8');
    for ($i = 0; $i < $len; $i++) {
        $char = mb_substr($content, $i, 1, 'UTF-8');
        $cp   = mb_ord($char, 'UTF-8');
        $byte = cpToByte($cp);
        $out .= ($byte !== null) ? $byte : $char;
    }
    return $out;
}

$dir   = __DIR__ . '/resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$fixed = $skipped = 0;

foreach ($files as $file) {
    if ($file->getExtension() !== 'php') continue;

    $path    = $file->getPathname();
    $content = file_get_contents($path);

    if (preg_match('/[\x{0600}-\x{06FF}]/u', $content)) {
        $skipped++;
        echo "SKIP  (correct Arabic): " . $file->getFilename() . "\n";
        continue;
    }
    if (!preg_match('/[ØÙ]/', $content)) continue;

    $result = fixContent($content);
    if ($result !== $content) {
        file_put_contents($path, $result);
        echo "FIXED: " . $file->getFilename() . "\n";
        $fixed++;
    }
}
echo "\nFixed: $fixed | Skipped (already correct): $skipped\n";
