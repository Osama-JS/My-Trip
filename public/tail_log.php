<?php
$file = __DIR__ . '/../storage/logs/laravel.log';
if (!file_exists($file)) die('No log');
$lines = file($file);
$last = array_slice($lines, -100);
echo implode("", $last);
