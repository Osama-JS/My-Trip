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
                    <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
                </div>
                <div class="filter-group">
                    <label class="filter-label">{{ __('To Check-In Date') }}</label>
                    <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
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
        <div class="booking-card">
            {{-- Main Column --}}
            <div class="booking-card-main">
                <div class="booking-img-placeholder"><i class="fas fa-hotel"></i></div>

                <div class="booking-details">
                    @if($booking->city_name || $booking->country_name)
                        <div class="ticket-location">
                            <i class="fas fa-map-marker-alt"></i> {{ $booking->city_name }}, {{ $booking->country_name }}
                        </div>
                    @endif

                    <div class="booking-trip-name">{{ $booking->hotel_name }}</div>
                    
                    <div class="booking-meta-row">
                        <span><i class="fas fa-calendar-check"></i> {{ __('Check-in') }}: {{ $booking->check_in->format('d/m/Y') }}</span>
                        <span><i class="fas fa-calendar-times"></i> {{ __('Check-out') }}: {{ $booking->check_out->format('d/m/Y') }}</span>
                        @if($booking->room_name)
                            <span><i class="fas fa-bed"></i> {{ $booking->room_name }}</span>
                        @endif
                        @if($booking->rooms)
                            <span><i class="fas fa-door-open"></i> {{ $booking->rooms }} {{ __('Room(s)') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Boarding Pass Cutout Divider --}}
            <div class="booking-card-divider"></div>

            {{-- Stub Column --}}
            <div class="booking-card-stub">
                <div class="price-section">
                    <span class="booking-price">{{ number_format($booking->total_price, 0) }} {{ $booking->currency ?: __('SAR') }}</span>
                    <span class="price-label">{{ __('Total Price') }}</span>
                </div>

                <span class="status-badge-wrapper status-{{ $booking->status }}">
                    <span class="pulse-dot"></span>
                    {{ $booking->status === 'pending' ? __('Pending') : ($booking->status === 'confirmed' ? __('Confirmed') : __('Cancelled')) }}
                </span>

                <div class="stub-actions">
                    <a href="{{ route('customer.bookings.show', ['id' => $booking->id, 'type' => 'hotel']) }}" class="btn-sm btn-outline">
                        <i class="fas fa-eye"></i> {{ __('Details') }}
                    </a>
                    @if($booking->status === 'confirmed')
                        <a href="{{ route('customer.bookings.hotels.voucher', ['id' => $booking->id]) }}" class="btn-sm btn-outline" style="border-color: #10b981; color: #10b981; background: rgba(16, 185, 129, 0.02);">
                            <i class="fas fa-file-pdf"></i> {{ __('Voucher') }}
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
</script>

@endsection
