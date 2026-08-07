@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Hotel Booking') . ' · ' . ($booking->reference_num ?? '#' . $booking->id))
@section('page-title', __('Hotel Booking Details'))

@push('styles')
<style>
/* ─── Variables & Animations ─── */
:root {
    --detail-border-radius: 20px;
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
.hotel-details-container {
    animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* ─── Back Link ─── */
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--text-muted);
    text-decoration: none !important;
    font-size: .88rem;
    margin-bottom: 24px;
    font-weight: 700;
    transition: all 0.2s ease;
}
.back-link:hover {
    color: var(--primary-blue);
    transform: translateX(-4px);
}
[dir="rtl"] .back-link:hover {
    transform: translateX(4px);
}

/* ─── Boarding Pass Stay Voucher ─── */
.boarding-pass {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--detail-border-radius);
    overflow: hidden;
    box-shadow: var(--ticket-shadow);
    margin-bottom: 28px;
    position: relative;
}
.pass-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    padding: 16px 24px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.pass-title {
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: 1.5px;
    color: rgba(255,255,255,0.7);
    text-transform: uppercase;
}
.pass-pnr {
    font-size: 0.9rem;
    font-weight: 900;
    letter-spacing: 0.5px;
    background: rgba(255,255,255,0.12);
    padding: 4px 14px;
    border-radius: 8px;
    color: #fff;
}
.pass-body {
    padding: 28px;
    position: relative;
}

.hotel-hero-section {
    display: flex;
    align-items: center;
    gap: 20px;
}
.hotel-avatar {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: rgba(37, 99, 235, 0.08);
    color: var(--primary-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    flex-shrink: 0;
}
body.dark-mode .hotel-avatar {
    background: rgba(37, 99, 235, 0.15);
}
.hotel-info .hotel-title {
    font-size: 1.4rem;
    font-weight: 900;
    color: var(--text-main);
    margin: 0 0 4px;
}
.hotel-info .hotel-loc {
    font-size: 0.88rem;
    color: var(--text-muted);
    font-weight: 600;
}
.hotel-info .hotel-loc i {
    color: var(--primary-blue);
}

.boarding-pass-stub-line {
    position: relative;
    border-top: 2px dashed var(--border-color);
    margin-top: 20px;
    padding-top: 20px;
}
.boarding-pass-stub-line::before, .boarding-pass-stub-line::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    border-radius: 50%;
    top: -11px;
    z-index: 5;
    transition: background-color 0.3s, border-color 0.3s;
}
.boarding-pass-stub-line::before {
    left: -39px;
}
.boarding-pass-stub-line::after {
    right: -39px;
}
[dir="rtl"] .boarding-pass-stub-line::before {
    left: auto;
    right: -39px;
}
[dir="rtl"] .boarding-pass-stub-line::after {
    right: auto;
    left: -39px;
}

.pass-details-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}
@media (max-width: 768px) {
    .pass-details-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
}
.pass-label {
    display: block;
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}
.pass-val {
    display: block;
    font-size: 0.95rem;
    font-weight: 850;
    color: var(--text-main);
}

/* ─── Detail Grid ─── */
.booking-detail-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
}
@media (max-width: 991px) {
    .booking-detail-grid {
        grid-template-columns: 1fr;
    }
}

/* ─── Detail Cards ─── */
.detail-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--detail-border-radius);
    box-shadow: var(--ticket-shadow);
    margin-bottom: 24px;
    overflow: hidden;
}
.detail-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    font-weight: 850;
    font-size: 1.05rem;
    color: var(--text-main);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(0, 0, 0, 0.005);
}
.detail-card-header h5 {
    margin: 0;
    font-weight: 850;
    font-size: 1.05rem;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 10px;
}
.detail-card-header h5 i {
    color: var(--primary-blue);
    font-size: 1.15rem;
}
.detail-card-body {
    padding: 24px;
}

/* ─── Info Rows ─── */
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid var(--border-color);
    font-size: .9rem;
}
.info-row:last-child {
    border-bottom: none;
}
.info-label {
    color: var(--text-muted);
    font-weight: 600;
}
.info-value {
    color: var(--text-main);
    font-weight: 750;
    text-align: end;
}

/* Dates layout */
.hbd-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 24px;
}
@media (max-width: 576px) {
    .hbd-row-2 {
        grid-template-columns: 1fr;
    }
}
.dates-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.date-box label {
    display: block;
    font-size: 0.72rem;
    color: var(--text-muted);
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 4px;
}
.date-box strong {
    font-size: 1rem;
    font-weight: 850;
    color: var(--text-main);
}
.date-arrow {
    color: var(--text-muted);
    font-size: 1.2rem;
}
.nights-pill {
    margin-top: 16px;
    background: rgba(37, 99, 235, 0.08);
    color: var(--primary-blue);
    border-radius: 100px;
    padding: 6px 14px;
    font-size: 0.85rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
body.dark-mode .nights-pill {
    background: rgba(37, 99, 235, 0.15);
}

/* Guest details styling */
.room-group {
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 16px;
    margin-bottom: 16px;
}
.room-group:last-child {
    border-bottom: none;
    padding-bottom: 0;
    margin-bottom: 0;
}
.room-group-label {
    font-size: 0.8rem;
    font-weight: 800;
    color: var(--primary-blue);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
}
.guest-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.guest-chip {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 0.88rem;
}
.guest-chip i {
    color: var(--text-muted);
}
.guest-chip span {
    font-weight: 700;
    color: var(--text-main);
}
.guest-chip small {
    color: var(--text-muted);
    font-weight: 600;
}

/* ─── Callout Alerts ─── */
.callout-alert {
    padding: 16px 20px;
    border-radius: 14px;
    margin-bottom: 24px;
    display: flex;
    align-items: start;
    gap: 14px;
    border: 1px solid transparent;
}
.callout-success {
    background: rgba(16, 185, 129, 0.06);
    color: #15803d;
    border-color: rgba(16, 185, 129, 0.15);
}
body.dark-mode .callout-success {
    background: rgba(16, 185, 129, 0.12);
    color: #10b981;
}
.callout-warning {
    background: rgba(249, 115, 22, 0.06);
    color: #b45309;
    border-color: rgba(249, 115, 22, 0.15);
}
body.dark-mode .callout-warning {
    background: rgba(249, 115, 22, 0.12);
    color: #f97316;
}
.callout-danger {
    background: rgba(239, 68, 68, 0.06);
    color: #b91c1c;
    border-color: rgba(239, 68, 68, 0.15);
}
body.dark-mode .callout-danger {
    background: rgba(239, 68, 68, 0.12);
    color: #ef4444;
}
.callout-alert i {
    font-size: 1.25rem;
    margin-top: 2px;
}
.conf-badge {
    margin-inline-start: auto;
    background: var(--bg-card);
    padding: 6px 16px;
    border-radius: 100px;
    font-weight: 900;
    font-size: 1rem;
    letter-spacing: 0.5px;
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

/* ─── Sidebar Pricing Card ─── */
.summary-total {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-radius: 18px;
    padding: 28px 24px;
    color: #fff;
    text-align: center;
    margin-bottom: 20px;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15);
    position: relative;
    overflow: hidden;
}
.summary-total::after {
    content: '';
    position: absolute;
    width: 150px;
    height: 150px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.08) 0%, transparent 70%);
    top: -30px;
    right: -30px;
}
.summary-total .amount {
    font-size: 2.2rem;
    font-weight: 950;
    letter-spacing: -0.5px;
    line-height: 1.1;
}
.summary-total .amount span {
    font-size: 1.1rem;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.7);
    margin-inline-start: 4px;
}
.summary-total .amount-label {
    font-size: .85rem;
    color: #94a3b8;
    margin-top: 6px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.refs-section {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 20px;
    text-align: start;
}
.ref-item {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 12px;
    padding: 12px 14px;
}
.ref-item-green {
    background: rgba(16, 185, 129, 0.15);
    border-color: rgba(16, 185, 129, 0.25);
}
.ref-item-warn {
    background: rgba(245, 158, 11, 0.15);
    border-color: rgba(245, 158, 11, 0.25);
}
.ref-item-red {
    background: rgba(239, 68, 68, 0.15);
    border-color: rgba(239, 68, 68, 0.25);
}
.ref-label {
    font-size: 0.65rem;
    color: rgba(255,255,255,0.65);
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.ref-val {
    font-size: 1.1rem;
    font-weight: 900;
    color: white;
}

/* ─── Actions Sidebar ─── */
.actions-stack {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 12px 20px;
    border-radius: 12px;
    text-align: center;
    font-weight: 700;
    font-size: .88rem;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.25s ease;
}
.action-btn-primary {
    background: var(--primary-blue);
    color: #fff;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
}
.action-btn-primary:hover {
    background: #1d4ed8;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
    color: #fff;
}
.action-btn-success {
    background: #10b981;
    color: #fff;
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.15);
}
.action-btn-success:hover {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25);
    color: #fff;
}
.action-btn-outline {
    border-color: var(--border-color);
    background: var(--bg-card);
    color: var(--text-main);
}
.action-btn-outline:hover {
    border-color: var(--primary-blue);
    color: var(--primary-blue);
    background: rgba(37, 99, 235, 0.03);
    transform: translateY(-2px);
}
.action-btn-danger-soft {
    background: rgba(239, 68, 68, 0.06);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.15);
}
.action-btn-danger-soft:hover {
    background: #ef4444;
    color: white;
    transform: translateY(-2px);
}

/* ─── Timeline ─── */
.timeline-v {
    display: flex;
    flex-direction: column;
}
.tl-item {
    display: flex;
    gap: 14px;
    position: relative;
    padding-bottom: 24px;
}
.tl-item:last-child {
    padding-bottom: 0;
}
.tl-item::before {
    content: '';
    position: absolute;
    inset-inline-start: 17px;
    top: 34px;
    width: 2px;
    height: calc(100% - 14px);
    background: var(--border-color);
}
.tl-item:last-child::before {
    display: none;
}
.tl-item.done::before {
    background: #10b981;
}
.tl-dot {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    flex-shrink: 0;
    background: var(--bg-main);
    border: 2px solid var(--border-color);
    color: var(--text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    position: relative;
    z-index: 1;
}
.tl-item.done .tl-dot {
    background: #10b981;
    border-color: #10b981;
    color: white;
}
.tl-item.cancelled .tl-dot {
    background: #ef4444;
    border-color: #ef4444;
    color: white;
}
.tl-text strong {
    display: block;
    font-size: 0.9rem;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 2px;
}
.tl-text small {
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 600;
}

/* Support link styling */
.support-links {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.support-links a {
    text-decoration: none;
    color: var(--text-muted);
    font-size: 0.88rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all .2s;
}
.support-links a:hover {
    color: var(--primary-blue);
    padding-inline-start: 4px;
}

/* ─── Tip Card ─── */
.card-tip {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 20px 24px;
    background: rgba(37, 99, 235, 0.05);
    border: 1px dashed var(--primary-blue);
    border-radius: var(--detail-border-radius);
}
.tip-icon {
    font-size: 1.5rem;
    color: var(--primary-blue);
    flex-shrink: 0;
    margin-top: 2px;
}
.card-tip h6 {
    font-weight: 800;
    color: var(--text-main);
    margin: 0 0 4px;
    font-size: 0.95rem;
}
.card-tip p {
    color: var(--text-muted);
    font-size: 0.85rem;
    margin: 0;
    line-height: 1.6;
}

/* Spin slow */
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.fa-spin-slow {
    animation: spin-slow 3s linear infinite;
}
</style>
@endpush

@section('content')
<div class="hotel-details-container">

    {{-- Back Link --}}
    <a href="{{ route('customer.bookings.hotels') }}" class="back-link">
        <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'right' : 'left' }}"></i>
        {{ __('Back to Bookings') }}
    </a>

    {{-- ─── Fetch Data States ─── --}}
    @php
        // Supplier confirmation visual confirm logic
        $displayStatus = $booking->status;
        if($booking->status === 'pending' && !empty($booking->supplier_confirmation_num)) {
            $displayStatus = 'confirmed';
        }

        // 10 minute expiry logic
        $isExpired = false;
        if ($booking->status === 'pending' && $booking->created_at->diffInMinutes(now()) >= 10) {
            $isExpired = true;
            if ($displayStatus !== 'confirmed') {
                $displayStatus = 'cancelled';
            }
        }
    @endphp

    {{-- Alerts --}}
    @if($displayStatus === 'confirmed')
        <div class="callout-alert callout-success">
            <i class="fas fa-check-circle"></i>
            <div>
                <strong>{{ __('Booking Confirmed!') }}</strong>
                <span>{{ __('Your stay at :hotel is fully confirmed.', ['hotel' => $booking->hotel_name]) }}</span>
            </div>
            @if($booking->supplier_confirmation_num)
                <div class="conf-badge">{{ $booking->supplier_confirmation_num }}</div>
            @endif
        </div>
    @elseif($displayStatus === 'paid' || $displayStatus === 'processing')
        <div class="callout-alert callout-warning">
            <i class="fas fa-hourglass-half fa-spin-slow"></i>
            <div>
                <strong>{{ __('Payment Received') }}</strong>
                <span>{{ __('Finalizing your reservation with the hotel supplier...') }}</span>
            </div>
        </div>
    @elseif($booking->status === 'cancelled')
        <div class="callout-alert callout-danger">
            <i class="fas fa-times-circle"></i>
            <div>
                <strong>{{ __('Booking Cancelled') }}</strong>
                <span>{{ __('This reservation has been cancelled.') }}</span>
            </div>
        </div>
    @endif

    {{-- ─── Stay Voucher Ticket Card ─── --}}
    <div class="boarding-pass">
        <div class="pass-header">
            <span class="pass-title">{{ __('HOTEL STAY VOUCHER') }}</span>
            <span class="pass-pnr">{{ __('Reference') }}: #{{ $booking->reference_num ?? $booking->id }}</span>
        </div>
        <div class="pass-body">
            <div class="hotel-hero-section">
                <div class="hotel-avatar"><i class="fas fa-hotel"></i></div>
                <div class="hotel-info">
                    <h3 class="hotel-title">{{ $booking->hotel_name }}</h3>
                    <span class="hotel-loc"><i class="fas fa-map-marker-alt"></i> {{ $booking->city_name }}, {{ $booking->country_name }}</span>
                </div>
            </div>

            <div class="boarding-pass-stub-line">
                <div class="pass-details-grid">
                    <div>
                        <span class="pass-label">{{ __('GUEST NAME') }}</span>
                        <span class="pass-val">{{ auth()->user()->full_name }}</span>
                    </div>
                    <div>
                        <span class="pass-label">{{ __('CHECK-IN') }}</span>
                        <span class="pass-val">{{ $booking->check_in->format('d M Y') }}</span>
                    </div>
                    <div>
                        <span class="pass-label">{{ __('CHECK-OUT') }}</span>
                        <span class="pass-val">{{ $booking->check_out->format('d M Y') }}</span>
                    </div>
                    <div>
                        <span class="pass-label">{{ __('ROOMS & NIGHTS') }}</span>
                        <span class="pass-val">{{ $booking->rooms }} {{ __('Room(s)') }} / {{ $booking->check_in->diffInDays($booking->check_out) }} {{ __('Nights') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Grid Layout --}}
    <div class="booking-detail-grid">

        {{-- LEFT COLUMN --}}
        <div>
            
            {{-- Dates + Duration --}}
            <div class="hbd-row-2">
                <div class="detail-card" style="margin-bottom: 0;">
                    <div class="detail-card-header" style="padding: 16px 20px;">
                        <h5><i class="fas fa-calendar-alt"></i> {{ __('Stay Duration') }}</h5>
                    </div>
                    <div class="detail-card-body">
                        <div class="dates-row">
                            <div class="date-box">
                                <label>{{ __('Check-in') }}</label>
                                <strong>{{ $booking->check_in->format('D, d M Y') }}</strong>
                            </div>
                            <div class="date-arrow"><i class="fas fa-long-arrow-alt-{{ app()->isLocale('ar') ? 'left' : 'right' }}"></i></div>
                            <div class="date-box text-end">
                                <label>{{ __('Check-out') }}</label>
                                <strong>{{ $booking->check_out->format('D, d M Y') }}</strong>
                            </div>
                        </div>
                        <div class="nights-pill">
                            <i class="fas fa-moon"></i>
                            {{ $booking->check_in->diffInDays($booking->check_out) }} {{ __('Nights') }}
                        </div>
                    </div>
                </div>

                <div class="detail-card" style="margin-bottom: 0;">
                    <div class="detail-card-header" style="padding: 16px 20px;">
                        <h5><i class="fas fa-bed"></i> {{ __('Accommodation') }}</h5>
                    </div>
                    <div class="detail-card-body">
                        <div class="info-row"><span>{{ __('Room') }}</span><strong>{{ $booking->room_name ?? 'N/A' }}</strong></div>
                        <div class="info-row"><span>{{ __('Board Type') }}</span><strong>{{ $booking->board_type ?? 'N/A' }}</strong></div>
                        <div class="info-row"><span>{{ __('Rooms Count') }}</span><strong>{{ $booking->rooms }}</strong></div>
                        <div class="info-row"><span>{{ __('Guests') }}</span><strong>{{ $booking->adults }} {{ __('Adult(s)') }}@if($booking->childs > 0), {{ $booking->childs }} {{ __('Child(ren)') }}@endif</strong></div>
                    </div>
                </div>
            </div>

            {{-- Guest details --}}
            <div class="detail-card">
                <div class="detail-card-header">
                    <h5><i class="fas fa-user-friends"></i> {{ __('Guest Details') }}</h5>
                </div>
                <div class="detail-card-body">
                    @if($booking->pax_details && is_array($booking->pax_details))
                        @foreach($booking->pax_details as $room)
                            <div class="room-group">
                                <div class="room-group-label"><i class="fas fa-door-open"></i> {{ __('Room') }} {{ $room['room_no'] ?? $loop->iteration }}</div>
                                <div class="guest-chips">
                                    @foreach($room['pax'] ?? [] as $pax)
                                        <div class="guest-chip">
                                            <i class="fas {{ ($pax['type'] ?? 'AD') === 'CH' ? 'fa-child' : 'fa-user' }}"></i>
                                            <span>{{ trim(($pax['Title'] ?? $pax['title'] ?? '') . ' ' . ($pax['FirstName'] ?? $pax['firstName'] ?? '') . ' ' . ($pax['LastName'] ?? $pax['lastName'] ?? '')) }}</span>
                                            <small>({{ ($pax['type'] ?? 'AD') === 'CH' ? __('Child') : __('Adult') }})</small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="info-row"><span>{{ __('Name') }}</span><strong>{{ $booking->user->name ?? 'Guest' }}</strong></div>
                        <div class="info-row"><span>{{ __('Email') }}</span><strong>{{ $booking->user->email ?? 'N/A' }}</strong></div>
                    @endif
                </div>
            </div>

            {{-- Check-in Instructions --}}
            <div class="card-tip">
                <i class="fas fa-info-circle tip-icon"></i>
                <div>
                    <h6>{{ __('Check-in Instructions') }}</h6>
                    <p>{{ __('Present this voucher (digital or printed) with your Passport/ID at the hotel reception. The hotel will use the Supplier Reference as proof of your reservation.') }}</p>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="hbd-sidebar">
            
            {{-- Payment Card summary total --}}
            <div class="summary-total">
                <div class="amount-label">{{ __('Total Paid') }}</div>
                <div class="amount">{{ number_format($booking->total_price, 2) }} <span>{{ $booking->currency }}</span></div>

                <div class="refs-section">
                    @if($booking->reference_num)
                        <div class="ref-item">
                            <div class="ref-label">{{ __('Booking ID') }}</div>
                            <div class="ref-val">{{ $booking->reference_num }}</div>
                        </div>
                    @endif

                    @if($isExpired && $displayStatus === 'cancelled')
                        <div class="ref-item ref-item-red">
                            <div class="ref-label"><i class="fas fa-clock"></i> {{ __('Session Expired') }}</div>
                            <div class="ref-val" style="font-size: 0.85rem;">{{ __('Please search again') }}</div>
                        </div>
                    @elseif($booking->supplier_confirmation_num)
                        <div class="ref-item ref-item-green">
                            <div class="ref-label"><i class="fas fa-check-circle"></i> {{ __('Supplier Reference') }}</div>
                            <div class="ref-val">{{ $booking->supplier_confirmation_num }}</div>
                        </div>
                    @elseif($displayStatus === 'paid' || $displayStatus === 'confirmed')
                        <div class="ref-item ref-item-blue" style="background: rgba(37, 99, 235, 0.15); border-color: rgba(37, 99, 235, 0.25);">
                            <div class="ref-label"><i class="fas fa-check-circle"></i> {{ __('Payment Confirmed') }}</div>
                            <div class="ref-val" style="font-size: 0.85rem;">{{ __('Finalizing Reservation...') }}</div>
                        </div>
                    @else
                        <div class="ref-item ref-item-warn">
                            <div class="ref-label"><i class="fas fa-hourglass-half"></i> {{ __('Supplier Confirmation') }}</div>
                            <div class="ref-val" style="font-size: 0.85rem;">{{ __('Pending Payment...') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions stack --}}
            <div class="detail-card">
                <div class="detail-card-body" style="padding: 20px;">
                    <div class="actions-stack">
                        @if($displayStatus === 'confirmed')
                            <a href="{{ route('customer.bookings.hotels.voucher', $booking->id) }}" class="action-btn action-btn-success">
                                <i class="fas fa-download"></i> {{ __('Download Voucher') }}
                            </a>
                        @endif

                        @if($booking->status === 'pending' && !$isExpired)
                            <a href="{{ route('hotels.payment.select', $booking->id) }}" class="action-btn action-btn-primary">
                                <i class="fas fa-credit-card"></i> {{ __('Complete Payment') }}
                            </a>
                        @endif

                        @if(!$isExpired)
                            <form id="sync-form" action="{{ route('customer.bookings.hotels.sync-status', $booking->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="action-btn action-btn-outline" style="width: 100%;">
                                    <i class="fas fa-sync-alt"></i> {{ __('Refresh Status') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Timeline progress --}}
            <div class="detail-card">
                <div class="detail-card-header" style="padding: 16px 20px;">
                    <h5><i class="fas fa-stream"></i> {{ __('Booking Progress') }}</h5>
                </div>
                <div class="detail-card-body" style="padding: 20px;">
                    <div class="timeline-v">
                        <div class="tl-item {{ in_array($displayStatus, ['pending','paid','confirmed']) ? 'done' : '' }}">
                            <div class="tl-dot"><i class="fas fa-file-alt"></i></div>
                            <div class="tl-text">
                                <strong>{{ __('Booking Created') }}</strong>
                                <small>{{ $booking->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        <div class="tl-item {{ in_array($displayStatus, ['paid','confirmed']) ? 'done' : '' }}">
                            <div class="tl-dot"><i class="fas fa-credit-card"></i></div>
                            <div class="tl-text">
                                <strong>{{ __('Payment Verified') }}</strong>
                                <small>{{ in_array($displayStatus, ['paid','confirmed']) ? __('Completed') : __('Awaiting...') }}</small>
                            </div>
                        </div>
                        <div class="tl-item {{ $displayStatus === 'confirmed' ? 'done' : ($displayStatus === 'cancelled' ? 'cancelled' : '') }}">
                            <div class="tl-dot"><i class="fas fa-check-double"></i></div>
                            <div class="tl-text">
                                <strong>{{ $displayStatus === 'cancelled' ? __('Cancelled') : __('Confirmed by Supplier') }}</strong>
                                <small>{{ $displayStatus === 'confirmed' ? __('Ready for Stay') : ($displayStatus === 'cancelled' ? __('Reservation Cancelled') : __('Processing...')) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cancellation or Support --}}
            <div class="detail-card">
                <div class="detail-card-header" style="padding: 16px 20px;">
                    <h5><i class="fas fa-shield-alt"></i> {{ __('Manage Booking') }}</h5>
                </div>
                <div class="detail-card-body" style="padding: 20px;">
                    @if($booking->status !== 'cancelled')
                        <button class="action-btn action-btn-danger-soft mb-3" id="btn-cancel-hotel" data-id="{{ $booking->id }}">
                            <i class="fas fa-times-circle"></i> {{ __('Request Cancellation') }}
                        </button>
                    @endif
                    <div class="support-links">
                        <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', '') }}" target="_blank"><i class="fab fa-whatsapp" style="color: #25d366;"></i> {{ __('Contact Support') }}</a>
                        <a href="#"><i class="fas fa-question-circle"></i> {{ __('Help Center') }}</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<form id="form-cancel-hotel" action="{{ route('customer.bookings.hotels.cancel', $booking->id) }}" method="POST" style="display:none;">@csrf</form>

@endsection

@push('scripts')
<script>
document.getElementById('btn-cancel-hotel')?.addEventListener('click', function() {
    if (confirm('{{ __("Are you sure you want to request cancellation?") }}')) {
        document.getElementById('form-cancel-hotel').submit();
    }
});
</script>
@endpush
