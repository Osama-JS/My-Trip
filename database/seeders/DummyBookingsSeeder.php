<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Booking;
use App\Models\FlightBooking;
use App\Models\HotelBooking;
use Illuminate\Support\Str;

class DummyBookingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::firstOrCreate(
            ['email' => 'hosammmm369@gmail.com'],
            [
                'name' => 'حسام المغبش',
                'password' => bcrypt('password123'),
            ]
        );

        // Create 5 Flight Bookings
        for ($i = 0; $i < 5; $i++) {
            $booking = Booking::create([
                'user_id' => $user->id,
                'booking_reference' => 'FL' . strtoupper(Str::random(6)),
                'status' => ['pending', 'confirmed', 'cancelled'][array_rand(['pending', 'confirmed', 'cancelled'])],
                'ticket_status' => 'ticketed',
                'total_amount' => rand(500, 3000),
                'currency' => 'SAR',
                'airline_name' => ['Saudia', 'Flynas', 'Emirates', 'Qatar Airways'][array_rand(['Saudia', 'Flynas', 'Emirates', 'Qatar Airways'])],
                'pnr_code' => strtoupper(Str::random(6)),
                'contact_email' => $user->email,
                'contact_phone' => '05' . rand(10000000, 99999999),
            ]);

            FlightBooking::create([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'origin' => ['JED', 'RUH', 'DMM', 'MED'][array_rand(['JED', 'RUH', 'DMM', 'MED'])],
                'destination' => ['DXB', 'LHR', 'CAI', 'CDG'][array_rand(['DXB', 'LHR', 'CAI', 'CDG'])],
                'departure_date' => now()->addDays(rand(1, 30)),
                'adults' => rand(1, 3),
                'childs' => rand(0, 2),
                'infants' => rand(0, 1),
                'flight_class' => ['Economy', 'Business', 'First'][array_rand(['Economy', 'Business', 'First'])],
                'flight_number' => 'FL' . rand(100, 999),
                'total_amount' => $booking->total_amount,
                'currency' => 'SAR',
            ]);
        }

        // Create 5 Hotel Bookings
        for ($i = 0; $i < 5; $i++) {
            HotelBooking::create([
                'user_id' => $user->id,
                'hotel_name' => ['Hilton', 'Marriott', 'Ritz Carlton', 'Four Seasons', 'Sheraton'][array_rand(['Hilton', 'Marriott', 'Ritz Carlton', 'Four Seasons', 'Sheraton'])],
                'hotel_id' => rand(1000, 9999),
                'city_name' => ['Dubai', 'London', 'Paris', 'New York', 'Riyadh'][array_rand(['Dubai', 'London', 'Paris', 'New York', 'Riyadh'])],
                'country_name' => ['UAE', 'UK', 'France', 'USA', 'Saudi Arabia'][array_rand(['UAE', 'UK', 'France', 'USA', 'Saudi Arabia'])],
                'check_in' => now()->addDays(rand(1, 30)),
                'check_out' => now()->addDays(rand(31, 40)),
                'rooms' => rand(1, 2),
                'adults' => rand(1, 4),
                'childs' => rand(0, 3),
                'total_price' => rand(800, 5000),
                'currency' => 'SAR',
                'status' => ['pending', 'confirmed', 'cancelled'][array_rand(['pending', 'confirmed', 'cancelled'])],
                'reference_num' => 'HTL' . strtoupper(Str::random(6)),
            ]);
        }
    }
}
