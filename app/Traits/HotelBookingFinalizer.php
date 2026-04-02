<?php

namespace App\Traits;

use App\Models\HotelBooking;
use App\Services\TraveloproHotelService;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Log;

trait HotelBookingFinalizer
{
    /**
     * Finalize the hotel booking with the supplier (Travelopro) after payment.
     * 
     * This is called AFTER payment. If the supplier ref was already obtained before payment,
     * we simply mark the booking as confirmed. If not, we attempt to re-book.
     *
     * @param HotelBooking $booking
     * @return bool
     */
    protected function finalizeHotelSupplierBooking($booking)
    {
        try {
            // 1. Already fully confirmed — nothing to do
            if ($booking->status === 'confirmed' && !empty($booking->supplier_confirmation_num)) {
                Log::info("Booking {$booking->id} is already confirmed with supplier ref.");
                return true;
            }

            Log::info("Finalizing HotelBooking ID: {$booking->id}. Status: {$booking->status}");

            // 2. Supplier ref was already captured before payment (ideal flow)
            if (!empty($booking->supplier_confirmation_num)) {
                Log::info("Booking {$booking->id} already has supplier ref. Marking as confirmed.");
                $booking->update(['status' => 'confirmed']);
                $this->generateVoucher($booking);
                return true;
            }

            // 3. Fallback: Session likely expired. Try to re-book anyway.
            Log::warning("Booking {$booking->id} has no supplier ref — attempting late hotel_book call.");

            if (empty($booking->rate_basis_id)) {
                Log::error("Missing rateBasisId for HotelBooking {$booking->id}");
                return false;
            }

            $hotelService = app(TraveloproHotelService::class);

            $bookingData = [
                'sessionId'    => $booking->session_id,
                'productId'    => $booking->product_id,
                'tokenId'      => $booking->token_id,
                'rateBasisId'  => $booking->rate_basis_id,
                'clientRef'    => $booking->reference_num ?? ('HTL-' . $booking->id . '-' . time()),
                'customerEmail' => $booking->user->email ?? 'guest@example.com',
                'customerPhone' => $booking->user->phone ?? '0000000000',
                'bookingNote'  => 'Paid Hotel Booking via Gateway',
                'paxDetails'   => $booking->pax_details,
            ];

            $result = $hotelService->book($bookingData);

            // Check various success structures Travelopro might return
            $supplierRef = $result['supplierConfirmationNum']
                ?? $result['referenceNum']
                ?? $result['bookingId']
                ?? null;

            if ($supplierRef) {
                $booking->update([
                    'status' => 'confirmed',
                    'supplier_confirmation_num' => $supplierRef,
                ]);

                Log::info("Late hotel_book succeeded. Supplier Ref: {$supplierRef}");
                $this->generateVoucher($booking);
                return true;
            }

            $errorMsg = $result['status']['error']
                ?? $result['message']
                ?? 'Unknown supplier error';

            Log::error("Late hotel_book failed for ID {$booking->id}: {$errorMsg}");

            // Keep as 'paid' so admin can retry
            if ($booking->status !== 'confirmed') {
                $booking->update(['status' => 'paid']);
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Exception in finalizeHotelSupplierBooking for ID {$booking->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate voucher PDF for confirmed hotel booking.
     */
    private function generateVoucher(HotelBooking $booking): void
    {
        try {
            $invoiceService = app(InvoiceService::class);
            $voucherPath    = $invoiceService->generateHotelVoucher($booking);
            if ($voucherPath) {
                $booking->update(['invoice_path' => $voucherPath]);
                Log::info("Voucher generated at: {$voucherPath}");
            }
        } catch (\Exception $e) {
            Log::error("Voucher generation failed for HTL-{$booking->id}: " . $e->getMessage());
        }
    }
}
