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
        Schema::table('trip_addons', function (Blueprint $table) {
            $table->enum('pricing_type', ['per_person', 'fixed_per_booking'])->default('per_person')->after('is_replacement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_addons', function (Blueprint $table) {
            $table->dropColumn('pricing_type');
        });
    }
};
