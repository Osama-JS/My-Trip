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
            set_time_limit(0); 
            ini_set('memory_limit', '1G');
            if ($method === 'GET') {
                $payload = array_merge($authData, $data);
                $response = Http::withoutVerifying()->connectTimeout(60)->timeout(120)->get($url, $payload);
            } else {
                $payload = array_merge($authData, $data);
                $response = Http::withoutVerifying()->connectTimeout(60)->timeout(120)->post($url, $payload);
            }

            Log::info("Travelopro Hotel {$actionName} Response received.", ['status' => $response->status()]);

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
        $locale = app()->getLocale();
        $langCode = ($locale === 'ar') ? 'ARA' : 'ENG';

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
            'requiredLanguage' => $langCode,
            'nationality' => $data['residentNationality'] ?? 'SA',
        ];

        // Format occupancy
        $rooms = (int) ($data['rooms'] ?? 1);
        $adults = (int) ($data['adults'] ?? 1);
        $childs = (int) ($data['childs'] ?? 0);
        $childAge = $data['childAge'] ?? [];

        $payload['occupancy'] = [];
        $remainingAdults = $adults;
        $remainingChilds = $childs;

        for ($i = 1; $i <= $rooms; $i++) {
            // Distribute adults
            $roomAdults = ceil($remainingAdults / ($rooms - $i + 1));
            $remainingAdults -= $roomAdults;

            // Distribute children
            $roomChilds = ceil($remainingChilds / ($rooms - $i + 1));
            $remainingChilds -= $roomChilds;

            $payload['occupancy'][] = [
                'room_no' => $i,
                'adult' => (int) $roomAdults,
                'child' => (int) $roomChilds,
                'child_age' => !empty($childAge) ? $childAge : [0]
            ];
        }

        $response = $this->sendRequest('hotel_search', $payload, 'Hotel Search');
        
        // The API 'status' field may be an object containing sessionId
        $statusField = $response['status'] ?? null;
        Log::info('Hotel Search - Full status field', ['status' => $statusField]);
        Log::info('Hotel Search - All top-level response keys', ['keys' => array_keys($response ?? [])]);
        
        // Extract sessionId from status object
        $sessionId = null;
        if (is_array($statusField)) {
            $sessionId = $statusField['sessionId'] ?? $statusField['session_id'] ?? null;
            Log::info('Hotel Search - Extracted sessionId from status', ['sessionId' => $sessionId]);
        }
        
        if (!empty($response)) {
            $rawKeyCheck = array_intersect_key($response, array_flip(['itineraries', 'hotels', 'HotelResults', 'HotelList']));
            if (!empty($rawKeyCheck)) {
                $firstKey = array_key_first($rawKeyCheck);
                $firstItem = $response[$firstKey][0] ?? null;
                Log::info('Hotel Search - First hotel item keys', ['keys' => array_keys($firstItem ?? [])]);
            }
        }

        $normalized = $this->normalizeHotelResults($response);
        
        // Store hotel list in session — include sessionId in every hotel entry
        if (!empty($normalized['hotels'])) {
            $hotelMap = [];
            foreach ($normalized['hotels'] as $h) {
                if (!empty($h['hotelId'])) {
                    // Attach sessionId so the details page can use it for room rates
                    $h['sessionId'] = $sessionId ?? $h['sessionId'] ?? null;
                    $hotelMap[$h['hotelId']] = $h;
                }
            }
            session(['hotel_search_results' => $hotelMap]);
            session(['hotel_search_session_id' => $sessionId]); // also store at top level
        }

        return $normalized;
    }

    /**
     * Normalize hotel API results for consistent frontend display.
     */
    private function normalizeHotelResults($response)
    {
        if (!$response || isset($response['status']) && $response['status'] === 'error') {
            return $response;
        }

        // Log all top-level keys for debugging
        Log::info('normalizeHotelResults - top-level keys', ['keys' => array_keys($response ?? [])]);
        
        // Preserve critical session fields BEFORE any unset()
        $sessionId  = $response['sessionId']  ?? $response['session_id']  ?? null;
        $tokenId    = $response['tokenId']    ?? $response['token_id']    ?? null;
        $moreResults = $response['moreResults'] ?? false;
        $nextToken   = $response['nextToken']   ?? null;
        $currency    = $response['requiredCurrency'] ?? $response['currency'] ?? 'SAR';

        $rawKey = isset($response['itineraries']) ? 'itineraries' : (isset($response['hotels']) ? 'hotels' : (isset($response['HotelResults']) ? 'HotelResults' : (isset($response['HotelList']) ? 'HotelList' : null)));
        $rawHotels = $rawKey ? $response[$rawKey] : [];

        if (!empty($rawHotels) && is_array($rawHotels)) {
            // Log one hotel item's keys to understand the structure
            Log::info('normalizeHotelResults - sample hotel keys', ['keys' => array_keys($rawHotels[0] ?? [])]);
            
            // Also capture sessionId from a hotel item if not found at top level
            if (!$sessionId) {
                $sessionId = $rawHotels[0]['sessionId'] ?? $rawHotels[0]['session_id'] ?? null;
            }
            if (!$tokenId) {
                $tokenId = $rawHotels[0]['tokenId'] ?? $rawHotels[0]['token_id'] ?? null;
            }

            $normalizedHotels = [];
            foreach ($rawHotels as $hotel) {
                // Limit facilities to save memory and space
                $facilities = $hotel['facilities'] ?? [];
                if (count($facilities) > 10) {
                    $facilities = array_slice($facilities, 0, 10);
                }

                // Per-hotel sessionId/tokenId (some APIs embed them per item)
                $hSessionId = $hotel['sessionId'] ?? $hotel['session_id'] ?? $sessionId;
                $hTokenId   = $hotel['tokenId']   ?? $hotel['token_id']   ?? $tokenId;

                $normalizedHotels[] = [
                    'hotelId'     => $hotel['hotelId']     ?? null,
                    'productId'   => $hotel['productId']   ?? null,
                    'name'        => $hotel['hotelName']   ?? $hotel['name']   ?? 'Unknown Hotel',
                    'hotelRating' => (int) ($hotel['hotelRating'] ?? 0),
                    'address'     => $hotel['address']     ?? null,
                    'city'        => $hotel['city']        ?? null,
                    'minPrice'    => $hotel['total']       ?? $hotel['minPrice'] ?? 0,
                    'hotelImages' => !empty($hotel['thumbNailUrl']) ? [['url' => $hotel['thumbNailUrl']]] : [],
                    'facilities'  => $facilities,
                    'latitude'    => $hotel['latitude']    ?? null,
                    'longitude'   => $hotel['longitude']   ?? null,
                    'perNightArray' => $hotel['perNightArray'] ?? [],
                    'sessionId'   => $hSessionId,
                    'tokenId'     => $hTokenId,
                ];
            }

            // Clean up raw data to free memory
            if ($rawKey) unset($response[$rawKey]);
            
            $response['hotels']      = $normalizedHotels;
            $response['HotelResults'] = $normalizedHotels;
        }

        // Always ensure session keys are on the top-level response
        $response['sessionId']   = $sessionId;
        $response['tokenId']     = $tokenId;
        $response['moreResults'] = $moreResults;
        $response['nextToken']   = $nextToken;
        $response['currency']    = $currency;

        return $response;
    }

    /**
     * Get more hotels (Pagination).
     */
    public function nextToken(array $data)
    {
        // Requires sessionId and nextToken
        $response = $this->sendRequest('moreResultsPagination', $data, 'Hotel Pagination', 'GET');
        return $this->normalizeHotelResults($response);
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
        $locale = app()->getLocale();
        $data['requiredLanguage'] = ($locale === 'ar') ? 'ARA' : 'ENG';
        
        // Requires hotelId, sessionId, productId, tokenId
        $result = $this->sendRequest('hotelDetails', $data, 'Get Hotel Content', 'GET');
        
        // Log all top-level keys so we can map the response correctly
        Log::info('Hotel Details - All top-level response keys', ['keys' => array_keys($result ?? [])]);
        if (!empty($result['hotelDetails'])) {
            Log::info('Hotel Details - hotelDetails keys', ['keys' => array_keys($result['hotelDetails'])]);
        } elseif (!empty($result['hotel'])) {
            Log::info('Hotel Details - hotel keys', ['keys' => array_keys($result['hotel'])]);
        }
        
        return $result;
    }

    /**
     * Get Room Rates.
     */
    public function getRoomRates(array $data)
    {
        $locale = app()->getLocale();
        $data['requiredLanguage'] = ($locale === 'ar') ? 'ARA' : 'ENG';

        // Requires hotelId, sessionId, productId, tokenId
        return $this->sendRequest('get_room_rates', $data, 'Get Room Rates');
    }

    /**
     * Check Room Rates (Revalidate).
     */
    public function checkRoomRates(array $data)
    {
        $locale = app()->getLocale();
        $data['requiredLanguage'] = ($locale === 'ar') ? 'ARA' : 'ENG';

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
     * Get Hotel Cancellation Charge.
     */
    public function getCancelCharge(array $data)
    {
        // Requires supplierConfirmationNum, referenceNum, sessionId, productId, tokenId
        return $this->sendRequest('hotel_cancel_charge', $data, 'Get Cancellation Charge');
    }

    /**
     * Cancel Hotel Booking.
     */
    public function cancel(array $data)
    {
        return $this->sendRequest('cancel', $data, 'Cancel Booking');
    }

    /**
     * Sync cities to local database.
     */
    public function syncCities($startFrom = 1)
    {
        // We fetch in smaller batches to avoid timeouts.
        $batchSize = 100;
        $from = $startFrom;
        $totalSynced = 0;
        $maxCities = 10000; // Let's sync more cities for a start
        $retryLimit = 3;
        
        while ($from < $maxCities) {
            $to = $from + $batchSize - 1;
            
            $attempts = 0;
            $cities = [];
            while ($attempts < $retryLimit) {
                try {
                    $response = $this->getCities(['from' => $from, 'to' => $to]);
                    $cities = $response['cities'] ?? $response['Cities'] ?? [];
                    break; // Success
                } catch (\Exception $e) {
                    $attempts++;
                    Log::warning("Batch sync attempt {$attempts} failed for range {$from}-{$to}: " . $e->getMessage());
                    sleep(2); // Wait before retry
                }
            }

            if (empty($cities)) break;

            $data = [];
            foreach ($cities as $city) {
                $cityCode = $city['id'] ?? $city['CityCode'] ?? null;
                $cityName = $city['city_name'] ?? $city['CityName'] ?? null;
                $countryName = $city['country_name'] ?? $city['CountryName'] ?? null;
                $countryCode = $city['country_code'] ?? $city['CountryCode'] ?? null;

                if (!$cityCode || !$cityName) continue;

                $data[] = [
                    'city_code' => $cityCode,
                    'city_name_en' => $cityName,
                    'country_code' => $countryCode,
                    'country_name_en' => $countryName,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($data)) {
                \App\Models\HotelCity::upsert($data, ['city_name_en', 'country_name_en'], ['city_code', 'country_code', 'updated_at']);
                $totalSynced += count($data);
                Log::info("Synced batch {$from}-{$to}. Current total: " . \App\Models\HotelCity::count());
            }

            if (count($cities) < $batchSize) break;
            $from += $batchSize;
        }

        // Second Pass: Try to get Arabic names (Only for top results)
        $from = $startFrom;
        $maxArabic = min($totalSynced, 2000); 
        while ($from < $maxArabic) {
            $to = $from + $batchSize - 1;
            try {
                $response = $this->getCities(['from' => $from, 'to' => $to, 'requiredLanguage' => 'ARA']);
                $cities = $response['cities'] ?? $response['Cities'] ?? [];
                if (empty($cities)) break;

                foreach ($cities as $city) {
                    $id = $city['id'] ?? null;
                    $cityNameAr = $city['city_name'] ?? $city['CityName'] ?? null;
                    $countryNameAr = $city['country_name'] ?? $city['CountryName'] ?? null;

                    if ($id && $cityNameAr) {
                        \App\Models\HotelCity::where('city_code', $id)->update([
                            'city_name_ar' => $cityNameAr,
                            'country_name_ar' => $countryNameAr
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Arabic sync pass failed for range {$from}-{$to}");
            }

            if (count($cities) < $batchSize) break;
            $from += $batchSize;
        }

        // --- FALLBACK: Sync prominent cities with Arabic names if API fails/times out ---
        $prominentCities = [
            ['city_code' => 'SA_1', 'city_name_en' => 'Makkah', 'city_name_ar' => 'مكة المكرمة', 'country_code' => 'SA', 'country_name_en' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['city_code' => 'SA_2', 'city_name_en' => 'Madinah', 'city_name_ar' => 'المدينة المنورة', 'country_code' => 'SA', 'country_name_en' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['city_code' => 'SA_3', 'city_name_en' => 'Riyadh', 'city_name_ar' => 'الرياض', 'country_code' => 'SA', 'country_name_en' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['city_code' => 'SA_4', 'city_name_en' => 'Jeddah', 'city_name_ar' => 'جدة', 'country_code' => 'SA', 'country_name_en' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['city_code' => 'SA_5', 'city_name_en' => 'Dammam', 'city_name_ar' => 'الدمام', 'country_code' => 'SA', 'country_name_en' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['city_code' => 'AE_1', 'city_name_en' => 'Dubai', 'city_name_ar' => 'دبي', 'country_code' => 'AE', 'country_name_en' => 'United Arab Emirates', 'country_name_ar' => 'الإمارات العربية المتحدة'],
            ['city_code' => 'AE_2', 'city_name_en' => 'Abu Dhabi', 'city_name_ar' => 'أبو ظبي', 'country_code' => 'AE', 'country_name_en' => 'United Arab Emirates', 'country_name_ar' => 'الإمارات العربية المتحدة'],
            ['city_code' => 'EG_1', 'city_name_en' => 'Cairo', 'city_name_ar' => 'القاهرة', 'country_code' => 'EG', 'country_name_en' => 'Egypt', 'country_name_ar' => 'مصر'],
            ['city_code' => 'EG_2', 'city_name_en' => 'Alexandria', 'city_name_ar' => 'الإسكندرية', 'country_code' => 'EG', 'country_name_en' => 'Egypt', 'country_name_ar' => 'مصر'],
            ['city_code' => 'TR_1', 'city_name_en' => 'Istanbul', 'city_name_ar' => 'اسطنبول', 'country_code' => 'TR', 'country_name_en' => 'Turkey', 'country_name_ar' => 'تركيا'],
            ['city_code' => 'FR_1', 'city_name_en' => 'Paris', 'city_name_ar' => 'باريس', 'country_code' => 'FR', 'country_name_en' => 'France', 'country_name_ar' => 'فرنسا'],
            ['city_code' => 'GB_1', 'city_name_en' => 'London', 'city_name_ar' => 'لندن', 'country_code' => 'GB', 'country_name_en' => 'United Kingdom', 'country_name_ar' => 'المملكة المتحدة'],
            ['city_code' => 'JO_1', 'city_name_en' => 'Amman', 'city_name_ar' => 'عمان', 'country_code' => 'JO', 'country_name_en' => 'Jordan', 'country_name_ar' => 'الأردن'],
            ['city_code' => 'KW_1', 'city_name_en' => 'Kuwait City', 'city_name_ar' => 'الكويت', 'country_code' => 'KW', 'country_name_en' => 'Kuwait', 'country_name_ar' => 'الكويت'],
            ['city_code' => 'QA_1', 'city_name_en' => 'Doha', 'city_name_ar' => 'الدوحة', 'country_code' => 'QA', 'country_name_en' => 'Qatar', 'country_name_ar' => 'قطر'],
        ];

        foreach ($prominentCities as $city) {
            \App\Models\HotelCity::updateOrCreate(
                ['city_code' => $city['city_code']],
                array_merge($city, ['is_active' => true])
            );
        }

        return [
            'status' => 'success',
            'count' => max($totalSynced, count($prominentCities)),
            'message' => $totalSynced > 0 
                ? "Full sync complete. Synced {$totalSynced} cities." 
                : "API Timeout. Fallback major cities seeded with Arabic names."
        ];
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
