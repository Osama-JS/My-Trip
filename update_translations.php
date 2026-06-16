<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views');
$iterator = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($iterator, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$matches = [];
foreach ($files as $file) {
    $content = file_get_contents($file[0]);
    preg_match_all("/__\(['\"](.*?)['\"]\)/", $content, $m);
    if (!empty($m[1])) {
        $matches = array_merge($matches, $m[1]);
    }
}

$uniqueStrings = array_unique($matches);

$arFile = __DIR__ . '/lang/ar.json';
$enFile = __DIR__ . '/lang/en.json';

$ar = json_decode(file_get_contents($arFile), true) ?: [];
$en = json_decode(file_get_contents($enFile), true) ?: [];

$newArCount = 0;
$newEnCount = 0;

$toTranslateAr = [];

foreach ($uniqueStrings as $str) {
    if (!isset($en[$str])) {
        $en[$str] = $str;
        $newEnCount++;
    }
    if (!isset($ar[$str])) {
        $ar[$str] = $str . " [AR]"; 
        $toTranslateAr[] = $str;
        $newArCount++;
    }
}

file_put_contents($enFile, json_encode($en, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
file_put_contents($arFile, json_encode($ar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Added $newEnCount new strings to en.json\n";
echo "Added $newArCount new strings to ar.json\n";

file_put_contents(__DIR__ . '/needs_ar_translation.json', json_encode($toTranslateAr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Missing AR translations saved to needs_ar_translation.json\n";
