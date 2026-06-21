@extends('frontend.customer.layouts.customer-layout')

@section('title', __('My Wallet'))
@section('page-title', __('My Wallet'))

@push('styles')
<style>
    /* ─── Premium Card ─── */
    .premium-card {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
        border-radius: 24px;
        padding: 30px;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(49, 46, 129, 0.25);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 240px;
        border: 1px solid rgba(255,255,255,0.08);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
        max-width: 420px;
        margin-bottom: 28px;
    }
    .premium-card:hover {
        transform: translateY(-6px) rotate(-1deg);
        box-shadow: 0 25px 45px rgba(49, 46, 129, 0.35);
    }
    .premium-card::after {
        content: '';
        position: absolute;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
        bottom: -80px;
        right: -80px;
        pointer-events: none;
    }
    [dir="rtl"] .premium-card::after {
        right: auto;
        left: -80px;
    }
    
    .card-logo {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 2px;
        opacity: 0.85;
    }
    .card-logo i {
        font-size: 1.1rem;
    }
    .card-chip {
        width: 44px;
        height: 32px;
        background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
        border-radius: 6px;
        position: relative;
        padding: 4px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2px;
        box-shadow: inset 0 1px 2px rgba(255,255,255,0.3);
        margin: 10px 0;
    }
    .chip-line {
        border: 1px solid rgba(0,0,0,0.15);
        border-radius: 2px;
    }
    .card-balance label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.7;
        display: block;
        margin-bottom: 4px;
        font-weight: 700;
    }
    .card-balance h3 {
        font-size: 2.1rem;
        font-weight: 950;
        margin: 0;
        line-height: 1;
        letter-spacing: -0.5px;
    }
    .card-balance h3 span {
        font-size: 1.1rem;
        font-weight: 700;
        opacity: 0.85;
        margin-inline-start: 4px;
    }
    .card-number {
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: 3px;
        margin: 12px 0;
        font-family: monospace;
        opacity: 0.9;
    }
    .card-footer-info {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .info-group label {
        font-size: 0.58rem;
        text-transform: uppercase;
        opacity: 0.6;
        display: block;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .info-group span {
        font-size: 0.82rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.015);
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
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .filter-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--border-color);
    }
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

    /* ─── Detail Cards ─── */
    .detail-card {
        background: var(--bg-card);
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.015);
        overflow: hidden;
    }
    .detail-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
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
    }

    /* ─── Modern Tables ─── */
    .table-modern {
        width: 100%;
        border-collapse: collapse;
        text-align: start;
    }
    .table-modern th {
        background: rgba(0,0,0,0.005);
        color: var(--text-muted);
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-color);
    }
    .table-modern td {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-main);
        font-size: 0.9rem;
    }
    .table-modern tr:last-child td {
        border-bottom: none;
    }
    .table-modern tr {
        transition: background-color 0.25s ease;
    }
    .table-modern tr:hover {
        background: var(--bg-main);
    }

    /* Transaction Amount Badges */
    .amount-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 800;
    }
    .amount-badge.credit {
        background: rgba(16, 185, 129, 0.08);
        color: #10b981;
    }
    .amount-badge.debit {
        background: rgba(239, 68, 68, 0.08);
        color: #ef4444;
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
<div class="wallet-dashboard-wrapper">
    
    {{-- Wallet Card display --}}
    <div class="premium-card">
        <div class="card-logo">
            <i class="fas fa-globe-americas"></i>
            <span>{{ __('TRAVELER PASS') }}</span>
        </div>
        <div class="card-chip">
            <div class="chip-line"></div>
            <div class="chip-line"></div>
            <div class="chip-line"></div>
            <div class="chip-line"></div>
        </div>
        <div class="card-balance">
            <label>{{ __('Current Balance') }}</label>
            <h3>
                {{ number_format($wallet->balance, 2) }} 
                <span>{{ $wallet->currency }}</span>
            </h3>
        </div>
        <div class="card-number">
            •••• •••• •••• {{ str_pad(auth()->id(), 4, '0', STR_PAD_LEFT) }}
        </div>
        <div class="card-footer-info">
            <div class="info-group">
                <label>{{ __('CARD HOLDER') }}</label>
                <span>{{ auth()->user()->full_name }}</span>
            </div>
            <div class="info-group text-end">
                <label>{{ __('VALID THRU') }}</label>
                <span>12/30</span>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('customer.wallet.index', request()->except(['type', 'page'])) }}" class="filter-btn {{ !request('type') ? 'active' : '' }}">
                {{ __('All') }}
            </a>
            <a href="{{ route('customer.wallet.index', array_merge(request()->except(['page']), ['type' => 'credit'])) }}" class="filter-btn {{ request('type') === 'credit' ? 'active' : '' }}">
                <i class="fas fa-arrow-trend-up"></i> {{ __('Credits') }}
            </a>
            <a href="{{ route('customer.wallet.index', array_merge(request()->except(['page']), ['type' => 'debit'])) }}" class="filter-btn {{ request('type') === 'debit' ? 'active' : '' }}">
                <i class="fas fa-arrow-trend-down"></i> {{ __('Debits') }}
            </a>
        </div>
        <button class="filter-btn" type="button" onclick="toggleAdvancedFilters()" style="color: var(--text-main); border-color: var(--border-color); background: var(--bg-card);">
            <i class="fas fa-filter"></i> {{ __('Advanced Filters') }}
            @if(request('search') || request('date_from') || request('date_to'))
                <span style="display: inline-block; width: 8px; height: 8px; background: #ef4444; border-radius: 50%;"></span>
            @endif
        </button>
    </div>

    {{-- Advanced Filters Panel --}}
    <form action="{{ route('customer.wallet.index') }}" method="GET" id="filterForm">
        @if(request('type'))
            <input type="hidden" name="type" value="{{ request('type') }}">
        @endif

        <div class="advanced-filters-panel" id="advancedFilters" style="{{ (request('search') || request('date_from') || request('date_to')) ? 'display: block;' : 'display: none;' }}">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">{{ __('Search') }}</label>
                    <input type="text" name="search" class="filter-input" value="{{ request('search') }}" placeholder="{{ __('Search by description, reference...') }}">
                </div>
                <div class="filter-group">
                    <label class="filter-label">{{ __('From Date') }}</label>
                    <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
                </div>
                <div class="filter-group">
                    <label class="filter-label">{{ __('To Date') }}</label>
                    <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="filter-actions">
                <a href="{{ route('customer.wallet.index', request('type') ? ['type' => request('type')] : []) }}" class="btn-sm btn-outline" style="width: auto;">
                    <i class="fas fa-undo"></i> {{ __('Reset') }}
                </a>
                <button type="submit" class="btn-sm btn-accent" style="width: auto;">
                    <i class="fas fa-search"></i> {{ __('Apply Filters') }}
                </button>
            </div>
        </div>
    </form>

    {{-- Transactions History --}}
    <div class="detail-card">
        <div class="detail-card-header">
            <h5><i class="fas fa-history"></i> {{ __('Transaction History') }}</h5>
        </div>
        <div class="detail-card-body p-0">
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr>
                                <td style="color: var(--text-muted); font-weight: 600;">
                                    {{ $tx->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td>
                                    <strong style="color: var(--text-main); font-weight: 750;">{{ $tx->description }}</strong>
                                    @if($tx->reference_id)
                                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; margin-top: 2px;">
                                            Ref: #{{ $tx->reference_id }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($tx->type == 'credit')
                                        <span class="amount-badge credit">
                                            <i class="fas fa-arrow-trend-up"></i> +{{ number_format($tx->amount, 2) }}
                                        </span>
                                    @else
                                        <span class="amount-badge debit">
                                            <i class="fas fa-arrow-trend-down"></i> -{{ number_format($tx->amount, 2) }}
                                        </span>
                                    @endif
                                </td>
                                <td style="font-weight: 750;">
                                    {{ number_format($tx->balance_after, 2) }} {{ $wallet->currency }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5" style="color: var(--text-muted);">
                                    <i class="fas fa-search mb-3" style="font-size: 3rem; opacity: 0.15;"></i>
                                    <p class="m-0" style="font-weight: 700;">{{ __('No matching transactions found.') }}</p>
                                    <p class="m-0 text-xs text-muted" style="margin-top: 4px;">{{ __('Try clearing some of the filters to view full list.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
            <div class="card-footer bg-transparent border-top p-4 d-flex justify-content-center">
                {{ $transactions->appends(request()->input())->links() }}
            </div>
        @endif
    </div>

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
