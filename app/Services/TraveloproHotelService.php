<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\HotelApiLog;
use Illuminate\Support\Facades\Auth;

class TraveloproHotelService
{
    protected $userId;
    protected $password;
    protected $access;
    protected $baseUrl;

    public function __construct()
    {
        $this->userId = config('services.travelopro.user_id');
        $this->password = config('services.travelopro.password');
        $this->access = config('services.travelopro.access');
        $this->baseUrl = 'https://travelnext.works/api/hotel-api-v6'; // Correct base URL for Hotel v6
    }

    private function logApiCall($action, $url, $payload, $response, $statusCode, $startTime, $bookingId = null, $method = 'POST')
    {
        $executionTime = microtime(true) - $startTime;

        // Mask password
        if (isset($payload['user_password'])) {
            $payload['user_password'] = '***';
        }

        try {
            HotelApiLog::create([
                'user_id' => Auth::id(),
                'booking_id' => $bookingId,
                'action' => $action,
                'endpoint' => $url,
                'method' => $method,
                'request_payload' => $payload,
                'response_payload' => $response,
                'status_code' => $statusCode,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'execution_time' => $executionTime
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log Hotel API call: ' . $e->getMessage());
        }
    }

    private function sendRequest($endpoint, $data, $actionName, $method = 'POST', $bookingId = null)
    {
        $url = "{$this->baseUrl}/{$endpoint}";

        $authData = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ];

        $startTime = microtime(true);
        Log::info("Travelopro Hotel {$actionName} Request", ['url' => $url, 'method' => $method, 'data' => $data]);

        try {
            if ($method === 'GET') {
                $payload = array_merge($authData, $data);
                $response = Http::timeout(60)->get($url, $payload);
            } else {
                $payload = array_merge($authData, $data);
                $response = Http::timeout(60)->post($url, $payload);
            }

            $this->logApiCall($actionName, $url, $payload, $response->json(), $response->status(), $startTime, $bookingId, $method);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Travelopro Hotel {$actionName} Error", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'status' => 'error',
                'message' => "Failed to perform {$actionName}",
                'details' => $response->json() ?? ['raw_body' => $response->body()]
            ];
        } catch (\Exception $e) {
            $this->logApiCall($actionName, $url, $data, ['error' => $e->getMessage()], 500, $startTime, $bookingId, $method);
            Log::error("Travelopro Hotel {$actionName} Exception", ['message' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Service unavailable',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Search Hotels.
     */
    public function search(array $data)
    {
        $payload = [
            'checkin' => $data['checkIn'] ?? null,
            'checkout' => $data['checkOut'] ?? null,
            'city_name' => $data['cityName'] ?? null,
            'country_name' => $data['countryName'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'radius' => $data['radius'] ?? 20,
            'maxResult' => $data['maxResult'] ?? 100,
            'requiredCurrency' => $data['requiredCurrency'] ?? 'SAR',
            'requiredLanguage' => $data['requiredLanguage'] ?? 'ENG',
            'nationality' => $data['residentNationality'] ?? 'SA',
        ];

        // Format occupancy
        $rooms = $data['rooms'] ?? 1;
        $adults = $data['adults'] ?? 1;
        $childs = $data['childs'] ?? 0;
        $childAge = $data['childAge'] ?? [];

        $payload['occupancy'] = [];
        for ($i = 1; $i <= $rooms; $i++) {
            $payload['occupancy'][] = [
                'room_no' => $i,
                'adult' => $adults, // Assuming adults per room for simplicity, or split if multi-room logic is added later
                'child' => $childs,
                'child_age' => !empty($childAge) ? $childAge : [0]
            ];
        }

        return $this->sendRequest('hotel_search', $payload, 'Hotel Search');
    }

    /**
     * Get more hotels (Pagination).
     */
    public function nextToken(array $data)
    {
        // Requires sessionId and nextToken
        return $this->sendRequest('moreResultsPagination', $data, 'Hotel Pagination', 'GET');
    }

    /**
     * Filter Hotels.
     */
    public function filter(array $data)
    {
        $payload = [
            'sessionId' => $data['sessionId'] ?? null,
            'maxResult' => $data['maxResult'] ?? 100,
            'filters' => [
                'price' => [
                    'min' => $data['minPrice'] ?? 0,
                    'max' => $data['maxPrice'] ?? 1000000,
                ],
                'rating' => $data['starRating'] ?? "0,1,2,3,4,5",
                'hotelName' => $data['hotelName'] ?? '',
            ]
        ];

        // Travelopro filterResults also accepts requiredLanguage occasionally in some v6 versions,
        // we include it in case it's supported for dynamic content update.
        if (isset($data['requiredLanguage'])) {
            $payload['requiredLanguage'] = $data['requiredLanguage'];
        }

        return $this->sendRequest('filterResults', $payload, 'Hotel Filter');
    }

    /**
     * Get Hotel Content.
     */
    public function getHotelContent(array $data)
    {
        // Requires hotelId, sessionId, productId, tokenId
        return $this->sendRequest('hotelDetails', $data, 'Get Hotel Content', 'GET');
    }

    /**
     * Get Room Rates.
     */
    public function getRoomRates(array $data)
    {
        // Requires hotelId, sessionId, productId, tokenId
        return $this->sendRequest('get_room_rates', $data, 'Get Room Rates');
    }

    /**
     * Check Room Rates (Revalidate).
     */
    public function checkRoomRates(array $data)
    {
        // Requires rateBasisId, sessionId, productId, tokenId
        return $this->sendRequest('get_rate_rules', $data, 'Check Room Rates');
    }

    /**
     * Create Hotel Booking.
     */
    public function book(array $data)
    {
        return $this->sendRequest('hotel_book', $data, 'Hotel Booking');
    }

    /**
     * Get Hotel Booking Details.
     */
    public function getBookingDetails(array $data)
    {
        return $this->sendRequest('bookingDetails', $data, 'Hotel Booking Details');
    }

    /**
     * Cancel Hotel Booking.
     */
    public function cancel(array $data)
    {
        return $this->sendRequest('cancel', $data, 'Cancel Booking');
    }

    /**
     * Get Cities.
     */
    public function getCities(array $data = [])
    {
        $data = array_merge(['from' => 1, 'to' => 100], $data);
        return $this->sendRequest('cities', $data, 'Get Cities', 'GET');
    }

    /**
     * Get Languages.
     */
    public function getLanguages(array $data = [])
    {
        $data = array_merge(['from' => 1, 'to' => 100], $data);
        return $this->sendRequest('languages', $data, 'Get Languages', 'GET');
    }
}
