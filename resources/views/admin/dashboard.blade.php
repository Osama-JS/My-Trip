@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<div class="row">
    <!-- Welcome Header -->
    <div class="col-12 mb-4">
        <div class="card welcome-card border-0 overflow-hidden position-relative" style="border-radius: 20px; background: linear-gradient(135deg, #041741 0%, #0d2766 100%); box-shadow: 0 10px 30px rgba(4, 23, 65, 0.15);">
            <!-- Decorative background elements -->
            <div class="position-absolute top-0 end-0 h-100 w-50" style="background: radial-gradient(circle at top right, rgba(255,255,255,0.1) 0%, transparent 60%); pointer-events: none;"></div>
            <div class="position-absolute bottom-0 start-0 h-100 w-50" style="background: radial-gradient(circle at bottom left, rgba(255,255,255,0.05) 0%, transparent 50%); pointer-events: none;"></div>
            
            <div class="card-body p-4 p-md-5 text-white position-relative z-1">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <span class="badge bg-white text-primary mb-3 px-3 py-2 rounded-pill fw-semibold shadow-sm" style="font-size: 12px;">{{ date('l, d M Y') }}</span>
                        <h3 class="fw-bold mb-2">{{ $greeting }}, <span class="text-warning">{{ $adminName }}</span>! <span class="wave-emoji">👋</span></h3>
                        <p class="mb-4 text-white-50 small">{{ __('Welcome to your travel hub. Here is an overview for today.') }}</p>
                        
                        <!-- Quick actions -->
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.trips.create') }}" class="btn btn-sm btn-warning fw-semibold rounded-pill px-3 py-2 shadow-sm text-dark">
                                <i class="fas fa-plus me-1"></i> {{ __('Add New Trip') }}
                            </a>
                            <a href="{{ route('admin.trip-bookings.index') }}" class="btn btn-sm btn-light btn-outline-light bg-white-10 text-white border-0 fw-semibold rounded-pill px-3 py-2 shadow-sm">
                                <i class="fas fa-calendar-check me-1"></i> {{ __('Manage Bookings') }}
                            </a>
                            <a href="{{ route('admin.support.index') }}" class="btn btn-sm btn-light btn-outline-light bg-white-10 text-white border-0 fw-semibold rounded-pill px-3 py-2 shadow-sm">
                                <i class="fas fa-headset me-1"></i> {{ __('Support Center') }}
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 text-end d-none d-lg-block">
                        <div class="bg-white-10 rounded-circle d-inline-flex align-items-center justify-content-center p-3" style="width: 100px; height: 100px; backdrop-filter: blur(10px);">
                            <i class="fas fa-chart-line fa-3x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Grid -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card kpi-card border-0 h-100 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-money-bill-wave fa-lg"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 fw-semibold" style="font-size: 11px;">
                        <i class="fas fa-arrow-up me-1"></i> {{ __('Total lifetime') }}
                    </span>
                </div>
                <h6 class="text-muted fw-semibold mb-1" style="font-size: 13px;">{{ __('Total Revenue') }}</h6>
                <h3 class="fw-bold mb-0 text-dark">
                    <span class="animate-counter format-currency" data-target="{{ $stats['revenue_total'] }}">0.00</span>
                    <span class="small text-muted" style="font-size: 14px;">{{ __('SAR') }}</span>
                </h3>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card kpi-card border-0 h-100 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-calendar-check fa-lg"></i>
                    </div>
                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 fw-semibold" style="font-size: 11px;">
                        {{ $stats['bookings_pending'] }} {{ __('Pending') }}
                    </span>
                </div>
                <h6 class="text-muted fw-semibold mb-1" style="font-size: 13px;">{{ __('Total Bookings') }}</h6>
                <h3 class="fw-bold mb-0 text-dark">
                    <span class="animate-counter" data-target="{{ $stats['bookings_total'] }}">0</span>
                </h3>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card kpi-card border-0 h-100 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-plane fa-lg"></i>
                    </div>
                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 fw-semibold" style="font-size: 11px;">
                        {{ $stats['trips_expired'] }} {{ __('Expired') }}
                    </span>
                </div>
                <h6 class="text-muted fw-semibold mb-1" style="font-size: 13px;">{{ __('Active Trips') }}</h6>
                <h3 class="fw-bold mb-0 text-dark">
                    <span class="animate-counter" data-target="{{ $stats['trips_active'] }}">0</span>
                </h3>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card kpi-card border-0 h-100 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 fw-semibold" style="font-size: 11px;">
                        <i class="fas fa-plus me-1"></i>{{ $stats['users_new_today'] }} {{ __('Today') }}
                    </span>
                </div>
                <h6 class="text-muted fw-semibold mb-1" style="font-size: 13px;">{{ __('Total Users') }}</h6>
                <h3 class="fw-bold mb-0 text-dark">
                    <span class="animate-counter" data-target="{{ $stats['users_total'] }}">0</span>
                </h3>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card kpi-card border-0 h-100 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-ticket-alt fa-lg"></i>
                    </div>
                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 fw-semibold" style="font-size: 11px;">
                        {{ __('Today') }}
                    </span>
                </div>
                <h6 class="text-muted fw-semibold mb-1" style="font-size: 13px;">{{ __("Today's Bookings") }}</h6>
                <h3 class="fw-bold mb-0 text-dark">
                    <span class="animate-counter" data-target="{{ $stats['bookings_today'] }}">0</span>
                </h3>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card kpi-card border-0 h-100 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-headset fa-lg"></i>
                    </div>
                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 fw-semibold" style="font-size: 11px;">
                        {{ $stats['tickets_open'] }} {{ __('Open') }}
                    </span>
                </div>
                <h6 class="text-muted fw-semibold mb-1" style="font-size: 13px;">{{ __('Support Tickets') }}</h6>
                <h3 class="fw-bold mb-0 text-dark">
                    <span class="animate-counter" data-target="{{ $stats['tickets_open'] }}">0</span>
                </h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart Column -->
    <div class="col-xl-8 col-lg-12 mb-4">
        <div class="card border-0 h-100 kpi-card shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-1 text-dark">{{ __('User Growth Analysis') }}</h6>
                    <p class="text-muted mb-0" style="font-size: 11px;">{{ __('Monthly user registration trend') }}</p>
                </div>
            </div>
            <div class="card-body pt-3 px-4 pb-4">
                <div style="height: 320px; position: relative;">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Donut Chart Column -->
    <div class="col-xl-4 col-lg-12 mb-4">
        <div class="card border-0 h-100 kpi-card shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-bold mb-1 text-dark">{{ __('Booking States') }}</h6>
                <p class="text-muted mb-0" style="font-size: 11px;">{{ __('Breakdown of booking statuses') }}</p>
            </div>
            <div class="card-body pt-3 px-4 pb-4 d-flex align-items-center justify-content-center">
                <div style="height: 250px; width: 100%; position: relative;">
                    <canvas id="bookingStatesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Activities Table Column -->
    <div class="col-xl-6 col-lg-12 mb-4">
        <div class="card border-0 h-100 kpi-card shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-1 text-dark">{{ __('Recent Bookings') }}</h6>
                    <p class="text-muted mb-0" style="font-size: 11px;">{{ __('Latest customer reservations') }}</p>
                </div>
                <a href="{{ route('admin.trip-bookings.index') }}" class="btn btn-sm btn-primary bg-primary-subtle text-primary border-0 fw-semibold rounded-pill px-3" style="font-size: 11px;">{{ __('View All') }}</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-table">
                        <tbody>
                            @forelse($recentBookings->take(5) as $booking)
                                <tr>
                                    <td class="ps-4 py-3" style="width: 50px;">
                                        <div class="icon-box bg-light text-dark rounded-circle text-center d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px; font-size: 12px;">
                                            <i class="fas fa-ticket-alt text-primary"></i>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <h6 class="mb-1 fw-bold text-dark" style="font-size: 13px;">{{ $booking->user->name ?? __('Guest') }}</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px;"><i class="fas fa-map-marker-alt text-primary me-1 opacity-50"></i>{{ \Illuminate\Support\Str::limit($booking->trip->title_ar ?? $booking->trip->title_en ?? __('Trip'), 25) }}</p>
                                    </td>
                                    <td class="py-3 text-center">
                                        @php
                                            $badgeClass = 'bg-secondary';
                                            $stateLabel = $booking->booking_state;
                                            if($booking->booking_state === 'awaiting_payment') {
                                                $badgeClass = 'bg-warning-subtle text-warning';
                                            } elseif($booking->booking_state === 'completed') {
                                                $badgeClass = 'bg-success-subtle text-success';
                                            } elseif($booking->booking_state === 'cancelled') {
                                                $badgeClass = 'bg-danger-subtle text-danger';
                                            } elseif(in_array($booking->booking_state, ['preparing', 'confirmed', 'issuing_tickets', 'tickets_uploaded', 'tickets_sent'])) {
                                                $badgeClass = 'bg-primary-subtle text-primary';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }} rounded-pill px-2 py-1 fw-medium" style="font-size: 10px;">
                                            {{ __($stateLabel) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <div class="fw-bold text-dark" style="font-size: 13px;">{{ number_format($booking->total_price, 2) }} <span class="text-muted fw-normal" style="font-size: 10px;">SAR</span></div>
                                        <div class="text-muted" style="font-size: 10px;"><i class="far fa-clock me-1"></i>{{ $booking->created_at->diffForHumans(null, true) }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-3 opacity-25"></i>
                                    <p class="mb-0 fw-semibold" style="font-size: 13px;">{{ __('No recent bookings yet.') }}</p>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- User List Column -->
    <div class="col-xl-6 col-lg-12 mb-4">
        <div class="card border-0 h-100 kpi-card shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-1 text-dark">{{ __('New Members') }}</h6>
                    <p class="text-muted mb-0" style="font-size: 11px;">{{ __('Recently joined users') }}</p>
                </div>
                 <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary bg-primary-subtle text-primary border-0 fw-semibold rounded-pill px-3" style="font-size: 11px;">{{ __('Manage') }}</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-table">
                        <tbody>
                            @forelse($latestUsers->take(5) as $user)
                            <tr>
                                <td class="ps-4 py-3" style="width: 50px;">
                                    <div class="avatar rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px; font-size: 12px;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                </td>
                                <td class="py-3">
                                    <h6 class="mb-1 fw-bold text-dark" style="font-size: 13px;">{{ $user->name }}</h6>
                                    <p class="text-muted mb-0" style="font-size: 11px;"><i class="far fa-envelope text-muted me-1 opacity-50"></i>{{ \Illuminate\Support\Str::limit($user->email, 25) }}</p>
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-1 fw-medium shadow-sm" style="font-size: 10px;"><i class="far fa-calendar-alt me-1 text-muted"></i>{{ $user->created_at->format('d M') }}</span>
                                </td>
                            </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-5 text-muted">
                                    <i class="fas fa-users fa-2x mb-3 opacity-25"></i>
                                    <p class="mb-0 fw-semibold" style="font-size: 13px;">{{ __('No new members.') }}</p>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Companies Promo Column -->
    <div class="col-12 mb-4">
        <div class="card border-0 overflow-hidden position-relative promo-card" style="border-radius: 16px; background: linear-gradient(135deg, #f8faff 0%, #eff6ff 100%);">
             <!-- Abstract Shapes -->
            <div class="position-absolute top-0 end-0" style="width: 150px; height: 150px; background: rgba(4, 23, 65, 0.05); border-radius: 50%; transform: translate(30%, -30%); pointer-events: none;"></div>
            <div class="position-absolute bottom-0 start-0" style="width: 100px; height: 100px; background: rgba(4, 23, 65, 0.05); border-radius: 50%; transform: translate(-30%, 30%); pointer-events: none;"></div>
            
            <div class="card-body p-4 p-lg-5 d-flex flex-column justify-content-center align-items-center text-center position-relative z-1">
                <div class="icon-box bg-white text-primary rounded-circle shadow-sm d-flex justify-content-center align-items-center mb-4" style="width: 70px; height: 70px;">
                    <i class="fas fa-handshake" style="font-size: 1.8em;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">{{ __('Business Partners & Categories') }}</h5>
                <p class="text-muted mb-4 px-2 px-md-4" style="font-size: 13px;">{{ __('Manage your registered travel partners and trip categories. Currently you have') }} <strong class="text-primary">{{ $stats['companies_count'] }}</strong> {{ __('partners active.') }}</p>
                <div class="d-flex flex-wrap justify-content-center gap-3 w-100">
                    <a href="{{ route('admin.trip-categories.index') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-semibold d-flex align-items-center justify-content-center">
                        <i class="fas fa-tags me-2"></i> {{ __('Categories') }}
                    </a>
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 shadow-sm fw-semibold d-flex align-items-center justify-content-center">
                        <i class="fas fa-building me-2"></i> {{ __('Companies') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Color Variables & Utility Classes */
    :root {
        --primary-color: #041741;
        --primary-light: rgba(4, 23, 65, 0.08);
        --success-light: rgba(40, 167, 69, 0.12);
        --warning-light: rgba(255, 193, 7, 0.12);
        --danger-light: rgba(239, 68, 68, 0.12);
        --info-light: rgba(23, 162, 184, 0.12);
        --secondary-light: rgba(108, 117, 125, 0.12);
    }
    
    .bg-primary-subtle { background-color: var(--primary-light) !important; color: var(--primary-color) !important; }
    .bg-success-subtle { background-color: var(--success-light) !important; color: #198754 !important; }
    .bg-warning-subtle { background-color: var(--warning-light) !important; color: #b45309 !important; }
    .bg-danger-subtle { background-color: var(--danger-light) !important; color: #dc3545 !important; }
    .bg-info-subtle { background-color: var(--info-light) !important; color: #0dcaf0 !important; }
    .bg-secondary-subtle { background-color: var(--secondary-light) !important; color: #6c757d !important; }

    .bg-white-10 {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }
    
    .text-primary { color: var(--primary-color) !important; }
    .btn-primary { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; color: #fff !important; }
    .btn-primary:hover { background-color: #062261 !important; border-color: #062261 !important; }
    .btn-outline-primary { color: var(--primary-color) !important; border-color: var(--primary-color) !important; background: transparent; }
    .btn-outline-primary:hover { background-color: var(--primary-color) !important; color: #fff !important; }

    /* Cards */
    .kpi-card {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background-color: #ffffff;
    }
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    }
    
    .promo-card {
        border: 1px solid rgba(4, 23, 65, 0.08) !important;
        box-shadow: 0 4px 20px rgba(4, 23, 65, 0.04);
        transition: all 0.3s ease;
    }
    .promo-card:hover {
        box-shadow: 0 10px 30px rgba(4, 23, 65, 0.12);
        border-color: rgba(4, 23, 65, 0.2) !important;
    }

    /* Tables */
    .custom-table tr {
        transition: background-color 0.2s ease;
    }
    .custom-table tr:hover {
        background-color: #f8fafc;
    }
    .custom-table td {
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .custom-table tr:last-child td {
        border-bottom: none;
    }

    /* Wave Emoji Keyframes */
    .wave-emoji {
        display: inline-block;
        animation: wave-animation 2.5s infinite;
        transform-origin: 70% 70%;
    }
    @keyframes wave-animation {
        0% { transform: rotate( 0.0deg) }
        10% { transform: rotate(14.0deg) }
        20% { transform: rotate(-8.0deg) }
        30% { transform: rotate(14.0deg) }
        40% { transform: rotate(-4.0deg) }
        50% { transform: rotate(10.0deg) }
        60% { transform: rotate( 0.0deg) }
        100% { transform: rotate( 0.0deg) }
    }

    /* Animations */
    .welcome-card { 
        animation: slideInDown 0.6s cubic-bezier(0.16, 1, 0.3, 1); 
    }
    @keyframes slideInDown { 
        from { opacity: 0; transform: translateY(-20px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
    
    .kpi-card, .promo-card {
        animation: fadeIn 0.8s ease backwards;
    }
    .kpi-card:nth-child(2) { animation-delay: 0.1s; }
    .kpi-card:nth-child(3) { animation-delay: 0.2s; }
    .kpi-card:nth-child(4) { animation-delay: 0.3s; }
    .kpi-card:nth-child(5) { animation-delay: 0.4s; }
    .kpi-card:nth-child(6) { animation-delay: 0.5s; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Dark Mode compatibility styles */
    [data-theme-version="dark"] .kpi-card {
        background-color: #1e1e2d !important;
    }
    [data-theme-version="dark"] .text-dark,
    [data-theme-version="dark"] h6.text-dark,
    [data-theme-version="dark"] h3.text-dark,
    [data-theme-version="dark"] h5.text-dark,
    [data-theme-version="dark"] h6.text-dark,
    [data-theme-version="dark"] .custom-table td {
        color: #ffffff !important;
    }
    [data-theme-version="dark"] .custom-table tr:hover {
        background-color: #27273d !important;
    }
    [data-theme-version="dark"] .custom-table td {
        border-bottom: 1px solid #2d2d44;
    }
    [data-theme-version="dark"] .promo-card {
        background: linear-gradient(135deg, #1e1e2d 0%, #171725 100%) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme-version="dark"] .icon-box.bg-white {
        background-color: #27273d !important;
        color: #ffffff !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // --- Counter Animations ---
        const counters = document.querySelectorAll('.animate-counter');
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const duration = 1200; // ms
            const startTime = performance.now();
            const startValue = 0;
            
            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                // Ease out quad
                const easeProgress = progress * (2 - progress);
                const currentValue = startValue + easeProgress * (target - startValue);
                
                if (counter.classList.contains('format-currency')) {
                    counter.innerText = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(currentValue);
                } else {
                    counter.innerText = Math.floor(currentValue).toLocaleString();
                }
                
                if (progress < 1) {
                    requestAnimationFrame(update);
                } else {
                    if (counter.classList.contains('format-currency')) {
                        counter.innerText = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(target);
                    } else {
                        counter.innerText = target.toLocaleString();
                    }
                }
            }
            requestAnimationFrame(update);
        });

        // --- Charts Setup ---
        const isDarkMode = document.body.getAttribute('data-theme-version') === 'dark';
        
        // 1. User Growth Chart
        const growthCtx = document.getElementById('userGrowthChart').getContext('2d');
        const primaryChartColor = isDarkMode ? '#ffffff' : '#041741';
        const chartGradientStart = isDarkMode ? 'rgba(255, 255, 255, 0.25)' : 'rgba(4, 23, 65, 0.25)';
        const chartGradientEnd = isDarkMode ? 'rgba(255, 255, 255, 0.0)' : 'rgba(4, 23, 65, 0.0)';
        const gridColor = isDarkMode ? '#2d2d44' : '#f1f5f9';

        let gradient = growthCtx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, chartGradientStart);
        gradient.addColorStop(1, chartGradientEnd);

        new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: '{{ __("New Users") }}',
                    data: {!! json_encode($chartData) !!},
                    borderColor: primaryChartColor,
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: isDarkMode ? '#111827' : '#fff',
                    pointBorderColor: primaryChartColor,
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { 
                        backgroundColor: primaryChartColor,
                        titleColor: isDarkMode ? '#111827' : '#ffffff',
                        bodyColor: isDarkMode ? '#111827' : '#ffffff',
                        titleFont: { size: 12, family: "'Inter', 'Tajawal', sans-serif" },
                        bodyFont: { size: 13, family: "'Inter', 'Tajawal', sans-serif", weight: 'bold' },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: gridColor, drawBorder: false },
                        ticks: { font: { family: "'Inter', 'Tajawal', sans-serif", size: 11 }, color: '#64748b', padding: 10 }
                    },
                    x: { 
                        grid: { display: false, drawBorder: false },
                        ticks: { font: { family: "'Inter', 'Tajawal', sans-serif", size: 11 }, color: '#64748b', padding: 10 }
                    }
                }
            }
        });

        // 2. Booking States Donut Chart
        const statesCtx = document.getElementById('bookingStatesChart').getContext('2d');
        new Chart(statesCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    '{{ __("Pending Payment") }}',
                    '{{ __("Processing") }}',
                    '{{ __("Completed") }}',
                    '{{ __("Cancelled") }}'
                ],
                datasets: [{
                    data: [
                        {{ $donutData['pending_payment'] }},
                        {{ $donutData['processing'] }},
                        {{ $donutData['completed'] }},
                        {{ $donutData['cancelled'] }}
                    ],
                    backgroundColor: [
                        '#eab308', // warning / yellow
                        '#3b82f6', // primary / blue
                        '#22c55e', // success / green
                        '#ef4444'  // danger / red
                    ],
                    borderWidth: isDarkMode ? 2 : 1,
                    borderColor: isDarkMode ? '#1e1e2d' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: isDarkMode ? '#ffffff' : '#64748b',
                            padding: 15,
                            font: { family: "'Inter', 'Tajawal', sans-serif", size: 11 }
                        }
                    },
                    tooltip: {
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { family: "'Inter', 'Tajawal', sans-serif", size: 12 },
                        bodyFont: { family: "'Inter', 'Tajawal', sans-serif", size: 13, weight: 'bold' }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endpush
