@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')

{{-- ═══ Welcome Banner ═══ --}}
<div class="row">
    <div class="col-12 mb-4">
        <div class="dash-banner position-relative overflow-hidden">
            {{-- Decorative SVG Blobs --}}
            <div class="dash-blob dash-blob-1"></div>
            <div class="dash-blob dash-blob-2"></div>
            <div class="dash-blob dash-blob-3"></div>

            <div class="dash-banner-inner position-relative z-2">
                <div class="row align-items-center gy-3">
                    <div class="col-lg-8">
                        <div class="dash-date-pill mb-3">
                            <i class="far fa-calendar-alt me-2"></i>{{ date('l, d M Y') }}
                        </div>
                        <h2 class="dash-greeting">
                            {{ $greeting }}, <span class="dash-name">{{ $adminName }}</span>!
                            <span class="wave-emoji">👋</span>
                        </h2>
                        <p class="dash-subtitle">{{ __('Welcome to your travel hub. Here is an overview for today.') }}</p>
                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <a href="{{ route('admin.trips.create') }}" class="dash-btn dash-btn-gold">
                                <i class="fas fa-plus"></i> {{ __('Add New Trip') }}
                            </a>
                            <a href="{{ route('admin.trip-bookings.index') }}" class="dash-btn dash-btn-ghost">
                                <i class="fas fa-calendar-check"></i> {{ __('Manage Bookings') }}
                            </a>
                            <a href="{{ route('admin.support.index') }}" class="dash-btn dash-btn-ghost">
                                <i class="fas fa-headset"></i> {{ __('Support Center') }}
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 d-none d-lg-flex justify-content-end align-items-center">
                        <div class="dash-icon-ring">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ KPI Cards ═══ --}}
<div class="row g-4 mb-2">

    {{-- Revenue --}}
    <div class="col-xl-4 col-md-6">
        <div class="kpi-card kpi-card--green">
            <div class="kpi-icon-wrap">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Total Revenue') }}</span>
                <h3 class="kpi-value">
                    <span class="animate-counter format-currency" data-target="{{ $stats['revenue_total'] }}">0.00</span>
                    <small class="kpi-currency">{{ __('SAR') }}</small>
                </h3>
                <span class="kpi-badge kpi-badge--green">
                    <i class="fas fa-arrow-trend-up me-1"></i>{{ __('Total lifetime') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Bookings --}}
    <div class="col-xl-4 col-md-6">
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-icon-wrap">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Total Bookings') }}</span>
                <h3 class="kpi-value">
                    <span class="animate-counter" data-target="{{ $stats['bookings_total'] }}">0</span>
                </h3>
                <span class="kpi-badge kpi-badge--amber">
                    <i class="fas fa-clock me-1"></i>{{ $stats['bookings_pending'] }} {{ __('Pending') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Active Trips --}}
    <div class="col-xl-4 col-md-6">
        <div class="kpi-card kpi-card--indigo">
            <div class="kpi-icon-wrap">
                <i class="fas fa-plane-departure"></i>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Active Trips') }}</span>
                <h3 class="kpi-value">
                    <span class="animate-counter" data-target="{{ $stats['trips_active'] }}">0</span>
                </h3>
                <span class="kpi-badge kpi-badge--red">
                    <i class="fas fa-ban me-1"></i>{{ $stats['trips_expired'] }} {{ __('Expired') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Total Users --}}
    <div class="col-xl-4 col-md-6">
        <div class="kpi-card kpi-card--teal">
            <div class="kpi-icon-wrap">
                <i class="fas fa-users"></i>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Total Users') }}</span>
                <h3 class="kpi-value">
                    <span class="animate-counter" data-target="{{ $stats['users_total'] }}">0</span>
                </h3>
                <span class="kpi-badge kpi-badge--green">
                    <i class="fas fa-user-plus me-1"></i>{{ $stats['users_new_today'] }} {{ __('Today') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Today's Bookings --}}
    <div class="col-xl-4 col-md-6">
        <div class="kpi-card kpi-card--amber">
            <div class="kpi-icon-wrap">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __("Today's Bookings") }}</span>
                <h3 class="kpi-value">
                    <span class="animate-counter" data-target="{{ $stats['bookings_today'] }}">0</span>
                </h3>
                <span class="kpi-badge kpi-badge--amber">
                    <i class="fas fa-calendar-day me-1"></i>{{ __('Today') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Support Tickets --}}
    <div class="col-xl-4 col-md-6">
        <div class="kpi-card kpi-card--red">
            <div class="kpi-icon-wrap">
                <i class="fas fa-headset"></i>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Support Tickets') }}</span>
                <h3 class="kpi-value">
                    <span class="animate-counter" data-target="{{ $stats['tickets_open'] }}">0</span>
                </h3>
                <span class="kpi-badge kpi-badge--red">
                    <i class="fas fa-circle-exclamation me-1"></i>{{ $stats['tickets_open'] }} {{ __('Open') }}
                </span>
            </div>
        </div>
    </div>

</div>

{{-- ═══ Charts Row ═══ --}}
<div class="row g-4 mb-4">
    {{-- Line Chart --}}
    <div class="col-xl-8 col-lg-12">
        <div class="dash-chart-card h-100">
            <div class="dash-chart-header">
                <div>
                    <h6 class="dash-chart-title">{{ __('User Growth Analysis') }}</h6>
                    <p class="dash-chart-sub">{{ __('Monthly user registration trend for the current year') }}</p>
                </div>
                <div class="dash-chart-legend">
                    <span class="legend-dot"></span> {{ __('New Users') }}
                </div>
            </div>
            <div class="dash-chart-body">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Donut Chart --}}
    <div class="col-xl-4 col-lg-12">
        <div class="dash-chart-card h-100">
            <div class="dash-chart-header">
                <div>
                    <h6 class="dash-chart-title">{{ __('Booking States') }}</h6>
                    <p class="dash-chart-sub">{{ __('Breakdown of booking statuses') }}</p>
                </div>
            </div>
            <div class="dash-chart-body d-flex align-items-center justify-content-center" style="padding-top: 0;">
                <canvas id="bookingStatesChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ═══ Recent Data Tables ═══ --}}
<div class="row g-4 mb-4">
    {{-- Recent Bookings --}}
    <div class="col-xl-6 col-lg-12">
        <div class="dash-table-card h-100">
            <div class="dash-table-header">
                <div>
                    <h6 class="dash-chart-title">{{ __('Recent Bookings') }}</h6>
                    <p class="dash-chart-sub">{{ __('Latest customer reservations') }}</p>
                </div>
                <a href="{{ route('admin.trip-bookings.index') }}" class="dash-view-all-btn">
                    {{ __('View All') }} <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table dash-table mb-0">
                    <tbody>
                        @forelse($recentBookings->take(5) as $booking)
                            <tr>
                                <td class="ps-4 py-3" style="width: 50px;">
                                    <div class="dash-avatar dash-avatar--primary">
                                        <i class="fas fa-ticket-alt"></i>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <h6 class="dash-row-title">{{ $booking->user->name ?? __('Guest') }}</h6>
                                    <p class="dash-row-sub"><i class="fas fa-map-marker-alt me-1"></i>{{ \Illuminate\Support\Str::limit($booking->trip->title_ar ?? $booking->trip->title_en ?? __('Trip'), 25) }}</p>
                                </td>
                                <td class="py-3 text-center">
                                    @php
                                        $stateClass = 'badge-state--default';
                                        $stateLabel = $booking->booking_state;
                                        if($booking->booking_state === 'awaiting_payment') $stateClass = 'badge-state--amber';
                                        elseif($booking->booking_state === 'completed') $stateClass = 'badge-state--green';
                                        elseif($booking->booking_state === 'cancelled') $stateClass = 'badge-state--red';
                                        elseif(in_array($booking->booking_state, ['preparing', 'confirmed', 'issuing_tickets', 'tickets_uploaded', 'tickets_sent'])) $stateClass = 'badge-state--blue';
                                    @endphp
                                    <span class="badge-state {{ $stateClass }}">{{ __($stateLabel) }}</span>
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <div class="dash-amount">{{ number_format($booking->total_price, 2) }} <span class="dash-currency">SAR</span></div>
                                    <div class="dash-time"><i class="far fa-clock me-1"></i>{{ $booking->created_at->diffForHumans(null, true) }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="fas fa-inbox fa-2x mb-3 text-muted opacity-25 d-block"></i>
                                    <span class="text-muted fw-semibold" style="font-size: 13px;">{{ __('No recent bookings yet.') }}</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- New Members --}}
    <div class="col-xl-6 col-lg-12">
        <div class="dash-table-card h-100">
            <div class="dash-table-header">
                <div>
                    <h6 class="dash-chart-title">{{ __('New Members') }}</h6>
                    <p class="dash-chart-sub">{{ __('Recently joined users') }}</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="dash-view-all-btn">
                    {{ __('Manage') }} <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table dash-table mb-0">
                    <tbody>
                        @forelse($latestUsers->take(5) as $user)
                            <tr>
                                <td class="ps-4 py-3" style="width: 50px;">
                                    <div class="dash-avatar dash-avatar--navy">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                </td>
                                <td class="py-3">
                                    <h6 class="dash-row-title">{{ $user->name }}</h6>
                                    <p class="dash-row-sub"><i class="far fa-envelope me-1"></i>{{ \Illuminate\Support\Str::limit($user->email, 28) }}</p>
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <span class="dash-date-badge">
                                        <i class="far fa-calendar-alt me-1"></i>{{ $user->created_at->format('d M Y') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <i class="fas fa-users fa-2x mb-3 text-muted opacity-25 d-block"></i>
                                    <span class="text-muted fw-semibold" style="font-size: 13px;">{{ __('No new members.') }}</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ═══ Partners & Categories ═══ --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="dash-partners-card">
            <div class="dash-partners-bg-shape"></div>
            <div class="row align-items-center position-relative z-2 gy-4">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-4">
                        <div class="dash-partners-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <div>
                            <h5 class="dash-partners-title">{{ __('Business Partners & Categories') }}</h5>
                            <p class="dash-partners-sub">
                                {{ __('Manage your registered travel partners and trip categories. Currently you have') }}
                                <strong class="text-warning">{{ $stats['companies_count'] }}</strong>
                                {{ __('partners active.') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-wrap justify-content-lg-end gap-3">
                        <a href="{{ route('admin.trip-categories.index') }}" class="dash-btn dash-btn-gold">
                            <i class="fas fa-tags"></i> {{ __('Categories') }}
                        </a>
                        <a href="{{ route('admin.companies.index') }}" class="dash-btn dash-btn-ghost">
                            <i class="fas fa-building"></i> {{ __('Companies') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* ════════════════════════════════════
   ADMIN DASHBOARD – Design System
   Primary: #041741 | Gold: #f5a623
   ════════════════════════════════════ */
:root {
    --dash-navy: #041741;
    --dash-navy-2: #0a2456;
    --dash-navy-3: #0d2d6e;
    --dash-gold: #f5a623;
    --dash-gold-2: #e09010;
    --dash-surface: #ffffff;
    --dash-bg: #f5f7fb;
    --dash-text: #1e293b;
    --dash-muted: #64748b;
    --dash-border: #e8edf5;
    --dash-radius: 16px;
    --dash-radius-sm: 10px;
    --dash-shadow: 0 4px 24px rgba(4, 23, 65, 0.06);
    --dash-shadow-hover: 0 12px 36px rgba(4, 23, 65, 0.13);
}

/* ─── Welcome Banner ─── */
.dash-banner {
    background: linear-gradient(135deg, var(--dash-navy) 0%, var(--dash-navy-3) 60%, #1a3a8f 100%);
    border-radius: 20px;
    padding: 40px 44px;
    box-shadow: 0 16px 50px rgba(4, 23, 65, 0.22);
    animation: dashSlideDown 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes dashSlideDown {
    from { opacity: 0; transform: translateY(-18px); }
    to { opacity: 1; transform: translateY(0); }
}

.dash-blob {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}
.dash-blob-1 {
    width: 260px; height: 260px;
    top: -80px; right: -60px;
    background: radial-gradient(circle, rgba(245,166,35,0.18) 0%, transparent 70%);
}
.dash-blob-2 {
    width: 180px; height: 180px;
    bottom: -60px; left: 10%;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
}
.dash-blob-3 {
    width: 120px; height: 120px;
    top: 20%; right: 20%;
    background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
}

.dash-banner-inner { position: relative; }

.dash-date-pill {
    display: inline-flex;
    align-items: center;
    background: rgba(255,255,255,0.12);
    color: rgba(255,255,255,0.85);
    border: 1px solid rgba(255,255,255,0.15);
    backdrop-filter: blur(8px);
    border-radius: 50px;
    padding: 6px 16px;
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0.3px;
}

.dash-greeting {
    font-size: 2rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 8px;
    line-height: 1.3;
}
.dash-name { color: var(--dash-gold); }

.dash-subtitle {
    color: rgba(255,255,255,0.60);
    font-size: 14px;
    margin-bottom: 0;
}

.wave-emoji {
    display: inline-block;
    animation: waveAnim 2.5s infinite;
    transform-origin: 70% 70%;
}
@keyframes waveAnim {
    0%,60%,100% { transform: rotate(0deg); }
    10%,30% { transform: rotate(14deg); }
    20% { transform: rotate(-8deg); }
    40% { transform: rotate(-4deg); }
    50% { transform: rotate(10deg); }
}

.dash-icon-ring {
    width: 110px; height: 110px;
    background: rgba(255,255,255,0.08);
    border: 2px solid rgba(245,166,35,0.30);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    backdrop-filter: blur(10px);
}
.dash-icon-ring i {
    font-size: 2.8rem;
    color: var(--dash-gold);
    filter: drop-shadow(0 0 14px rgba(245,166,35,0.4));
}

/* ─── Buttons ─── */
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 20px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.25s ease;
    white-space: nowrap;
}
.dash-btn-gold {
    background: var(--dash-gold);
    color: var(--dash-navy);
    box-shadow: 0 4px 14px rgba(245,166,35,0.35);
}
.dash-btn-gold:hover {
    background: var(--dash-gold-2);
    color: var(--dash-navy);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(245,166,35,0.45);
}
.dash-btn-ghost {
    background: rgba(255,255,255,0.10);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.18);
    backdrop-filter: blur(8px);
}
.dash-btn-ghost:hover {
    background: rgba(255,255,255,0.18);
    color: #fff;
    transform: translateY(-2px);
}

/* ─── KPI Cards ─── */
.kpi-card {
    display: flex;
    align-items: flex-start;
    gap: 18px;
    background: var(--dash-surface);
    border-radius: var(--dash-radius);
    padding: 24px;
    box-shadow: var(--dash-shadow);
    border: 1px solid var(--dash-border);
    transition: all 0.3s ease;
    animation: kpiFadeIn 0.6s ease backwards;
    height: 100%;
}
.kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--dash-shadow-hover);
}
@keyframes kpiFadeIn {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: translateY(0); }
}
.kpi-card:nth-child(1) { animation-delay: 0.0s; }
.kpi-card:nth-child(2) { animation-delay: 0.08s; }
.kpi-card:nth-child(3) { animation-delay: 0.16s; }
.kpi-card:nth-child(4) { animation-delay: 0.24s; }
.kpi-card:nth-child(5) { animation-delay: 0.32s; }
.kpi-card:nth-child(6) { animation-delay: 0.40s; }

.kpi-icon-wrap {
    flex-shrink: 0;
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
}
/* Color variants for icon wrap */
.kpi-card--green  .kpi-icon-wrap { background: rgba(16,185,129,0.12); color: #059669; }
.kpi-card--blue   .kpi-icon-wrap { background: rgba(4,23,65,0.09);   color: var(--dash-navy); }
.kpi-card--indigo .kpi-icon-wrap { background: rgba(99,102,241,0.12); color: #4f46e5; }
.kpi-card--teal   .kpi-icon-wrap { background: rgba(20,184,166,0.12); color: #0d9488; }
.kpi-card--amber  .kpi-icon-wrap { background: rgba(245,158,11,0.12); color: #d97706; }
.kpi-card--red    .kpi-icon-wrap { background: rgba(239,68,68,0.12);  color: #dc2626; }

/* Left accent bar */
.kpi-card--green  { border-left: 4px solid #10b981; }
.kpi-card--blue   { border-left: 4px solid var(--dash-navy); }
.kpi-card--indigo { border-left: 4px solid #6366f1; }
.kpi-card--teal   { border-left: 4px solid #14b8a6; }
.kpi-card--amber  { border-left: 4px solid #f59e0b; }
.kpi-card--red    { border-left: 4px solid #ef4444; }

[dir="rtl"] .kpi-card--green,
[dir="rtl"] .kpi-card--blue,
[dir="rtl"] .kpi-card--indigo,
[dir="rtl"] .kpi-card--teal,
[dir="rtl"] .kpi-card--amber,
[dir="rtl"] .kpi-card--red {
    border-left: none;
    border-right: 4px solid;
}
[dir="rtl"] .kpi-card--green  { border-right-color: #10b981; }
[dir="rtl"] .kpi-card--blue   { border-right-color: var(--dash-navy); }
[dir="rtl"] .kpi-card--indigo { border-right-color: #6366f1; }
[dir="rtl"] .kpi-card--teal   { border-right-color: #14b8a6; }
[dir="rtl"] .kpi-card--amber  { border-right-color: #f59e0b; }
[dir="rtl"] .kpi-card--red    { border-right-color: #ef4444; }

.kpi-info { flex: 1; }
.kpi-label {
    font-size: 12px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.6px;
    color: var(--dash-muted);
    display: block; margin-bottom: 6px;
}
.kpi-value {
    font-size: 1.85rem; font-weight: 800;
    color: var(--dash-text);
    margin-bottom: 8px;
    line-height: 1.1;
}
.kpi-currency {
    font-size: 13px; font-weight: 500; color: var(--dash-muted);
}
.kpi-badge {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 600;
    padding: 4px 10px; border-radius: 50px;
}
.kpi-badge--green  { background: rgba(16,185,129,0.12); color: #059669; }
.kpi-badge--blue   { background: rgba(4,23,65,0.08);   color: var(--dash-navy); }
.kpi-badge--amber  { background: rgba(245,158,11,0.12); color: #b45309; }
.kpi-badge--red    { background: rgba(239,68,68,0.10);  color: #dc2626; }
.kpi-badge--indigo { background: rgba(99,102,241,0.10); color: #4f46e5; }

/* ─── Chart Cards ─── */
.dash-chart-card {
    background: var(--dash-surface);
    border-radius: var(--dash-radius);
    border: 1px solid var(--dash-border);
    box-shadow: var(--dash-shadow);
    overflow: hidden;
    transition: box-shadow 0.3s ease;
}
.dash-chart-card:hover { box-shadow: var(--dash-shadow-hover); }

.dash-chart-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 22px 24px 16px;
    border-bottom: 1px solid var(--dash-border);
}
.dash-chart-title {
    font-size: 15px; font-weight: 700; color: var(--dash-text); margin-bottom: 3px;
}
.dash-chart-sub {
    font-size: 11.5px; color: var(--dash-muted); margin: 0;
}
.dash-chart-legend {
    display: flex; align-items: center; gap: 7px;
    font-size: 12px; font-weight: 500; color: var(--dash-muted);
    white-space: nowrap;
}
.legend-dot {
    width: 10px; height: 10px; border-radius: 50%;
    background: var(--dash-navy);
    flex-shrink: 0;
}
.dash-chart-body {
    padding: 20px 24px 24px;
    height: 310px;
    position: relative;
}

/* ─── Table Cards ─── */
.dash-table-card {
    background: var(--dash-surface);
    border-radius: var(--dash-radius);
    border: 1px solid var(--dash-border);
    box-shadow: var(--dash-shadow);
    overflow: hidden;
    transition: box-shadow 0.3s ease;
}
.dash-table-card:hover { box-shadow: var(--dash-shadow-hover); }

.dash-table-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 22px 24px 16px;
    border-bottom: 1px solid var(--dash-border);
}

.dash-view-all-btn {
    display: inline-flex; align-items: center;
    font-size: 12px; font-weight: 600;
    color: var(--dash-navy);
    background: rgba(4,23,65,0.07);
    border-radius: 50px;
    padding: 6px 14px;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s ease;
}
.dash-view-all-btn:hover {
    background: var(--dash-navy);
    color: #fff;
}

.dash-table { margin: 0; }
.dash-table tr { transition: background 0.18s ease; }
.dash-table tr:hover { background: rgba(4,23,65,0.025); }
.dash-table td {
    border-bottom: 1px solid var(--dash-border) !important;
    vertical-align: middle;
}
.dash-table tr:last-child td { border-bottom: none !important; }

.dash-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700;
}
.dash-avatar--navy {
    background: rgba(4,23,65,0.09);
    color: var(--dash-navy);
}
.dash-avatar--primary {
    background: rgba(4,23,65,0.09);
    color: var(--dash-navy);
}

.dash-row-title {
    font-size: 13.5px; font-weight: 700;
    color: var(--dash-text); margin-bottom: 3px;
}
.dash-row-sub {
    font-size: 11px; color: var(--dash-muted); margin: 0;
}
.dash-row-sub i { opacity: 0.5; }

.badge-state {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 600;
    padding: 4px 12px; border-radius: 50px;
}
.badge-state--amber  { background: rgba(245,158,11,0.12); color: #b45309; }
.badge-state--green  { background: rgba(16,185,129,0.12); color: #059669; }
.badge-state--red    { background: rgba(239,68,68,0.10);  color: #dc2626; }
.badge-state--blue   { background: rgba(4,23,65,0.09);   color: var(--dash-navy); }
.badge-state--default{ background: #f1f5f9; color: #64748b; }

.dash-amount {
    font-size: 13px; font-weight: 700; color: var(--dash-text);
}
.dash-currency { font-size: 10px; font-weight: 400; color: var(--dash-muted); }
.dash-time {
    font-size: 11px; color: var(--dash-muted); margin-top: 3px;
}
.dash-date-badge {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 500;
    color: var(--dash-muted);
    background: #f1f5f9;
    border-radius: 50px;
    padding: 4px 12px;
    border: 1px solid var(--dash-border);
}

/* ─── Partners Banner ─── */
.dash-partners-card {
    background: linear-gradient(135deg, var(--dash-navy) 0%, var(--dash-navy-3) 100%);
    border-radius: 18px;
    padding: 36px 40px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 40px rgba(4,23,65,0.20);
}
.dash-partners-bg-shape {
    position: absolute;
    top: -60px; right: -60px;
    width: 200px; height: 200px;
    background: rgba(245,166,35,0.12);
    border-radius: 50%;
    pointer-events: none;
}
.dash-partners-icon {
    width: 64px; height: 64px;
    background: rgba(245,166,35,0.18);
    border: 2px solid rgba(245,166,35,0.30);
    border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.dash-partners-icon i { font-size: 1.7rem; color: var(--dash-gold); }
.dash-partners-title { font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 6px; }
.dash-partners-sub   { font-size: 13.5px; color: rgba(255,255,255,0.65); margin: 0; }

/* ─── Dark Mode ─── */
[data-theme-version="dark"] .kpi-card,
[data-theme-version="dark"] .dash-chart-card,
[data-theme-version="dark"] .dash-table-card {
    background: #1e1e2d !important;
    border-color: rgba(255,255,255,0.06) !important;
}
[data-theme-version="dark"] .dash-chart-header,
[data-theme-version="dark"] .dash-table-header {
    border-bottom-color: rgba(255,255,255,0.06) !important;
}
[data-theme-version="dark"] .kpi-value,
[data-theme-version="dark"] .dash-chart-title,
[data-theme-version="dark"] .dash-row-title,
[data-theme-version="dark"] .dash-amount,
[data-theme-version="dark"] .dash-partners-title { color: #fff !important; }
[data-theme-version="dark"] .kpi-label,
[data-theme-version="dark"] .dash-chart-sub,
[data-theme-version="dark"] .dash-muted,
[data-theme-version="dark"] .dash-row-sub { color: #94a3b8 !important; }
[data-theme-version="dark"] .dash-table td { border-bottom-color: rgba(255,255,255,0.05) !important; }
[data-theme-version="dark"] .dash-table tr:hover { background: rgba(255,255,255,0.03) !important; }
[data-theme-version="dark"] .dash-date-badge { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08); color: #94a3b8; }
[data-theme-version="dark"] .dash-view-all-btn { background: rgba(255,255,255,0.08); color: #e2e8f0; }
[data-theme-version="dark"] .dash-view-all-btn:hover { background: var(--dash-gold); color: var(--dash-navy); }

@media (max-width: 576px) {
    .dash-banner { padding: 28px 22px; }
    .dash-greeting { font-size: 1.45rem; }
    .kpi-card { padding: 18px; }
    .dash-partners-card { padding: 24px 22px; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ── Counter Animations ──
    document.querySelectorAll('.animate-counter').forEach(counter => {
        const target = +counter.getAttribute('data-target');
        const duration = 1400;
        const startTime = performance.now();
        function update(now) {
            const p = Math.min((now - startTime) / duration, 1);
            const ease = p * (2 - p);
            const val = ease * target;
            counter.innerText = counter.classList.contains('format-currency')
                ? new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val)
                : Math.floor(val).toLocaleString();
            if (p < 1) requestAnimationFrame(update);
        }
        requestAnimationFrame(update);
    });

    const isDark = document.body.getAttribute('data-theme-version') === 'dark';
    const navy = isDark ? '#a5b4fc' : '#041741';
    const gridC = isDark ? '#2d2d44' : '#f1f5f9';
    const textC = isDark ? '#94a3b8' : '#64748b';

    // ── User Growth Chart ──
    const gCtx = document.getElementById('userGrowthChart').getContext('2d');
    const grad = gCtx.createLinearGradient(0, 0, 0, 280);
    grad.addColorStop(0, isDark ? 'rgba(165,180,252,0.30)' : 'rgba(4,23,65,0.22)');
    grad.addColorStop(1, 'rgba(0,0,0,0)');

    new Chart(gCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: '{{ __("New Users") }}',
                data: {!! json_encode($chartData) !!},
                borderColor: navy,
                backgroundColor: grad,
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 7,
                pointBackgroundColor: isDark ? '#1e1e2d' : '#fff',
                pointBorderColor: navy,
                pointBorderWidth: 2.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: navy,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false,
                    titleFont: { size: 12 },
                    bodyFont: { size: 13, weight: 'bold' }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridC, drawBorder: false },
                    ticks: { color: textC, font: { size: 11 }, padding: 10 }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: textC, font: { size: 11 }, padding: 10 }
                }
            }
        }
    });

    // ── Booking States Donut ──
    const dCtx = document.getElementById('bookingStatesChart').getContext('2d');
    new Chart(dCtx, {
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
                backgroundColor: ['#f59e0b', '#041741', '#10b981', '#ef4444'],
                borderWidth: isDark ? 3 : 2,
                borderColor: isDark ? '#1e1e2d' : '#fff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: textC, padding: 14,
                        font: { size: 11 },
                        usePointStyle: true, pointStyleWidth: 8
                    }
                },
                tooltip: {
                    padding: 12, cornerRadius: 10,
                    titleFont: { size: 12 },
                    bodyFont: { size: 13, weight: 'bold' }
                }
            }
        }
    });
});
</script>
@endpush
