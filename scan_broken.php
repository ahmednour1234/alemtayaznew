<?php
$dir = 'C:/Users/USER/alemtayaznew/resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$replacement = "\xEF\xBF\xBD"; // U+FFFD
foreach ($files as $f) {
    if ($f->getExtension() !== 'php') continue;
    $lines = file($f->getPathname());
    foreach ($lines as $n => $line) {
        if (strpos($line, $replacement) !== false) {
            echo $f->getFilename() . ':' . ($n+1) . ': ' . trim($line) . "\n";
        }
    }
}
