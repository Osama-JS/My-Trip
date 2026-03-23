<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trip;
use App\Models\TripCategory;
use App\Models\TripImage;
use App\Models\TripItinerary;
use App\Models\TripBooking;
use App\Models\Payment;
use App\Models\User;
use App\Models\City;
use App\Models\Country;
use App\Models\Company;
use Illuminate\Support\Str;

class TourPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Categories
        $categories = [
            ['name_ar' => 'شهر العسل', 'name_en' => 'Honeymoon'],
            ['name_ar' => 'عائلي', 'name_en' => 'Family'],
            ['name_ar' => 'مغامرة', 'name_en' => 'Adventure'],
            ['name_ar' => 'ترفيهي', 'name_en' => 'Entertainment'],
            ['name_ar' => 'ثقافي', 'name_en' => 'Cultural'],
        ];

        foreach ($categories as $cat) {
            TripCategory::updateOrCreate(['name_en' => $cat['name_en']], $cat);
        }

        // 2. Prepare dynamic data
        $admin = User::where('user_type', 'admin')->first() ?? User::first();
        $company = Company::first();
        $country = Country::first();
        $city = City::where('country_id', $country->id)->first() ?? City::first();

        if (!$admin || !$company || !$country || !$city) {
            $this->command->warn('Missing essential data (Admin, Company, Country, or City). Please seed them first.');
            return;
        }

        // 3. Create realistic Trips
        $tripsData = [
            [
                'title_ar' => 'رحلة ساحرة إلى جزر المالديف',
                'title_en' => 'Magical Trip to Maldives',
                'description_ar' => 'استمتع بإقامة فاخرة لمدة 5 أيام في أجمل جزر المالديف، تشمل الطيران والإقامة في منتجع 5 نجوم مع وجبات الإفطار والعشاء.',
                'description_en' => 'Enjoy a 5-day luxury stay in the most beautiful islands of Maldives, including flights and stay in a 5-star resort with half-board.',
                'price' => 4500,
                'price_before_discount' => 5200,
                'duration' => 5,
                'personnel_capacity' => 20,
                'expiry_date' => now()->addMonths(3),
            ],
            [
                'title_ar' => 'مغامرة في جبال الألب السويسرية',
                'title_en' => 'Adventure in Swiss Alps',
                'description_ar' => 'رحلة مليئة بالإثارة والمغامرة في قلب سويسرا، تسلق الجبال والتزلج في الشتاء، أو المشي لمسافات طويلة في الصيف.',
                'description_en' => 'A trip full of excitement and adventure in the heart of Switzerland, mountain climbing and skiing in winter, or hiking in summer.',
                'price' => 3800,
                'price_before_discount' => 4000,
                'duration' => 7,
                'personnel_capacity' => 15,
                'expiry_date' => now()->addMonths(2),
            ],
            [
                'title_ar' => 'جولة ثقافية في إسطنبول التاريخية',
                'title_en' => 'Cultural Tour in Historical Istanbul',
                'description_ar' => 'اكتشف عبق التاريخ في إسطنبول، زيارة آيا صوفيا والجامع الأزرق والبازار الكبير مع جولة بحرية في البوسفور.',
                'description_en' => 'Discover the scent of history in Istanbul, visit Hagia Sophia, the Blue Mosque, and the Grand Bazaar with a Bosphorus cruise.',
                'price' => 1200,
                'price_before_discount' => 1500,
                'duration' => 4,
                'personnel_capacity' => 30,
                'expiry_date' => now()->addMonths(1),
            ],
        ];

        foreach ($tripsData as $data) {
            $trip = Trip::create(array_merge($data, [
                'company_id' => $company->id,
                'from_country_id' => $country->id,
                'from_city_id' => $city->id,
                'to_country_id' => $country->id,
                'to_city_id' => $city->id,
                'admin_id' => $admin->id,
                'is_public' => true,
                'active' => true,
                'is_featured' => true,
                'tickets' => 50,
                'base_capacity' => 2,
                'extra_passenger_price' => 200,
            ]));

            // Attach random categories
            $trip->categories()->attach(TripCategory::inRandomOrder()->take(2)->pluck('id'));

            // Create Itineraries
            for ($i = 1; $i <= 3; $i++) {
                TripItinerary::create([
                    'trip_id' => $trip->id,
                    'day_number' => $i,
                    'sort_order' => $i,
                    'title' => 'Day ' . $i . ': Activity Title',
                    'description' => 'Detailed description for day ' . $i . ' activities and visits.',
                ]);
            }

            // Create placeholder image
            TripImage::create([
                'trip_id' => $trip->id,
                'image_path' => 'trips/default.jpg',
            ]);

            // Create some bookings for each trip
            $users = User::where('user_type', 'customer')->take(2)->get();
            if ($users->isEmpty()) {
                $user = User::create([
                    'first_name' => 'Demo',
                    'last_name' => 'Customer',
                    'email' => 'customer@example.com',
                    'password' => bcrypt('password'),
                    'user_type' => 'customer',
                    'status' => 'active',
                ]);
                $users = collect([$user]);
            }
            foreach ($users as $user) {
                $booking = TripBooking::create([
                    'user_id' => $user->id,
                    'trip_id' => $trip->id,
                    'status' => 'confirmed',
                    'total_price' => $trip->price,
                    'booking_date' => now()->subDays(rand(1, 10)),
                    'tickets_count' => 1,
                    'booking_state' => TripBooking::STATE_COMPLETED,
                ]);

                // Create a payment for the booking
                Payment::create([
                    'payable_id' => $booking->id,
                    'payable_type' => TripBooking::class,
                    'user_id' => $user->id,
                    'payment_gateway' => 'hyperpay',
                    'payment_method' => 'Credit Card',
                    'transaction_id' => 'TXN_' . Str::random(10),
                    'amount' => $booking->total_price,
                    'currency' => 'USD',
                    'status' => 'paid',
                ]);
            }
        }
    }
}
