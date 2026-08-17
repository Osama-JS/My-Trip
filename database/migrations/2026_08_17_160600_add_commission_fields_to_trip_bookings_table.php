<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trip_bookings', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('trip_id')->constrained('companies')->nullOnDelete();
            $table->string('commission_type', 20)->nullable()->after('total_price')->comment('percentage or fixed');
            $table->decimal('commission_value', 10, 2)->default(0)->after('commission_type')->comment('Rate % or fixed amount per pax');
            $table->decimal('platform_profit', 10, 2)->default(0)->after('commission_value')->comment('Platform commission earned');
            $table->decimal('provider_price', 10, 2)->default(0)->after('platform_profit')->comment('Company net earnings = total_price - platform_profit');
        });

        // Backfill existing trip bookings
        try {
            $bookings = DB::table('trip_bookings')
                ->join('trips', 'trip_bookings.trip_id', '=', 'trips.id')
                ->leftJoin('companies', 'trips.company_id', '=', 'companies.id')
                ->select(
                    'trip_bookings.id as booking_id',
                    'trip_bookings.total_price',
                    'trip_bookings.tickets_count',
                    'trips.company_id as trip_company_id',
                    'companies.commission_type as comp_comm_type',
                    'companies.commission_value as comp_comm_val',
                    'companies.commission_rate as comp_comm_rate'
                )
                ->get();

            foreach ($bookings as $b) {
                $companyId = $b->trip_company_id;
                $commType = $b->comp_comm_type ?? 'percentage';
                $commVal = floatval($b->comp_comm_val ?? $b->comp_comm_rate ?? 0);
                $totalPrice = floatval($b->total_price ?? 0);
                $ticketsCount = max(1, intval($b->tickets_count ?? 1));

                $profit = 0;
                if ($commVal > 0) {
                    if ($commType === 'percentage') {
                        $profit = ($totalPrice * $commVal) / 100;
                    } else {
                        $profit = $commVal * $ticketsCount;
                    }
                }
                $providerPrice = max(0, $totalPrice - $profit);

                DB::table('trip_bookings')->where('id', $b->booking_id)->update([
                    'company_id'       => $companyId,
                    'commission_type'  => $commType,
                    'commission_value' => $commVal,
                    'platform_profit'  => $profit,
                    'provider_price'   => $providerPrice,
                ]);
            }
        } catch (\Exception $e) {
            // Log if needed but don't fail migration structure
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_bookings', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn([
                'company_id',
                'commission_type',
                'commission_value',
                'platform_profit',
                'provider_price',
            ]);
        });
    }
};
