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
            'adults' => (int)($data['adults'] ?? 1),
            'childs' => (int)($data['childs'] ?? 0),
            'infants' => (int)($data['infants'] ?? 0),
            // Optional fields included even if null/default
            'airlineCode' => $data['airlineCode'] ?? '',
            'directFlight' => $data['directFlight'] ?? 'false',
        ];

        // Log request for debugging (remove sensitive data in production)
        Log::info('Travelopro Search Request', ['payload' => $payload]);

        try {
            $response = Http::timeout(60)->post($this->url, $payload);

            if ($response->successful()) {
                $results = $response->json();
                
                // Apply Profit Margin
                $margin     = floatval(\App\Models\Setting::get('flight_margin', 0));
                $marginType = \App\Models\Setting::get('flight_margin_type', 'percentage');

                if ($margin > 0 && isset($results['AirSearchResponse']['AirSearchResult']['FareItineraries'])) {
                    $itineraries = &$results['AirSearchResponse']['AirSearchResult']['FareItineraries'];

                    if (isset($itineraries['FareItinerary'])) {
                        $this->applyMarginToItinerary($itineraries['FareItinerary'], $margin, $marginType);
                    } else {
                        foreach ($itineraries as &$itinerary) {
                            $this->applyMarginToItinerary($itinerary, $margin, $marginType);
                        }
                    }
                }
                
                return $results;
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
     * Helper to apply margin to a single flight itinerary.
     * Supports two modes: 'percentage' (default) or 'fixed' (flat SAR amount added to total fare).
     */
    private function applyMarginToItinerary(&$itinerary, $margin, $marginType = 'percentage')
    {
        if (isset($itinerary['AirItineraryFareInfo']['ItinTotalFares']['TotalFare']['Amount'])) {
            $oldAmount = floatval($itinerary['AirItineraryFareInfo']['ItinTotalFares']['TotalFare']['Amount']);

            if ($marginType === 'fixed') {
                $newAmount = $oldAmount + $margin;
            } else {
                $newAmount = $oldAmount * (1 + ($margin / 100));
            }

            $itinerary['AirItineraryFareInfo']['ItinTotalFares']['TotalFare']['Amount'] = number_format($newAmount, 2, '.', '');

            // Also update BaseFare proportionally
            if (isset($itinerary['AirItineraryFareInfo']['ItinTotalFares']['BaseFare']['Amount'])) {
                $oldBase = floatval($itinerary['AirItineraryFareInfo']['ItinTotalFares']['BaseFare']['Amount']);
                if ($marginType === 'fixed') {
                    $itinerary['AirItineraryFareInfo']['ItinTotalFares']['BaseFare']['Amount'] = number_format($oldBase + $margin, 2, '.', '');
                } else {
                    $itinerary['AirItineraryFareInfo']['ItinTotalFares']['BaseFare']['Amount'] = number_format($oldBase * (1 + ($margin / 100)), 2, '.', '');
                }
            }
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
                'airportOriginCode' => (string)($segment['airportOriginCode'] ?? ''),
                'airportDestinationCode' => (string)($segment['airportDestinationCode'] ?? ''),
            ];
        }, $itineraries);
    }

    /**
     * Get list of airports.
     *
     * @return array
     */
    public function getAirportList($force = false)
    {
        $cacheKey = 'travelopro_airports';
        
        if ($force) {
            cache()->forget($cacheKey);
        }

        return cache()->remember($cacheKey, 60 * 24, function () {
            $payload = [
                'user_id' => $this->userId,
                'user_password' => $this->password,
                'access' => $this->access,
                'ip_address' => request()->ip() ?? '127.0.0.1',
            ];

            $url = str_replace('availability', 'airport_list', $this->url);

            Log::info('Travelopro Airport List Request', ['url' => $url]);

            try {
                // Increased timeouts and added SSL verification bypass more explicitly
                $response = Http::withoutVerifying()
                    ->connectTimeout(60)
                    ->timeout(120)
                    ->post($url, $payload);

                if ($response->successful()) {
                     return $response->json();
                }

                Log::error('Travelopro Airport List Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [];

            } catch (\Exception $e) {
                Log::error('Travelopro Airport List Exception', ['message' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Sync airports to local database.
     *
     * @return array
     */
    public function syncAirports($force = false)
    {
        $airports = $this->getAirportList($force);
        
        // Handle both flat array and nested structure
        $list = [];
        if (isset($airports[0]['AirportCode'])) {
            $list = $airports;
        } elseif (isset($airports['Airports']['Airport'])) {
            $list = $airports['Airports']['Airport'];
        }

        if (!empty($list)) {
            $data = [];

            foreach ($list as $item) {
                // Adaptive mapping with length protection for codes
                $data[] = [
                    'airport_code' => substr($item['AirportCode'] ?? $item['airport_code'] ?? 'UNK', 0, 10),
                    'airport_name' => $item['AirportName'] ?? $item['airport_name'] ?? null,
                    'city_code' => substr($item['CityCode'] ?? $item['City'] ?? $item['city_code'] ?? 'UNK', 0, 10),
                    'city_name' => $item['CityName'] ?? $item['City'] ?? $item['city_name'] ?? null,
                    'country_code' => substr($item['CountryCode'] ?? $item['Country'] ?? $item['country_code'] ?? 'UNK', 0, 10),
                    'country_name' => $item['CountryName'] ?? $item['Country'] ?? $item['country_name'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Chunking to avoid massive single insert
                if (count($data) >= 500) {
                    \App\Models\Airport::upsert($data, ['airport_code'], ['airport_name', 'city_code', 'city_name', 'country_code', 'country_name', 'updated_at']);
                    $data = [];
                }
            }

            if (!empty($data)) {
                \App\Models\Airport::upsert($data, ['airport_code'], ['airport_name', 'city_code', 'city_name', 'country_code', 'country_name', 'updated_at']);
            }
            
            return [
                'status' => 'success',
                'count' => count($list),
                'message' => 'Airports synced successfully from API'
            ];
        }

        // Fallback: Seed basic airports if API is unreachable
        $fallbackAirports = [
            ['airport_code' => 'JED', 'airport_name' => 'King Abdulaziz International', 'city_code' => 'JED', 'city_name' => 'Jeddah', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia'],
            ['airport_code' => 'RUH', 'airport_name' => 'King Khalid International', 'city_code' => 'RUH', 'city_name' => 'Riyadh', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia'],
            ['airport_code' => 'DMM', 'airport_name' => 'King Fahd International', 'city_code' => 'DMM', 'city_name' => 'Dammam', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia'],
            ['airport_code' => 'MED', 'airport_name' => 'Prince Mohammad bin Abdulaziz', 'city_code' => 'MED', 'city_name' => 'Medina', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia'],
            ['airport_code' => 'DXB', 'airport_name' => 'Dubai International', 'city_code' => 'DXB', 'city_name' => 'Dubai', 'country_code' => 'AE', 'country_name' => 'United Arab Emirates'],
            ['airport_code' => 'CAI', 'airport_name' => 'Cairo International', 'city_code' => 'CAI', 'city_name' => 'Cairo', 'country_code' => 'EG', 'country_name' => 'Egypt'],
            ['airport_code' => 'LHR', 'airport_name' => 'Heathrow', 'city_code' => 'LHR', 'city_name' => 'London', 'country_code' => 'GB', 'country_name' => 'United Kingdom'],
            ['airport_code' => 'CDG', 'airport_name' => 'Charles de Gaulle', 'city_code' => 'CDG', 'city_name' => 'Paris', 'country_code' => 'FR', 'country_name' => 'France'],
            ['airport_code' => 'JFK', 'airport_name' => 'John F. Kennedy International', 'city_code' => 'NYC', 'city_name' => 'New York', 'country_code' => 'US', 'country_name' => 'United States'],
            ['airport_code' => 'IST', 'airport_name' => 'Istanbul Airport', 'city_code' => 'IST', 'city_name' => 'Istanbul', 'country_code' => 'TR', 'country_name' => 'Turkey'],
            ['airport_code' => 'AMM', 'airport_name' => 'Queen Alia International', 'city_code' => 'AMM', 'city_name' => 'Amman', 'country_code' => 'JO', 'country_name' => 'Jordan'],
        ];

        \App\Models\Airport::upsert($fallbackAirports, ['airport_code'], ['airport_name', 'city_code', 'city_name', 'country_code', 'country_name']);

        return [
            'status' => 'warning',
            'count' => count($fallbackAirports),
            'message' => 'API timeout. Fallback popular airports seeded successfully.'
        ];
    }

    /**
     * Get list of airlines.
     *
     * @return array
     */
    public function getAirlineList()
    {
        return cache()->remember('travelopro_airlines', 60 * 24, function () {
            $payload = [
                'user_id' => $this->userId,
                'user_password' => $this->password,
                'access' => $this->access,
                'ip_address' => request()->ip(),
            ];

            $url = str_replace('availability', 'airline_list', $this->url);

             Log::info('Travelopro Airline List Request');

            try {
                $response = Http::timeout(60)->post($url, $payload);

                if ($response->successful()) {
                    return $response->json();
                }

                 Log::error('Travelopro Airline List Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [];

            } catch (\Exception $e) {
                Log::error('Travelopro Airline List Exception', ['message' => $e->getMessage()]);
                return [];
            }
        });
    }
        /**
     * Validate flight fare.
     *
     * @param array $data
     * @return array
     */
    public function validateFare(array $data)
    {
        Log::info('Travelopro Validate Fare Request', ['data' => $data]);

        $payload = [
            'session_id' => $data['session_id'],
            'fare_source_code' => $data['fare_source_code'],
            'fare_source_code_inbound' => $data['fare_source_code_inbound'] ?? '',
        ];

        $url = str_replace('availability', 'revalidate', $this->url);

        try {
            $response = Http::timeout(60)->post($url, $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                // Apply Profit Margin
                $margin = floatval(\App\Models\Setting::get('flight_margin', 0));
                if ($margin > 0 && isset($result['AirRevalidateResponse']['AirRevalidateResult']['FareItineraries'])) {
                    $itineraries = &$result['AirRevalidateResponse']['AirRevalidateResult']['FareItineraries'];
                    
                    if (isset($itineraries['FareItinerary'])) {
                        $this->applyMarginToItinerary($itineraries['FareItinerary'], $margin);
                    } else {
                        foreach ($itineraries as &$itinerary) {
                            $this->applyMarginToItinerary($itinerary, $margin);
                        }
                    }
                }
                
                return $result;
            }

            Log::error('Travelopro Validate Fare Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to validate fare',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Travelopro Validate Fare Exception', ['message' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Service unavailable',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create flight booking (PNR).
     *
     * @param array $data
     * @return array
     */
    public function createBooking(array $data)
    {
        Log::info('Travelopro Booking Request', ['data' => $data]);

        $payload = [
            'flightBookingInfo' => [
                'flight_session_id' => $data['flight_session_id'],
                'fare_source_code' => $data['fare_source_code'],
                'IsPassportMandatory' => $data['IsPassportMandatory'] ?? false,
                'areaCode' => $data['areaCode'] ?? '080',
                'countryCode' => $data['countryCode'] ?? '966',
                'fareType' => $data['fareType'] ?? 'Private',
                'fare_source_code_inbound' => $data['fare_source_code_inbound'] ?? null,
            ],
            'paxInfo' => [
                'clientRef' => $data['clientRef'] ?? uniqid('TR'),
                'customerEmail' => $data['customerEmail'],
                'customerPhone' => $data['customerPhone'],
                'bookingNote' => $data['bookingNote'] ?? '',
                'paxDetails' => [
                    $this->formatPaxDetails($data['passengers'])
                ]
            ]
        ];

        $url = str_replace('availability', 'booking', $this->url);

        try {
            $response = Http::timeout(90)->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Travelopro Booking Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to create booking',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Travelopro Booking Exception', ['message' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Service unavailable',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format passenger details for booking.
     *
     * @param array $passengers
     * @return array
     */
    private function formatPaxDetails(array $passengers)
    {
        $formatted = [];

        // Group passengers by type (adt, chd, inf)
        $groups = [
            'adult' => [],
            'child' => [],
            'infant' => []
        ];

        foreach ($passengers as $pax) {
            $type = strtolower($pax['type']); // assuming type is passed as adult, child, infant
            if (isset($groups[$type])) {
                $groups[$type][] = $pax;
            }
        }

        foreach ($groups as $type => $paxList) {
            if (empty($paxList)) continue;

            $details = [
                'title' => array_column($paxList, 'title'),
                'firstName' => array_column($paxList, 'first_name'),
                'lastName' => array_column($paxList, 'last_name'),
                'dob' => array_column($paxList, 'dob'),
                'nationality' => array_column($paxList, 'nationality'),
                'passportNo' => array_column($paxList, 'passport_no'),
                'passportIssueCountry' => array_column($paxList, 'passport_issue_country'),
                'passportExpiryDate' => array_column($paxList, 'passport_expiry_date'),
            ];

            // Add extra services if present
            // Simplified handling: assuming extra services are passed as nested arrays
            if (isset($paxList[0]['extra_services_outbound'])) {
                 $details['ExtraServiceOutbound'] = array_column($paxList, 'extra_services_outbound');
            }
             if (isset($paxList[0]['extra_services_inbound'])) {
                 $details['ExtraServiceInbound'] = array_column($paxList, 'extra_services_inbound');
            }

            $formatted[$type] = $details;
        }

        return $formatted;
    }

    /**
     * Order ticket for booking.
     *
     * @param string $uniqueId
     * @return array
     */
    public function orderTicket(string $uniqueId)
    {
        Log::info('Travelopro Order Ticket Request', ['UniqueID' => $uniqueId]);

        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(),
            'UniqueID' => $uniqueId
        ];

        $url = str_replace('availability', 'ticket_order', $this->url);

        try {
            $response = Http::timeout(60)->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Travelopro Order Ticket Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

             return [
                'status' => 'error',
                'message' => 'Failed to order ticket',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Travelopro Order Ticket Exception', ['message' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Service unavailable',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get trip details.
     *
     * @param string $uniqueId
     * @return array
     */
    public function getTripDetails(string $uniqueId)
    {
        // Trip Details Request
        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(),
            'UniqueID' => $uniqueId
        ];

        $url = str_replace('availability', 'trip_details', $this->url);

        try {
            $response = Http::timeout(60)->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Travelopro Trip Details Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to get trip details',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Travelopro Trip Details Exception', ['message' => $e->getMessage()]);
             return [
                'status' => 'error',
                'message' => 'Service unavailable',
                'error' => $e->getMessage()
            ];
        }
    }
}
