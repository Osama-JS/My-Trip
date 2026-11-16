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
        Schema::table('hotel_bookings', function (Blueprint $table) {
            $table->text('cancellation_policy')->nullable()->after('currency');
            $table->decimal('cancellation_fee', 10, 2)->nullable()->after('cancellation_policy');
            $table->decimal('refund_amount', 10, 2)->nullable()->after('cancellation_fee');
            $table->timestamp('cancelled_at')->nullable()->after('refund_amount');
            $table->string('cancel_reference_num')->nullable()->after('cancelled_at');
            $table->text('cancellation_reason')->nullable()->after('cancel_reference_num');
            $table->string('contact_email')->nullable()->after('cancellation_reason');
            $table->string('contact_phone')->nullable()->after('contact_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotel_bookings', function (Blueprint $table) {
            $table->dropColumn([
                'cancellation_policy',
                'cancellation_fee',
                'refund_amount',
                'cancelled_at',
                'cancel_reference_num',
                'cancellation_reason',
                'contact_email',
                'contact_phone'
            ]);
        });
    }
};
