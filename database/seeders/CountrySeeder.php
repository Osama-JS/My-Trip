<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['SA', 'Saudi Arabia', '966'],
            ['AE', 'United Arab Emirates', '971'],
            ['QA', 'Qatar', '974'],
            ['KW', 'Kuwait', '965'],
            ['OM', 'Oman', '968'],
            ['BH', 'Bahrain', '973'],
            ['EG', 'Egypt', '20'],
            ['JO', 'Jordan', '962'],
            ['LB', 'Lebanon', '961'],
            ['SY', 'Syria', '963'],
            ['IQ', 'Iraq', '964'],
            ['YE', 'Yemen', '967'],
            ['PS', 'Palestine', '970'],
            ['MA', 'Morocco', '212'],
            ['DZ', 'Algeria', '213'],
            ['TN', 'Tunisia', '216'],
            ['LY', 'Libya', '218'],
            ['SD', 'Sudan', '249'],
            ['US', 'United States', '1'],
            ['GB', 'United Kingdom', '44'],
            ['FR', 'France', '33'],
            ['DE', 'Germany', '49'],
            ['IT', 'Italy', '39'],
            ['ES', 'Spain', '34'],
            ['TR', 'Turkey', '90'],
            ['IN', 'India', '91'],
            ['PK', 'Pakistan', '92'],
            ['BD', 'Bangladesh', '880'],
            ['PH', 'Philippines', '63'],
            ['ID', 'Indonesia', '62'],
            ['MY', 'Malaysia', '60'],
            ['TH', 'Thailand', '66'],
            ['CN', 'China', '86'],
            ['JP', 'Japan', '81'],
            ['KR', 'South Korea', '82'],
            ['RU', 'Russia', '7'],
            ['BR', 'Brazil', '55'],
            ['CA', 'Canada', '1'],
            ['AU', 'Australia', '61'],
            ['MV', 'Maldives', '960'],
        ];

        foreach ($countries as $c) {
            \App\Models\Country::updateOrCreate(
                ['iso' => $c[0]],
                [
                    'name' => strtoupper($c[1]),
                    'nicename' => $c[1],
                    'phonecode' => $c[2],
                    'active' => true
                ]
            );
        }
    }
}
