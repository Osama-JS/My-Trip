@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Booking Details') . ' #' . $booking->id)
@section('page-title', __('Booking Details') . ' #' . $booking->id)

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
.booking-details-container {
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

/* Boarding pass ticket cutouts */
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

/* ─── Grid Layout ─── */
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
    gap: 10px;
    background: rgba(0, 0, 0, 0.005);
}
.detail-card-header i {
    color: var(--primary-blue);
    font-size: 1.15rem;
}
.detail-card-body {
    padding: 24px;
}

/* ─── Timeline ─── */
.timeline-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--detail-border-radius);
    padding: 24px;
    box-shadow: var(--ticket-shadow);
    margin-bottom: 24px;
}
.timeline {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    padding: 10px 0;
}
.timeline-step {
    flex: 1;
    text-align: center;
    position: relative;
    z-index: 2;
}
.timeline-step::after {
    content: '';
    position: absolute;
    top: 20px;
    left: 50%;
    width: 100%;
    height: 3px;
    background: var(--border-color);
    z-index: -1;
    transition: background-color 0.3s;
}
[dir="rtl"] .timeline-step::after {
    left: auto;
    right: 50%;
}
.timeline-step:last-child::after {
    display: none;
}
.timeline-step.done::after {
    background: var(--primary-blue);
}

.step-dot {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: 2px solid var(--border-color);
    background: var(--bg-card);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: .95rem;
    color: var(--text-muted);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 6px rgba(0,0,0,0.01);
}
.timeline-step.done .step-dot {
    border-color: var(--primary-blue);
    background: var(--primary-blue);
    color: #fff;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
}
.timeline-step.active .step-dot {
    border-color: var(--primary-blue);
    background: var(--primary-blue);
    color: #fff;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
}

.step-label {
    font-size: .78rem;
    color: var(--text-muted);
    font-weight: 700;
    transition: color 0.3s;
}
.timeline-step.done .step-label,
.timeline-step.active .step-label {
    color: var(--text-main);
}

/* Widescreen Hero image inside details card */
.trip-hero {
    height: 240px;
    background: var(--border-color);
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 24px;
    position: relative;
    box-shadow: var(--ticket-shadow);
}
.trip-hero img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.trip-hero-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3.5rem;
    color: var(--text-muted);
    background: linear-gradient(135deg, var(--bg-main), var(--border-color));
}

/* Info Rows */
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

/* Callout Box */
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
.callout-warning {
    background: rgba(249, 115, 22, 0.06);
    color: #b45309;
    border-color: rgba(249, 115, 22, 0.15);
}
.callout-danger {
    background: rgba(239, 68, 68, 0.06);
    color: #b91c1c;
    border-color: rgba(239, 68, 68, 0.15);
}
.callout-alert i {
    font-size: 1.25rem;
    margin-top: 2px;
}

/* ─── Passenger List ─── */
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
.passenger-seat-tag {
    background: rgba(37, 99, 235, 0.08);
    color: var(--primary-blue);
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.bullet-sep {
    color: var(--border-color);
    margin: 0 4px;
}

/* ─── Payment Details ─── */
.payment-method-badge {
    padding: 4px 12px;
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 800;
    color: var(--text-main);
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.receipt-thumb {
    width: 64px;
    height: 64px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid var(--border-color);
    cursor: pointer;
    transition: all 0.25s ease;
}
.receipt-thumb:hover {
    transform: scale(1.08);
    box-shadow: 0 6px 12px rgba(0,0,0,0.06);
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
.summary-total .amount-label {
    font-size: .85rem;
    color: #94a3b8;
    margin-top: 6px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ─── Action Buttons ─── */
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
    margin-bottom: 12px;
    transition: all 0.25s ease;
}
.action-btn:last-child {
    margin-bottom: 0;
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
.action-btn-danger {
    background: rgba(239, 68, 68, 0.06);
    color: #ef4444;
    border-color: rgba(239, 68, 68, 0.15);
}
.action-btn-danger:hover {
    background: #ef4444;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(239, 68, 68, 0.15);
}

/* Status Badges */
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
</style>
@endpush

@section('content')

<div class="booking-details-container">
    {{-- Back Link --}}
    <a href="{{ route('customer.bookings.trips') }}" class="back-link">
        <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'right' : 'left' }}"></i>
        {{ __('Back to Bookings') }}
    </a>

    {{-- Fetch Data --}}
    @php
        $trip = $booking->trip;
        $latestTransfer = $booking->bankTransfers()->latest()->first();
        $currentState = $booking->booking_state ?? 'received';
        $isCancelled = $booking->status === 'cancelled' || $currentState === 'cancelled';

        $states = [
            'received' => ['icon' => 'fa-inbox', 'label' => __('Order Received')],
            'preparing' => ['icon' => 'fa-cogs', 'label' => __('Preparing Tickets')],
            'confirmed' => ['icon' => 'fa-check-circle', 'label' => __('Confirmed')],
            'tickets_sent' => ['icon' => 'fa-ticket-alt', 'label' => __('Tickets Sent')]
        ];

        $stateKeys = array_keys($states);
        $currentIndex = array_search($currentState, $stateKeys);
        if ($currentIndex === false) $currentIndex = -1;

        // Airport abbreviation logic
        $fromCode = strtoupper(substr($trip->fromCity?->title_en ?: 'RUH', 0, 3));
        $toCode = strtoupper(substr($trip->toCity?->title_en ?: 'IST', 0, 3));
    @endphp

    {{-- Alerts --}}
    @if(request()->has('bank_transfer_submitted'))
        <div class="callout-alert callout-success">
            <i class="fas fa-check-circle"></i>
            <div>
                <strong>{{ __('Success!') }}</strong> {{ __('Your bank transfer receipt has been submitted and is under review. You will be notified once approved.') }}
            </div>
        </div>
    @endif

    @if($latestTransfer)
        @if($latestTransfer->status === 'pending')
            <div class="callout-alert callout-warning">
                <i class="fas fa-clock"></i>
                <div>
                    <strong>{{ __('Transfer Under Review') }}</strong>: {{ __('Your bank transfer submitted on :date is currently being reviewed by our team.', ['date' => $latestTransfer->created_at->format('d/m/Y H:i')]) }}
                </div>
            </div>
        @elseif($latestTransfer->status === 'rejected')
            <div class="callout-alert callout-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>{{ __('Transfer Rejected') }}</strong><br>
                    {{ __('Your previous bank transfer was rejected for the following reason:') }}
                    <div style="margin-top:6px; padding:10px; background:var(--bg-main); border: 1px solid var(--border-color); border-radius:8px; font-size:0.88rem; font-weight: 500;">
                        {{ $latestTransfer->rejection_reason ?? __('No reason provided.') }}
                    </div>
                    <div style="margin-top:10px; font-size: 0.85rem;">
                        {{ __('Please try paying again with a valid receipt or another payment method.') }}
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- ─── Boarding Pass Ticket Header ─── --}}
    <div class="boarding-pass">
        <div class="pass-header">
            <span class="pass-title">{{ __('BOARDING PASS / TICKET') }}</span>
            <span class="pass-pnr">{{ __('Booking ID') }}: #{{ $booking->id }}</span>
        </div>
        <div class="pass-body">
            <div class="pass-airport-row">
                <div class="pass-airport-code">
                    <h3>{{ $fromCode }}</h3>
                    <span>{{ $trip->fromCity?->name ?? '—' }}</span>
                </div>
                <div class="pass-path-line">
                    <i class="fas fa-plane"></i>
                </div>
                <div class="pass-airport-code dest-code">
                    <h3>{{ $toCode }}</h3>
                    <span>{{ $trip->toCity?->name ?? '—' }}</span>
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
                        <span class="pass-label">{{ __('DURATION') }}</span>
                        <span class="pass-val">{{ $trip->duration ?? '—' }} {{ __('Days') }}</span>
                    </div>
                    <div>
                        <span class="pass-label">{{ __('CLASS / TIER') }}</span>
                        <span class="pass-val">{{ $booking->package?->title ?: __('Standard') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Timeline Card --}}
    <div class="timeline-card">
        <div class="timeline">
            @if($isCancelled)
                <div class="timeline-step done active" style="flex: 1;">
                    <div class="step-dot" style="border-color:#ef4444; background:#ef4444; color:#fff;">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="step-label" style="color:#ef4444;">{{ __('Cancelled') }}</div>
                </div>
            @else
                @foreach($states as $key => $data)
                    @php
                        $stepIndex = array_search($key, $stateKeys);
                        $isDone = $stepIndex <= $currentIndex;
                        $isActive = $stepIndex === $currentIndex;
                    @endphp
                    <div class="timeline-step {{ $isDone ? 'done' : '' }} {{ $isActive ? 'active' : '' }}">
                        <div class="step-dot"><i class="fas {{ $data['icon'] }}"></i></div>
                        <div class="step-label">{{ $data['label'] }}</div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="booking-detail-grid">

        {{-- LEFT COLUMN --}}
        <div>
            {{-- Trip Info --}}
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fas fa-map-marked-alt"></i> {{ __('Trip Information') }}
                </div>
                <div class="detail-card-body">
                    @php $img = $trip?->images?->first(); @endphp
                    <div class="trip-hero">
                        @if($img)
                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="">
                        @else
                            <div class="trip-hero-placeholder"><i class="fas fa-map-marked-alt"></i></div>
                        @endif
                    </div>
                    <h3 style="font-size:1.25rem; font-weight:850; margin:0 0 16px; color: var(--text-main);">{{ $trip?->title ?? __('Trip') }}</h3>

                    <div class="info-row">
                        <span class="info-label">{{ __('Destination') }}</span>
                        <span class="info-value">{{ $trip?->toCountry?->name ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('Booking No') }}</span>
                        <span class="info-value">#{{ $booking->id }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('Booking Date') }}</span>
                        <span class="info-value">{{ $booking->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('Passengers Count') }}</span>
                        <span class="info-value">{{ $booking->tickets_count }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('Booking State') }}</span>
                        <span class="info-value">
                            <span class="status-badge-wrapper status-{{ $booking->status }}">
                                <span class="pulse-dot"></span>
                                @if($isCancelled)
                                    {{ __('Cancelled') }}
                                @else
                                    {{ $states[$currentState]['label'] ?? ucfirst($currentState) }}
                                @endif
                            </span>
                        </span>
                    </div>
                    @if($booking->status === 'cancelled' && $booking->cancellation_reason)
                        <div class="info-row" style="background-color: rgba(239, 68, 68, 0.05); border-radius: 12px; padding: 14px; margin-top: 14px; border: 1px solid rgba(239, 68, 68, 0.15); flex-direction: column; align-items: flex-start; gap: 6px;">
                            <span class="info-label" style="color: #ef4444; font-weight: 800;"><i class="fas fa-exclamation-circle"></i> {{ __('Cancellation Reason') }}:</span>
                            <span class="info-value" style="color: #ef4444; font-size: 0.88rem; text-align: start;">{{ $booking->cancellation_reason }}</span>
                        </div>
                    @endif
                    @if($booking->notes)
                        <div class="info-row" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                            <span class="info-label">{{ __('Notes') }}</span>
                            <span class="info-value" style="text-align: start;">{{ $booking->notes }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Passengers --}}
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fas fa-users"></i> {{ __('Passengers') }} ({{ $booking->passengers->count() }})
                </div>
                <div class="detail-card-body">
                    @foreach($booking->passengers as $i => $p)
                        @php
                            // Seating tags like 01A, 01B, 02A for visual realism
                            $seat = sprintf("%02d", floor($i / 2) + 1) . ($i % 2 === 0 ? 'A' : 'B');
                        @endphp
                        <div class="passenger-item">
                            <div class="passenger-main">
                                <div class="passenger-avatar">{{ $i + 1 }}</div>
                                <div class="passenger-info">
                                    <div class="p-name">{{ $p->name }}</div>
                                    <div class="p-meta">
                                        @if($p->nationality) {{ $p->nationality }} @endif
                                        @if($p->passport_number) <span class="bullet-sep">·</span> {{ __('Passport') }}: {{ $p->passport_number }} @endif
                                        @if($p->phone) <span class="bullet-sep">·</span> {{ $p->phone }} @endif
                                    </div>
                                </div>
                            </div>
                            <span class="passenger-seat-tag">{{ __('SEAT') }} {{ $seat }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Payment Details --}}
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fas fa-credit-card"></i> {{ __('Payment Details') }}
                </div>
                <div class="detail-card-body">
                    @php
                        $mainPayment = $booking->payments->where('status', 'paid')->first() ?? $booking->payments->first();
                    @endphp

                    @if($mainPayment)
                        <div class="info-row">
                            <span class="info-label">{{ __('Payment Method') }}</span>
                            <span class="info-value">
                                <span class="payment-method-badge">
                                    <i class="fas fa-{{ $mainPayment->payment_gateway === 'bank_transfer' ? 'university' : 'wallet' }}"></i>
                                    {{ str_replace('_', ' ', strtoupper($mainPayment->payment_gateway)) }}
                                </span>
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">{{ __('Transaction No') }}</span>
                            <span class="info-value" style="font-family: monospace; font-size: 0.85rem;">{{ $mainPayment->transaction_id ?? '—' }}</span>
                        </div>

                        {{-- Bank Details --}}
                        @if($mainPayment->payment_gateway === 'bank_transfer' && $latestTransfer)
                            <div class="info-row">
                                <span class="info-label">{{ __('Sender Name') }}</span>
                                <span class="info-value">{{ $latestTransfer->sender_name }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">{{ __('Receipt Number') }}</span>
                                <span class="info-value">{{ $latestTransfer->receipt_number ?? '—' }}</span>
                            </div>

                            @if($latestTransfer->bankAccount)
                                <div class="info-row" style="align-items: flex-start;">
                                    <span class="info-label" style="margin-top: 2px;">{{ __('Transferred To') }}</span>
                                    <div class="info-value">
                                        <div style="font-weight: 700; color: var(--text-main);">{{ $latestTransfer->bankAccount->bank_name }}</div>
                                        <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $latestTransfer->bankAccount->beneficiary_name }}</div>
                                        <div style="font-size: 0.78rem; color: var(--text-muted); font-family: monospace;">{{ $latestTransfer->bankAccount->iban }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($latestTransfer->receipt_image)
                                <div class="info-row" style="align-items: center;">
                                    <span class="info-label">{{ __('Transfer Receipt') }}</span>
                                    <div class="info-value">
                                        <a href="{{ asset('storage/' . $latestTransfer->receipt_image) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $latestTransfer->receipt_image) }}" class="receipt-thumb" alt="Receipt">
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Receipt summary box --}}
                        <div style="margin-top: 20px; padding-top: 16px; border-top: 2px dashed var(--border-color);">
                            <div class="info-row">
                                <span class="info-label" style="font-weight: 700; color: var(--text-main);">{{ __('Paid Amount') }}</span>
                                <span class="info-value" style="font-size: 1.15rem; color: #10b981; font-weight: 900;">
                                    {{ number_format($mainPayment->amount, 2) }} {{ __('SAR') }}
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">{{ __('Payment Status') }}</span>
                                <span class="info-value">
                                    @php
                                        $statusLower = strtolower($mainPayment->status);
                                        $badgeClass = ($statusLower === 'success' || $statusLower === 'paid') ? 'bg-success' : (($statusLower === 'pending' || $statusLower === 'processing') ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <span class="badge {{ $badgeClass }}" style="padding: 6px 12px; border-radius: 12px; font-weight: 700;">
                                        {{ strtoupper(__($mainPayment->status)) }}
                                    </span>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">{{ __('Date & Time') }}</span>
                                <span class="info-value text-muted" style="font-size: 0.8rem;">
                                    {{ $mainPayment->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>

                    @else
                        <div class="text-center p-4">
                            <i class="fas fa-exclamation-circle fa-2x text-warning mb-2 opacity-50"></i>
                            <p class="text-muted mb-0">{{ __('No payment has been registered for this booking yet.') }}</p>
                            @if($booking->status === 'pending')
                                <a href="{{ route('customer.payments.checkout', $booking->id) }}" class="btn btn-primary btn-sm mt-3 px-4" style="border-radius: 8px; font-weight:700;">
                                    {{ __('Pay Now') }}
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN (Sidebar) --}}
        <div>
            {{-- Price Summary --}}
            <div class="summary-total">
                <div class="amount">{{ number_format($booking->total_price, 0) }} <small style="font-size:1.1rem; font-weight:700;">{{ __('SAR') }}</small></div>
                <div class="amount-label">{{ __('Total Price') }}</div>
            </div>

            {{-- Actions --}}
            @if($booking->status === 'pending')
                @if(!$latestTransfer || $latestTransfer->status === 'rejected')
                    <a href="{{ route('customer.payments.checkout', $booking->id) }}" class="action-btn action-btn-primary">
                        <i class="fas fa-credit-card"></i> {{ __('Complete Payment Now') }}
                    </a>
                @else
                    <button class="action-btn" style="background:var(--border-color); color:var(--text-muted); cursor:not-allowed;" disabled>
                        <i class="fas fa-clock"></i> {{ __('Payment Under Review') }}
                    </button>
                    @if($latestTransfer->receipt_image)
                        <a href="{{ asset('storage/' . $latestTransfer->receipt_image) }}" target="_blank" class="action-btn action-btn-outline" style="border-color: #7dd3fc; color: #0284c7; background: rgba(224, 242, 254, 0.2);">
                            <i class="fas fa-file-invoice"></i> {{ __('View Receipt') }}
                        </a>
                    @endif
                @endif

                <form method="POST" action="{{ route('customer.bookings.cancel', $booking->id) }}"
                      onsubmit="return confirm('{{ __('Are you sure you want to cancel this booking?') }}')">
                    @csrf
                    <button class="action-btn action-btn-danger" type="submit">
                        <i class="fas fa-times"></i> {{ __('Cancel Booking') }}
                    </button>
                </form>
            @elseif($booking->status === 'confirmed')
                @if($booking->ticket_url)
                    <a href="{{ $booking->ticket_url }}" target="_blank" class="action-btn" style="background: #10b981; color: #fff; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.15);">
                        <i class="fas fa-ticket-alt"></i> {{ __('Download Tickets') }}
                    </a>
                @endif
                @if($latestTransfer && $latestTransfer->status === 'approved' && $latestTransfer->receipt_image)
                    <a href="{{ asset('storage/' . $latestTransfer->receipt_image) }}" target="_blank" class="action-btn action-btn-outline" style="border-color: #7dd3fc; color: #0284c7; background: rgba(224, 242, 254, 0.2);">
                        <i class="fas fa-file-invoice"></i> {{ __('View Receipt') }}
                    </a>
                @endif
                <a href="{{ $booking->ticket_url ? '#' : route('customer.bookings.invoice', $booking->id) }}" class="action-btn action-btn-outline" {!! $booking->ticket_url ? 'style="display:none;"' : '' !!}>
                    <i class="fas fa-file-pdf"></i> {{ __('Download Invoice') }}
                </a>
            @endif

            <a href="{{ route('customer.bookings.index') }}" class="action-btn action-btn-outline">
                <i class="fas fa-list"></i> {{ __('All Bookings') }}
            </a>
        </div>

    </div>
</div>

@endsection
