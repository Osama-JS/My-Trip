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
        // Modify ticket_status on bookings table to VARCHAR(50) so it supports any status (refunded_to_wallet, ticketed, booked, failed, etc.)
        DB::statement("ALTER TABLE bookings MODIFY COLUMN ticket_status VARCHAR(50) DEFAULT 'booked'");
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY COLUMN ticket_status ENUM('booked', 'ticketed', 'voided', 'reissued') DEFAULT 'booked'");
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'paid', 'confirmed', 'cancelled', 'refunded', 'failed') DEFAULT 'pending'");
    }
};
