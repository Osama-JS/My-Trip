<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Services\TraveloproService;

/**
 * PollBookingStatus — P2 Polling / Webhook Support
 *
 * Polls Travelopro's post-ticket-status endpoint for all flight bookings
 * that are still in a 'pending' or 'booked' state and updates the local
 * status accordingly.
 *
 * Usage:
 *   php artisan travelopro:poll-status          # run once
 *   php artisan travelopro:poll-status --limit=50
 *
 * Schedule (in app/Console/Kernel.php):
 *   $schedule->command('travelopro:poll-status')->everyFiveMinutes();
 */
class PollBookingStatus extends Command
{
    protected $signature = 'travelopro:poll-status
                            {--limit=20 : Maximum bookings to poll per run}
                            {--dry-run  : Log changes without saving}';

    protected $description = 'Poll Travelopro post-ticket status for pending flight bookings and update local records.';

    public function __construct(protected TraveloproService $travelopro)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $limit  = (int) $this->option('limit');
        $dryRun = (bool) $this->option('dry-run');

        $this->info("Polling up to {$limit} pending flight bookings" . ($dryRun ? ' [DRY RUN]' : '') . '…');

        // Only poll flight bookings that haven't been confirmed yet
        $bookings = Booking::query()
            ->whereIn('status', ['pending', 'booked'])
            ->whereNotNull('booking_reference')
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No pending bookings to poll.');
            return self::SUCCESS;
        }

        $updated = 0;
        $failed  = 0;

        foreach ($bookings as $booking) {
            try {
                $result = $this->travelopro->getPostTicketStatus([
                    'UniqueID' => $booking->booking_reference,
                ]);

                if (isset($result['status']) && $result['status'] === 'error') {
                    $this->warn("  ✗ [{$booking->booking_reference}] API error: " . ($result['message'] ?? 'unknown'));
                    $failed++;
                    continue;
                }

                $newStatus = $this->resolveStatus($result);

                if ($newStatus && $newStatus !== $booking->status) {
                    $this->line("  → [{$booking->booking_reference}] {$booking->status} → <fg=green>{$newStatus}</>");

                    if (!$dryRun) {
                        $booking->update(['status' => $newStatus]);
                        Log::info('PollBookingStatus: status updated', [
                            'booking_id'  => $booking->id,
                            'reference'   => $booking->booking_reference,
                            'old_status'  => $booking->status,
                            'new_status'  => $newStatus,
                        ]);
                    }
                    $updated++;
                } else {
                    $this->line("  · [{$booking->booking_reference}] no change ({$booking->status})");
                }

            } catch (\Throwable $e) {
                $this->error("  ✗ [{$booking->booking_reference}] Exception: {$e->getMessage()}");
                Log::error('PollBookingStatus exception', [
                    'booking_id' => $booking->id,
                    'error'      => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Polled',   $bookings->count()],
                ['Updated',  $updated],
                ['Failed',   $failed],
                ['Dry Run',  $dryRun ? 'YES' : 'NO'],
            ]
        );

        return self::SUCCESS;
    }

    /**
     * Map the raw Travelopro response to a local booking status string.
     */
    private function resolveStatus(array $result): ?string
    {
        // Response shapes vary; normalise the status field
        $raw = $result['PostTicketStatusResponse']['PostTicketStatusResult']['Status']
            ?? $result['Status']
            ?? $result['status']
            ?? null;

        if (empty($raw)) {
            return null;
        }

        return match (strtolower((string) $raw)) {
            'confirmed', 'ticketed', 'issued' => 'confirmed',
            'cancelled', 'canceled'           => 'cancelled',
            'failed', 'rejected'              => 'failed',
            'pending', 'booked'               => null, // no change
            default                           => null,
        };
    }
}
