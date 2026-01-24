<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TraveloproService
{
    protected $userId;
    protected $password;
    protected $access;
    protected $url;

    public function __construct()
    {
        $this->userId = config('services.travelopro.user_id');
        $this->password = config('services.travelopro.password');
        $this->access = config('services.travelopro.access');
        $this->url = config('services.travelopro.url');
    }

    /**
     * Search for flights.
     *
     * @param array $data
     * @return array
     */
    public function searchFlights(array $data)
    {
        // Construct the payload with all available fields
        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(), // Get user's IP
            'requiredCurrency' => $data['requiredCurrency'] ?? 'SAR',
            'journeyType' => $data['journeyType'],
            'OriginDestinationInfo' => $this->formatItinerary($data['OriginDestinationInfo']),
            'class' => $data['class'] ?? 'Economy',
            'adults' => $data['adults'] ?? 1,
            'childs' => $data['childs'] ?? 0,
            'infants' => $data['infants'] ?? 0,
            // Optional fields included even if null/default
            'airlineCode' => $data['airlineCode'] ?? '',
            'directFlight' => $data['directFlight'] ?? 'false',
        ];

        // Log request for debugging (remove sensitive data in production)
        Log::info('Travelopro Search Request', ['payload' => $payload]);

        try {
            $response = Http::timeout(60)->post($this->url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Travelopro Search Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to fetch flight data',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Travelopro Search Exception', ['message' => $e->getMessage()]);

            return [
                'status' => 'error',
                'message' => 'Service unavailable',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format OriginDestinationInfo array.
     *
     * @param array $itineraries
     * @return array
     */
    private function formatItinerary(array $itineraries)
    {
        // Ensure structure matches Travelopro expectation
        // Example:
        // [
        //    [
        //        "departureDate" => "2023-02-19",
        //        "airportOriginCode" => "DEL",
        //        "airportDestinationCode" => "BOM"
        //    ]
        // ]
        return array_map(function ($segment) {
            return [
                'departureDate' => $segment['departureDate'],
                'returnDate' => $segment['returnDate'] ?? '', // Required for Return journeyType
                'airportOriginCode' => $segment['airportOriginCode'],
                'airportDestinationCode' => $segment['airportDestinationCode'],
            ];
        }, $itineraries);
    }
}
