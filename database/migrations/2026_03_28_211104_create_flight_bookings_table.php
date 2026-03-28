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
        Schema::create('flight_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            
            $table->string('origin');
            $table->string('destination');
            $table->date('departure_date');
            $table->date('return_date')->nullable();
            
            $table->integer('adults')->default(1);
            $table->integer('childs')->default(0);
            $table->integer('infants')->default(0);
            
            $table->string('flight_class')->default('Economy');
            $table->text('itinerary_data')->nullable(); // Store raw JSON if needed
            
            $table->decimal('total_amount', 10, 2);
            $table->string('currency')->default('SAR');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_bookings');
    }
};
