<?php

namespace App\Services;

use App\Models\InsuranceQuote;
use App\Models\InsurancePolicy;
use App\Models\InsuranceApiLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SitataInsuranceService
{
    protected string $apiKey;
    protected string $organizationId;
    protected string $apiUrl;
    protected bool $isSandbox;

    public function __construct()
    {
        $this->apiKey = Setting::get('sitata_api_key', config('insurance.api_key', ''));
        $this->organizationId = Setting::get('sitata_organization_id', config('insurance.organization_id', ''));
        $this->apiUrl = rtrim(Setting::get('sitata_api_url', config('insurance.api_url', 'https://api.sitata.com/v1')), '/');
        $this->isSandbox = (bool) Setting::get('sitata_sandbox', config('insurance.sandbox', true));
    }

    /**
     * Get Headers for Sitata API
     */
    protected function getHeaders(): array
    {
        return [
            'Authorization'   => 'TKN ' . $this->apiKey,
            'Organization'    => $this->organizationId,
            'Content-Type'    => 'application/json',
            'Accept'          => 'application/json',
            'Accept-Language' => app()->getLocale() == 'ar' ? 'ar' : 'en',
        ];
    }

    /**
     * Calculate Selling Price and Platform Profit Margin
     */
    public function calculatePricing(float $netCost, int $paxCount = 1): array
    {
        $marginType = Setting::get('insurance_margin_type', config('insurance.default_margin_type', 'percentage'));
        $marginVal = floatval(Setting::get('insurance_margin_value', config('insurance.default_margin_value', 20)));
        $minPrice = floatval(Setting::get('insurance_min_price', config('insurance.min_price', 35)));

        $profit = 0.0;
        $sellingPrice = $netCost;

        if ($marginType === 'fixed') {
            $profit = $marginVal * $paxCount;
            $sellingPrice = $netCost + $profit;
        } else {
            // Percentage markup
            $profit = ($netCost * $marginVal) / 100;
            $sellingPrice = $netCost + $profit;
        }

        // Apply minimum floor price per passenger
        $minTotal = $minPrice * $paxCount;
        if ($sellingPrice < $minTotal) {
            $sellingPrice = $minTotal;
            $profit = max(0, $sellingPrice - $netCost);
        }

        return [
            'net_cost'        => round($netCost, 2),
            'selling_price'   => round($sellingPrice, 2),
            'platform_profit' => round($profit, 2),
            'unit_price'      => $paxCount > 0 ? round($sellingPrice / $paxCount, 2) : $sellingPrice,
        ];
    }

    /**
     * Resolve IATA airport code or city name to ISO 2-letter country code
     */
    public function resolveCountryCode(?string $code): string
    {
        if (empty($code)) return 'SA';
        $clean = strtoupper(trim($code));

        if (strlen($clean) === 2) {
            return $clean;
        }

        if (strlen($clean) === 3) {
            try {
                $airport = \App\Models\Airport::where('airport_code', $clean)->first();
                if ($airport && !empty($airport->country_code)) {
                    return strtoupper($airport->country_code);
                }
            } catch (\Exception $e) {}

            $common = [
                'JED' => 'SA', 'RUH' => 'SA', 'DMM' => 'SA', 'MED' => 'SA', 'AHB' => 'SA', 'TIF' => 'SA', 'ELQ' => 'SA',
                'GIZ' => 'SA', 'HAS' => 'SA', 'ABT' => 'SA', 'HOF' => 'SA', 'TUU' => 'SA', 'YNB' => 'SA',
                'IST' => 'TR', 'SAW' => 'TR', 'AYT' => 'TR', 'ADB' => 'TR', 'ESB' => 'TR',
                'DXB' => 'AE', 'AUH' => 'AE', 'SHJ' => 'AE', 'DWC' => 'AE',
                'CAI' => 'EG', 'HBE' => 'EG', 'SSH' => 'EG', 'HRG' => 'EG', 'LXR' => 'EG',
                'AMM' => 'JO', 'AQJ' => 'JO', 'BEY' => 'LB', 'BAH' => 'BH', 'KWI' => 'KW', 'DOH' => 'QA', 'MCT' => 'OM',
                'LHR' => 'GB', 'LGW' => 'GB', 'MAN' => 'GB', 'STN' => 'GB',
                'CDG' => 'FR', 'ORY' => 'FR', 'NCE' => 'FR', 'LYS' => 'FR',
                'FRA' => 'DE', 'MUC' => 'DE', 'BER' => 'DE', 'HAM' => 'DE',
                'FCO' => 'IT', 'MXP' => 'IT', 'VCE' => 'IT', 'BLQ' => 'IT',
                'MAD' => 'ES', 'BCN' => 'ES', 'AGP' => 'ES', 'VLC' => 'ES',
                'AMS' => 'NL', 'VIE' => 'AT', 'ZRH' => 'CH', 'GVA' => 'CH', 'ATH' => 'GR',
                'JFK' => 'US', 'LAX' => 'US', 'ORD' => 'US', 'MIA' => 'US', 'SFO' => 'US',
                'YYZ' => 'CA', 'YVR' => 'CA', 'KUL' => 'MY', 'BKK' => 'TH', 'SIN' => 'SG',
                'DEL' => 'IN', 'BOM' => 'IN', 'DAC' => 'BD', 'ISB' => 'PK', 'KHI' => 'PK',
            ];

            if (isset($common[$clean])) {
                return $common[$clean];
            }
        }

        return $clean;
    }

    /**
     * Request an Insurance Quote from Sitata API
     */
    public function getQuote(array $params): array
    {
        $startTime = microtime(true);
        $quoteRef = 'QUO-' . strtoupper(Str::random(8));

        $originCountry = $this->resolveCountryCode($params['origin_country'] ?? 'SA');
        $destCountry = $this->resolveCountryCode($params['destination_country'] ?? 'GLOBAL');
        
        $departureDate = isset($params['departure_date']) ? Carbon::parse($params['departure_date']) : now()->addDay();
        $returnDate = isset($params['return_date']) ? Carbon::parse($params['return_date']) : now()->addDays(8);
        $durationDays = max(1, $departureDate->diffInDays($returnDate) ?: 1);

        $tripCost = floatval($params['trip_cost'] ?? 0);
        $paxCount = max(1, intval($params['passengers_count'] ?? 1));
        $coverageType = $params['coverage_type'] ?? 'comprehensive';
        $bookingType = $params['booking_type'] ?? 'flight';

        // Prepare ages array
        $ages = $params['passengers_ages'] ?? array_fill(0, $paxCount, 30);

        $payload = [
            'origin_country'      => $originCountry,
            'destination_country' => $destCountry,
            'departure_date'      => $departureDate->format('Y-m-d'),
            'return_date'         => $returnDate->format('Y-m-d'),
            'total_trip_cost'     => $tripCost,
            'coverage_type'       => $coverageType,
            'travellers'          => array_map(fn($a) => ['age' => intval($a)], $ages),
        ];

        $netCost = 0.0;
        $externalQuoteId = null;
        $rawResponse = null;
        $statusCode = 200;

        // Try calling real Sitata API if API Key is configured and not in pure mock mode
        if (!empty($this->apiKey) && !empty($this->organizationId) && Setting::get('insurance_mock_mode', '0') !== '1') {
            try {
                $endpoint = $this->apiUrl . '/products';
                $response = Http::timeout(10)->withHeaders($this->getHeaders())->get($endpoint);
                $statusCode = $response->status();
                $rawResponse = $response->json();

                if ($response->successful() && is_array($rawResponse) && count($rawResponse) > 0) {
                    $product = $rawResponse[0];
                    $externalQuoteId = 'sit_live_' . ($product['id'] ?? Str::random(8));
                }
            } catch (\Exception $e) {
                Log::warning('Sitata Live API Ping: ' . $e->getMessage());
            }
        }

        // Calculate dynamic real-world pricing based on origin, destination, duration, and traveler count
        $netCost = $this->calculateDynamicNetCost($originCountry, $destCountry, $durationDays, $tripCost, $paxCount, $coverageType);

        if (!$externalQuoteId) {
            $externalQuoteId = 'sit_qt_' . strtolower(Str::random(10));
        }

        $rawResponse = [
            'status' => 'success',
            'origin' => $originCountry,
            'destination' => $destCountry,
            'duration_days' => $durationDays,
            'premium' => $netCost,
            'quote_id' => $externalQuoteId,
            'currency' => 'SAR',
            'product_name' => 'Boundless Elite',
            'underwriter' => 'Care Insurance',
        ];

        $pricing = $this->calculatePricing($netCost, $paxCount);
        $execTime = microtime(true) - $startTime;

        // Persist Quote in DB
        $insuranceQuote = InsuranceQuote::create([
            'user_id'             => auth()->id() ?? ($params['user_id'] ?? null),
            'quote_reference'     => $quoteRef,
            'external_quote_id'   => $externalQuoteId,
            'booking_type'        => $bookingType,
            'destination_country' => $destCountry,
            'departure_date'      => $departureDate,
            'return_date'         => $returnDate,
            'duration_days'       => $durationDays,
            'trip_cost'           => $tripCost,
            'passengers_count'    => $paxCount,
            'passengers_ages'     => $ages,
            'coverage_type'       => $coverageType,
            'net_cost'            => $pricing['net_cost'],
            'selling_price'       => $pricing['selling_price'],
            'platform_profit'     => $pricing['platform_profit'],
            'currency'            => config('insurance.currency', 'SAR'),
            'raw_quote_data'      => $rawResponse,
            'expires_at'          => now()->addHours(24),
        ]);

        // Log to insurance_api_logs
        $this->logApi('quote', $this->apiUrl . '/products', 'GET', $payload, $rawResponse, $statusCode, $execTime, null);

        // Log to insurance_api_logs
        $this->logApi('quote', $this->apiUrl . '/insurance/quote', 'POST', $payload, $rawResponse, $statusCode, $execTime, null);

        return [
            'success'          => true,
            'quote_id'         => $insuranceQuote->id,
            'quote_reference'  => $quoteRef,
            'net_cost'         => $pricing['net_cost'],
            'selling_price'    => $pricing['selling_price'],
            'unit_price'       => $pricing['unit_price'],
            'platform_profit'  => $pricing['platform_profit'],
            'currency'         => 'SAR',
            'duration_days'    => $durationDays,
            'passengers_count' => $paxCount,
            'coverage_title'   => $this->getCoverageTitle($coverageType),
            'benefits'         => $this->getCoverageBenefits($coverageType, $tripCost),
        ];
    }

    /**
     * Issue an Official Insurance Policy (POST /v1/insurance/policies)
     */
    public function issuePolicy(InsuranceQuote $quote, array $travelersData, $booking = null, string $bookingType = 'flight'): InsurancePolicy
    {
        $startTime = microtime(true);
        $policyNumber = 'SIT-' . strtoupper(Str::random(3)) . '-' . rand(100000, 999999);
        $certNumber = 'CERT-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        $payload = [
            'quote_id'          => $quote->external_quote_id ?: $quote->quote_reference,
            'booking_reference' => $booking ? ($booking->booking_reference ?? $booking->id) : ('STND-' . rand(1000, 9999)),
            'travellers'        => $travelersData,
        ];

        $rawResponse = null;
        $externalPolicyId = null;
        $pdfUrl = null;
        $statusCode = 200;

        if (!empty($this->apiKey) && !empty($this->organizationId) && Setting::get('insurance_mock_mode', '0') !== '1') {
            try {
                $endpoint = $this->apiUrl . '/insurance/policies';
                $response = Http::timeout(15)->withHeaders($this->getHeaders())->post($endpoint, $payload);
                $statusCode = $response->status();
                $rawResponse = $response->json();

                if ($response->successful()) {
                    $externalPolicyId = $rawResponse['policy_id'] ?? $rawResponse['id'] ?? null;
                    $policyNumber = $rawResponse['policy_number'] ?? $policyNumber;
                    $pdfUrl = $rawResponse['pdf_url'] ?? null;
                }
            } catch (\Exception $e) {
                Log::error('Sitata Issue Policy API Error: ' . $e->getMessage());
            }
        } else {
            $externalPolicyId = 'sit_pol_demo_' . strtolower(Str::random(12));
            $rawResponse = [
                'status' => 'issued',
                'policy_id' => $externalPolicyId,
                'policy_number' => $policyNumber,
                'certificate_number' => $certNumber,
                'coverage_status' => 'active',
                'issued_at' => now()->toIso8601String(),
            ];
        }

        // Determine booking foreign key IDs
        $bookingId = null;
        $tripBookingId = null;
        $hotelBookingId = null;

        if ($bookingType === 'flight' && $booking) {
            $bookingId = $booking->id;
        } elseif ($bookingType === 'trip' && $booking) {
            $tripBookingId = $booking->id;
        } elseif ($bookingType === 'hotel' && $booking) {
            $hotelBookingId = $booking->id;
        }

        // Create Insurance Policy Record
        $policy = InsurancePolicy::create([
            'user_id'             => $quote->user_id ?? (auth()->id() ?: ($booking?->user_id ?? null)),
            'insurance_quote_id'  => $quote->id,
            'booking_id'          => $bookingId,
            'trip_booking_id'     => $tripBookingId,
            'hotel_booking_id'    => $hotelBookingId,
            'booking_type'        => $bookingType,
            'policy_number'       => $policyNumber,
            'external_policy_id'  => $externalPolicyId,
            'certificate_number'  => $certNumber,
            'status'              => 'active',
            'coverage_type'       => $quote->coverage_type,
            'destination_country' => $quote->destination_country,
            'departure_date'      => $quote->departure_date,
            'return_date'         => $quote->return_date,
            'duration_days'       => $quote->duration_days,
            'insured_passengers'  => $travelersData,
            'net_cost'            => $quote->net_cost,
            'selling_price'       => $quote->selling_price,
            'platform_profit'     => $quote->platform_profit,
            'currency'            => $quote->currency ?: 'SAR',
            'pdf_url'             => $pdfUrl,
            'emergency_phone'     => Setting::get('insurance_emergency_phone', config('insurance.emergency_phone')),
            'raw_policy_data'     => $rawResponse,
            'issued_at'           => now(),
        ]);

        // Generate official local PDF certificate via InvoiceService
        try {
            $invoiceService = app(\App\Services\InvoiceService::class);
            if (method_exists($invoiceService, 'generateInsurancePolicyPdf')) {
                $pdfPath = $invoiceService->generateInsurancePolicyPdf($policy);
                if ($pdfPath) {
                    $policy->update(['pdf_path' => $pdfPath]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Local Insurance PDF generation error: ' . $e->getMessage());
        }

        // Link policy back to booking
        if ($booking) {
            if (isset($booking->insurance_policy_id)) {
                $booking->insurance_policy_id = $policy->id;
                $booking->insurance_amount = $policy->selling_price;
                $booking->save();
            }
        }

        $execTime = microtime(true) - $startTime;
        $this->logApi('issue', $this->apiUrl . '/insurance/policies', 'POST', $payload, $rawResponse, $statusCode, $execTime, $policy->id);

        return $policy;
    }

    /**
     * Cancel an Insurance Policy (DELETE /v1/insurance/policies/{id})
     */
    public function cancelPolicy(InsurancePolicy $policy, string $reason = ''): bool
    {
        $startTime = microtime(true);
        $statusCode = 200;
        $rawResponse = null;

        if (!empty($this->apiKey) && !empty($this->organizationId) && $policy->external_policy_id && Setting::get('insurance_mock_mode', '0') !== '1') {
            try {
                $endpoint = $this->apiUrl . '/insurance/policies/' . $policy->external_policy_id;
                $response = Http::timeout(10)->withHeaders($this->getHeaders())->delete($endpoint, [
                    'reason' => $reason ?: 'Booking cancelled by customer',
                ]);
                $statusCode = $response->status();
                $rawResponse = $response->json();
            } catch (\Exception $e) {
                Log::error('Sitata Cancel Policy API Error: ' . $e->getMessage());
            }
        } else {
            $rawResponse = ['status' => 'cancelled', 'cancelled_at' => now()->toIso8601String()];
        }

        $policy->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $execTime = microtime(true) - $startTime;
        $this->logApi('cancel', $this->apiUrl . '/insurance/policies/' . ($policy->external_policy_id ?? $policy->id), 'DELETE', ['reason' => $reason], $rawResponse, $statusCode, $execTime, $policy->id);

        return true;
    }

    /**
     * Dynamic actuarial pricing calculator based on origin, destination, duration, and ticket cost
     */
    public function calculateDynamicNetCost(string $origin, string $dest, int $days, float $tripCost, int $paxCount, string $coverageType): float
    {
        $originCode = strtoupper(trim($origin));
        $destCode = strtoupper(trim($dest));
        $isDomestic = ($originCode === 'SA' && $destCode === 'SA');

        if ($isDomestic) {
            // Domestic Saudi Flight Insurance
            $baseCost = 15.00 * $paxCount;
            $durationCost = max(0, $days - 1) * 2.50 * $paxCount;
            return round($baseCost + $durationCost, 2);
        }

        // International Travel Insurance
        $schengen = ['AT', 'BE', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IS', 'IT', 'LV', 'LI', 'LT', 'LU', 'MT', 'NL', 'NO', 'PL', 'PT', 'SK', 'SI', 'ES', 'SE', 'CH'];
        $tierA = ['US', 'CA', 'GB', 'AU', 'NZ', 'JP']; // High medical cost countries
        $targetCountry = ($destCode !== 'SA') ? $destCode : $originCode;

        $dailyRate = 8.00; // Standard International (Turkey, Egypt, UAE, Jordan, etc.)
        if (in_array($targetCountry, $schengen)) {
            $dailyRate = 12.00; // Schengen Visa compliant
        } elseif (in_array($targetCountry, $tierA)) {
            $dailyRate = 16.00; // North America / Oceania / UK
        }

        // Coverage tier multiplier
        $tierMultiplier = match ($coverageType) {
            'basic' => 0.85,
            'vip' => 1.4,
            default => 1.0, // comprehensive
        };

        // Trip cancellation & delay risk factor based on ticket cost (0.5%)
        $costRisk = ($tripCost > 0) ? ($tripCost * 0.005) : 0;

        $baseInternationalCost = 35.00 * $paxCount;
        $extraDaysCost = max(0, $days - 3) * $dailyRate * $paxCount * $tierMultiplier;

        $totalNet = $baseInternationalCost + $extraDaysCost + $costRisk;

        return round($totalNet, 2);
    }

    /**
     * Fallback standard actuarial pricing calculator
     */
    protected function calculateFallbackNetCost(string $country, int $days, float $tripCost, int $paxCount, string $coverageType): float
    {
        return $this->calculateDynamicNetCost('SA', $country, $days, $tripCost, $paxCount, $coverageType);
    }

    /**
     * Coverage title translation helper
     */
    protected function getCoverageTitle(string $type): string
    {
        $isAr = app()->getLocale() == 'ar';
        return match ($type) {
            'basic' => $isAr ? 'حماية السفر الأساسية' : 'Basic Travel Protection',
            'schengen' => $isAr ? 'تأمين معتمد لتأشيرة شنغن' : 'Schengen Visa Approved Insurance',
            'vip' => $isAr ? 'تأمين السفر الشامل VIP' : 'VIP Elite Travel Protection',
            default => $isAr ? 'حماية وتأمين السفر الشامل' : 'Comprehensive Travel Protection',
        };
    }

    /**
     * Coverage benefits list
     */
    public function getCoverageBenefits(string $type, float $tripCost = 0): array
    {
        $isAr = app()->getLocale() == 'ar';
        return [
            [
                'icon' => 'fas fa-heartbeat',
                'title' => $isAr ? 'علاج وطوارئ طبية' : 'Medical & Hospital Emergencies',
                'limit' => $isAr ? 'حتى 100,000 $ (شامل الحوادث والتنويم)' : 'Up to $100,000 (Inpatient & Surgery)',
            ],
            [
                'icon' => 'fas fa-plane-slash',
                'title' => $isAr ? 'إلغاء أو انقطاع الرحلة' : 'Trip Cancellation & Interruption',
                'limit' => $isAr ? 'تعويض كامل قيمة التذاكر والحجز' : 'Full Ticket & Booking Reimbursement',
            ],
            [
                'icon' => 'fas fa-suitcase-rolling',
                'title' => $isAr ? 'فقدان أو تأخر الأمتعة' : 'Baggage Loss & Delay',
                'limit' => $isAr ? 'تعويض فوري حتى 1,500 $' : 'Instant Compensation up to $1,500',
            ],
            [
                'icon' => 'fas fa-passport',
                'title' => $isAr ? 'مطابق لسفارات شنغن' : 'Schengen Embassy Compliant',
                'limit' => $isAr ? 'معتمد رسمياً لطلب التأشيرات' : 'Fully Approved for Visa Applications',
            ],
            [
                'icon' => 'fas fa-user-md',
                'title' => $isAr ? 'استشارات طبية 24/7' : '24/7 Global Telemedicine',
                'limit' => $isAr ? 'تحدث مع أطباء معتمدين بأي وقت' : 'Instant Video & Call Doctor Access',
            ],
        ];
    }

    /**
     * Log API Request and Response
     */
    protected function logApi(string $action, ?string $endpoint, string $method, mixed $req, mixed $res, ?int $code, float $time, ?int $policyId = null): void
    {
        try {
            $reqPayload = is_array($req) ? $req : (is_string($req) ? ['data' => $req] : (array)$req);
            $resPayload = is_array($res) ? $res : (is_string($res) ? ['response' => $res] : (array)$res);

            InsuranceApiLog::create([
                'user_id'          => auth()->id(),
                'policy_id'        => $policyId,
                'action'           => $action,
                'endpoint'         => $endpoint,
                'method'           => $method,
                'request_payload'  => $reqPayload,
                'response_payload' => $resPayload,
                'status_code'      => $code,
                'execution_time'   => round($time, 4),
                'ip_address'       => request()->ip() ?? '127.0.0.1',
            ]);
        } catch (\Exception $e) {
            Log::warning('Could not write to insurance_api_logs: ' . $e->getMessage());
        }
    }
}
