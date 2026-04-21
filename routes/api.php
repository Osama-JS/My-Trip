<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppSettingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\HotelController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Discovery Routes
Route::prefix('v1')->group(function () {
    Route::get('/countries', [\App\Http\Controllers\Api\V1\DiscoveryController::class, 'getCountries']);
    Route::get('/cities', [\App\Http\Controllers\Api\V1\DiscoveryController::class, 'getCities']);
    Route::get('/banners', [\App\Http\Controllers\Api\V1\DiscoveryController::class, 'getBanners']);
    Route::get('/locations', [\App\Http\Controllers\Api\V1\DiscoveryController::class, 'getLocations']);
    Route::get('/faqs', [\App\Http\Controllers\Api\V1\DiscoveryController::class, 'getFaqs']);
    Route::get('/categories', [\App\Http\Controllers\Api\V1\DiscoveryController::class, 'getCategories']);

    // Trips
    Route::get('/trips/featured', [\App\Http\Controllers\Api\V1\TripController::class, 'featured']);
    Route::get('/trips', [\App\Http\Controllers\Api\V1\TripController::class, 'index']);
    Route::get('/trips/{id}', [\App\Http\Controllers\Api\V1\TripController::class, 'show']);
    Route::post('/trips/book', [\App\Http\Controllers\Api\V1\TripController::class, 'book'])->middleware('auth:sanctum');

    // My Bookings & Favorites
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/my-bookings', [\App\Http\Controllers\Api\V1\TripController::class, 'myBookings']);
        Route::get('/bookings/{id}', [\App\Http\Controllers\Api\V1\TripController::class, 'bookingDetails']);
        Route::get('/bookings/{id}/invoice', [\App\Http\Controllers\Api\V1\TripController::class, 'downloadInvoice']);
        Route::get('/bookings/{id}/ticket', [\App\Http\Controllers\Api\V1\TripController::class, 'downloadTicket']);
        Route::post('/bookings/{id}/cancel', [\App\Http\Controllers\Api\V1\TripController::class, 'cancelPendingBooking']);
        Route::get('/favorites', [\App\Http\Controllers\Api\V1\TripController::class, 'getFavorites']);
        Route::post('/trips/{id}/favorite', [\App\Http\Controllers\Api\V1\TripController::class, 'toggleFavorite']);

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [\App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy']);
    });
});

// Public Routes
Route::get('/app-settings', [AppSettingController::class, 'index']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');

// Flight Routes
Route::post('/flights/search', [FlightController::class, 'search']);
Route::get('/flights/airports', [FlightController::class, 'getAirports']);
Route::get('/flights/airlines', [FlightController::class, 'getAirlines']);
Route::post('/flights/validate-fare', [FlightController::class, 'validateFare']);

// Hotel Routes
Route::post('/hotels/search', [HotelController::class, 'search']);
Route::post('/hotels/next-page', [HotelController::class, 'nextToken']);
Route::post('/hotels/filter', [HotelController::class, 'filter']);
Route::post('/hotels/content', [HotelController::class, 'getHotelContent']);
Route::post('/hotels/room-rates', [HotelController::class, 'roomRates']);
Route::post('/hotels/check-rates', [HotelController::class, 'checkRates']);
Route::get('/hotels/cities', [HotelController::class, 'getCities']);
Route::get('/hotels/languages', [HotelController::class, 'getLanguages']);

Route::get('/payment/methods', [PaymentController::class, 'methods']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Flight Booking Routes (Now Protected)
    Route::post('/flights/book', [FlightController::class, 'book']);
    Route::post('/flights/order-ticket', [FlightController::class, 'orderTicket']);
    Route::post('/flights/trip-details', [FlightController::class, 'getTripDetails']);

    // Hotel Booking Routes (Protected)
    Route::post('/hotels/book', [HotelController::class, 'book']);
    Route::post('/hotels/cancel', [HotelController::class, 'cancel']);
    Route::post('/hotels/booking-details', [HotelController::class, 'getBookingDetails']);

    Route::get('/check-token', [AuthController::class, 'checkToken']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
    Route::post('/profile/change-password', [AuthController::class, 'changePassword']);
    Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);

    // Payment Routes
    Route::prefix('payment')->group(function () {
        Route::post('/initiate', [PaymentController::class, 'initiate']);
        Route::post('/verify', [PaymentController::class, 'verify']);
    });

    // User Bookings (Unified)
    Route::get('/user/bookings', [App\Http\Controllers\Api\UserBookingController::class, 'index']); // Flights
    Route::get('/user/bookings/{id}', [App\Http\Controllers\Api\UserBookingController::class, 'show']);

    Route::get('/user/hotel-bookings', [App\Http\Controllers\Api\UserBookingController::class, 'hotelBookings']);
    Route::get('/user/hotel-bookings/{id}', [App\Http\Controllers\Api\UserBookingController::class, 'hotelBookingDetails']);
    Route::get('/user/hotel-bookings/{id}/voucher', [App\Http\Controllers\Api\UserBookingController::class, 'downloadHotelVoucher']);

    Route::get('/user/trip-bookings', [App\Http\Controllers\Api\UserBookingController::class, 'tripBookings']);
    Route::get('/user/trip-bookings/{id}', [App\Http\Controllers\Api\UserBookingController::class, 'tripBookingDetails']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
