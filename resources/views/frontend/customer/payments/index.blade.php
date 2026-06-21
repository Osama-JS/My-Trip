@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Payments & Invoices'))
@section('page-title', __('Payments & Invoices'))

@push('styles')
<style>
    /* ─── Premium Header ─── */
    .payment-header-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 20px;
        padding: 32px;
        margin-bottom: 28px;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12);
        border: 1px solid rgba(255,255,255,0.05);
    }
    .payment-header-banner h2 {
        margin: 0;
        font-weight: 900;
        font-size: 1.8rem;
        letter-spacing: -0.5px;
    }
    .payment-header-banner p {
        margin: 6px 0 0;
        color: #94a3b8;
        font-weight: 500;
        font-size: 1rem;
    }

    /* ─── Stats Grid ─── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }
    @media (max-width: 991px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
    .stat-card-custom {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .stat-card-custom:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.06);
    }
    .stat-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .stat-info-box {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .stat-info-box label {
        font-size: 0.78rem;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        margin: 0;
    }
    .stat-info-box span {
        font-size: 1.6rem;
        font-weight: 850;
        color: var(--text-main);
        line-height: 1.1;
    }

    .stat-total .stat-icon-box { background: rgba(37, 99, 235, 0.08); color: var(--primary-blue); }
    .stat-paid .stat-icon-box { background: rgba(16, 185, 129, 0.08); color: #10b981; }
    .stat-pending .stat-icon-box { background: rgba(245, 158, 11, 0.08); color: #f59e0b; }
    .stat-failed .stat-icon-box { background: rgba(239, 68, 68, 0.08); color: #ef4444; }

    /* ─── Filter Bar ─── */
    .filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }
    .filter-pills {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .pill-btn {
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
    .pill-btn:hover {
        color: var(--primary-blue);
        border-color: var(--primary-blue);
        background: rgba(37, 99, 235, 0.03);
    }
    .pill-btn.active {
        background: var(--primary-blue);
        border-color: var(--primary-blue);
        color: #fff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.18);
    }

    /* ─── Advanced Filters Drawer ─── */
    .filters-drawer {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 28px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.01);
        display: none;
        animation: fadeInUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .drawer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1.5fr;
        gap: 16px;
    }
    @media (max-width: 768px) {
        .drawer-grid { grid-template-columns: 1fr; }
    }
    .form-group-custom {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .form-group-custom label {
        font-size: 0.78rem;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }
    .input-field-custom {
        background: var(--bg-main);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        padding: 11px 14px;
        border-radius: 10px;
        font-size: 0.88rem;
        font-weight: 600;
        width: 100%;
        outline: none;
        transition: all 0.2s;
    }
    .input-field-custom:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .drawer-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid var(--border-color);
    }
    .btn-sm-custom {
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
    }
    .btn-outline-custom {
        border-color: var(--border-color);
        background: var(--bg-card);
        color: var(--text-main);
    }
    .btn-outline-custom:hover {
        border-color: var(--primary-blue);
        color: var(--primary-blue);
        background: rgba(37, 99, 235, 0.03);
        transform: translateY(-1px);
    }
    .btn-accent-custom {
        background: var(--primary-blue);
        color: #fff;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
    }
    .btn-accent-custom:hover {
        background: #1d4ed8;
        box-shadow: 0 6px 14px rgba(37, 99, 235, 0.25);
        transform: translateY(-1px);
    }

    /* ─── Payments Card List ─── */
    .payments-container {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .payment-card-row {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.005);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .payment-card-row:hover {
        transform: translateY(-2px);
        border-color: var(--primary-blue);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.04);
    }
    .payment-main-info {
        display: flex;
        align-items: center;
        gap: 20px;
        flex: 1;
        min-width: 0;
    }
    
    /* Category Icon Box */
    .payment-category-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .cat-trip { background: rgba(59, 130, 246, 0.08); color: #2563eb; }
    .cat-hotel { background: rgba(139, 92, 246, 0.08); color: #8b5cf6; }
    .cat-flight { background: rgba(20, 184, 166, 0.08); color: #14b8a6; }
    .cat-fallback { background: rgba(100, 116, 139, 0.08); color: #64748b; }

    .payment-meta-details {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 0;
        flex: 1;
    }
    .payment-title-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .payment-title {
        font-weight: 800;
        font-size: 1rem;
        color: var(--text-main);
        line-height: 1.3;
    }
    .payment-id-badge {
        font-size: 0.72rem;
        font-weight: 800;
        background: var(--bg-main);
        color: var(--text-muted);
        padding: 2px 8px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
    }
    .payment-meta-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        font-size: 0.78rem;
        color: var(--text-muted);
    }
    .bullet-sep {
        color: var(--border-color);
    }
    
    .gateway-tag {
        font-size: 0.68rem;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .gw-tabby { background: #ffe4e6; color: #e11d48; }
    .gw-tamara { background: #fef3c7; color: #d97706; }
    .gw-card { background: #dbeafe; color: #2563eb; }
    .gw-bank_transfer { background: #f3f4f6; color: #4b5563; }
    .gw-wallet { background: #e0f2fe; color: #0369a1; }
    .gw-fallback { background: #f1f5f9; color: #64748b; }

    .status-badge-translucent {
        font-size: 0.75rem;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .status-badge-translucent.status-paid {
        background: rgba(16, 185, 129, 0.08);
        color: #10b981;
    }
    .status-badge-translucent.status-pending {
        background: rgba(245, 158, 11, 0.08);
        color: #f59e0b;
    }
    .status-badge-translucent.status-failed {
        background: rgba(239, 68, 68, 0.08);
        color: #ef4444;
    }

    .payment-amount-box {
        font-weight: 850;
        font-size: 1.15rem;
        color: var(--text-main);
        text-align: end;
        line-height: 1;
    }
    .payment-amount-box .currency {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
        margin-inline-start: 2px;
    }

    /* Download Action Buttons */
    .dl-btn-custom {
        padding: 8px 16px;
        border-radius: 10px;
        background: var(--bg-main);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        text-decoration: none !important;
        font-size: 0.82rem;
        font-weight: 750;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .dl-btn-custom:hover {
        background: var(--primary-blue);
        border-color: var(--primary-blue);
        color: #fff;
        transform: translateY(-1px);
    }

    /* ─── Empty State Redesign ─── */
    .empty-payments-state {
        text-align: center;
        padding: 60px 20px;
        background: var(--bg-card);
        border-radius: 20px;
        border: 1px solid var(--border-color);
        color: var(--text-muted);
    }
    .empty-payments-state i {
        font-size: 3.5rem;
        color: var(--primary-blue);
        margin-bottom: 16px;
        opacity: 0.15;
    }
    .empty-payments-state h4 {
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 6px 0;
    }
    .empty-payments-state p {
        font-size: 0.88rem;
        margin: 0 0 16px 0;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@section('content')
<div class="payments-dashboard-wrapper">
    
    {{-- Header Banner --}}
    <div class="payment-header-banner">
        <div>
            <h2>{{ __('Payments & Invoices') }}</h2>
            <p>{{ __('Track your payments, check status, and download invoices.') }}</p>
        </div>
    </div>

    {{-- Stats Cards Row --}}
    <div class="stats-grid">
        <div class="stat-card-custom stat-total">
            <div class="stat-icon-box">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-info-box">
                <label>{{ __('Total Payments') }}</label>
                <span>{{ $stats['total'] }}</span>
            </div>
        </div>
        <div class="stat-card-custom stat-paid">
            <div class="stat-icon-box">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info-box">
                <label>{{ __('Paid') }}</label>
                <span>{{ $stats['paid'] }}</span>
            </div>
        </div>
        <div class="stat-card-custom stat-pending">
            <div class="stat-icon-box">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info-box">
                <label>{{ __('Pending') }}</label>
                <span>{{ $stats['pending'] }}</span>
            </div>
        </div>
        <div class="stat-card-custom stat-failed">
            <div class="stat-icon-box">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info-box">
                <label>{{ __('Failed') }}</label>
                <span>{{ $stats['failed'] }}</span>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <div class="filter-pills">
            <a href="{{ route('customer.payments.index', request()->except(['status', 'page'])) }}" class="pill-btn {{ !request('status') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i> {{ __('All') }}
            </a>
            <a href="{{ route('customer.payments.index', array_merge(request()->except(['page']), ['status' => 'paid'])) }}" class="pill-btn {{ request('status') === 'paid' ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i> {{ __('Paid') }}
            </a>
            <a href="{{ route('customer.payments.index', array_merge(request()->except(['page']), ['status' => 'pending'])) }}" class="pill-btn {{ request('status') === 'pending' ? 'active' : '' }}">
                <i class="fas fa-clock"></i> {{ __('Pending') }}
            </a>
            <a href="{{ route('customer.payments.index', array_merge(request()->except(['page']), ['status' => 'failed'])) }}" class="pill-btn {{ request('status') === 'failed' ? 'active' : '' }}">
                <i class="fas fa-times-circle"></i> {{ __('Failed') }}
            </a>
        </div>
        
        <button class="pill-btn" type="button" onclick="toggleAdvancedFilters()" style="color: var(--text-main); border-color: var(--border-color); background: var(--bg-card);">
            <i class="fas fa-filter"></i> {{ __('Advanced Filters') }}
            @if(request('search') || request('gateway') || request('date_from') || request('date_to'))
                <span style="display: inline-block; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; margin-inline-start: 4px;"></span>
            @endif
        </button>
    </div>

    {{-- Advanced Filters Panel Drawer --}}
    <form action="{{ route('customer.payments.index') }}" method="GET" id="filterForm">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <div class="filters-drawer" id="advancedFilters" style="{{ (request('search') || request('gateway') || request('date_from') || request('date_to')) ? 'display: block;' : 'display: none;' }}">
            <div class="drawer-grid">
                <div class="form-group-custom">
                    <label>{{ __('Search') }}</label>
                    <input type="text" name="search" class="input-field-custom" value="{{ request('search') }}" placeholder="{{ __('Search Transaction ID, reference...') }}">
                </div>
                <div class="form-group-custom">
                    <label>{{ __('Gateway') }}</label>
                    <select name="gateway" class="input-field-custom select2">
                        <option value="">{{ __('All Gateways') }}</option>
                        <option value="card" {{ request('gateway') === 'card' ? 'selected' : '' }}>{{ __('Credit Card') }}</option>
                        <option value="tamara" {{ request('gateway') === 'tamara' ? 'selected' : '' }}>{{ __('Tamara') }}</option>
                        <option value="tabby" {{ request('gateway') === 'tabby' ? 'selected' : '' }}>{{ __('Tabby') }}</option>
                        <option value="bank_transfer" {{ request('gateway') === 'bank_transfer' ? 'selected' : '' }}>{{ __('Bank Transfer') }}</option>
                        <option value="wallet" {{ request('gateway') === 'wallet' ? 'selected' : '' }}>{{ __('Wallet') }}</option>
                    </select>
                </div>
                <div class="form-group-custom" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <div>
                        <label>{{ __('From') }}</label>
                        <input type="date" name="date_from" class="input-field-custom" value="{{ request('date_from') }}">
                    </div>
                    <div>
                        <label>{{ __('To') }}</label>
                        <input type="date" name="date_to" class="input-field-custom" value="{{ request('date_to') }}">
                    </div>
                </div>
            </div>
            <div class="drawer-actions">
                <a href="{{ route('customer.payments.index', request('status') ? ['status' => request('status')] : []) }}" class="btn-sm-custom btn-outline-custom">
                    <i class="fas fa-undo"></i> {{ __('Reset') }}
                </a>
                <button type="submit" class="btn-sm-custom btn-accent-custom">
                    <i class="fas fa-search"></i> {{ __('Apply Filters') }}
                </button>
            </div>
        </div>
    </form>

    {{-- Payments Card Container --}}
    <div class="payments-container">
        @forelse($payments as $payment)
            @php
                $iconClass = 'fa-receipt';
                $catClass = 'cat-fallback';
                $title = __('Booking Payment');
                
                if ($payment->payable instanceof \App\Models\TripBooking) {
                    $iconClass = 'fa-map-marked-alt';
                    $catClass = 'cat-trip';
                    $title = $payment->payable->trip->title ?? __('Trip Booking');
                } elseif ($payment->payable instanceof \App\Models\HotelBooking) {
                    $iconClass = 'fa-hotel';
                    $catClass = 'cat-hotel';
                    $title = $payment->payable->hotel_name ?? __('Hotel Booking');
                } elseif ($payment->payable instanceof \App\Models\Booking) {
                    $iconClass = 'fa-plane';
                    $catClass = 'cat-flight';
                    $title = $payment->payable->airline_name ?? __('Flight Booking');
                }
            @endphp
            <div class="payment-card-row">
                <div class="payment-main-info">
                    <div class="payment-category-icon {{ $catClass }}">
                        <i class="fas {{ $iconClass }}"></i>
                    </div>
                    <div class="payment-meta-details">
                        <div class="payment-title-wrapper">
                            @if($payment->payable_id)
                                <span class="payment-id-badge">#{{ $payment->payable_id }}</span>
                            @endif
                            <span class="payment-title">{{ $title }}</span>
                        </div>
                        <div class="payment-meta-row">
                            <span class="gateway-tag gw-{{ strtolower($payment->payment_gateway ?? 'fallback') }}">
                                {{ strtoupper($payment->payment_gateway) }}
                            </span>
                            @if($payment->transaction_id)
                                <span class="bullet-sep">·</span>
                                <span style="font-family: monospace; font-weight: 600;">Ref: #{{ $payment->transaction_id }}</span>
                            @endif
                            <span class="bullet-sep">·</span>
                            <span>
                                <i class="far fa-clock"></i> {{ $payment->created_at->format('Y-m-d H:i') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 20px;">
                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px;">
                        <div class="payment-amount-box">
                            {{ number_format($payment->amount, 2) }}
                            <span class="currency">{{ __('SAR') }}</span>
                        </div>
                        <span class="status-badge-translucent status-{{ $payment->status === 'paid' ? 'paid' : ($payment->status === 'pending' ? 'pending' : 'failed') }}">
                            @if($payment->status === 'paid') <i class="fas fa-check-circle"></i> {{ __('Paid') }}
                            @elseif($payment->status === 'pending') <i class="fas fa-clock"></i> {{ __('Pending') }}
                            @else <i class="fas fa-times-circle"></i> {{ __('Failed') }}
                            @endif
                        </span>
                    </div>

                    <div style="display: flex; align-items: center;">
                        @if($payment->status === 'paid')
                            @if($payment->payable instanceof \App\Models\TripBooking)
                                <a href="{{ route('customer.bookings.invoice', $payment->payable_id) }}" class="dl-btn-custom" title="{{ __('Download Invoice') }}">
                                    <i class="fas fa-file-pdf"></i> {{ __('Invoice') }}
                                </a>
                            @elseif($payment->payable instanceof \App\Models\HotelBooking)
                                <a href="{{ route('customer.bookings.hotels.voucher', $payment->payable_id) }}" class="dl-btn-custom" title="{{ __('Download Voucher') }}">
                                    <i class="fas fa-file-pdf"></i> {{ __('Voucher') }}
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-payments-state">
                <i class="fas fa-receipt"></i>
                <h4>{{ __('No payments found.') }}</h4>
                <p>{{ __('Try adjusting your filter settings or clear all filters to view full list.') }}</p>
                @if(request('search') || request('gateway') || request('date_from') || request('date_to') || request('status'))
                    <a href="{{ route('customer.payments.index') }}" class="btn-sm-custom btn-accent-custom" style="width: auto; display: inline-flex;">
                        <i class="fas fa-undo"></i> {{ __('Reset All Filters') }}
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($payments->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $payments->appends(request()->input())->links() }}
        </div>
    @endif

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
