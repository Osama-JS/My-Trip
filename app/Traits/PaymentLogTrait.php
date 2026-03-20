<?php

namespace App\Traits;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;

trait PaymentLogTrait
{
    /**
     * Log a pending payment.
     */
    protected function logPendingPayment($payable, $provider, $method, $transactionId, $amount, $details = null)
    {
        try {
            Payment::create([
                'payable_id' => $payable->id,
                'payable_type' => get_class($payable),
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
