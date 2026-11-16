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
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('cancellation_fee', 10, 2)->nullable()->after('currency');
            $table->decimal('refund_amount', 10, 2)->nullable()->after('cancellation_fee');
            $table->timestamp('cancelled_at')->nullable()->after('refund_amount');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['cancellation_fee', 'refund_amount', 'cancelled_at', 'cancellation_reason']);
        });
    }
};
