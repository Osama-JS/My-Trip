<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HyperPayService
{
    protected $baseUrl;
    protected $accessToken;
    protected $entityIds;
    protected $testMode;
    protected $merchantUrl;
    protected $merchantPhone;

    public function __construct()
    {
        $this->baseUrl       = config('hyperpay.base_url');
        $this->accessToken   = config('hyperpay.access_token');
        $this->entityIds     = config('hyperpay.entity_ids');
        $this->testMode      = (bool) config('hyperpay.test_mode', false);
        $this->merchantUrl   = config('hyperpay.merchant_url');
        $this->merchantPhone = config('hyperpay.merchant_phone');
    }

    /**
     * Get Checkout ID to initialize payment
     *
     * @param float $amount
     * @param string $paymentType (mada, visa_master, apple_pay)
     * @param array $additionalParams
     * @return array|false
     */
    public function prepareCheckout($amount, $paymentType = 'visa_master', $additionalParams = [])
    {
        $entityId = $this->getEntityId($paymentType);

        if (!$entityId) {
            Log::error("HyperPay: Invalid payment type [{$paymentType}] or missing Entity ID.");
            return false;
        }

        $url = $this->baseUrl . 'checkouts';

        // Base parameters
        $params = [
            'entityId'      => $entityId,
            'amount'        => number_format($amount, 2, '.', ''),
            'currency'      => config('hyperpay.currency', 'SAR'),
            'paymentType'   => 'DB',
            'merchant.url'  => $this->merchantUrl ?: config('app.url'),
            'merchant.phone' => $this->merchantPhone ?: \App\Models\Setting::get('contact_phone', '0505741365'),
        ];

        // Add test mode parameters (REQUIRED for test server & 3DS2)
        if ($this->testMode) {
            $params['testMode'] = 'EXTERNAL';
            $params['customParameters[3DS2_enrolled]'] = 'true';
            // integrity parameter is sometimes required for specific test scenarios
            $params['integrity'] = 'true';
        }

        // Merge additional params (merchantTransactionId, billing, customer, etc.)
        $params = array_merge($params, $additionalParams);

        // Extensive Logging: Critical for HyperPay integration verification
        Log::info('HyperPay Outgoing Checkout Request', [
            'timestamp' => now()->toDateTimeString(),
            'url'       => $url,
            'params'    => $this->sanitizeForLog($params),
            'test_mode' => $this->testMode,
        ]);

        $response = Http::withoutVerifying()
            ->timeout(30)
            ->withToken($this->accessToken)
            ->asForm()
            ->post($url, $params);

        if ($response->successful()) {
            $result = $response->json();
            Log::info('HyperPay Checkout Success', [
                'checkout_id' => $result['id'] ?? 'N/A',
                'result_code' => $result['result']['code'] ?? 'N/A',
                'description' => $result['result']['description'] ?? 'N/A'
            ]);
            return $result;
        }

        Log::error("HyperPay Checkout Failed: Status {$response->status()}", [
            'body'   => $response->body(),
            'params' => $this->sanitizeForLog($params)
        ]);

        return false;
    }

    /**
     * Mask sensitive data in logs
     */
    protected function sanitizeForLog(array $params): array
    {
        $sensitiveKeys = ['customer.email', 'billing.postcode'];
        foreach ($sensitiveKeys as $key) {
             if (isset($params[$key])) {
                 $params[$key] = '***MASKED***';
             }
        }
        return $params;
    }

    /**
     * Get Payment Status
     *
     * @param string $checkoutId
     * @param string $paymentType
     * @return array|false
     */
    public function getPaymentStatus($checkoutId, $paymentType = 'visa_master')
    {
        $entityId = $this->getEntityId($paymentType);
        $url = $this->baseUrl . "checkouts/{$checkoutId}/payment";

        $response = Http::withoutVerifying()
            ->timeout(30)
            ->withToken($this->accessToken)
            ->get($url, [
                'entityId' => $entityId
            ]);

        if ($response->successful()) {
            $data = $response->json();
            Log::info("HyperPay Payment Status Result", [
                'checkout_id' => $checkoutId,
                'result_code' => $data['result']['code'] ?? 'N/A',
                'description' => $data['result']['description'] ?? 'N/A',
                'full_response' => $data
            ]);
            return $data;
        }

        Log::error("HyperPay Get Status Failed: " . $response->body());
        return false;
    }

    /**
     * Verify if the payment result code indicates success
     *
     * @param string $resultCode
     * @return bool
     */
    public function isSuccessful($resultCode)
    {
        return (bool) preg_match('/^(000\.000\.|000\.100\.1|000\.[36])/', $resultCode);
    }

    /**
     * Build billing & customer params from user data
     *
     * @param array $userData Keys: email, first_name, last_name, street, city, state, country, postcode
     * @return array HyperPay formatted parameters
     */
    public function buildCustomerParams(array $userData): array
    {
        $params = [];

        // Customer Info
        if (!empty($userData['email'])) {
            $params['customer.email'] = $userData['email'];
        }
        $params['customer.givenName'] = $this->transliterateArabic($userData['first_name'] ?? 'User', 'User');
        $params['customer.surname'] = $this->transliterateArabic($userData['last_name'] ?? 'Guest', 'Guest');

        // Billing address (MANDATORY for 3DS2)
        $params['billing.street1'] = $this->transliterateArabic($userData['street'] ?? 'Saudi Arabia', 'Saudi Arabia');
        $params['billing.city'] = $this->transliterateArabic($userData['city'] ?? 'Riyadh', 'Riyadh');
        $params['billing.state'] = $this->transliterateArabic($userData['state'] ?? 'Riyadh', 'Riyadh');

        $country = $userData['country'] ?? 'SA';
        if (strlen($country) !== 2 || !preg_match('/^[a-zA-Z]{2}$/', $country)) {
            $country = 'SA';
        }
        $params['billing.country'] = strtoupper($country);

        $params['billing.postcode'] = $userData['postcode'] ?? '12345';

        return $params;
    }

    /**
     * Transliterate Arabic characters to English for HyperPay protocol
     */
    protected function transliterateArabic($string, $default = 'Customer')
    {
        if (empty($string)) return $default;

        $arabicMap = [
            'أ'=>'a', 'إ'=>'e', 'آ'=>'a', 'ا'=>'a', 'ب'=>'b', 'ت'=>'t', 'ث'=>'th', 'ج'=>'j', 'ح'=>'h', 'خ'=>'kh',
            'د'=>'d', 'ذ'=>'th', 'ر'=>'r', 'ز'=>'z', 'س'=>'s', 'ش'=>'sh', 'ص'=>'s', 'ض'=>'d', 'ط'=>'t', 'ظ'=>'th',
            'ع'=>'a', 'غ'=>'gh', 'ف'=>'f', 'ق'=>'q', 'ك'=>'k', 'ل'=>'l', 'م'=>'m', 'ن'=>'n', 'ه'=>'h', 'و'=>'w',
            'ي'=>'y', 'ى'=>'a', 'ة'=>'h', 'ئ'=>'e', 'ء'=>'a', 'ؤ'=>'o', 'ٲ'=>'a'
        ];

        // Replace Arabic letters
        $transliterated = strtr($string, $arabicMap);

        // Remove any remaining non-latin alphanumeric characters (keep spaces)
        $cleaned = preg_replace('/[^a-zA-Z0-9\s-]/', '', $transliterated);
        $cleaned = trim($cleaned);

        return empty($cleaned) ? $default : $cleaned;
    }

    /**
     * Get user-friendly translated message based on HyperPay result code
     *
     * @param string $resultCode
     * @param string|null $defaultDescription
     * @return string
     */
    public function getUserFriendlyMessage($resultCode, $defaultDescription = null): string
    {
        // Success codes
        if ($this->isSuccessful($resultCode)) {
            return __('payment.success');
        }

        // Map result code patterns to translation keys
        $codePatterns = [
            // Card/Account issues
            '100.100.303' => 'payment.insufficient_funds',
            '100.100.304' => 'payment.insufficient_funds',
            '800.100.151' => 'payment.card_declined',
            '800.100.152' => 'payment.card_declined',
            '800.100.153' => 'payment.card_invalid_cvv',
            '800.100.154' => 'payment.card_expired',
            '800.100.155' => 'payment.card_holder_invalid',
            '800.100.157' => 'payment.card_stolen',
            '800.100.159' => 'payment.card_fraud',
            '800.100.160' => 'payment.card_not_enrolled_3ds',
            '800.100.162' => 'payment.card_limit_exceeded',
            '800.100.163' => 'payment.card_limit_exceeded',
            '800.100.170' => 'payment.card_restriction',
            '800.100.171' => 'payment.card_restriction',
            '800.100.190' => 'payment.card_declined_issuer',

            // Technical/Session issues
            '700.400.200' => 'payment.checkout_expired',
            '700.400.300' => 'payment.checkout_expired',
            '700.400.530' => 'payment.checkout_expired',
            '700.400.560' => 'payment.checkout_already_used',
            '200.300.404' => 'payment.risk_rejected',
            '100.400.311' => 'payment.3ds_failed',
            '100.390.111' => 'payment.3ds_failed',
            '100.380.401' => 'payment.3ds_failed',
            '800.400.500' => 'payment.duplicate_request',

            // Network/connection
            '800.800.100' => 'payment.network_error',
            '800.800.102' => 'payment.timeout',
            '800.800.202' => 'payment.bank_unavailable',
            '900.100.100' => 'payment.internal_error',
        ];

        // Check exact match first
        if (isset($codePatterns[$resultCode])) {
            return __($codePatterns[$resultCode]);
        }

        // Check pattern-based matching
        if (preg_match('/^800\.100\./', $resultCode)) {
            return __('payment.card_declined');
        }
        if (preg_match('/^700\./', $resultCode)) {
            return __('payment.checkout_expired');
        }
        if (preg_match('/^800\.800\./', $resultCode)) {
            return __('payment.network_error');
        }
        if (preg_match('/^100\.39/', $resultCode)) {
            return __('payment.3ds_failed');
        }
        if (preg_match('/^900\./', $resultCode)) {
            return __('payment.internal_error');
        }

        // General fallback
        return __('payment.general_failure');
    }

    /**
     * Get Entity ID based on payment type
     */
    protected function getEntityId($type)
    {
        return $this->entityIds[$type] ?? null;
    }
}
