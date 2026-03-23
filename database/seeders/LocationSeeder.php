<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $saudi = \App\Models\Country::updateOrCreate(['name' => 'SAUDI ARABIA'], [
            'nicename' => 'Saudi Arabia',
            'iso' => 'SA',
            'phonecode' => '966',
            'active' => true,
        ]);

        \App\Models\City::updateOrCreate(['title_en' => 'Riyadh'], [
            'title_ar' => 'الرياض',
            'country_id' => $saudi->id,
            'active' => true,
        ]);

        $maldives = \App\Models\Country::updateOrCreate(['name' => 'MALDIVES'], [
            'nicename' => 'Maldives',
            'iso' => 'MV',
            'phonecode' => '960',
            'active' => true,
        ]);

        \App\Models\City::updateOrCreate(['title_en' => 'Male'], [
            'title_ar' => 'ماليه',
            'country_id' => $maldives->id,
            'active' => true,
        ]);
    }
}
