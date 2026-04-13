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
        Schema::table('airports', function (Blueprint $table) {
            $table->string('airport_name_ar')->nullable()->after('airport_name');
            $table->string('city_name_ar')->nullable()->after('city_name');
            $table->string('country_name_ar')->nullable()->after('country_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('airports', function (Blueprint $table) {
            $table->dropColumn(['airport_name_ar', 'city_name_ar', 'country_name_ar']);
        });
    }
};
