<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add ticket numbers, PNR, and airline info to the main bookings (flights) table
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('pnr_code')->nullable()->after('booking_reference'); // Airline PNR code
            $table->string('airline_name')->nullable()->after('pnr_code');      // e.g. "Saudia"
            $table->string('airline_code', 5)->nullable()->after('airline_name'); // e.g. "SV"
            $table->json('ticket_numbers')->nullable()->after('ticket_status'); // eTicket array from Travelopro
        });

        // Add e_ticket_no to each passenger record
        Schema::table('booking_passengers', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_passengers', 'e_ticket_no')) {
                $table->string('e_ticket_no')->nullable()->after('passport_number');
            }
        });

        // Add airline_name to flight_bookings for display convenience
        Schema::table('flight_bookings', function (Blueprint $table) {
            $table->string('airline_name')->nullable()->after('flight_class');
            $table->string('airline_code', 5)->nullable()->after('airline_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['pnr_code', 'airline_name', 'airline_code', 'ticket_numbers']);
        });

        Schema::table('booking_passengers', function (Blueprint $table) {
            if (Schema::hasColumn('booking_passengers', 'e_ticket_no')) {
                $table->dropColumn('e_ticket_no');
            }
        });

        Schema::table('flight_bookings', function (Blueprint $table) {
            $table->dropColumn(['airline_name', 'airline_code']);
        });
    }
};
