<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Agent Dashboard')) - {{ config('app.name') }}</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/dist/css/bootstrap.min.css') }}">
    <link href="{{ asset('icons/font-awesome/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    {{-- Agent dashboard base styles are inline in this file --}}

    @stack('styles')

<style>
/* ══════════════════════════════════════════════
   AGENT DASHBOARD — UNIFIED DESIGN SYSTEM
   ══════════════════════════════════════════════ */

*, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* ─── CSS Variables ─── */
:root {
    /* Brand */
    --accent: #6366f1;
    --accent-hover: #4f46e5;
    --accent-soft: rgba(99, 102, 241, 0.08);
    --accent-glow: rgba(99, 102, 241, 0.25);

    /* Surfaces */
    --bg-body: #f1f5f9;
    --bg-card: #ffffff;
    --bg-sidebar: #0f172a;
    --bg-sidebar-hover: rgba(255, 255, 255, 0.06);
    --bg-sidebar-active: linear-gradient(135deg, var(--accent), #8b5cf6);
    --bg-topbar: rgba(255, 255, 255, 0.85);

    /* Text */
    --text-primary: #0f172a;
    --text-secondary: #475569;
    --text-muted: #94a3b8;
    --text-sidebar: #94a3b8;
    --text-sidebar-active: #ffffff;

    /* Borders & Shadows */
    --border: #e2e8f0;
    --border-soft: #f1f5f9;
    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.04);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 12px 40px rgba(0, 0, 0, 0.08);
    --shadow-sidebar: 4px 0 24px rgba(0, 0, 0, 0.15);

    /* Layout */
    --sidebar-width: 272px;
    --sidebar-collapsed-width: 80px;
    --topbar-height: 68px;

    /* Radius */
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    --radius-2xl: 24px;

    /* Transitions */
    --transition-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-smooth: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-spring: 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* ─── Dark Mode ─── */
body.dark-mode {
    --bg-body: #0b1120;
    --bg-card: #1e293b;
    --bg-sidebar: #0f172a;
    --bg-topbar: rgba(30, 41, 59, 0.9);

    --text-primary: #f1f5f9;
    --text-secondary: #cbd5e1;
    --text-muted: #64748b;

    --border: #334155;
    --border-soft: #1e293b;
    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.2);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.3);
    --shadow-lg: 0 12px 40px rgba(0, 0, 0, 0.4);
    --shadow-sidebar: 4px 0 24px rgba(0, 0, 0, 0.5);
}

/* ─── Body ─── */
body {
    background: var(--bg-body);
    color: var(--text-primary);
    font-family: 'Tajawal', 'Inter', -apple-system, sans-serif;
    line-height: 1.6;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
    transition: background var(--transition-smooth), color var(--transition-smooth);
}

/* ═══════════════════════════
   WRAPPER
   ═══════════════════════════ */
.cdash-wrapper {
    display: flex;
    min-height: 100vh;
}

/* ═══════════════════════════
   SIDEBAR
   ═══════════════════════════ */
.cdash-sidebar {
    width: var(--sidebar-width);
    height: 100vh;
    position: fixed;
    top: 0;
    display: flex;
    flex-direction: column;
    background: var(--bg-sidebar);
    color: var(--text-sidebar);
    padding: 0;
    z-index: 1000;
    box-shadow: var(--shadow-sidebar);
    transition: width var(--transition-smooth), transform var(--transition-smooth);
    overflow-x: hidden;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.1) transparent;
}

html[dir="ltr"] .cdash-sidebar { left: 0; border-right: 1px solid rgba(255,255,255,0.06); }
html[dir="rtl"] .cdash-sidebar { right: 0; border-left: 1px solid rgba(255,255,255,0.06); }

.cdash-sidebar::-webkit-scrollbar { width: 4px; }
.cdash-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

/* Sidebar — Brand */
.cdash-sidebar-brand {
    padding: 24px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    flex-shrink: 0;
    min-height: 80px;
}

.cdash-sidebar-brand a {
    display: flex;
    align-items: center;
    text-decoration: none;
}

.cdash-sidebar-brand img {
    height: 40px;
    width: auto;
    max-width: 180px;
    object-fit: contain;
    transition: var(--transition-smooth);
}

/* Sidebar — User Card */
.cdash-user-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    margin: 16px 12px;
    background: rgba(255, 255, 255, 0.04);
    border-radius: var(--radius-lg);
    border: 1px solid rgba(255, 255, 255, 0.06);
    flex-shrink: 0;
    transition: var(--transition-fast);
}

.cdash-user-card:hover {
    background: rgba(255, 255, 255, 0.07);
}

.cdash-avatar {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-md);
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}

.cdash-user-meta {
    overflow: hidden;
    min-width: 0;
}

.cdash-user-name {
    font-weight: 700;
    font-size: 0.9rem;
    color: #f1f5f9;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cdash-user-role {
    font-size: 0.78rem;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Sidebar — Mini Widgets */
.cdash-mini-widgets {
    display: flex;
    gap: 8px;
    padding: 0 12px;
    margin-bottom: 8px;
    flex-shrink: 0;
}

.cdash-mini-widgets .widget {
    flex: 1;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: var(--radius-md);
    padding: 12px 8px;
    text-align: center;
    transition: var(--transition-fast);
    cursor: default;
}

.cdash-mini-widgets .widget:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
}

.cdash-mini-widgets .widget span {
    display: block;
    font-size: 0.7rem;
    color: #64748b;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.cdash-mini-widgets .widget strong {
    display: block;
    font-size: 1.2rem;
    color: #f1f5f9;
    font-weight: 800;
}

/* Sidebar — Navigation */
.cdash-nav {
    flex: 1;
    padding: 8px 0;
    overflow-y: auto;
}

.cdash-section {
    margin-bottom: 8px;
}

.cdash-label {
    display: block;
    padding: 16px 24px 8px;
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #475569;
}

.cdash-link {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 11px 20px;
    margin: 2px 10px;
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--text-sidebar);
    font-weight: 600;
    font-size: 0.88rem;
    transition: var(--transition-fast);
    position: relative;
    overflow: hidden;
}

.cdash-link i {
    width: 20px;
    text-align: center;
    font-size: 1rem;
    flex-shrink: 0;
    transition: var(--transition-fast);
}

.cdash-link span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cdash-link .active-indicator {
    position: absolute;
    width: 3px;
    top: 6px;
    bottom: 6px;
    border-radius: 3px;
    background: var(--accent);
    opacity: 0;
    transition: var(--transition-fast);
}

html[dir="ltr"] .cdash-link .active-indicator { left: 0; }
html[dir="rtl"] .cdash-link .active-indicator { right: 0; }

.cdash-link:hover {
    background: var(--bg-sidebar-hover);
    color: #e2e8f0;
}

html[dir="ltr"] .cdash-link:hover { transform: translateX(4px); }
html[dir="rtl"] .cdash-link:hover { transform: translateX(-4px); }

.cdash-link.active {
    background: var(--bg-sidebar-active);
    color: var(--text-sidebar-active);
    box-shadow: 0 4px 15px var(--accent-glow);
}

.cdash-link.active .active-indicator {
    opacity: 1;
    background: #fff;
}

.cdash-link.active i {
    color: #fff;
}

/* Sidebar — Footer */
.cdash-footer {
    margin-top: auto;
    padding: 16px 12px;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
    flex-shrink: 0;
}

.cdash-theme-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 10px 14px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: var(--radius-md);
    color: #94a3b8;
    font-family: inherit;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: var(--transition-fast);
    margin-bottom: 8px;
}

.cdash-theme-btn:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #e2e8f0;
}

.cdash-logout {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 10px 14px;
    background: none;
    border: 1px solid transparent;
    border-radius: var(--radius-md);
    color: #94a3b8;
    font-family: inherit;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: var(--transition-fast);
    text-align: inherit;
}

.cdash-logout:hover {
    background: rgba(239, 68, 68, 0.1);
    border-color: rgba(239, 68, 68, 0.2);
    color: #f87171;
}

/* Sidebar — Collapse Toggle (Desktop) */
.cdash-collapse-btn {
    display: none; /* Hidden for now — sidebar collapse not needed per plan */
}

/* ═══════════════════════════
   MAIN CONTENT AREA
   ═══════════════════════════ */
.cdash-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    transition: var(--transition-smooth);
}

html[dir="ltr"] .cdash-main { margin-left: var(--sidebar-width); }
html[dir="rtl"] .cdash-main { margin-right: var(--sidebar-width); }

/* ═══════════════════════════
   TOPBAR
   ═══════════════════════════ */
.cdash-topbar {
    position: sticky;
    top: 0;
    z-index: 100;
    background: var(--bg-topbar);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    padding: 0 28px;
    height: var(--topbar-height);
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
    transition: background var(--transition-smooth);
}

.cdash-topbar-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

.cdash-topbar-title {
    font-weight: 800;
    font-size: 1.1rem;
    color: var(--text-primary);
}

.cdash-topbar-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Topbar — Control Buttons */
.topbar-control-btn {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-card);
    border: 1px solid var(--border);
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition-fast);
    text-decoration: none;
    position: relative;
    font-size: 0.95rem;
}

.topbar-control-btn:hover {
    background: var(--accent-soft);
    color: var(--accent);
    border-color: var(--accent);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px var(--accent-glow);
}

/* Theme Toggle Icons */
body.dark-mode .topbar-control-btn .fa-moon { display: none; }
body:not(.dark-mode) .topbar-control-btn .fa-sun { display: none; }
body.dark-mode .topbar-control-btn .fa-sun { color: #fbbf24; }

/* Sidebar Theme Button */
body.dark-mode .cdash-theme-btn .fa-moon { display: none; }
body:not(.dark-mode) .cdash-theme-btn .fa-sun { display: none; }

/* Language Switcher */
.lang-switcher-wrapper {
    position: relative;
}

.lang-dropdown-menu {
    display: none;
    position: absolute;
    top: calc(100% + 10px);
    z-index: 1001;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    min-width: 155px;
    overflow: hidden;
    animation: agDropIn 0.25s ease;
}

html[dir="ltr"] .lang-dropdown-menu { right: 0; }
html[dir="rtl"] .lang-dropdown-menu { left: 0; }

.lang-dropdown-menu.show {
    display: block;
}

@keyframes agDropIn {
    from { opacity: 0; transform: translateY(-8px) scale(0.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.lang-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    color: var(--text-primary);
    text-decoration: none;
    font-size: 0.88rem;
    font-weight: 600;
    transition: var(--transition-fast);
}

.lang-item:hover {
    background: var(--bg-body);
    color: var(--accent);
}

.lang-item.active {
    background: var(--accent-soft);
    color: var(--accent);
}

/* ═══════════════════════════
   CONTENT AREA
   ═══════════════════════════ */
.cdash-content {
    flex: 1;
    padding: 28px;
    max-width: 100%;
}

/* ═══════════════════════════
   FLASH MESSAGES
   ═══════════════════════════ */
.cdash-flash {
    padding: 14px 20px;
    border-radius: var(--radius-md);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    animation: agSlideIn 0.4s ease;
}

@keyframes agSlideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.cdash-flash-success {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border: 1px solid #86efac;
    color: #166534;
}

.cdash-flash-error {
    background: linear-gradient(135deg, #fef2f2, #fee2e2);
    border: 1px solid #fca5a5;
    color: #991b1b;
}

body.dark-mode .cdash-flash-success {
    background: rgba(16, 185, 129, 0.1);
    border-color: rgba(16, 185, 129, 0.3);
    color: #6ee7b7;
}

body.dark-mode .cdash-flash-error {
    background: rgba(239, 68, 68, 0.1);
    border-color: rgba(239, 68, 68, 0.3);
    color: #fca5a5;
}

/* ═══════════════════════════
   PAGINATION
   ═══════════════════════════ */
.pagination-wrapper {
    margin-top: 40px;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    gap: 6px;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
    justify-content: center;
}

.page-item .page-link {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-md);
    background: var(--bg-card);
    border: 1px solid var(--border);
    color: var(--text-muted);
    font-weight: 700;
    text-decoration: none;
    transition: var(--transition-fast);
    font-size: 0.9rem;
}

.page-item.active .page-link {
    background: var(--accent);
    color: #fff;
    border-color: var(--accent);
    box-shadow: 0 6px 16px var(--accent-glow);
}

.page-item.disabled .page-link {
    opacity: 0.4;
    cursor: not-allowed;
    background: var(--bg-body);
}

.page-item:not(.active):not(.disabled) .page-link:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: var(--accent-soft);
    transform: translateY(-2px);
}

/* ═══════════════════════════
   MOBILE OVERLAY
   ═══════════════════════════ */
.cdash-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    z-index: 999;
    opacity: 0;
    transition: opacity var(--transition-smooth);
}

.cdash-overlay.visible {
    display: block;
    opacity: 1;
}

/* Mobile Burger Button */
.cdash-burger {
    display: none;
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    background: var(--bg-card);
    color: var(--text-primary);
    cursor: pointer;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    transition: var(--transition-fast);
}

.cdash-burger:hover {
    background: var(--accent-soft);
    color: var(--accent);
    border-color: var(--accent);
}

/* ═══════════════════════════
   RESPONSIVE — TABLET LANDSCAPE (≤1280px)
   ═══════════════════════════ */
@media (max-width: 1280px) {
    :root {
        --sidebar-width: 250px;
    }

    .cdash-content {
        padding: 24px;
    }
}

/* ═══════════════════════════
   RESPONSIVE — TABLET (≤1024px)
   ═══════════════════════════ */
@media (max-width: 1024px) {
    :root {
        --sidebar-width: 240px;
    }

    .cdash-content {
        padding: 20px;
    }

    .cdash-topbar {
        padding: 0 20px;
    }
}

/* ═══════════════════════════
   RESPONSIVE — TABLET PORTRAIT / LARGE PHONE (≤768px)
   ═══════════════════════════ */
@media (max-width: 768px) {
    .cdash-sidebar {
        width: 280px;
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

    .cdash-burger {
        display: flex;
    }

    .cdash-content {
        padding: 16px;
    }

    .cdash-topbar {
        padding: 0 16px;
        height: 60px;
    }

    .cdash-topbar-title {
        font-size: 0.95rem;
    }
}

/* ═══════════════════════════
   RESPONSIVE — SMALL PHONE (≤480px)
   ═══════════════════════════ */
@media (max-width: 480px) {
    .cdash-sidebar {
        width: 100%;
    }

    .cdash-content {
        padding: 12px;
    }

    .cdash-topbar {
        padding: 0 12px;
        height: 56px;
    }

    .cdash-topbar-title {
        font-size: 0.88rem;
    }

    .topbar-control-btn {
        width: 36px;
        height: 36px;
        font-size: 0.85rem;
    }

    .cdash-topbar-actions {
        gap: 6px;
    }

    .page-item .page-link {
        width: 36px;
        height: 36px;
        font-size: 0.8rem;
    }
}

/* ═══════════════════════════
   UTILITY: d-flex, gap, etc.
   ═══════════════════════════ */
.d-flex { display: flex; }
.align-items-center { align-items: center; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }

</style>
</head>
<body>
<div class="cdash-overlay" id="cdashOverlay" onclick="closeSidebar()"></div>

<div class="cdash-wrapper">
    <aside class="cdash-sidebar" id="cdashSidebar">

    {{-- Top Logo --}}
    <div class="cdash-sidebar-brand">
        <a href="{{ url('/') }}">
            <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="{{ config('app.name') }}">
        </a>
    </div>

    {{-- User Card --}}
    <div class="cdash-user-card">

        <div style="position:relative; flex-shrink:0;">

            <img class="cdash-avatar"
                 src="{{ auth()->user()->profile_photo_url }}"
                 alt="{{ auth()->user()->full_name }}">

            @if(auth()->user()->company && auth()->user()->company->logo)
                <img
                    src="{{ auth()->user()->company->logo_url }}"
                    style="width:20px;height:20px;border-radius:50%;position:absolute;bottom:-2px;right:-2px;border:2px solid #0f172a"
                    alt="{{ auth()->user()->company->name }}">
            @endif

        </div>

        <div class="cdash-user-meta">
            <div class="cdash-user-name">{{ auth()->user()->full_name }}</div>

            <div class="cdash-user-role">
                {{ __('Agent') }}
                @if(auth()->user()->company)
                    · {{ auth()->user()->company->localized_name }}
                @endif
            </div>
        </div>

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

        <button id="sidebarThemeBtn" class="cdash-theme-btn" onclick="toggleDark()">
            <i class="fas fa-moon"></i>
            <i class="fas fa-sun"></i>
            <span>{{ __('Toggle Theme') }}</span>
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
            <div class="cdash-topbar-left d-flex align-items-center gap-3">
                <button class="cdash-burger" onclick="toggleSidebar()" aria-label="Toggle Menu">
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
                    <button type="button" class="topbar-control-btn" onclick="toggleLangMenu()" id="langBtn" title="{{ __('Language') }}">
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
// ═══════════════════════════
// Sidebar Toggle (Mobile)
// ═══════════════════════════
function toggleSidebar() {
    const sidebar = document.getElementById('cdashSidebar');
    const overlay = document.getElementById('cdashOverlay');
    sidebar.classList.toggle('open');
    overlay.classList.toggle('visible');
    document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
}

function closeSidebar() {
    const sidebar = document.getElementById('cdashSidebar');
    const overlay = document.getElementById('cdashOverlay');
    sidebar.classList.remove('open');
    overlay.classList.remove('visible');
    document.body.style.overflow = '';
}

// ═══════════════════════════
// Dark Mode Toggle
// ═══════════════════════════
function toggleDark() {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('agentDashDark', isDark ? '1' : '0');
}

// Restore dark mode preference on load
(function() {
    if (localStorage.getItem('agentDashDark') === '1') {
        document.body.classList.add('dark-mode');
    }
})();

// ═══════════════════════════
// Language Dropdown
// ═══════════════════════════
function toggleLangMenu() {
    document.getElementById('langMenu').classList.toggle('show');
}

// Close dropdown on outside click
document.addEventListener('click', function(e) {
    const langWrapper = document.querySelector('.lang-switcher-wrapper');
    const langMenu = document.getElementById('langMenu');
    if (langWrapper && langMenu && !langWrapper.contains(e.target)) {
        langMenu.classList.remove('show');
    }
});

// ═══════════════════════════
// Close sidebar on Escape key
// ═══════════════════════════
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSidebar();
        const langMenu = document.getElementById('langMenu');
        if (langMenu) langMenu.classList.remove('show');
    }
});

// ═══════════════════════════
// Close sidebar on window resize to desktop
// ═══════════════════════════
window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        closeSidebar();
    }
});
</script>

{{-- Core JS Libraries --}}
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

<script>
// ═══════════════════════════
// Global AJAX Setup
// ═══════════════════════════
$(document).ready(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
});

// ═══════════════════════════
// Global submitAjaxForm Helper
// ═══════════════════════════
window.submitAjaxForm = function({
    formId,
    url,
    method = "POST",
    modalId = null,
    table = null,
    successMessage = "{{ __('Saved successfully') }}",
    buttonText = "{{ __('Save') }}",
    usePut = false,
    resetSelect2 = false,
    useSweetAlert = false,
    onSuccess = null
}) {
    const form = document.getElementById(formId);
    let formData = new FormData(form);

    if (usePut) {
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url: url,
        type: method,
        data: formData,
        processData: false,
        contentType: false,

        beforeSend: function () {
            $(`#${formId}`).find('button[type="submit"]')
                .prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin"></i>');
        },

        success: function (response) {
            if (response.success) {
                if (modalId) {
                    $(`#${modalId}`).modal('hide');
                }
                if (form) form.reset();

                if (useSweetAlert) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message ?? successMessage,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    toastr.success(response.message ?? successMessage);
                }

                if (typeof onSuccess === 'function') {
                    onSuccess(response);
                }
            } else {
                toastr.error(response.message || "{{ __('Something went wrong') }}");
            }
        },

        error: function (xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON?.errors ?? {};
                Object.values(errors).forEach(err => {
                    toastr.error(err[0]);
                });
            } else {
                toastr.error("{{ __('Something went wrong') }}");
            }
        },

        complete: function () {
            $(`#${formId}`).find('button[type="submit"]')
                .prop('disabled', false)
                .html(buttonText || "{{ __('Save') }}");
        }
    });
};
</script>

@stack('scripts')
</body>
</html>
