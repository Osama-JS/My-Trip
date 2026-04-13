@php
    $theme_version = $_COOKIE['version'] ?? 'light';
    $typography = $_COOKIE['typography'] ?? 'poppins';
    $layout = $_COOKIE['layout'] ?? 'vertical';
    $nav_headerbg = $_COOKIE['navheaderBg'] ?? 'color_1';
    $headerbg = $_COOKIE['headerBg'] ?? 'color_1';
    $sidebarStyle = $_COOKIE['sidebarStyle'] ?? 'full';
    $sidebarBg = $_COOKIE['sidebarBg'] ?? 'color_1';
    $sidebarPosition = $_COOKIE['sidebarPosition'] ?? 'fixed';
    $headerPosition = $_COOKIE['headerPosition'] ?? 'fixed';
    $containerLayout = $_COOKIE['containerLayout'] ?? 'wide';
    $primary = $_COOKIE['primary'] ?? 'color_1';
    $direction = $_COOKIE['direction'] ?? (app()->getLocale() == 'ar' ? 'rtl' : 'ltr');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $direction }}" class="{{ $direction == 'rtl' ? 'rtl' : '' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Fly Vio')) - {{ __('Admin Dashboard') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset(\App\Models\Setting::get('site_favicon', 'images/favicon.png')) }}">

    <!-- Global Scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
     <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

     <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-nice-select/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('vendor/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('vendor/perfect-scrollbar/js/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/toastr/js/toastr.min.js') }}"></script>


    <!-- Global Variables for Template -->
    <script>
        window.bootstrap = bootstrap;
        var dlabConfig = {
            typography: "poppins",
            version: "light",
            layout: "horizontal",
            primary: "color_1",
            headerBg: "color_1",
            navheaderBg: "color_1",
            sidebarBg: "color_1",
            sidebarStyle: "full",
            sidebarPosition: "fixed",
            headerPosition: "fixed",
            containerLayout: "full",
        };
    </script>

    <!-- Custom Stylesheet -->
    <link href="{{ asset('vendor/jquery-nice-select/css/nice-select.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/owl-carousel/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/aos/css/aos.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/metismenu/css/metisMenu.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/toastr/css/toastr.min.css') }}" rel="stylesheet">

    <!-- Icons -->
    <link href="{{ asset('icons/simple-line-icons/css/simple-line-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/font-awesome/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/material-design-iconic-font/css/materialdesignicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/themify-icons/css/themify-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/line-awesome/css/line-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/avasta/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/flaticon/flaticon.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/flaticon_1/flaticon_1.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/icomoon/icomoon.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/bootstrap-icons/font/bootstrap-icons.css') }}" rel="stylesheet">

    <!-- Vite CSS -->
    @vite(['resources/css/app.css'])

    <!-- ============================================================ -->
    <!-- MOBILE SIDEBAR FIX — Overrides horizontal nav for small screens -->
    <!-- ============================================================ -->
    <style>
        @media (max-width: 991px) {

            /* ── Sidebar container ── */
            .dlabnav {
                overflow-y: auto !important;
                overflow-x: hidden !important;
            }

            .dlabnav-scroll {
                flex-direction: column !important;
                align-items: stretch !important;
                padding: 0 !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                height: 100% !important;
                box-shadow: none !important;
            }

            .nav-scroll-wrapper {
                width: 100% !important;
                overflow: visible !important;
                transform: none !important; /* Reset JS-applied transform */
            }

            /* ── Make metismenu vertical ── */
            .dlabnav .metismenu {
                display: block !important;
                flex-direction: column !important;
                flex-wrap: wrap !important;
                transform: none !important;  /* Freeze horizontal scroll position */
                width: 100% !important;
            }

            .dlabnav .metismenu > li {
                display: block !important;
                width: 100% !important;
                flex-shrink: unset !important;
            }

            /* ── Top-level link layout ── */
            .dlabnav .metismenu > li > a {
                display: flex !important;
                align-items: center !important;
                gap: 10px !important;
                padding: 12px 20px !important;
                white-space: nowrap !important;
            }

            /* ── Sub-menu: INLINE (not floating) ── */
            .dlabnav .metismenu ul {
                position: static !important;
                top: auto !important;
                left: auto !important;
                right: auto !important;
                width: 100% !important;
                min-width: 100% !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                background: rgba(0, 0, 0, 0.04) !important;
                padding: 4px 0 4px 28px !important;
                z-index: auto !important;
                /* MetisMenu controls display via max-height & mm-collapse */
            }

            [dir="rtl"] .dlabnav .metismenu ul {
                padding: 4px 28px 4px 0 !important;
            }

            .dlabnav .metismenu ul li a {
                padding: 9px 15px !important;
                display: block !important;
            }

            /* ── MetisMenu open state ── */
            .dlabnav .metismenu ul.mm-show,
            .dlabnav .metismenu li.mm-active > ul {
                display: block !important;
            }

            /* ── Hide horizontal scroll arrows ── */
            .nav-control-btn {
                display: none !important;
            }

            /* ── Arrow indicator fix (RTL) ── */
            [dir="rtl"] .dlabnav .metismenu li a.has-arrow::after {
                left: 15px;
                right: auto;
            }
        }
    </style>

    @stack('styles')
</head>
<body
    data-typography="{{ $typography }}"
    data-theme-version="{{ $theme_version }}"
    data-layout="{{ $layout }}"
    data-nav-headerbg="{{ $nav_headerbg }}"
    data-headerbg="{{ $headerbg }}"
    data-sidebar-style="{{ $sidebarStyle }}"
    data-sibebarbg="{{ $sidebarBg }}"
    data-sidebar-position="{{ $sidebarPosition }}"
    data-header-position="{{ $headerPosition }}"
    data-container="{{ $containerLayout }}"
    data-primary="{{ $primary }}"
    direction="{{ $direction }}"
>

    <!-- Preloader -->
    @include('partials.preloader')

    <!-- Main wrapper -->
    <div id="main-wrapper">
        <!-- Nav header -->
        @include('partials.nav-header')

        <!-- Header -->
        @include('partials.header')

        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Content body -->
        <div class="content-body">
            <div class="container-fluid">
                @yield('page-header')

                <!-- Display flash messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Main content -->
                @yield('content')
            </div>
        </div>

        <!-- Footer -->
        @include('partials.footer')
    </div>
        <div id="globalLoader" style="
        position: fixed;
        inset: 0;
        background: rgba(255,255,255,0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;">
        <div class="spinner-border text-primary"></div>
    </div>

    <!-- Vite JS -->
    @vite(['resources/js/app.js'])

    <!-- ============================================================ -->
    <!-- MOBILE SIDEBAR PATCH — Works immediately without npm build   -->
    <!-- ============================================================ -->
    <script>
    (function() {
        'use strict';

        var MOBILE_BREAKPOINT = 991;

        function isMobile() {
            return window.innerWidth <= MOBILE_BREAKPOINT;
        }

        /**
         * Reset the horizontal translateX transform applied by initHorizontalNav
         * so that the mobile vertical sidebar is not broken.
         */
        function resetMenuTransform() {
            var menu = document.getElementById('menu');
            var navScrollContainer = document.getElementById('nav-scroll-container');
            var prevBtn = document.getElementById('nav-prev-btn');
            var nextBtn = document.getElementById('nav-next-btn');

            if (menu) menu.style.transform = '';
            if (navScrollContainer) navScrollContainer.style.transform = '';
            if (prevBtn) { prevBtn.style.opacity = '0'; prevBtn.style.visibility = 'hidden'; }
            if (nextBtn) { nextBtn.style.opacity = '0'; nextBtn.style.visibility = 'hidden'; }
        }

        /**
         * On mobile, ensure MetisMenu submenus work correctly.
         * MetisMenu uses max-height + mm-show/mm-collapse for animation.
         * We patch the sidebar link clicks to properly toggle submenus.
         */
        function initMobileSidebar() {
            if (!isMobile()) return;

            resetMenuTransform();

            // Make sure metismenu is re-initialized when sidebar opens
            var hamburger = document.querySelector('.nav-control');
            if (hamburger) {
                hamburger.addEventListener('click', function() {
                    setTimeout(function() {
                        if (typeof $ !== 'undefined' && $.fn.metisMenu) {
                            if (typeof $('#menu').data('mm') === 'undefined') {
                                $('#menu').metisMenu();
                            }
                        }
                        resetMenuTransform();
                    }, 100);
                });
            }
        }

        // Run on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            initMobileSidebar();
        });

        // Run on window load (after all scripts initialize)
        window.addEventListener('load', function() {
            if (isMobile()) {
                resetMenuTransform();
            }
        });

        // Reset on resize if we switch to mobile
        window.addEventListener('resize', function() {
            if (isMobile()) {
                resetMenuTransform();
            }
        });

        // Also patch immediately since DOMContentLoaded may have already fired
        if (document.readyState !== 'loading') {
            setTimeout(initMobileSidebar, 0);
        }

    })();
    </script>

    @stack('scripts')
</body>
</html>
