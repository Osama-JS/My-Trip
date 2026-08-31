<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

Setting::set('sitata_organization_id', 'd745d42c-4e0b-4be4-829f-b0d30dad006f');
Setting::set('sitata_api_key', '453d1ba9-7ee8-4cc8-b091-5c1cf705dd2c');
Setting::set('sitata_public_token', '2a9758e5-d840-4aac-bca3-bb4961f5bb7c');
Setting::set('sitata_api_url', 'https://staging.sitata.com/api/v2');
Setting::set('insurance_enabled', '1');

echo "Saved settings:\n";
echo "Organization ID: " . Setting::get('sitata_organization_id') . "\n";
echo "API Key: " . Setting::get('sitata_api_key') . "\n";
echo "API URL: " . Setting::get('sitata_api_url') . "\n";
echo "Public Token: " . Setting::get('sitata_public_token') . "\n";
