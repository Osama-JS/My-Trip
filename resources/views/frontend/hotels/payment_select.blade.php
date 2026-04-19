@extends('frontend.layouts.app')

@section('content')

{{-- Hero Background --}}
<div class="pay-page ">
    <div class="pay-page__bg"></div>

    <div class="fe-container pay-page__inner">

        {{-- ── Breadcrumb Progress ── --}}
        <div class="pay-steps " style="margin-top: 50px;">
            <div class="pay-steps__item done"><span class="pay-steps__bubble"><i class="fas fa-check"></i></span><span class="pay-steps__label">{{ __('Search') }}</span></div>
            <div class="pay-steps__line done"></div>
            <div class="pay-steps__item done"><span class="pay-steps__bubble"><i class="fas fa-check"></i></span><span class="pay-steps__label">{{ __('Guest Details') }}</span></div>
            <div class="pay-steps__line active"></div>
            <div class="pay-steps__item active"><span class="pay-steps__bubble">3</span><span class="pay-steps__label">{{ __('Payment') }}</span></div>
        </div>

        <div class="pay-grid">

            {{-- ── LEFT: Methods ── --}}
            <div class="pay-grid__left">
                <div class="pay-card">
                    <div class="pay-card__head">
                        <div class="pay-card__icon-wrap"><i class="fas fa-lock"></i></div>
                        <div>
                            <h1 class="pay-card__title">{{ __('Secure Checkout') }}</h1>
                            <p class="pay-card__sub">{{ __('Pick a payment method to complete your booking.') }}</p>
                        </div>
                    </div>

                    <div class="pay-methods">

                        {{-- Mada --}}
                        <a href="{{ route('payments.web.checkout', ['booking_id' => $booking->id, 'method' => 'mada', 'type' => 'hotel']) }}" class="pay-method" style="border-right: 5px solid #0055aa;">
                            <div class="pay-method__left">
                                <div class="pay-method__img-wrap" style="background:#fff;">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/f/fb/Mada_Logo.svg" alt="Mada" style="height:18px;">
                                </div>
                                <div class="pay-method__info">
                                    <span class="pay-method__name">{{ __('Mada') }}</span>
                                    <span class="pay-method__desc">{{ __('Local debit card — Zero extra fees') }}</span>
                                </div>
                            </div>
                            <span class="pay-method__arrow"><i class="fas fa-chevron-right"></i></span>
                        </a>

                        {{-- Card --}}
                        <a href="{{ route('payments.web.checkout', ['booking_id' => $booking->id, 'method' => 'visa_master', 'type' => 'hotel']) }}" class="pay-method">
                            <div class="pay-method__left">
                                <div class="pay-method__img-wrap card-wrap">
                                    <div class="card-chips">
                                        <img src="https://www.logo.wine/a/logo/Visa_Inc./Visa_Inc.-Logo.wine.svg" alt="Visa">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard">
                                    </div>
                                </div>
                                <div class="pay-method__info">
                                    <span class="pay-method__name">{{ __('Credit Cards') }}</span>
                                    <span class="pay-method__desc">{{ __('Visa, Mastercard — global coverage') }}</span>
                                </div>
                            </div>
                            <span class="pay-method__arrow"><i class="fas fa-chevron-right"></i></span>
                        </a>

                        {{-- Tap --}}
                        <a href="{{ route('payments.web.checkout', ['booking_id' => $booking->id, 'method' => 'tap', 'type' => 'hotel']) }}" class="pay-method">
                            <div class="pay-method__left">
                                <div class="pay-method__img-wrap" style="background:#f5f8ff;">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/cb/Tap_Payments_Logo.svg/512px-Tap_Payments_Logo.svg.png" alt="Tap" style="height:26px;">
                                </div>
                                <div class="pay-method__info">
                                    <span class="pay-method__name">{{ __('Tap Payment') }}</span>
                                    <span class="pay-method__desc">{{ __('Fast, secure global gateway') }}</span>
                                </div>
                            </div>
                            <span class="pay-method__arrow"><i class="fas fa-chevron-right"></i></span>
                        </a>

                        {{-- Apple Pay --}}
                        <!-- <a href="{{ route('payments.web.checkout', ['booking_id' => $booking->id, 'method' => 'apple_pay', 'type' => 'hotel']) }}" class="pay-method pay-method--dark">
                            <div class="pay-method__left">
                                <div class="pay-method__img-wrap" style="background:rgba(255,255,255,.08);">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b0/Apple_Pay_logo.svg" alt="Apple Pay" style="height:28px; filter:brightness(0) invert(1);">
                                </div>
                                <div class="pay-method__info">
                                    <span class="pay-method__name">{{ __('Apple Pay') }}</span>
                                    <span class="pay-method__desc" style="color:rgba(255,255,255,.5);">{{ __('One-touch secure payment') }}</span>
                                </div>
                            </div>
                            <span class="pay-method__arrow"><i class="fas fa-chevron-right"></i></span>
                        </a> -->

                        {{-- Installments Label --}}
                        <div class="pay-divider"><span>{{ __('Pay in installments') }}</span></div>

                        <div class="pay-mini-grid">
                            <a href="{{ route('payments.web.checkout', ['booking_id' => $booking->id, 'method' => 'tamara', 'type' => 'hotel']) }}" class="pay-mini">
                                <img src="https://cdn.tamara.co/assets/svg/tamara-logo-badge-en.svg" alt="Tamara">
                                <span>{{ __('Split × 3 or 4') }}</span>
                            </a>
                            <a href="{{ route('payments.web.checkout', ['booking_id' => $booking->id, 'method' => 'tabby', 'type' => 'hotel']) }}" class="pay-mini">
                                <img src="https://www.pfgrowth.com/wp-content/uploads/2023/03/tabby-logo-1.png" alt="Tabby" style="background:#39ffbd; padding:3px 6px; border-radius:5px;">
                                <span>{{ __('Pay later, no interest') }}</span>
                            </a>
                        </div>

                    </div>

                    <div class="pay-trust">
                        <span><i class="fas fa-shield-alt"></i> {{ __('256-bit SSL') }}</span>
                        <span><i class="fas fa-lock"></i> {{ __('PCI-DSS Level 1') }}</span>
                        <span><i class="fas fa-check-circle"></i> {{ __('Encrypted') }}</span>
                    </div>
                </div>
            </div>

            {{-- ── RIGHT: Summary ── --}}
            <div class="pay-grid__right">
                <div class="pay-summary">
                    <div class="pay-summary__tag"><i class="fas fa-building"></i>  {{ __('Hotel Booking') }}</div>
                    <h2 class="pay-summary__name">{{ $booking->hotel_name }}</h2>
                    <p class="pay-summary__loc"><i class="fas fa-map-marker-alt" style="color:#f97316;"></i> {{ $booking->city_name }}, {{ $booking->country_name }}</p>

                    <div class="pay-summary__dates">
                        <div class="pay-summary__date-box">
                            <span>{{ __('Check-in') }}</span>
                            <strong>{{ $booking->check_in->format('d M') }}</strong>
                            <small>{{ $booking->check_in->format('Y') }}</small>
                        </div>
                        <div class="pay-summary__date-divider"><i class="fas fa-plane" style="transform:rotate(90deg);"></i></div>
                        <div class="pay-summary__date-box pay-summary__date-box--right">
                            <span>{{ __('Check-out') }}</span>
                            <strong>{{ $booking->check_out->format('d M') }}</strong>
                            <small>{{ $booking->check_out->format('Y') }}</small>
                        </div>
                    </div>

                    @if($booking->room_name)
                    <div class="pay-summary__pill">
                        <i class="fas fa-bed"></i> {{ $booking->room_name }}
                        @if($booking->board_type)
                        <span class="pay-summary__board">{{ $booking->board_type }}</span>
                        @endif
                    </div>
                    @endif

                    <div class="pay-summary__amount">
                        <span>{{ __('Total Due') }}</span>
                        <div class="pay-summary__price">
                            {{ number_format($booking->total_price, 2) }}
                            <sup>{{ $booking->currency }}</sup>
                        </div>
                    </div>

                    <div class="pay-summary__refund">
                        <i class="fas fa-undo-alt"></i> {{ __('Free cancellation available on most rooms') }}
                    </div>
                </div>
            </div>

        </div>{{-- /pay-grid --}}
    </div>{{-- /container --}}
</div>{{-- /pay-page --}}

<style>
/* ─── Root ─────── */
.pay-page {
    position: relative;
    min-height: 100vh;
    padding: 70px 0 80px;
    font-family: 'Outfit', 'Cairo', sans-serif;
    overflow: hidden;
}
.pay-page__bg {
    position: absolute; inset: 0;
    background: linear-gradient(135deg, #0a2540 0%, #0f4c81 55%, #1565c0 100%);
    z-index: 0;
}
.pay-page__bg::after {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse at 70% 20%, rgba(255,255,255,.07) 0%, transparent 60%),
                radial-gradient(ellipse at 20% 90%, rgba(0,100,200,.15) 0%, transparent 50%);
}
.pay-page__inner { position: relative; z-index: 1; }

/* ─── Steps ─────── */
.pay-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: 50px;
}
.pay-steps__item {
    display: flex; flex-direction: column; align-items: center; gap: 8px;
}
.pay-steps__bubble {
    width: 44px; height: 44px; border-radius: 50%;
    border: 2px solid rgba(255,255,255,.25);
    background: rgba(255,255,255,.1);
    backdrop-filter: blur(8px);
    color: rgba(255,255,255,.5);
    font-weight: 700; font-size: 0.95rem;
    display: flex; align-items: center; justify-content: center;
    transition: all .3s;
}
.pay-steps__item.done .pay-steps__bubble {
    background: #10b981; border-color: #10b981; color: white;
}
.pay-steps__item.active .pay-steps__bubble {
    background: white; border-color: white; color: #0f4c81;
    box-shadow: 0 0 0 6px rgba(255,255,255,.15);
}
.pay-steps__label {
    font-size: .75rem; font-weight: 700; color: rgba(255,255,255,.5);
    text-transform: uppercase; letter-spacing: .5px;
}
.pay-steps__item.done .pay-steps__label,
.pay-steps__item.active .pay-steps__label { color: rgba(255,255,255,.9); }
.pay-steps__line {
    flex: 1; max-width: 80px; height: 2px;
    background: rgba(255,255,255,.15);
    margin: 0 12px 22px;
    transition: all .3s;
}
.pay-steps__line.done { background: #10b981; }
.pay-steps__line.active { background: rgba(255,255,255,.4); }

/* ─── Grid ─────── */
.pay-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 30px;
    align-items: start;
}

/* ─── Left Card ─────── */
.pay-card {
    background: rgba(255,255,255,.97);
    border-radius: 28px;
    padding: 44px 44px 36px;
    box-shadow: 0 40px 80px rgba(0,0,0,.25);
}
.pay-card__head {
    display: flex; align-items: flex-start; gap: 20px;
    margin-bottom: 36px; padding-bottom: 28px;
    border-bottom: 1px solid #f1f5f9;
}
.pay-card__icon-wrap {
    width: 52px; height: 52px; flex-shrink: 0;
    background: linear-gradient(135deg, #0f4c81, #1976d2);
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 1.2rem;
    box-shadow: 0 8px 20px rgba(15,76,129,.35);
}
.pay-card__title {
    font-size: 1.65rem; font-weight: 900; color: #0f172a;
    margin: 0 0 6px; letter-spacing: -.02em;
}
.pay-card__sub { color: #64748b; font-size: .95rem; margin: 0; }

/* ─── Methods ─────── */
.pay-methods { display: flex; flex-direction: column; gap: 12px; }

.pay-method {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 22px;
    background: #f8fafc;
    border: 1.5px solid #e8edf3;
    border-radius: 18px;
    text-decoration: none !important;
    transition: all .35s cubic-bezier(.175,.885,.32,1.275);
    cursor: pointer;
}
.pay-method:hover {
    background: white;
    border-color: #0f4c81;
    box-shadow: 0 8px 24px rgba(15,76,129,.12);
    transform: translateY(-3px);
}
.pay-method--dark { background: #0d1117; border-color: #1c2638; }
.pay-method--dark:hover { border-color: #555; box-shadow: 0 8px 24px rgba(0,0,0,.3); }
.pay-method--dark .pay-method__arrow { color: rgba(255,255,255,.3); }
.pay-method--dark:hover .pay-method__arrow { color: white; }

.pay-method__left { display: flex; align-items: center; gap: 18px; }
.pay-method__img-wrap {
    width: 68px; height: 52px;
    border-radius: 14px;
    background: white;
    display: flex; align-items: center; justify-content: center;
    border: 1px solid #e8edf3;
    overflow: hidden;
    flex-shrink: 0;
}
.card-chips { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; padding: 4px; }
.card-chips img { height: 14px; object-fit: contain; }

.pay-method__name {
    display: block; font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 3px;
}
.pay-method--dark .pay-method__name { color: white; }
.pay-method__desc { display: block; font-size: .82rem; color: #94a3b8; font-weight: 500; }

.pay-method__arrow {
    width: 36px; height: 36px;
    background: white; border: 1px solid #e8edf3;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #cbd5e1; font-size: .8rem;
    transition: all .3s; flex-shrink: 0;
}
.pay-method:hover .pay-method__arrow {
    background: #0f4c81; border-color: #0f4c81; color: white;
}

/* ─── Divider ─────── */
.pay-divider {
    display: flex; align-items: center; gap: 15px;
    margin: 8px 0;
    color: #94a3b8; font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
}
.pay-divider::before, .pay-divider::after {
    content: ''; flex: 1; height: 1px; background: #e8edf3;
}

/* ─── Mini Methods ─────── */
.pay-mini-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.pay-mini {
    display: flex; flex-direction: column; align-items: center; gap: 10px;
    padding: 18px 12px;
    background: #f8fafc; border: 1.5px solid #e8edf3; border-radius: 16px;
    text-decoration: none !important;
    transition: all .3s;
}
.pay-mini:hover { border-color: #0f4c81; background: white; transform: translateY(-2px); }
.pay-mini img { height: 22px; object-fit: contain; }
.pay-mini span { font-size: .78rem; font-weight: 700; color: #475569; text-align: center; }

/* ─── Trust ─────── */
.pay-trust {
    display: flex; justify-content: center; gap: 24px;
    margin-top: 28px; padding-top: 24px;
    border-top: 1px solid #f1f5f9;
    color: #94a3b8; font-size: .78rem; font-weight: 600;
}
.pay-trust span { display: flex; align-items: center; gap: 6px; }
.pay-trust i { color: #10b981; }

/* ─── Summary Card ─────── */
.pay-summary {
    background: rgba(255,255,255,.09);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 28px;
    padding: 36px;
    position: sticky; top: 24px;
    color: white;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
}
.pay-summary__tag {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
    border-radius: 100px;
    padding: 6px 16px;
    font-size: .73rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px;
    color: rgba(255,255,255,.85);
    margin-bottom: 22px;
}
.pay-summary__name {
    font-size: 1.6rem; font-weight: 950; color: white;
    margin: 0 0 10px; letter-spacing: -.02em; line-height: 1.2;
}
.pay-summary__loc {
    font-size: .9rem; color: rgba(255,255,255,.65); margin: 0 0 28px; display: flex; align-items: center; gap: 6px;
}

.pay-summary__dates {
    display: flex; align-items: center; justify-content: space-between;
    background: rgba(255,255,255,.08); border-radius: 18px; padding: 20px;
    margin-bottom: 16px; border: 1px solid rgba(255,255,255,.1);
}
.pay-summary__date-box { display: flex; flex-direction: column; }
.pay-summary__date-box--right { text-align: end; align-items: flex-end; }
.pay-summary__date-box span { font-size: .7rem; color: rgba(255,255,255,.5); font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
.pay-summary__date-box strong { font-size: 1.4rem; font-weight: 900; color: white; line-height: 1; }
.pay-summary__date-box small { font-size: .8rem; color: rgba(255,255,255,.55); font-weight: 600; }
.pay-summary__date-divider { color: rgba(255,255,255,.3); font-size: 1.2rem; }

.pay-summary__pill {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    background: rgba(255,255,255,.1); border-radius: 12px; padding: 10px 16px;
    font-size: .85rem; font-weight: 700; color: rgba(255,255,255,.85);
    margin-bottom: 28px;
}
.pay-summary__board {
    font-size: .75rem; color: rgba(255,255,255,.55); font-weight: 500;
    background: rgba(255,255,255,.08); padding: 2px 8px; border-radius: 6px;
}

.pay-summary__amount {
    display: flex; align-items: center; justify-content: space-between;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
    border-radius: 16px; padding: 20px 22px; margin-bottom: 16px;
}
.pay-summary__amount span { font-size: .8rem; font-weight: 700; color: rgba(255,255,255,.6); text-transform: uppercase; }
.pay-summary__price {
    font-size: 2.2rem; font-weight: 950; color: white; line-height: 1;
}
.pay-summary__price sup { font-size: .95rem; font-weight: 700; color: rgba(255,255,255,.7); margin-inline-start: 4px; vertical-align: super; }

.pay-summary__refund {
    display: flex; align-items: center; gap: 8px;
    font-size: .78rem; color: rgba(255,255,255,.5); font-weight: 600;
}
.pay-summary__refund i { color: #10b981; }

/* ─── RTL ─────── */
[dir="rtl"] .pay-method:hover { transform: translateY(-3px); }
[dir="rtl"] .pay-steps__line { margin: 0 12px 22px; }
[dir="rtl"] .pay-summary__date-box--right { text-align: start; align-items: flex-start; }

/* ─── Responsive ─────── */
@media (max-width: 992px) {
    .pay-grid { grid-template-columns: 1fr; }
    .pay-summary { position: static; }
    .pay-card { padding: 28px 22px; }
}
@media (max-width: 576px) {
    .pay-page { padding: 50px 0 60px; }
    .pay-steps__line { max-width: 40px; }
    .pay-card__title { font-size: 1.3rem; }
    .pay-trust { flex-wrap: wrap; gap: 12px; }
}
</style>

@endsection
