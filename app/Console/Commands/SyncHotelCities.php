<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncHotelCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-hotel-cities {--start=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync hotel cities from Travelopro API to local database';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\TraveloproHotelService $service)
    {
        $start = (int) $this->option('start');
        $this->info("Starting comprehensive hotel city synchronization from index {$start}...");
        $this->warn("This process may take several minutes as it syncs both English and Arabic names.");
        
        // Disable time limit for large syncs
        set_time_limit(0);
        
        $result = $service->syncCities($start);
        
        if ($result['status'] === 'success') {
            $this->info("Successfully synced {$result['count']} hotel cities.");
            $this->info("Message: " . $result['message']);
        } elseif ($result['status'] === 'warning') {
            $this->warn($result['message'] . " Seeded " . ($result['count'] ?? 0) . " fallback cities.");
        } else {
            $this->error($result['message']);
        }

        return 0;
    }
}
