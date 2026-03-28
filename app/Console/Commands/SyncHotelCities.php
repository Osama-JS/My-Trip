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
        $this->info("Starting hotel city synchronization from index {$start}...");
        
        $result = $service->syncCities($start);
        
        if ($result['status'] === 'success') {
            $this->info("Successfully synced {$result['count']} hotel cities.");
        } else {
            $this->error($result['message']);
        }

        return 0;
    }
}
