@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Hotel Bookings'))
@section('page-title', __('Hotel Bookings'))

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
    background: rgba(139, 92, 246, 0.08);
    color: #8b5cf6;
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

/* Location Header */
.ticket-location {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
    font-size: 0.85rem;
    font-weight: 800;
    color: var(--primary-blue);
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.ticket-location i {
    font-size: 0.9rem;
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

/* ─── Card Header Meta ─── */
.card-header-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 12px;
}
.hotel-badge-wrap {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.18);
    padding: 4px 12px;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 750;
    color: #7c3aed;
}
body.dark-mode .hotel-badge-wrap {
    background: rgba(139, 92, 246, 0.15);
    color: #a78bfa;
}
.hotel-loc-text {
    font-size: 0.82rem;
    color: var(--text-muted);
    font-weight: 600;
}
.pnr-badge-wrap {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.meta-tag {
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 750;
    color: var(--text-muted);
}
.meta-tag-supplier {
    background: rgba(16, 185, 129, 0.08);
    border-color: rgba(16, 185, 129, 0.25);
    color: #059669;
}
body.dark-mode .meta-tag-supplier {
    background: rgba(16, 185, 129, 0.15);
    color: #34d399;
}

/* ─── Stay Route Strip ─── */
.stay-route-strip {
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    gap: 12px;
}
.stay-point {
    display: flex;
    flex-direction: column;
}
.stay-date {
    font-size: 0.95rem;
    font-weight: 850;
    color: var(--text-main);
}
.stay-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 4px;
}
.stay-connector {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    flex: 1;
    max-width: 140px;
}
.stay-line {
    width: 100%;
    height: 2px;
    background: var(--border-color);
    position: relative;
}
.stay-duration-badge {
    background: rgba(37, 99, 235, 0.08);
    border: 1px solid rgba(37, 99, 235, 0.15);
    color: var(--primary-blue);
    font-size: 0.72rem;
    font-weight: 800;
    padding: 2px 10px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
body.dark-mode .stay-duration-badge {
    background: rgba(37, 99, 235, 0.18);
    color: #60a5fa;
}

/* ─── Contact Info Strip ─── */
.booking-contact-strip {
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px dashed var(--border-color);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px 18px;
}
.contact-strip-label {
    font-size: 0.78rem;
    font-weight: 800;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
}
.contact-strip-items {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 14px;
    align-items: center;
}
.contact-item {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--text-main);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--bg-main);
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
</style>
@endpush

@section('content')

<div class="booking-list-container">
    {{-- Filter Bar --}}
    <div class="filter-bar">
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('customer.bookings.hotels', request()->except(['status', 'page'])) }}" class="filter-btn {{ !request('status') ? 'active' : '' }}">
                {{ __('All Status') }}
            </a>
            <a href="{{ route('customer.bookings.hotels', array_merge(request()->except(['page']), ['status' => 'pending'])) }}" class="filter-btn {{ request('status') === 'pending' ? 'active' : '' }}">
                <i class="fas fa-clock"></i> {{ __('Pending') }}
            </a>
            <a href="{{ route('customer.bookings.hotels', array_merge(request()->except(['page']), ['status' => 'confirmed'])) }}" class="filter-btn {{ request('status') === 'confirmed' ? 'active' : '' }}">
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
    <form action="{{ route('customer.bookings.hotels') }}" method="GET" id="filterForm">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <div class="advanced-filters-panel" id="advancedFilters" style="{{ (request('search') || request('date_from') || request('date_to')) ? 'display: block;' : '' }}">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">{{ __('Search') }}</label>
                    <input type="text" name="search" class="filter-input" value="{{ request('search') }}" placeholder="{{ __('Search by Hotel Name, City, Country or Reference...') }}">
                </div>
                <div class="filter-group">
                    <label class="filter-label">{{ __('From Check-In Date') }}</label>
                    <input type="date" id="filter_date_from" name="date_from" class="filter-input" value="{{ request('date_from') }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="filter-group">
                    <label class="filter-label">{{ __('To Check-In Date') }}</label>
                    <input type="date" id="filter_date_to" name="date_to" class="filter-input" value="{{ request('date_to') }}" max="{{ date('Y-m-d') }}" @if(request('date_from')) min="{{ request('date_from') }}" @endif>
                </div>
            </div>
            <div class="filter-actions">
                <a href="{{ route('customer.bookings.hotels', request('status') ? ['status' => request('status')] : []) }}" class="btn-sm btn-outline" style="width: auto;">
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
            $paxList = $booking->pax_details ?? [];
            $firstPax = null;
            if (!empty($paxList) && is_array($paxList)) {
                if (isset($paxList[0]['pax'][0])) {
                    $p = $paxList[0]['pax'][0];
                    $firstPax = trim(($p['Title'] ?? '') . ' ' . ($p['FirstName'] ?? '') . ' ' . ($p['LastName'] ?? ''));
                } elseif (isset($paxList[0]['FirstName'])) {
                    $firstPax = trim(($paxList[0]['Title'] ?? '') . ' ' . ($paxList[0]['FirstName'] ?? '') . ' ' . ($paxList[0]['LastName'] ?? ''));
                }
            }
            $contactName = $firstPax ?: ($booking->user->full_name ?? ($booking->user->name ?? __('النزيل الرئيسي')));
            $contactPhone = $booking->contact_phone ?: ($booking->user->phone ?? null);
            $contactEmail = $booking->contact_email ?: ($booking->user->email ?? null);

            $nights = ($booking->check_in && $booking->check_out) ? $booking->check_in->diffInDays($booking->check_out) : 1;
            $nights = max(1, $nights);

            $isCancelled = ($booking->status === 'cancelled');
            $isPending = ($booking->status === 'pending');
            $isConfirmed = in_array($booking->status, ['confirmed', 'paid', 'completed']);
        @endphp

        <div class="booking-card">
            {{-- Main Column --}}
            <div class="booking-card-main">
                <div class="booking-img-placeholder" style="position: relative;">
                    <i class="fas fa-hotel"></i>
                </div>

                <div class="booking-details">
                    {{-- Header Badges --}}
                    <div class="card-header-meta">
                        <div class="hotel-badge-wrap">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            <span class="hotel-loc-text">{{ $booking->city_name ?: __('المدينة') }}@if($booking->country_name)، {{ $booking->country_name }}@endif</span>
                        </div>
                        <div class="pnr-badge-wrap">
                            <span class="meta-tag"><i class="fas fa-hashtag"></i> #{{ $booking->reference_num ?: $booking->id }}</span>
                            @if($booking->supplier_confirmation_num)
                                <span class="meta-tag meta-tag-supplier notranslate" translate="no"><i class="fas fa-barcode"></i> {{ $booking->supplier_confirmation_num }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Hotel Name --}}
                    <h3 class="booking-trip-name">{{ $booking->hotel_name }}</h3>

                    {{-- Stay Dates Route Strip --}}
                    <div class="stay-route-strip">
                        <div class="stay-point">
                            <span class="stay-label"><i class="far fa-calendar-check text-primary"></i> {{ __('تسجيل الوصول') }}</span>
                            <span class="stay-date">{{ $booking->check_in ? $booking->check_in->translatedFormat('d M Y') : 'N/A' }}</span>
                        </div>
                        <div class="stay-connector">
                            <span class="stay-duration-badge">
                                <i class="fas fa-moon"></i> {{ $nights }} {{ $nights > 1 ? __('ليالٍ') : __('ليلة') }}
                            </span>
                            <span class="stay-line"></span>
                        </div>
                        <div class="stay-point text-end">
                            <span class="stay-label"><i class="far fa-calendar-times text-danger"></i> {{ __('تسجيل المغادرة') }}</span>
                            <span class="stay-date">{{ $booking->check_out ? $booking->check_out->translatedFormat('d M Y') : 'N/A' }}</span>
                        </div>
                    </div>

                    {{-- Room & Stay Metadata Row --}}
                    <div class="booking-meta-row">
                        @if($booking->room_name)
                            <span>
                                <i class="fas fa-bed text-primary"></i>
                                <span>{{ $booking->room_name }}</span>
                            </span>
                        @endif
                        @if($booking->board_type)
                            <span>
                                <i class="fas fa-utensils text-warning"></i>
                                <span>{{ $booking->board_type }}</span>
                            </span>
                        @endif
                        <span>
                            <i class="fas fa-users text-primary"></i>
                            <span>{{ $booking->rooms ?: 1 }} {{ __('غرفة') }} • {{ $booking->adults ?: 1 }} {{ __('بالغين') }}@if($booking->childs) • {{ $booking->childs }} {{ __('أطفال') }}@endif</span>
                        </span>
                    </div>

                    {{-- Contact Info Strip (بيانات التواصل للحجز) --}}
                    <div class="booking-contact-strip">
                        <div class="contact-strip-label">
                            <i class="fas fa-address-card text-primary"></i> {{ __('بيانات التواصل للحجز:') }}
                        </div>
                        <div class="contact-strip-items">
                            <span class="contact-item" title="{{ __('اسم النزيل / العميل') }}">
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
                    <span class="booking-price">{{ number_format($booking->total_price, 2) }} <small style="font-size: 0.72rem; font-weight: 800;">{{ $booking->currency ?: __('SAR') }}</small></span>
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
                @elseif($isPending)
                    <span class="status-badge-wrapper status-pending">
                        <span class="pulse-dot"></span>
                        {{ __('بانتظار الدفع') }}
                    </span>
                @else
                    <span class="status-badge-wrapper status-confirmed">
                        <span class="pulse-dot"></span>
                        {{ __('مؤكد') }}
                    </span>
                @endif

                <div class="stub-actions">
                    <a href="{{ route('customer.bookings.show', ['id' => $booking->id, 'type' => 'hotel']) }}" class="btn-sm btn-outline">
                        <i class="fas fa-eye"></i> {{ __('تفاصيل الحجز') }}
                    </a>
                    @if($isConfirmed)
                        <a href="{{ route('customer.bookings.hotels.voucher', ['id' => $booking->id]) }}" class="btn-sm btn-outline" style="border-color: #10b981; color: #10b981; background: rgba(16, 185, 129, 0.02);">
                            <i class="fas fa-file-pdf"></i> {{ __('قسيمة الحجز') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-hotel"></i></div>
            <h3>{{ __('No hotel bookings found') }}</h3>
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
