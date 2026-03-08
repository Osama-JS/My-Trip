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
            Schema::create('hotel_search_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
             $table->unsignedBigInteger('booking_id')->nullable();
            $table->string('origin_code', 3);
            $table->string('destination_code', 3);
            $table->date('departure_date');
            $table->date('return_date')->nullable();
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->integer('infants')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_search_logs');
    }
};
