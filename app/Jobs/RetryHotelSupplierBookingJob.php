<?php

namespace App\Jobs;

use App\Models\HotelBooking;
use App\Services\TraveloproHotelService;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as LaravelNotification;

/**
 * RetryHotelSupplierBookingJob
 *
 * Dispatched when a hotel booking payment succeeds but the Travelopro
 * supplier session has expired (Fallback scenario).
 *
 * Strategy:
 * - Attempt 1: Immediately after payment
 * - Attempt 2: 5 minutes later
 * - Attempt 3: 30 minutes later
 * - Attempt 4: 2 hours later
 * - After all attempts: Notify admin for manual intervention
 */
class RetryHotelSupplierBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 4;

    public $backoff = [60, 300, 1800]; // seconds: 1min, 5min, 30min between retries

    public $timeout = 120;

    public function __construct(
        public readonly int $hotelBookingId
    ) {}

    public function handle(
        TraveloproHotelService $hotelService,
        InvoiceService $invoiceService,
        NotificationService $notificationService
    ): void {
        $booking = HotelBooking::with('user')->find($this->hotelBookingId);

        if (!$booking) {
            Log::error("[RetryHotelJob] Booking #{$this->hotelBookingId} not found. Aborting.");
            return;
        }

        // Already confirmed by another process — nothing to do
        if ($booking->status === 'confirmed' && !empty($booking->supplier_confirmation_num)) {
            Log::info("[RetryHotelJob] Booking #{$this->hotelBookingId} already confirmed. Skipping.");
            return;
        }

        $attempt = $this->attempts();
        Log::info("[RetryHotelJob] Attempt #{$attempt} for HotelBooking #{$this->hotelBookingId}");

        // ── Try to book with Travelopro ──────────────────────────────────
        try {
            $bookingData = [
                'sessionId'     => $booking->session_id,
                'productId'     => $booking->product_id,
                'tokenId'       => $booking->token_id,
                'rateBasisId'   => $booking->rate_basis_id,
                'clientRef'     => $booking->reference_num ?? ('HTL-' . $booking->id . '-' . time()),
                'customerEmail' => $booking->user->email ?? 'guest@mytrip.com',
                'customerPhone' => $booking->user->phone ?? '0000000000',
                'bookingNote'   => "Retry attempt #{$attempt} — Paid booking awaiting supplier confirmation.",
                'paxDetails'    => $booking->pax_details ?? [],
            ];

            $result = $hotelService->book($bookingData);

            $supplierRef = $result['supplierConfirmationNum']
                ?? $result['referenceNum']
                ?? $result['bookingId']
                ?? null;

            if ($supplierRef) {
                // ✅ SUCCESS: Confirm the booking
                $booking->update([
                    'status'                  => 'confirmed',
                    'supplier_confirmation_num' => $supplierRef,
                ]);

                // Generate Voucher PDF
                try {
                    $voucherPath = $invoiceService->generateHotelVoucher($booking);
                    if ($voucherPath) {
                        $booking->update(['invoice_path' => $voucherPath]);
                    }
                } catch (\Exception $e) {
                    Log::warning("[RetryHotelJob] Voucher generation failed: " . $e->getMessage());
                }

                // Notify the user
                if ($booking->user) {
                    $notificationService->sendToUser(
                        $booking->user,
                        \App\Models\Notification::TYPE_BOOKING_CONFIRMED,
                        __('Hotel Booking Confirmed'),
                        __('Your hotel booking at :hotel has been confirmed! Reference: :ref', [
                            'hotel' => $booking->hotel_name,
                            'ref'   => $supplierRef,
                        ]),
                        ['booking_id' => $booking->id, 'type' => 'hotel']
                    );
                }

                // Notify Admin of resolution
                $this->notifyAdminResolved($booking, $supplierRef, $attempt);

                Log::info("[RetryHotelJob] ✅ SUCCESS on attempt #{$attempt}. Supplier Ref: {$supplierRef}");
                return;
            }

            // Travelopro returned a response but without a supplier ref (business error)
            $errorMsg = $result['status']['error'] ?? $result['message'] ?? 'No supplier reference returned.';
            Log::warning("[RetryHotelJob] Attempt #{$attempt} got response but no supplier ref: {$errorMsg}");

            // Throw to trigger Laravel's retry mechanism
            throw new \RuntimeException("Supplier booking failed: {$errorMsg}");

        } catch (\Exception $e) {
            $remaining = $this->tries - $attempt;
            Log::error("[RetryHotelJob] Attempt #{$attempt} FAILED for Booking #{$this->hotelBookingId}: " . $e->getMessage() . " — {$remaining} retries left.");

            // Re-throw so Laravel's queue retries the job
            throw $e;
        }
    }

    /**
     * Called when all retry attempts are exhausted — maximum failure.
     * This is the final fallback: alert the admin immediately.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical(
            "[RetryHotelJob] ❌ ALL {$this->tries} ATTEMPTS FAILED for HotelBooking #{$this->hotelBookingId}. " .
            "Admin intervention required. Error: " . $exception->getMessage()
        );

        $booking = HotelBooking::with('user')->find($this->hotelBookingId);

        if (!$booking) {
            Log::error("[RetryHotelJob::failed] Could not load booking #{$this->hotelBookingId} for failed notification.");
            return;
        }

        // ── 1. Create Admin Notification in DB ────────────────────────────
        $this->createAdminNotification($booking, $exception->getMessage());

        // ── 2. Send Admin Email Alert ─────────────────────────────────────
        $this->sendAdminEmailAlert($booking, $exception->getMessage());

        // ── 3. Update booking with failure note ───────────────────────────
        $notes = ($booking->getOriginal()['booking_note'] ?? '') .
                 "\n[AUTO-ALERT] Supplier confirmation failed after {$this->tries} attempts on " . now()->toDateTimeString() .
                 ". Error: " . $exception->getMessage();

        // We add a custom column to track failure reason if it exists, else just log
        if (\Illuminate\Support\Facades\Schema::hasColumn('hotel_bookings', 'booking_note')) {
            $booking->update(['booking_note' => trim($notes)]);
        }

        // ── 4. IMPORTANT: Keep status as 'paid' (NOT failed) so user is NOT alarmed ──────
        // The money was taken. Admin must manually resolve this.
        // Do NOT change booking status to 'failed'.
        Log::info("[RetryHotelJob] Booking #{$this->hotelBookingId} kept as 'paid'. Awaiting admin manual resolution.");
    }

    // ── Private Helpers ───────────────────────────────────────────────────

    private function createAdminNotification(HotelBooking $booking, string $errorMsg): void
    {
        try {
            // Get all admin users
            $admins = \App\Models\User::role('admin')->orWhere('user_type', 'admin')->get();

            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'type'    => 'admin_hotel_booking_failed',
                    'title'   => '⚠️ فشل تأكيد حجز فندق مدفوع',
                    'content' => "حجز الفندق #{$booking->id} ({$booking->hotel_name}) — تم الدفع ولكن فشل تأكيد الحجز مع المورد بعد {$this->tries} محاولات. يجب التدخل اليدوي.\n\nالخطأ: {$errorMsg}",
                    'icon'    => 'hotel_error',
                    'user_id' => $admin->id,
                    'data'    => [
                        'booking_id'   => $booking->id,
                        'hotel_name'   => $booking->hotel_name,
                        'user_name'    => $booking->user->full_name ?? 'N/A',
                        'user_email'   => $booking->user->email ?? 'N/A',
                        'total_price'  => $booking->total_price,
                        'currency'     => $booking->currency,
                        'session_id'   => $booking->session_id,
                        'rate_basis_id'=> $booking->rate_basis_id,
                        'admin_url'    => route('admin.bookings.hotels.show', $booking->id),
                        'error'        => $errorMsg,
                        'attempts'     => $this->tries,
                        'alert_level'  => 'critical',
                    ],
                    'is_read' => false,
                ]);
            }

            Log::info("[RetryHotelJob] Admin notification created for Booking #{$booking->id}");
        } catch (\Exception $e) {
            Log::error("[RetryHotelJob] Failed to create admin notification: " . $e->getMessage());
        }
    }

    private function sendAdminEmailAlert(HotelBooking $booking, string $errorMsg): void
    {
        try {
            $adminEmail = config('mail.admin_email', env('ADMIN_EMAIL', 'admin@mytrip.sa'));

            Mail::send([], [], function ($message) use ($booking, $errorMsg, $adminEmail) {
                // Build a simple inline HTML email
                $html = "
                <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;direction:rtl;'>
                    <div style='background:#ef4444;color:white;padding:20px;border-radius:8px 8px 0 0;'>
                        <h2>⚠️ تنبيه عاجل: فشل تأكيد حجز فندق مدفوع</h2>
                    </div>
                    <div style='background:#fff;padding:24px;border:1px solid #e5e7eb;'>
                        <table style='width:100%;border-collapse:collapse;'>
                            <tr><td style='padding:8px;font-weight:bold;color:#374151;'>رقم الحجز:</td><td style='padding:8px;color:#6366f1;font-weight:bold;'>#" . $booking->id . "</td></tr>
                            <tr style='background:#f9fafb;'><td style='padding:8px;font-weight:bold;color:#374151;'>الفندق:</td><td style='padding:8px;'>" . $booking->hotel_name . "</td></tr>
                            <tr><td style='padding:8px;font-weight:bold;color:#374151;'>اسم العميل:</td><td style='padding:8px;'>" . ($booking->user->full_name ?? 'N/A') . "</td></tr>
                            <tr style='background:#f9fafb;'><td style='padding:8px;font-weight:bold;color:#374151;'>إيميل العميل:</td><td style='padding:8px;'>" . ($booking->user->email ?? 'N/A') . "</td></tr>
                            <tr><td style='padding:8px;font-weight:bold;color:#374151;'>المبلغ المدفوع:</td><td style='padding:8px;color:#10b981;font-weight:bold;'>" . number_format($booking->total_price, 2) . " " . $booking->currency . "</td></tr>
                            <tr style='background:#f9fafb;'><td style='padding:8px;font-weight:bold;color:#374151;'>عدد المحاولات:</td><td style='padding:8px;color:#ef4444;'>" . $this->tries . " محاولات — جميعها فشلت</td></tr>
                            <tr><td style='padding:8px;font-weight:bold;color:#374151;'>الخطأ:</td><td style='padding:8px;color:#ef4444;font-size:13px;'>" . htmlspecialchars($errorMsg) . "</td></tr>
                        </table>
                        <div style='margin-top:20px;padding:16px;background:#fef3c7;border-radius:8px;border-right:4px solid #f59e0b;'>
                            <strong>⚡ الإجراء المطلوب:</strong><br>
                            يجب مراجعة هذا الحجز يدوياً وتأكيد الحجز مع مزود الخدمة (Travelopro) أو استرداد المبلغ للعميل.
                        </div>
                        <div style='margin-top:16px;text-align:center;'>
                            <a href='" . route('admin.bookings.hotels.show', $booking->id) . "' 
                               style='background:#6366f1;color:white;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:bold;display:inline-block;'>
                                🔗 مراجعة الحجز في لوحة التحكم
                            </a>
                        </div>
                    </div>
                    <div style='text-align:center;padding:12px;color:#9ca3af;font-size:12px;'>
                        My Trip Platform — تنبيه أوتوماتيكي — " . now()->format('Y-m-d H:i:s') . "
                    </div>
                </div>";

                $message
                    ->to($adminEmail)
                    ->subject("⚠️ [عاجل] فشل تأكيد حجز فندق #{$booking->id} — تدخل يدوي مطلوب")
                    ->html($html);
            });

            Log::info("[RetryHotelJob] Admin email alert sent to {$adminEmail} for Booking #{$booking->id}");
        } catch (\Exception $e) {
            Log::error("[RetryHotelJob] Failed to send admin email: " . $e->getMessage());
        }
    }

    private function notifyAdminResolved(HotelBooking $booking, string $supplierRef, int $attempt): void
    {
        try {
            $admins = \App\Models\User::role('admin')->orWhere('user_type', 'admin')->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'type'    => 'admin_hotel_booking_resolved',
                    'title'   => '✅ تم تأكيد الحجز بعد إعادة المحاولة',
                    'content' => "تم تأكيد حجز الفندق #{$booking->id} ({$booking->hotel_name}) في المحاولة رقم {$attempt}. رقم التأكيد: {$supplierRef}",
                    'icon'    => 'hotel_confirmed',
                    'user_id' => $admin->id,
                    'data'    => ['booking_id' => $booking->id, 'supplier_ref' => $supplierRef, 'attempt' => $attempt],
                    'is_read' => false,
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("[RetryHotelJob] Could not notify admin of resolution: " . $e->getMessage());
        }
    }
}
