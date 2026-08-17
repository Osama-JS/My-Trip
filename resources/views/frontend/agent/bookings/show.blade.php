@extends('frontend.agent.layouts.agent-layout')

@section('title', __('Booking Details') . ' #' . $booking->id . ' - ' . ($booking->trip->title ?? ''))
@section('page-title', __('Booking Details'))

@push('styles')
<style>
/* ══════════════════════════════════════════════
   AGENT BOOKING DETAILS — MODERN DESIGN SYSTEM
   ══════════════════════════════════════════════ */

.abk-container { width: 100%; max-width: 100%; margin: 0; }

/* ─── Hero Banner ─── */
.abk-hero {
    background: linear-gradient(135deg, var(--accent), #4f46e5, #7c3aed);
    border-radius: var(--radius-2xl);
    padding: 32px 38px;
    margin-bottom: 26px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 36px var(--accent-glow);
}
.abk-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 420px;
    height: 420px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
    pointer-events: none;
}
.abk-hero-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    position: relative;
    z-index: 2;
}
.abk-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.85);
    margin-bottom: 6px;
}
.abk-breadcrumb a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-weight: 600;
    transition: color var(--transition-fast);
}
.abk-breadcrumb a:hover { color: #fff; }
.abk-hero-title {
    font-size: 1.65rem;
    font-weight: 900;
    margin: 0 0 8px;
    color: #fff;
}
.abk-hero-badges {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.abk-route-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.95);
    background: rgba(255, 255, 255, 0.16);
    padding: 5px 14px;
    border-radius: 20px;
    backdrop-filter: blur(6px);
}
.abk-state-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 16px;
    border-radius: 30px;
    font-size: 0.82rem;
    font-weight: 800;
    backdrop-filter: blur(8px);
}
.state-awaiting_payment { background: rgba(245, 158, 11, 0.25); color: #fef08a; border: 1px solid rgba(245, 158, 11, 0.4); }
.state-preparing        { background: rgba(59, 130, 246, 0.25); color: #bfdbfe; border: 1px solid rgba(59, 130, 246, 0.4); }
.state-confirmed        { background: rgba(16, 185, 129, 0.25); color: #a7f3d0; border: 1px solid rgba(16, 185, 129, 0.4); }
.state-tickets_sent     { background: rgba(147, 51, 234, 0.25); color: #e9d5ff; border: 1px solid rgba(147, 51, 234, 0.4); }
.state-completed        { background: rgba(16, 185, 129, 0.25); color: #a7f3d0; border: 1px solid rgba(16, 185, 129, 0.4); }
.state-cancelled        { background: rgba(239, 68, 68, 0.25); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.4); }

.abk-hero-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.abk-action-btn {
    padding: 9px 18px;
    border-radius: var(--radius-md);
    font-size: 0.88rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all var(--transition-fast);
    border: none;
    cursor: pointer;
    line-height: 1;
}
.abk-btn-view-trip {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.35);
    backdrop-filter: blur(8px);
}
.abk-btn-view-trip:hover {
    background: rgba(255, 255, 255, 0.3);
    color: #fff;
    transform: translateY(-2px);
}

/* ─── Grid Layout ─── */
.abk-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    align-items: start;
}

/* ─── Section Cards ─── */
.abk-card {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    border: 1.5px solid var(--border-soft);
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
    overflow: hidden;
    transition: box-shadow var(--transition-fast), border-color var(--transition-fast);
}
.abk-card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--border);
}
.abk-card-header {
    padding: 18px 24px;
    background: var(--bg-card);
    border-bottom: 1.5px solid var(--border-soft);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.abk-card-header .hdr-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.abk-card-header .hdr-icon {
    width: 38px;
    height: 38px;
    background: var(--accent-soft);
    color: var(--accent);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.abk-card-header h5 {
    margin: 0;
    font-weight: 800;
    color: var(--text-primary);
    font-size: 1rem;
}
.abk-card-body { padding: 24px; }

/* ─── Trip Details Summary Strip ─── */
.abk-trip-strip {
    display: flex;
    gap: 20px;
    align-items: center;
    padding-bottom: 20px;
    border-bottom: 1.5px solid var(--border-soft);
    margin-bottom: 22px;
}
.abk-trip-thumb {
    width: 90px;
    height: 90px;
    border-radius: var(--radius-lg);
    object-fit: cover;
    border: 1.5px solid var(--border);
    flex-shrink: 0;
}
.abk-trip-title {
    font-size: 1.15rem;
    font-weight: 900;
    color: var(--text-primary);
    margin: 0 0 6px;
}

/* ─── Spec Grid ─── */
.abk-spec-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
}
.abk-spec-item {
    background: var(--bg-body);
    border: 1px solid var(--border-soft);
    border-radius: var(--radius-lg);
    padding: 14px 16px;
}
.abk-spec-label {
    font-size: 0.75rem;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.abk-spec-val {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--text-primary);
}

/* ─── Package Badges ─── */
.abk-tier-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
}
.tier-vip { background: rgba(245, 158, 11, 0.15); color: #d97706; }
.tier-gold { background: rgba(99, 102, 241, 0.15); color: #4f46e5; }
.tier-silver { background: rgba(148, 163, 184, 0.2); color: #475569; }
.tier-economy { background: var(--bg-body); color: var(--text-secondary); border: 1px solid var(--border); }

/* ─── Passenger List ─── */
.abk-passenger-card {
    background: var(--bg-body);
    border: 1.5px solid var(--border-soft);
    border-radius: var(--radius-lg);
    padding: 16px 20px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    transition: all var(--transition-fast);
}
.abk-passenger-card:hover {
    border-color: var(--border);
    background: var(--bg-card);
    box-shadow: var(--shadow-sm);
}
.abk-pax-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: var(--accent-soft);
    color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    font-weight: 900;
    flex-shrink: 0;
}
.abk-pax-name {
    font-weight: 800;
    font-size: 0.95rem;
    color: var(--text-primary);
    margin: 0 0 2px;
}
.abk-pax-meta {
    font-size: 0.82rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.abk-pax-passport-pill {
    background: var(--bg-card);
    border: 1px solid var(--border);
    padding: 4px 10px;
    border-radius: var(--radius-sm);
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--text-secondary);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* ─── Financial Summary ─── */
.abk-finance-box {
    background: var(--bg-body);
    border: 1.5px solid var(--border-soft);
    border-radius: var(--radius-xl);
    padding: 20px;
}
.abk-finance-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid var(--border-soft);
    font-size: 0.88rem;
}
.abk-finance-row:last-child { border-bottom: none; }
.abk-finance-total {
    padding-top: 14px;
    margin-top: 4px;
    border-top: 2px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.abk-finance-total .lbl { font-size: 0.95rem; font-weight: 800; color: var(--text-primary); }
.abk-finance-total .val { font-size: 1.45rem; font-weight: 900; color: var(--accent); }

/* ─── Ticket Upload Area ─── */
.abk-ticket-dropzone {
    border: 2px dashed var(--accent);
    background: var(--bg-body);
    border-radius: var(--radius-lg);
    padding: 24px 16px;
    text-align: center;
    position: relative;
    cursor: pointer;
    transition: all var(--transition-fast);
}
.abk-ticket-dropzone:hover {
    background: var(--accent-soft);
}
.abk-ticket-dropzone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
}
.abk-btn-upload {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: var(--radius-md);
    background: var(--accent);
    color: #fff;
    font-weight: 800;
    font-size: 0.9rem;
    cursor: pointer;
    box-shadow: 0 4px 14px var(--accent-glow);
    transition: all var(--transition-fast);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 14px;
}
.abk-btn-upload:hover {
    background: var(--accent-hover);
    transform: translateY(-2px);
}

/* ─── Responsive ─── */
@media (max-width: 992px) {
    .abk-grid { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .abk-hero { padding: 24px 20px; }
    .abk-hero-top { flex-direction: column; }
    .abk-trip-strip { flex-direction: column; text-align: center; }
    .abk-passenger-card { flex-direction: column; align-items: flex-start; }
}
</style>
@endpush

@section('content')
<div class="abk-container">

    {{-- ── Hero Banner ── --}}
    <div class="abk-hero">
        <div class="abk-hero-top">
            <div>
                <div class="abk-breadcrumb">
                    <a href="{{ route('agent.bookings.index') }}"><i class="fas fa-ticket-alt" style="margin-inline-end:5px;"></i>{{ __('My Bookings') }}</a>
                    <span>›</span>
                    <span style="color:#fff;">{{ __('Booking') }} #{{ $booking->id }}</span>
                </div>
                <h1 class="abk-hero-title">{{ $booking->trip->title ?? __('Trip Booking') }}</h1>
                <div class="abk-hero-badges">
                    <div class="abk-route-badge">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $booking->trip->fromCity?->name ?? '-' }}
                        <i class="fas fa-arrow-right" style="font-size:0.7rem; margin:0 4px;"></i>
                        {{ $booking->trip->toCity?->name ?? '-' }}
                    </div>
                    @php $st = $booking->booking_state ?? 'awaiting_payment'; @endphp
                    <span class="abk-state-pill state-{{ $st }}">
                        <i class="fas fa-circle" style="font-size:0.5rem;"></i>
                        {{ __(ucfirst(str_replace('_', ' ', $st))) }}
                    </span>
                    <div class="abk-route-badge">
                        <i class="fas fa-users"></i> {{ $booking->tickets_count ?? 1 }} {{ __('Passengers') }}
                    </div>
                </div>
            </div>
            <div class="abk-hero-actions">
                @if($booking->trip)
                    <a href="{{ route('agent.trips.show', $booking->trip_id) }}" class="abk-action-btn abk-btn-view-trip">
                        <i class="fas fa-eye"></i> {{ __('View Trip Page') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Main Two-Column Layout ── --}}
    <div class="abk-grid">

        {{-- ── Left Column: Specs & Passenger Details ── --}}
        <div>
            {{-- 1. Reservation & Package Specs --}}
            <div class="abk-card">
                <div class="abk-card-header">
                    <div class="hdr-left">
                        <span class="hdr-icon"><i class="fas fa-concierge-bell"></i></span>
                        <h5>{{ __('Package & Reservation Specifications') }}</h5>
                    </div>
                    <span style="font-size:0.8rem; font-weight:700; color:var(--text-muted);">ID #{{ $booking->id }}</span>
                </div>
                <div class="abk-card-body">
                    {{-- Trip Preview --}}
                    <div class="abk-trip-strip">
                        @php $thumb = $booking->trip->images->first(); @endphp
                        @if($thumb)
                            <img src="{{ Str::startsWith($thumb->image_path, ['http://', 'https://']) ? $thumb->image_path : asset('storage/' . $thumb->image_path) }}" class="abk-trip-thumb" alt="Trip">
                        @else
                            <div class="abk-trip-thumb" style="background:var(--bg-body); display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:1.8rem;">
                                <i class="fas fa-suitcase-rolling"></i>
                            </div>
                        @endif
                        <div style="flex:1;">
                            <h4 class="abk-trip-title">{{ $booking->trip->title ?? '-' }}</h4>
                            <div style="font-size:0.85rem; color:var(--text-muted); display:flex; gap:12px; flex-wrap:wrap;">
                                <span><i class="fas fa-calendar-check" style="color:var(--accent); margin-inline-end:4px;"></i> {{ __('Travel Date') }}: <strong>{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') : '-' }}</strong></span>
                                <span><i class="fas fa-clock" style="color:var(--accent); margin-inline-end:4px;"></i> {{ __('Booked On') }}: {{ $booking->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Specs Grid --}}
                    <div class="abk-spec-grid">
                        <div class="abk-spec-item">
                            <div class="abk-spec-label"><i class="fas fa-layer-group"></i> {{ __('Selected Package') }}</div>
                            <div class="abk-spec-val">
                                @if($booking->package)
                                    <span class="abk-tier-badge tier-{{ strtolower($booking->package->tier) }}">
                                        {{ strtoupper($booking->package->tier) }}
                                    </span>
                                    <span style="margin-inline-start:6px;">{{ $booking->package->name }}</span>
                                @else
                                    <span style="color:var(--text-muted);">{{ __('Standard Base Package') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="abk-spec-item">
                            <div class="abk-spec-label"><i class="fas fa-calendar-alt"></i> {{ __('Travel Season') }}</div>
                            <div class="abk-spec-val">
                                {{ $booking->season->name ?? __('Regular Period') }}
                            </div>
                        </div>

                        <div class="abk-spec-item">
                            <div class="abk-spec-label"><i class="fas fa-bed"></i> {{ __('Room Occupancy') }}</div>
                            <div class="abk-spec-val">
                                {{ __(ucfirst($booking->occupancy ?? 'Standard')) }}
                            </div>
                        </div>

                        <div class="abk-spec-item">
                            <div class="abk-spec-label"><i class="fas fa-users"></i> {{ __('Tickets / Travelers') }}</div>
                            <div class="abk-spec-val">
                                {{ $booking->tickets_count ?? 1 }} {{ __('Persons') }}
                            </div>
                        </div>
                    </div>

                    {{-- Hotel Details if available --}}
                    @if($booking->package && $booking->package->hotel_name)
                        <div style="margin-top:18px; padding:14px 18px; background:var(--bg-body); border-radius:var(--radius-md); border:1px solid var(--border-soft); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                            <div>
                                <span style="font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; display:block; margin-bottom:2px;">{{ __('Hotel Accommodations') }}</span>
                                <strong style="font-size:0.95rem; color:var(--text-primary);"><i class="fas fa-hotel" style="color:var(--accent); margin-inline-end:6px;"></i>{{ $booking->package->hotel_name }}</strong>
                                @if($booking->package->hotel_stars > 0)
                                    <span style="color:#f59e0b; margin-inline-start:8px;">
                                        @for($i=0; $i<$booking->package->hotel_stars; $i++) <i class="fas fa-star" style="font-size:0.75rem;"></i> @endfor
                                    </span>
                                @endif
                            </div>
                            @if($booking->package->hotel_website)
                                <a href="{{ $booking->package->hotel_website }}" target="_blank" style="font-size:0.82rem; font-weight:700; color:var(--accent); text-decoration:none;">
                                    <i class="fas fa-external-link-alt"></i> {{ __('Hotel Website') }}
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- Addons list if any --}}
                    @if(!empty($booking->addons) && is_array($booking->addons))
                        <div style="margin-top:18px;">
                            <span style="font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; display:block; margin-bottom:8px;">{{ __('Included Extra Services / Add-ons') }}</span>
                            <div style="display:flex; flex-direction:column; gap:8px;">
                                @foreach($booking->addons as $addonItem)
                                    <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 14px; background:var(--bg-body); border-radius:var(--radius-md); border:1px solid var(--border-soft);">
                                        <span style="font-weight:700; font-size:0.88rem; color:var(--text-primary);">
                                            <i class="fas fa-puzzle-piece" style="color:var(--accent); margin-inline-end:6px;"></i>
                                            {{ $addonItem['name'] ?? ($addonItem['name_ar'] ?? __('Add-on Service')) }}
                                        </span>
                                        <strong style="color:var(--accent); font-size:0.9rem;">+{{ number_format($addonItem['price'] ?? ($addonItem['total'] ?? 0), 0) }} {{ __('SAR') }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Customer Notes --}}
                    @if($booking->notes)
                        <div style="margin-top:18px; padding:14px 18px; background:rgba(245,158,11,0.08); border-radius:var(--radius-md); border:1px solid rgba(245,158,11,0.2);">
                            <span style="font-size:0.75rem; font-weight:800; color:#d97706; text-transform:uppercase; display:block; margin-bottom:2px;"><i class="fas fa-comment-alt"></i> {{ __('Customer Notes / Special Requests') }}</span>
                            <p style="margin:0; font-size:0.88rem; color:var(--text-secondary); line-height:1.5;">{{ $booking->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. Passenger Details & Passports --}}
            <div class="abk-card">
                <div class="abk-card-header">
                    <div class="hdr-left">
                        <span class="hdr-icon"><i class="fas fa-users"></i></span>
                        <h5>{{ __('Passenger Details & Passports') }}</h5>
                    </div>
                    <span style="font-size:0.8rem; font-weight:700; color:var(--text-muted);">{{ $booking->passengers->count() }} {{ __('Registered') }}</span>
                </div>
                <div class="abk-card-body">
                    @forelse($booking->passengers as $pax)
                        <div class="abk-passenger-card">
                            <div style="display:flex; align-items:center; gap:14px;">
                                <div class="abk-pax-avatar">
                                    {{ mb_substr($pax->name ?? $pax->first_name ?? 'P', 0, 1) }}
                                </div>
                                <div>
                                    <h5 class="abk-pax-name">{{ $pax->name ?? trim(($pax->first_name ?? '') . ' ' . ($pax->last_name ?? '')) }}</h5>
                                    <div class="abk-pax-meta">
                                        @if($pax->phone)
                                            <span><i class="fas fa-phone-alt"></i> {{ $pax->phone }}</span>
                                        @endif
                                        @if($pax->nationality)
                                            <span><i class="fas fa-flag"></i> {{ $pax->nationality }}</span>
                                        @endif
                                        @if($pax->dob)
                                            <span><i class="fas fa-birthday-cake"></i> {{ \Carbon\Carbon::parse($pax->dob)->format('d M Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                @if($pax->passport_number)
                                    <div class="abk-pax-passport-pill">
                                        <i class="fas fa-passport" style="color:var(--accent);"></i>
                                        <span>{{ $pax->passport_number }}</span>
                                        @if($pax->passport_expiry)
                                            <small style="color:var(--text-muted);">({{ __('Exp') }}: {{ \Carbon\Carbon::parse($pax->passport_expiry)->format('d/m/Y') }})</small>
                                        @endif
                                    </div>
                                @endif
                                @if($pax->passport_image)
                                    <a href="{{ asset('storage/' . $pax->passport_image) }}" target="_blank" class="abk-pax-passport-pill" style="color:var(--accent); text-decoration:none;">
                                        <i class="fas fa-file-image"></i> {{ __('View Passport') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center; padding:36px 20px; color:var(--text-muted);">
                            <i class="fas fa-user-slash" style="font-size:2rem; opacity:0.3; margin-bottom:8px; display:block;"></i>
                            <p style="margin:0; font-size:0.9rem;">{{ __('No passenger details recorded.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── Right Column: Financials, Customer & Ticket Dispatch ── --}}
        <div>
            {{-- 3. Financial & Revenue Breakdown --}}
            <div class="abk-card">
                <div class="abk-card-header">
                    <div class="hdr-left">
                        <span class="hdr-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                        <h5>{{ __('Financial Breakdown') }}</h5>
                    </div>
                </div>
                <div class="abk-card-body">
                    <div class="abk-finance-box">
                        <div class="abk-finance-row">
                            <span style="color:var(--text-muted);"><i class="fas fa-user-tag" style="margin-inline-end:6px;"></i>{{ __('Customer Paid Amount') }}</span>
                            <strong style="color:var(--text-primary); font-size:1rem;">{{ number_format($booking->total_price ?? 0, 0) }} {{ __('SAR') }}</strong>
                        </div>
                        @if($booking->platform_profit > 0)
                            <div class="abk-finance-row">
                                <span style="color:var(--text-muted);"><i class="fas fa-percentage" style="margin-inline-end:6px;"></i>{{ __('Platform Commission') }}</span>
                                <span style="color:#ef4444; font-weight:700;">-{{ number_format($booking->platform_profit, 0) }} {{ __('SAR') }}</span>
                            </div>
                        @endif
                        <div class="abk-finance-total">
                            <span class="lbl"><i class="fas fa-wallet" style="color:var(--accent); margin-inline-end:6px;"></i>{{ __('Agent Net Revenue') }}</span>
                            <span class="val">{{ number_format($booking->provider_price ?? $booking->total_price, 0) }} <small style="font-size:0.65em;">{{ __('SAR') }}</small></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Primary Customer Information --}}
            <div class="abk-card">
                <div class="abk-card-header">
                    <div class="hdr-left">
                        <span class="hdr-icon"><i class="fas fa-user-circle"></i></span>
                        <h5>{{ __('Customer Profile') }}</h5>
                    </div>
                </div>
                <div class="abk-card-body">
                    <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px;">
                        <div style="width:48px; height:48px; border-radius:50%; background:var(--accent-soft); color:var(--accent); display:flex; align-items:center; justify-content:center; font-size:1.2rem; font-weight:900;">
                            {{ mb_substr($booking->user->full_name ?? $booking->user->name ?? 'C', 0, 1) }}
                        </div>
                        <div>
                            <h5 style="margin:0 0 2px; font-weight:800; color:var(--text-primary); font-size:1rem;">{{ $booking->user->full_name ?? $booking->user->name ?? '-' }}</h5>
                            <span style="font-size:0.8rem; color:var(--text-muted);">{{ __('Registered Client') }}</span>
                        </div>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:10px; font-size:0.88rem;">
                        <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 12px; background:var(--bg-body); border-radius:var(--radius-md);">
                            <span style="color:var(--text-muted);"><i class="fas fa-envelope" style="margin-inline-end:6px;"></i>{{ __('Email') }}</span>
                            <strong style="color:var(--text-primary);">{{ $booking->user->email ?? '-' }}</strong>
                        </div>
                        @if($booking->user->phone ?? null)
                            <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 12px; background:var(--bg-body); border-radius:var(--radius-md);">
                                <span style="color:var(--text-muted);"><i class="fas fa-phone" style="margin-inline-end:6px;"></i>{{ __('Phone') }}</span>
                                <strong style="color:var(--text-primary);">{{ $booking->user->phone }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 5. Ticket Upload & Customer Dispatch --}}
            <div class="abk-card">
                <div class="abk-card-header">
                    <div class="hdr-left">
                        <span class="hdr-icon"><i class="fas fa-paper-plane"></i></span>
                        <h5>{{ __('Tickets & Client Dispatch') }}</h5>
                    </div>
                </div>
                <div class="abk-card-body">
                    @php $ticketFile = $booking->ticket_file_path ?? $booking->tickets ?? null; @endphp
                    @if($ticketFile)
                        <div style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.25); border-radius:var(--radius-lg); padding:16px; margin-bottom:16px;">
                            <div style="display:flex; align-items:center; gap:8px; color:#10b981; font-weight:800; font-size:0.88rem; margin-bottom:8px;">
                                <i class="fas fa-check-circle"></i> {{ __('Ticket Issued & Sent') }}
                            </div>
                            <a href="{{ asset('storage/' . $ticketFile) }}" target="_blank" class="abk-action-btn" style="background:#fff; color:#10b981; border:1px solid rgba(16,185,129,0.3); width:100%; justify-content:center;">
                                <i class="fas fa-file-download"></i> {{ __('Download / Preview Ticket') }}
                            </a>
                        </div>
                    @else
                        <div style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.25); border-radius:var(--radius-lg); padding:14px; margin-bottom:16px; color:#d97706; font-weight:700; font-size:0.85rem; display:flex; align-items:center; gap:8px;">
                            <i class="fas fa-exclamation-triangle"></i> {{ __('Pending Ticket Issuance & Upload') }}
                        </div>
                    @endif

                    <form action="{{ route('agent.bookings.tickets', $booking->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="abk-ticket-dropzone">
                            <i class="fas fa-cloud-upload-alt" style="font-size:1.8rem; color:var(--accent); margin-bottom:6px; display:block;"></i>
                            <div style="font-size:0.85rem; font-weight:800; color:var(--text-primary); margin-bottom:2px;">
                                {{ __('Upload Ticket Voucher') }}
                            </div>
                            <span style="font-size:0.75rem; color:var(--text-muted);">PDF, PNG, JPG ({{ __('Max 5MB') }})</span>
                            <input type="file" name="tickets_file" required onchange="document.getElementById('ticketFileName').innerText = this.files[0].name">
                        </div>
                        <div id="ticketFileName" style="font-size:0.78rem; color:var(--accent); font-weight:700; margin-top:6px; text-align:center;"></div>
                        <button type="submit" class="abk-btn-upload">
                            <i class="fas fa-paper-plane"></i> {{ __('Upload & Notify Customer') }}
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
