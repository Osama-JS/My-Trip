<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('لوحة التحكم')) - {{ config('app.name') }}</title>

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/frontend/app.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/frontend/components.css') }}?v={{ time() }}">
    <link href="{{ asset('icons/font-awesome/css/all.min.css') }}" rel="stylesheet">
    @if(app()->getLocale() === 'ar')
    <link rel="stylesheet" href="{{ asset('css/frontend/rtl.css') }}">
    @endif

    @stack('styles')

<style>
    /* ─── Customer Dashboard Layout ─── */
    /* ─── Global Font ─── */
    * {
        box-sizing: border-box;
    }

    :root {
        --accent: #e8532e;
        --accent-soft: #fff4f1;

        --bg-main: #f8fafc;
        --bg-card: #ffffff;
        --bg-glass: rgba(255,255,255,0.7);

        --text-main: #0f172a;
        --text-muted: #64748b;

        --border-soft: #eef2f7;
        --shadow-soft: 0 10px 40px rgba(0,0,0,0.05);
    }

    /* 🌙 Dark Mode */
    body.dark-mode {
        --bg-main: #0f172a;
        --bg-card: #1e293b;
        --bg-glass: rgba(30,41,59,0.7);
        --text-main: #f1f5f9;
        --text-muted: #94a3b8;
        --border-soft: #334155;
        --shadow-soft: 0 10px 40px rgba(0,0,0,0.4);
    }

    body {
        background: var(--bg-main);
        color: var(--text-main);
        transition: background .3s ease, color .3s ease;
    }

    /* Ensure icons use FontAwesome font */
    i, .fas, .far, .fab, .fa {
        font-family: "Font Awesome 5 Free", "Font Awesome 5 Brands" !important;
    }

    .cdash-sidebar,
    .cdash-topbar,
    .cdash-content > * {
        background: var(--bg-card);
        box-shadow: var(--shadow-soft);
    }

    .cdash-wrapper {
        display: flex;
        min-height: 100vh;
    }

    /* ─── Sidebar ─── */
    .cdash-sidebar {
        width: 260px;
        min-height: 100vh;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 0;
        bottom: 0;
        z-index: 100;
        transition: transform .3s ease;
        border-inline-end: 1px solid #e2e8f0;
        box-shadow: 4px 0 24px rgba(0,0,0,0.02);
    }

    html[dir="ltr"] .cdash-sidebar { left: 0; }
    html[dir="rtl"] .cdash-sidebar { right: 0; }

    .cdash-sidebar-brand {
        padding: 30px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: center;
    }

    .cdash-sidebar-brand img {
        height: 48px;
        object-fit: contain;
    }

    .cdash-sidebar-user {
        padding: 24px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cdash-sidebar-user img {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #f1f5f9;
    }

    .cdash-sidebar-user .user-info .user-name {
        color: #1e293b;
        font-weight: 700;
        font-size: .95rem;
        line-height: 1.2;
    }

    .cdash-sidebar-user .user-info .user-type {
        color: #64748b;
        font-size: .8rem;
    }

    .cdash-nav {
        flex: 1;
        padding: 16px 0;
        overflow-y: auto;
    }

    .cdash-nav-label {
        color: #94a3b8;
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        padding: 20px 24px 8px;
        font-weight: 700;
    }

    .cdash-nav-item a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 24px;
        color: #475569;
        text-decoration: none;
        font-size: .95rem;
        font-weight: 500;
        transition: all .2s;
        border-radius: 0;
        position: relative;
    }

    .cdash-nav-item a:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    .cdash-nav-item a.active {
        background: var(--accent-color, #e8532e);
        color: #fff;
    }

    html[dir="ltr"] .cdash-nav-item a.active { border-radius: 0 8px 8px 0; }
    html[dir="rtl"] .cdash-nav-item a.active { border-radius: 8px 0 0 8px; }

    .cdash-nav-item a i {
        width: 20px;
        text-align: center;
        font-size: 1rem;
        color: #64748b;
    }

    .cdash-nav-item a.active i {
        color: #fff;
    }

    .cdash-nav-item a .nav-badge {
        margin-inline-start: auto;
        background: var(--accent-color, #e8532e);
        color: #fff;
        font-size: .7rem;
        padding: 2px 7px;
        border-radius: 20px;
        font-weight: 600;
    }

    .cdash-nav-item a.active .nav-badge {
        background: rgba(255,255,255,.25);
    }

    .cdash-sidebar-footer {
        padding: 20px;
        border-top: 1px solid #f1f5f9;
    }

    .cdash-sidebar-footer form button {
        width: 100%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #ef4444;
        padding: 12px 16px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
        font-size: .9rem;
        font-weight: 700;
        transition: all .2s;
    }

    .cdash-sidebar-footer form button:hover {
        background: #fef2f2;
        border-color: #fca5a5;
    }

    /* ─── Main Content ─── */
    .cdash-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        transition: margin .3s;
    }

    html[dir="ltr"] .cdash-main { margin-left: 260px; }
    html[dir="rtl"] .cdash-main { margin-right: 260px; }

    /* ─── Top Bar ─── */
    .cdash-topbar {
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        padding: 14px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 50;
    }

    .cdash-topbar-title {
        font-weight: 700;
        font-size: 1.1rem;
        color: #1a2537;
    }

    .cdash-topbar-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cdash-topbar-link {
        color: #6b7280;
        text-decoration: none;
        font-size: .88rem;
        transition: color .2s;
    }

    .cdash-topbar-link:hover { color: var(--accent-color, #e8532e); }

    .cdash-burger {
        display: none;
        background: none;
        border: none;
        font-size: 1.3rem;
        cursor: pointer;
        color: #1a2537;
    }

    /* ─── Content Area ─── */
    .cdash-content {
        flex: 1;
        padding: 28px;
    }

    /* ─── Flash Messages ─── */
    .cdash-flash {
        padding: 12px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .9rem;
        animation: fadeInUp .3s ease;
    }

    .cdash-flash-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
    .cdash-flash-error   { background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; }
    .cdash-flash-info    { background: #eff6ff; border: 1px solid #93c5fd; color: #1d4ed8; }

    /* ─── Sidebar overlay (mobile) ─── */
    .cdash-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.5);
        z-index: 99;
    }

    /* ─── Responsive ─── */
    @media (max-width: 768px) {
        .cdash-sidebar {
            transform: translateX(-100%);
        }

        html[dir="rtl"] .cdash-sidebar {
            transform: translateX(100%);
        }

        .cdash-sidebar.open {
            transform: translateX(0) !important;
        }

        html[dir="ltr"] .cdash-main { margin-left: 0; }
        html[dir="rtl"] .cdash-main { margin-right: 0; }

        .cdash-burger { display: block; }

        .cdash-content { padding: 18px; }

        .cdash-overlay.visible { display: block; }
    }

    .notification-wrapper {
        position: relative;
    }

    .notif-badge {
        background: var(--accent);
        color: #fff;
        font-size: .7rem;
        padding: 2px 6px;
        border-radius: 20px;
        position: absolute;
        top: -6px;
        right: -6px;
    }

    .notif-dropdown {
        position: absolute;
        top: 120%;
        inset-inline-end: 0;
        width: 320px;
        background: var(--bg-card);
        border-radius: 16px;
        box-shadow: var(--shadow-soft);
        border: 1px solid var(--border-soft);
        display: none;
        overflow: hidden;
    }

    .notif-header {
        padding: 14px;
        font-weight: 700;
        border-bottom: 1px solid var(--border-soft);
    }

    .notif-item {
        display: flex;
        gap: 10px;
        padding: 14px;
        font-size: .9rem;
        border-bottom: 1px solid var(--border-soft);
    }

    .notif-item:hover {
        background: var(--accent-soft);
    }

    .notif-time {
        font-size: .75rem;
        color: var(--text-muted);
    }

    .notif-footer {
        display: block;
        text-align: center;
        padding: 12px;
        font-weight: 600;
        color: var(--accent);
        text-decoration: none;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    </style>


<style>
        /* Sidebar Base */
    /* Sidebar Base */
    /* Sidebar Base */
    .cdash-sidebar {
        width: 270px;
        height: 100vh;
        background: #0f172a;
        color: #cbd5e1;
        position: fixed;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
        padding: 20px;
        transition: 0.3s ease;
        box-shadow: 8px 0 40px rgba(0,0,0,0.4);
        z-index: 1000;
    }

    /* Collapsed */
    .cdash-sidebar.collapsed {
        width: 80px;
    }

    .cdash-sidebar.collapsed span,
    .cdash-sidebar.collapsed .cdash-label,
    .cdash-sidebar.collapsed .cdash-user-meta {
        display: none;
    }

    /* Top */
    .cdash-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .cdash-collapse-btn {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 18px;
    }

    /* User */
    .cdash-user-card {
        display: flex;
        align-items: center;
        gap: 12px;
        background: rgba(255,255,255,0.05);
        padding: 12px;
        border-radius: 12px;
        margin-bottom: 25px;
    }

    .cdash-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
    }

    /* Links */
    .cdash-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        text-decoration: none;
        color: #cbd5e1;
        margin-bottom: 6px;
        transition: 0.25s;
        position: relative;
    }

    .cdash-link:hover {
        background: rgba(255,255,255,0.06);
        transform: translateX(5px);
    }

    .cdash-link.active {
        background: linear-gradient(90deg,#2563eb,#4f46e5);
        color: #fff;
    }

    /* Badge */
    .cdash-badge {
        margin-left: auto;
        background: #ef4444;
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 20px;
        color: #fff;
    }

    /* Footer */
    .cdash-footer {
        margin-top: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .cdash-theme-btn,
    .cdash-logout {
        background: none;
        border: none;
        color: #cbd5e1;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        text-align: left;
    }

    .cdash-theme-btn:hover {
        background: rgba(255,255,255,0.08);
    }

    .cdash-logout:hover {
        background: rgba(239,68,68,0.2);
        color: #f87171;
    }

    /* Light Mode */
    body.light-mode .cdash-sidebar {
        background: #ffffff;
        color: #1e293b;
    }
    body.light-mode .cdash-link {
        color: #1e293b;
    }
    .cdash-sidebar {
        width: 280px; height: 100vh;
        background: #0f172a; color: #cbd5e1;
        position: fixed; left: 0; top: 0;
        display: flex; flex-direction: column;
        padding: 20px; transition: 0.3s;
        box-shadow: 8px 0 40px rgba(0,0,0,0.4);
        z-index: 1000;
    }

    .cdash-sidebar.collapsed { width: 80px; }
    .cdash-sidebar.collapsed span, .cdash-sidebar.collapsed .cdash-label, .cdash-sidebar.collapsed .cdash-user-meta { display: none; }

    /* Active Indicator */
    .cdash-link { position: relative; display: flex; align-items: center; gap:12px; padding: 10px; border-radius: 8px; transition: all 0.3s; }
    .cdash-link .active-indicator { position: absolute; left:0; top:0; bottom:0; width:4px; background:#2563eb; border-radius:4px; opacity:0; transition: all 0.3s; }
    .cdash-link.active .active-indicator { opacity:1; }

    /* Notifications Dropdown */
    .cdash-notif-dropdown { display:none; position:absolute; top:100%; right:0; background:#1e293b; border-radius:8px; width:220px; max-height:300px; overflow-y:auto; box-shadow:0 8px 20px rgba(0,0,0,0.5); z-index:999; }
    .cdash-notif-dropdown .notif-item { padding:10px; border-bottom:1px solid rgba(255,255,255,0.05); cursor:pointer; transition:0.2s; }
    .cdash-notif-dropdown .notif-item:hover { background: rgba(255,255,255,0.05); }

    /* Mini Widgets */
    .cdash-mini-widgets { display:flex; gap:10px; margin-bottom:20px; }
    .cdash-mini-widgets .widget { background: rgba(255,255,255,0.05); padding:10px; border-radius:10px; flex:1; text-align:center; font-size:0.85rem; transition:0.2s; }
    .cdash-mini-widgets .widget:hover { background: rgba(255,255,255,0.1); }

    /* Footer & Buttons */
    .cdash-footer { margin-top:auto; display:flex; flex-direction:column; gap:8px; }
    .cdash-theme-btn, .cdash-logout { background:none; border:none; color:#cbd5e1; cursor:pointer; padding:8px 12px; border-radius:8px; text-align:left; transition:0.2s; }
    .cdash-theme-btn:hover { background: rgba(255,255,255,0.08); }
    .cdash-logout:hover { background: rgba(239,68,68,0.2); color: #f87171; }

    /* Dark/Light Mode */
    body.light-mode .cdash-sidebar { background:#fff; color:#1e293b; }
    body.light-mode .cdash-link { color:#1e293b; }
</style>

</head>
<body>

<div class="cdash-overlay" id="cdashOverlay" onclick="closeSidebar()"></div>

<div class="cdash-wrapper">

    {{-- ─── Sidebar ─── --}}
 <aside class="cdash-sidebar" id="cdashSidebar">

    {{-- Top Logo & Collapse --}}
    <div class="cdash-top">
        <div class="cdash-sidebar-brand">
            <a href="{{ url('/') }}">
                <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="{{ config('app.name') }}">
            </a>
        </div>
        <button id="toggleSidebar" class="cdash-collapse-btn">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    {{-- User Card --}}
    <div class="cdash-user-card">
        <img class="cdash-avatar" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->full_name }}">
        <div class="cdash-user-meta">
            <div class="cdash-user-name">{{ auth()->user()->full_name }}</div>
            <div class="cdash-user-role">{{ __('Subscriber') }}</div>
        </div>
        <button id="notificationsToggle" class="cdash-notif-btn">
            <i class="fas fa-bell"></i>
            <span class="cdash-badge">{{-- auth()->user()->unreadNotifications->count() --}}</span>
        </button>

        {{-- Dropdown Notifications --}}
        <div id="notifDropdown" class="cdash-notif-dropdown">
           {{--  @forelse(auth()->user()->notifications->take(5) as $notif)
                <div class="notif-item">{{ $notif->data['title'] ?? __('New Notification') }}</div>
            @empty
                <div class="notif-empty">{{ __('No notifications') }}</div>
            @endforelse --}}
        </div>
    </div>

    {{-- Mini Stats Widgets --}}
    <div class="cdash-mini-widgets">
        <div class="widget">
            <span>{{ __('Bookings') }}</span>
            <strong>{{ auth()->user()->bookings()->count() }}</strong>
        </div>
        <div class="widget">
            <span>{{ __('Favorites') }}</span>
            <strong>{{ auth()->user()->favorites()->count() }}</strong>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="cdash-nav">
        <div class="cdash-section">
            <span class="cdash-label">{{ __('Main Menu') }}</span>

            <a href="{{ route('customer.dashboard') }}" class="cdash-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                <span class="active-indicator"></span>
                <i class="fas fa-th-large"></i>
                <span>{{ __('Dashboard') }}</span>
            </a>

            <a href="{{ route('customer.bookings.index') }}" class="cdash-link {{ request()->routeIs('customer.bookings.*') ? 'active' : '' }}">
                <span class="active-indicator"></span>
                <i class="fas fa-ticket-alt"></i>
                <span>{{ __('My Bookings') }}</span>
                <span class="cdash-badge">{{ auth()->user()->bookings()->where('status','pending')->count() }}</span>
            </a>

            <a href="{{ route('customer.favorites.index') }}" class="cdash-link {{ request()->routeIs('customer.favorites.*') ? 'active' : '' }}">
                <span class="active-indicator"></span>
                <i class="fas fa-heart"></i>
                <span>{{ __('Favorites') }}</span>
            </a>
        </div>

        <div class="cdash-section">
            <span class="cdash-label">{{ __('Account') }}</span>

            <a href="{{ route('customer.profile') }}" class="cdash-link {{ request()->routeIs('customer.profile*') ? 'active' : '' }}">
                <span class="active-indicator"></span>
                <i class="fas fa-user-circle"></i>
                <span>{{ __('Profile') }}</span>
            </a>

            <a href="{{ route('customer.payments.index') }}" class="cdash-link {{ request()->routeIs('customer.payments.*') ? 'active' : '' }}">
                <span class="active-indicator"></span>
                <i class="fas fa-credit-card"></i>
                <span>{{ __('Payments') }}</span>
            </a>
        </div>
    </nav>

    {{-- Footer --}}
    <div class="cdash-footer">
        <button id="toggleTheme" class="cdash-theme-btn">
            <i class="fas fa-moon"></i>
            <span>Dark Mode</span>
        </button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="cdash-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>{{ __('Logout') }}</span>
            </button>
        </form>
    </div>

</aside>

    

{{-- ─── Main ─── --}}
<main class="cdash-main">

    {{-- Top Bar --}}
    <div class="cdash-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="cdash-burger" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <span class="cdash-topbar-title">@yield('page-title', __('Dashboard'))</span>
        </div>

        <div class="cdash-topbar-actions align-items-center gap-3">

            {{-- Dark Mode Toggle --}}
            <button onclick="toggleDarkMode()" class="cdash-topbar-link" title="{{ __('Toggle Dark Mode') }}">
                <i class="fas fa-moon"></i>
            </button>

            {{-- Notifications Dropdown --}}
            <div class="notification-wrapper position-relative">
                <button class="cdash-topbar-link" onclick="toggleNotif()" title="{{ __('Notifications') }}">
                    <i class="fas fa-bell"></i>
                    <span class="notif-badge">3</span>
                </button>

                <div class="notif-dropdown shadow" id="notifDropdown">
                    <div class="notif-header">{{ __('Notifications') }}</div>

                    <div class="notif-item">
                        <i class="fas fa-check-circle text-success"></i>
                        <div>
                            {{ __('Booking confirmed') }}
                            <div class="notif-time">{{ __('5 minutes ago') }}</div>
                        </div>
                    </div>

                    <div class="notif-item">
                        <i class="fas fa-credit-card text-info"></i>
                        <div>
                            {{ __('Payment received') }}
                            <div class="notif-time">{{ __('1 hour ago') }}</div>
                        </div>
                    </div>

                    <a href="{{ route('customer.notifications.index') }}" class="notif-footer">{{ __('View all') }}</a>
                </div>
            </div>

            {{-- Language Switcher --}}
            <div class="dropdown position-relative">
                <a href="#" class="cdash-topbar-link" onclick="event.preventDefault(); this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'block' ? 'none' : 'block'">
                    <i class="fas fa-globe"></i> {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                </a>
                <div class="cdash-lang-dropdown" style="display: none; position: absolute; top: 100%; inset-inline-end: 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); min-width: 120px; z-index: 1000; margin-top: 8px;">
                        <a href="{{ route('lang.switch', 'ar') }}" style="display: block; padding: 10px 16px; color: #1e293b; text-decoration: none; font-size: .85rem; @if(app()->getLocale() === 'ar') background: #f8fafc; font-weight: 700; @endif border-bottom: 1px solid #f1f5f9;">العربية</a>
                        <a href="{{ route('lang.switch', 'en') }}" style="display: block; padding: 10px 16px; color: #1e293b; text-decoration: none; font-size: .85rem; @if(app()->getLocale() === 'en') background: #f8fafc; font-weight: 700; @endif">English</a>
                </div>
            </div>

        </div>
    </div>

    {{-- Content Area --}}
    <div class="cdash-content">

        {{-- Flash Messages --}}
        @foreach (['success', 'error', 'info'] as $msg)
            @if(session($msg))
                <div class="cdash-flash cdash-flash-{{ $msg }}">
                    <i class="fas fa-{{ $msg === 'success' ? 'check-circle' : ($msg === 'error' ? 'exclamation-circle' : 'info-circle') }}"></i>
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach

        @yield('content')
    </div>
</main>



<style>
/* Topbar styling */
.cdash-topbar {
    background: var(--topbar-bg, #fff);
    border-bottom: 1px solid #e5e7eb;
    padding: 12px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 50;
    transition: background 0.3s;
}

.cdash-topbar-title {
    font-weight: 700;
    font-size: 1.1rem;
}

.cdash-topbar-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.cdash-topbar-link {
    background: none;
    border: none;
    color: #475569;
    cursor: pointer;
    font-size: 1rem;
    position: relative;
}

.notif-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #e8532e;
    color: #fff;
    font-size: 0.65rem;
    padding: 2px 6px;
    border-radius: 50%;
}

.notif-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    width: 280px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-top: 8px;
    z-index: 1000;
    animation: fadeInDown 0.2s ease;
}

.notif-header {
    font-weight: 700;
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
}

.notif-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    cursor: pointer;
    transition: background 0.2s;
}

.notif-item:hover { background: #f8fafc; }

.notif-time {
    font-size: 0.7rem;
    color: #94a3b8;
}

.notif-footer {
    display: block;
    text-align: center;
    padding: 10px 0;
    border-top: 1px solid #f1f5f9;
    text-decoration: none;
    color: var(--accent-color, #e8532e);
    font-weight: 700;
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Dark Mode */
.dark-mode .cdash-topbar {
    background: #1e293b;
    color: #f8fafc;
}
.dark-mode .cdash-topbar-link { color: #f8fafc; }
.dark-mode .notif-dropdown { background: #334155; color: #f8fafc; }
.dark-mode .notif-item:hover { background: #475569; }
.dark-mode .notif-time { color: #cbd5e1; }
.dark-mode .notif-footer { color: #fbbf24; }
</style>

<script>
function toggleSidebar() {
    document.getElementById('cdashSidebar').classList.toggle('open');
    document.getElementById('cdashOverlay').classList.toggle('visible');
}

function closeSidebar() {
    document.getElementById('cdashSidebar').classList.remove('open');
    document.getElementById('cdashOverlay').classList.remove('visible');
}
</script>
<script>
function toggleDarkMode(){
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
}

if(localStorage.getItem('darkMode') === 'true'){
    document.body.classList.add('dark-mode');
}
</script>

<script>
function toggleNotif(){
    const el = document.getElementById('notifDropdown');
    el.style.display = el.style.display === 'block' ? 'none' : 'block';
}
</script>
<script>
const sidebar = document.getElementById('cdashSidebar');
const toggleBtn = document.getElementById('toggleSidebar');
const themeBtn = document.getElementById('toggleTheme');
const notifBtn = document.getElementById('notificationsToggle');
const notifDropdown = document.getElementById('notifDropdown');

toggleBtn.onclick = () => sidebar.classList.toggle('collapsed');
themeBtn.onclick = () => document.body.classList.toggle('light-mode');
notifBtn.onclick = () => notifDropdown.classList.toggle('show');

// Close dropdown on click outside
document.addEventListener('click', e => {
    if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
        notifDropdown.classList.remove('show');
    }
});
</script>

{{-- JS for Topbar interactions --}}
<script>
function toggleNotif() {
    document.getElementById('notifDropdown').classList.toggle('d-block');
}

function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('dark-mode', document.body.classList.contains('dark-mode'));
}

// Remember dark mode preference
if(localStorage.getItem('dark-mode') === 'true') {
    document.body.classList.add('dark-mode');
}

// Close dropdowns on click outside
document.addEventListener('click', function(e){
    const notif = document.getElementById('notifDropdown');
    if(!e.target.closest('.notification-wrapper')) notif.classList.remove('d-block');

    const lang = document.querySelector('.cdash-lang-dropdown');
    if(!e.target.closest('.dropdown')) lang.classList.remove('d-block');
});
</script>

@stack('scripts')
</body>
</html>
