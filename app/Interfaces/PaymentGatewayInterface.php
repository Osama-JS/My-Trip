<?php

namespace App\Interfaces;

interface PaymentGatewayInterface
{
    /**
     * Initiate a checkout session.
     *
     * @param array $data Payment data (amount, currency, customer info, etc.)
     * @return array Response containing checkout URL and session ID.
     */
    public function initiateCheckout(array $data): array;

    /**
     * Verify payment status.
     *
     * @param string $paymentId The payment or session ID to verify.
     * @return array Detailed payment status response.
     */
    public function verifyPayment(string $paymentId): array;

    /**
     * Get a simplified payment status string (e.g., 'paid', 'failed').
     *
     * @param string $paymentId
     * @return string
     */
    public function getPaymentStatus(string $paymentId): string;
}
