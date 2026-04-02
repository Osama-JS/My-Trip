@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Hotel Booking') . ' · ' . ($booking->reference_num ?? '#' . $booking->id))
@section('page-title', __('Hotel Booking Details'))

@section('content')

{{-- ─── Top Status Banner ─── --}}
@if($booking->status === 'confirmed')
<div class="alert-banner alert-confirmed">
    <i class="fas fa-check-circle"></i>
    <div>
        <strong>{{ __('Booking Confirmed!') }}</strong>
        <span>{{ __('Your stay at :hotel is fully confirmed.', ['hotel' => $booking->hotel_name]) }}</span>
    </div>
    @if($booking->supplier_confirmation_num)
    <div class="conf-badge">{{ $booking->supplier_confirmation_num }}</div>
    @endif
</div>
@elseif($booking->status === 'paid')
<div class="alert-banner alert-processing">
    <i class="fas fa-hourglass-half fa-spin-slow"></i>
    <div>
        <strong>{{ __('Payment Received') }}</strong>
        <span>{{ __('Finalizing your reservation with the hotel supplier...') }}</span>
    </div>
</div>
@elseif($booking->status === 'cancelled')
<div class="alert-banner alert-cancelled">
    <i class="fas fa-times-circle"></i>
    <div>
        <strong>{{ __('Booking Cancelled') }}</strong>
        <span>{{ __('This reservation has been cancelled.') }}</span>
    </div>
</div>
@endif

<div class="hbd-grid">

    {{-- ─── LEFT ─── --}}
    <div class="hbd-main">

        {{-- Hotel Hero Card --}}
        <div class="hbd-card hotel-hero">
            <div class="hotel-avatar"><i class="fas fa-hotel"></i></div>
            <div class="hotel-meta">
                <h2 class="hotel-title">{{ $booking->hotel_name }}</h2>
                <p class="hotel-loc"><i class="fas fa-map-marker-alt"></i> {{ $booking->city_name }}, {{ $booking->country_name }}</p>
                <div class="hbd-tags">
                    <span class="tag-status tag-{{ $booking->status }}">{{ __($booking->status) }}</span>
                    <span class="tag-ref"><i class="fas fa-hashtag"></i>{{ $booking->reference_num ?? $booking->id }}</span>
                </div>
            </div>
        </div>

        {{-- Dates + Rooms Row --}}
        <div class="hbd-row-2">
            <div class="hbd-card">
                <div class="hbd-card-head"><i class="fas fa-calendar-alt"></i> {{ __('Stay Duration') }}</div>
                <div class="hbd-card-body">
                    <div class="dates-row">
                        <div class="date-box">
                            <label>{{ __('Check-in') }}</label>
                            <strong>{{ $booking->check_in->format('D, d M Y') }}</strong>
                        </div>
                        <div class="date-arrow"><i class="fas fa-long-arrow-alt-right"></i></div>
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

            <div class="hbd-card">
                <div class="hbd-card-head"><i class="fas fa-bed"></i> {{ __('Accommodation') }}</div>
                <div class="hbd-card-body">
                    <div class="info-row"><span>{{ __('Room') }}</span><strong>{{ $booking->room_name ?? 'N/A' }}</strong></div>
                    <div class="info-row"><span>{{ __('Board') }}</span><strong>{{ $booking->board_type ?? 'N/A' }}</strong></div>
                    <div class="info-row"><span>{{ __('Guests') }}</span><strong>{{ $booking->adults }} {{ __('Adult(s)') }}@if($booking->childs > 0), {{ $booking->childs }} {{ __('Child(ren)') }}@endif</strong></div>
                    <div class="info-row"><span>{{ __('Rooms') }}</span><strong>{{ $booking->rooms }}</strong></div>
                </div>
            </div>
        </div>

        {{-- Guests --}}
        <div class="hbd-card">
            <div class="hbd-card-head"><i class="fas fa-user-friends"></i> {{ __('Guest Details') }}</div>
            <div class="hbd-card-body">
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
        <div class="hbd-card card-tip">
            <i class="fas fa-info-circle tip-icon"></i>
            <div>
                <h6>{{ __('Check-in Instructions') }}</h6>
                <p>{{ __('Present this voucher (digital or printed) with your Passport/ID at the hotel reception. The hotel will use the Supplier Reference as proof of your reservation.') }}</p>
            </div>
        </div>

    </div>

    {{-- ─── RIGHT ─── --}}
    <div class="hbd-sidebar">

        {{-- Payment Card --}}
        <div class="hbd-card card-primary">
            <div class="price-label">{{ __('Total Paid') }}</div>
            <div class="price-amount">{{ number_format($booking->total_price, 2) }} <span>{{ $booking->currency }}</span></div>

            <div class="refs-section">
                @if($booking->reference_num)
                <div class="ref-item">
                    <div class="ref-label">{{ __('Booking ID') }}</div>
                    <div class="ref-val">{{ $booking->reference_num }}</div>
                </div>
                @endif

                @if($booking->supplier_confirmation_num)
                <div class="ref-item ref-item-green">
                    <div class="ref-label"><i class="fas fa-check-circle"></i> {{ __('Supplier Reference') }}</div>
                    <div class="ref-val">{{ $booking->supplier_confirmation_num }}</div>
                </div>
                @else
                <div class="ref-item ref-item-warn">
                    <div class="ref-label"><i class="fas fa-hourglass-half"></i> {{ __('Supplier Confirmation') }}</div>
                    <div class="ref-val" style="font-size: 0.85rem;">{{ __('Pending...') }}</div>
                </div>
                @endif
            </div>

            <div class="actions-stack">
                @if($booking->status === 'confirmed')
                <a href="{{ route('customer.bookings.hotels.voucher', $booking->id) }}" class="btn-action btn-green">
                    <i class="fas fa-download"></i> {{ __('Download Voucher') }}
                </a>
                @endif

                <form action="{{ route('customer.bookings.hotels.sync-status', $booking->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-action btn-ghost">
                        <i class="fas fa-sync-alt"></i> {{ __('Refresh Status') }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Booking Timeline --}}
        <div class="hbd-card">
            <div class="hbd-card-head"><i class="fas fa-stream"></i> {{ __('Booking Progress') }}</div>
            <div class="hbd-card-body">
                <div class="timeline-v">
                    <div class="tl-item {{ in_array($booking->status, ['pending','paid','confirmed']) ? 'done' : '' }}">
                        <div class="tl-dot"><i class="fas fa-file-alt"></i></div>
                        <div class="tl-text"><strong>{{ __('Booking Created') }}</strong><small>{{ $booking->created_at->format('d M Y, H:i') }}</small></div>
                    </div>
                    <div class="tl-item {{ in_array($booking->status, ['paid','confirmed']) ? 'done' : '' }}">
                        <div class="tl-dot"><i class="fas fa-credit-card"></i></div>
                        <div class="tl-text"><strong>{{ __('Payment Verified') }}</strong><small>{{ in_array($booking->status, ['paid','confirmed']) ? __('Completed') : __('Awaiting...') }}</small></div>
                    </div>
                    <div class="tl-item {{ $booking->status === 'confirmed' ? 'done' : ($booking->status === 'cancelled' ? 'cancelled' : '') }}">
                        <div class="tl-dot"><i class="fas fa-check-double"></i></div>
                        <div class="tl-text">
                            <strong>{{ $booking->status === 'cancelled' ? __('Cancelled') : __('Confirmed by Supplier') }}</strong>
                            <small>{{ $booking->status === 'confirmed' ? __('Ready for Stay') : ($booking->status === 'cancelled' ? __('Reservation Cancelled') : __('Processing...')) }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Support --}}
        <div class="hbd-card">
            <div class="hbd-card-head"><i class="fas fa-shield-alt"></i> {{ __('Manage Booking') }}</div>
            <div class="hbd-card-body">
                @if($booking->status !== 'cancelled')
                <button class="btn-action btn-danger-soft" id="btn-cancel-hotel" data-id="{{ $booking->id }}">
                    <i class="fas fa-times-circle"></i> {{ __('Request Cancellation') }}
                </button>
                @endif
                <div class="support-links mt-3">
                    <a href="#"><i class="fas fa-headset"></i> {{ __('Contact Support') }}</a>
                    <a href="#"><i class="fas fa-question-circle"></i> {{ __('Help Center') }}</a>
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

@push('styles')
<style>
:root {
    --primary: #0f4c81;
    --primary-light: #e8f0fe;
    --success: #10b981;
    --success-light: #ecfdf5;
    --warning: #f59e0b;
    --warning-light: #fffbeb;
    --danger: #ef4444;
    --danger-light: #fef2f2;
    --card-shadow: 0 4px 20px rgba(0,0,0,0.06);
    --radius: 18px;
}

/* ─── Alert Banners ─── */
.alert-banner {
    display: flex; align-items: center; gap: 16px;
    padding: 16px 24px; border-radius: 14px; margin-bottom: 24px;
    font-size: 0.92rem; flex-wrap: wrap;
}
.alert-banner i { font-size: 1.5rem; flex-shrink: 0; }
.alert-banner strong { display: block; font-weight: 800; font-size: 1.0rem; }
.alert-confirmed { background: var(--success-light); color: #065f46; border: 1px solid #a7f3d0; }
.alert-confirmed i { color: var(--success); }
.alert-processing { background: var(--warning-light); color: #92400e; border: 1px solid #fde68a; }
.alert-processing i { color: var(--warning); }
.alert-cancelled { background: var(--danger-light); color: #991b1b; border: 1px solid #fca5a5; }
.alert-cancelled i { color: var(--danger); }
.conf-badge {
    margin-left: auto; background: white; padding: 8px 18px; border-radius: 100px;
    font-weight: 900; font-size: 1.1rem; letter-spacing: 1px; color: var(--success);
    border: 1px solid #a7f3d0;
}

/* ─── Grid ─── */
.hbd-grid { display: grid; grid-template-columns: 1fr 360px; gap: 24px; }
.hbd-main { display: flex; flex-direction: column; gap: 20px; }
.hbd-sidebar { display: flex; flex-direction: column; gap: 20px; }

/* ─── Cards ─── */
.hbd-card {
    background: #fff; border-radius: var(--radius);
    border: 1px solid #f0f4f8; box-shadow: var(--card-shadow); overflow: hidden;
}
.hbd-card-head {
    padding: 18px 24px; border-bottom: 1px solid #f8fafc;
    font-weight: 800; font-size: 0.95rem; color: #0f172a;
    display: flex; align-items: center; gap: 10px;
}
.hbd-card-head i { color: var(--primary); }
.hbd-card-body { padding: 24px; }

/* ─── Hotel Hero ─── */
.hotel-hero {
    display: flex; align-items: center; gap: 20px; padding: 24px;
    background: linear-gradient(135deg, #ffffff, #f0f6ff);
}
.hotel-avatar {
    width: 72px; height: 72px; border-radius: 20px;
    background: var(--primary-light); color: var(--primary);
    display: flex; align-items: center; justify-content: center; font-size: 2rem; flex-shrink: 0;
}
.hotel-meta { flex: 1; }
.hotel-title { font-size: 1.5rem; font-weight: 900; color: #0f172a; margin: 0 0 4px; }
.hotel-loc { color: #64748b; font-size: 0.9rem; margin: 0 0 10px; }
.hbd-tags { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.tag-status {
    padding: 4px 14px; border-radius: 100px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;
}
.tag-confirmed { background: var(--success-light); color: var(--success); border: 1px solid #d1fae5; }
.tag-pending   { background: var(--warning-light); color: var(--warning); border: 1px solid #fde68a; }
.tag-paid      { background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; }
.tag-cancelled { background: var(--danger-light); color: var(--danger); border: 1px solid #fca5a5; }
.tag-ref { background: #f1f5f9; color: #64748b; padding: 4px 12px; border-radius: 100px; font-size: 0.8rem; font-weight: 700; }

/* ─── Dates ─── */
.hbd-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.dates-row { display: flex; align-items: center; justify-content: space-between; }
.date-box label { display: block; font-size: 0.72rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
.date-box strong { font-size: 1rem; font-weight: 800; color: #0f172a; }
.date-arrow { color: #cbd5e1; font-size: 1.2rem; }
.nights-pill {
    margin-top: 16px; background: var(--primary-light); color: var(--primary);
    border-radius: 100px; padding: 6px 14px; font-size: 0.85rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: 6px;
}

/* ─── Info Rows ─── */
.info-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 8px 0; border-bottom: 1px solid #f8fafc; font-size: 0.9rem;
}
.info-row:last-child { border-bottom: none; }
.info-row span { color: #64748b; font-weight: 600; }
.info-row strong { color: #0f172a; font-weight: 800; }

/* ─── Guests ─── */
.room-group { margin-bottom: 16px; }
.room-group:last-child { margin-bottom: 0; }
.room-group-label { font-size: 0.8rem; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
.guest-chips { display: flex; flex-wrap: wrap; gap: 8px; }
.guest-chip {
    display: flex; align-items: center; gap: 8px;
    background: #f8fafc; border: 1px solid #f1f5f9; padding: 8px 14px; border-radius: 10px;
    font-size: 0.88rem;
}
.guest-chip i { color: #94a3b8; }
.guest-chip span { font-weight: 700; color: #1e293b; }
.guest-chip small { color: #94a3b8; font-weight: 600; }

/* ─── Tip Card ─── */
.card-tip {
    display: flex; align-items: flex-start; gap: 16px; padding: 20px 24px;
    background: #f0f7ff; border: 1px dashed #bfdbfe;
}
.tip-icon { font-size: 1.5rem; color: var(--primary); flex-shrink: 0; margin-top: 2px; }
.card-tip h6 { font-weight: 800; color: #0f172a; margin: 0 0 4px; font-size: 0.95rem; }
.card-tip p { color: #475569; font-size: 0.85rem; margin: 0; line-height: 1.6; }

/* ─── Primary Card ─── */
.card-primary { background: var(--primary); color: white; border: none; padding: 28px; }
.price-label { font-size: 0.75rem; color: rgba(255,255,255,0.7); font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
.price-amount { font-size: 2.4rem; font-weight: 950; color: white; margin-bottom: 20px; }
.price-amount span { font-size: 1rem; opacity: 0.8; vertical-align: middle; margin-left: 4px; }

.refs-section { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
.ref-item { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; padding: 14px 16px; }
.ref-item-green { background: rgba(16,185,129,0.15); border-color: rgba(16,185,129,0.3); }
.ref-item-warn { background: rgba(245,158,11,0.15); border-color: rgba(245,158,11,0.3); }
.ref-label { font-size: 0.7rem; color: rgba(255,255,255,0.65); font-weight: 700; text-transform: uppercase; margin-bottom: 4px; display: flex; align-items: center; gap: 5px; }
.ref-val { font-size: 1.2rem; font-weight: 900; color: white; letter-spacing: 0.5px; }

/* ─── Buttons ─── */
.actions-stack { display: flex; flex-direction: column; gap: 8px; }
.btn-action {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 13px; border-radius: 12px; font-weight: 800; font-size: 0.9rem;
    border: none; cursor: pointer; text-decoration: none; transition: all .2s;
    width: 100%;
}
.btn-green { background: var(--success); color: white; }
.btn-green:hover { background: #059669; color: white; transform: translateY(-2px); }
.btn-ghost { background: rgba(255,255,255,0.15); color: white; }
.btn-ghost:hover { background: rgba(255,255,255,0.25); color: white; }
.btn-danger-soft { background: var(--danger-light); color: var(--danger); border: 1px solid #fca5a5; width: 100%; }
.btn-danger-soft:hover { background: #fee2e2; transform: translateY(-2px); }

/* ─── Timeline ─── */
.timeline-v { display: flex; flex-direction: column; gap: 0; }
.tl-item { display: flex; gap: 14px; position: relative; padding-bottom: 20px; }
.tl-item:last-child { padding-bottom: 0; }
.tl-item::before {
    content: ''; position: absolute; left: 17px; top: 34px;
    width: 2px; background: #e2e8f0; height: calc(100% - 10px);
}
.tl-item:last-child::before { display: none; }
.tl-item.done::before { background: var(--success); }
.tl-dot {
    width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
    background: #e2e8f0; color: #94a3b8;
    display: flex; align-items: center; justify-content: center; font-size: 0.85rem;
    position: relative; z-index: 1;
}
.tl-item.done .tl-dot { background: var(--success); color: white; }
.tl-item.cancelled .tl-dot { background: var(--danger); color: white; }
.tl-text strong { display: block; font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
.tl-text small { color: #94a3b8; font-size: 0.78rem; font-weight: 600; }

/* ─── Support ─── */
.support-links { display: flex; flex-direction: column; gap: 8px; }
.support-links a { text-decoration: none; color: #64748b; font-size: 0.88rem; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all .2s; }
.support-links a:hover { color: var(--primary); padding-left: 4px; }

/* ─── Responsive ─── */
@media (max-width: 1024px) {
    .hbd-grid { grid-template-columns: 1fr; }
    .hbd-sidebar { display: grid; grid-template-columns: 1fr 1fr; }
}
@media (max-width: 640px) {
    .hbd-row-2 { grid-template-columns: 1fr; }
    .hbd-sidebar { display: flex; }
    .hotel-hero { flex-direction: column; align-items: flex-start; }
}

/* Spin slow */
@keyframes spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.fa-spin-slow { animation: spin-slow 3s linear infinite; }
</style>
@endpush
