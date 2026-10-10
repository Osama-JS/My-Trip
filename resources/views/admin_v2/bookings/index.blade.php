@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'مركز عمليات الحجوزات' : 'Bookings Operations Hub')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.8rem; color: var(--text-muted);">
                <a href="{{ route('admin.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Dashboard') }}</a>
                <i class="fas fa-chevron-right text-xs" style="font-size: 0.65rem;"></i>
                <span style="color: var(--text-main); font-weight: 600;">{{ __('Bookings Hub') }}</span>
            </div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-calendar-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'مركز إدارة ومتابعة الحجوزات' : 'Bookings & Reservations Hub' }}
            </h1>
        </div>
    </div>

    <!-- 3 Big Action Hub Cards -->
    <div class="row g-4 my-2">
        <!-- Flights Hub Card -->
        <div class="col-xl-4 col-md-6">
            <div class="admin-card p-4 h-100 position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: #fff; border: none;">
                <i class="fa-solid fa-plane position-absolute" style="top: 10px; inset-inline-end: -15px; font-size: 8rem; opacity: 0.12; transform: rotate(-15deg); pointer-events: none;"></i>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div style="width: 58px; height: 58px; border-radius: 16px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                        <i class="fa-solid fa-plane-departure text-white"></i>
                    </div>
                    <h2 class="text-white mb-0" style="font-size: 2.5rem; font-weight: 900;">{{ number_format($stats['flights'] ?? 0) }}</h2>
                </div>
                <h5 class="text-white mb-1 font-bold">{{ app()->getLocale() == 'ar' ? 'حجوزات الطيران' : 'Flight Bookings' }}</h5>
                <p class="text-white text-opacity-75 small mb-4">{{ app()->getLocale() == 'ar' ? 'إدارة التذاكر، إصدار الفواتير، التحليلات، وتتبع حالة المزودين.' : 'Manage flight manifests, e-tickets, analytics, and supplier tickets.' }}</p>

                <div class="d-flex gap-2 mt-auto">
                    <a href="{{ route('admin.bookings.flights.index') }}" class="btn btn-light rounded-pill font-bold w-100 text-primary">
                        {{ __('View All') }} <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                    <a href="{{ route('admin.bookings.flights.analytics') }}" class="btn btn-outline-light rounded-pill px-3" title="{{ __('Analytics') }}">
                        <i class="fa-solid fa-chart-line"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Hotels Hub Card -->
        <div class="col-xl-4 col-md-6">
            <div class="admin-card p-4 h-100 position-relative overflow-hidden" style="background: linear-gradient(135deg, #065f46 0%, #10b981 100%); color: #fff; border: none;">
                <i class="fa-solid fa-hotel position-absolute" style="top: 10px; inset-inline-end: -15px; font-size: 8rem; opacity: 0.12; transform: rotate(-15deg); pointer-events: none;"></i>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div style="width: 58px; height: 58px; border-radius: 16px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                        <i class="fa-solid fa-bed text-white"></i>
                    </div>
                    <h2 class="text-white mb-0" style="font-size: 2.5rem; font-weight: 900;">{{ number_format($stats['hotels'] ?? 0) }}</h2>
                </div>
                <h5 class="text-white mb-1 font-bold">{{ app()->getLocale() == 'ar' ? 'حجوزات الفنادق' : 'Hotel Bookings' }}</h5>
                <p class="text-white text-opacity-75 small mb-4">{{ app()->getLocale() == 'ar' ? 'إدارة الإقامات، قسائم الفنادق Vouchers، وهوامش الأرباح.' : 'Hotel vouchers, room allocations, guest lists, and profit margins.' }}</p>

                <div class="d-flex gap-2 mt-auto">
                    <a href="{{ route('admin.bookings.hotels.index') }}" class="btn btn-light rounded-pill font-bold w-100 text-success">
                        {{ __('View All') }} <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                    <a href="{{ route('admin.bookings.hotels.analytics') }}" class="btn btn-outline-light rounded-pill px-3" title="{{ __('Analytics') }}">
                        <i class="fa-solid fa-chart-line"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Trips Hub Card -->
        <div class="col-xl-4 col-md-6">
            <div class="admin-card p-4 h-100 position-relative overflow-hidden" style="background: linear-gradient(135deg, #b45309 0%, #f59e0b 100%); color: #fff; border: none;">
                <i class="fa-solid fa-suitcase-rolling position-absolute" style="top: 10px; inset-inline-end: -15px; font-size: 8rem; opacity: 0.12; transform: rotate(-15deg); pointer-events: none;"></i>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div style="width: 58px; height: 58px; border-radius: 16px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                        <i class="fa-solid fa-map-location-dot text-white"></i>
                    </div>
                    <h2 class="text-white mb-0" style="font-size: 2.5rem; font-weight: 900;">{{ number_format($stats['trips'] ?? 0) }}</h2>
                </div>
                <h5 class="text-white mb-1 font-bold">{{ app()->getLocale() == 'ar' ? 'حجوزات الرحلات المنظمة' : 'Organized Trip Bookings' }}</h5>
                <p class="text-white text-opacity-75 small mb-4">{{ app()->getLocale() == 'ar' ? 'إدارة ركاب البرامج السياحية، تأكيدات الحجز، ومتابعة الأرباح.' : 'Tour packages manifest, participant details, payments and seat capacity.' }}</p>

                <div class="d-flex gap-2 mt-auto">
                    <a href="{{ route('admin.trip-bookings.index') }}" class="btn btn-light rounded-pill font-bold w-100 text-warning" style="color: #b45309 !important;">
                        {{ __('View All') }} <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                    <a href="{{ route('admin.trip-bookings.analytics') }}" class="btn btn-outline-light rounded-pill px-3" title="{{ __('Analytics') }}">
                        <i class="fa-solid fa-chart-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
