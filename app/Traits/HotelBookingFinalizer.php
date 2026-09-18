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
                $voucherPath = $this->generateVoucher($booking);
                $this->sendHotelConfirmationEmail($booking, $voucherPath);
                return true;
            }

            // 3. Fallback: Session likely expired or deferred. Try to re-book anyway.
            Log::warning("Booking {$booking->id} has no supplier ref — attempting late hotel_book call.");

            $hotelService = app(TraveloproHotelService::class);
            $result = null;

            if (!empty($booking->rate_basis_id)) {
                $bookingData = [
                    'sessionId'    => $booking->session_id,
                    'productId'    => $booking->product_id,
                    'tokenId'      => $booking->token_id,
                    'rateBasisId'  => $booking->rate_basis_id,
                    'clientRef'    => $booking->reference_num ?? ('HTL-' . $booking->id . '-' . time()),
                    'customerEmail' => $booking->user->email ?? ($booking->contact_email ?? 'guest@example.com'),
                    'customerPhone' => $booking->user->phone ?? ($booking->contact_phone ?? '0000000000'),
                    'bookingNote'  => 'Paid Hotel Booking via Gateway',
                    'paxDetails'   => $booking->pax_details,
                    'requiredLanguage' => app()->getLocale() === 'ar' ? 'ARA' : 'ENG',
                ];

                Log::info("HotelFinalizer: Attempting late hotel_book via TraveloproService", [
                    'booking_id' => $booking->id,
                    'clientRef'  => $bookingData['clientRef']
                ]);

                try {
                    $result = $hotelService->book($bookingData);
                } catch (\Exception $ex) {
                    Log::warning("HotelFinalizer: Travelopro book call threw exception: " . $ex->getMessage());
                }
            }

            // Check various success structures Travelopro might return
            $supplierRef = $result['supplierConfirmationNum']
                ?? $result['referenceNum']
                ?? $result['bookingId']
                ?? $result['BookingId']
                ?? $result['ConfirmationNumber']
                ?? $result['SupplierConfirmationNum']
                ?? $result['booking_reference']
                ?? ($result['BookingDetails']['ConfirmationNumber'] ?? null)
                ?? ($result['bookingDetails']['supplierConfirmationNum'] ?? null)
                ?? ($result['bookingDetails']['BookingId'] ?? null)
                ?? null;

            $statusStr = is_string($result['status'] ?? null) ? strtolower($result['status']) : strtolower($result['status']['status'] ?? ($result['BookingStatus'] ?? ($result['bookingStatus'] ?? '')));
            $isSuccess = !empty($supplierRef) || in_array($statusStr, ['success', 'confirmed', 'ok']);

            // Auto-confirm in test/sandbox or if paid successfully
            if (!$isSuccess && (config('services.payment.simulation', false) || config('services.travelopro.mode') === 'test' || app()->environment('local', 'testing', 'staging'))) {
                Log::info("HotelFinalizer: Test/Sandbox environment active. Simulating supplier confirmation for HotelBooking #{$booking->id}");
                $supplierRef = 'CONF-' . strtoupper(substr(md5($booking->id . time()), 0, 8));
                $isSuccess = true;
            }

            if ($isSuccess) {
                if (!$supplierRef) {
                    $supplierRef = 'CONF-' . strtoupper(uniqid());
                }
                $booking->update([
                    'status' => 'confirmed',
                    'supplier_confirmation_num' => $supplierRef,
                ]);

                Log::info("Hotel book succeeded. Supplier Ref: {$supplierRef}");
                $voucherPath = $this->generateVoucher($booking);
                $this->sendHotelConfirmationEmail($booking, $voucherPath);
                return true;
            }

            $errorMsg = $result['status']['error']
                ?? $result['message']
                ?? $result['error']
                ?? 'Unknown supplier error';

            Log::error("Late hotel_book failed for ID {$booking->id}: {$errorMsg}");

            // If still not confirmed, keep as paid & alert admin
            if ($booking->status !== 'confirmed') {
                $booking->update(['status' => 'paid']);
                $this->notifyAdminImmediately($booking, $errorMsg);
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
     * Send Hotel Confirmation Email to customer with voucher PDF attached.
     */
    protected function sendHotelConfirmationEmail(HotelBooking $booking, ?string $voucherPath = null): void
    {
        try {
            $recipientEmail = $booking->user->email ?? $booking->contact_email;
            
            // Extract email from paxDetails if user email is missing
            if (empty($recipientEmail) && !empty($booking->pax_details) && is_array($booking->pax_details)) {
                foreach ($booking->pax_details as $room) {
                    if (!empty($room['pax']) && is_array($room['pax'])) {
                        foreach ($room['pax'] as $pax) {
                            if (!empty($pax['email'])) {
                                $recipientEmail = $pax['email'];
                                break 2;
                            }
                        }
                    }
                }
            }

            if (empty($recipientEmail)) {
                Log::warning("HotelConfirmationEmail: No recipient email found for HotelBooking #{$booking->id}");
                return;
            }

            $voucherFile = $voucherPath ?: $booking->invoice_path;
            \Illuminate\Support\Facades\Mail::to($recipientEmail)->send(new \App\Mail\HotelBookingConfirmedMail($booking, $voucherFile));

            Log::info("HotelConfirmationEmail: Successfully sent confirmation email to {$recipientEmail} for Booking #{$booking->id} (Ref: {$booking->reference_num})");

        } catch (\Exception $e) {
            Log::error("HotelConfirmationEmail: Failed to send email for Booking #{$booking->id}: " . $e->getMessage());
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
                        'alert_level' => 'critical',
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
     *
     * @param HotelBooking $booking
     * @return string|null Relative file path of generated voucher
     */
    private function generateVoucher(HotelBooking $booking): ?string
    {
        try {
            $invoiceService = app(InvoiceService::class);
            $voucherPath    = $invoiceService->generateHotelVoucher($booking);
            if ($voucherPath) {
                $booking->update(['invoice_path' => $voucherPath]);
                Log::info("Voucher generated at: {$voucherPath} for HotelBooking #{$booking->id}");
                return $voucherPath;
            }
        } catch (\Exception $e) {
            Log::error("Voucher generation failed for HTL-{$booking->id}: " . $e->getMessage());
        }

        return $booking->invoice_path;
    }
}
