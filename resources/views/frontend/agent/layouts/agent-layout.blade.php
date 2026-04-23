<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Agent Dashboard')) - {{ config('app.name') }}</title>

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/frontend/app.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/frontend/components.css') }}?v={{ time() }}">
    <link href="{{ asset('icons/font-awesome/css/all.min.css') }}" rel="stylesheet">
    @if(app()->getLocale() === 'ar')
    <link rel="stylesheet" href="{{ asset('css/frontend/rtl.css') }}">
    @endif

    @stack('styles')

   <style>
*{
    box-sizing:border-box;
}

:root{
    --accent:#e8532e;
    --accent-soft:#fff4f1;

    --bg-main:#f8fafc;
    --bg-card:#ffffff;
    --bg-glass:rgba(255,255,255,0.7);

    --text-main:#0f172a;
    --text-muted:#64748b;

    --border-soft:#eef2f7;
    --shadow-soft:0 10px 40px rgba(0,0,0,0.05);
}

/* Dark Mode */
body.dark-mode{
    --bg-main:#0f172a;
    --bg-card:#1e293b;
    --bg-glass:rgba(30,41,59,0.7);

    --text-main:#f1f5f9;
    --text-muted:#94a3b8;

    --border-soft:#334155;
    --shadow-soft:0 10px 40px rgba(0,0,0,0.4);
}

body{
    margin:0;
    background:var(--bg-main);
    color:var(--text-main);
    font-family:'Tajawal',sans-serif;
    transition:.3s;
}

.cdash-wrapper{
    display:flex;
    min-height:100vh;
}

/* Sidebar */

.cdash-sidebar{
    width:260px;
    position:fixed;
    top:0;
    bottom:0;
    z-index:100;
    background:var(--bg-card);
    display:flex;
    flex-direction:column;
    border-inline-end:1px solid var(--border-soft);
    box-shadow:var(--shadow-soft);
    transition:.3s;
}

html[dir="ltr"] .cdash-sidebar{left:0}
html[dir="rtl"] .cdash-sidebar{right:0}

.cdash-sidebar-brand{
    padding:30px 24px;
    border-bottom:1px solid var(--border-soft);
    display:flex;
    justify-content:center;
}

.cdash-sidebar-brand img{
    height:48px;
}

.cdash-sidebar-user{
    padding:24px 20px;
    border-bottom:1px solid var(--border-soft);
    display:flex;
    align-items:center;
    gap:12px;
}

.cdash-sidebar-user img{
    width:48px;
    height:48px;
    border-radius:12px;
    object-fit:cover;
}

.user-name{
    font-weight:700;
}

.user-type{
    font-size:.8rem;
    color:var(--text-muted);
}

/* Nav */

.cdash-nav{
    flex:1;
    padding:16px 0;
}

.cdash-nav-label{
    padding:20px 24px 8px;
    font-size:.75rem;
    color:#94a3b8;
    font-weight:700;
}

.cdash-nav-item a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 24px;
    text-decoration:none;
    color:#475569;
    transition:.2s;
}

.cdash-nav-item a:hover{
    background:#f8fafc;
}

.cdash-nav-item a.active{
    background:var(--accent);
    color:#fff;
}

html[dir="ltr"] .cdash-nav-item a.active{
    border-radius:0 8px 8px 0;
}

html[dir="rtl"] .cdash-nav-item a.active{
    border-radius:8px 0 0 8px;
}

.cdash-nav-item i{
    width:20px;
    text-align:center;
}

/* Main */

.cdash-main{
    flex:1;
    display:flex;
    flex-direction:column;
}

html[dir="ltr"] .cdash-main{
    margin-left:260px;
}

html[dir="rtl"] .cdash-main{
    margin-right:260px;
}

/* Topbar */

.cdash-topbar{
    background:var(--bg-card);
    padding:14px 28px;
    border-bottom:1px solid var(--border-soft);
    display:flex;
    justify-content:space-between;
    align-items:center;
    position:sticky;
    top:0;
}

.cdash-topbar-title{
    font-weight:700;
}

.cdash-topbar-actions{
    display:flex;
    gap:12px;
}

/* Content */

.cdash-content{
    padding:28px;
}

/* Flash */

.cdash-flash{
    padding:12px 18px;
    border-radius:10px;
    margin-bottom:20px;
    display:flex;
    gap:8px;
}

.cdash-flash-success{
    background:#f0fdf4;
    border:1px solid #86efac;
}

.cdash-flash-error{
    background:#fef2f2;
    border:1px solid #fca5a5;
}

/* Mobile */

.cdash-overlay{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.5);
    z-index:99;
}

.cdash-burger{
    display:none;
}

@media(max-width:768px){

    .cdash-sidebar{
        transform:translateX(-100%);
    }

    html[dir="rtl"] .cdash-sidebar{
        transform:translateX(100%);
    }

    .cdash-sidebar.open{
        transform:translateX(0);
    }

    .cdash-main{
        margin:0;
    }

    .cdash-burger{
        display:block;
    }

    .cdash-overlay.visible{
        display:block;
    }

}

/* Premium Pagination Styling */
.pagination-wrapper {
    margin-top: 40px;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    gap: 8px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.page-item .page-link {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: var(--bg-card);
    border: 1.5px solid var(--border-soft);
    color: var(--text-muted);
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 0.95rem;
}

.page-item.active .page-link {
    background: var(--accent);
    color: #fff;
    border-color: var(--accent);
    box-shadow: 0 8px 20px rgba(232, 83, 46, 0.25);
}

.page-item.disabled .page-link {
    opacity: 0.5;
    cursor: not-allowed;
    background: var(--bg-main);
}

.page-item:not(.active):not(.disabled) .page-link:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: var(--accent-soft);
    transform: translateY(-2px);
}

.page-item .page-link i {
    font-size: 0.8rem;
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

    /* Modern Topbar Controls */
    .cdash-topbar-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .topbar-control-btn {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bg-main);
        border: 1px solid var(--border-soft);
        color: var(--text-main);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        position: relative;
    }

    .topbar-control-btn:hover {
        background: var(--accent-soft);
        color: var(--accent);
        border-color: var(--accent);
        transform: translateY(-2px);
    }

    .lang-switcher-wrapper {
        position: relative;
    }

    .lang-dropdown-menu {
        display: none;
        position: absolute;
        top: calc(100% + 12px);
        inset-inline-end: 0;
        background: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        min-width: 160px;
        z-index: 1001;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .lang-dropdown-menu.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .lang-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 18px;
        color: var(--text-main);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .lang-item:hover {
        background: var(--bg-main);
        color: var(--accent);
    }

    .lang-item.active {
        background: var(--accent-soft);
        color: var(--accent);
    }

    .lang-flag {
        width: 20px;
        height: 15px;
        border-radius: 2px;
        object-fit: cover;
    }

    /* Theme Toggle Specifics */
    body.dark-mode .fa-moon { display: none; }
    body:not(.dark-mode) .fa-sun { display: none; }
    body.dark-mode .fa-sun { color: #fbbf24; }

    /* Fix for sidebar dark mode button too */
    .cdash-theme-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        color: #cbd5e1;
        font-weight: 600;
        width: 100%;
        margin-bottom: 20px;
        transition: 0.2s;
    }
    body.light-mode .cdash-theme-btn {
        background: #f1f5f9;
        border-color: #e2e8f0;
        color: #1e293b;
    }
</style>
</head>
<body>
<div class="cdash-overlay" id="cdashOverlay" onclick="closeSidebar()"></div>

<div class="cdash-wrapper">
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

        <div style="position:relative">

            <img class="cdash-avatar"
                 src="{{ auth()->user()->profile_photo_url }}"
                 alt="{{ auth()->user()->full_name }}">

            @if(auth()->user()->company && auth()->user()->company->logo)
                <img
                    src="{{ auth()->user()->company->logo_url }}"
                    style="width:22px;height:22px;border-radius:50%;position:absolute;bottom:-2px;right:-2px;border:2px solid #fff"
                    alt="{{ auth()->user()->company->name }}">
            @endif

        </div>

        <div class="cdash-user-meta">
            <div class="cdash-user-name">{{ auth()->user()->full_name }}</div>

            <div class="cdash-user-role">
                {{ __('Agent') }}
                @if(auth()->user()->company)
                    ({{ auth()->user()->company->localized_name }})
                @endif
            </div>
        </div>

        {{-- Notifications --}}
        {{--<button id="notificationsToggle" class="cdash-notif-btn">
            <i class="fas fa-bell"></i>

            <span class="cdash-badge">
                {{ auth()->user()->unreadNotifications->count() }}
            </span>
        </button>

        <div id="notifDropdown" class="cdash-notif-dropdown">

            @forelse(auth()->user()->notifications->take(5) as $notif)

                <div class="notif-item">
                    {{ $notif->data['title'] ?? __('New Notification') }}
                </div>

            @empty
                <div class="notif-empty">
                    {{ __('No notifications') }}
                </div>
            @endforelse

        </div>--}}

    </div>

    {{-- Mini Widgets --}}
    <div class="cdash-mini-widgets">

        <div class="widget">
            <span>{{ __('Trips') }}</span>
            <strong>{{ auth()->user()->trips()->count() }}</strong>
        </div>

        <div class="widget">
            <span>{{ __('Bookings') }}</span>
            <strong>{{ auth()->user()->bookings()->count() }}</strong>
        </div>

    </div>

    {{-- Navigation --}}
    <nav class="cdash-nav">

        <div class="cdash-section">

            <span class="cdash-label">{{ __('Main Menu') }}</span>

            <a href="{{ route('agent.dashboard') }}"
               class="cdash-link {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">

                <span class="active-indicator"></span>
                <i class="fas fa-th-large"></i>
                <span>{{ __('Dashboard') }}</span>

            </a>


            <a href="{{ route('agent.trips.index') }}"
               class="cdash-link {{ request()->routeIs('agent.trips.*') ? 'active' : '' }}">

                <span class="active-indicator"></span>
                <i class="fas fa-map-marked-alt"></i>
                <span>{{ __('Fly Vio') }}</span>

            </a>


            <a href="{{ route('agent.bookings.index') }}"
               class="cdash-link {{ request()->routeIs('agent.bookings.*') ? 'active' : '' }}">

                <span class="active-indicator"></span>
                <i class="fas fa-ticket-alt"></i>
                <span>{{ __('Bookings') }}</span>

            </a>


            <a href="{{ route('agent.favorites.index') }}"
               class="cdash-link {{ request()->routeIs('agent.favorites.*') ? 'active' : '' }}">

                <span class="active-indicator"></span>
                <i class="fas fa-heart"></i>
                <span>{{ __('My Favorites') }}</span>

            </a>

        </div>


        <div class="cdash-section">

            <span class="cdash-label">{{ __('Account') }}</span>

            <a href="{{ route('agent.profile.index') }}"
               class="cdash-link {{ request()->routeIs('agent.profile.*') ? 'active' : '' }}">

                <span class="active-indicator"></span>
                <i class="fas fa-user-circle"></i>
                <span>{{ __('My Profile') }}</span>

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
    <main class="cdash-main">
        <div class="cdash-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="cdash-burger" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                
                <span class="cdash-topbar-title">@yield('page-title', __('Dashboard'))</span>
            </div>
            <div class="cdash-topbar-actions">
                {{-- Theme Toggle --}}
                <button type="button" class="topbar-control-btn" onclick="toggleDark()" title="{{ __('Toggle Theme') }}">
                    <i class="fas fa-moon"></i>
                    <i class="fas fa-sun"></i>
                </button>

                {{-- Language Switcher --}}
                <div class="lang-switcher-wrapper">
                    <button type="button" class="topbar-control-btn" onclick="toggleLangMenu()" id="langBtn">
                        <i class="fas fa-globe"></i>
                    </button>
                    <div class="lang-dropdown-menu" id="langMenu">
                        <a href="{{ route('lang.switch', 'ar') }}" class="lang-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}">
                            <span class="lang-text">العربية</span>
                        </a>
                        <a href="{{ route('lang.switch', 'en') }}" class="lang-item {{ app()->getLocale() === 'en' ? 'active' : '' }}">
                            <span class="lang-text">English</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="cdash-content">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="cdash-flash cdash-flash-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="cdash-flash cdash-flash-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>

<script>
function toggleSidebar() {
    document.getElementById('cdashSidebar').classList.toggle('open');
    document.getElementById('cdashOverlay').classList.toggle('visible');
}

function closeSidebar() {
    document.getElementById('cdashSidebar').classList.remove('open');
    document.getElementById('cdashOverlay').classList.remove('visible');
}

function toggleDark(){
    document.body.classList.toggle('dark-mode');
    // Save preference if needed
}

function toggleLangMenu() {
    document.getElementById('langMenu').classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const langWrapper = document.querySelector('.lang-switcher-wrapper');
    const langMenu = document.getElementById('langMenu');
    if (!langWrapper.contains(event.target)) {
        langMenu.classList.remove('show');
    }
});
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

@stack('scripts')
</body>
</html>
