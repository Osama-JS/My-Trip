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
        Schema::table('countries', function (Blueprint $table) {
            if (!Schema::hasColumn('countries', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('id');
            }
            if (!Schema::hasColumn('countries', 'name_en')) {
                $table->string('name_en')->nullable()->after('name_ar');
            }
        });

        // Migrate data Safely
        \Illuminate\Support\Facades\DB::table('countries')->get()->each(function ($country) {
            \Illuminate\Support\Facades\DB::table('countries')
                ->where('id', $country->id)
                ->update([
                    'name_ar' => $country->name,
                    'name_en' => $country->nicename
                ]);
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['name', 'nicename']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->string('nicename')->nullable()->after('name');
        });

        \Illuminate\Support\Facades\DB::table('countries')->get()->each(function ($country) {
            \Illuminate\Support\Facades\DB::table('countries')
                ->where('id', $country->id)
                ->update([
                    'name' => $country->name_ar,
                    'nicename' => $country->name_en
                ]);
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['name_ar', 'name_en']);
        });
    }
};
