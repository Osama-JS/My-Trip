<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\FlightApiLog;
use Illuminate\Support\Facades\Auth;

class TraveloproService
{
    private function logApiCall($action, $url, $payload, $response, $statusCode, $startTime)
    {
        $executionTime = microtime(true) - $startTime;

        // Hide password from logs
        if (isset($payload['user_password'])) {
            $payload['user_password'] = '***';
        }

        try {
            FlightApiLog::create([
                'user_id' => Auth::id(), // Login user ID not API user ID
                'action' => $action,
                'endpoint' => $url,
                'method' => 'POST',
                'request_payload' => $payload,
                'response_payload' => $response,
                'status_code' => $statusCode,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'execution_time' => $executionTime
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log API call to DB: ' . $e->getMessage());
        }
    }
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
        // Construct the payload with ALL 13 fields mentioned in travelopro.txt
        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'requiredCurrency' => $data['requiredCurrency'] ?? 'USD',
            'journeyType' => $data['journeyType'] ?? 'OneWay', // OneWay, Return, Circle
            'OriginDestinationInfo' => $this->formatItinerary($data['OriginDestinationInfo']),
            'class' => $data['class'] ?? 'Economy', // First, Business, Economy, PremiumEconomy
            'adults' => (int) ($data['adults'] ?? 1),
            'childs' => (int) ($data['childs'] ?? 0),
            'infants' => (int) ($data['infants'] ?? 0),
            'airlineCode' => $data['airlineCode'] ?? '',
            'directFlight' => (int) ($data['directFlight'] ?? 0), // 0 or 1
        ];

        // Log request for debugging (remove sensitive data in production)
        Log::info('Travelopro Search Request', ['payload' => $payload]);
        $startTime = microtime(true);

        try {
            $response = Http::timeout(60)->post($this->url, $payload);

            $this->logApiCall('Search Flights', $this->url, $payload, $response->json(), $response->status(), $startTime);

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
            $this->logApiCall('Search Flights', $this->url, $payload, ['error' => $e->getMessage()], 500, $startTime);
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
        // Ensure structure matches Travelopro expectation (travelopro.txt)
        return array_map(function ($segment) {
            $formatted = [
                'departureDate' => $segment['departureDate'], // YYYY-MM-DD
                'airportOriginCode' => $segment['airportOriginCode'], // Three letter code
                'airportDestinationCode' => $segment['airportDestinationCode'], // Three letter code
            ];

            // returnDate is mandatory for Return journeyType
            if (isset($segment['returnDate']) && !empty($segment['returnDate'])) {
                $formatted['returnDate'] = $segment['returnDate'];
            }

            return $formatted;
        }, $itineraries);
    }

    /**
     * Get list of airports.
     *
     * @param bool $forceRefresh
     * @return array
     */
    public function getAirportList($forceRefresh = false)
    {
        if ($forceRefresh) {
            cache()->forget('travelopro_airports');
        }

        return cache()->remember('travelopro_airports', 60 * 24 * 7, function () {
            $payload = [
                'user_id' => $this->userId,
                'user_password' => $this->password,
                'access' => $this->access,
                'ip_address' => request()->ip(),
            ];

            $url = str_replace('availability', 'airport_list', $this->url);

            Log::info('Travelopro Airport List Request');
            $startTime = microtime(true);

            try {
                $response = Http::timeout(60)->post($url, $payload);

                $this->logApiCall('Get Airport List', $url, $payload, $response->json(), $response->status(), $startTime);

                if ($response->successful()) {
                     return $response->json();
                }

                Log::error('Travelopro Airport List Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [];

            } catch (\Exception $e) {
                $this->logApiCall('Get Airport List', $url, $payload, ['error' => $e->getMessage()], 500, $startTime);
                Log::error('Travelopro Airport List Exception', ['message' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Get list of airlines.
     *
     * @param bool $forceRefresh
     * @return array
     */
    public function getAirlineList($forceRefresh = false)
    {
        if ($forceRefresh) {
            cache()->forget('travelopro_airlines');
        }

        return cache()->remember('travelopro_airlines', 60 * 24 * 7, function () {
            $payload = [
                'user_id' => $this->userId,
                'user_password' => $this->password,
                'access' => $this->access,
                'ip_address' => request()->ip(),
            ];

            $url = str_replace('availability', 'airline_list', $this->url);

             Log::info('Travelopro Airline List Request');
             $startTime = microtime(true);

            try {
                $response = Http::timeout(60)->post($url, $payload);

                $this->logApiCall('Get Airline List', $url, $payload, $response->json(), $response->status(), $startTime);

                if ($response->successful()) {
                    return $response->json();
                }

                 Log::error('Travelopro Airline List Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [];

            } catch (\Exception $e) {
                $this->logApiCall('Get Airline List', $url, $payload, ['error' => $e->getMessage()], 500, $startTime);
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
        $startTime = microtime(true);

        try {
            $response = Http::timeout(60)->post($url, $payload);

            $this->logApiCall('Validate Fare', $url, $payload, $response->json(), $response->status(), $startTime);

            if ($response->successful()) {
                return $response->json();
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
            $this->logApiCall('Validate Fare', $url, $payload, ['error' => $e->getMessage()], 500, $startTime);
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
        $startTime = microtime(true);

        try {
            $response = Http::timeout(90)->post($url, $payload);

            $this->logApiCall('Create Booking', $url, $payload, $response->json(), $response->status(), $startTime);

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
            $this->logApiCall('Create Booking', $url, $payload, ['error' => $e->getMessage()], 500, $startTime);
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
        $startTime = microtime(true);

        try {
            $response = Http::timeout(60)->post($url, $payload);

            $this->logApiCall('Order Ticket', $url, $payload, $response->json(), $response->status(), $startTime);

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
            $this->logApiCall('Order Ticket', $url, $payload, ['error' => $e->getMessage()], 500, $startTime);
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
        $startTime = microtime(true);

        try {
            $response = Http::timeout(60)->post($url, $payload);

            $this->logApiCall('Get Trip Details', $url, $payload, $response->json(), $response->status(), $startTime);

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
            $this->logApiCall('Get Trip Details', $url, $payload, ['error' => $e->getMessage()], 500, $startTime);
            Log::error('Travelopro Trip Details Exception', ['message' => $e->getMessage()]);
             return [
                'status' => 'error',
                'message' => 'Service unavailable',
                'error' => $e->getMessage()
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | New Implemented Methods (Missing Features)
    |--------------------------------------------------------------------------
    */

    public function addBookingNotes(array $data)
    {
        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(),
            'UniqueID' => $data['uniqueId'],
            'notes' => $data['notes']
        ];

        return $this->sendRequest('booking_notes', $payload, 'Booking Notes');
    }

    public function cancelBooking(array $data)
    {
        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(),
            'UniqueID' => $data['uniqueId']
        ];

        return $this->sendRequest('cancel', $payload, 'Cancel Booking');
    }

    public function getExtraServices(array $data)
    {
        $payload = [
            'session_id' => $data['session_id'],
            'fare_source_code' => $data['fare_source_code']
        ];
        // Does not require user_id/password in body according to docs, only session_id/fare_source_code?
        // Checking doc again: Request Parameters: session_id, fare_source_code.
        // It does NOT list user_id/password.
        // But usually auth is needed.
        // Let's assume it might rely on session_id or the doc is implicit about auth only for some.
        // Wait, SearchFlights used user_id. ExtraServices doc says only session_id and fare_source_code.
        // I will follow doc.

        return $this->sendRequest('extra_services', $payload, 'Extra Services');
    }

    public function getFareRules(array $data)
    {
        $payload = [
            'session_id' => $data['session_id'],
            'fare_source_code' => $data['fare_source_code'],
            'fare_source_code_inbound' => $data['fare_source_code_inbound'] ?? ''
        ];

        return $this->sendRequest('fare_rules', $payload, 'Fare Rules');
    }

    public function refundQuote(array $data)
    {
        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(),
            'UniqueID' => $data['uniqueId'],
            'paxDetails' => $data['paxDetails'],
            'remark' => $data['remark'] ?? ''
        ];

        return $this->sendRequest('refund_quote', $payload, 'Refund Quote');
    }

    public function refundTicket(array $data)
    {
        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(),
            'UniqueID' => $data['uniqueId'],
            'paxDetails' => $data['paxDetails'],
            'remark' => $data['remark'] ?? ''
        ];

        // Doc says reissue_ticket but likely refund_ticket based on context
        return $this->sendRequest('refund_ticket', $payload, 'Refund Ticket');
    }

    public function reissueQuote(array $data)
    {
        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(),
            'UniqueID' => $data['uniqueId'],
            'paxDetails' => $data['paxDetails'],
            'OriginDestinationInfo' => $this->formatItinerary($data['OriginDestinationInfo'])
        ];

        return $this->sendRequest('reissue_ticket_quote', $payload, 'Reissue Quote');
    }

    public function reissueTicket(array $data)
    {
        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(),
            'UniqueID' => $data['uniqueId'],
            'ptrUniqueID' => $data['ptrUniqueID'],
            'PreferenceOption' => $data['PreferenceOption'],
            'remark' => $data['remark'] ?? ''
        ];

        return $this->sendRequest('reissue_ticket', $payload, 'Reissue Ticket');
    }

    public function voidQuote(array $data)
    {
        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(),
            'UniqueID' => $data['uniqueId'],
            'paxDetails' => $data['paxDetails']
        ];

        return $this->sendRequest('void_ticket_quote', $payload, 'Void Quote');
    }

    public function voidTicket(array $data)
    {
        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(),
            'UniqueID' => $data['uniqueId'],
            'paxDetails' => $data['paxDetails'],
            'remark' => $data['remark'] ?? ''
        ];

        return $this->sendRequest('void_ticket', $payload, 'Void Ticket');
    }

    public function searchPostTicketStatus(array $data)
    {
        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(),
            'UniqueID' => $data['uniqueId'],
            'ptrUniqueID' => $data['ptrUniqueID']
        ];

        return $this->sendRequest('search_post_ticket_status', $payload, 'Search Post Ticket Status');
    }

    /**
     * Helper to send request and handle response.
     */
    private function sendRequest($endpoint, $payload, $actionName)
    {
        $url = str_replace('availability', $endpoint, $this->url);
        $startTime = microtime(true);

        Log::info("Travelopro {$actionName} Request", ['payload' => $payload]);

        try {
            $response = Http::timeout(60)->post($url, $payload);

            $this->logApiCall($actionName, $url, $payload, $response->json(), $response->status(), $startTime);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Travelopro {$actionName} Error", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'status' => 'error',
                'message' => "Failed to perform {$actionName}",
                'details' => $response->json()
            ];
        } catch (\Exception $e) {
            $this->logApiCall($actionName, $url, $payload, ['error' => $e->getMessage()], 500, $startTime);
            Log::error("Travelopro {$actionName} Exception", ['message' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Service unavailable',
                'error' => $e->getMessage()
            ];
        }
    }
}
