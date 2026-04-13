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
    public function getAirportList(array $params = [], $force = false)
    {
        $cacheKey = 'travelopro_airports_' . md5(serialize($params));
        
        if ($force) {
            cache()->forget($cacheKey);
        }

        return cache()->remember($cacheKey, 60 * 24, function () use ($params) {
            $payload = array_merge([
                'user_id' => $this->userId,
                'user_password' => $this->password,
                'access' => $this->access,
                'ip_address' => request()->ip() ?? '127.0.0.1',
            ], $params);

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
        $maxAttempts = 3;
        $syncResult = [
            'status' => 'error',
            'count' => 0,
            'message' => 'No data received from API'
        ];

        // --- FIRST PASS: English Names ---
        $list = [];
        $attempt = 1;
        while ($attempt <= $maxAttempts) {
            try {
                $response = $this->getAirportList([], $force);
                if (isset($response[0]['AirportCode'])) {
                    $list = $response;
                } elseif (isset($response['Airports']['Airport'])) {
                    $list = $response['Airports']['Airport'];
                }
                
                if (!empty($list)) break;
            } catch (\Exception $e) {
                Log::warning("Airport sync (EN) attempt {$attempt} failed: " . $e->getMessage());
            }
            $attempt++;
            sleep(2);
        }

        if (!empty($list)) {
            $data = [];
            foreach ($list as $item) {
                $code = substr($item['AirportCode'] ?? $item['airport_code'] ?? 'UNK', 0, 10);
                $data[] = [
                    'airport_code' => $code,
                    'airport_name' => $item['AirportName'] ?? $item['airport_name'] ?? null,
                    'city_code' => substr($item['CityCode'] ?? $item['City'] ?? $item['city_code'] ?? 'UNK', 0, 10),
                    'city_name' => $item['CityName'] ?? $item['City'] ?? $item['city_name'] ?? null,
                    'country_code' => substr($item['CountryCode'] ?? $item['Country'] ?? $item['country_code'] ?? 'UNK', 0, 10),
                    'country_name' => $item['CountryName'] ?? $item['Country'] ?? $item['country_name'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($data) >= 500) {
                    \App\Models\Airport::upsert($data, ['airport_code'], ['airport_name', 'city_code', 'city_name', 'country_code', 'country_name', 'updated_at']);
                    $data = [];
                }
            }
            if (!empty($data)) {
                \App\Models\Airport::upsert($data, ['airport_code'], ['airport_name', 'city_code', 'city_name', 'country_code', 'country_name', 'updated_at']);
            }
            $syncResult = ['status' => 'success', 'count' => count($list), 'message' => 'Flights synced successfully (EN pass)'];
        }

        // --- SECOND PASS: Arabic Names (Optional/Conditional) ---
        if (!empty($list)) {
            try {
                $responseAr = $this->getAirportList(['requiredLanguage' => 'ARA'], $force);
                $listAr = isset($responseAr[0]['AirportCode']) ? $responseAr : ($responseAr['Airports']['Airport'] ?? []);
                
                if (!empty($listAr)) {
                    $dataAr = [];
                    foreach ($listAr as $item) {
                        $code = substr($item['AirportCode'] ?? $item['airport_code'] ?? 'UNK', 0, 10);
                        $dataAr[] = [
                            'airport_code' => $code,
                            'airport_name_ar' => $item['AirportName'] ?? $item['airport_name'] ?? null,
                            'city_name_ar' => $item['CityName'] ?? $item['City'] ?? $item['city_name'] ?? null,
                            'country_name_ar' => $item['CountryName'] ?? $item['Country'] ?? $item['country_name'] ?? null,
                            'updated_at' => now(),
                        ];

                        if (count($dataAr) >= 500) {
                            \App\Models\Airport::upsert($dataAr, ['airport_code'], ['airport_name_ar', 'city_name_ar', 'country_name_ar', 'updated_at']);
                            $dataAr = [];
                        }
                    }
                    if (!empty($dataAr)) {
                        \App\Models\Airport::upsert($dataAr, ['airport_code'], ['airport_name_ar', 'city_name_ar', 'country_name_ar', 'updated_at']);
                    }
                    $syncResult['message'] = 'Flights synced successfully (EN & AR pass)';
                }
            } catch (\Exception $e) {
                Log::warning("Airport sync (AR pass) failed: " . $e->getMessage());
            }
        }

        // --- FALLBACK / SEEDER: Rich data for all Saudi and major International airports ---
        $fallbackAirports = [
            // KSA Airports
            ['airport_code' => 'JED', 'airport_name' => 'King Abdulaziz International', 'airport_name_ar' => 'مطار الملك عبد العزيز الدولي', 'city_code' => 'JED', 'city_name' => 'Jeddah', 'city_name_ar' => 'جدة', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['airport_code' => 'RUH', 'airport_name' => 'King Khalid International', 'airport_name_ar' => 'مطار الملك خالد الدولي', 'city_code' => 'RUH', 'city_name' => 'Riyadh', 'city_name_ar' => 'الرياض', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['airport_code' => 'DMM', 'airport_name' => 'King Fahd International', 'airport_name_ar' => 'مطار الملك فهد الدولي', 'city_code' => 'DMM', 'city_name' => 'Dammam', 'city_name_ar' => 'الدمام', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['airport_code' => 'MED', 'airport_name' => 'Prince Mohammad bin Abdulaziz', 'airport_name_ar' => 'مطار الأمير محمد بن عبد العزيز', 'city_code' => 'MED', 'city_name' => 'Medina', 'city_name_ar' => 'المدينة المنورة', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['airport_code' => 'AHB', 'airport_name' => 'Abha Airport', 'airport_name_ar' => 'مطار أبها الدولي', 'city_code' => 'Abha', 'city_name' => 'Abha', 'city_name_ar' => 'أبها', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['airport_code' => 'GIZ', 'airport_name' => 'Jizan Regional Airport', 'airport_name_ar' => 'مطار جيزان الإقليمي', 'city_code' => 'Gizan', 'city_name' => 'Gizan', 'city_name_ar' => 'جازان', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['airport_code' => 'ELQ', 'airport_name' => 'Prince Nayef bin Abdulaziz Regional', 'airport_name_ar' => 'مطار الأمير نايف بن عبد العزيز الدولي', 'city_code' => 'Gassim', 'city_name' => 'Gassim', 'city_name_ar' => 'القصيم', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['airport_code' => 'TUU', 'airport_name' => 'Tabuk Regional Airport', 'airport_name_ar' => 'مطار تبوك الإقليمي', 'city_code' => 'Tabuk', 'city_name' => 'Tabuk', 'city_name_ar' => 'تبوك', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['airport_code' => 'HAS', 'airport_name' => 'Ha\'il Regional Airport', 'airport_name_ar' => 'مطار حائل الإقليمي', 'city_code' => 'Hail', 'city_name' => 'Hail', 'city_name_ar' => 'حائل', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['airport_code' => 'TIF', 'airport_name' => 'Taif Regional Airport', 'airport_name_ar' => 'مطار الطائف الإقليمي', 'city_code' => 'Taif', 'city_name' => 'Taif', 'city_name_ar' => 'الطائف', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['airport_code' => 'HOF', 'airport_name' => 'Al-Ahsa International Airport', 'airport_name_ar' => 'مطار الأحساء الدولي', 'city_code' => 'Hofuf', 'city_name' => 'Hofuf', 'city_name_ar' => 'الهفوف', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['airport_code' => 'YNB', 'airport_name' => 'Yanbu Airport', 'airport_name_ar' => 'مطار ينبع الدولي', 'city_code' => 'Yanbu', 'city_name' => 'Yanbu', 'city_name_ar' => 'ينبع', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['airport_code' => 'DWD', 'airport_name' => 'Dawadmi Domestic Airport', 'airport_name_ar' => 'مطار الدوادمي المحلي', 'city_code' => 'Dawadmi', 'city_name' => 'Dawadmi', 'city_name_ar' => 'الدوادمي', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['airport_code' => 'NUM', 'airport_name' => 'Neom Bay Airport', 'airport_name_ar' => 'مطار خليج نيوم', 'city_code' => 'Neom', 'city_name' => 'Neom', 'city_name_ar' => 'نيوم', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            ['airport_code' => 'ULH', 'airport_name' => 'Al-Ula Airport', 'airport_name_ar' => 'مطار العلا', 'city_code' => 'Al-Ula', 'city_name' => 'Al-Ula', 'city_name_ar' => 'العلا', 'country_code' => 'SA', 'country_name' => 'Saudi Arabia', 'country_name_ar' => 'المملكة العربية السعودية'],
            
            // Regional & Global Hubs
            ['airport_code' => 'DXB', 'airport_name' => 'Dubai International', 'airport_name_ar' => 'مطار دبي الدولي', 'city_code' => 'DXB', 'city_name' => 'Dubai', 'city_name_ar' => 'دبي', 'country_code' => 'AE', 'country_name' => 'United Arab Emirates', 'country_name_ar' => 'الإمارات العربية المتحدة'],
            ['airport_code' => 'AUH', 'airport_name' => 'Abu Dhabi International', 'airport_name_ar' => 'مطار أبو ظبي الدولي', 'city_code' => 'AUH', 'city_name' => 'Abu Dhabi', 'city_name_ar' => 'أبو ظبي', 'country_code' => 'AE', 'country_name' => 'United Arab Emirates', 'country_name_ar' => 'الإمارات العربية المتحدة'],
            ['airport_code' => 'CAI', 'airport_name' => 'Cairo International', 'airport_name_ar' => 'مطار القاهرة الدولي', 'city_code' => 'CAI', 'city_name' => 'Cairo', 'city_name_ar' => 'القاهرة', 'country_code' => 'EG', 'country_name' => 'Egypt', 'country_name_ar' => 'مصر'],
            ['airport_code' => 'AMM', 'airport_name' => 'Queen Alia International', 'airport_name_ar' => 'مطار الملكة علياء الدولي', 'city_code' => 'AMM', 'city_name' => 'Amman', 'city_name_ar' => 'عمان', 'country_code' => 'JO', 'country_name' => 'Jordan', 'country_name_ar' => 'الأردن'],
            ['airport_code' => 'IST', 'airport_name' => 'Istanbul Airport', 'airport_name_ar' => 'مطار اسطنبول', 'city_code' => 'IST', 'city_name' => 'Istanbul', 'city_name_ar' => 'اسطنبول', 'country_code' => 'TR', 'country_name' => 'Turkey', 'country_name_ar' => 'تركيا'],
            ['airport_code' => 'LHR', 'airport_name' => 'Heathrow Airport', 'airport_name_ar' => 'مطار هيثرو', 'city_code' => 'LON', 'city_name' => 'London', 'city_name_ar' => 'لندن', 'country_code' => 'GB', 'country_name' => 'United Kingdom', 'country_name_ar' => 'المملكة المتحدة'],
            ['airport_code' => 'CDG', 'airport_name' => 'Charles de Gaulle Airport', 'airport_name_ar' => 'مطار شارل ديغول', 'city_code' => 'PAR', 'city_name' => 'Paris', 'city_name_ar' => 'باريس', 'country_code' => 'FR', 'country_name' => 'France', 'country_name_ar' => 'فرنسا'],
        ];

        \App\Models\Airport::upsert($fallbackAirports, ['airport_code'], ['airport_name_ar', 'city_name_ar', 'country_name_ar']);

        if ($syncResult['status'] === 'error' && !empty($fallbackAirports)) {
            return [
                'status' => 'warning',
                'count' => count($fallbackAirports),
                'message' => 'API timeout. Major airports seeded with Arabic names.'
            ];
        }

        return $syncResult;
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
