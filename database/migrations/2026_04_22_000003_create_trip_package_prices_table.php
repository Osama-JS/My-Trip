<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_package_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('trip_packages')->onDelete('cascade');
            $table->foreignId('season_id')->nullable()->constrained('trip_seasons')->onDelete('cascade');
            $table->enum('occupancy_type', [
                '2pax_1room',    // شخصان في غرفة واحدة
                '3pax_1room',    // 3 أشخاص في غرفة واحدة
                '3_4pax_2rooms', // 3 أو 4 أشخاص في غرفتين
                '5pax_2rooms',   // 5 أشخاص في غرفتين
            ]);
            $table->decimal('price', 10, 2)->comment('Total price for the whole group');
            $table->timestamps();

            $table->index('package_id');
            $table->index('season_id');
            $table->unique(['package_id', 'season_id', 'occupancy_type'], 'pkg_season_occupancy_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_package_prices');
    }
};
