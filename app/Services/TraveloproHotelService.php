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
        $this->baseUrl = 'https://travelnext.works/api/hotel'; // Base for hotels
    }

    private function logApiCall($action, $url, $payload, $response, $statusCode, $startTime, $bookingId = null)
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
                'method' => 'POST',
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

    private function sendRequest($method, $data, $actionName, $bookingId = null)
    {
        $url = "{$this->baseUrl}/{$method}";

        // Inject Auth if not present (Some endpoints like hotel_search need it)
        $payload = array_merge([
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ], $data);

        $startTime = microtime(true);
        Log::info("Travelopro Hotel {$actionName} Request", ['url' => $url, 'payload' => $payload]);

        try {
            $response = Http::timeout(60)->post($url, $payload);
            $this->logApiCall($actionName, $url, $payload, $response->json(), $response->status(), $startTime, $bookingId);

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
                'details' => $response->json()
            ];
        } catch (\Exception $e) {
            $this->logApiCall($actionName, $url, $payload, ['error' => $e->getMessage()], 500, $startTime, $bookingId);
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
        return $this->sendRequest('search', $data, 'Hotel Search');
    }

    /**
     * Get more hotels (Pagination).
     */
    public function nextToken(array $data)
    {
        // Requires sessionId and nextToken
        return $this->sendRequest('nextToken', $data, 'Hotel Pagination');
    }

    /**
     * Filter Hotels.
     */
    public function filter(array $data)
    {
        return $this->sendRequest('hotel_filter', $data, 'Hotel Filter');
    }

    /**
     * Get Hotel Content.
     */
    public function getHotelContent(array $data)
    {
        // Requires hotelId
        return $this->sendRequest('get_hotel_content', $data, 'Get Hotel Content');
    }

    /**
     * Get Room Rates.
     */
    public function getRoomRates(array $data)
    {
        // Requires hotelId and sessionId
        return $this->sendRequest('get_room_rates', $data, 'Get Room Rates');
    }

    /**
     * Check Room Rates (Revalidate).
     */
    public function checkRoomRates(array $data)
    {
        // Requires rateBasisId and sessionId
        return $this->sendRequest('check_room_rates', $data, 'Check Room Rates');
    }

    /**
     * Create Hotel Booking.
     */
    public function book(array $data)
    {
        return $this->sendRequest('book', $data, 'Hotel Booking');
    }

    /**
     * Get Hotel Booking Details.
     */
    public function getBookingDetails(array $data)
    {
        return $this->sendRequest('hotel_booking_details', $data, 'Hotel Booking Details');
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
        return $this->sendRequest('cities', $data, 'Get Cities');
    }

    /**
     * Get Languages.
     */
    public function getLanguages(array $data = [])
    {
        return $this->sendRequest('languages', $data, 'Get Languages');
    }
}
