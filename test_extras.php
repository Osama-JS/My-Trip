<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$service = app(App\Services\TraveloproService::class);
$res = $service->getExtraServices([
    'session_id' => 'MTc4NjQ1NTUxM18yNTg3Njgx',
    'fare_source_code' => 'TFY0c3VDekxUSVF2bU5QWVR0RzMxTS81UkwxZUNKRnh2OTZXYlpHRlkvMjVDMmFPa0lyMnA0ams1TVh5QWE0Wm5LZWFtVlBvOVozRUhVN2JQRGZBenlySEN0eWs4cXY1TjNPTFplNnN2b3MxT0dMa0dBS2FXKzVqc3hZSFQwUUFYZFVnSjdnRTJrSFNtcmpwcDM1dGZ3PT0='
]);
file_put_contents('test_extras.json', json_encode($res));
