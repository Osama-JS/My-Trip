<?php

namespace App\Traits;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;

trait PaymentLogTrait
{
    /**
     * Log a pending payment.
     */
    protected function logPendingPayment($bookingId, $provider, $method, $transactionId, $amount, $details = null)
    {
        try {
            Payment::create([
                'trip_booking_id' => $bookingId,
                'user_id' => auth()->id(),
                'payment_provider' => $provider,
                'payment_method' => $method,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'status' => 'pending',
                'payment_details' => $details,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to log pending payment: " . $e->getMessage());
        }
    }
}
