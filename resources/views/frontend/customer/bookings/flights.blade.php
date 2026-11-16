@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Flight Bookings'))
@section('page-title', __('Flight Bookings'))

@push('styles')
<style>
/* ─── Global Variables & Animations ─── */
:root {
    --ticket-radius: 20px;
    --ticket-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.03), 0 4px 6px -4px rgba(0, 0, 0, 0.03), 0 0 0 1px rgba(0, 0, 0, 0.025);
    --ticket-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.03), 0 0 0 1px rgba(37, 99, 235, 0.15);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.booking-list-container {
    animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* ─── Filter Bar ─── */
.filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 24px;
}
.filter-btn {
    padding: 10px 22px;
    border-radius: 30px;
    border: 1px solid var(--border-color);
    background: var(--bg-card);
    color: var(--text-muted);
    font-size: .88rem;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.filter-btn:hover {
    color: var(--primary-blue);
    border-color: var(--primary-blue);
    background: rgba(37, 99, 235, 0.03);
    transform: translateY(-1px);
}
.filter-btn.active {
    background: var(--primary-blue);
    border-color: var(--primary-blue);
    color: #fff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.18);
}

/* ─── Advanced Filters Panel ─── */
.advanced-filters-panel {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 24px;
    box-shadow: var(--ticket-shadow);
    display: none;
    animation: fadeInUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.filters-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 16px;
}
@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
    }
}
.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.filter-label {
    font-size: 0.78rem;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.filter-input {
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    color: var(--text-main);
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 0.88rem;
    font-weight: 600;
    width: 100%;
    outline: none;
    transition: all 0.2s;
}
.filter-input:focus {
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
.filter-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border-color);
}

/* ─── Boarding Ticket Card ─── */
.booking-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--ticket-radius);
    margin-bottom: 24px;
    position: relative;
    overflow: visible;
    display: flex;
    box-shadow: var(--ticket-shadow);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.booking-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--ticket-shadow-hover);
    border-color: rgba(37, 99, 235, 0.15);
}

.booking-card-main {
    flex: 1;
    padding: 24px;
    min-width: 0;
    display: flex;
    gap: 20px;
    align-items: center;
}
@media (max-width: 768px) {
    .booking-card-main {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
}

.booking-img-placeholder {
    width: 96px;
    height: 96px;
    border-radius: 14px;
    background: rgba(59, 130, 246, 0.08);
    color: #3b82f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    flex-shrink: 0;
}

.booking-details {
    flex: 1;
    min-width: 0;
}

/* Route Visualizer */
.ticket-route {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    flex-wrap: wrap;
    gap: 8px;
}
.route-city {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--primary-blue);
    text-transform: uppercase;
}
.route-arrow {
    display: flex;
    align-items: center;
    gap: 6px;
    flex: 1;
    max-width: 120px;
    min-width: 40px;
    padding: 0 4px;
}
.route-line {
    height: 2px;
    background: var(--border-color);
    flex: 1;
    position: relative;
}
.route-arrow i {
    font-size: 0.75rem;
    color: var(--primary-blue);
    transform: rotate(90deg);
}
[dir="rtl"] .route-arrow i {
    transform: rotate(-90deg);
}

.booking-trip-name {
    font-weight: 850;
    font-size: 1.15rem;
    color: var(--text-main);
    margin: 0 0 10px;
    line-height: 1.3;
}

.booking-meta-row {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    font-size: .82rem;
    color: var(--text-muted);
}
.booking-meta-row span {
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 600;
}
.booking-meta-row i {
    color: var(--text-muted);
    font-size: 0.85rem;
}

/* Ticket divider with punch-holes */
.booking-card-divider {
    position: relative;
    width: 1px;
    border-left: 2px dashed var(--border-color);
    margin: 18px 0;
    flex-shrink: 0;
}
@media (max-width: 900px) {
    .booking-card-divider {
        width: 100%;
        height: 1px;
        border-left: none;
        border-top: 2px dashed var(--border-color);
        margin: 0;
    }
}

.booking-card-divider::before, .booking-card-divider::after {
    content: '';
    position: absolute;
    width: 18px;
    height: 18px;
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    border-radius: 50%;
    left: -10px;
    z-index: 5;
    transition: background-color 0.3s, border-color 0.3s;
}
.booking-card-divider::before {
    top: -28px;
}
.booking-card-divider::after {
    bottom: -28px;
}
@media (max-width: 900px) {
    .booking-card-divider::before {
        left: -10px;
        top: -9px;
    }
    .booking-card-divider::after {
        right: -10px;
        left: auto;
        bottom: -9px;
    }
}

/* Right ticket stub */
.booking-card-stub {
    width: 200px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    text-align: center;
    background: rgba(0, 0, 0, 0.005);
    flex-shrink: 0;
    gap: 12px;
}
@media (max-width: 900px) {
    .booking-card-stub {
        width: 100%;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        background: rgba(0, 0, 0, 0.005);
        padding: 16px 24px;
    }
}

.price-section {
    display: flex;
    flex-direction: column;
    gap: 4px;
    align-items: center;
}
@media (max-width: 900px) {
    .price-section {
        align-items: flex-start;
        text-align: left;
    }
    [dir="rtl"] .price-section {
        text-align: right;
    }
}

.booking-price {
    font-size: 1.4rem;
    font-weight: 950;
    color: var(--text-main);
    letter-spacing: -0.5px;
    line-height: 1;
}
.price-label {
    font-size: 0.72rem;
    color: var(--text-muted);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stub-actions {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
@media (max-width: 900px) {
    .stub-actions {
        width: auto;
        flex-direction: row;
        align-items: center;
    }
}

/* Buttons & Badges */
.btn-sm {
    padding: 8px 18px;
    border-radius: 12px;
    font-size: .82rem;
    font-weight: 750;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
}
.btn-outline {
    border-color: var(--border-color);
    background: var(--bg-card);
    color: var(--text-main);
}
.btn-outline:hover {
    border-color: var(--primary-blue);
    color: var(--primary-blue);
    background: rgba(37, 99, 235, 0.03);
    transform: translateY(-1px);
}
.btn-accent {
    background: var(--primary-blue);
    color: #fff;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
}
.btn-accent:hover {
    background: #1d4ed8;
    box-shadow: 0 6px 14px rgba(37, 99, 235, 0.25);
    transform: translateY(-1px);
}

/* Status wrapper */
.status-badge-wrapper {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
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

.status-badge-wrapper.status-confirmed,
.status-badge-wrapper.status-paid,
.status-badge-wrapper.status-ticketed,
.status-badge-wrapper.status-completed {
    background: rgba(16, 185, 129, 0.08);
    color: #15803d;
}
.status-badge-wrapper.status-confirmed .pulse-dot,
.status-badge-wrapper.status-paid .pulse-dot,
.status-badge-wrapper.status-ticketed .pulse-dot,
.status-badge-wrapper.status-completed .pulse-dot {
    background: #10b981;
    animation: statusPulseGreen 1.5s infinite;
}

.status-badge-wrapper.status-cancelled,
.status-badge-wrapper.status-failed,
.status-badge-wrapper.status-expired {
    background: rgba(239, 68, 68, 0.08);
    color: #b91c1c;
}
.status-badge-wrapper.status-cancelled .pulse-dot,
.status-badge-wrapper.status-failed .pulse-dot,
.status-badge-wrapper.status-expired .pulse-dot {
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

/* Empty State */
.empty-state {
    text-align: center;
    padding: 70px 30px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--ticket-radius);
    box-shadow: var(--ticket-shadow);
}
.empty-state .empty-icon {
    font-size: 4.5rem;
    color: var(--text-muted);
    margin-bottom: 20px;
    opacity: 0.25;
}
.empty-state h3 {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--text-main);
    margin: 0 0 8px;
}
.empty-state p {
    font-size: 0.95rem;
    color: var(--text-muted);
    margin: 0;
}

/* ─── Premium Flight Card Enhancements ─── */
.card-header-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 12px;
}
.airline-badge-wrap {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
    font-weight: 850;
    color: var(--text-main);
}
.flight-no-badge {
    background: rgba(37, 99, 235, 0.08);
    color: var(--primary-blue);
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 850;
}
.pnr-badge-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.meta-tag {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-muted);
    background: var(--bg-main);
    padding: 3px 8px;
    border-radius: 6px;
    border: 1px solid var(--border-color);
}
.meta-tag-pnr {
    color: var(--primary-blue);
    font-weight: 850;
    border-color: rgba(37, 99, 235, 0.25);
    background: rgba(37, 99, 235, 0.04);
}

/* Flight Route Strip */
.flight-route-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 12px 18px;
    margin-bottom: 14px;
}
.route-point {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.route-city-name {
    font-size: 0.9rem;
    font-weight: 850;
    color: var(--text-main);
}
.route-iata-code {
    font-size: 1.3rem;
    font-weight: 950;
    color: var(--primary-blue);
    line-height: 1;
    letter-spacing: 0.5px;
}
.route-iata-code.dest-iata {
    color: #10b981;
}
.route-arrow-connector {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    max-width: 140px;
    margin: 0 12px;
}
.route-path-line {
    height: 2px;
    background: linear-gradient(to right, var(--primary-blue), #10b981);
    flex: 1;
    border-radius: 2px;
}
.route-plane-icon {
    color: var(--primary-blue);
    font-size: 0.95rem;
}
[dir="rtl"] .route-plane-icon i {
    transform: rotate(180deg);
}

/* Contact Strip */
.booking-contact-strip {
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px dashed var(--border-color);
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.contact-strip-label {
    font-size: 0.78rem;
    font-weight: 850;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
}
.contact-strip-items {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.contact-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    font-weight: 750;
    color: var(--text-main);
    background: var(--bg-card);
    padding: 4px 10px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
}
.contact-link {
    text-decoration: none;
    transition: all 0.2s;
}
.contact-link:hover {
    border-color: var(--primary-blue);
    color: var(--primary-blue);
}

.airline-code-tag {
    position: absolute;
    bottom: -6px;
    background: var(--primary-blue);
    color: white;
    font-size: 0.65rem;
    font-weight: 900;
    padding: 1px 6px;
    border-radius: 4px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
</style>
@endpush

@section('content')

<div class="booking-list-container">
    {{-- Filter Bar --}}
    <div class="filter-bar">
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('customer.bookings.flights', request()->except(['status', 'page'])) }}" class="filter-btn {{ !request('status') ? 'active' : '' }}">
                {{ __('All Status') }}
            </a>
            <a href="{{ route('customer.bookings.flights', array_merge(request()->except(['page']), ['status' => 'pending'])) }}" class="filter-btn {{ request('status') === 'pending' ? 'active' : '' }}">
                <i class="fas fa-clock"></i> {{ __('Pending') }}
            </a>
            <a href="{{ route('customer.bookings.flights', array_merge(request()->except(['page']), ['status' => 'confirmed'])) }}" class="filter-btn {{ request('status') === 'confirmed' ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i> {{ __('Confirmed') }}
            </a>
        </div>
        <button class="filter-btn" type="button" onclick="toggleAdvancedFilters()" style="color: var(--text-main); border-color: var(--border-color);">
            <i class="fas fa-filter"></i> {{ __('Advanced Filters') }}
            @if(request('search') || request('date_from') || request('date_to'))
                <span style="display: inline-block; width: 8px; height: 8px; background: #ef4444; border-radius: 50%;"></span>
            @endif
        </button>
    </div>

    {{-- Advanced Filters Panel --}}
    <form action="{{ route('customer.bookings.flights') }}" method="GET" id="filterForm">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <div class="advanced-filters-panel" id="advancedFilters" style="{{ (request('search') || request('date_from') || request('date_to')) ? 'display: block;' : '' }}">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">{{ __('Search') }}</label>
                    <input type="text" name="search" class="filter-input" value="{{ request('search') }}" placeholder="{{ __('Search by PNR, Reference or Airline...') }}">
                </div>
                <div class="filter-group">
                    <label class="filter-label">{{ __('From Date') }}</label>
                    <input type="date" id="filter_date_from" name="date_from" class="filter-input" value="{{ request('date_from') }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="filter-group">
                    <label class="filter-label">{{ __('To Date') }}</label>
                    <input type="date" id="filter_date_to" name="date_to" class="filter-input" value="{{ request('date_to') }}" max="{{ date('Y-m-d') }}" @if(request('date_from')) min="{{ request('date_from') }}" @endif>
                </div>
            </div>
            <div class="filter-actions">
                <a href="{{ route('customer.bookings.flights', request('status') ? ['status' => request('status')] : []) }}" class="btn-sm btn-outline" style="width: auto;">
                    <i class="fas fa-undo"></i> {{ __('Reset') }}
                </a>
                <button type="submit" class="btn-sm btn-accent" style="width: auto;">
                    <i class="fas fa-search"></i> {{ __('Apply Filters') }}
                </button>
            </div>
        </div>
    </form>

    {{-- Bookings Loop --}}
    @forelse($bookings as $booking)
        @php
            $fb = $booking->flightBooking;
            $originCode = $fb->origin ?? 'N/A';
            $destCode = $fb->destination ?? 'N/A';

            // Check if itinerary_data has segments
            $itinData = $fb->itinerary_data ?? [];
            if (($originCode === 'N/A' || $destCode === 'N/A') && !empty($itinData['segments'][0]['legs'])) {
                $firstLeg = $itinData['segments'][0]['legs'][0] ?? [];
                $lastLeg = end($itinData['segments'][0]['legs']) ?: [];
                $originCode = $firstLeg['DepartureAirportLocationCode'] ?? $originCode;
                $destCode = $lastLeg['ArrivalAirportLocationCode'] ?? $destCode;
            }

            $isAr = app()->isLocale('ar');
            $originAirport = ($originCode !== 'N/A') ? \App\Models\Airport::where('airport_code', $originCode)->first() : null;
            $destAirport = ($destCode !== 'N/A') ? \App\Models\Airport::where('airport_code', $destCode)->first() : null;

            $originCity = ($isAr && !empty($originAirport->city_name_ar)) ? $originAirport->city_name_ar : ($originAirport->city_name ?? $originCode);
            $destCity = ($isAr && !empty($destAirport->city_name_ar)) ? $destAirport->city_name_ar : ($destAirport->city_name ?? $destCode);

            $depDate = $fb && $fb->departure_date ? \Carbon\Carbon::parse($fb->departure_date) : null;
            $retDate = $fb && $fb->return_date ? \Carbon\Carbon::parse($fb->return_date) : null;
            $isRoundTrip = !empty($retDate);

            // Contact Info
            $contactEmail = $booking->contact_email ?: ($fb->contact_email ?? ($booking->user->email ?? null));
            $contactPhone = $booking->contact_phone ?: ($fb->contact_phone ?? ($booking->user->phone ?? null));
            $leadPassenger = $booking->passengers->first();
            $contactName = $leadPassenger ? ($leadPassenger->first_name . ' ' . $leadPassenger->last_name) : ($booking->user->full_name ?? __('العميل'));

            $isConfirmedStatus = in_array($booking->status, ['confirmed', 'paid', 'ticketed', 'completed']);
            $isPendingStatus = ($booking->status === 'pending');
            $isCancelled = ($booking->status === 'cancelled');
        @endphp

        <div class="booking-card">
            {{-- Main Column --}}
            <div class="booking-card-main">
                <div class="booking-img-placeholder" style="position: relative;">
                    <i class="fas fa-plane-departure"></i>
                    @if($booking->airline_code)
                        <span class="airline-code-tag notranslate" translate="no">{{ strtoupper($booking->airline_code) }}</span>
                    @endif
                </div>

                <div class="booking-details">
                    {{-- Header Badges --}}
                    <div class="card-header-meta">
                        <div class="airline-badge-wrap">
                            <i class="fas fa-plane text-primary"></i>
                            <span class="airline-name-text">{{ $booking->airline_name ?: __('طيران') }}</span>
                            @if($fb && $fb->flight_number)
                                <span class="flight-no-badge notranslate" translate="no">{{ $fb->flight_number }}</span>
                            @endif
                        </div>
                        <div class="pnr-badge-wrap">
                            <span class="meta-tag"><i class="fas fa-ticket-alt"></i> #{{ $booking->booking_reference }}</span>
                            @if($booking->pnr_code)
                                <span class="meta-tag meta-tag-pnr notranslate" translate="no"><i class="fas fa-barcode"></i> PNR: {{ $booking->pnr_code }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- True Flight Route --}}
                    <div class="flight-route-strip">
                        <div class="route-point">
                            <span class="route-city-name">{{ $originCity }}</span>
                            <span class="route-iata-code notranslate" translate="no">{{ strtoupper(substr($originCode, 0, 3)) }}</span>
                        </div>
                        <div class="route-arrow-connector">
                            <span class="route-path-line"></span>
                            <div class="route-plane-icon">
                                <i class="fas fa-plane"></i>
                            </div>
                            <span class="route-path-line"></span>
                        </div>
                        <div class="route-point text-end">
                            <span class="route-city-name">{{ $destCity }}</span>
                            <span class="route-iata-code dest-iata notranslate" translate="no">{{ strtoupper(substr($destCode, 0, 3)) }}</span>
                        </div>
                    </div>

                    {{-- Flight Metadata Row --}}
                    <div class="booking-meta-row">
                        @if($depDate)
                            <span>
                                <i class="far fa-calendar-alt text-primary"></i>
                                <span>{{ __('تاريخ الإقلاع:') }} <strong>{{ $depDate->translatedFormat('d M Y') }}</strong></span>
                            </span>
                        @endif
                        @if($isRoundTrip)
                            <span>
                                <i class="fas fa-sync-alt text-warning"></i>
                                <span>{{ __('تاريخ العودة:') }} <strong>{{ $retDate->translatedFormat('d M Y') }}</strong></span>
                            </span>
                        @else
                            <span>
                                <i class="fas fa-long-arrow-alt-{{ $isAr ? 'left' : 'right' }} text-muted"></i>
                                <span>{{ __('ذهاب فقط') }}</span>
                            </span>
                        @endif
                        <span>
                            <i class="fas fa-users text-primary"></i>
                            <span>{{ $booking->passengers->count() }} {{ __('مسافر') }}</span>
                        </span>
                        @if($fb && $fb->flight_class)
                            <span>
                                <i class="fas fa-chair text-muted"></i>
                                <span>{{ $fb->flight_class }}</span>
                            </span>
                        @endif
                    </div>

                    {{-- Contact Info Strip (بيانات التواصل للحجز) --}}
                    <div class="booking-contact-strip">
                        <div class="contact-strip-label">
                            <i class="fas fa-address-card text-primary"></i> {{ __('بيانات التواصل للحجز:') }}
                        </div>
                        <div class="contact-strip-items">
                            <span class="contact-item" title="{{ __('المسافر الرئيسي / العميل') }}">
                                <i class="fas fa-user-circle text-primary"></i>
                                <span>{{ $contactName }}</span>
                            </span>
                            @if($contactPhone)
                                <a href="tel:{{ $contactPhone }}" class="contact-item contact-link" title="{{ __('رقم الهاتف') }}">
                                    <i class="fas fa-phone-alt text-success"></i>
                                    <span dir="ltr">{{ $contactPhone }}</span>
                                </a>
                            @endif
                            @if($contactEmail)
                                <a href="mailto:{{ $contactEmail }}" class="contact-item contact-link" title="{{ __('البريد الإلكتروني') }}">
                                    <i class="fas fa-envelope text-info"></i>
                                    <span>{{ $contactEmail }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Boarding Pass Cutout Divider --}}
            <div class="booking-card-divider"></div>

            {{-- Stub Column --}}
            <div class="booking-card-stub">
                <div class="price-section">
                    <span class="price-label">{{ __('إجمالي السعر') }}</span>
                    <span class="booking-price">{{ number_format($booking->total_amount, 2) }} <small style="font-size: 0.72rem; font-weight: 800;">{{ $booking->currency ?: __('SAR') }}</small></span>
                </div>

                @if($isCancelled)
                    <span class="status-badge-wrapper status-cancelled">
                        <span class="pulse-dot"></span>
                        {{ __('ملغي ومسترد') }}
                    </span>
                    @if($booking->refund_amount > 0)
                        <div style="font-size: 0.75rem; font-weight: 850; color: #166534; background: #dcfce7; padding: 4px 10px; border-radius: 6px; border: 1px solid #bbf7d0;">
                            +{{ number_format($booking->refund_amount, 2) }} {{ $booking->currency }} {{ __('بالمحفظة') }}
                        </div>
                    @endif
                @elseif($isPendingStatus)
                    <span class="status-badge-wrapper status-pending">
                        <span class="pulse-dot"></span>
                        {{ __('بانتظار الدفع') }}
                    </span>
                @else
                    <span class="status-badge-wrapper status-confirmed">
                        <span class="pulse-dot"></span>
                        {{ __('مؤكد ومصدر') }}
                    </span>
                @endif

                <div class="stub-actions">
                    <a href="{{ route('customer.bookings.show', ['id' => $booking->id, 'type' => 'flight']) }}" class="btn-sm btn-accent" style="padding: 10px 16px;">
                        <i class="fas fa-eye"></i> {{ __('عرض التذكرة والتفاصيل') }}
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-plane"></i></div>
            <h3>{{ __('No flight bookings found') }}</h3>
            <p>{{ __('No results match your active search filters. Try clearing some options.') }}</p>
        </div>
    @endforelse

    <div class="mt-4">{{ $bookings->links() }}</div>
</div>

<script>
function toggleAdvancedFilters() {
    const panel = document.getElementById('advancedFilters');
    if (panel.style.display === 'none' || panel.style.display === '') {
        panel.style.display = 'block';
    } else {
        panel.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const dateFromInput = document.getElementById('filter_date_from');
    const dateToInput = document.getElementById('filter_date_to');
    const filterForm = document.getElementById('filterForm');
    const today = new Date().toISOString().split('T')[0];

    if (!dateFromInput || !dateToInput) return;

    dateFromInput.max = dateToInput.value && dateToInput.value < today ? dateToInput.value : today;
    dateToInput.max = today;
    if (dateFromInput.value) {
        dateToInput.min = dateFromInput.value;
    }

    function showDateAlert(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: '{{ app()->getLocale() == "ar" ? "تنبيه بخصوص التاريخ" : "Invalid Date Selection" }}',
                text: msg,
                confirmButtonText: '{{ app()->getLocale() == "ar" ? "حسناً" : "OK" }}',
                confirmButtonColor: '#2563eb'
            });
        } else {
            alert(msg);
        }
    }

    dateFromInput.addEventListener('change', function() {
        if (this.value > today) {
            this.value = today;
            showDateAlert('{{ app()->getLocale() == "ar" ? "لا يمكن اختيار تاريخ في المستقبل" : "Future dates are not allowed" }}');
        }
        if (this.value) {
            dateToInput.min = this.value;
            if (dateToInput.value && dateToInput.value < this.value) {
                dateToInput.value = this.value;
            }
        } else {
            dateToInput.removeAttribute('min');
        }
    });

    dateToInput.addEventListener('change', function() {
        if (this.value > today) {
            this.value = today;
            showDateAlert('{{ app()->getLocale() == "ar" ? "لا يمكن اختيار تاريخ في المستقبل" : "Future dates are not allowed" }}');
        }
        if (this.value) {
            dateFromInput.max = this.value < today ? this.value : today;
            if (dateFromInput.value && dateFromInput.value > this.value) {
                dateFromInput.value = this.value;
            }
        } else {
            dateFromInput.max = today;
        }
    });

    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            const fromVal = dateFromInput.value;
            const toVal = dateToInput.value;

            if (fromVal && fromVal > today) {
                e.preventDefault();
                showDateAlert('{{ app()->getLocale() == "ar" ? "تاريخ البداية (من) لا يمكن أن يكون في المستقبل" : "From Date cannot be in the future" }}');
                return false;
            }
            if (toVal && toVal > today) {
                e.preventDefault();
                showDateAlert('{{ app()->getLocale() == "ar" ? "تاريخ النهاية (إلى) لا يمكن أن يكون في المستقبل" : "To Date cannot be in the future" }}');
                return false;
            }
            if (fromVal && toVal && toVal < fromVal) {
                e.preventDefault();
                showDateAlert('{{ app()->getLocale() == "ar" ? "تاريخ النهاية (إلى) لا يمكن أن يكون أصغر من تاريخ البداية (من)" : "To Date cannot be earlier than From Date" }}');
                return false;
            }
        });
    }
});
</script>

@endsection
