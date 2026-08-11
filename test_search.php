<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$service = app(App\Services\TraveloproService::class);
$res = $service->searchFlights([
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
]);
file_put_contents('test_search.json', json_encode($res));
