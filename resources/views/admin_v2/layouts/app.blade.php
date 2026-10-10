<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'لوحة التحكم') | {{ config('app.name', 'My-Trip') }} Admin v2</title>

    <!-- Google Fonts (Cairo for Arabic, Inter for English) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons 6.5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Select2 CSS (Local with CDN Fallback) -->
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" onerror="this.onerror=null;">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">

    <!-- Toastr CSS (Local with CDN Fallback) -->
    <link rel="stylesheet" href="{{ asset('vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" onerror="this.onerror=null;">

    <!-- SweetAlert2 CSS (Local with CDN Fallback) -->
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" onerror="this.onerror=null;">

    <!-- Bootstrap 5 CSS Framework (RTL for Arabic, LTR for English with Local Fallbacks) -->
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap-rtl/bootstrap-rtl.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" onerror="this.onerror=null;">
    @else
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap/dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" onerror="this.onerror=null;">
    @endif

    <!-- Admin v2 Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin_v2/css/app.css') }}?v={{ @filemtime(public_path('assets/admin_v2/css/app.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/admin_v2/css/tables.css') }}?v={{ @filemtime(public_path('assets/admin_v2/css/tables.css')) }}">

    <!-- jQuery loaded in HEAD to ensure full availability across all views & scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script>
        if (typeof jQuery === 'undefined') {
            document.write('<script src="https://code.jquery.com/jquery-3.7.1.min.js"><\/script>');
        }
    </script>

    <!-- Core Vendor Libraries (Select2, Bootstrap Bundle) in Head for Immediate Execution -->
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        if (typeof $.fn.select2 === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"><\/script>');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toastr & SweetAlert2 -->
    <script src="{{ asset('vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <!-- Flatpickr & ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Admin v2 Core JS Engines -->
    <script src="{{ asset('assets/admin_v2/js/notifications.js') }}?v={{ @filemtime(public_path('assets/admin_v2/js/notifications.js')) }}"></script>
    <script src="{{ asset('assets/admin_v2/js/components.js') }}?v={{ @filemtime(public_path('assets/admin_v2/js/components.js')) }}"></script>
    <script>
        window.ADMIN_GLOBAL_SEARCH_URL = "{{ route('admin.global-search') }}";
    </script>

    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar Backdrop for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Master Sidebar -->
        @include('admin_v2.layouts.sidebar')

        <!-- Master Main Container -->
        <div class="admin-main">
            <!-- Master Top Header -->
            @include('admin_v2.layouts.header')

            <!-- Main Dynamic Content -->
            <main class="admin-content">
                @yield('content')
            </main>

            <!-- Master Footer -->
            @include('admin_v2.layouts.footer')
        </div>
    </div>

    <!-- Admin v2 App UI Engine -->
    <script src="{{ asset('assets/admin_v2/js/app.js') }}?v={{ @filemtime(public_path('assets/admin_v2/js/app.js')) }}"></script>

    <!-- Laravel Flash Messages Bridge to Toastr -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Notify !== 'undefined') {
                @if(session('success'))
                    Notify.success("{{ session('success') }}");
                @endif
                @if(session('error'))
                    Notify.error("{{ session('error') }}");
                @endif
                @if(session('warning'))
                    Notify.warning("{{ session('warning') }}");
                @endif
                @if(session('info'))
                    Notify.info("{{ session('info') }}");
                @endif
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
