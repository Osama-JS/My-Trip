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
        Schema::create('hotel_cities', function (Blueprint $table) {
            $table->id();
            $table->string('city_code', 20)->unique()->index();
            $table->string('city_name_en')->index();
            $table->string('city_name_ar')->nullable()->index();
            $table->string('country_code', 10)->nullable();
            $table->string('country_name_en')->nullable();
            $table->string('country_name_ar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_cities');
    }
};
