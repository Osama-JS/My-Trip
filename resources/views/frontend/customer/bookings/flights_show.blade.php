@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Flight Booking') . ' · #' . $booking->booking_reference)
@section('page-title', __('Flight Booking Details'))

@section('content')
<div class="flight-details-wrapper">

    {{-- ─── Top Status Banner ─── --}}
    @php
        $statusClass = match($booking->status) {
            'confirmed' => 'confirmed',
            'paid'      => 'paid',
            'cancelled' => 'cancelled',
            default     => 'pending'
        };
        $statusLabel = match($booking->status) {
            'confirmed' => __('Confirmed'),
            'paid'      => __('Paid & Processing'),
            'cancelled' => __('Cancelled'),
            default     => __('Awaiting Payment')
        };
        $statusIcon = match($booking->status) {
            'confirmed' => 'fa-check-circle',
            'paid'      => 'fa-receipt',
            'cancelled' => 'fa-times-circle',
            default     => 'fa-clock'
        };
    @endphp

    <div class="status-hero status-{{ $statusClass }}">
        <div class="status-hero-main">
            <div class="status-icon"><i class="fas {{ $statusIcon }}"></i></div>
            <div class="status-info">
                <h3>{{ $statusLabel }}</h3>
                <p>{{ __('Booking Reference') }}: <strong>#{{ $booking->booking_reference }}</strong></p>
            </div>
        </div>
        @if($booking->pnr_code)
        <div class="pnr-badge">
            <label>{{ __('Airline PNR') }}</label>
            <span>{{ $booking->pnr_code }}</span>
        </div>
        @endif
    </div>

    <div class="fd-grid">
        {{-- ─── LEFT COLUMN ─── --}}
        <div class="fd-main-content">
            
            {{-- Itinerary Card (Boarding Pass Style) --}}
            <div class="fd-card itinerary-card">
                <div class="fd-card-header">
                    <h5><i class="fas fa-plane-departure"></i> {{ __('Flight Itinerary') }}</h5>
                    <span class="airline-text">{{ $booking->airline_name ?? __('Direct Flight') }}</span>
                </div>
                <div class="boarding-pass">
                    <div class="bp-top">
                        <div class="airport">
                            <span class="city">{{ $booking->origin }}</span>
                            <span class="time">{{ $booking->departure_date }}</span>
                        </div>
                        <div class="flight-path">
                            <div class="path-line"></div>
                            <i class="fas fa-plane"></i>
                            <span class="duration">{{ __('Direct') }}</span>
                        </div>
                        <div class="airport text-end">
                            <span class="city">{{ $booking->destination }}</span>
                            <span class="time">{{ __('Scheduled Arrival') }}</span>
                        </div>
                    </div>
                    <div class="bp-divider">
                        <div class="notch left"></div>
                        <div class="line"></div>
                        <div class="notch right"></div>
                    </div>
                    <div class="bp-bottom">
                        <div class="bp-info">
                            <label><i class="fas fa-chair"></i> {{ __('Class') }}</label>
                            <span>{{ $booking->flightBooking->flight_class ?? __('Economy') }}</span>
                        </div>
                        <div class="bp-info">
                            <label><i class="fas fa-suitcase"></i> {{ __('Baggage') }}</label>
                            <span>{{ __('Included') }}</span>
                        </div>
                        <div class="bp-info text-end">
                            <label><i class="fas fa-ticket-alt"></i> {{ __('Ticket Status') }}</label>
                            <span class="badge-status">{{ ucfirst($booking->ticket_status ?? 'pending') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Passengers Section --}}
            <div class="fd-card">
                <div class="fd-card-header">
                    <h5><i class="fas fa-users"></i> {{ __('Travelers') }}</h5>
                    <span class="badge badge-primary">{{ $booking->passengers->count() }} {{ __('Total') }}</span>
                </div>
                <div class="passengers-list">
                    @foreach($booking->passengers as $pax)
                        <div class="passenger-item">
                            <div class="pax-avatar">
                                <i class="fas {{ $pax->passenger_type === 'child' ? 'fa-child' : 'fa-user' }}"></i>
                            </div>
                            <div class="pax-meta">
                                <span class="pax-name">{{ $pax->title }} {{ $pax->first_name }} {{ $pax->last_name }}</span>
                                <span class="pax-type">{{ __($pax->passenger_type ?? 'adult') }}</span>
                            </div>
                            <div class="pax-docs">
                                @if($pax->e_ticket_no)
                                    <div class="ticket-tag">
                                        <label>{{ __('Ticket No') }}</label>
                                        <strong>{{ $pax->e_ticket_no }}</strong>
                                    </div>
                                @endif
                                <div class="passport-tag">
                                    <label>{{ __('Passport') }}</label>
                                    <strong>{{ $pax->passport_no ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ─── RIGHT COLUMN ─── --}}
        <div class="fd-sidebar">
            
            {{-- Pricing Summary --}}
            <div class="fd-card total-card">
                <div class="total-label">{{ __('Total Price') }}</div>
                <div class="total-value">{{ number_format($booking->total_amount, 2) }} <span>{{ $booking->currency }}</span></div>
                
                <div class="price-breakdown">
                    <div class="row-info"><span>{{ __('Base Fare') }}</span><strong>{{ number_format($booking->total_amount, 2) }}</strong></div>
                    <div class="row-info"><span>{{ __('Taxes & Fees') }}</span><strong>0.00</strong></div>
                </div>

                <div class="fd-actions">
                    @if($booking->status === 'pending')
                        <a href="{{ route('payments.web.checkout', ['booking_id' => $booking->id, 'method' => 'mada', 'type' => 'flight']) }}" class="btn-fd btn-fd-primary">
                            <i class="fas fa-credit-card"></i> {{ __('Pay Now') }}
                        </a>
                    @elseif($booking->status === 'confirmed')
                        <a href="{{ route('customer.bookings.invoice', $booking->id) }}" class="btn-fd btn-fd-success">
                            <i class="fas fa-file-invoice-dollar"></i> {{ __('Download Invoice') }}
                        </a>
                    @endif
                    <a href="https://wa.me/" class="btn-fd btn-fd-outline">
                        <i class="fab fa-whatsapp"></i> {{ __('Need Assistance?') }}
                    </a>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="fd-card">
                <div class="fd-card-header">
                    <h5><i class="fas fa-history"></i> {{ __('Booking Timeline') }}</h5>
                </div>
                <div class="timeline">
                    <div class="timeline-step done">
                        <div class="step-dot"><i class="fas fa-check"></i></div>
                        <div class="step-content">
                            <strong>{{ __('Reservation Received') }}</strong>
                            <small>{{ $booking->created_at->format('d M Y, H:i') }}</small>
                        </div>
                    </div>
                    <div class="timeline-step {{ in_array($booking->status, ['paid', 'confirmed']) ? 'done' : '' }}">
                        <div class="step-dot"><i class="fas {{ in_array($booking->status, ['paid', 'confirmed']) ? 'fa-check' : 'fa-credit-card' }}"></i></div>
                        <div class="step-content">
                            <strong>{{ __('Payment Verification') }}</strong>
                            <small>{{ in_array($booking->status, ['paid', 'confirmed']) ? __('Completed') : __('Awaiting Payment') }}</small>
                        </div>
                    </div>
                    <div class="timeline-step {{ $booking->status === 'confirmed' ? 'done' : '' }}">
                        <div class="step-dot"><i class="fas {{ $booking->status === 'confirmed' ? 'fa-check' : 'fa-plane' }}"></i></div>
                        <div class="step-content">
                            <strong>{{ __('Official Confirmation') }}</strong>
                            <small>{{ $booking->status === 'confirmed' ? __('Ready to fly') : __('Processing...') }}</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    :root {
        --fd-primary: #1e293b;
        --fd-accent: #3b82f6;
        --fd-success: #10b981;
        --fd-warning: #f59e0b;
        --fd-danger: #ef4444;
        --fd-bg: #f8fafc;
        --fd-card-shadow: 0 10px 30px -5px rgba(0,0,0,0.04);
        --fd-radius: 20px;
    }

    .flight-details-wrapper {
        padding: 20px 0;
    }

    /* ─── Status Hero ─── */
    .status-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 30px;
        border-radius: var(--fd-radius);
        margin-bottom: 30px;
        border-left: 8px solid transparent;
        box-shadow: var(--fd-card-shadow);
        background: white;
    }
    .status-confirmed { border-color: var(--fd-success); }
    .status-paid      { border-color: var(--fd-accent); }
    .status-pending   { border-color: var(--fd-warning); }
    .status-cancelled { border-color: var(--fd-danger); }

    .status-hero-main { display: flex; align-items: center; gap: 20px; }
    .status-icon {
        width: 60px; height: 60px; border-radius: 15px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }
    .status-confirmed .status-icon { background: #ecfdf5; color: var(--fd-success); }
    .status-paid .status-icon      { background: #eff6ff; color: var(--fd-accent); }
    .status-pending .status-icon   { background: #fffbeb; color: var(--fd-warning); }
    .status-cancelled .status-icon { background: #fef2f2; color: var(--fd-danger); }

    .status-info h3 { margin: 0; font-weight: 900; color: var(--fd-primary); font-size: 1.4rem; }
    .status-info p { margin: 5px 0 0; color: #64748b; font-size: 0.95rem; }

    .pnr-badge { text-align: right; }
    .pnr-badge label { display: block; font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; }
    .pnr-badge span { font-size: 1.6rem; font-weight: 900; color: var(--fd-accent); letter-spacing: 1px; }

    /* ─── Layout Grid ─── */
    .fd-grid { display: grid; grid-template-columns: 1fr 380px; gap: 30px; }
    @media (max-width: 1100px) { .fd-grid { grid-template-columns: 1fr; } }

    /* ─── Cards ─── */
    .fd-card {
        background: white; border-radius: var(--fd-radius);
        box-shadow: var(--fd-card-shadow); margin-bottom: 25px;
        border: 1px solid #f1f5f9; overflow: hidden;
    }
    .fd-card-header {
        padding: 20px 25px; border-bottom: 1px solid #f8fafc;
        display: flex; justify-content: space-between; align-items: center;
    }
    .fd-card-header h5 { margin: 0; font-weight: 800; color: var(--fd-primary); display: flex; align-items: center; gap: 10px; font-size: 1rem; }
    .fd-card-header h5 i { color: var(--fd-accent); }
    .airline-text { font-size: 0.85rem; font-weight: 700; color: #94a3b8; }

    /* ─── Boarding Pass ─── */
    .boarding-pass { padding: 30px; background: linear-gradient(135deg, #ffffff, #f9fafb); }
    .bp-top { display: flex; justify-content: space-between; align-items: center; gap: 20px; }
    .airport .city { display: block; font-size: 2rem; font-weight: 900; color: var(--fd-primary); line-height: 1; }
    .airport .time { font-size: 0.9rem; color: #64748b; font-weight: 600; display: block; margin-top: 8px; }

    .flight-path { flex: 1; position: relative; text-align: center; }
    .path-line { position: absolute; top: 50%; left: 0; right: 0; border-top: 2px dashed #cbd5e1; z-index: 1; }
    .flight-path i { position: relative; z-index: 2; background: white; padding: 0 15px; color: var(--fd-accent); font-size: 1.4rem; }
    .duration { display: block; font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-top: 5px; }

    .bp-divider { position: relative; margin: 25px -30px; display: flex; align-items: center; height: 30px; }
    .bp-divider .line { flex: 1; border-top: 2px dashed #f1f5f9; }
    .bp-divider .notch { width: 30px; height: 30px; background: var(--fd-bg); border-radius: 50%; position: absolute; z-index: 5; }
    .bp-divider .notch.left { left: -15px; }
    .bp-divider .notch.right { right: -15px; }

    .bp-bottom { display: flex; justify-content: space-between; align-items: center; }
    .bp-info label { display: block; font-size: 0.72rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
    .bp-info span { font-weight: 800; color: var(--fd-primary); font-size: 0.95rem; }
    .badge-status { padding: 4px 12px; background: var(--fd-accent); color: white; border-radius: 100px; font-size: 0.75rem; }

    /* ─── Passengers ─── */
    .passengers-list { padding: 15px 25px; }
    .passenger-item {
        display: flex; align-items: center; gap: 15px; padding: 15px 0;
        border-bottom: 1px solid #f8fafc;
    }
    .passenger-item:last-child { border-bottom: none; }
    .pax-avatar {
        width: 45px; height: 45px; border-radius: 12px; background: #f1f5f9;
        display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 1.1rem;
    }
    .pax-meta { flex: 1; }
    .pax-name { display: block; font-weight: 800; color: var(--fd-primary); font-size: 0.95rem; }
    .pax-type { font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; }

    .pax-docs { display: flex; gap: 20px; }
    .pax-docs label { display: block; font-size: 0.68rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; }
    .pax-docs strong { font-size: 0.88rem; font-weight: 800; color: var(--fd-primary); }

    /* ─── Sidebar ─── */
    .total-card { background: var(--fd-primary); color: white; border: none; padding: 30px; }
    .total-label { font-size: 0.8rem; color: rgba(255,255,255,0.6); font-weight: 700; text-transform: uppercase; margin-bottom: 5px; }
    .total-value { font-size: 2.5rem; font-weight: 900; line-height: 1; margin-bottom: 20px; }
    .total-value span { font-size: 1rem; opacity: 0.7; font-weight: 600; margin-left: 5px; }

    .price-breakdown { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; margin-bottom: 25px; }
    .row-info { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.88rem; }
    .row-info span { color: rgba(255,255,255,0.6); }

    .fd-actions { display: flex; flex-direction: column; gap: 10px; }
    .btn-fd {
        display: flex; align-items: center; justify-content: center; gap: 10px;
        padding: 14px; border-radius: 15px; font-weight: 800; font-size: 0.9rem;
        cursor: pointer; transition: all .3s; text-decoration: none; border: none;
    }
    .btn-fd-primary { background: white; color: var(--fd-primary); }
    .btn-fd-primary:hover { background: #f1f5f9; transform: translateY(-3px); }
    .btn-fd-success { background: var(--fd-success); color: white; }
    .btn-fd-success:hover { background: #059669; transform: translateY(-3px); }
    .btn-fd-outline { background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); }
    .btn-fd-outline:hover { background: rgba(255,255,255,0.2); }

    /* ─── Timeline ─── */
    .timeline { padding: 25px; }
    .timeline-step { display: flex; gap: 15px; position: relative; padding-bottom: 25px; }
    .timeline-step:last-child { padding-bottom: 0; }
    .timeline-step::before {
        content: ''; position: absolute; left: 17px; top: 30px; width: 2px;
        height: calc(100% - 25px); background: #f1f5f9;
        z-index: 1;
    }
    .timeline-step:last-child::before { display: none; }
    .timeline-step.done::before { background: var(--fd-success); }

    .step-dot {
        width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; color: #94a3b8;
        display: flex; align-items: center; justify-content: center; font-size: 0.8rem;
        position: relative; z-index: 2; border: 4px solid white;
    }
    .timeline-step.done .step-dot { background: var(--fd-success); color: white; }
    .step-content strong { display: block; font-size: 0.9rem; color: var(--fd-primary); font-weight: 800; margin-bottom: 2px; }
    .step-content small { color: #94a3b8; font-size: 0.75rem; font-weight: 600; }

    /* Arabic RTL Adjustments */
    html[dir="rtl"] .status-hero { border-left: none; border-right: 8px solid transparent; }
    html[dir="rtl"] .status-hero.status-confirmed { border-right-color: var(--fd-success); }
    html[dir="rtl"] .status-hero.status-paid      { border-right-color: var(--fd-accent); }
    html[dir="rtl"] .status-hero.status-pending   { border-right-color: var(--fd-warning); }
    html[dir="rtl"] .status-hero.status-cancelled { border-right-color: var(--fd-danger); }
    html[dir="rtl"] .pnr-badge { text-align: left; }
    html[dir="rtl"] .timeline-step::before { left: auto; right: 17px; }
    html[dir="rtl"] .step-dot { border-width: 4px; }
</style>
@endpush
