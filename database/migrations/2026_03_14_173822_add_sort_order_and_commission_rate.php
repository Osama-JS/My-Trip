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
        Schema::table('trip_itineraries', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('day_number');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(0.00)->after('logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_itineraries', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
        });
    }
};
