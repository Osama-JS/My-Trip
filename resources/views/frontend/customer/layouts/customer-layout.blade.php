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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
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
            height: var(--topbar-height); 
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-color);
            padding: 0 25px; display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.01);
        }
        body.dark-mode .cdash-topbar {
            background: rgba(15, 23, 42, 0.8) !important;
        }
        .cdash-topbar.scrolled {
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.08);
            background: rgba(255, 255, 255, 0.9) !important;
            border-bottom-color: rgba(226, 232, 240, 0.5);
        }
        body.dark-mode .cdash-topbar.scrolled {
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
            background: rgba(15, 23, 42, 0.9) !important;
            border-bottom-color: rgba(51, 65, 85, 0.5);
        }

        .cdash-topbar-title-wrapper {
            display: flex;
            flex-direction: column;
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
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
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

        .cdash-topbar-title { font-weight: 900; font-size: 1.15rem; color: var(--text-main); line-height: 1.2; }
        .cdash-topbar-subtitle { font-size: 0.75rem; color: var(--text-muted); font-weight: 500; margin-top: 2px; }

        /* Topbar User Avatar button */
        .topbar-avatar-btn {
            padding: 2px;
            border-radius: 50%;
            border: 2px solid var(--border-color);
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            background: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .topbar-avatar-btn:hover {
            border-color: var(--primary-blue);
            transform: translateY(-2px);
        }
        .topbar-avatar-btn img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Notification Icon sway animation */
        @keyframes bellSway {
            0%, 100% { transform: rotate(0); }
            15% { transform: rotate(10deg); }
            30% { transform: rotate(-10deg); }
            45% { transform: rotate(5deg); }
            60% { transform: rotate(-5deg); }
            75% { transform: rotate(2deg); }
            85% { transform: rotate(-2deg); }
        }
        .cdash-topbar-btn.has-notifications i {
            animation: bellSway 2.5s ease-in-out infinite;
            transform-origin: top center;
        }

        /* Badge pulsing animation */
        @keyframes badgePulse {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
            70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        .notif-badge { 
            position: absolute; top: 6px; right: 6px; background: #ef4444; color: #fff; 
            font-size: 0.6rem; min-width: 18px; height: 18px; border-radius: 10px; 
            display: flex; align-items: center; justify-content: center; 
            border: 2px solid var(--bg-card); font-weight: 800; 
            animation: badgePulse 1.8s infinite;
        }

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

        /* Dropdowns Styling */
        .notif-dropdown, .cdash-lang-dropdown, .cdash-user-dropdown {
            display: none; position: absolute; top: calc(100% + 12px); inset-inline-end: 0;
            background: rgba(255, 255, 255, 0.9); border-radius: 20px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.12); 
            border: 1px solid var(--border-color);
            z-index: 1200; overflow: hidden;
            transform-origin: top right;
        }
        body.dark-mode .notif-dropdown, body.dark-mode .cdash-lang-dropdown, body.dark-mode .cdash-user-dropdown {
            background: rgba(30, 41, 59, 0.95);
            border-color: var(--border-color);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
        }
        
        .notif-dropdown { width: 360px; }
        .cdash-lang-dropdown { min-width: 160px; }
        .cdash-user-dropdown { width: 280px; }

        .notif-dropdown.show, .cdash-lang-dropdown.show, .cdash-user-dropdown.show { 
            display: block; 
            animation: zoomIn 0.25s cubic-bezier(0.2, 0, 0, 1.2); 
        }

        .cdash-lang-dropdown a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            color: var(--text-main);
            text-decoration: none !important;
            font-size: .88rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .cdash-lang-dropdown a:hover {
            background: rgba(37, 99, 235, 0.08);
            color: var(--primary-blue);
        }
        .cdash-lang-dropdown a.active {
            background: rgba(37, 99, 235, 0.04);
            color: var(--primary-blue);
            font-weight: 800;
        }

        /* User Dropdown Details */
        .cdash-user-dropdown-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(0,0,0,0.01);
        }
        .cdash-user-dropdown-header img {
            width: 42px; height: 42px; border-radius: 50%; object-fit: cover;
            border: 2px solid var(--primary-blue);
        }
        .cdash-user-dropdown-meta {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .cdash-user-dropdown-name {
            font-weight: 800; font-size: 0.88rem; color: var(--text-main);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .cdash-user-dropdown-email {
            font-size: 0.72rem; color: var(--text-muted);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        
        .cdash-user-dropdown-wallet {
            margin: 12px 15px 6px 15px;
            padding: 10px 14px;
            background: rgba(37, 99, 235, 0.06);
            border: 1px dashed rgba(37, 99, 235, 0.2);
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cdash-user-dropdown-wallet span {
            font-size: 0.72rem; font-weight: 700; color: var(--text-muted);
        }
        .cdash-user-dropdown-wallet strong {
            font-size: 0.82rem; color: var(--primary-blue); font-weight: 800;
        }
        
        .cdash-user-dropdown-links {
            padding: 8px;
        }
        .cdash-user-dropdown-links a, .cdash-user-dropdown-links button {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 12px;
            color: var(--text-main);
            text-decoration: none !important;
            font-size: 0.82rem;
            font-weight: 600;
            border-radius: 10px;
            border: none;
            background: none;
            text-align: start;
            cursor: pointer;
            transition: all 0.2s;
        }
        .cdash-user-dropdown-links a:hover, .cdash-user-dropdown-links button:hover {
            background: rgba(37, 99, 235, 0.08);
            color: var(--primary-blue);
        }
        .cdash-user-dropdown-links button.logout-btn {
            color: #ef4444;
            border-top: 1px solid var(--border-color);
            border-radius: 0;
            margin-top: 4px;
            padding-top: 12px;
        }
        .cdash-user-dropdown-links button.logout-btn:hover {
            background: rgba(239, 68, 68, 0.08);
            color: #ef4444;
        }

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

        /* Spotlight Search Modal */
        .search-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            z-index: 2000;
            align-items: flex-start;
            justify-content: center;
            padding: 80px 20px;
            animation: fadeIn 0.25s ease-out;
        }
        .search-modal-backdrop.open {
            display: flex;
        }
        
        .search-modal-content {
            width: 100%;
            max-width: 650px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            animation: slideDown 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        body.dark-mode .search-modal-content {
            background: rgba(30, 41, 59, 0.96);
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.5);
        }
        .search-modal-content:focus-within {
            border-color: rgba(37, 99, 235, 0.4);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1), 0 30px 70px rgba(0, 0, 0, 0.2);
        }
        body.dark-mode .search-modal-content:focus-within {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15), 0 30px 70px rgba(0, 0, 0, 0.6);
        }

        .search-modal-header {
            display: flex;
            align-items: center;
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            position: relative;
        }
        
        .search-modal-icon-wrapper {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-inline-end: 14px;
            transition: all 0.25s ease;
            color: var(--text-muted);
        }
        body.dark-mode .search-modal-icon-wrapper {
            background: rgba(255, 255, 255, 0.03);
        }
        .search-modal-content:focus-within .search-modal-icon-wrapper {
            border-color: rgba(37, 99, 235, 0.3);
            background: rgba(37, 99, 235, 0.05);
            color: var(--primary-blue);
        }

        .search-modal-icon {
            font-size: 1rem;
            transition: color 0.2s;
        }

        #globalSearchInput {
            flex: 1;
            border: none;
            background: none;
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-main);
            outline: none;
            font-family: inherit;
        }
        #globalSearchInput::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
        }

        .clear-query-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 6px;
            font-size: 1rem;
            margin-inline-end: 12px;
            transition: color 0.2s, transform 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .clear-query-btn:hover {
            color: #ef4444;
            transform: scale(1.1);
        }

        .search-close-btn {
            background: none;
            border: none;
            font-size: 1.6rem;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0;
            line-height: 1;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .search-close-btn:hover {
            color: #ef4444;
        }

        .search-modal-body {
            max-height: 400px;
            overflow-y: auto;
            padding: 24px;
        }
        .search-modal-body::-webkit-scrollbar {
            width: 6px;
        }
        .search-modal-body::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 10px;
        }

        .search-section {
            margin-bottom: 24px;
        }
        .search-section:last-child {
            margin-bottom: 0;
        }
        
        .search-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .search-section-title {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
        }
        .clear-history-link {
            font-size: 0.72rem;
            color: var(--primary-blue);
            text-decoration: none !important;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .clear-history-link:hover {
            opacity: 0.8;
        }

        .search-results-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .search-result-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: 16px;
            text-decoration: none !important;
            color: var(--text-main);
            transition: all 0.2s ease;
            background: rgba(0,0,0,0.01);
            border: 1px solid transparent;
            position: relative;
        }
        .search-result-item:hover, .search-result-item.active {
            background: rgba(37, 99, 235, 0.08);
            border-color: rgba(37, 99, 235, 0.15);
            transform: translateX(4px);
        }
        [dir="rtl"] .search-result-item:hover, [dir="rtl"] .search-result-item.active {
            transform: translateX(-4px);
        }

        .search-result-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-inline-end: 14px;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .bg-blue-soft { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
        .bg-purple-soft { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
        .bg-green-soft { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .bg-orange-soft { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .bg-teal-soft { background: rgba(20, 184, 166, 0.1); color: #14b8a6; }
        .bg-rose-soft { background: rgba(244, 63, 94, 0.1); color: #f43f5e; }

        .search-result-info {
            flex: 1;
            min-width: 0;
        }
        .search-result-title {
            font-weight: 700;
            font-size: 0.88rem;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .search-result-subtitle {
            font-size: 0.75rem;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .search-result-badge {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 8px;
            margin-inline-start: 12px;
            text-transform: uppercase;
        }
        .search-result-arrow {
            font-size: 0.8rem;
            color: var(--text-muted);
            opacity: 0.5;
            margin-inline-start: 12px;
            transition: transform 0.2s;
        }
        .delete-history-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            font-size: 0.8rem;
            margin-inline-start: 8px;
            opacity: 0;
            transition: opacity 0.2s, color 0.2s;
            z-index: 10;
        }
        .search-result-item:hover .delete-history-btn {
            opacity: 0.6;
        }
        .search-result-item:hover .delete-history-btn:hover {
            opacity: 1;
            color: #ef4444;
        }
        .search-result-item:hover .search-result-arrow, .search-result-item.active .search-result-arrow {
            opacity: 1;
            transform: translateX(2px);
        }
        [dir="rtl"] .search-result-item:hover .search-result-arrow, [dir="rtl"] .search-result-item.active .search-result-arrow {
            transform: translateX(-2px);
        }

        .search-modal-footer {
            padding: 12px 24px;
            background: rgba(0, 0, 0, 0.01);
            border-top: 1px solid var(--border-color);
        }
        .search-hint {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 0.72rem;
            color: var(--text-muted);
        }
        .search-hint kbd {
            background: rgba(0,0,0,0.04);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 1px 4px;
            font-family: inherit;
            font-weight: bold;
        }
        body.dark-mode .search-hint kbd {
            background: rgba(255,255,255,0.05);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* ==========================================
           Select2 Premium Custom Overrides
           ========================================== */
        .select2-container {
            width: 100% !important;
            margin-bottom: 2px;
        }
        
        .select2-container--default .select2-selection--single {
            background-color: var(--bg-card) !important;
            border: 2px solid var(--border-color) !important;
            border-radius: 12px !important;
            height: 48px !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.2s ease !important;
            padding: 0 10px !important;
            outline: none !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text-main) !important;
            font-size: 0.88rem !important;
            font-weight: 600 !important;
            padding-inline-start: 6px !important;
            padding-inline-end: 25px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: var(--text-muted) !important;
            opacity: 0.8;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px !important;
            position: absolute !important;
            top: 2px !important;
            inset-inline-end: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: var(--text-muted) transparent transparent transparent !important;
            border-width: 5px 4px 0 4px !important;
        }
        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent var(--text-muted) transparent !important;
            border-width: 0 4px 5px 4px !important;
        }

        /* Focus & Active States */
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--primary-blue) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }

        /* Dropdown Panel */
        .select2-dropdown {
            background-color: var(--bg-card) !important;
            border: 2px solid var(--border-color) !important;
            border-radius: 16px !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12) !important;
            overflow: hidden !important;
            z-index: 9999 !important;
            padding: 6px !important;
        }
        body.dark-mode .select2-dropdown {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4) !important;
        }

        /* Dropdown Search Field */
        .select2-search--dropdown {
            padding: 8px 8px 4px 8px !important;
        }
        .select2-search--dropdown .select2-search__field {
            background-color: var(--bg-main) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            color: var(--text-main) !important;
            outline: none !important;
            font-size: 0.82rem !important;
            font-weight: 500 !important;
            font-family: inherit !important;
        }
        .select2-search--dropdown .select2-search__field:focus {
            border-color: var(--primary-blue) !important;
        }

        /* Options Results */
        .select2-results__options {
            max-height: 220px !important;
            padding-inline-start: 0 !important;
            margin: 4px 0 0 0 !important;
        }
        .select2-results__option {
            padding: 10px 14px !important;
            font-size: 0.82rem !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            color: var(--text-main) !important;
            margin-bottom: 2px !important;
            transition: background 0.15s, color 0.15s !important;
        }
        .select2-results__option:last-child {
            margin-bottom: 0 !important;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary-blue) !important;
            color: #ffffff !important;
        }
        
        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: rgba(37, 99, 235, 0.08) !important;
            color: var(--primary-blue) !important;
        }
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
                    <div class="cdash-topbar-title-wrapper">
                        <span class="cdash-topbar-title">@yield('page-title', __('Dashboard'))</span>
                        <span class="cdash-topbar-subtitle">
                            {{ __('Welcome back,') }} {{ auth()->user()->first_name ?: auth()->user()->full_name }} ✨
                        </span>
                    </div>
                </div>

                <div class="cdash-topbar-actions">
                    
                    {{-- Spotlight Search Trigger --}}
                    <button onclick="openSearchModal()" class="cdash-topbar-btn d-flex align-items-center gap-2 px-3" style="width: auto; height: 40px;" title="{{ __('Search...') }}">
                        <i class="fas fa-search"></i>
                        <span class="d-none d-lg-inline-block text-muted small" style="font-weight: 600;">{{ __('Search...') }}</span>
                        <kbd class="d-none d-lg-inline-block bg-light text-muted border px-1.5 rounded" style="font-size: 0.65rem; border-color: var(--border-color) !important;">⌘K</kbd>
                    </button>

                    {{-- Quick Theme Toggle --}}
                    <button onclick="toggleDarkMode()" class="cdash-topbar-btn" title="{{ __('Toggle Dark Mode') }}" id="quickThemeBtn">
                        <i class="fas fa-moon"></i>
                    </button>

                    {{-- Notifications --}}
                    <div class="notification-wrapper position-relative">
                        <button class="cdash-topbar-btn {{ auth()->user()->unreadNotifications()->count() > 0 ? 'has-notifications' : '' }}" onclick="toggleNotif(event)" title="{{ __('Notifications') }}">
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
                        <div class="cdash-lang-dropdown shadow">
                            <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}">
                                🇸🇦 العربية
                            </a>
                            <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">
                                🇺🇸 English
                            </a>
                        </div>
                    </div>

                    {{-- User Profile Dropdown --}}
                    <div class="dropdown position-relative">
                        <button class="topbar-avatar-btn" onclick="toggleUserMenu(event)" title="{{ __('Account') }}">
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->full_name }}">
                        </button>
                        <div class="cdash-user-dropdown shadow" id="userMenuDropdown">
                            <div class="cdash-user-dropdown-header">
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->full_name }}">
                                <div class="cdash-user-dropdown-meta">
                                    <span class="cdash-user-dropdown-name">{{ auth()->user()->full_name }}</span>
                                    <span class="cdash-user-dropdown-email">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                            
                            @php
                                $walletService = app(\App\Services\WalletService::class);
                                $wallet = $walletService->getOrCreateWallet(auth()->id());
                                $walletBalance = $wallet ? $wallet->balance : 0;
                            @endphp
                            <div class="cdash-user-dropdown-wallet">
                                <span>{{ __('Wallet Balance') }}</span>
                                <strong>{{ number_format($walletBalance, 2) }} {{ __('SAR') }}</strong>
                            </div>
                            
                            <div class="cdash-user-dropdown-links">
                                <a href="{{ route('customer.profile') }}">
                                    <i class="fas fa-user-cog text-primary"></i>
                                    <span>{{ __('Profile Settings') }}</span>
                                </a>
                                <a href="{{ route('customer.wallet.index') }}">
                                    <i class="fas fa-wallet text-success"></i>
                                    <span>{{ __('My Wallet') }}</span>
                                </a>
                                <a href="{{ route('customer.support.index') }}">
                                    <i class="fas fa-headset text-warning"></i>
                                    <span>{{ __('Support Tickets') }}</span>
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="logout-btn">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>{{ __('Logout') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

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

                @if(auth()->check() && auth()->user()->is_guest)
                    <div class="guest-banner-wrapper mb-4" style="
                        background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
                        border-radius: 16px;
                        padding: 1px;
                        box-shadow: 0 8px 32px rgba(245, 158, 11, 0.35);
                        animation: pulse-border 3s ease-in-out infinite;
                    ">
                        <div style="
                            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
                            border-radius: 15px;
                            padding: 18px 24px;
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            flex-wrap: wrap;
                            gap: 16px;
                            position: relative;
                            overflow: hidden;
                        ">
                            {{-- Decorative background circle --}}
                            <div style="
                                position: absolute;
                                top: -30px;
                                {{ app()->getLocale() === 'ar' ? 'left: -30px;' : 'right: -30px;' }}
                                width: 120px;
                                height: 120px;
                                background: rgba(245, 158, 11, 0.12);
                                border-radius: 50%;
                                pointer-events: none;
                            "></div>
                            <div style="
                                position: absolute;
                                bottom: -20px;
                                {{ app()->getLocale() === 'ar' ? 'right: 80px;' : 'left: 80px;' }}
                                width: 80px;
                                height: 80px;
                                background: rgba(217, 119, 6, 0.08);
                                border-radius: 50%;
                                pointer-events: none;
                            "></div>

                            {{-- Icon + Text --}}
                            <div style="display: flex; align-items: center; gap: 16px; z-index: 1;">
                                <div style="
                                    width: 52px;
                                    height: 52px;
                                    background: linear-gradient(135deg, #f59e0b, #d97706);
                                    border-radius: 14px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.5);
                                    flex-shrink: 0;
                                ">
                                    <i class="fas fa-user-shield" style="font-size: 1.3rem; color: #fff;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 800; font-size: 0.95rem; color: #78350f; margin-bottom: 3px; font-family: 'Tajawal', sans-serif;">
                                        {{ __('Complete Your Profile') }}
                                    </div>
                                    <div style="font-size: 0.82rem; color: #92400e; font-family: 'Tajawal', sans-serif; line-height: 1.5;">
                                        {{ __('You must complete your profile information to unlock all platform features.') }}
                                    </div>
                                </div>
                            </div>

                            {{-- CTA Button --}}
                            <a href="{{ route('customer.profile') }}" style="
                                background: linear-gradient(135deg, #f59e0b, #d97706);
                                color: #fff;
                                font-weight: 800;
                                font-size: 0.88rem;
                                padding: 10px 22px;
                                border-radius: 10px;
                                text-decoration: none !important;
                                display: inline-flex;
                                align-items: center;
                                gap: 8px;
                                box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
                                transition: all 0.25s ease;
                                white-space: nowrap;
                                z-index: 1;
                                font-family: 'Tajawal', sans-serif;
                            "
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(245,158,11,0.55)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(245,158,11,0.4)';">
                                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" style="font-size: 0.8rem;"></i>
                                {{ __('Complete Now') }}
                            </a>
                        </div>
                    </div>
                    <style>
                        @keyframes pulse-border {
                            0%, 100% { box-shadow: 0 8px 32px rgba(245, 158, 11, 0.35); }
                            50% { box-shadow: 0 8px 40px rgba(245, 158, 11, 0.6); }
                        }
                    </style>
                @endif

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
            const userMenu = document.getElementById('userMenuDropdown');
            
            dropdown.classList.toggle('show');
            if (lang) lang.style.display = 'none';
            if (userMenu) userMenu.classList.remove('show');
        }

        function toggleLang(event) {
            if(event) event.stopPropagation();
            const lang = document.querySelector('.cdash-lang-dropdown');
            const dropdown = document.getElementById('notifDropdown');
            const userMenu = document.getElementById('userMenuDropdown');
            
            const isVisible = window.getComputedStyle(lang).display === 'block';
            lang.style.display = isVisible ? 'none' : 'block';
            if (dropdown) dropdown.classList.remove('show');
            if (userMenu) userMenu.classList.remove('show');
        }

        function toggleUserMenu(event) {
            if(event) event.stopPropagation();
            const userMenu = document.getElementById('userMenuDropdown');
            const lang = document.querySelector('.cdash-lang-dropdown');
            const dropdown = document.getElementById('notifDropdown');
            
            userMenu.classList.toggle('show');
            if (lang) lang.style.display = 'none';
            if (dropdown) dropdown.classList.remove('show');
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
            const quickThemeBtnIcon = document.querySelector('#quickThemeBtn i');
            if(quickThemeBtnIcon) {
                quickThemeBtnIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
            }
        }

        // Global Click to Close Dropdowns
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.notification-wrapper')) {
                const notif = document.getElementById('notifDropdown');
                if (notif) notif.classList.remove('show');
            }
            if (!e.target.closest('.dropdown')) {
                const lang = document.querySelector('.cdash-lang-dropdown');
                if (lang) lang.style.display = 'none';
                
                const userMenu = document.getElementById('userMenuDropdown');
                if (userMenu) userMenu.classList.remove('show');
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
                const quickThemeBtnIcon = document.querySelector('#quickThemeBtn i');
                if(quickThemeBtnIcon) {
                    quickThemeBtnIcon.className = 'fas fa-sun';
                }
            }
        });

        // Scroll Header Effect
        window.addEventListener('scroll', () => {
            const topbar = document.querySelector('.cdash-topbar');
            if (topbar) {
                if (window.scrollY > 10) {
                    topbar.classList.add('scrolled');
                } else {
                    topbar.classList.remove('scrolled');
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

    {{-- Global Spotlight Search Modal --}}
    <div class="search-modal-backdrop" id="searchModal" onclick="closeSearchModal(event)">
        <div class="search-modal-content" onclick="event.stopPropagation()">
            <div class="search-modal-header">
                <div class="search-modal-icon-wrapper">
                    <i class="fas fa-search search-modal-icon" id="searchStateIcon"></i>
                </div>
                <input type="text" id="globalSearchInput" placeholder="{{ __('Search trips, flights, hotels, tickets, settings...') }}" autocomplete="off" oninput="performGlobalSearch()">
                
                {{-- Clear query button --}}
                <button id="clearSearchQueryBtn" onclick="clearSearchQuery()" class="clear-query-btn" style="display: none;" title="{{ __('Clear search') }}">
                    <i class="fas fa-times-circle"></i>
                </button>
                
                <button class="search-close-btn" onclick="closeSearchModal(event)">&times;</button>
            </div>
            <div class="search-modal-body" id="globalSearchResults">
                {{-- Quick Shortcuts & Recent Searches --}}
            </div>
            <div class="search-modal-footer">
                <div class="search-hint">
                    <span><kbd>↑↓</kbd> {{ __('to navigate') }}</span>
                    <span><kbd>Enter</kbd> {{ __('to select') }}</span>
                    <span><kbd>Esc</kbd> {{ __('to close') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Spotlight Search Script --}}
    <script>
        let searchTimeout = null;
        let activeResultIndex = -1;

        function openSearchModal() {
            const modal = document.getElementById('searchModal');
            if (modal) {
                modal.classList.add('open');
                document.body.style.overflow = 'hidden';
                const input = document.getElementById('globalSearchInput');
                if (input) {
                    input.value = '';
                    setTimeout(() => input.focus(), 100);
                }
                const clearBtn = document.getElementById('clearSearchQueryBtn');
                if (clearBtn) clearBtn.style.display = 'none';
                restoreDefaultSearchContent();
            }
        }

        function closeSearchModal(event) {
            const modal = document.getElementById('searchModal');
            if (modal) {
                modal.classList.remove('open');
                document.body.style.overflow = '';
            }
        }

        function clearSearchQuery() {
            const input = document.getElementById('globalSearchInput');
            if (input) {
                input.value = '';
                input.focus();
            }
            const clearBtn = document.getElementById('clearSearchQueryBtn');
            if (clearBtn) clearBtn.style.display = 'none';
            
            const stateIcon = document.getElementById('searchStateIcon');
            if (stateIcon) stateIcon.className = 'fas fa-search search-modal-icon';
            
            restoreDefaultSearchContent();
        }

        function saveRecentSearch(title, link, icon) {
            let history = JSON.parse(localStorage.getItem('recent_searches') || '[]');
            history = history.filter(h => h.link !== link);
            history.unshift({ title, link, icon });
            if (history.length > 5) history.pop();
            localStorage.setItem('recent_searches', JSON.stringify(history));
        }

        function deleteHistoryItem(idx) {
            let history = JSON.parse(localStorage.getItem('recent_searches') || '[]');
            history.splice(idx, 1);
            localStorage.setItem('recent_searches', JSON.stringify(history));
            restoreDefaultSearchContent();
        }

        function clearSearchHistory() {
            localStorage.removeItem('recent_searches');
            restoreDefaultSearchContent();
        }

        function restoreDefaultSearchContent() {
            activeResultIndex = -1;
            const container = document.getElementById('globalSearchResults');
            if (!container) return;

            let history = JSON.parse(localStorage.getItem('recent_searches') || '[]');
            let historyHtml = '';
            
            if (history.length > 0) {
                historyHtml = `
                    <div class="search-section">
                        <div class="search-section-header">
                            <div class="search-section-title">${"{{ __('Recent Searches') }}"}</div>
                            <a onclick="clearSearchHistory()" class="clear-history-link">${"{{ __('Clear All') }}"}</a>
                        </div>
                        <div class="search-results-list">
                `;
                
                history.forEach((item, idx) => {
                    historyHtml += `
                        <div class="search-result-item" style="cursor: pointer;" onclick="window.location.href='${item.link}'">
                            <div class="search-result-icon bg-blue-soft">
                                <i class="${item.icon || 'fas fa-history'}"></i>
                            </div>
                            <div class="search-result-info">
                                <div class="search-result-title">${item.title}</div>
                                <div class="search-result-subtitle">${"{{ __('Recently Visited') }}"}</div>
                            </div>
                            <button onclick="event.stopPropagation(); deleteHistoryItem(${idx})" class="delete-history-btn" title="${"{{ __('Delete') }}"}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <i class="fas fa-chevron-right search-result-arrow"></i>
                        </div>
                    `;
                });
                
                historyHtml += `
                        </div>
                    </div>
                `;
            }

            container.innerHTML = `
                ${historyHtml}
                <div class="search-section">
                    <div class="search-section-title">{{ __('Quick Navigation') }}</div>
                    <div class="search-results-list">
                        <a href="{{ route('customer.dashboard') }}" class="search-result-item">
                            <div class="search-result-icon bg-blue-soft"><i class="fas fa-columns"></i></div>
                            <div class="search-result-info">
                                <div class="search-result-title">{{ __('Dashboard') }}</div>
                                <div class="search-result-subtitle">{{ __('Main stats and upcoming bookings') }}</div>
                            </div>
                            <i class="fas fa-chevron-right search-result-arrow"></i>
                        </a>
                        <a href="{{ route('customer.profile') }}" class="search-result-item">
                            <div class="search-result-icon bg-purple-soft"><i class="fas fa-user-cog"></i></div>
                            <div class="search-result-info">
                                <div class="search-result-title">{{ __('Profile Settings') }}</div>
                                <div class="search-result-subtitle">{{ __('Update details and password') }}</div>
                            </div>
                            <i class="fas fa-chevron-right search-result-arrow"></i>
                        </a>
                        <a href="{{ route('customer.wallet.index') }}" class="search-result-item">
                            <div class="search-result-icon bg-green-soft"><i class="fas fa-wallet"></i></div>
                            <div class="search-result-info">
                                <div class="search-result-title">{{ __('My Wallet') }}</div>
                                <div class="search-result-subtitle">{{ __('Balance and bank statements') }}</div>
                            </div>
                            <i class="fas fa-chevron-right search-result-arrow"></i>
                        </a>
                        <a href="{{ route('customer.support.index') }}" class="search-result-item">
                            <div class="search-result-icon bg-orange-soft"><i class="fas fa-headset"></i></div>
                            <div class="search-result-info">
                                <div class="search-result-title">{{ __('Support Tickets') }}</div>
                                <div class="search-result-subtitle">{{ __('Create or track support tickets') }}</div>
                            </div>
                            <i class="fas fa-chevron-right search-result-arrow"></i>
                        </a>
                    </div>
                </div>
            `;
        }

        function performGlobalSearch() {
            clearTimeout(searchTimeout);
            const input = document.getElementById('globalSearchInput');
            const clearBtn = document.getElementById('clearSearchQueryBtn');
            const stateIcon = document.getElementById('searchStateIcon');
            const q = input.value.trim();
            
            if (q.length < 2) {
                if (clearBtn) clearBtn.style.display = 'none';
                if (stateIcon) stateIcon.className = 'fas fa-search search-modal-icon';
                restoreDefaultSearchContent();
                return;
            }

            if (clearBtn) clearBtn.style.display = 'block';
            if (stateIcon) stateIcon.className = 'fas fa-circle-notch fa-spin search-modal-icon';

            searchTimeout = setTimeout(() => {
                const container = document.getElementById('globalSearchResults');
                
                fetch(`{{ route('customer.search-all') }}?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (stateIcon) stateIcon.className = 'fas fa-search search-modal-icon';
                        renderSearchResults(data);
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                        if (stateIcon) stateIcon.className = 'fas fa-search search-modal-icon';
                        if (container) {
                            container.innerHTML = `
                                <div class="text-center p-5 text-danger">
                                    <i class="fas fa-exclamation-triangle font-size-xl"></i>
                                    <div class="small mt-2">${"{{ __('An error occurred while searching') }}"}</div>
                                </div>
                            `;
                        }
                    });
            }, 300);
        }

        function renderSearchResults(data) {
            const container = document.getElementById('globalSearchResults');
            if (!container) return;

            let html = '';
            let hasResults = false;
            activeResultIndex = -1;

            const sections = [
                { key: 'links', title: "{{ __('Quick Actions') }}", bg: 'bg-teal-soft' },
                { key: 'trips', title: "{{ __('Trip Bookings') }}", bg: 'bg-blue-soft' },
                { key: 'flights', title: "{{ __('Flight Bookings') }}", bg: 'bg-purple-soft' },
                { key: 'hotels', title: "{{ __('Hotel Bookings') }}", bg: 'bg-orange-soft' },
                { key: 'tickets', title: "{{ __('Support Tickets') }}", bg: 'bg-rose-soft' },
                { key: 'payments', title: "{{ __('Payments') }}", bg: 'bg-green-soft' }
            ];

            sections.forEach(sec => {
                const items = data[sec.key] || [];
                if (items.length > 0) {
                    hasResults = true;
                    html += `
                        <div class="search-section">
                            <div class="search-section-title">${sec.title}</div>
                            <div class="search-results-list">
                    `;
                    
                    items.forEach(item => {
                        const statusBadge = item.status 
                            ? `<span class="search-result-badge badge bg-${getStatusColor(item.status)}-subtle text-${getStatusColor(item.status)}">${item.status}</span>`
                            : '';
                            
                        // escape title to prevent JS issues on click
                        const escapedTitle = item.title.replace(/'/g, "\\'");
                        const itemIcon = item.icon || 'fas fa-chevron-right';
                            
                        html += `
                            <a href="${item.link}" onclick="saveRecentSearch('${escapedTitle}', '${item.link}', '${itemIcon}')" class="search-result-item">
                                <div class="search-result-icon ${item.icon ? '' : sec.bg}">
                                    <i class="${itemIcon}"></i>
                                </div>
                                <div class="search-result-info">
                                    <div class="search-result-title">${item.title}</div>
                                    <div class="search-result-subtitle">${item.subtitle || ''}</div>
                                </div>
                                ${statusBadge}
                                <i class="fas fa-chevron-right search-result-arrow"></i>
                            </a>
                        `;
                    });
                    
                    html += `
                            </div>
                        </div>
                    `;
                }
            });

            if (!hasResults) {
                html = `
                    <div class="text-center p-5 text-muted">
                        <i class="fas fa-search-minus font-size-xxl mb-3 opacity-25"></i>
                        <div class="small">${"{{ __('No matching results found') }}"}</div>
                    </div>
                `;
            }

            container.innerHTML = html;
        }

        function getStatusColor(status) {
            status = status.toLowerCase();
            if (['confirmed', 'paid', 'open', 'completed'].includes(status)) return 'success';
            if (['pending', 'processing'].includes(status)) return 'warning';
            if (['cancelled', 'failed', 'closed'].includes(status)) return 'danger';
            return 'secondary';
        }

        // Global hotkey Ctrl+K / Cmd+K
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                const modal = document.getElementById('searchModal');
                if (modal && modal.classList.contains('open')) {
                    closeSearchModal();
                } else {
                    openSearchModal();
                }
            }
            
            const modal = document.getElementById('searchModal');
            if (modal && modal.classList.contains('open')) {
                const items = document.querySelectorAll('.search-result-item');
                
                if (e.key === 'Escape') {
                    e.preventDefault();
                    closeSearchModal();
                }
                
                if (items.length > 0) {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        activeResultIndex = (activeResultIndex + 1) % items.length;
                        highlightSearchResult(items);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        activeResultIndex = (activeResultIndex - 1 + items.length) % items.length;
                        highlightSearchResult(items);
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (activeResultIndex >= 0 && activeResultIndex < items.length) {
                            items[activeResultIndex].click();
                        }
                    }
                }
            }
        });

        function highlightSearchResult(items) {
            items.forEach((item, index) => {
                if (index === activeResultIndex) {
                    item.classList.add('active');
                    item.scrollIntoView({ block: 'nearest' });
                } else {
                    item.classList.remove('active');
                }
            });
        }
    </script>

    {{-- JS Dependencies --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Function to initialize Select2 with custom settings
            function initSelect2() {
                $('select.select2').each(function() {
                    let $select = $(this);
                    
                    // Don't re-initialize if already done
                    if ($select.hasClass('select2-hidden-accessible')) {
                        return;
                    }
                    
                    $select.select2({
                        width: '100%',
                        minimumResultsForSearch: 0, // always show search inside select
                        dropdownParent: $select.parent(), // Prevents issues inside modals or sticky containers
                        dir: $('html').attr('dir') || 'ltr', // Sync RTL direction from html lang
                        language: {
                            noResults: function() {
                                return $('html').attr('dir') === 'rtl' ? "لا توجد نتائج" : "No results found";
                            }
                        }
                    });
                });
            }

            // Initialize on load
            initSelect2();

            // Re-initialize when Bootstrap modals are opened or content changes dynamically
            $(document).on('shown.bs.modal', function() {
                initSelect2();
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
