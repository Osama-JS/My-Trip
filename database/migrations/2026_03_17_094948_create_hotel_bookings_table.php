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
        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('hotel_name');
            $table->string('hotel_id');
            $table->string('city_name')->nullable();
            $table->string('country_name')->nullable();
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('rooms')->default(1);
            $table->integer('adults')->default(1);
            $table->integer('childs')->default(0);
            $table->decimal('total_price', 10, 2);
            $table->string('currency', 3)->default('SAR');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'failed'])->default('pending');
            $table->string('reference_num')->nullable();
            $table->string('supplier_confirmation_num')->nullable();
            $table->string('session_id')->nullable();
            $table->string('product_id')->nullable();
            $table->string('token_id')->nullable();
            $table->json('pax_details')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('hotel_id');
            $table->index(['reference_num', 'supplier_confirmation_num'], 'hotel_booking_refs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_bookings');
    }
};
