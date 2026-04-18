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
        // Add 'paid' to trip_bookings status enum
        DB::statement("ALTER TABLE trip_bookings MODIFY COLUMN status ENUM('pending', 'paid', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending'");

        // Add 'paid' to bookings (flights) status enum
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'paid', 'confirmed', 'cancelled', 'refunded', 'failed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE trip_bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending'");
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'refunded', 'failed') DEFAULT 'pending'");
    }
};
