<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Dashboard')) - {{ config('app.name') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    
    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/frontend/app.css') }}?v={{ time() }}">
    <link href="{{ asset('icons/font-awesome/css/all.min.css') }}" rel="stylesheet">
    @if(app()->getLocale() === 'ar')
    <link rel="stylesheet" href="{{ asset('css/frontend/rtl.css') }}">
    @endif

    @stack('styles')

    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --topbar-height: 70px;
            --accent-color: #0f172a;
            --primary-blue: #2563eb;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --sidebar-bg: #0f172a;
        }

        body.dark-mode {
            --bg-main: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --sidebar-bg: #020617;
        }

        * { box-sizing: border-box; }
        body { 
            margin: 0; 
            font-family: 'Tajawal', sans-serif; 
            background: var(--bg-main); 
            color: var(--text-main); 
            transition: background 0.3s, color 0.3s;
            overflow-x: hidden;
        }

        /* Prevent icons from using Tajawal */
        i, .fas, .far, .fab, .fa {
            font-family: "Font Awesome 5 Free", "Font Awesome 5 Brands" !important;
        }

        .cdash-wrapper { display: flex; min-height: 100vh; }

        /* Sidebar Styles */
        .cdash-sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            color: #cbd5e1;
            position: fixed;
            inset-inline-start: 0; top: 0;
            display: flex;
            flex-direction: column;
            padding: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1100;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        }

        .cdash-sidebar.collapsed { width: var(--sidebar-collapsed-width); }

        .cdash-sidebar.collapsed .cdash-sidebar-brand span,
        .cdash-sidebar.collapsed .cdash-link span,
        .cdash-sidebar.collapsed .cdash-label,
        .cdash-sidebar.collapsed .cdash-user-meta,
        .cdash-sidebar.collapsed .cdash-badge,
        .cdash-sidebar.collapsed .cdash-mini-widgets {
            display: none;
        }

        .cdash-sidebar.collapsed .cdash-link { justify-content: center; padding: 12px; }
        .cdash-sidebar.collapsed .cdash-link i { font-size: 1.25rem; margin: 0; }
        .cdash-sidebar.collapsed .cdash-user-card { padding: 8px; justify-content: center; }

        .cdash-sidebar-brand { padding: 10px 0 30px; text-align: center; }
        .cdash-sidebar-brand img { height: 40px; object-fit: contain; }

        .cdash-user-card {
            display: flex; align-items: center; gap: 12px;
            background: rgba(255,255,255,0.05);
            padding: 12px; border-radius: 12px; margin-bottom: 20px;
            transition: 0.3s;
        }
        .cdash-avatar { width: 42px; height: 42px; border-radius: 10px; object-fit: cover; }
        .cdash-user-meta { flex: 1; min-width: 0; }
        .user-name { display: block; font-weight: 700; font-size: 0.85rem; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 0.7rem; color: #64748b; }

        .cdash-label { color: #475569; font-size: 0.65rem; font-weight: 900; text-transform: uppercase; letter-spacing: 1.5px; margin: 25px 0 10px 10px; }

        .cdash-nav { flex: 1; overflow-y: auto; padding-bottom: 20px; }
        .cdash-nav::-webkit-scrollbar { width: 4px; }
        .cdash-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

        .cdash-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 15px; border-radius: 10px;
            text-decoration: none; color: #94a3b8;
            margin-bottom: 4px; transition: 0.2s;
            position: relative;
        }
        .cdash-link:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .cdash-link.active { background: var(--primary-blue); color: #fff; }
        .cdash-link i { width: 22px; text-align: center; font-size: 1.1rem; }

        .cdash-badge { margin-inline-start: auto; background: #ef4444; color: #fff; font-size: 0.65rem; padding: 2px 7px; border-radius: 10px; font-weight: 700; }

        .cdash-mini-widgets { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 15px; }
        .cdash-mini-widgets .widget { background: rgba(255,255,255,0.03); padding: 8px; border-radius: 8px; text-align: center; }
        .cdash-mini-widgets .widget span { display: block; font-size: 0.6rem; color: #64748b; margin-bottom: 2px; }
        .cdash-mini-widgets .widget strong { font-size: 0.8rem; color: #fff; }

        .cdash-footer { margin-top: auto; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.05); }
        .cdash-theme-btn, .cdash-logout { 
            width: 100%; background: none; border: none; color: #94a3b8; padding: 10px 15px; 
            border-radius: 10px; cursor: pointer; text-align: start; display: flex; align-items: center; gap: 12px; font-weight: 600; transition: 0.2s;
        }
        .cdash-theme-btn:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .cdash-logout:hover { background: rgba(239, 68, 68, 0.1); color: #fca5a5; }

        /* Main Area */
        .cdash-main {
            flex: 1; margin-inline-start: var(--sidebar-width);
            min-height: 100vh; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .cdash-sidebar.collapsed + .cdash-main { margin-inline-start: var(--sidebar-collapsed-width); }

        .cdash-topbar {
            height: var(--topbar-height); background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 0 25px; display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }

        .cdash-topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: flex-end;
            flex-wrap: nowrap;
        }

        .cdash-topbar-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(0,0,0,0.02);
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        body.dark-mode .cdash-topbar-btn {
            background: rgba(255,255,255,0.05);
        }

        .cdash-topbar-btn:hover {
            color: var(--primary-blue);
            background: rgba(37, 99, 235, 0.08);
            border-color: rgba(37, 99, 235, 0.2);
            transform: translateY(-2px);
        }

        .cdash-topbar-title { font-weight: 800; font-size: 1.1rem; }

        .notif-badge { position: absolute; top: 6px; right: 6px; background: #ef4444; color: #fff; font-size: 0.6rem; min-width: 18px; height: 18px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: 2px solid var(--bg-card); font-weight: 800; }

        .cdash-content { padding: 30px; }

        /* Overlays & Mobile */
        .cdash-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; backdrop-filter: blur(2px); transition: 0.3s; }
        .cdash-burger { display: none; background: none; border: none; font-size: 1.4rem; color: var(--text-main); cursor: pointer; padding: 0 10px 0 0; }
        [dir="rtl"] .cdash-burger { padding: 0 0 0 10px; }

        @media (max-width: 991px) {
            .cdash-sidebar { inset-inline-start: calc(-1 * var(--sidebar-width)); }
            .cdash-sidebar.open { inset-inline-start: 0; }
            .cdash-main { margin-inline-start: 0 !important; }
            .cdash-burger { display: block; }
            .cdash-overlay.visible { display: block; }
        }

        /* Notif Dropdown */
        .notif-dropdown {
            display: none; position: absolute; top: calc(100% + 12px); inset-inline-end: 0;
            width: 360px; background: var(--bg-card); border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.18), 0 0 1px rgba(0,0,0,0.1); 
            border: 1px solid var(--border-color);
            z-index: 1200; overflow: hidden;
            transform-origin: top right;
        }
        .notif-dropdown.show { display: block; animation: zoomIn 0.25s cubic-bezier(0.2, 0, 0, 1.2); }
        @keyframes zoomIn { from { opacity: 0; transform: scale(0.95) translateY(-5px); } to { opacity: 1; transform: scale(1) translateY(0); } }

        .notif-header { 
            padding: 20px 24px; border-bottom: 1px solid var(--border-color); 
            background: rgba(0,0,0,0.01);
            display: flex; justify-content: space-between; align-items: center;
        }
        .notif-header span { font-weight: 800; font-size: 1rem; color: var(--text-main); }
        
        .notif-item { 
            padding: 16px 24px; border-bottom: 1px solid var(--border-color); 
            transition: 0.2s; position: relative;
        }
        .notif-item:last-child { border-bottom: none; }
        .notif-item:hover { background: rgba(37, 99, 235, 0.04); }
        
        .notif-icon-box {
            width: 40px; height: 40px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(37, 99, 235, 0.1); color: var(--primary-blue);
            flex-shrink: 0;
        }
        
        .notif-title { font-size: 0.88rem; font-weight: 700; color: var(--text-main); margin-bottom: 3px; line-height: 1.4; }
        .notif-time { font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 5px; }

        .notif-footer-btn {
            display: block; width: 100%; padding: 15px; text-align: center;
            background: rgba(37, 99, 235, 0.02); color: var(--primary-blue);
            font-weight: 700; font-size: 0.85rem; border: none; border-top: 1px solid var(--border-color);
            cursor: pointer; transition: 0.2s;
        }
        .notif-footer-btn:hover { background: var(--primary-blue); color: #fff; }

        /* Animation Classes */
        .fade-in-up { animation: fadeInUp 0.4s ease-out; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

        /* Dark Mode Switch Smoothness */
        body { transition: background-color 0.3s, border-color 0.3s, color 0.3s; }
    </style>
</head>
<body>
    <script>
        if (localStorage.getItem('dark-mode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    </script>

    {{-- Mobile Overlay --}}
    <div class="cdash-overlay" id="cdashOverlay" onclick="closeSidebar()"></div>

    <div class="cdash-wrapper">

        {{-- ─── Sidebar ─── --}}
        <aside class="cdash-sidebar" id="cdashSidebar">
            
            <div class="cdash-sidebar-brand">
                <a href="{{ url('/') }}">
                    <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="{{ config('app.name') }}">
                </a>
            </div>

            <div class="cdash-user-card">
                <img class="cdash-avatar" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->full_name }}">
                <div class="cdash-user-meta">
                    <span class="user-name">{{ auth()->user()->full_name }}</span>
                    <span class="user-role">{{ __('Subscriber') }}</span>
                </div>
            </div>

            <div class="cdash-mini-widgets">
                <div class="widget">
                    <span>{{ __('Bookings') }}</span>
                    <strong>{{ auth()->user()->bookings()->count() + auth()->user()->tripBookings()->count() + auth()->user()->hotelBookings()->count() }}</strong>
                </div>
                <div class="widget">
                    <span>{{ __('Unread') }}</span>
                    <strong>{{ auth()->user()->unreadNotifications()->count() }}</strong>
                </div>
            </div>

            <nav class="cdash-nav">
                <span class="cdash-label">{{ __('Main Menu') }}</span>

                <a href="{{ route('customer.dashboard') }}" class="cdash-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-columns"></i>
                    <span>{{ __('Dashboard') }}</span>
                </a>

                <a href="{{ route('customer.bookings.trips') }}" class="cdash-link {{ request()->routeIs('customer.bookings.trips') ? 'active' : '' }}">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>{{ __('Trip Bookings') }}</span>
                </a>

                <a href="{{ route('customer.bookings.flights') }}" class="cdash-link {{ request()->routeIs('customer.bookings.flights') ? 'active' : '' }}">
                    <i class="fas fa-plane"></i>
                    <span>{{ __('Flight Bookings') }}</span>
                </a>

                <a href="{{ route('customer.bookings.hotels') }}" class="cdash-link {{ request()->routeIs('customer.bookings.hotels') ? 'active' : '' }}">
                    <i class="fas fa-hotel"></i>
                    <span>{{ __('Hotel Bookings') }}</span>
                </a>

                <a href="{{ route('customer.favorites.index') }}" class="cdash-link {{ request()->routeIs('customer.favorites.*') ? 'active' : '' }}">
                    <i class="fas fa-heart"></i>
                    <span>{{ __('Favorites') }}</span>
                </a>

                <a href="{{ route('customer.notifications.index') }}" class="cdash-link {{ request()->routeIs('customer.notifications.*') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i>
                    <span>{{ __('Notifications') }}</span>
                    @if(auth()->user()->unreadNotifications()->count() > 0)
                        <span class="cdash-badge">{{ auth()->user()->unreadNotifications()->count() }}</span>
                    @endif
                </a>

                <span class="cdash-label">{{ __('Account') }}</span>

                <a href="{{ route('customer.profile') }}" class="cdash-link {{ request()->routeIs('customer.profile*') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ __('Profile Settings') }}</span>
                </a>

                <a href="{{ route('customer.wallet.index') }}" class="cdash-link {{ request()->routeIs('customer.wallet.*') ? 'active' : '' }}">
                    <i class="fas fa-wallet"></i>
                    <span>{{ __('My Wallet') }}</span>
                </a>

                <a href="{{ route('customer.support.index') }}" class="cdash-link {{ request()->routeIs('customer.support.*') ? 'active' : '' }}">
                    <i class="fas fa-headset"></i>
                    <span>{{ __('My Tickets') }}</span>
                </a>

                <a href="{{ route('customer.payments.index') }}" class="cdash-link {{ request()->routeIs('customer.payments.*') ? 'active' : '' }}">
                    <i class="fas fa-receipt"></i>
                    <span>{{ __('Payments') }}</span>
                </a>
            </nav>

            <div class="cdash-footer">
                <button onclick="toggleDarkMode()" class="cdash-theme-btn">
                    <i class="fas fa-moon"></i>
                    <span>{{ __('Dark Mode') }}</span>
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

        {{-- ─── Main Content ─── --}}
        <div class="cdash-main">
            
            {{-- Top Bar --}}
            <header class="cdash-topbar">
                <div class="d-flex align-items-center">
                    <button class="cdash-burger" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="cdash-topbar-title">@yield('page-title', __('Dashboard'))</span>
                </div>

                <div class="cdash-topbar-actions">
                    
                    {{-- Quick Theme Toggle --}}
                    <button onclick="toggleDarkMode()" class="cdash-topbar-btn d-none d-md-flex" title="{{ __('Toggle Dark Mode') }}">
                        <i class="fas fa-adjust"></i>
                    </button>

                    {{-- Notifications --}}
                    <div class="notification-wrapper position-relative">
                        <button class="cdash-topbar-btn" onclick="toggleNotif(event)" title="{{ __('Notifications') }}">
                            <i class="fas fa-bell"></i>
                            @if(auth()->user()->unreadNotifications()->count() > 0)
                                <span class="notif-badge">{{ auth()->user()->unreadNotifications()->count() }}</span>
                            @endif
                        </button>

                        <div class="notif-dropdown shadow" id="notifDropdown">
                            <div class="notif-header">
                                <span>{{ __('Notifications') }}</span>
                                <a href="{{ route('customer.notifications.index') }}" class="small text-primary text-decoration-none">{{ __('View all') }}</a>
                            </div>
                            
                            <div class="notif-list" style="max-height: 380px; overflow-y: auto;">
                                @if(auth()->user()->unreadNotifications()->count() > 0)
                                    @foreach(auth()->user()->unreadNotifications()->limit(5)->get() as $notif)
                                        <div class="notif-item d-flex gap-3 align-items-start">
                                            <div class="notif-icon-box">
                                                <i class="fas fa-bell" style="font-size: 0.9rem;"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="notif-title">{{ $notif->title }}</div>
                                                <div class="notif-time">
                                                    <i class="far fa-clock"></i> {{ $notif->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="p-5 text-center">
                                        <i class="fas fa-bell-slash d-block mb-3 font-size-xxl text-muted opacity-25"></i>
                                        <div class="small text-muted">{{ __('No unread notifications') }}</div>
                                    </div>
                                @endif
                            </div>

                            @if(auth()->user()->unreadNotifications()->count() > 0)
                                <form action="{{ route('customer.notifications.read-all') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="notif-footer-btn">
                                        <i class="fas fa-check-double me-1"></i> {{ __('Mark all as read') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Language --}}
                    <div class="dropdown position-relative">
                        <button class="cdash-topbar-btn" onclick="toggleLang(event)">
                            <i class="fas fa-globe"></i>
                        </button>
                        <div class="cdash-lang-dropdown shadow" style="display: none; position: absolute; top: 100%; inset-inline-end: 0; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; min-width: 150px; z-index: 1200; margin-top: 10px; overflow: hidden;">
                                <a href="{{ route('lang.switch', 'ar') }}" style="display: block; padding: 12px 18px; color: var(--text-main); text-decoration: none; font-size: .85rem; @if(app()->getLocale() === 'ar') background: rgba(0,0,0,0.03); font-weight: 700; @endif border-bottom: 1px solid var(--border-color);">العربية</a>
                                <a href="{{ route('lang.switch', 'en') }}" style="display: block; padding: 12px 18px; color: var(--text-main); text-decoration: none; font-size: .85rem; @if(app()->getLocale() === 'en') background: rgba(0,0,0,0.03); font-weight: 700; @endif">English</a>
                        </div>
                    </div>

                    {{-- Logout Mobile Link --}}
                    <form method="POST" action="{{ route('logout') }}" class="d-flex d-md-none m-0">
                        @csrf
                        <button type="submit" class="cdash-topbar-btn text-danger">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>

                </div>
            </header>

            {{-- Main Content Window --}}
            <main class="cdash-content fade-in-up">
                
                {{-- Flash Messages --}}
                @foreach (['success', 'error', 'info'] as $msg)
                    @if(session($msg))
                        <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-{{ $msg === 'success' ? 'check-circle' : ($msg === 'error' ? 'exclamation-circle' : 'info-circle') }}"></i>
                                <div>{{ session($msg) }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                @endforeach

                @yield('content')
            </main>

        </div>
    </div>

    {{-- JS Scripts --}}
    <script>
        // Sidebar Control
        function toggleSidebar() {
            const sidebar = document.getElementById('cdashSidebar');
            const overlay = document.getElementById('cdashOverlay');
            
            if (window.innerWidth > 991) {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
            } else {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('visible');
            }
        }

        function closeSidebar() {
            document.getElementById('cdashSidebar').classList.remove('open');
            document.getElementById('cdashOverlay').classList.remove('visible');
        }

        // Dropdowns Control
        function toggleNotif(event) {
            if(event) event.stopPropagation();
            const dropdown = document.getElementById('notifDropdown');
            const lang = document.querySelector('.cdash-lang-dropdown');
            
            dropdown.classList.toggle('show');
            lang.style.display = 'none';
        }

        function toggleLang(event) {
            if(event) event.stopPropagation();
            const lang = document.querySelector('.cdash-lang-dropdown');
            const dropdown = document.getElementById('notifDropdown');
            
            lang.style.display = lang.style.display === 'block' ? 'none' : 'block';
            dropdown.classList.remove('show');
        }

        // Dark Mode Control
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('dark-mode', isDark);
            
            // Icon transition
            const themeBtnIcon = document.querySelector('.cdash-theme-btn i');
            if(themeBtnIcon) {
                themeBtnIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
                document.querySelector('.cdash-theme-btn span').innerText = isDark ? "{{ __('Light Mode') }}" : "{{ __('Dark Mode') }}";
            }
        }

        // Global Click to Close Dropdowns
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.notification-wrapper')) {
                document.getElementById('notifDropdown').classList.remove('show');
            }
            if (!e.target.closest('.dropdown')) {
                document.querySelector('.cdash-lang-dropdown').style.display = 'none';
            }
        });

        // Initialize State
        document.addEventListener('DOMContentLoaded', () => {
            // Restore Sidebar
            if (localStorage.getItem('sidebar-collapsed') === 'true' && window.innerWidth > 991) {
                document.getElementById('cdashSidebar').classList.add('collapsed');
            }
            
            // Restore Dark Mode
            if (localStorage.getItem('dark-mode') === 'true') {
                document.body.classList.add('dark-mode');
                const themeBtnIcon = document.querySelector('.cdash-theme-btn i');
                if(themeBtnIcon) {
                    themeBtnIcon.className = 'fas fa-sun';
                    document.querySelector('.cdash-theme-btn span').innerText = "{{ __('Light Mode') }}";
                }
            }
        });

        // Responsive handling
        window.addEventListener('resize', () => {
            if (window.innerWidth > 991) {
                closeSidebar();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
