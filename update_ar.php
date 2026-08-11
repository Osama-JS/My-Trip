<?php
$arFile = 'lang/ar.json';
$arTranslations = json_decode(file_get_contents($arFile), true);

$newTranslations = [
    "Meals" => "الوجبات",
    "Seats" => "المقاعد",
    "Baggage" => "الأمتعة"
];

foreach ($newTranslations as $key => $value) {
    $arTranslations[$key] = $value;
}

file_put_contents($arFile, json_encode($arTranslations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "More Missing Translations updated!";
