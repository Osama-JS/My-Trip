<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncAirports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-airports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync airports from Travelopro API to local database';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\TraveloproService $service)
    {
        $this->info('Starting airport synchronization...');
        
        // Pass true to force bypass cache
        $result = $service->syncAirports(true);
        
        if ($result['status'] === 'success') {
            $this->info("Successfully synced {$result['count']} airports.");
        } elseif ($result['status'] === 'warning') {
            $this->warn($result['message'] . " Seeded " . ($result['count'] ?? 0) . " fallback airports.");
        } else {
            $this->error($result['message']);
        }

        return 0;
    }
}
