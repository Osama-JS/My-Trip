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
        Schema::table('booking_passengers', function (Blueprint $table) {
            $table->string('first_name')->after('name')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
            $table->string('title', 20)->after('last_name')->nullable();
            $table->date('dob')->after('title')->nullable();
            $table->string('passenger_type', 20)->after('dob')->nullable(); // adult, child, infant
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_passengers', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'title', 'dob', 'passenger_type']);
        });
    }

};
