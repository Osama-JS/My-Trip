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
        Schema::table('trip_bookings', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->after('trip_id')->constrained('trip_packages')->onDelete('set null');
            $table->foreignId('season_id')->nullable()->after('package_id')->constrained('trip_seasons')->onDelete('set null');
            $table->enum('occupancy', ['single', 'double', 'triple', 'child'])->nullable()->after('season_id');
        });
    }

    public function down(): void
    {
        Schema::table('trip_bookings', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropForeign(['season_id']);
            $table->dropColumn(['package_id', 'season_id', 'occupancy']);
        });
    }
};
