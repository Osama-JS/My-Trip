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
use App\Http\Controllers\Web\PaymentWebController;

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
Route::get('/flights/booking', [FrontendController::class, 'flightBookingForm'])->name('flights.booking.form')->middleware('auth');
Route::post('/flights/book', [FrontendController::class, 'processFlightBooking'])->name('flights.book.process')->middleware('auth');
Route::get('/flights/payment/{booking_id}', [FrontendController::class, 'flightSelectPayment'])->name('flights.payment.select')->middleware('auth');
Route::get('/airports/search', [FrontendController::class, 'searchAirports'])->name('airports.search');
Route::get('/airports/sync', [FrontendController::class, 'syncAirports'])->name('airports.sync');

Route::post('/ocr/passport', [\App\Http\Controllers\OcrController::class, 'scanPassport'])->name('ocr.passport')->middleware('auth');

// Hotel Routes
Route::get('/hotels', [FrontendController::class, 'hotels'])->name('hotels');
Route::get('/hotels/results', [FrontendController::class, 'hotelResults'])->name('hotels.results');
Route::get('/hotels/load-more', [FrontendController::class, 'hotelLoadMore'])->name('hotels.load_more');
Route::get('/hotels/room-rates', [FrontendController::class, 'hotelRoomRates'])->name('hotels.room_rates');
Route::get('/hotels/detail/{hotelId}', [FrontendController::class, 'hotelDetails'])->name('hotels.details');
Route::get('/hotels/revalidate', [FrontendController::class, 'hotelRevalidate'])->name('hotels.revalidate');
Route::get('/hotels/booking', [FrontendController::class, 'hotelBookingForm'])->name('hotels.booking.form')->middleware('auth');
Route::post('/hotels/book', [FrontendController::class, 'processHotelBooking'])->name('hotels.book.process')->middleware('auth');
Route::get('/hotels/payment/{booking_id}', [FrontendController::class, 'hotelSelectPayment'])->name('hotels.payment.select')->middleware('auth');
Route::get('/trips/payment/{booking_id}', [FrontendController::class, 'tripSelectPayment'])->name('trips.payment.select')->middleware('auth');
Route::get('/hotels/cities/search', [FrontendController::class, 'searchHotelCities'])->name('hotels.cities.search');

Route::get('/destinations', [FrontendController::class, 'destinations'])->name('destinations');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/search', [FrontendController::class, 'search'])->name('search');
Route::get('/book-trip/form', [FrontendController::class, 'tripBookingForm'])->name('trips.booking.form')->middleware('auth');
Route::post('/book-trip', [FrontendController::class, 'bookTrip'])->name('book.trip')->middleware('auth');

// Guest Profile Completion
Route::middleware('auth')->group(function () {
    Route::get('/profile/complete', [CustomerProfileController::class, 'completeProfileForm'])->name('profile.complete.form');
    Route::post('/profile/complete', [CustomerProfileController::class, 'submitCompleteProfile'])->name('profile.complete.submit');
});

// Dynamic Pages
Route::get('/p/{slug}', [FrontendController::class, 'showPage'])->name('pages.show');


// =============================================================================
// WEB VIEW PAYMENT ROUTES
// =============================================================================
Route::group(['prefix' => 'payments', 'as' => 'payments.web.'], function () {
    Route::get('/checkout/{booking_id}/{method}', [PaymentWebController::class, 'checkout'])->name('checkout');
    Route::post('/initiate', [PaymentWebController::class, 'initiateRedirect'])->name('initiate');
    Route::post('/bank-transfer', [PaymentWebController::class, 'submitBankTransfer'])->name('bank_transfer');
    // Public verify endpoint — called from callback_processing.blade.php (no Sanctum token needed)
    Route::post('/verify', [PaymentWebController::class, 'webVerify'])->name('verify');
    Route::get('/success', [PaymentWebController::class, 'success'])->name('success');
    Route::get('/failure', [PaymentWebController::class, 'failure'])->name('failure');

    // Specialized callback — receives user after payment gateway redirect
    Route::get('/callback/{payment_type}', function (Illuminate\Http\Request $request, $payment_type) {
        $paymentId  = $request->payment_id ?? $request->orderId ?? $request->order_id ?? $request->id;
        $checkoutId = $request->id; // For HyperPay

        if ($request->status === 'cancel' || $request->status === 'failure') {
            return redirect()->route('payments.web.failure', ['error' => __('Payment cancelled by user.')]);
        }

        // Pass booking context so webVerify can update the correct booking
        return view('payments.callback_processing', [
            'payment_type' => $payment_type,
            'payment_id'   => $paymentId,
            'checkout_id'  => $checkoutId,
            'booking_id'   => $request->booking_id,
            'booking_type' => $request->type ?? $request->booking_type ?? 'trip',
            'status'       => $request->status,
            'source'       => $request->source,
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
Route::get('/book-trip/form', [FrontendController::class, 'tripBookingForm'])->name('trips.booking.form')->middleware('auth');
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
    Route::get('/global-search', [App\Http\Controllers\Admin\GlobalSearchController::class, 'search'])->name('global-search');
    Route::post('/translate', [App\Http\Controllers\Admin\TranslationController::class, 'translate'])->name('translate');

    // User Management
    Route::get('users/data', [UserController::class, 'getData'])->name('users.data');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('users/{user}/verify', [UserController::class, 'verify'])->name('users.verify');
    Route::resource('users', UserController::class);

    // Subscribers
    Route::get('subscribers', [UserController::class, 'subscribers'])->name('subscribers.index');
    Route::get('subscribers/data', [UserController::class, 'subscribersData'])->name('subscribers.data');

    // Role Management
    Route::get('roles/data', [RoleController::class, 'getData'])->name('roles.data');
    Route::resource('roles', RoleController::class);

    // Bookings
    Route::group(['prefix' => 'bookings', 'as' => 'bookings.'], function() {
        Route::get('/', [BookingController::class, 'index'])->name('index');

        // Flights
        Route::group(['prefix' => 'flights', 'as' => 'flights.'], function() {
            Route::get('/', [BookingController::class, 'flightBookings'])->name('index');
            Route::get('/data', [BookingController::class, 'getFlightData'])->name('data');
            Route::get('/analytics', [BookingController::class, 'flightAnalytics'])->name('analytics');
            Route::get('/profits', [BookingController::class, 'flightProfits'])->name('profits');
            Route::get('/profits/data', [BookingController::class, 'getFlightProfitsData'])->name('profits.data');
            Route::get('/ongoing', [BookingController::class, 'ongoingFlights'])->name('ongoing');
            Route::post('/search', [BookingController::class, 'searchFlights'])->name('search');
            Route::post('/validate', [BookingController::class, 'validateFare'])->name('validate');
            Route::post('/book', [BookingController::class, 'createBooking'])->name('book');
            Route::get('/{id}/show', [BookingController::class, 'showFlight'])->name('show');

        });

        // Hotels
        Route::group(['prefix' => 'hotels', 'as' => 'hotels.'], function() {
            Route::get('/', [BookingController::class, 'hotelBookings'])->name('index');
            Route::get('/data', [BookingController::class, 'getHotelData'])->name('data');
            Route::get('/profits', [BookingController::class, 'hotelProfits'])->name('profits');
            Route::get('/profits/data', [BookingController::class, 'getHotelProfitsData'])->name('profits.data');
            Route::get('/analytics', [BookingController::class, 'hotelAnalytics'])->name('analytics');
            Route::get('/{id}/show', [BookingController::class, 'showHotel'])->name('show');
            Route::get('/{id}/invoice', [BookingController::class, 'invoice'])->name('invoice');

            // Fallback Management
            Route::get('/paid-not-confirmed', [BookingController::class, 'getPaidHotelBookings'])->name('paid_not_confirmed');
            Route::post('/{id}/retry', [BookingController::class, 'retryHotelSupplierBooking'])->name('retry_supplier');
            Route::post('/{id}/force-confirm', [BookingController::class, 'forceConfirmHotelBooking'])->name('force_confirm');
        });

        // Common
        Route::get('/{id}/show', [BookingController::class, 'show'])->name('show');
        Route::get('/{id}/invoice', [BookingController::class, 'invoice'])->name('invoice');
    });

    // Settings
    Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // Wallets Management
    Route::get('wallets', [App\Http\Controllers\Admin\WalletController::class, 'index'])->name('wallets.index');
    Route::get('wallets/{id}', [App\Http\Controllers\Admin\WalletController::class, 'show'])->name('wallets.show');
    Route::post('wallets/{id}/add-transaction', [App\Http\Controllers\Admin\WalletController::class, 'addTransaction'])->name('wallets.add-transaction');

    // Support Tickets
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SupportTicketController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\SupportTicketController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [App\Http\Controllers\Admin\SupportTicketController::class, 'reply'])->name('reply');
        Route::post('/{id}/status', [App\Http\Controllers\Admin\SupportTicketController::class, 'updateStatus'])->name('status');
        Route::post('/{id}/assign', [App\Http\Controllers\Admin\SupportTicketController::class, 'assign'])->name('assign');
    });

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

    // Pages Management (CMS)
    Route::post('pages/{page}/toggle-status', [App\Http\Controllers\Admin\PageController::class, 'toggleStatus'])->name('pages.toggle-status');
    Route::resource('pages', App\Http\Controllers\Admin\PageController::class);

     // Trips Management
    Route::get('trips/analytics', [App\Http\Controllers\Admin\TripBookingController::class, 'analytics'])->name('trips.analytics');
    Route::get('trips/profits', [App\Http\Controllers\Admin\TripBookingController::class, 'profits'])->name('trips.profits');
    Route::get('trips/profits/data', [App\Http\Controllers\Admin\TripBookingController::class, 'getProfitsData'])->name('trips.profits.data');
    Route::get('bookings/trips/analytics', [App\Http\Controllers\Admin\TripBookingController::class, 'analytics'])->name('bookings.trips.analytics');
    Route::get('bookings/trips/profits', [App\Http\Controllers\Admin\TripBookingController::class, 'profits'])->name('bookings.trips.profits');
    Route::get('bookings/trips/profits/data', [App\Http\Controllers\Admin\TripBookingController::class, 'getProfitsData'])->name('bookings.trips.profits.data');
    Route::get('trips/data', [App\Http\Controllers\Admin\TripsController::class, 'getData'])->name('trips.data');
    Route::post('trips/{trip}/toggle-status', [App\Http\Controllers\Admin\TripsController::class, 'toggleStatus'])->name('trips.toggle-status');
    Route::post('/trips/{trip}/renew', [App\Http\Controllers\Admin\TripsController::class, 'renew'])->name('trips.renew');
    Route::get('trips/{trip}/stats', [App\Http\Controllers\Admin\TripsController::class, 'stats'])->name('trips.stats');
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

    // Trip Pricing Management View
    Route::get('/trips/{trip}/pricing', [TripsController::class, 'pricing'])->name('trips.pricing');

    // Trip Packages (multi-tier pricing)
    Route::post('/trips/{trip}/packages', [App\Http\Controllers\Admin\TripPackageController::class, 'store'])->name('packages.store');
    Route::put('/trips/{trip}/packages/{package}', [App\Http\Controllers\Admin\TripPackageController::class, 'update'])->name('packages.update');
    Route::delete('/trips/{trip}/packages/{package}', [App\Http\Controllers\Admin\TripPackageController::class, 'destroy'])->name('packages.destroy');

    // Trip Seasons (date-range pricing)
    Route::post('/trips/{trip}/seasons', [App\Http\Controllers\Admin\TripSeasonController::class, 'store'])->name('seasons.store');
    Route::put('/trips/{trip}/seasons/{season}', [App\Http\Controllers\Admin\TripSeasonController::class, 'update'])->name('seasons.update');
    Route::delete('/trips/{trip}/seasons/{season}', [App\Http\Controllers\Admin\TripSeasonController::class, 'destroy'])->name('seasons.destroy');

    // Trip Add-ons
    Route::post('/trips/{trip}/addons', [App\Http\Controllers\Admin\TripAddonController::class, 'store'])->name('addons.store');
    Route::put('/trips/{trip}/addons/{addon}', [App\Http\Controllers\Admin\TripAddonController::class, 'update'])->name('addons.update');
    Route::delete('/trips/{trip}/addons/{addon}', [App\Http\Controllers\Admin\TripAddonController::class, 'destroy'])->name('addons.destroy');


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



    // Bank Accounts Management
    Route::get('bank-accounts/data', [App\Http\Controllers\Admin\BankAccountController::class, 'getData'])->name('bank-accounts.data');
    Route::post('bank-accounts/{id}/toggle-active', [App\Http\Controllers\Admin\BankAccountController::class, 'toggleActive'])->name('bank-accounts.toggle-active');
    Route::resource('bank-accounts', App\Http\Controllers\Admin\BankAccountController::class);

    // Permission Management
    Route::get('permissions/data', [PermissionController::class, 'getData'])->name('permissions.data');
    Route::resource('permissions', PermissionController::class);

    // Reports
    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function() {
        Route::get('api-logs', [App\Http\Controllers\Admin\ReportController::class, 'apiLogs'])->name('api_logs');
        Route::get('search-logs', [App\Http\Controllers\Admin\ReportController::class, 'searchLogs'])->name('search_logs');
    });

    // Commissions & Profits
    Route::get('commissions', [\App\Http\Controllers\Admin\CommissionController::class, 'index'])->name('commissions.index');
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

    // Trip Add-ons
    Route::post('/trips/{trip}/addons', [\App\Http\Controllers\Agent\AgentTripController::class, 'storeAddon'])->name('trips.addons.store');
    Route::put('/trips/addons/{addon}', [\App\Http\Controllers\Agent\AgentTripController::class, 'updateAddon'])->name('trips.addons.update');
    Route::delete('/trips/addons/{addon}', [\App\Http\Controllers\Agent\AgentTripController::class, 'destroyAddon'])->name('trips.addons.destroy');

    // Trip Pricing & Packages
    Route::get('/trips/{trip}/pricing', [\App\Http\Controllers\Agent\AgentTripController::class, 'pricing'])->name('trips.pricing');
    
    Route::post('/trips/{trip}/packages', [\App\Http\Controllers\Agent\AgentTripPackageController::class, 'store'])->name('trips.packages.store');
    Route::put('/trips/packages/{package}', [\App\Http\Controllers\Agent\AgentTripPackageController::class, 'update'])->name('trips.packages.update');
    Route::delete('/trips/packages/{package}', [\App\Http\Controllers\Agent\AgentTripPackageController::class, 'destroy'])->name('trips.packages.destroy');
    
    Route::post('/trips/{trip}/seasons', [\App\Http\Controllers\Agent\AgentTripSeasonController::class, 'store'])->name('trips.seasons.store');
    Route::put('/trips/seasons/{season}', [\App\Http\Controllers\Agent\AgentTripSeasonController::class, 'update'])->name('trips.seasons.update');
    Route::delete('/trips/seasons/{season}', [\App\Http\Controllers\Agent\AgentTripSeasonController::class, 'destroy'])->name('trips.seasons.destroy');

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

    // Global Search
    Route::get('/search-all', [\App\Http\Controllers\Customer\CustomerSearchController::class, 'search'])->name('search-all');

    // Bookings
    Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/trips', [CustomerBookingController::class, 'trips'])->name('bookings.trips');
    Route::get('/bookings/flights', [CustomerBookingController::class, 'flights'])->name('bookings.flights');
    Route::get('/bookings/hotels', [CustomerBookingController::class, 'hotels'])->name('bookings.hotels');
    Route::get('/bookings/create/{trip_id}', [CustomerBookingController::class, 'create'])->name('bookings.create');
    Route::get('/bookings/{id}', [CustomerBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings', [CustomerBookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{id}/cancel', [CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/bookings/hotels/{id}/cancel-charge', [CustomerBookingController::class, 'cancelHotelCharge'])->name('bookings.hotels.cancel-charge');
    Route::post('/bookings/hotels/{id}/cancel', [CustomerBookingController::class, 'cancelHotel'])->name('bookings.hotels.cancel');
    Route::post('/bookings/{id}/sync-status', [CustomerBookingController::class, 'syncHotelBookingStatus'])->name('bookings.hotels.sync-status');
    Route::get('/bookings/{id}/invoice', [CustomerBookingController::class, 'downloadInvoice'])->name('bookings.invoice');
    Route::get('/bookings/hotels/{id}/voucher', [CustomerBookingController::class, 'downloadHotelVoucher'])->name('bookings.hotels.voucher');

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

    // Wallet
    Route::get('/wallet', [\App\Http\Controllers\Customer\WalletController::class, 'index'])->name('wallet.index');

    // Notifications
    Route::get('/notifications', [CustomerNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [CustomerNotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [CustomerNotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Support Tickets
    Route::prefix('support')->name('support.')->group(function() {
        Route::get('/', [App\Http\Controllers\Customer\SupportTicketController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Customer\SupportTicketController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Customer\SupportTicketController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Customer\SupportTicketController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [App\Http\Controllers\Customer\SupportTicketController::class, 'reply'])->name('reply');
        Route::post('/{id}/rate', [App\Http\Controllers\Customer\SupportTicketController::class, 'rate'])->name('rate');
    });
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

// Web Phone OTP Routes
Route::post('/login/phone/request-otp', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'requestWebOtp'])->name('login.phone.request');
Route::post('/login/phone/verify-otp', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'verifyWebOtp'])->name('login.phone.verify');

