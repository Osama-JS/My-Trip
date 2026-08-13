<?php
$data = json_decode(file_get_contents('test_search.json'), true);
$itin = $data['AirSearchResponse']['AirSearchResult']['FareItineraries']['FareItinerary'][0] ?? [];
$segment = $itin['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment'] ?? [];
print_r($segment);
