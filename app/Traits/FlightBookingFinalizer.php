<?php

namespace App\Traits;

use App\Models\Booking;
use App\Services\InvoiceService;
use App\Mail\FlightBookingConfirmedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait FlightBookingFinalizer
{
    /**
     * Send Flight Confirmation & E-Ticket Email with generated PDF attached.
     */
    protected function sendFlightConfirmationEmail(Booking $booking, ?string $voucherPath = null): void
    {
        try {
            if (!$voucherPath) {
                $invoiceService = app(InvoiceService::class);
                $voucherPath = $invoiceService->generateInvoice($booking);
            }

            $recipientEmail = $booking->user->email ?? ($booking->contact_email ?? null);
            
            // Check flightBooking extra_services or passenger inputs if email is missing
            if (empty($recipientEmail) && $booking->flightBooking) {
                $extra = $booking->flightBooking->extra_services;
                if (is_array($extra)) {
                    foreach ($extra as $pax) {
                        if (!empty($pax['email'])) {
                            $recipientEmail = $pax['email'];
                            break;
                        }
                    }
                }
            }

            if (empty($recipientEmail)) {
                Log::warning("FlightConfirmationEmail: No recipient email found for Flight Booking #{$booking->id}");
                return;
            }

            Mail::to($recipientEmail)->send(new FlightBookingConfirmedMail($booking, $voucherPath));
            Log::info("FlightConfirmationEmail: Successfully sent confirmation email to {$recipientEmail} for Flight Booking #{$booking->id} (Ref: {$booking->booking_reference})");

        } catch (\Exception $e) {
            Log::error("FlightConfirmationEmail: Failed to send email for Flight Booking #{$booking->id}: " . $e->getMessage());
        }
    }
}
