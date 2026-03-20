<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\TripsController;
use App\Http\Controllers\Admin\TripCategoryController;
use App\Http\Controllers\FrontendController;

// Customer Controllers
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\FavoriteController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\CustomerPaymentController;
use App\Http\Controllers\Customer\NotificationController as CustomerNotificationController;
use App\Http\Controllers\CompanyProfileController;


use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/trips', [FrontendController::class, 'trips'])->name('trips.index');
Route::get('/trips/{id}', [FrontendController::class, 'tripDetails'])->name('trips.show');
// Flight Routes
Route::get('/flights', [FrontendController::class, 'flights'])->name('flights');
Route::get('/flights/results', [FrontendController::class, 'flightResults'])->name('flights.results');
Route::get('/flights/revalidate', [FrontendController::class, 'flightRevalidate'])->name('flights.revalidate');
Route::get('/flights/booking', [FrontendController::class, 'flightBookingForm'])->name('flights.booking.form');
Route::post('/flights/book', [FrontendController::class, 'processFlightBooking'])->name('flights.book.process');
Route::get('/airports/search', [FrontendController::class, 'searchAirports'])->name('airports.search');
Route::get('/airports/sync', [FrontendController::class, 'syncAirports'])->name('airports.sync');

// Hotel Routes
Route::get('/hotels', [FrontendController::class, 'hotels'])->name('hotels');
Route::get('/hotels/results', [FrontendController::class, 'hotelResults'])->name('hotels.results');
Route::get('/hotels/room-rates', [FrontendController::class, 'hotelRoomRates'])->name('hotels.room_rates');
Route::get('/hotels/revalidate', [FrontendController::class, 'hotelRevalidate'])->name('hotels.revalidate');
Route::get('/hotels/booking', [FrontendController::class, 'hotelBookingForm'])->name('hotels.booking.form');
Route::post('/hotels/book', [FrontendController::class, 'processHotelBooking'])->name('hotels.book.process');
Route::get('/hotels/cities/search', [FrontendController::class, 'searchHotelCities'])->name('hotels.cities.search');

Route::get('/destinations', [FrontendController::class, 'destinations'])->name('destinations');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/search', [FrontendController::class, 'search'])->name('search');
Route::post('/book-trip', [FrontendController::class, 'bookTrip'])->name('book.trip')->middleware('auth');


// =============================================================================
// WEB VIEW PAYMENT ROUTES
// =============================================================================
Route::group(['prefix' => 'payments', 'as' => 'payments.web.'], function () {
    Route::get('/checkout/{booking_id}/{method}', [PaymentWebController::class, 'checkout'])->name('checkout');
    Route::post('/initiate', [PaymentWebController::class, 'initiateRedirect'])->name('initiate');
    Route::post('/bank-transfer', [PaymentWebController::class, 'submitBankTransfer'])->name('bank_transfer');
    Route::get('/success', [PaymentWebController::class, 'success'])->name('success');
    Route::get('/failure', [PaymentWebController::class, 'failure'])->name('failure');

    // Specialized callback that triggers verification then redirects to success/failure
    Route::get('/callback/{payment_type}', function (Illuminate\Http\Request $request, $payment_type) {
        $paymentId = $request->payment_id ?? $request->orderId ?? $request->id;
        $checkoutId = $request->id; // For HyperPay

        // We'll redirect to success or failure based on basic query params for now,
        // but ideally we verify here. For the WebView flow, we'll let the success/failure
        // pages handle the verification or use this intermediate route.
        if ($request->status === 'cancel') {
             return redirect()->route('payments.web.failure', ['error' => __('Payment cancelled by user.')]);
        }

        // Return a processing page that will then call the verify logic
        return view('payments.callback_processing', [
            'payment_type' => $payment_type,
            'payment_id' => $paymentId,
            'checkout_id' => $checkoutId,
            'status' => $request->status,
            'source' => $request->source
        ]);
    })->name('callback');
});


// Language switcher
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

// =============================================================================
// FRONTEND (PUBLIC) ROUTES
// =============================================================================
Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/trips', [FrontendController::class, 'trips'])->name('trips.index');
Route::get('/trips/{id}', [FrontendController::class, 'tripDetails'])->name('trips.show');
Route::get('/flights', [FrontendController::class, 'flights'])->name('flights');
Route::get('/hotels', [FrontendController::class, 'hotels'])->name('hotels');
Route::get('/destinations', [FrontendController::class, 'destinations'])->name('destinations');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/search', [FrontendController::class, 'search'])->name('search');
Route::post('/book-trip', [FrontendController::class, 'bookTrip'])->name('book.trip')->middleware('auth');

// Redirect root to login
Route::get('/login', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('customer.dashboard');
    }
    return redirect()->route('login');
});

// Default dashboard - redirect based on user type
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('customer.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes - Protected by isAdmin middleware
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('users/data', [UserController::class, 'getData'])->name('users.data');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', UserController::class);

    // Subscribers
    Route::get('subscribers', [UserController::class, 'subscribers'])->name('subscribers.index');
    Route::get('subscribers/data', [UserController::class, 'subscribersData'])->name('subscribers.data');

    // Role Management
    Route::get('roles/data', [RoleController::class, 'getData'])->name('roles.data');
    Route::resource('roles', RoleController::class);

    // Bookings
    Route::group(['prefix' => 'bookings', 'as' => 'bookings.'], function() {
        // Flights
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/data', [BookingController::class, 'getData'])->name('data');
        Route::get('/{id}/show', [BookingController::class, 'show'])->name('show');
        Route::get('/{id}/invoice', [BookingController::class, 'invoice'])->name('invoice');

        Route::get('flights/available', [BookingController::class, 'availableFlights'])->name('flights.available');
        Route::post('flights/search', [BookingController::class, 'searchFlights'])->name('flights.search');
        Route::post('flights/validate', [BookingController::class, 'validateFare'])->name('flights.validate');
        Route::post('flights/book', [BookingController::class, 'createBooking'])->name('flights.book');
        Route::get('flights/airports', [BookingController::class, 'getAirports'])->name('flights.airports');
        Route::get('flights/airlines', [BookingController::class, 'getAirlines'])->name('flights.airlines');
        Route::get('flights/requests', [BookingController::class, 'flightRequests'])->name('flights.requests');
        Route::get('flights/ongoing', [BookingController::class, 'ongoingFlights'])->name('flights.ongoing');

        // Hotels
        Route::get('hotels', [BookingController::class, 'hotelList'])->name('hotels.index');
        Route::get('hotels/requests', [BookingController::class, 'hotelRequests'])->name('hotels.requests');
    });

    // Settings
    Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // Question Management
    Route::get('questions/data', [App\Http\Controllers\Admin\QuestionController::class, 'getData'])->name('questions.data');
    Route::post('questions/{question}/toggle-status', [App\Http\Controllers\Admin\QuestionController::class, 'toggleStatus'])->name('questions.toggle-status');
    Route::resource('questions', App\Http\Controllers\Admin\QuestionController::class);


    // Countries Management
    Route::get('countries/data', [App\Http\Controllers\Admin\CountryController::class, 'getData'])->name('countries.data');
    Route::get('countries/active', [App\Http\Controllers\Admin\CountryController::class, 'getActiveCountries'])->name('countries.active');
    Route::post('countries/{country}/toggle-status', [App\Http\Controllers\Admin\CountryController::class, 'toggleStatus'])->name('countries.toggle-status');
    Route::resource('countries', App\Http\Controllers\Admin\CountryController::class);

    // Cities Management
    Route::get('cities/data', [App\Http\Controllers\Admin\CityController::class, 'getData'])->name('cities.data');
    Route::get('cities/by-country/{country}', [App\Http\Controllers\Admin\CityController::class, 'byCountry'])->name('cities.by-country');
    Route::post('cities/{city}/toggle-status', [App\Http\Controllers\Admin\CityController::class, 'toggleStatus'])->name('cities.toggle-status');
    Route::resource('cities', App\Http\Controllers\Admin\CityController::class);

     // companies Management
    Route::get('companies/data', [App\Http\Controllers\Admin\CompanyController::class, 'getData'])->name('companies.data');
    Route::get('companies/active', [App\Http\Controllers\Admin\CompanyController::class, 'getActivecompanies'])->name('companies.active');
    Route::post('companies/{company}/toggle-status', [App\Http\Controllers\Admin\CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');

    // Agent Management for Company
    Route::get('companies/{company}/agents', [App\Http\Controllers\Admin\CompanyAgentController::class, 'index'])->name('companies.agents');
    Route::get('companies/{company}/agents/data', [App\Http\Controllers\Admin\CompanyAgentController::class, 'getData'])->name('companies.agents.data');
    Route::post('companies/{company}/agents', [App\Http\Controllers\Admin\CompanyAgentController::class, 'store'])->name('companies.agents.store');
    Route::get('agents/{user}/edit', [App\Http\Controllers\Admin\CompanyAgentController::class, 'edit'])->name('companies.agents.edit');
    Route::put('agents/{user}', [App\Http\Controllers\Admin\CompanyAgentController::class, 'update'])->name('companies.agents.update');
    Route::post('agents/{user}/toggle-status', [App\Http\Controllers\Admin\CompanyAgentController::class, 'toggleStatus'])->name('companies.agents.toggle-status');
    Route::delete('agents/{user}', [App\Http\Controllers\Admin\CompanyAgentController::class, 'destroy'])->name('companies.agents.destroy');
    
    Route::resource('companies', App\Http\Controllers\Admin\CompanyController::class);


    Route::get('companycodes/data', [App\Http\Controllers\Admin\CompanyCodesController::class, 'getData'])->name('companycodes.data');
    Route::post('companycodes/{companycode}/toggle-status', [App\Http\Controllers\Admin\CompanyCodesController::class, 'toggleStatus'])->name('companycodes.toggle-status');
    Route::resource('companycodes', App\Http\Controllers\Admin\CompanyCodesController::class);

    // Banners Management
    Route::get('banners/data', [App\Http\Controllers\Admin\BannerController::class, 'getData'])->name('banners.data');
    Route::post('banners/{banner}/toggle-status', [App\Http\Controllers\Admin\BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
    Route::post('banners/reorder', [App\Http\Controllers\Admin\BannerController::class, 'reorder'])->name('banners.reorder');
    Route::resource('banners', App\Http\Controllers\Admin\BannerController::class);


     // Trips Management
    Route::get('trips/data', [App\Http\Controllers\Admin\TripsController::class, 'getData'])->name('trips.data');
    Route::post('trips/{trip}/toggle-status', [App\Http\Controllers\Admin\TripsController::class, 'toggleStatus'])->name('trips.toggle-status');
    Route::post('/trips/{trip}/renew', [App\Http\Controllers\Admin\TripsController::class, 'renew'])->name('trips.renew');
    Route::resource('trips', App\Http\Controllers\Admin\TripsController::class);
    Route::post('/trips/{trip}/images', [App\Http\Controllers\Admin\TripsController::class, 'imagestore'])->name('trips.images-store');
    Route::get('/trips/{id}/get-images', [App\Http\Controllers\Admin\TripsController::class, 'getImages'])->name('trips.get-images');
    Route::delete('/trips/{image}/destroyimages', [App\Http\Controllers\Admin\TripsController::class, 'imagedestroy'])->name('trips.images-destroy');

    // Trip Itinerary
    Route::get('/trips/{trip}/itinerary', [TripsController::class, 'itinerary'])->name('trips.itinerary');
    Route::post('/trips/{trip}/itinerary', [TripsController::class, 'storeItinerary'])->name('trips.itinerary.store');
    Route::put('/trips/itinerary/{itinerary}', [TripsController::class, 'updateItinerary'])->name('trips.itinerary.update');
    Route::post('/trips/itinerary/reorder', [TripsController::class, 'reorderItinerary'])->name('trips.itinerary.reorder');
    Route::delete('/trips/itinerary/{itinerary}', [TripsController::class, 'destroyItinerary'])->name('trips.itinerary.destroy');


    // Trip Categories
    Route::get('trip-categories/data', [TripCategoryController::class, 'getData'])->name('trip-categories.data');
    Route::get('trip-categories', [TripCategoryController::class, 'index'])->name('trip-categories.index');
    Route::get('trip-categories/all', [TripCategoryController::class, 'getAll'])->name('trip-categories.all');
    Route::resource('trip-categories', TripCategoryController::class)->except(['index']);

     // Trip Bookings Management
    Route::get('trip-bookings/data', [App\Http\Controllers\Admin\TripBookingController::class, 'getData'])->name('trip-bookings.data');
    Route::post('trip-bookings/{id}/update-status', [App\Http\Controllers\Admin\TripBookingController::class, 'updateStatus'])->name('trip-bookings.update-status');
    Route::post('trip-bookings/{id}/update-state', [App\Http\Controllers\Admin\TripBookingController::class, 'updateBookingState'])->name('trip-bookings.update-state');
    Route::post('trip-bookings/{id}/upload-ticket', [App\Http\Controllers\Admin\TripBookingController::class, 'uploadTicket'])->name('trip-bookings.upload-ticket');
    Route::post('trip-bookings/{id}/send-ticket', [App\Http\Controllers\Admin\TripBookingController::class, 'sendTicket'])->name('trip-bookings.send-ticket');
    Route::resource('trip-bookings', App\Http\Controllers\Admin\TripBookingController::class);


    // Notifications Management
    Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/data', [AdminNotificationController::class, 'getData'])->name('notifications.data');
    Route::get('notifications/search-users', [AdminNotificationController::class, 'searchUsers'])->name('notifications.search-users');
    Route::post('notifications/send', [AdminNotificationController::class, 'send'])->name('notifications.send');
    Route::delete('notifications/{id}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');


     // Payments Management
    Route::get('payments', [App\Http\Controllers\Admin\PaymentLogController::class, 'index'])->name('payments.index');
    Route::get('payments/{id}', [App\Http\Controllers\Admin\PaymentLogController::class, 'show'])->name('payments.show');


     // Bank Transfers Review
    Route::get('bank-transfers', [App\Http\Controllers\Admin\BankTransferController::class, 'index'])->name('bank-transfers.index');
    Route::get('bank-transfers/data', [App\Http\Controllers\Admin\BankTransferController::class, 'getData'])->name('bank-transfers.data');
    Route::get('bank-transfers/{id}', [App\Http\Controllers\Admin\BankTransferController::class, 'show'])->name('bank-transfers.show');
    // We use POST for actions to be safe
    Route::post('bank-transfers/{id}/approve', [App\Http\Controllers\Admin\BankTransferController::class, 'approve'])->name('bank-transfers.approve');
    Route::post('bank-transfers/{id}/reject', [App\Http\Controllers\Admin\BankTransferController::class, 'reject'])->name('bank-transfers.reject');


    // User Activity
    Route::get('users/{id}/activity', [UserController::class, 'activity'])->name('users.activity');



    // Permission Management
    Route::get('permissions/data', [PermissionController::class, 'getData'])->name('permissions.data');
    Route::resource('permissions', PermissionController::class);

    // Reports
    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function() {
        Route::get('api-logs', [App\Http\Controllers\Admin\ReportController::class, 'apiLogs'])->name('api_logs');
        Route::get('search-logs', [App\Http\Controllers\Admin\ReportController::class, 'searchLogs'])->name('search_logs');
    });
});



// =============================================================================
// AGENT ROUTES
// =============================================================================
Route::middleware(['auth', 'isAgent'])->prefix('agent')->name('agent.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Agent\AgentDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Agent\AgentProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Agent\AgentProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [\App\Http\Controllers\Agent\AgentProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::post('/profile/password', [\App\Http\Controllers\Agent\AgentProfileController::class, 'changePassword'])->name('profile.password');

    // Trips (Company-scoped)
    Route::get('/trips', [\App\Http\Controllers\Agent\AgentTripController::class, 'index'])->name('trips.index');

    Route::get('/trips/create', [\App\Http\Controllers\Agent\AgentTripController::class, 'create'])->name('trips.create');
    Route::post('/trips', [\App\Http\Controllers\Agent\AgentTripController::class, 'store'])->name('trips.store');
    Route::get('/trips/{trip}/edit', [\App\Http\Controllers\Agent\AgentTripController::class, 'edit'])->name('trips.edit');
    Route::put('/trips/{trip}', [\App\Http\Controllers\Agent\AgentTripController::class, 'update'])->name('trips.update');
    Route::delete('/trips/{trip}', [\App\Http\Controllers\Agent\AgentTripController::class, 'destroy'])->name('trips.destroy');

    // Trip Show / Details
    Route::get('/trips/{trip}/show', [\App\Http\Controllers\Agent\AgentTripController::class, 'show'])->name('trips.show');

    // Trip Images
    Route::post('/trips/{trip_id}/images', [\App\Http\Controllers\Agent\AgentTripController::class, 'imageStore'])->name('trips.images.store');
    Route::delete('/trips/images/{image}', [\App\Http\Controllers\Agent\AgentTripController::class, 'imageDestroy'])->name('trips.images.destroy');
    Route::get('/trips/{trip_id}/images', [\App\Http\Controllers\Agent\AgentTripController::class, 'getImages'])->name('trips.images.get');

    // Trip Itinerary
    Route::post('/trips/{trip}/itinerary', [\App\Http\Controllers\Agent\AgentTripController::class, 'storeItinerary'])->name('trips.itinerary.store');
    Route::post('/trips/itinerary/reorder', [\App\Http\Controllers\Agent\AgentTripController::class, 'reorderItinerary'])->name('trips.itinerary.reorder');
    Route::post('/trips/itinerary/{itinerary}', [\App\Http\Controllers\Agent\AgentTripController::class, 'updateItinerary'])->name('trips.itinerary.update');
    Route::delete('/trips/itinerary/{itinerary}', [\App\Http\Controllers\Agent\AgentTripController::class, 'destroyItinerary'])->name('trips.itinerary.destroy');

    // Bookings (Company-scoped)
    Route::get('/bookings', [\App\Http\Controllers\Agent\AgentBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Agent\AgentBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/tickets', [\App\Http\Controllers\Agent\AgentBookingController::class, 'uploadTickets'])->name('bookings.tickets');

    // Favorites
    Route::get('/favorites', [\App\Http\Controllers\Agent\AgentFavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{tripId}/toggle', [\App\Http\Controllers\Agent\AgentFavoriteController::class, 'toggle'])->name('favorites.toggle');
});

// =============================================================================
// CUSTOMER (USER) ROUTES
// =============================================================================
Route::middleware(['auth', 'isCustomer'])->prefix('customer')->name('customer.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Customer\CustomerDashboardController::class, 'index'])->name('dashboard');

    // Bookings
    Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create/{trip_id}', [CustomerBookingController::class, 'create'])->name('bookings.create');
    Route::get('/bookings/{id}', [CustomerBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings', [CustomerBookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{id}/cancel', [CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/bookings/{id}/invoice', [CustomerBookingController::class, 'downloadInvoice'])->name('bookings.invoice');

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{tripId}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Profile
    Route::get('/profile', [CustomerProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [CustomerProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::post('/profile/password', [CustomerProfileController::class, 'changePassword'])->name('profile.password');

    // Payments & Invoices
    Route::get('/payments', [CustomerPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/checkout/{bookingId}', [CustomerPaymentController::class, 'checkout'])->name('payments.checkout');
    Route::get('/payments/{bookingId}/invoice', [CustomerPaymentController::class, 'downloadInvoice'])->name('payments.invoice');

    // Notifications
    Route::get('/notifications', [CustomerNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [CustomerNotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [CustomerNotificationController::class, 'markAllRead'])->name('notifications.read-all');
});



// // Customer Routes
// Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
//     // Dashboard
//     Route::get('/dashboard', function () {
//         return view('customer.dashboard');
//     })->name('dashboard');

  

   

//     Route::get('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'index'])->name('profile');
//     Route::put('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('profile.update');
// });


// Payment Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/payment/{booking}', [App\Http\Controllers\Web\WebPaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{booking}', [App\Http\Controllers\Web\WebPaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/callback/{gateway}', [App\Http\Controllers\Web\WebPaymentController::class, 'callbackWithGateway'])->name('payment.callback.web');
});

require __DIR__.'/auth.php';
