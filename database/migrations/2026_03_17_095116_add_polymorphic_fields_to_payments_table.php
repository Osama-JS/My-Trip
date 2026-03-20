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
        Schema::table('payments', function (Blueprint $table) {
            // Make trip_booking_id nullable so we can transition
            $table->unsignedBigInteger('trip_booking_id')->nullable()->change();
            
            // Add polymorphic fields
            $table->unsignedBigInteger('payable_id')->nullable()->after('id');
            $table->string('payable_type')->nullable()->after('payable_id');
            
            $table->index(['payable_id', 'payable_type'], 'payments_payable_index');
        });

        // Migrate existing data from trip_booking_id to polymorphic fields
        DB::table('payments')->whereNotNull('trip_booking_id')->update([
            'payable_id' => DB::raw('trip_booking_id'),
            'payable_type' => 'App\Models\TripBooking'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_payable_index');
            $table->dropColumn(['payable_id', 'payable_type']);
            $table->unsignedBigInteger('trip_booking_id')->nullable(false)->change();
        });
    }
};
