<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Trip;
use App\Models\TripCategory;
use App\Models\TripSeason;
use App\Models\TripPackage;
use App\Models\TripPackagePrice;
use App\Models\TripAddon;
use App\Models\TripItinerary;
use App\Models\Country;
use App\Models\City;
use Illuminate\Support\Str;

class ComprehensiveTripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Try to find a country and city to attach to the trip
        $country = Country::first();
        if (!$country) {
            $country = new Country();
            $country->save();
        }

        $city = City::first();
        if (!$city) {
            $city = new City();
            $city->country_id = $country->id;
            $city->save();
        }

        $company = \App\Models\Company::first();
        if (!$company) {
            $company = new \App\Models\Company();
            $company->name = 'Wjhtak Tourism';
            $company->save();
        }

        // 1. Create the Main Trip (Package)
        $trip = Trip::create([
            'company_id' => $company->id,
            'title_en' => 'Ultimate Istanbul & Cappadocia Experience',
            'title_ar' => 'التجربة الشاملة في اسطنبول وكابادوكيا',
            'description_en' => 'Discover the magic of Turkey with this comprehensive package including hot air balloon rides, historical tours, and luxury stays.',
            'description_ar' => 'اكتشف سحر تركيا مع هذا البكج الشامل الذي يتضمن جولات المنطاد، زيارات تاريخية، وإقامة فاخرة.',
            'price' => 1500.00,
            'price_before_discount' => 1800.00,
            'tickets' => 50,
            'duration' => '7 Days',
            'from_country_id' => $country->id,
            'from_city_id' => $city->id,
            'to_country_id' => $country->id,
            'to_city_id' => $city->id,
            'active' => 1,
            'is_featured' => 1,
            'expiry_date' => Carbon::now()->addMonths(6),
            'base_capacity' => 2,
            'extra_passenger_price' => 300.00,
        ]);

        // 2. Attach a Category
        $category = TripCategory::firstOrCreate(
            ['name_en' => 'Luxury Packages'],
            ['name_ar' => 'بكجات فاخرة']
        );
        $trip->categories()->attach($category->id);

        // 3. Create Seasons
        $summerSeason = TripSeason::create([
            'trip_id' => $trip->id,
            'name_en' => 'Summer Peak Season',
            'name_ar' => 'موسم الصيف (الذروة)',
            'start_date' => Carbon::now()->addDays(10),
            'end_date' => Carbon::now()->addMonths(3),
            'is_active' => 1,
        ]);

        $winterSeason = TripSeason::create([
            'trip_id' => $trip->id,
            'name_en' => 'Winter Promo Season',
            'name_ar' => 'موسم الشتاء (تخفيضات)',
            'start_date' => Carbon::now()->addMonths(4),
            'end_date' => Carbon::now()->addMonths(7),
            'is_active' => 1,
        ]);

        // 4. Create Packages (Hotels/Tiers)
        $standardPackage = TripPackage::create([
            'trip_id' => $trip->id,
            'name_en' => 'Standard Package (4 Stars)',
            'name_ar' => 'البكج الاقتصادي (4 نجوم)',
            'hotel_name' => 'City Center Hotel Istanbul',
            'hotel_stars' => 4,
            'tier' => 'economy',
            'sort_order' => 1,
        ]);

        $vipPackage = TripPackage::create([
            'trip_id' => $trip->id,
            'name_en' => 'VIP Package (5 Stars)',
            'name_ar' => 'بكج كبار الشخصيات (5 نجوم)',
            'hotel_name' => 'Bosphorus Luxury Palace',
            'hotel_stars' => 5,
            'tier' => 'vip',
            'sort_order' => 2,
        ]);

        // 5. Assign Prices for Packages and Seasons
        $occupancyTypes = ['single', 'double', 'triple', 'child'];

        // Standard Package Prices
        foreach ($occupancyTypes as $type) {
            $basePrice = match($type) {
                'single' => 1000,
                'double' => 800,
                'triple' => 700,
                'child' => 400,
            };

            TripPackagePrice::create([
                'package_id' => $standardPackage->id,
                'season_id' => $summerSeason->id,
                'occupancy_type' => $type,
                'price' => $basePrice + 200, // Summer surge
            ]);

            TripPackagePrice::create([
                'package_id' => $standardPackage->id,
                'season_id' => $winterSeason->id,
                'occupancy_type' => $type,
                'price' => $basePrice,
            ]);
        }

        // VIP Package Prices
        foreach ($occupancyTypes as $type) {
            $basePrice = match($type) {
                'single' => 2000,
                'double' => 1500,
                'triple' => 1300,
                'child' => 800,
            };

            TripPackagePrice::create([
                'package_id' => $vipPackage->id,
                'season_id' => $summerSeason->id,
                'occupancy_type' => $type,
                'price' => $basePrice + 500, // Summer VIP surge
            ]);

            TripPackagePrice::create([
                'package_id' => $vipPackage->id,
                'season_id' => $winterSeason->id,
                'occupancy_type' => $type,
                'price' => $basePrice,
            ]);
        }

        // 6. Create Addons
        TripAddon::create([
            'trip_id' => $trip->id,
            'name_en' => 'Hot Air Balloon in Cappadocia',
            'name_ar' => 'ركوب المنطاد في كابادوكيا',
            'extra_cost' => 250.00,
        ]);

        TripAddon::create([
            'trip_id' => $trip->id,
            'name_en' => 'Bosphorus Dinner Cruise',
            'name_ar' => 'عشاء بحري في مضيق البوسفور',
            'extra_cost' => 80.00,
        ]);

        TripAddon::create([
            'trip_id' => $trip->id,
            'name_en' => 'Airport VIP Transfer',
            'name_ar' => 'نقل VIP من المطار',
            'extra_cost' => 120.00,
        ]);

        // 7. Create Itineraries
        TripItinerary::create([
            'trip_id' => $trip->id,
            'day_number' => 1,
            'title' => 'Arrival & Check-in',
            'description' => 'Arrival at Istanbul airport, VIP transfer to the hotel, and rest.',
            'sort_order' => 1,
        ]);

        TripItinerary::create([
            'trip_id' => $trip->id,
            'day_number' => 2,
            'title' => 'Historical Peninsula Tour',
            'description' => 'Visit Hagia Sophia, Blue Mosque, and Topkapi Palace.',
            'sort_order' => 2,
        ]);

        TripItinerary::create([
            'trip_id' => $trip->id,
            'day_number' => 3,
            'title' => 'Flight to Cappadocia',
            'description' => 'Domestic flight to Cappadocia, check-in to cave hotel.',
            'sort_order' => 3,
        ]);

        $this->command->info('Comprehensive Trip (Package) seeded successfully! Trip ID: ' . $trip->id);
    }
}
