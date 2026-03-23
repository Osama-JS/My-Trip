@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Notifications'))
@section('page-title', __('Notifications'))

@push('styles')
<style>
    .notif-container {
        max-width: 900px;
        margin: 0 auto;
    }
    .notif-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        overflow: hidden;
        border: 1px solid #f1f5f9;
        margin-bottom: 30px;
    }
    .notif-header {
        padding: 24px 30px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fafbfc;
    }
    .notif-header h3 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
    }
    .btn-mark-all {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--primary-blue, #2563eb);
        background: rgba(37, 99, 235, 0.08);
        border: none;
        padding: 10px 18px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-mark-all:hover {
        background: rgba(37, 99, 235, 0.15);
        transform: translateY(-1px);
    }
    .notif-item {
        display: flex;
        gap: 20px;
        padding: 24px 30px;
        border-bottom: 1px solid #f8fafc;
        transition: all 0.2s;
        text-decoration: none;
        color: inherit;
        position: relative;
    }
    .notif-item:last-child { border-bottom: none; }
    .notif-item:hover { background: #fcfdfe; }
    .notif-item.unread { background: #fcf8f7; }
    .notif-item.unread::before {
        content: '';
        position: absolute;
        inset-inline-start: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #e8532e;
    }
    .notif-icon-box {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .icon-booking { background: #e0f2fe; color: #0369a1; }
    .icon-payment { background: #dcfce7; color: #15803d; }
    .icon-cancel { background: #fee2e2; color: #b91c1c; }
    .icon-general { background: #f1f5f9; color: #475569; }

    .notif-body { flex: 1; min-width: 0; }
    .notif-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 6px;
    }
    .notif-type-tag {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 4px 10px;
        border-radius: 6px;
        background: rgba(0,0,0,0.05);
        color: #64748b;
    }
    .notif-time {
        font-size: 0.8rem;
        color: #94a3b8;
    }
    .notif-title {
        font-weight: 700;
        font-size: 1.05rem;
        color: #111827;
        margin-bottom: 6px;
    }
    .notif-content-text {
        font-size: 0.95rem;
        color: #64748b;
        line-height: 1.6;
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
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-read-toggle:hover {
        border-color: #2563eb;
        color: #2563eb;
        background: #f0f7ff;
    }

    .empty-notif {
        padding: 80px 40px;
        text-align: center;
    }
    .empty-notif i {
        font-size: 5rem;
        color: #f1f5f9;
        margin-bottom: 24px;
        display: block;
    }
    .empty-notif h4 {
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 8px;
    }
    .empty-notif p {
        color: #94a3b8;
    }

    /* Dark Mode Overrides */
    body.dark-mode .notif-card { background: #1e293b; border-color: #334155; }
    body.dark-mode .notif-header { background: #243049; border-color: #334155; }
    body.dark-mode .notif-header h3 { color: #f8fafc; }
    body.dark-mode .notif-item { border-color: #334155; }
    body.dark-mode .notif-item:hover { background: #26344d; }
    body.dark-mode .notif-item.unread { background: #2d2a29; }
    body.dark-mode .notif-title { color: #f8fafc; }
    body.dark-mode .notif-content-text { color: #cbd5e1; }
</style>
@endpush

@section('content')
<div class="notif-container">
    <div class="notif-card">
        <div class="notif-header">
            <h3>{{ __('All Notifications') }}</h3>
            @if(auth()->user()->unreadNotifications()->count() > 0)
                <form action="{{ route('customer.notifications.read-all') }}" method="POST">
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
                @endphp
                <div class="notif-item {{ $isUnread ? 'unread' : '' }}">
                    <div class="notif-icon-box {{ $iconClass }}">
                        <i class="fas {{ $icon }}"></i>
                    </div>
                    <div class="notif-body">
                        <div class="notif-meta">
                            <span class="notif-type-tag">{{ __($notif->type) }}</span>
                            <span class="notif-time">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="notif-title">{{ $notif->title }}</div>
                        <div class="notif-content-text">{{ $notif->content }}</div>
                    </div>
                    @if($isUnread)
                        <div class="notif-actions">
                            <form action="{{ route('customer.notifications.read', $notif->id) }}" method="POST">
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
                    <i class="fas fa-bell-slash"></i>
                    <h4>{{ __('No Notifications Yet') }}</h4>
                    <p>{{ __('When you receive updates about your bookings, they will appear here.') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    @if($notifications->hasPages())
        <div class="d-flex justify-content-center">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
