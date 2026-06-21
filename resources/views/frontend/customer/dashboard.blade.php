@extends('frontend.customer.layouts.customer-layout')

@section('title', __('لوحة التحكم'))
@section('page-title', __('لوحة التحكم'))

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

<style>
/* ─── Global Styling for Dashboard ─── */
body, .cdash-wrapper, .welcome-banner-wrapper, .stat-card, .dash-section, .quick-card {
    font-family: 'Tajawal', sans-serif !important;
}

i, .fas, .far, .fab, .fa {
    font-family: "Font Awesome 5 Free", "Font Awesome 5 Brands" !important;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.cdash-animate {
    animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* ─── Welcome Banner & Wallet Section ─── */
.welcome-banner-wrapper {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 28px;
    opacity: 0;
}
@media (max-width: 991px) {
    .welcome-banner-wrapper {
        grid-template-columns: 1fr;
    }
}

.welcome-banner-main {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 18px;
    padding: 32px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12);
}
.welcome-banner-main h2 {
    font-size: 1.75rem;
    font-weight: 800;
    margin: 0 0 10px;
    color: #ffffff;
    position: relative;
    z-index: 2;
}
.welcome-banner-main p {
    font-size: 1rem;
    color: #94a3b8;
    margin: 0;
    font-weight: 500;
    position: relative;
    z-index: 2;
}
.welcome-banner-main::after {
    content: '';
    position: absolute;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
    top: -50px;
    right: -50px;
    z-index: 1;
}

.welcome-wallet-card {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    border-radius: 18px;
    padding: 24px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.welcome-wallet-card::before {
    content: '';
    position: absolute;
    top: -30%;
    left: -20%;
    width: 150px;
    height: 150px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 50%;
}
.welcome-wallet-card::after {
    content: '';
    position: absolute;
    bottom: -40%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
}
.wallet-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    z-index: 2;
}
.wallet-card-header span {
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: rgba(255, 255, 255, 0.8);
    text-transform: uppercase;
}
.wallet-card-header i {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.9);
}
.wallet-card-balance {
    z-index: 2;
    margin-bottom: 16px;
    display: flex;
    align-items: baseline;
    gap: 6px;
}
.wallet-card-balance .balance-value {
    font-size: 2.1rem;
    font-weight: 950;
    letter-spacing: -0.5px;
}
.wallet-card-balance .balance-currency {
    font-size: 0.95rem;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.8);
}
.wallet-card-footer {
    z-index: 2;
}
.btn-wallet-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 0.8rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}
.btn-wallet-action:hover {
    background: #ffffff;
    color: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* ─── Stats Cards ─── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 28px;
    opacity: 0;
}
@media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 520px)  { .stats-grid { grid-template-columns: 1fr; } }

.stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 24px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
    border-color: rgba(59, 130, 246, 0.25);
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: transparent;
    transition: background-color 0.3s;
}
.stat-card.stat-icon-blue:hover::before { background: #3b82f6; }
.stat-card.stat-icon-orange:hover::before { background: #f97316; }
.stat-card.stat-icon-green:hover::before { background: #10b981; }
.stat-card.stat-icon-purple:hover::before { background: #8b5cf6; }

.stat-icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
    transition: transform 0.3s;
}
.stat-card:hover .stat-icon-wrapper {
    transform: scale(1.1);
}

.stat-icon-blue .stat-icon-wrapper { background: rgba(59, 130, 246, 0.08); color: #3b82f6; }
.stat-icon-orange .stat-icon-wrapper { background: rgba(249, 115, 22, 0.08); color: #f97316; }
.stat-icon-green .stat-icon-wrapper { background: rgba(16, 185, 129, 0.08); color: #10b981; }
.stat-icon-purple .stat-icon-wrapper { background: rgba(139, 92, 246, 0.08); color: #8b5cf6; }

.stat-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.stat-label {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1.1;
}

/* ─── Two Columns Layout ─── */
.dash-two-cols {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 28px;
    opacity: 0;
}
@media (max-width: 991px) { .dash-two-cols { grid-template-columns: 1fr; } }

.dash-section {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    overflow: hidden;
}
.dash-section-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(0, 0, 0, 0.01);
}
.dash-section-header h3 {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--text-main);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.dash-section-header h3 i {
    color: var(--primary-blue);
    font-size: 1.15rem;
}
.dash-section-link {
    font-size: 0.85rem;
    color: var(--primary-blue);
    text-decoration: none;
    font-weight: 700;
    transition: color 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.dash-section-link:hover {
    color: #1d4ed8;
    text-decoration: underline;
}
.dash-section-body {
    padding: 20px 24px;
}

/* ─── Booking Items ─── */
.booking-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    border-radius: 14px;
    border: 1px solid transparent;
    transition: all 0.25s ease;
    margin-bottom: 12px;
}
.booking-row:last-child {
    margin-bottom: 0;
}
.booking-row:hover {
    background: rgba(59, 130, 246, 0.03);
    border-color: rgba(59, 130, 246, 0.1);
    transform: translateX(4px);
}
[dir="rtl"] .booking-row:hover {
    transform: translateX(-4px);
}

.booking-thumb, .booking-thumb-placeholder {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    object-fit: cover;
    flex-shrink: 0;
}
.booking-thumb-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.booking-thumb-placeholder.trip-type { background: rgba(249, 115, 22, 0.08); color: #f97316; }
.booking-thumb-placeholder.flight-type { background: rgba(59, 130, 246, 0.08); color: #3b82f6; }
.booking-thumb-placeholder.hotel-type { background: rgba(139, 92, 246, 0.08); color: #8b5cf6; }

.booking-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.booking-title-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.booking-title {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--text-main);
    line-height: 1.3;
}

.type-badge {
    font-size: 0.68rem;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.type-badge.type-trip { background: rgba(249, 115, 22, 0.1); color: #ea580c; }
.type-badge.type-flight { background: rgba(59, 130, 246, 0.1); color: #2563eb; }
.type-badge.type-hotel { background: rgba(139, 92, 246, 0.1); color: #9333ea; }

.booking-meta {
    font-size: 0.8rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.bullet-sep {
    color: var(--border-color);
}

.booking-price-wrapper {
    text-align: right;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
}
[dir="rtl"] .booking-price-wrapper {
    text-align: left;
    align-items: flex-start;
}
.booking-price {
    font-weight: 800;
    font-size: 1.05rem;
    color: var(--text-main);
}
.booking-price-label {
    font-size: 0.7rem;
    color: var(--text-muted);
}

/* Status Badges */
.status-badge-wrapper {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 700;
}
.status-badge-wrapper .pulse-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
}

.status-badge-wrapper.status-pending {
    background: rgba(249, 115, 22, 0.08);
    color: #c2410c;
}
.status-badge-wrapper.status-pending .pulse-dot {
    background: #f97316;
    animation: statusPulseOrange 1.5s infinite;
}

.status-badge-wrapper.status-confirmed {
    background: rgba(16, 185, 129, 0.08);
    color: #15803d;
}
.status-badge-wrapper.status-confirmed .pulse-dot {
    background: #10b981;
    animation: statusPulseGreen 1.5s infinite;
}

.status-badge-wrapper.status-cancelled {
    background: rgba(239, 68, 68, 0.08);
    color: #b91c1c;
}
.status-badge-wrapper.status-cancelled .pulse-dot {
    background: #ef4444;
}

@keyframes statusPulseGreen {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
    70% { box-shadow: 0 0 0 5px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
@keyframes statusPulseOrange {
    0% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.4); }
    70% { box-shadow: 0 0 0 5px rgba(249, 115, 22, 0); }
    100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
}

/* Empty States */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-muted);
}
.empty-state i {
    font-size: 2.8rem;
    margin-bottom: 12px;
    opacity: 0.3;
    display: block;
    color: var(--text-muted);
}
.empty-state p {
    font-size: 0.9rem;
    margin: 0;
    font-weight: 600;
}

/* ─── Payment Rows ─── */
.payment-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    background: var(--bg-card);
    margin-bottom: 10px;
    transition: all 0.2s ease;
}
.payment-row:last-child {
    margin-bottom: 0;
}
.payment-row:hover {
    border-color: rgba(16, 185, 129, 0.2);
    background: rgba(16, 185, 129, 0.01);
}
.pay-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.pay-trip {
    font-weight: 700;
    font-size: 0.88rem;
    color: var(--text-main);
}
.pay-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    color: var(--text-muted);
}
.gateway-badge {
    font-size: 0.65rem;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 4px;
    text-transform: uppercase;
}
.gateway-badge.gw-tabby { background: #ffe4e6; color: #e11d48; }
.gateway-badge.gw-tamara { background: #fef3c7; color: #d97706; }
.gateway-badge.gw-card { background: #dbeafe; color: #2563eb; }
.gateway-badge.gw-bank_transfer { background: #f3f4f6; color: #4b5563; }
.gateway-badge.gw-wallet { background: #e0f2fe; color: #0369a1; }

.pay-amount-wrapper {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
}
[dir="rtl"] .pay-amount-wrapper {
    align-items: flex-start;
}
.pay-amount {
    font-weight: 800;
    font-size: 1rem;
    color: #10b981;
}
.pay-amount.pay-pending { color: #f97316; }
.pay-amount.pay-failed { color: #ef4444; }
.pay-status-text {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
}

/* ─── Quick Shortcuts ─── */
.quick-access-section {
    opacity: 0;
}
.quick-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
}
.quick-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 20px;
    text-decoration: none !important;
    display: flex;
    flex-direction: column;
    gap: 12px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
}
.quick-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
}

.quick-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    transition: all 0.3s;
}
.quick-card:hover .quick-card-icon {
    transform: scale(1.1) rotate(5deg);
}

.qc-blue { border-top: 3px solid #3b82f6; }
.qc-blue .quick-card-icon { background: rgba(59, 130, 246, 0.08); color: #3b82f6; }
.qc-blue:hover { border-color: #3b82f6; }

.qc-red { border-top: 3px solid #ef4444; }
.qc-red .quick-card-icon { background: rgba(239, 68, 68, 0.08); color: #ef4444; }
.qc-red:hover { border-color: #ef4444; }

.qc-purple { border-top: 3px solid #8b5cf6; }
.qc-purple .quick-card-icon { background: rgba(139, 92, 246, 0.08); color: #8b5cf6; }
.qc-purple:hover { border-color: #8b5cf6; }

.qc-green { border-top: 3px solid #10b981; }
.qc-green .quick-card-icon { background: rgba(16, 185, 129, 0.08); color: #10b981; }
.qc-green:hover { border-color: #10b981; }

.quick-card-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.quick-card-title {
    font-weight: 800;
    font-size: 0.95rem;
    color: var(--text-main);
}
.quick-card-desc {
    font-size: 0.78rem;
    color: var(--text-muted);
    line-height: 1.4;
}
.quick-card-arrow {
    position: absolute;
    bottom: 20px;
    right: 20px;
    font-size: 0.85rem;
    color: var(--text-muted);
    opacity: 0;
    transform: translateX(-5px);
    transition: all 0.25s ease;
}
[dir="rtl"] .quick-card-arrow {
    right: auto;
    left: 20px;
    transform: translateX(5px);
}
.quick-card:hover .quick-card-arrow {
    opacity: 1;
    transform: translateX(0);
}
</style>

{{-- Welcome Banner & Wallet Card --}}
<div class="welcome-banner-wrapper cdash-animate" style="animation-delay: 0.05s;">
    <div class="welcome-banner-main">
        <h2>{{ __('Welcome') }}, {{ auth()->user()->first_name }}! 👋</h2>
        <p>{{ __('Manage your bookings, favorites, and payments easily.') }}</p>
    </div>
    <div class="welcome-wallet-card">
        <div class="wallet-card-header">
            <span>{{ __('Wallet Balance') }}</span>
            <i class="fas fa-wallet"></i>
        </div>
        <div class="wallet-card-balance">
            <span class="balance-value">{{ number_format($walletBalance, 2) }}</span>
            <span class="balance-currency">{{ __('SAR') }}</span>
        </div>
        <div class="wallet-card-footer">
            <a href="{{ route('customer.wallet.index') }}" class="btn-wallet-action">
                {{ __('Manage Wallet') }} <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

{{-- Stats Cards --}}
<div class="stats-grid cdash-animate" style="animation-delay: 0.15s;">
    @foreach($stats as $stat)
        <div class="stat-card {{ $stat['color'] }}">
            <div class="stat-icon-wrapper">
                <i class="{{ $stat['icon'] }}"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">{{ $stat['label'] }}</div>
                <div class="stat-value">{{ $stat['value'] }}</div>
            </div>
        </div>
    @endforeach
</div>

{{-- Main Content Grid --}}
<div class="dash-two-cols cdash-animate" style="animation-delay: 0.25s;">

    {{-- Upcoming Bookings --}}
    <div class="dash-section">
        <div class="dash-section-header">
            <h3><i class="fas fa-calendar-alt"></i> {{ __('Latest Bookings') }}</h3>
            <a href="{{ route('customer.bookings.index') }}" class="dash-section-link">
                {{ __('View All') }} <i class="fas fa-chevron-right small"></i>
            </a>
        </div>
        <div class="dash-section-body">
            @forelse($upcomingBookings as $booking)
                <div class="booking-row">
                    @php
                        $isTrip = $booking instanceof \App\Models\TripBooking;
                        $isFlight = $booking instanceof \App\Models\Booking;
                        $isHotel = $booking instanceof \App\Models\HotelBooking;
                    @endphp

                    @if($isTrip)
                        @if($booking->trip && $booking->trip->image_url)
                            <img src="{{ $booking->trip->image_url }}" alt="" class="booking-thumb">
                        @else
                            <div class="booking-thumb-placeholder trip-type"><i class="fas fa-map-marked-alt"></i></div>
                        @endif

                        <div class="booking-info">
                            <div class="booking-title-wrapper">
                                <span class="booking-title">{{ $booking->trip->title ?? __('Trip') }}</span>
                                <span class="type-badge type-trip"><i class="fas fa-map-marked-alt"></i> {{ __('Trip') }}</span>
                            </div>
                            <div class="booking-meta">
                                <span class="status-badge-wrapper status-{{ $booking->status }}">
                                    <span class="pulse-dot"></span>
                                    {{ $booking->status === 'pending' ? __('Pending') : ($booking->status === 'confirmed' ? __('Confirmed') : __('Cancelled')) }}
                                </span>
                                <span class="bullet-sep">·</span>
                                <span>{{ $booking->tickets_count }} {{ __('Passenger') }}</span>
                                @if($booking->booking_date)
                                    <span class="bullet-sep">·</span>
                                    <span>{{ $booking->booking_date->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="booking-price-wrapper">
                            <span class="booking-price">{{ number_format($booking->total_price, 0) }} {{ __('SAR') }}</span>
                            <span class="booking-price-label">{{ __('Total Price') }}</span>
                        </div>

                    @elseif($isFlight)
                        <div class="booking-thumb-placeholder flight-type"><i class="fas fa-plane"></i></div>

                        <div class="booking-info">
                            <div class="booking-title-wrapper">
                                <span class="booking-title">{{ $booking->airline_name ?: __('Flight') }} ({{ $booking->pnr_code ?: $booking->booking_reference }})</span>
                                <span class="type-badge type-flight"><i class="fas fa-plane"></i> {{ __('Flight') }}</span>
                            </div>
                            <div class="booking-meta">
                                <span class="status-badge-wrapper status-{{ $booking->status }}">
                                    <span class="pulse-dot"></span>
                                    {{ $booking->status === 'pending' ? __('Pending') : ($booking->status === 'confirmed' ? __('Confirmed') : __('Cancelled')) }}
                                </span>
                                <span class="bullet-sep">·</span>
                                <span>{{ $booking->passengers()->count() }} {{ __('Passenger') }}</span>
                                <span class="bullet-sep">·</span>
                                <span>{{ ($booking->pnr_created_at ?: $booking->created_at)->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        <div class="booking-price-wrapper">
                            <span class="booking-price">{{ number_format($booking->total_amount, 0) }} {{ __('SAR') }}</span>
                            <span class="booking-price-label">{{ __('Total Price') }}</span>
                        </div>

                    @elseif($isHotel)
                        <div class="booking-thumb-placeholder hotel-type"><i class="fas fa-hotel"></i></div>

                        <div class="booking-info">
                            <div class="booking-title-wrapper">
                                <span class="booking-title">{{ $booking->hotel_name ?? __('Hotel') }}</span>
                                <span class="type-badge type-hotel"><i class="fas fa-hotel"></i> {{ __('Hotel') }}</span>
                            </div>
                            <div class="booking-meta">
                                <span class="status-badge-wrapper status-{{ $booking->status }}">
                                    <span class="pulse-dot"></span>
                                    {{ $booking->status === 'pending' ? __('Pending') : ($booking->status === 'confirmed' ? __('Confirmed') : __('Cancelled')) }}
                                </span>
                                <span class="bullet-sep">·</span>
                                <span>{{ $booking->city_name }}</span>
                                @if($booking->check_in)
                                    <span class="bullet-sep">·</span>
                                    <span>{{ $booking->check_in->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="booking-price-wrapper">
                            <span class="booking-price">{{ number_format($booking->total_price, 0) }} {{ __('SAR') }}</span>
                            <span class="booking-price-label">{{ __('Total Price') }}</span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-ticket-alt"></i>
                    <p>{{ __('No bookings yet') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Payments --}}
    <div class="dash-section">
        <div class="dash-section-header">
            <h3><i class="fas fa-credit-card"></i> {{ __('Latest Payments') }}</h3>
            <a href="{{ route('customer.payments.index') }}" class="dash-section-link">
                {{ __('View All') }} <i class="fas fa-chevron-right small"></i>
            </a>
        </div>
        <div class="dash-section-body">
            @forelse($recentPayments as $payment)
                @php
                    $payTitle = __('Payment');
                    $payGateway = strtolower($payment->payment_gateway);
                    
                    if ($payment->payable) {
                        if ($payment->payable instanceof \App\Models\TripBooking) {
                            $payTitle = $payment->payable->trip->title ?? __('Trip Payment');
                        } elseif ($payment->payable instanceof \App\Models\Booking) {
                            $payTitle = $payment->payable->airline_name ?? __('Flight Payment');
                        } elseif ($payment->payable instanceof \App\Models\HotelBooking) {
                            $payTitle = $payment->payable->hotel_name ?? __('Hotel Payment');
                        }
                    } elseif ($payment->booking && $payment->booking->trip) {
                        $payTitle = $payment->booking->trip->title;
                    }
                @endphp
                <div class="payment-row">
                    <div class="pay-info">
                        <div class="pay-trip">{{ $payTitle }}</div>
                        <div class="pay-meta-row">
                            <span>{{ $payment->created_at->format('d/m/Y') }}</span>
                            <span class="bullet-sep">·</span>
                            <span class="gateway-badge gw-{{ $payGateway ?: 'card' }}">{{ $payment->payment_method ?: $payment->payment_gateway ?: 'Card' }}</span>
                        </div>
                    </div>
                    <div class="pay-amount-wrapper">
                        @php
                            $statusLower = strtolower($payment->status);
                            $amountClass = ($statusLower === 'success' || $statusLower === 'paid') ? '' : (($statusLower === 'pending' || $statusLower === 'processing') ? 'pay-pending' : 'pay-failed');
                            $statusLabel = ($statusLower === 'success' || $statusLower === 'paid') ? __('Paid') : (($statusLower === 'pending' || $statusLower === 'processing') ? __('Pending') : __('Failed'));
                        @endphp
                        <span class="pay-amount {{ $amountClass }}">+{{ number_format($payment->amount, 0) }} {{ __('SAR') }}</span>
                        <span class="pay-status-text {{ $amountClass }}">{{ $statusLabel }}</span>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-receipt"></i>
                    <p>{{ __('No payments yet') }}</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- Quick Access Section --}}
<div class="dash-section quick-access-section cdash-animate" style="animation-delay: 0.35s;">
    <div class="dash-section-header">
        <h3><i class="fas fa-bolt"></i> {{ __('Quick Access') }}</h3>
    </div>
    <div class="dash-section-body">
        <div class="quick-cards-grid">
            <a href="{{ route('customer.bookings.index') }}" class="quick-card qc-blue">
                <div class="quick-card-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="quick-card-info">
                    <span class="quick-card-title">{{ __('My Bookings') }}</span>
                    <span class="quick-card-desc">{{ __('Manage flights, hotel reservations, and trip bookings.') }}</span>
                </div>
                <i class="fas fa-arrow-right quick-card-arrow"></i>
            </a>

            <a href="{{ route('customer.favorites.index') }}" class="quick-card qc-red">
                <div class="quick-card-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="quick-card-info">
                    <span class="quick-card-title">{{ __('Favorites') }}</span>
                    <span class="quick-card-desc">{{ __('Explore and track your saved trips and vacation packages.') }}</span>
                </div>
                <i class="fas fa-arrow-right quick-card-arrow"></i>
            </a>

            <a href="{{ route('customer.profile') }}" class="quick-card qc-purple">
                <div class="quick-card-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div class="quick-card-info">
                    <span class="quick-card-title">{{ __('Profile') }}</span>
                    <span class="quick-card-desc">{{ __('Update your profile details and change account settings.') }}</span>
                </div>
                <i class="fas fa-arrow-right quick-card-arrow"></i>
            </a>

            <a href="{{ route('customer.support.index') }}" class="quick-card qc-green">
                <div class="quick-card-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="quick-card-info">
                    <span class="quick-card-title">{{ __('Support Center') }}</span>
                    <span class="quick-card-desc">{{ __('Create or view support tickets for help with bookings.') }}</span>
                </div>
                <i class="fas fa-arrow-right quick-card-arrow"></i>
            </a>
        </div>
    </div>
</div>

@endsection