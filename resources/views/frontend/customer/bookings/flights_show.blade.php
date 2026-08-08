@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Flight Booking') . ' · #' . $booking->booking_reference)
@section('page-title', __('Flight Booking Details'))

@push('styles')
<style>
/* ─── Variables & Animations ─── */
:root {
    --detail-border-radius: 20px;
    --ticket-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.03), 0 4px 6px -4px rgba(0, 0, 0, 0.03), 0 0 0 1px rgba(0, 0, 0, 0.025);
    --ticket-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.03), 0 0 0 1px rgba(37, 99, 235, 0.15);
    --fd-success: #10b981;
    --fd-danger: #ef4444;
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
.flight-details-container {
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

/* ─── Boarding Pass ─── */
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
.pass-airport-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}
.pass-airport-code {
    display: flex;
    flex-direction: column;
}
.pass-airport-code h3 {
    font-size: 2.5rem;
    font-weight: 950;
    color: var(--primary-blue);
    margin: 0;
    line-height: 1;
    letter-spacing: -0.5px;
}
.pass-airport-code span {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-muted);
    margin-top: 4px;
}
.pass-airport-code.dest-code {
    align-items: flex-end;
}
.pass-path-line {
    flex: 1;
    height: 2px;
    background: var(--border-color);
    margin: 0 24px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}
.pass-path-line i {
    font-size: 1.25rem;
    color: var(--primary-blue);
    background: var(--bg-card);
    padding: 0 12px;
    z-index: 2;
    transition: background-color 0.3s;
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

/* ─── Passengers Seating Tags ─── */
.passenger-item {
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.25s ease;
}
.passenger-item:hover {
    border-color: rgba(37, 99, 235, 0.15);
    background: rgba(37, 99, 235, 0.01);
}
.passenger-item:last-child {
    margin-bottom: 0;
}
.passenger-main {
    display: flex;
    align-items: center;
    gap: 16px;
}
.passenger-avatar {
    width: 40px;
    height: 40px;
    background: var(--primary-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 800;
    font-size: .95rem;
    flex-shrink: 0;
    box-shadow: 0 4px 8px rgba(37, 99, 235, 0.15);
}
.passenger-info .p-name {
    font-weight: 800;
    font-size: 0.95rem;
    color: var(--text-main);
}
.passenger-info .p-meta {
    font-size: .8rem;
    color: var(--text-muted);
    margin-top: 3px;
}
.passenger-docs {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    align-items: center;
}
.ticket-tag, .passport-tag {
    display: flex;
    flex-direction: column;
    font-size: 0.72rem;
    background: rgba(0, 0, 0, 0.02);
    border: 1px solid var(--border-color);
    padding: 4px 10px;
    border-radius: 8px;
}
body.dark-mode .ticket-tag, body.dark-mode .passport-tag {
    background: rgba(255, 255, 255, 0.02);
}
.ticket-tag label, .passport-tag label {
    font-size: 0.6rem;
    color: var(--text-muted);
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 2px;
}
.ticket-tag strong, .passport-tag strong {
    color: var(--text-main);
    font-weight: 800;
}

/* ─── Detailed Journey Timeline ─── */
.segment-timeline {
    padding: 24px;
}
.segment-step {
    position: relative;
    padding-bottom: 30px;
    padding-inline-start: 32px;
}
.segment-step:last-child {
    padding-bottom: 0;
}
.segment-step::before {
    content: '';
    position: absolute;
    inset-inline-start: 11px;
    top: 24px;
    width: 2px;
    height: calc(100% - 16px);
    background: var(--border-color);
}
.segment-step:last-child::before {
    display: none;
}
.seg-dot {
    position: absolute;
    inset-inline-start: 0;
    top: 2px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: var(--bg-card);
    border: 2px solid var(--primary-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-blue);
    font-size: 0.7rem;
    z-index: 2;
}
.seg-airline {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}
.seg-airline img {
    height: 24px;
    width: auto;
    border-radius: 4px;
    background: #fff;
    padding: 2px;
    border: 1px solid var(--border-color);
}
.seg-airline span {
    font-size: 0.88rem;
    font-weight: 800;
    color: var(--text-main);
}
.seg-main {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 16px 20px;
    gap: 16px;
}
@media (max-width: 576px) {
    .seg-main {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }
    .seg-point.text-end {
        text-align: center !important;
    }
}
.seg-point .time {
    font-size: 1.2rem;
    font-weight: 900;
    color: var(--text-main);
}
.seg-point .airport {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--primary-blue);
    margin: 2px 0;
}
.seg-point .date {
    font-size: 0.78rem;
    color: var(--text-muted);
    font-weight: 600;
}
.seg-path {
    flex: 1;
    position: relative;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.seg-path-line {
    width: 100%;
    height: 2px;
    border-top: 2px dashed var(--border-color);
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 1;
}
.seg-path i {
    position: relative;
    z-index: 2;
    background: var(--bg-main);
    padding: 0 10px;
    color: var(--primary-blue);
    font-size: 0.95rem;
}
.seg-path .path-dur {
    font-size: 0.72rem;
    color: var(--text-muted);
    font-weight: 700;
    margin-top: 4px;
    position: relative;
    z-index: 2;
    background: var(--bg-main);
    padding: 0 6px;
}
.seg-footer {
    margin-top: 10px;
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 600;
    display: flex;
    gap: 16px;
}
.seg-footer span {
    display: flex;
    align-items: center;
    gap: 6px;
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

.price-breakdown {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 16px;
    margin-top: 16px;
    text-align: start;
}
.row-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.85rem;
    font-weight: 600;
}
.row-info:last-child {
    margin-bottom: 0;
}
.row-info span {
    color: rgba(255, 255, 255, 0.6);
}
.row-info strong {
    color: #fff;
    font-weight: 700;
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
    background: var(--fd-success, #10b981);
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

/* ─── Timeline Card ─── */
.timeline-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--detail-border-radius);
    padding: 24px;
    box-shadow: var(--ticket-shadow);
    margin-bottom: 24px;
}
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
    background: var(--fd-success, #10b981);
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
    background: var(--fd-success, #10b981);
    border-color: var(--fd-success, #10b981);
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

/* Status Badge */
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
.status-badge-wrapper.status-confirmed {
    background: rgba(16, 185, 129, 0.08);
    color: #15803d;
}
.status-badge-wrapper.status-confirmed .pulse-dot {
    background: #10b981;
}
.status-badge-wrapper.status-pending {
    background: rgba(249, 115, 22, 0.08);
    color: #c2410c;
}
.status-badge-wrapper.status-pending .pulse-dot {
    background: #f97316;
}
.status-badge-wrapper.status-cancelled {
    background: rgba(239, 68, 68, 0.08);
    color: #b91c1c;
}
.status-badge-wrapper.status-cancelled .pulse-dot {
    background: #ef4444;
}

/* Expiry timer */
.expired-msg-sidebar {
    background: rgba(239, 68, 68, 0.06);
    color: #ef4444;
    padding: 12px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 750;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid rgba(239, 68, 68, 0.15);
}
</style>
@endpush

@section('content')
<div class="flight-details-container">

    {{-- Back Link --}}
    <a href="{{ route('customer.bookings.flights') }}" class="back-link">
        <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'right' : 'left' }}"></i>
        {{ __('Back to Bookings') }}
    </a>

    {{-- Expiry Alerts & PNR States --}}
    @if($booking->status === 'pending' && $booking->ticketing_time_limit)
        <div class="callout-alert callout-warning" id="pnrTimer" data-expiry="{{ $booking->ticketing_time_limit->toIso8601String() }}">
            <i class="fas fa-hourglass-start fa-spin"></i>
            <div>
                <strong>{{ __('Action Required: Complete Payment') }}</strong>
                <div>{{ __('Your flight hold reservation is temporary. Please pay now to secure your ticket.') }}</div>
                <div class="timer-display-wrapper mt-2">
                    <span class="badge bg-danger p-2" id="timerDisplay" style="font-family: monospace; font-size: 1.1rem; font-weight: 800;">00:00</span>
                </div>
            </div>
        </div>
    @endif

    {{-- ─── Boarding Pass Ticket Header ─── --}}
    <div class="boarding-pass">
        <div class="pass-header">
            <span class="pass-title">{{ __('BOARDING PASS / FLIGHT TICKET') }}</span>
            <span class="pass-pnr">{{ __('Airline PNR') }}: {{ $booking->pnr_code ?: 'N/A' }}</span>
        </div>
        <div class="pass-body">
            @php
                $originCode = $booking->flightBooking->origin ?? 'N/A';
                $destCode = $booking->flightBooking->destination ?? 'N/A';
                
                // Fallback to itinerary data if N/A
                $itinData = $booking->flightBooking->itinerary_data ?? [];
                if (($originCode === 'N/A' || $destCode === 'N/A') && is_array($itinData)) {
                    $options = $itinData['FareItineraries']['FareItinerary']['OriginDestinationOptions'] ?? [];
                    $opts = $options['OriginDestinationOption'] ?? [];
                    if (isset($opts['FlightSegment'])) $opts = [$opts];
                    
                    if (!empty($opts)) {
                        $firstSeg = $opts[0]['FlightSegment'] ?? $opts[0][0]['FlightSegment'] ?? $opts[0];
                        $lastSeg = end($opts)['FlightSegment'] ?? end($opts);
                        if (is_array($lastSeg) && isset($lastSeg[0])) {
                            $lastSeg = end($lastSeg)['FlightSegment'] ?? end($lastSeg);
                        }
                        
                        if ($originCode === 'N/A') $originCode = $firstSeg['DepartureAirport']['LocationCode'] ?? $originCode;
                        if ($destCode === 'N/A') $destCode = $lastSeg['ArrivalAirport']['LocationCode'] ?? $destCode;
                    }
                }

                $originAirport = $originCode !== 'N/A' ? \App\Models\Airport::where('airport_code', $originCode)->first() : null;
                $destAirport = $destCode !== 'N/A' ? \App\Models\Airport::where('airport_code', $destCode)->first() : null;
                
                $originName = $originAirport ? ($originAirport->city_name . ' - ' . $originAirport->airport_name) : $originCode;
                $destName = $destAirport ? ($destAirport->city_name . ' - ' . $destAirport->airport_name) : $destCode;
            @endphp
            <div class="pass-airport-row">
                <div class="pass-airport-code">
                    <h3>{{ strtoupper(substr($originCode, 0, 3)) }}</h3>
                    <span style="font-size: 0.8rem; line-height: 1.2;">{{ $originName }}</span>
                </div>
                <div class="pass-path-line">
                    <i class="fas fa-plane"></i>
                </div>
                <div class="pass-airport-code dest-code">
                    <h3>{{ strtoupper(substr($destCode, 0, 3)) }}</h3>
                    <span style="font-size: 0.8rem; line-height: 1.2;">{{ $destName }}</span>
                </div>
            </div>

            <div class="boarding-pass-stub-line">
                <div class="pass-details-grid">
                    <div>
                        <span class="pass-label">{{ __('PASSENGER') }}</span>
                        <span class="pass-val">{{ auth()->user()->full_name }}</span>
                    </div>
                    <div>
                        <span class="pass-label">{{ __('BOOKING DATE') }}</span>
                        <span class="pass-val">{{ $booking->created_at->format('d M Y') }}</span>
                    </div>
                    <div>
                        <span class="pass-label">{{ __('DEPARTURE DATE') }}</span>
                        <span class="pass-val">{{ $booking->flightBooking->departure_date ? $booking->flightBooking->departure_date->format('d M Y') : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="pass-label">{{ __('CLASS') }}</span>
                        <span class="pass-val">{{ $booking->flightBooking->flight_class ?? __('Economy') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Layout Grid --}}
    <div class="booking-detail-grid">

        {{-- LEFT COLUMN --}}
        <div>
            
            {{-- Itinerary Card --}}
            <div class="detail-card">
                <div class="detail-card-header">
                    <h5><i class="fas fa-plane-departure"></i> {{ __('Flight Itinerary') }}</h5>
                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">{{ $booking->airline_name ?? __('Direct Flight') }}</span>
                </div>
                <div class="detail-card-body">
                    <div class="info-row">
                        <span class="info-label">{{ __('Origin') }}</span>
                        <span class="info-value">{{ $originName }} ({{ $originCode }})</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('Destination') }}</span>
                        <span class="info-value">{{ $destName }} ({{ $destCode }})</span>
                    </div>
                    
                    @php
                        $exactDepartureTime = null;
                        if (is_array($itinData)) {
                            $options = $itinData['FareItineraries']['FareItinerary']['OriginDestinationOptions'] ?? [];
                            $opts = $options['OriginDestinationOption'] ?? [];
                            if (isset($opts['FlightSegment'])) $opts = [$opts];
                            
                            foreach($opts as $opt) {
                                $segs = $opt['FlightSegment'] ?? $opt;
                                if (isset($segs['DepartureDateTime'])) {
                                    $exactDepartureTime = \Carbon\Carbon::parse($segs['DepartureDateTime']);
                                    break;
                                } elseif (is_array($segs)) {
                                    foreach($segs as $s) {
                                        $dt = $s['FlightSegment']['DepartureDateTime'] ?? $s['DepartureDateTime'] ?? null;
                                        if ($dt) {
                                            $exactDepartureTime = \Carbon\Carbon::parse($dt);
                                            break 2;
                                        }
                                    }
                                }
                            }
                        }
                    @endphp

                    <div class="info-row" style="background: rgba(37, 99, 235, 0.05); padding: 12px; border-radius: 8px; border: 1px solid rgba(37,99,235,0.2);">
                        <span class="info-label" style="color: var(--primary-blue); font-weight: 800;"><i class="fas fa-calendar-alt me-1"></i> {{ __('Departure') }}</span>
                        <span class="info-value text-dark" style="font-size: 1.1rem; font-weight: 900;">
                            {{ $exactDepartureTime ? $exactDepartureTime->format('d M Y, h:i A') : ($booking->flightBooking->departure_date ? $booking->flightBooking->departure_date->format('d M Y') : 'N/A') }}
                        </span>
                    </div>
                    @if($exactDepartureTime && $exactDepartureTime->isFuture())
                        <div class="info-row mt-3 p-3 rounded" style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid var(--fd-success);">
                            <span class="info-label text-success" style="font-weight: 800;"><i class="fas fa-stopwatch me-1"></i> {{ __('Time to Departure') }}</span>
                            <span class="info-value fw-bold text-success" style="font-size: 1.2rem;">{{ $exactDepartureTime->diffForHumans(['parts' => 2, 'short' => false]) }}</span>
                        </div>
                    @endif

                    @if($booking->flightBooking->flight_class)
                    <div class="info-row">
                        <span class="info-label">{{ __('Cabin Class') }}</span>
                        <span class="info-value">{{ $booking->flightBooking->flight_class }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Passengers Section --}}
            <div class="detail-card">
                <div class="detail-card-header">
                    <h5><i class="fas fa-users"></i> {{ __('Travelers') }}</h5>
                    <span class="badge" style="background: var(--primary-blue); color: white; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem;">
                        {{ $booking->passengers->count() }} {{ __('Total') }}
                    </span>
                </div>
                <div class="detail-card-body" style="padding: 16px;">
                    @forelse($booking->passengers as $pax)
                        <div class="passenger-item">
                            <div class="passenger-main">
                                <div class="passenger-avatar">
                                    <i class="fas {{ $pax->passenger_type === 'infant' ? 'fa-baby' : ($pax->passenger_type === 'child' ? 'fa-child' : 'fa-user') }}"></i>
                                </div>
                                <div class="passenger-info">
                                    <span class="p-name">{{ $pax->title }} {{ $pax->first_name }} {{ $pax->last_name }}</span>
                                    <span class="p-meta text-uppercase">
                                        {{ __($pax->passenger_type ?? 'adult') }}
                                    </span>
                                </div>
                            </div>
                            <div class="passenger-docs">
                                @if($pax->e_ticket_no && $booking->status === 'confirmed')
                                    <div class="ticket-tag" style="background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3); padding: 8px 14px; border-radius: 10px; flex: 1; min-width: 140px;">
                                        <label style="color: #10b981; font-size: 0.65rem;"><i class="fas fa-ticket-alt me-1"></i>{{ __('E-Ticket No') }}</label>
                                        <strong style="color: #047857; font-size: 1.05rem; letter-spacing: 0.5px;">{{ $pax->e_ticket_no }}</strong>
                                    </div>
                                @endif
                                @if($pax->passport_number)
                                    <div class="passport-tag" style="background: rgba(59, 130, 246, 0.08); border-color: rgba(59, 130, 246, 0.2); padding: 8px 14px; border-radius: 10px; flex: 1; min-width: 140px;">
                                        <label style="color: #3b82f6; font-size: 0.65rem;"><i class="fas fa-passport me-1"></i>{{ __('Passport') }}</label>
                                        <strong style="color: #1d4ed8; font-size: 1.05rem; letter-spacing: 0.5px;">{{ $pax->passport_number }}</strong>
                                    </div>
                                @endif
                                @if($pax->passport_expiry)
                                    <div class="passport-tag" style="padding: 8px 14px; border-radius: 10px; min-width: 100px;">
                                        <label><i class="fas fa-calendar-times me-1"></i>{{ __('Expiry') }}</label>
                                        <strong>{{ $pax->passport_expiry->format('d M Y') }}</strong>
                                    </div>
                                @endif
                                @if($pax->dob)
                                    <div class="passport-tag" style="padding: 8px 14px; border-radius: 10px; min-width: 100px;">
                                        <label><i class="fas fa-birthday-cake me-1"></i>{{ __('DOB') }}</label>
                                        <strong>{{ $pax->dob->format('d M Y') }}</strong>
                                    </div>
                                @endif
                                @if($pax->nationality)
                                    <div class="passport-tag" style="padding: 8px 14px; border-radius: 10px; min-width: 100px;">
                                        <label><i class="fas fa-globe-americas me-1"></i>{{ __('Nationality') }}</label>
                                        <strong>{{ $pax->nationality }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-user-slash d-block mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                            {{ __('No passenger details found.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Journey Timeline Segments --}}
            @php
                $itinData = $booking->flightBooking->itinerary_data ?? [];
                $segments = [];
                if (isset($itinData['FareItineraries']['FareItinerary']['OriginDestinationOptions'])) {
                    $options = $itinData['FareItineraries']['FareItinerary']['OriginDestinationOptions'];
                    if (isset($options['OriginDestinationOption']['FlightSegment'])) {
                        $options = [$options['OriginDestinationOption']];
                    } else {
                        $options = $options['OriginDestinationOption'] ?? [];
                    }
                    
                    foreach($options as $opt) {
                        $segs = $opt['FlightSegment'] ?? $opt;
                        if (isset($segs['FlightNumber'])) { $segments[] = $segs; }
                        else { foreach($segs as $s) { $segments[] = $s['FlightSegment'] ?? $s; } }
                    }
                }
            @endphp

            @if(!empty($segments))
                <div class="detail-card">
                    <div class="detail-card-header">
                        <h5><i class="fas fa-route"></i> {{ __('Detailed Journey') }}</h5>
                    </div>
                    <div class="segment-timeline">
                        @foreach($segments as $idx => $seg)
                            @php
                                $depTime = \Carbon\Carbon::parse($seg['DepartureDateTime']);
                                $arrTime = \Carbon\Carbon::parse($seg['ArrivalDateTime']);
                                $airlineCode = $seg['MarketingAirlineCode'] ?? $seg['OperatedByAirlineCode'] ?? '';
                            @endphp
                            <div class="segment-step">
                                <div class="seg-dot"><i class="fas fa-circle" style="font-size: 0.4rem;"></i></div>
                                <div class="seg-airline">
                                    <img src="https://travelnext.works/api/airlines/{{ $airlineCode }}.gif" 
                                         onerror="this.src='https://via.placeholder.com/30?text={{ $airlineCode }}'"
                                         alt="{{ $airlineCode }}">
                                    <span>{{ $airlineCode }} {{ $seg['FlightNumber'] ?? '' }}</span>
                                </div>
                                <div class="seg-main">
                                    <div class="seg-point">
                                        <div class="time">{{ $depTime->format('H:i') }}</div>
                                        <div class="airport"><strong>{{ $seg['DepartureAirportLocationCode'] }}</strong></div>
                                        <div class="date">{{ $depTime->format('d M, Y') }}</div>
                                    </div>
                                    <div class="seg-path">
                                        <div class="seg-path-line"></div>
                                        <i class="fas fa-plane"></i>
                                        <div class="path-dur">{{ $depTime->diff($arrTime)->format('%hh %im') }}</div>
                                    </div>
                                    <div class="seg-point text-end">
                                        <div class="time">{{ $arrTime->format('H:i') }}</div>
                                        <div class="airport"><strong>{{ $seg['ArrivalAirportLocationCode'] }}</strong></div>
                                        <div class="date">{{ $arrTime->format('d M, Y') }}</div>
                                    </div>
                                </div>
                                @if(isset($seg['ResBookDesigCode']))
                                    <div class="seg-footer">
                                        <span><i class="fas fa-couch"></i> {{ __('Class') }}: {{ $seg['ResBookDesigCode'] }}</span>
                                        @if(isset($seg['AdjustmentTime']))
                                            <span class="ms-3"><i class="fas fa-clock"></i> {{ __('Technical Stop') }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="fd-sidebar">
            
            {{-- Price Breakdown Stub --}}
            <div class="summary-total">
                <div class="amount-label">{{ __('Total Price') }}</div>
                <div class="amount">{{ number_format($booking->total_amount, 2) }} <span>{{ $booking->currency }}</span></div>
                
                <div class="price-breakdown">
                    <div class="row-info">
                        <span>{{ __('Base Fare') }}</span>
                        <strong>{{ number_format($booking->total_amount, 2) }} {{ $booking->currency }}</strong>
                    </div>
                    <div class="row-info">
                        <span>{{ __('Taxes & Fees') }}</span>
                        <strong>0.00 {{ $booking->currency }}</strong>
                    </div>
                </div>
            </div>

            {{-- Action Stack --}}
            <div class="detail-card">
                <div class="detail-card-body" style="padding: 20px;">
                    <div class="actions-stack">
                        @if($booking->status === 'pending')
                            @php
                                $isExpired = $booking->ticketing_time_limit && now()->greaterThan($booking->ticketing_time_limit);
                            @endphp
                            @if($isExpired)
                                <div class="expired-msg-sidebar">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>{{ __('This PNR reservation has expired.') }}</span>
                                </div>
                                <a href="{{ route('flights') }}" class="action-btn action-btn-outline">
                                    <i class="fas fa-search"></i> {{ __('Search Flights') }}
                                </a>
                            @else
                                <a href="{{ route('flights.payment-select', $booking->id) }}" class="action-btn action-btn-primary" id="payButton">
                                    <i class="fas fa-credit-card"></i> {{ __('Pay Now') }}
                                </a>
                            @endif
                        @elseif($booking->status === 'confirmed')
                            <a href="{{ route('customer.bookings.invoice', ['id' => $booking->id, 'type' => 'flight']) }}" class="action-btn action-btn-success">
                                <i class="fas fa-file-invoice-dollar"></i> {{ __('Download Voucher') }}
                            </a>
                        @endif
                        <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', '') }}" class="action-btn action-btn-outline" target="_blank">
                            <i class="fab fa-whatsapp" style="color: #25d366;"></i> {{ __('WhatsApp Support') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Booking Timeline --}}
            <div class="detail-card">
                <div class="detail-card-header" style="padding: 16px 20px;">
                    <h5><i class="fas fa-history"></i> {{ __('Booking Timeline') }}</h5>
                </div>
                <div class="detail-card-body" style="padding: 20px;">
                    <div class="timeline-v">
                        <div class="tl-item done">
                            <div class="tl-dot"><i class="fas fa-check"></i></div>
                            <div class="tl-text">
                                <strong>{{ __('Reservation Created') }}</strong>
                                <small>{{ $booking->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        <div class="tl-item {{ in_array($booking->status, ['paid', 'confirmed']) ? 'done' : '' }}">
                            <div class="tl-dot">
                                <i class="fas {{ in_array($booking->status, ['paid', 'confirmed']) ? 'fa-check' : 'fa-credit-card' }}"></i>
                            </div>
                            <div class="tl-text">
                                <strong>{{ __('Payment Verification') }}</strong>
                                <small>{{ in_array($booking->status, ['paid', 'confirmed']) ? __('Completed') : __('Awaiting Payment') }}</small>
                            </div>
                        </div>
                        <div class="tl-item {{ $booking->status === 'confirmed' ? 'done' : '' }}">
                            <div class="tl-dot">
                                <i class="fas {{ $booking->status === 'confirmed' ? 'fa-check' : 'fa-plane' }}"></i>
                            </div>
                            <div class="tl-text">
                                <strong>{{ __('Official Tickets Sent') }}</strong>
                                <small>{{ $booking->status === 'confirmed' ? __('Ready to fly') : __('Processing...') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@if($booking->status === 'pending' && $booking->ticketing_time_limit)
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timerBox = document.getElementById('pnrTimer');
        const display = document.getElementById('timerDisplay');
        const payButton = document.getElementById('payButton');
        const expiryDate = new Date(timerBox.dataset.expiry).getTime();

        const x = setInterval(function() {
            const now = new Date().getTime();
            const distance = expiryDate - now;

            if (distance < 0) {
                clearInterval(x);
                display.innerHTML = "00:00";
                display.classList.add('expired');
                if (payButton) {
                    payButton.classList.add('disabled');
                    payButton.style.opacity = '0.5';
                    payButton.style.pointerEvents = 'none';
                    payButton.innerHTML = '<i class="fas fa-times-circle"></i> {{ __("Expired") }}';
                }
                setTimeout(() => { window.location.reload(); }, 2000);
                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            display.innerHTML = (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);

            if (distance < 60000) { // Last minute
                display.style.color = '#ef4444';
            }
        }, 1000);
    });
</script>
@endpush
@endif
@endsection
