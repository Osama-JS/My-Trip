<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = app(\App\Services\TraveloproService::class);
$request = [
    'journeyType' => 'OneWay',
    'class' => 'Economy',
    'adults' => 1,
    'childs' => 0,
    'infants' => 0,
    'OriginDestinationInfo' => [
        [
            'departureDate' => '2026-08-12',
            'airportOriginCode' => 'RUH',
            'airportDestinationCode' => 'DMM',
        ]
    ]
];
try {
    $res = $service->searchFlights($request);
    file_put_contents('test_api_out.json', json_encode($res, JSON_PRETTY_PRINT));
} catch (Exception $e) {
    file_put_contents('test_api_out.txt', $e->getMessage());
}
