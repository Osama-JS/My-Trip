@extends('frontend.customer.layouts.customer-layout')

@section('title', __('My Support Tickets'))
@section('page-title', __('My Support Tickets'))

@push('styles')
<style>
    /* ─── Premium Header ─── */
    .support-header-banner {
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
    .support-header-banner h2 {
        margin: 0;
        font-weight: 900;
        font-size: 1.8rem;
        letter-spacing: -0.5px;
    }
    .support-header-banner p {
        margin: 6px 0 0;
        color: #94a3b8;
        font-weight: 500;
        font-size: 1rem;
    }
    .btn-new-ticket {
        background: var(--primary-blue);
        color: #fff;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 750;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 14 rgba(37, 99, 235, 0.25);
    }
    .btn-new-ticket:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
        color: #fff;
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

    /* Color configurations for stats */
    .stat-total .stat-icon-box { background: rgba(37, 99, 235, 0.08); color: var(--primary-blue); }
    .stat-open .stat-icon-box { background: rgba(16, 185, 129, 0.08); color: #10b981; }
    .stat-pending .stat-icon-box { background: rgba(245, 158, 11, 0.08); color: #f59e0b; }
    .stat-closed .stat-icon-box { background: rgba(100, 116, 139, 0.08); color: #64748b; }

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
        grid-template-columns: 2fr 1fr 1fr 1.5fr;
        gap: 16px;
    }
    @media (max-width: 991px) {
        .drawer-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 576px) {
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

    /* ─── Tickets Card List ─── */
    .tickets-container {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .ticket-card-row {
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
    .ticket-card-row:hover {
        transform: translateY(-2px);
        border-color: var(--primary-blue);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.04);
    }
    .ticket-main-info {
        display: flex;
        align-items: center;
        gap: 20px;
        flex: 1;
        min-width: 0;
    }
    
    /* Category Icon Box */
    .ticket-category-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .cat-technical { background: rgba(249, 115, 22, 0.08); color: #ea580c; }
    .cat-financial { background: rgba(234, 179, 8, 0.08); color: #d97706; }
    .cat-booking { background: rgba(59, 130, 246, 0.08); color: #2563eb; }
    .cat-general { background: rgba(139, 92, 246, 0.08); color: #8b5cf6; }

    .ticket-meta-details {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 0;
        flex: 1;
    }
    .ticket-subject-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .ticket-subject {
        font-weight: 800;
        font-size: 1rem;
        color: var(--text-main);
        text-decoration: none !important;
        line-height: 1.3;
        transition: color 0.2s;
    }
    .ticket-subject:hover {
        color: var(--primary-blue);
    }
    .ticket-id {
        font-size: 0.72rem;
        font-weight: 800;
        background: var(--bg-main);
        color: var(--text-muted);
        padding: 2px 8px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
    }
    .ticket-snippet {
        font-size: 0.82rem;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 480px;
    }

    /* Badges & Indicators */
    .ticket-badges-row {
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
    
    .priority-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 750;
        font-size: 0.78rem;
    }
    .priority-dot-glow {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    .prio-urgent { color: #ef4444; }
    .prio-urgent .priority-dot-glow { background: #ef4444; box-shadow: 0 0 6px #ef4444; }
    .prio-high { color: #f97316; }
    .prio-high .priority-dot-glow { background: #f97316; }
    .prio-medium { color: #3b82f6; }
    .prio-medium .priority-dot-glow { background: #3b82f6; }
    .prio-low { color: #10b981; }
    .prio-low .priority-dot-glow { background: #10b981; }

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
    .status-badge-translucent.status-open {
        background: rgba(16, 185, 129, 0.08);
        color: #10b981;
    }
    .status-badge-translucent.status-pending {
        background: rgba(245, 158, 11, 0.08);
        color: #f59e0b;
    }
    .status-badge-translucent.status-closed {
        background: rgba(100, 116, 139, 0.08);
        color: #64748b;
    }

    /* Actions */
    .ticket-actions-area {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .btn-circle-chevron {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--bg-main);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
    }
    .ticket-card-row:hover .btn-circle-chevron {
        background: var(--primary-blue);
        border-color: var(--primary-blue);
        color: #fff;
        transform: scale(1.05);
    }
    
    [dir="rtl"] .btn-circle-chevron i {
        transform: rotate(180deg);
    }

    /* ─── Empty State Redesign ─── */
    .empty-tickets-state {
        text-align: center;
        padding: 60px 20px;
        background: var(--bg-card);
        border-radius: 20px;
        border: 1px solid var(--border-color);
        color: var(--text-muted);
    }
    .empty-tickets-state i {
        font-size: 3.5rem;
        color: var(--primary-blue);
        margin-bottom: 16px;
        opacity: 0.15;
    }
    .empty-tickets-state h4 {
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 6px 0;
    }
    .empty-tickets-state p {
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
<div class="support-center-wrapper">
    
    {{-- Header Banner --}}
    <div class="support-header-banner">
        <div>
            <h2>{{ __('Support Center') }}</h2>
            <p>{{ __('How can we help you today?') }}</p>
        </div>
        <a href="{{ route('customer.support.create') }}" class="btn-new-ticket">
            <i class="fas fa-plus"></i> {{ __('New Ticket') }}
        </a>
    </div>

    {{-- Stats Cards Row --}}
    <div class="stats-grid">
        <div class="stat-card-custom stat-total">
            <div class="stat-icon-box">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="stat-info-box">
                <label>{{ __('Total Tickets') }}</label>
                <span>{{ $stats['total'] }}</span>
            </div>
        </div>
        <div class="stat-card-custom stat-open">
            <div class="stat-icon-box">
                <i class="fas fa-envelope-open-text"></i>
            </div>
            <div class="stat-info-box">
                <label>{{ __('Open') }}</label>
                <span>{{ $stats['open'] }}</span>
            </div>
        </div>
        <div class="stat-card-custom stat-pending">
            <div class="stat-icon-box">
                <i class="fas fa-history"></i>
            </div>
            <div class="stat-info-box">
                <label>{{ __('Pending') }}</label>
                <span>{{ $stats['pending'] }}</span>
            </div>
        </div>
        <div class="stat-card-custom stat-closed">
            <div class="stat-icon-box">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info-box">
                <label>{{ __('Closed') }}</label>
                <span>{{ $stats['closed'] }}</span>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <div class="filter-pills">
            <a href="{{ route('customer.support.index', request()->except(['status', 'page'])) }}" class="pill-btn {{ !request('status') ? 'active' : '' }}">
                <i class="fas fa-ticket-alt"></i> {{ __('All') }}
            </a>
            <a href="{{ route('customer.support.index', array_merge(request()->except(['page']), ['status' => 'open'])) }}" class="pill-btn {{ request('status') === 'open' ? 'active' : '' }}">
                <i class="fas fa-envelope-open-text"></i> {{ __('Open') }}
            </a>
            <a href="{{ route('customer.support.index', array_merge(request()->except(['page']), ['status' => 'pending'])) }}" class="pill-btn {{ request('status') === 'pending' ? 'active' : '' }}">
                <i class="fas fa-history"></i> {{ __('Pending') }}
            </a>
            <a href="{{ route('customer.support.index', array_merge(request()->except(['page']), ['status' => 'closed'])) }}" class="pill-btn {{ request('status') === 'closed' ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i> {{ __('Closed') }}
            </a>
        </div>
        
        <button class="pill-btn" type="button" onclick="toggleAdvancedFilters()" style="color: var(--text-main); border-color: var(--border-color); background: var(--bg-card);">
            <i class="fas fa-filter"></i> {{ __('Advanced Filters') }}
            @if(request('search') || request('category') || request('priority') || request('date_from') || request('date_to'))
                <span style="display: inline-block; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; margin-inline-start: 4px;"></span>
            @endif
        </button>
    </div>

    {{-- Advanced Filters Panel Drawer --}}
    <form action="{{ route('customer.support.index') }}" method="GET" id="filterForm">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <div class="filters-drawer" id="advancedFilters" style="{{ (request('search') || request('category') || request('priority') || request('date_from') || request('date_to')) ? 'display: block;' : 'display: none;' }}">
            <div class="drawer-grid">
                <div class="form-group-custom">
                    <label>{{ __('Search') }}</label>
                    <input type="text" name="search" class="input-field-custom" value="{{ request('search') }}" placeholder="{{ __('Search by subject or messages...') }}">
                </div>
                <div class="form-group-custom">
                    <label>{{ __('Category') }}</label>
                    <select name="category" class="input-field-custom select2">
                        <option value="">{{ __('All Categories') }}</option>
                        <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>{{ __('Technical') }}</option>
                        <option value="financial" {{ request('category') === 'financial' ? 'selected' : '' }}>{{ __('Financial') }}</option>
                        <option value="booking" {{ request('category') === 'booking' ? 'selected' : '' }}>{{ __('Booking') }}</option>
                        <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>{{ __('General') }}</option>
                    </select>
                </div>
                <div class="form-group-custom">
                    <label>{{ __('Priority') }}</label>
                    <select name="priority" class="input-field-custom select2">
                        <option value="">{{ __('All Priorities') }}</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>{{ __('Urgent') }}</option>
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
                <a href="{{ route('customer.support.index', request('status') ? ['status' => request('status')] : []) }}" class="btn-sm-custom btn-outline-custom">
                    <i class="fas fa-undo"></i> {{ __('Reset') }}
                </a>
                <button type="submit" class="btn-sm-custom btn-accent-custom">
                    <i class="fas fa-search"></i> {{ __('Apply Filters') }}
                </button>
            </div>
        </div>
    </form>

    {{-- Tickets Card Grid List --}}
    <div class="tickets-container">
        @forelse($tickets as $ticket)
            <div class="ticket-card-row">
                <div class="ticket-main-info">
                    @php
                        $iconClass = 'fa-folder-open';
                        $catClass = 'cat-general';
                        if ($ticket->category === 'technical') {
                            $iconClass = 'fa-tools';
                            $catClass = 'cat-technical';
                        } elseif ($ticket->category === 'financial') {
                            $iconClass = 'fa-wallet';
                            $catClass = 'cat-financial';
                        } elseif ($ticket->category === 'booking') {
                            $iconClass = 'fa-plane';
                            $catClass = 'cat-booking';
                        }
                    @endphp
                    <div class="ticket-category-icon {{ $catClass }}">
                        <i class="fas {{ $iconClass }}"></i>
                    </div>
                    <div class="ticket-meta-details">
                        <div class="ticket-subject-wrapper">
                            <span class="ticket-id">#{{ $ticket->id }}</span>
                            <a href="{{ route('customer.support.show', $ticket->id) }}" class="ticket-subject">
                                {{ $ticket->subject }}
                            </a>
                        </div>
                        @if($ticket->messages->first())
                            <div class="ticket-snippet">
                                {{ Str::limit($ticket->messages->first()->message, 90) }}
                            </div>
                        @endif
                        <div class="ticket-badges-row">
                            <span class="priority-pill prio-{{ $ticket->priority }}">
                                <span class="priority-dot-glow"></span>
                                {{ __(ucfirst($ticket->priority)) }}
                            </span>
                            <span class="bullet-sep">·</span>
                            <span>
                                <i class="far fa-folder-open"></i> {{ __(ucfirst($ticket->category)) }}
                            </span>
                            <span class="bullet-sep">·</span>
                            <span>
                                <i class="far fa-clock"></i> {{ $ticket->updated_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="ticket-actions-area">
                    <span class="status-badge-translucent status-{{ $ticket->status }}">
                        {{ __(ucfirst($ticket->status)) }}
                    </span>
                    <a href="{{ route('customer.support.show', $ticket->id) }}" class="btn-circle-chevron" title="{{ __('View Details') }}">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-tickets-state">
                <i class="fas fa-headset"></i>
                <h4>{{ __('No tickets found.') }}</h4>
                <p>{{ __('Try adjusting your filter settings or create a new support ticket to get help.') }}</p>
                @if(request('search') || request('category') || request('priority') || request('date_from') || request('date_to') || request('status'))
                    <a href="{{ route('customer.support.index') }}" class="btn-sm-custom btn-accent-custom" style="width: auto; display: inline-flex;">
                        <i class="fas fa-undo"></i> {{ __('Reset All Filters') }}
                    </a>
                @else
                    <a href="{{ route('customer.support.create') }}" class="btn-sm-custom btn-accent-custom" style="width: auto; display: inline-flex;">
                        <i class="fas fa-plus"></i> {{ __('Create Your First Ticket') }}
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($tickets->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $tickets->appends(request()->input())->links() }}
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
