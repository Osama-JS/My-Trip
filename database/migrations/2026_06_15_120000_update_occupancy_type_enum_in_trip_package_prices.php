<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First delete any rows with the old 'child' enum
        DB::statement("DELETE FROM trip_package_prices WHERE occupancy_type = 'child'");

        // Alter the enum
        DB::statement("ALTER TABLE trip_package_prices MODIFY occupancy_type ENUM('single', 'double', 'triple', 'quadruple', 'quintuple') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DELETE FROM trip_package_prices WHERE occupancy_type IN ('quadruple', 'quintuple')");
        DB::statement("ALTER TABLE trip_package_prices MODIFY occupancy_type ENUM('single', 'double', 'triple', 'child') NOT NULL");
    }
};
