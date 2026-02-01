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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('booking_reference', 50); // PNR / UniqueID
            $table->string('supplier_session_id')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'refunded', 'failed'])->default('pending');
            $table->enum('ticket_status', ['booked', 'ticketed', 'voided', 'reissued'])->default('booked');
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('SAR');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->timestamp('pnr_created_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
