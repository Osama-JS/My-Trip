<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\HotelBooking;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CancelExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically cancel expired pending flight and hotel bookings and send reminder alerts.';

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting expired bookings cleanup...');

        $this->processFlightBookings();
        $this->processHotelBookings();

        $this->info('Expired bookings check completed successfully.');
        return 0;
    }

    /**
     * Process flight bookings: Cancellations & Pre-expiry Reminders
     */
    protected function processFlightBookings(): void
    {
        // 1. Pre-expiry Reminders: Pending flights expiring in <= 5 minutes
        $preExpiringFlights = Booking::with('user')
            ->where('status', 'pending')
            ->whereNotNull('ticketing_time_limit')
            ->whereBetween('ticketing_time_limit', [now(), now()->addMinutes(5)])
            ->get();

        foreach ($preExpiringFlights as $booking) {
            $cacheKey = "flight_pre_expiry_alert_{$booking->id}";
            if (!Cache::has($cacheKey)) {
                Cache::put($cacheKey, true, now()->addHours(2));

                if ($booking->user) {
                    $ref = $booking->booking_reference ?: "#{$booking->id}";
                    $this->notificationService->sendToUser(
                        $booking->user,
                        'booking_expiry_warning',
                        __('Payment Deadline Warning'),
                        __('Your flight reservation :ref will expire in less than 5 minutes. Complete payment now to secure your seats.', ['ref' => $ref]),
                        ['booking_id' => $booking->id, 'type' => 'flight', 'action' => 'pay']
                    );
                    Log::info("Pre-expiry warning sent for Flight Booking #{$booking->id}");
                }
            }
        }

        // 2. Cancellation: Pending flights that have reached or passed their deadline
        $expiredFlights = Booking::with('user')
            ->where('status', 'pending')
            ->whereNotNull('ticketing_time_limit')
            ->where('ticketing_time_limit', '<=', now())
            ->get();

        foreach ($expiredFlights as $booking) {
            $booking->update(['status' => 'cancelled']);
            Log::info("Flight Booking #{$booking->id} (Ref: {$booking->booking_reference}) AUTO-CANCELLED due to ticketing time limit expiration.");

            if ($booking->user) {
                $ref = $booking->booking_reference ?: "#{$booking->id}";
                $this->notificationService->sendToUser(
                    $booking->user,
                    'booking_cancelled',
                    __('Flight Reservation Expired'),
                    __('Your flight booking :ref has been cancelled due to payment time limit expiration. You can search and re-book anytime.', ['ref' => $ref]),
                    ['booking_id' => $booking->id, 'type' => 'flight', 'action' => 'rebook']
                );
            }
            $this->line("Cancelled expired Flight Booking #{$booking->id}");
        }
    }

    /**
     * Process hotel bookings: Cancellations & Pre-expiry Reminders
     */
    protected function processHotelBookings(): void
    {
        // 1. Pre-expiry Reminders: Pending hotels created between 5 and 8 minutes ago (leaving 2-5 min)
        $preExpiringHotels = HotelBooking::with('user')
            ->where('status', 'pending')
            ->whereNull('supplier_confirmation_num')
            ->whereBetween('created_at', [now()->subMinutes(8), now()->subMinutes(5)])
            ->get();

        foreach ($preExpiringHotels as $booking) {
            $cacheKey = "hotel_pre_expiry_alert_{$booking->id}";
            if (!Cache::has($cacheKey)) {
                Cache::put($cacheKey, true, now()->addHours(2));

                if ($booking->user) {
                    $this->notificationService->sendToUser(
                        $booking->user,
                        'booking_expiry_warning',
                        __('Hotel Booking Expiring Soon'),
                        __('Your hotel reservation at :hotel will expire soon. Please complete payment within the next few minutes.', ['hotel' => $booking->hotel_name]),
                        ['booking_id' => $booking->id, 'type' => 'hotel', 'action' => 'pay']
                    );
                    Log::info("Pre-expiry warning sent for Hotel Booking #{$booking->id}");
                }
            }
        }

        // 2. Cancellation: Pending hotels older than 10 minutes
        $expiredHotels = HotelBooking::with('user')
            ->where('status', 'pending')
            ->whereNull('supplier_confirmation_num')
            ->where('created_at', '<=', now()->subMinutes(10))
            ->get();

        foreach ($expiredHotels as $booking) {
            $booking->update(['status' => 'cancelled']);
            Log::info("Hotel Booking #{$booking->id} (:hotel) AUTO-CANCELLED due to 10-minute hold expiration.", ['hotel' => $booking->hotel_name]);

            if ($booking->user) {
                $this->notificationService->sendToUser(
                    $booking->user,
                    'booking_cancelled',
                    __('Hotel Reservation Expired'),
                    __('Your hotel booking at :hotel has expired. Please re-search to get updated room availability and rates.', ['hotel' => $booking->hotel_name]),
                    ['booking_id' => $booking->id, 'type' => 'hotel', 'action' => 'rebook']
                );
            }
            $this->line("Cancelled expired Hotel Booking #{$booking->id}");
        }
    }
}
