<?php
$arFile = __DIR__ . '/lang/ar.json';
$ar = json_decode(file_get_contents($arFile), true);
$untranslated = [];
foreach ($ar as $k => $v) {
    if (preg_match('/[a-zA-Z]/', $v)) { // If it still contains english characters
        $untranslated[$k] = $v;
    }
}
file_put_contents(__DIR__ . '/untranslated_ar.json', json_encode($untranslated, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo 'Found ' . count($untranslated) . ' untranslated strings.';
