<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutomizeWebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Verify the Webhook URL with Meta/Automize
     * They will send a GET request with hub.mode, hub.challenge, and hub.verify_token
     */
    public function verifyAutomize(Request $request)
    {
        $mode = $request->query('hub_mode'); // Sometimes passed as hub.mode, PHP converts . to _
        $challenge = $request->query('hub_challenge');
        $verifyToken = $request->query('hub_verify_token');

        $ourVerifyToken = config('services.automize.webhook_verify_token');

        if ($mode === 'subscribe' && $verifyToken === $ourVerifyToken) {
            Log::info('Automize Webhook Verified successfully.');
            // Meta expects the raw challenge string as a response
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('Automize Webhook Verification Failed.', [
            'received_token' => $verifyToken,
            'expected_token' => $ourVerifyToken
        ]);

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming Webhook Events from Meta/Automize
     */
    public function handleAutomize(Request $request)
    {
        try {
            $payload = $request->all();

            // Meta wraps events in an 'entry' array
            $eventType = 'unknown';
            if (isset($payload['entry'][0]['changes'][0]['value']['messages'])) {
                $eventType = 'incoming_message';
            } elseif (isset($payload['entry'][0]['changes'][0]['value']['statuses'])) {
                $eventType = 'message_status_update';
            }

            // Save the log to the database
            AutomizeWebhookLog::create([
                'event_type' => $eventType,
                'payload' => $payload
            ]);

            Log::info("Automize Webhook Event Received: {$eventType}");

            // Must always return 200 OK so Meta knows we received it
            return response('EVENT_RECEIVED', 200);

        } catch (\Exception $e) {
            Log::error('Automize Webhook Error: ' . $e->getMessage());
            // Return 200 even on error to prevent Meta from disabling the webhook
            return response('EVENT_RECEIVED', 200);
        }
    }
}
