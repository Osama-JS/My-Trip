<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncAirportsCommand extends Command
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
    public function handle(\App\Services\TraveloproService $traveloproService)
    {
        $this->info('Starting airport synchronization...');
        
        $result = $traveloproService->syncAirports();
        
        if ($result['status'] === 'success') {
            $this->info("Successfully synced {$result['count']} airports.");
        } elseif ($result['status'] === 'warning') {
            $this->warn($result['message'] . " Seeded {$result['count']} fallback airports.");
        } else {
            $this->error('Failed to sync airports: ' . ($result['message'] ?? 'Unknown error'));
        }
    }
}
