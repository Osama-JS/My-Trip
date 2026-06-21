@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Notifications'))
@section('page-title', __('Notifications'))

@push('styles')
<style>
    .notif-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    /* ─── Filter Pills ─── */
    .filter-pills-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .filter-pill {
        padding: 8px 18px;
        border-radius: 30px;
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        color: var(--text-muted);
        font-size: .85rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        outline: none !important;
    }
    .filter-pill:hover {
        color: var(--primary-blue);
        border-color: var(--primary-blue);
        background: rgba(37, 99, 235, 0.02);
    }
    .filter-pill.active {
        background: var(--primary-blue);
        border-color: var(--primary-blue);
        color: #fff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.18);
    }
    .pill-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ef4444;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 800;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        margin-inline-start: 2px;
    }
    .filter-pill.active .pill-badge {
        background: #fff;
        color: var(--primary-blue);
    }

    /* ─── Card Design ─── */
    .notif-card {
        background: var(--bg-card);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.015);
        overflow: hidden;
        border: 1px solid var(--border-color);
        margin-bottom: 30px;
    }
    .notif-header {
        padding: 24px 30px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(0, 0, 0, 0.005);
    }
    .notif-header h3 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 850;
        color: var(--text-main);
    }
    .btn-mark-all {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--primary-blue, #2563eb);
        background: rgba(37, 99, 235, 0.08);
        border: none;
        padding: 10px 18px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-mark-all:hover {
        background: rgba(37, 99, 235, 0.15);
        transform: translateY(-1px);
    }

    /* ─── Notification Items Stream ─── */
    .notif-item {
        display: flex;
        gap: 20px;
        padding: 24px 30px;
        border-bottom: 1px solid var(--border-color);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
        color: inherit;
        position: relative;
        transform-origin: center top;
    }
    .notif-item:last-child { border-bottom: none; }
    .notif-item:hover { background: var(--bg-main); }
    .notif-item.unread { background: rgba(37, 99, 235, 0.015); }
    .notif-item.unread::before {
        content: '';
        position: absolute;
        inset-inline-start: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--primary-blue, #2563eb);
    }

    /* Translucent badge rings */
    .notif-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
        box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.015);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .notif-item:hover .notif-icon-box {
        transform: scale(1.05);
    }
    .icon-booking {
        background: rgba(3, 105, 161, 0.06);
        color: #0284c7;
        box-shadow: 0 0 0 4px rgba(3, 105, 161, 0.03);
    }
    .icon-payment {
        background: rgba(21, 128, 61, 0.06);
        color: #16a34a;
        box-shadow: 0 0 0 4px rgba(21, 128, 61, 0.03);
    }
    .icon-cancel {
        background: rgba(220, 38, 38, 0.06);
        color: #dc2626;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.03);
    }
    .icon-general {
        background: var(--bg-main);
        color: var(--text-muted);
        box-shadow: 0 0 0 4px var(--border-color);
    }
 
    .notif-body { flex: 1; min-width: 0; }
    .notif-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 6px;
    }
    .notif-type-tag {
        font-size: 0.68rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 4px 10px;
        border-radius: 6px;
        background: var(--bg-main);
        color: var(--text-muted);
        border: 1px solid var(--border-color);
    }
    .notif-time {
        font-size: 0.78rem;
        color: var(--text-muted);
        font-weight: 600;
    }
    .notif-title {
        font-weight: 850;
        font-size: 1.05rem;
        color: var(--text-main);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
    }
    .notif-content-text {
        font-size: 0.92rem;
        color: var(--text-muted);
        line-height: 1.6;
        font-weight: 500;
    }
    .notif-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }
    .btn-read-toggle {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-read-toggle:hover {
        border-color: var(--primary-blue);
        color: #fff;
        background: var(--primary-blue);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
    }

    /* Pulsing halos */
    .pulsing-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: var(--primary-blue, #2563eb);
        border-radius: 50%;
        animation: pulse 1.5s infinite;
        margin-inline-start: 8px;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4); }
        70% { box-shadow: 0 0 0 6px rgba(37, 99, 235, 0); }
        100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
    }

    /* ─── Empty States & Radar Sonar ─── */
    .empty-notif {
        padding: 80px 40px;
        text-align: center;
    }
    .sonar-wrapper {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .sonar-emitter {
        width: 64px;
        height: 64px;
        background: rgba(37, 99, 235, 0.05);
        color: var(--primary-blue);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        position: relative;
        z-index: 2;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.05);
    }
    .sonar-wave {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: var(--primary-blue);
        opacity: 0.15;
        z-index: 1;
        animation: sonar 2s infinite linear;
    }
    .sonar-wave-2 {
        animation-delay: 1s;
    }
    @keyframes sonar {
        0% { transform: scale(0.6); opacity: 0.3; }
        100% { transform: scale(1.3); opacity: 0; }
    }

    .empty-notif h4 {
        font-weight: 900;
        color: var(--text-main);
        margin-bottom: 8px;
        font-size: 1.25rem;
    }
    .empty-notif p {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="notif-container">
    
    {{-- Dynamic Filter Pills --}}
    <div class="filter-pills-row mb-4">
        <button class="filter-pill active" onclick="filterNotifications('all', this)">{{ __('All') }}</button>
        <button class="filter-pill" onclick="filterNotifications('unread', this)">
            {{ __('Unread') }}
            @if(auth()->user()->unreadNotifications()->count() > 0)
                <span class="pill-badge">{{ auth()->user()->unreadNotifications()->count() }}</span>
            @endif
        </button>
        <button class="filter-pill" onclick="filterNotifications('booking', this)">{{ __('Bookings') }}</button>
        <button class="filter-pill" onclick="filterNotifications('payment', this)">{{ __('Payments') }}</button>
    </div>

    <div class="notif-card">
        <div class="notif-header">
            <h3>{{ __('All Notifications') }}</h3>
            @if(auth()->user()->unreadNotifications()->count() > 0)
                <form action="{{ route('customer.notifications.read-all') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-mark-all">
                        <i class="fas fa-check-double"></i> {{ __('Mark all as read') }}
                    </button>
                </form>
            @endif
        </div>

        <div class="notif-list-body">
            @forelse($notifications as $notif)
                @php
                    $isUnread = $notif->unread();
                    $iconClass = 'icon-general';
                    $icon = 'fa-bell';
                    
                    if ($notif->type === 'booking_confirmed') { $iconClass = 'icon-booking'; $icon = 'fa-check-circle'; }
                    elseif ($notif->type === 'booking_cancelled') { $iconClass = 'icon-cancel'; $icon = 'fa-times-circle'; }
                    elseif ($notif->type === 'payment_success') { $iconClass = 'icon-payment'; $icon = 'fa-credit-card'; }
                    elseif ($notif->type === 'payment_failed') { $iconClass = 'icon-cancel'; $icon = 'fa-exclamation-triangle'; }

                    $category = 'general';
                    if (in_array($notif->type, ['booking_confirmed', 'booking_cancelled'])) {
                        $category = 'booking';
                    } elseif (in_array($notif->type, ['payment_success', 'payment_failed'])) {
                        $category = 'payment';
                    }
                @endphp
                <div class="notif-item {{ $isUnread ? 'unread' : '' }}" data-status="{{ $isUnread ? 'unread' : 'read' }}" data-category="{{ $category }}">
                    <div class="notif-icon-box {{ $iconClass }}">
                        <i class="fas {{ $icon }}"></i>
                    </div>
                    <div class="notif-body">
                        <div class="notif-meta">
                            <span class="notif-type-tag">{{ __($notif->type) }}</span>
                            <span class="notif-time">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="notif-title">
                            {{ $notif->title }}
                            @if($isUnread)
                                <span class="pulsing-dot" title="{{ __('New Notification') }}"></span>
                            @endif
                        </div>
                        <div class="notif-content-text">{{ $notif->content }}</div>
                    </div>
                    @if($isUnread)
                        <div class="notif-actions">
                            <form action="{{ route('customer.notifications.read', $notif->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="btn-read-toggle" title="{{ __('Mark as read') }}">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="empty-notif">
                    <div class="sonar-wrapper">
                        <div class="sonar-wave"></div>
                        <div class="sonar-wave sonar-wave-2"></div>
                        <div class="sonar-emitter"><i class="fas fa-bell-slash"></i></div>
                    </div>
                    <h4>{{ __('No Notifications Yet') }}</h4>
                    <p>{{ __('When you receive updates about your bookings, they will appear here.') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    @if($notifications->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
function filterNotifications(filter, element) {
    document.querySelectorAll('.filter-pill').forEach(btn => btn.classList.remove('active'));
    element.classList.add('active');

    const items = document.querySelectorAll('.notif-item');
    let visibleCount = 0;

    items.forEach(item => {
        item.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
        
        let match = false;
        if (filter === 'all') {
            match = true;
        } else if (filter === 'unread') {
            match = item.getAttribute('data-status') === 'unread';
        } else {
            match = item.getAttribute('data-category') === filter;
        }

        if (match) {
            item.style.display = 'flex';
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'scale(1)';
            }, 10);
            visibleCount++;
        } else {
            item.style.opacity = '0';
            item.style.transform = 'scale(0.96)';
            setTimeout(() => {
                item.style.display = 'none';
            }, 300);
        }
    });

    const emptyState = document.getElementById('noFilterResults');
    const mainList = document.querySelector('.notif-list-body');
    const dbEmptyState = document.querySelector('.empty-notif:not(#noFilterResults)');

    if (dbEmptyState) {
        return; // DB itself is empty
    }

    if (visibleCount === 0) {
        if (!emptyState) {
            const noResultsHTML = `
                <div class="empty-notif" id="noFilterResults" style="animation: fadeInUp 0.4s ease forwards; padding: 60px 40px;">
                    <div class="sonar-wrapper" style="width: 80px; height: 80px;">
                        <div class="sonar-wave"></div>
                        <div class="sonar-wave sonar-wave-2"></div>
                        <div class="sonar-emitter" style="width: 54px; height: 54px; font-size: 1.5rem;"><i class="fas fa-bell-slash"></i></div>
                    </div>
                    <h4>{{ __('No match found') }}</h4>
                    <p>{{ __('Try selecting a different filter option.') }}</p>
                </div>
            `;
            mainList.insertAdjacentHTML('beforeend', noResultsHTML);
        } else {
            emptyState.style.display = 'block';
        }
    } else {
        if (emptyState) {
            emptyState.remove();
        }
    }
}
</script>
@endpush

@endsection
