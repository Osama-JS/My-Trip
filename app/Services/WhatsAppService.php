<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Start OTP Verification via Automize Managed OTP API
     *
     * @param string $phone
     * @return string|false Returns verification_id on success, false on failure.
     */
    public function startVerification($phone)
    {
        // For testing/simulation, we can use a hardcoded OTP (e.g. 1234)
        if (config('services.whatsapp.simulation', false)) {
            Log::info("SIMULATED WHATSAPP OTP sent to {$phone}");
            // Return a dummy verification ID for simulation
            return 'sim_' . time();
        }

        $apiUrl = config('services.automize.url'); // Expected: https://api.saei.automize.sa/api
        $phoneNumberId = config('services.automize.phone_number_id');
        $token = config('services.automize.token');
        $templateId = config('services.automize.template_id');

        if (!$apiUrl || !$phoneNumberId || !$token || !$templateId) {
            Log::warning('Automize credentials are not fully set.');
            return false;
        }

        // Format phone to E.164 format with +
        $phoneFormatted = '+' . ltrim($phone, '+');
        
        $endpoint = rtrim($apiUrl, '/') . '/v1/verify/start';

        $payload = [
            'to' => $phoneFormatted,
            'from' => $phoneNumberId,
            'template_id' => (int) $templateId
        ];

        try {
            $response = Http::withToken($token)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                $verificationId = $responseData['verification_id'] ?? null;
                
                if ($verificationId) {
                    Log::info("WhatsApp OTP verification started via Automize for {$phone}. ID: {$verificationId}");
                    return (string) $verificationId;
                } else {
                    Log::error("Automize verification started but no verification_id returned for {$phone}", ['response' => $responseData]);
                    return false;
                }
            } else {
                Log::error("Failed to start WhatsApp OTP via Automize for {$phone}", [
                    'response' => $response->json(),
                    'status' => $response->status(),
                    'payload' => $payload
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp OTP (Automize Start) Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check OTP Verification via Automize Managed OTP API
     *
     * @param string $verificationId
     * @param string $code
     * @return bool Returns true if approved, false otherwise.
     */
    public function checkVerification($verificationId, $code)
    {
        if (config('services.whatsapp.simulation', false)) {
            Log::info("SIMULATED WHATSAPP OTP checked. ID: {$verificationId}, Code: {$code}");
            // In simulation mode, accept '1234' as valid code
            return $code === '1234';
        }

        $apiUrl = config('services.automize.url');
        $token = config('services.automize.token');

        if (!$apiUrl || !$token) {
            Log::warning('Automize credentials are not fully set for checking verification.');
            return false;
        }

        $endpoint = rtrim($apiUrl, '/') . '/v1/verify/check';

        $payload = [
            'verification_id' => $verificationId,
            'code' => $code
        ];

        try {
            $response = Http::withToken($token)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                $status = $responseData['status'] ?? '';
                
                if ($status === 'approved') {
                    Log::info("WhatsApp OTP checked successfully via Automize. ID: {$verificationId}");
                    return true;
                } else {
                    Log::warning("WhatsApp OTP check denied/expired via Automize. ID: {$verificationId}, Status: {$status}");
                    return false;
                }
            } else {
                Log::error("Failed to check WhatsApp OTP via Automize.", [
                    'response' => $response->json(),
                    'status' => $response->status(),
                    'payload' => $payload
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp OTP (Automize Check) Exception: " . $e->getMessage());
            return false;
        }
    }
}
