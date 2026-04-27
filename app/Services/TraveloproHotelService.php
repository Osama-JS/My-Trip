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
            'city_id' => $data['cityCode'] ?? null,
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
        $payload['occupancy'] = [];
        if (isset($data['distribution_mode']) && $data['distribution_mode'] === 'manual' && isset($data['occupancy'])) {
            foreach ($data['occupancy'] as $index => $roomData) {
                $payload['occupancy'][] = [
                    'room_no' => $index + 1,
                    'adult' => (int) ($roomData['adult'] ?? 1),
                    'child' => (int) ($roomData['child'] ?? 0),
                    'child_age' => (isset($roomData['child_age']) && !empty($roomData['child_age'])) ? $roomData['child_age'] : [0]
                ];
            }
        } else {
            $rooms = (int) ($data['rooms'] ?? 1);
            $adults = (int) ($data['adults'] ?? 1);
            $childs = (int) ($data['childs'] ?? 0);
            $childAge = $data['childAge'] ?? [];

            $remainingAdults = $adults;
            $remainingChilds = $childs;
            $ageIndex = 0;

            for ($i = 1; $i <= $rooms; $i++) {
                // Distribute adults
                $roomAdults = ceil($remainingAdults / ($rooms - $i + 1));
                $remainingAdults -= $roomAdults;

                // Distribute children
                $roomChilds = ceil($remainingChilds / ($rooms - $i + 1));
                $remainingChilds -= $roomChilds;

                // Distribute child ages
                $roomAges = [];
                for ($j = 0; $j < $roomChilds; $j++) {
                    $roomAges[] = $childAge[$ageIndex++] ?? 0;
                }

                $payload['occupancy'][] = [
                    'room_no' => $i,
                    'adult' => (int) $roomAdults,
                    'child' => (int) $roomChilds,
                    'child_age' => !empty($roomAges) ? $roomAges : [0]
                ];
            }
        }

        $response = $this->sendRequest('hotel_search', $payload, 'Hotel Search');
        
        $hotelCount = 0;
        if (isset($response['hotels'])) $hotelCount = count($response['hotels']);
        elseif (isset($response['HotelResults'])) $hotelCount = count($response['HotelResults']);
        elseif (isset($response['HotelList'])) $hotelCount = count($response['HotelList']);

        Log::info("Hotel Search Response received. Count: {$hotelCount}");
        
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
                    'minPrice'    => $this->applyHotelMargin($hotel['total'] ?? $hotel['minPrice'] ?? 0),
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
        $response = $this->sendRequest('get_room_rates', $data, 'Get Room Rates');
        
        // Apply Margin
        if (!empty($response['roomRates']['perBookingRates'])) {
            foreach ($response['roomRates']['perBookingRates'] as &$room) {
                if (isset($room['netPrice'])) {
                    $room['netPrice'] = $this->applyHotelMargin($room['netPrice']);
                }
            }
        } elseif (!empty($response['roomRates']['RoomResults'])) {
             foreach ($response['roomRates']['RoomResults'] as &$room) {
                if (isset($room['net_price'])) {
                    $room['net_price'] = $this->applyHotelMargin($room['net_price']);
                }
            }
        }

        return $response;
    }

    /**
     * Check Room Rates (Revalidate).
     */
    public function checkRoomRates(array $data)
    {
        $locale = app()->getLocale();
        $data['requiredLanguage'] = ($locale === 'ar') ? 'ARA' : 'ENG';

        // Requires rateBasisId, sessionId, productId, tokenId
        $response = $this->sendRequest('get_rate_rules', $data, 'Check Room Rates');
        
        // Apply Margin to revalidated price
        if (isset($response['rateBasis']['TotalFare']['Amount'])) {
            $response['rateBasis']['TotalFare']['Amount'] = $this->applyHotelMargin($response['rateBasis']['TotalFare']['Amount']);
        }
        
        return $response;
    }

    /**
     * Helper to apply hotel profit margin.
     * Supports two modes: 'percentage' (default) or 'fixed' (flat SAR amount).
     */
    private function applyHotelMargin($price)
    {
        $margin     = floatval(\App\Models\Setting::get('hotel_margin', 0));
        $marginType = \App\Models\Setting::get('hotel_margin_type', 'percentage');

        if ($margin <= 0) return $price;

        $basePrice = floatval($price);

        if ($marginType === 'fixed') {
            $newPrice = $basePrice + $margin;
        } else {
            // percentage (default)
            $newPrice = $basePrice * (1 + ($margin / 100));
        }

        return number_format($newPrice, 2, '.', '');
    }

    /**
     * Create Hotel Booking.
     *
     * Transforms our internal paxDetails structure:
     *   paxDetails[]{room_no, pax[]{type, Title, FirstName, LastName, Age}}
     *
     * Into the official Travelopro format:
     *   paxDetails[]{room_no, adult:{title:[], firstName:[], lastName:[]}, child:{title:[], firstName:[], lastName:[]}}
     */
    public function book(array $data)
    {
        $payload = $data;

        // Transform paxDetails to match Travelopro's official spec
        if (!empty($data['paxDetails']) && is_array($data['paxDetails'])) {
            $transformedPax = [];

            foreach ($data['paxDetails'] as $room) {
                $roomNo = $room['room_no'] ?? 1;
                $transformed = ['room_no' => $roomNo];

                $adultTitles     = [];
                $adultFirstNames = [];
                $adultLastNames  = [];
                $childTitles     = [];
                $childFirstNames = [];
                $childLastNames  = [];

                // Handle our internal flat pax[] structure
                if (!empty($room['pax']) && is_array($room['pax'])) {
                    foreach ($room['pax'] as $pax) {
                        $type      = strtoupper($pax['type'] ?? 'AD');
                        $title     = $pax['Title']     ?? $pax['title']     ?? 'Mr';
                        $firstName = $pax['FirstName'] ?? $pax['firstName'] ?? '';
                        $lastName  = $pax['LastName']  ?? $pax['lastName']  ?? '';

                        if ($type === 'CH') {
                            $childTitles[]     = $title;
                            $childFirstNames[] = $firstName;
                            $childLastNames[]  = $lastName;
                        } else {
                            $adultTitles[]     = $title;
                            $adultFirstNames[] = $firstName;
                            $adultLastNames[]  = $lastName;
                        }
                    }
                }

                // Handle legacy adult/child object structure (already in Travelopro format)
                if (!empty($room['adult']) && is_array($room['adult'])) {
                    $adultTitles     = $room['adult']['title']     ?? [];
                    $adultFirstNames = $room['adult']['firstName'] ?? [];
                    $adultLastNames  = $room['adult']['lastName']  ?? [];
                }
                if (!empty($room['child']) && is_array($room['child'])) {
                    $childTitles     = $room['child']['title']     ?? [];
                    $childFirstNames = $room['child']['firstName'] ?? [];
                    $childLastNames  = $room['child']['lastName']  ?? [];
                }

                // Build the official Travelopro paxDetails structure
                if (!empty($adultFirstNames)) {
                    $transformed['adult'] = [
                        'title'     => $adultTitles,
                        'firstName' => $adultFirstNames,
                        'lastName'  => $adultLastNames,
                    ];
                }

                if (!empty($childFirstNames)) {
                    $transformed['child'] = [
                        'title'     => $childTitles,
                        'firstName' => $childFirstNames,
                        'lastName'  => $childLastNames,
                    ];
                }

                $transformedPax[] = $transformed;
            }

            $payload['paxDetails'] = $transformedPax;
        }

        Log::info('Travelopro Hotel Book - Final paxDetails payload', ['paxDetails' => $payload['paxDetails'] ?? []]);

        return $this->sendRequest('hotel_book', $payload, 'Hotel Booking');
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
        $batchSize = 100;
        $from = $startFrom;
        $totalSynced = 0;
        $retryLimit = 3;
        $syncedCityCodes = [];

        // --- FIRST PASS: English Names ---
        Log::info("Starting Hotel Cities sync: English Pass from index {$from}");
        
        while (true) {
            $to = $from + $batchSize - 1;
            $cities = [];
            $attempts = 0;

            while ($attempts < $retryLimit) {
                try {
                    $response = $this->getCities(['from' => $from, 'to' => $to, 'requiredLanguage' => 'ENG']);
                    $cities = $response['cities'] ?? $response['Cities'] ?? [];
                    if (!empty($cities)) break;
                    
                    // If response is successful but empty, it might be the end
                    if (isset($response['cities']) || isset($response['Cities'])) {
                        break; 
                    }
                } catch (\Exception $e) {
                    $attempts++;
                    Log::warning("Batch sync (EN) attempt {$attempts} failed for range {$from}-{$to}: " . $e->getMessage());
                    sleep(2);
                }
            }

            if (empty($cities)) {
                Log::info("No more cities found at index {$from}. Ending English pass.");
                break;
            }

            $data = [];
            foreach ($cities as $city) {
                $cityCode = $city['id'] ?? $city['CityCode'] ?? null;
                $cityName = $city['city_name'] ?? $city['CityName'] ?? null;
                $countryName = $city['country_name'] ?? $city['CountryName'] ?? null;
                $countryCode = $city['country_code'] ?? $city['CountryCode'] ?? null;
                $latitude = $city['latitude'] ?? $city['Latitude'] ?? null;
                $longitude = $city['longitude'] ?? $city['Longitude'] ?? null;

                if (!$cityCode || !$cityName) continue;

                $data[] = [
                    'city_code' => $cityCode,
                    'city_name_en' => $cityName,
                    'country_code' => $countryCode,
                    'country_name_en' => $countryName,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $syncedCityCodes[] = $cityCode;
            }

            if (!empty($data)) {
                // Use city_code as the unique key for upserting
                \App\Models\HotelCity::upsert($data, ['city_code'], ['city_name_en', 'country_name_en', 'country_code', 'latitude', 'longitude', 'updated_at']);
                $totalSynced += count($data);
                Log::info("Synced batch {$from}-{$to}. Total EN synced: {$totalSynced}");
            }

            if (count($cities) < $batchSize) break;
            $from += $batchSize;
        }

        // --- SECOND PASS: Arabic Names (For all synced codes) ---
        if (!empty($syncedCityCodes)) {
            Log::info("Starting Hotel Cities sync: Arabic Pass for " . count($syncedCityCodes) . " cities.");
            $fromAr = $startFrom;
            while ($fromAr < ($startFrom + count($syncedCityCodes))) {
                $toAr = $fromAr + $batchSize - 1;
                $citiesAr = [];
                $attempts = 0;

                while ($attempts < $retryLimit) {
                    try {
                        $response = $this->getCities(['from' => $fromAr, 'to' => $toAr, 'requiredLanguage' => 'ARA']);
                        $citiesAr = $response['cities'] ?? $response['Cities'] ?? [];
                        break;
                    } catch (\Exception $e) {
                        $attempts++;
                        Log::warning("Batch sync (AR) attempt {$attempts} failed for range {$fromAr}-{$toAr}");
                        sleep(2);
                    }
                }

                if (empty($citiesAr)) break;

                foreach ($citiesAr as $city) {
                    $id = $city['id'] ?? $city['CityCode'] ?? null;
                    $cityNameAr = $city['city_name'] ?? $city['CityName'] ?? null;
                    $countryNameAr = $city['country_name'] ?? $city['CountryName'] ?? null;

                    if ($id && $cityNameAr) {
                        \App\Models\HotelCity::where('city_code', $id)->update([
                            'city_name_ar' => $cityNameAr,
                            'country_name_ar' => $countryNameAr
                        ]);
                    }
                }
                
                Log::info("Arabic names updated for batch {$fromAr}-{$toAr}");
                if (count($citiesAr) < $batchSize) break;
                $fromAr += $batchSize;
            }
        }

        // --- FALLBACK: Standard prominent cities (Optional, but using real IDs if possible) ---
        // Note: In a real environment, you'd want these IDs to match the supplier's actual IDs.
        $prominentCities = [
            ['city_code' => '13217', 'city_name_en' => 'Makkah', 'city_name_ar' => 'مكة المكرمة', 'country_code' => 'SA', 'country_name_en' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['city_code' => '13191', 'city_name_en' => 'Madinah', 'city_name_ar' => 'المدينة المنورة', 'country_code' => 'SA', 'country_name_en' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['city_code' => '13233', 'city_name_en' => 'Riyadh', 'city_name_ar' => 'الرياض', 'country_code' => 'SA', 'country_name_en' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['city_code' => '13184', 'city_name_en' => 'Jeddah', 'city_name_ar' => 'جدة', 'country_code' => 'SA', 'country_name_en' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['city_code' => '13158', 'city_name_en' => 'Dammam', 'city_name_ar' => 'الدمام', 'country_code' => 'SA', 'country_name_en' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['city_code' => '1792',  'city_name_en' => 'Dubai', 'city_name_ar' => 'دبي', 'country_code' => 'AE', 'country_name_en' => 'United Arab Emirates', 'country_name_ar' => 'الإمارات العربية المتحدة'],
        ];

        foreach ($prominentCities as $city) {
            \App\Models\HotelCity::updateOrCreate(['city_code' => $city['city_code']], array_merge($city, ['is_active' => true]));
        }

        return [
            'status' => 'success',
            'count' => max($totalSynced, count($prominentCities)),
            'message' => $totalSynced > 0 ? "Full sync complete. Synced {$totalSynced} cities." : "API Timeout or no cities found. Fallback major cities seeded."
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
