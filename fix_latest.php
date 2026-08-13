<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fb = \App\Models\FlightBooking::latest()->first();
$log = \App\Models\FlightApiLog::where('booking_id', $fb->booking_id)->where('action', 'createBooking')->latest()->first();
if ($log) {
    $req = $log->request_payload ?? [];
    $sessionId = $req['flightBookingInfo']['flight_session_id'] ?? null;
    
    if ($sessionId) {
        $validateLog = \App\Models\FlightApiLog::where('action', 'validateFare')
            ->where(function($q) use ($sessionId) {
                $q->whereJsonContains('request_payload->session_id', $sessionId)
                  ->orWhere('request_payload', 'like', '%"session_id":"' . $sessionId . '"%');
            })
            ->latest()->first();
            
        if ($validateLog) {
            $valRes = is_string($validateLog->response_payload) ? json_decode($validateLog->response_payload, true) : $validateLog->response_payload;
            $apiResult = $valRes['AirRevalidateResponse']['AirRevalidateResult'] ?? [];
            $fareItineraries = $apiResult['FareItineraries']['FareItinerary'] ?? [];
            if (isset($fareItineraries['OriginDestinationOptions'])) {
                $fareItineraries = [$fareItineraries];
            }
            $fi = $fareItineraries[0] ?? [];
            $odo = $fi['OriginDestinationOptions'] ?? [];
            
            $requestSegments = [];
            foreach ($odo as $wrapper) {
                $odOpts = $wrapper['OriginDestinationOption'] ?? [];
                if (!isset($odOpts[0])) $odOpts = [$odOpts];
                $legSegs = [];
                foreach ($odOpts as $opt) {
                    $seg = $opt['FlightSegment'] ?? null;
                    if ($seg) $legSegs[] = $seg;
                }
                if (!empty($legSegs)) $requestSegments[] = ['legs' => $legSegs];
            }
            
            if (!empty($requestSegments)) {
                $fb->update(['itinerary_data' => ['segments' => $requestSegments]]);
                echo "Successfully updated itinerary_data for latest booking with " . count($requestSegments) . " legs.\n";
            } else {
                echo "No segments found in validateFare log.\n";
            }
        } else {
            echo "validateFare log not found for session {$sessionId}.\n";
        }
    } else {
        echo "session_id not found in createBooking log.\n";
    }
}
