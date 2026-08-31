<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use App\Models\Setting;

$orgId = Setting::get('sitata_organization_id', 'd745d42c-4e0b-4be4-829f-b0d30dad006f');
$apiKey = Setting::get('sitata_api_key', '453d1ba9-7ee8-4cc8-b091-5c1cf705dd2c');
$apiUrl = 'https://staging.sitata.com/api/v2';

$headers = [
    'Organization'  => $orgId,
    'Authorization' => 'TKN ' . $apiKey,
    'Content-Type'  => 'application/json',
    'Accept'        => 'application/json'
];

$res = Http::withHeaders($headers)->get($apiUrl . '/products');
echo "Products response:\n";
echo json_encode($res->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
