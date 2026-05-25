<?php
/**
 * Fix double-encoded Arabic UTF-8 in blade files.
 * Arabic UTF-8 bytes were treated as Windows-1252 characters and re-encoded as UTF-8.
 * Fix: convert the broken UTF-8 string to Windows-1252 bytes = the correct UTF-8 bytes.
 *
 * Safety: skip any file that already contains correct Arabic (U+0600–U+06FF).
 */

$dir = __DIR__ . '/resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$fixed  = 0;
$skipped = 0;

foreach ($files as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }

    $path    = $file->getPathname();
    $content = file_get_contents($path);

    // If the file already has CORRECT Arabic Unicode (U+0600–U+06FF), leave it alone
    if (preg_match('/[\x{0600}-\x{06FF}]/u', $content)) {
        $skipped++;
        echo "SKIP  (correct Arabic): " . $file->getFilename() . "\n";
        continue;
    }

    // Only process files that contain the garbled Latin-1 surrogates for Arabic (Ø / Ù pattern)
    if (!preg_match('/[ØÙ]/', $content)) {
        continue;
    }

    // Each garbled char is a Windows-1252 character whose byte value IS the correct UTF-8 byte.
    // iconv('UTF-8','Windows-1252') maps each char back to its 1-byte Windows-1252 value.
    // The resulting byte string is already valid UTF-8 Arabic.
    $result = iconv('UTF-8', 'Windows-1252//IGNORE', $content);

    if ($result !== false && $result !== $content) {
        file_put_contents($path, $result);
        echo "FIXED: " . $file->getFilename() . "  (" . $file->getPathname() . ")\n";
        $fixed++;
    }
}

echo "\n✓ Fixed: $fixed files | Skipped (already correct): $skipped files\n";
