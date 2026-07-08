<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send an OTP via WhatsApp (Green API)
     *
     * @param string $phone
     * @param string $code
     * @param string $lang
     * @return bool
     */
    public function sendOTP($phone, $code, $lang = 'ar')
    {
        // For testing/simulation, we can use a hardcoded OTP (e.g. 1234)
        if (config('services.whatsapp.simulation', false)) {
            Log::info("SIMULATED WHATSAPP OTP sent to {$phone}: {$code}");
            return true;
        }

        $idInstance = config('services.green_api.id_instance');
        $apiTokenInstance = config('services.green_api.token_instance');

        if (!$idInstance || !$apiTokenInstance) {
            Log::warning('Green API credentials are not set. idInstance=' . ($idInstance ? 'set' : 'MISSING') . ', token=' . ($apiTokenInstance ? 'set' : 'MISSING'));
            return false;
        }

        $messages = [
            'ar' => "مرحباً بك في تطبيق Flyvio.\nكود التحقق الخاص بك هو: *{$code}*\nالرجاء عدم مشاركة هذا الكود مع أي شخص اخر.",
            'en' => "Welcome to Flyvio App.\nYour verification code is: *{$code}*\nPlease do not share this code with anyone else.",
        ];

        // Format phone to international format without +
        $phoneFormatted = ltrim($phone, '+');
        $chatId = $phoneFormatted . '@c.us';

        $url = "https://api.green-api.com/waInstance{$idInstance}/sendMessage/{$apiTokenInstance}";

        // Normalize language code
        $langCode = explode('_', $lang)[0];
        $message = $messages[$langCode] ?? $messages['ar'];

        try {
            $response = Http::post($url, [
                'chatId' => $chatId,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp OTP sent successfully to {$phone}");
                return true;
            } else {
                Log::error("Failed to send WhatsApp OTP to {$phone}", [
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp OTP Exception: " . $e->getMessage());
            return false;
        }
    }
}
