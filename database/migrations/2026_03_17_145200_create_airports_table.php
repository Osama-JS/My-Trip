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
        Schema::create('airports', function (Blueprint $row) {
            $row->id();
            $row->string('airport_code', 10)->unique()->index();
            $row->string('airport_name')->nullable();
            $row->string('city_code', 10)->nullable();
            $row->string('city_name')->nullable();
            $row->string('country_code', 10)->nullable();
            $row->string('country_name')->nullable();
            $row->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airports');
    }
};
