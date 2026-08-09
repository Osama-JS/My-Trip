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
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('provider_price', 10, 2)->default(0)->after('total_amount')->comment('Base price from Travelopro');
            $table->decimal('platform_profit', 10, 2)->default(0)->after('provider_price')->comment('Markup added by platform');
        });

        Schema::table('hotel_bookings', function (Blueprint $table) {
            $table->decimal('provider_price', 10, 2)->default(0)->after('total_price')->comment('Base price from Travelopro');
            $table->decimal('platform_profit', 10, 2)->default(0)->after('provider_price')->comment('Markup added by platform');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['provider_price', 'platform_profit']);
        });

        Schema::table('hotel_bookings', function (Blueprint $table) {
            $table->dropColumn(['provider_price', 'platform_profit']);
        });
    }
};
