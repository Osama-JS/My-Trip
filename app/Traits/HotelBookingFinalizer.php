<?php

namespace App\Traits;

use App\Models\HotelBooking;
use App\Models\Notification;
use App\Models\User;
use App\Services\TraveloproHotelService;
use App\Services\InvoiceService;
use App\Jobs\RetryHotelSupplierBookingJob;
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
            
            // NEW SAFETY GUARD: Never trigger supplier book if payment is not verified
            // We only allow proceeding if status is 'paid' OR 'confirmed'
            $allowedStatuses = ['paid', 'confirmed'];
            if (!in_array($booking->status, $allowedStatuses) && empty($booking->supplier_confirmation_num)) {
                Log::warning("HotelFinalizer: Aborting supplier book attempt for ID {$booking->id}. Current status '{$booking->status}' is not authorized for booking.");
                return false;
            }

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
                'requiredLanguage' => app()->getLocale() === 'ar' ? 'ARA' : 'ENG',
            ];

            Log::info("HotelFinalizer: Attempting late hotel_book via TraveloproService", [
                'booking_id' => $booking->id,
                'clientRef'  => $bookingData['clientRef']
            ]);

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

            // ── FALLBACK ALERT: Notify Admin for MANUAL intervention ──────
            // As requested, we do NOT auto-retry. We notify the admin immediately.
            if ($booking->status !== 'confirmed') {
                $booking->update(['status' => 'paid']); // Keep as paid, NOT failed
                
                // Immediate admin notification
                $this->notifyAdminImmediately($booking, $errorMsg);
                
                Log::warning("HotelBooking #{$booking->id}: Fallback triggered. Admin notified for manual intervention.");
            }

            return false;

        } catch (\Exception $e) {
            Log::error("HotelFinalizer: Travelopro Hotel Booking Exception for Booking #{$booking->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Immediately notify admins when the supplier booking attempt fails.
     */
    private function notifyAdminImmediately(HotelBooking $booking, string $errorMsg): void
    {
        try {
            $admins = User::where(function ($q) {
                $q->where('user_type', 'admin')
                  ->orWhereHas('roles', fn($r) => $r->where('name', 'admin'));
            })->get();

            foreach ($admins as $admin) {
                Notification::create([
                    'type'    => 'admin_hotel_booking_failed',
                    'title'   => '⚠️ تدخل يدوي مطلوب: دفع مكتمل وحجز معلق',
                    'content' => "حجز الفندق #{$booking->id} ({$booking->hotel_name}) — تم استلام الدفع ولكن انتهت صلاحية جلسة Travelopro. " .
                                 "يجب التدخل اليدوي الفوري لتأكيد الحجز.\n\nالخطأ: {$errorMsg}",
                    'icon'    => 'hotel_error',
                    'user_id' => $admin->id,
                    'data'    => [
                        'booking_id'  => $booking->id,
                        'hotel_name'  => $booking->hotel_name,
                        'total_price' => $booking->total_price,
                        'currency'    => $booking->currency,
                        'error'       => $errorMsg,
                        'alert_level' => 'critical', // Set to critical
                        'admin_url'   => route('admin.bookings.hotels.show_detail', $booking->id),
                    ],
                    'is_read' => false,
                ]);
            }

            Log::info("HotelBookingFinalizer: Admin manual intervention notification sent for Booking #{$booking->id}");
        } catch (\Exception $e) {
            Log::error("HotelBookingFinalizer: Failed to notify admins: " . $e->getMessage());
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
