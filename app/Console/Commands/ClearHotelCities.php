<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearHotelCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-hotel-cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all hotel cities from the local database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn("This will delete all hotel cities from the database.");
        if ($this->confirm('Are you sure you want to proceed?')) {
            \App\Models\HotelCity::truncate();
            $this->info("All hotel cities have been cleared successfully.");
        }
    }
}
