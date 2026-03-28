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
        Schema::table('hotel_cities', function (Blueprint $table) {
            $table->dropUnique('hotel_cities_city_code_unique');
            $table->unique(['city_name_en', 'country_name_en'], 'hotel_cities_name_country_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotel_cities', function (Blueprint $table) {
            $table->dropUnique('hotel_cities_name_country_unique');
            $table->unique('city_code');
        });
    }
};
