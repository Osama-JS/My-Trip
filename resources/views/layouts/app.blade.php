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
    $direction = app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $direction }}" class="{{ $direction == 'rtl' ? 'rtl' : '' }}">
<head>
    <script>
        (function() {
            var currentLocaleDir = "{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}";
            document.cookie = "direction=" + currentLocaleDir + ";path=/;max-age=" + (30 * 60);
        })();
    </script>
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
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>

    {{-- ═══ TOASTR GLOBAL CONFIG ═══ --}}
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-center",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "350",
            "hideDuration": "400",
            "timeOut": "4000",
            "extendedTimeOut": "1500",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
            "rtl": document.documentElement.dir === 'rtl'
        };
    </script>


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

    <!-- Global AJAX & Helper Functions -->
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        window.submitAjaxForm = function({
            formId,
            url,
            method = "POST",
            modalId = null,
            table = null,
            successMessage = "{{ __('Saved successfully') }}",
            buttonText = "{{ __('Save') }}",
            usePut = false,
            resetSelect2 = true,
            useSweetAlert = false,
            onSuccess = null
        }) {
            const form = document.getElementById(formId);
            let formData = new FormData(form);

            if (usePut) {
                formData.append('_method', 'PUT');
            }

            $('#globalLoader').fadeIn(150);

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

                        if (resetSelect2) {
                            $(`#${formId} .select2`).val(null).trigger('change');
                        }

                        if (table) {
                            table.ajax.reload(null, false);
                        }

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
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => {
                            toastr.error(err[0]);
                        });
                    } else {
                        toastr.error("{{ __('Something went wrong') }}");
                    }
                },

                complete: function () {
                    $('#globalLoader').fadeOut(150);
                    $(`#${formId}`).find('button[type="submit"]')
                        .prop('disabled', false)
                        .html(buttonText || "{{ __('Save') }}");
                }
            });
        }
    </script>

    <!-- Custom Stylesheet -->
    <link href="{{ asset('vendor/jquery-nice-select/css/nice-select.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/owl-carousel/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/aos/css/aos.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/metismenu/css/metisMenu.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/toastr/css/toastr.min.css') }}" rel="stylesheet">

    {{-- ═══ PREMIUM TOASTR OVERRIDES ═══ --}}
    <style>
        /* ── Container: bottom-center ── */
        #toast-container {
            bottom: 28px !important;
            top: auto !important;
            left: 50% !important;
            right: auto !important;
            transform: translateX(-50%);
            width: auto !important;
            max-width: 92vw;
            display: flex;
            flex-direction: column-reverse;
            align-items: center;
            gap: 10px;
        }

        /* ── Individual Toast ── */
        #toast-container > div {
            width: auto !important;
            min-width: 340px;
            max-width: 520px;
            padding: 16px 22px 16px 56px !important;
            margin: 0 !important;
            border-radius: 14px !important;
            font-family: 'Inter', 'Cairo', 'Segoe UI', system-ui, sans-serif !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            letter-spacing: 0.01em;
            line-height: 1.5 !important;
            opacity: 1 !important;
            filter: none !important;
            background-size: 22px !important;
            background-position: 18px center !important;
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.12),
                0 2px 8px rgba(0, 0, 0, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
            backdrop-filter: blur(12px) saturate(1.6);
            -webkit-backdrop-filter: blur(12px) saturate(1.6);
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            /* Entry animation */
            animation: toastSlideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* RTL support */
        [dir="rtl"] #toast-container > div,
        #toast-container > div.rtl {
            padding: 16px 56px 16px 22px !important;
            background-position: right 18px center !important;
        }

        /* ── Slide-up entrance ── */
        @keyframes toastSlideUp {
            0% {
                opacity: 0;
                transform: translateY(24px) scale(0.96);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ── Hover ── */
        #toast-container > div:hover {
            box-shadow:
                0 12px 40px rgba(0, 0, 0, 0.18),
                0 4px 12px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
            transform: translateY(-2px);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* ── Success ── */
        #toast-container > .toast-success {
            background-color: rgba(16, 185, 129, 0.95) !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M22 11.08V12a10 10 0 1 1-5.93-9.14'/%3E%3Cpolyline points='22 4 12 14.01 9 11.01'/%3E%3C/svg%3E") !important;
        }

        /* ── Error ── */
        #toast-container > .toast-error {
            background-color: rgba(239, 68, 68, 0.95) !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'/%3E%3Cline x1='15' y1='9' x2='9' y2='15'/%3E%3Cline x1='9' y1='9' x2='15' y2='15'/%3E%3C/svg%3E") !important;
        }

        /* ── Warning ── */
        #toast-container > .toast-warning {
            background-color: rgba(245, 158, 11, 0.95) !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z'/%3E%3Cline x1='12' y1='9' x2='12' y2='13'/%3E%3Cline x1='12' y1='17' x2='12.01' y2='17'/%3E%3C/svg%3E") !important;
        }

        /* ── Info ── */
        #toast-container > .toast-info {
            background-color: rgba(59, 130, 246, 0.95) !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'/%3E%3Cline x1='12' y1='16' x2='12' y2='12'/%3E%3Cline x1='12' y1='8' x2='12.01' y2='8'/%3E%3C/svg%3E") !important;
        }

        /* ── Title ── */
        #toast-container .toast-title {
            font-weight: 700 !important;
            font-size: 14.5px !important;
            margin-bottom: 3px;
        }

        /* ── Message ── */
        #toast-container .toast-message {
            font-weight: 400 !important;
            font-size: 13.5px !important;
            opacity: 0.95;
        }

        /* ── Close Button ── */
        #toast-container .toast-close-button {
            position: absolute !important;
            top: 10px !important;
            font-size: 16px !important;
            font-weight: 400 !important;
            color: rgba(255, 255, 255, 0.7) !important;
            text-shadow: none !important;
            opacity: 1 !important;
            transition: all 0.2s ease;
        }
        [dir="rtl"] #toast-container .toast-close-button {
            left: 12px !important;
            right: auto !important;
        }
        [dir="ltr"] #toast-container .toast-close-button,
        #toast-container .toast-close-button {
            right: 12px !important;
            left: auto !important;
        }
        #toast-container .toast-close-button:hover {
            color: rgba(255, 255, 255, 1) !important;
            transform: scale(1.15);
        }

        /* ── Progress Bar ── */
        #toast-container .toast-progress {
            height: 3px !important;
            border-radius: 0 0 14px 14px !important;
            background-color: rgba(255, 255, 255, 0.35) !important;
            opacity: 1 !important;
        }

        /* ── Mobile Responsive ── */
        @media (max-width: 480px) {
            #toast-container {
                bottom: 16px !important;
                max-width: 96vw;
            }
            #toast-container > div {
                min-width: 280px;
                max-width: 94vw;
                padding: 14px 18px 14px 50px !important;
                font-size: 13px !important;
                border-radius: 12px !important;
            }
            [dir="rtl"] #toast-container > div {
                padding: 14px 50px 14px 18px !important;
            }
        }
    </style>

    {{-- ═══ PREMIUM SELECT / SELECT2 OVERRIDES ═══ --}}
    <style>
        /* ══════════════════════════════════════════════════════════════ */
        /* NATIVE FORM-SELECT — Premium Styling                         */
        /* ══════════════════════════════════════════════════════════════ */
        .form-select {
            border: 1.5px solid #d1d9e6 !important;
            border-radius: 0.625rem !important;
            padding: 10px 40px 10px 16px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            background-color: #fff !important;
            min-height: 44px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 1px 2px rgba(4, 23, 65, 0.04) !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23041741' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 14px center !important;
            background-size: 16px !important;
            appearance: none !important;
            -webkit-appearance: none !important;
        }

        [dir="rtl"] .form-select {
            padding: 10px 16px 10px 40px !important;
            background-position: left 14px center !important;
        }

        .form-select:hover {
            border-color: rgba(4, 23, 65, 0.3) !important;
            box-shadow: 0 2px 8px rgba(4, 23, 65, 0.06) !important;
        }

        .form-select:focus {
            border-color: #041741 !important;
            box-shadow: 0 0 0 3px rgba(4, 23, 65, 0.1), 0 1px 2px rgba(4, 23, 65, 0.04) !important;
            outline: none !important;
        }

        .form-select:disabled {
            background-color: #f1f5f9 !important;
            color: #94a3b8 !important;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .form-select.is-invalid {
            border-color: #dc3545 !important;
        }

        .form-select.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.12) !important;
        }

        /* ══════════════════════════════════════════════════════════════ */
        /* SELECT2 — Premium Overrides (Site Colors #041741)            */
        /* ══════════════════════════════════════════════════════════════ */

        /* ── Container ── */
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border: 1.5px solid #d1d9e6 !important;
            border-radius: 0.625rem !important;
            min-height: 44px !important;
            padding: 4px 12px !important;
            background-color: #fff !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 1px 2px rgba(4, 23, 65, 0.04) !important;
        }

        .select2-container--default .select2-selection--single:hover,
        .select2-container--default .select2-selection--multiple:hover {
            border-color: rgba(4, 23, 65, 0.3) !important;
            box-shadow: 0 2px 8px rgba(4, 23, 65, 0.06) !important;
        }

        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--multiple,
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #041741 !important;
            box-shadow: 0 0 0 3px rgba(4, 23, 65, 0.1), 0 1px 2px rgba(4, 23, 65, 0.04) !important;
        }

        /* ── Single Selection Rendered Text ── */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            line-height: 34px !important;
            padding-left: 4px !important;
            padding-right: 28px !important;
        }

        [dir="rtl"] .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-right: 4px !important;
            padding-left: 28px !important;
        }

        /* ── Placeholder ── */
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8 !important;
            font-weight: 400 !important;
        }

        /* ── Arrow ── */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
            width: 32px !important;
            top: 0 !important;
            right: 4px !important;
        }

        [dir="rtl"] .select2-container--default .select2-selection--single .select2-selection__arrow {
            right: auto !important;
            left: 4px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border: none !important;
            width: 16px !important;
            height: 16px !important;
            margin: auto !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23041741' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            background-size: contain !important;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            transform: translate(-50%, -50%) rotate(180deg) !important;
        }

        /* ── Clear Button ── */
        .select2-container--default .select2-selection__clear {
            color: #94a3b8 !important;
            font-size: 18px !important;
            font-weight: 300 !important;
            margin-right: 6px !important;
            transition: color 0.2s ease;
        }

        .select2-container--default .select2-selection__clear:hover {
            color: #dc3545 !important;
        }

        /* ── Dropdown Panel ── */
        .select2-dropdown {
            border: 1px solid #d1d9e6 !important;
            border-radius: 0.75rem !important;
            box-shadow:
                0 10px 40px rgba(4, 23, 65, 0.12),
                0 2px 10px rgba(4, 23, 65, 0.06) !important;
            overflow: hidden;
            margin-top: 5px !important;
            animation: s2DropIn 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            background: #fff !important;
        }

        .select2-dropdown--above {
            margin-top: 0 !important;
            margin-bottom: 5px !important;
        }

        @keyframes s2DropIn {
            0% {
                opacity: 0;
                transform: translateY(-6px) scale(0.98);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ── Search Box ── */
        .select2-container--default .select2-search--dropdown {
            padding: 10px 10px 6px !important;
            background: #f8fafc;
            border-bottom: 1px solid #eef2f7;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1.5px solid #d1d9e6 !important;
            border-radius: 0.5rem !important;
            padding: 9px 14px 9px 38px !important;
            font-size: 13.5px !important;
            font-weight: 400 !important;
            color: #1e293b !important;
            background-color: #fff !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='%23041741' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: 12px center !important;
            background-size: 15px !important;
            transition: all 0.2s ease !important;
            outline: none !important;
        }

        [dir="rtl"] .select2-container--default .select2-search--dropdown .select2-search__field {
            padding: 9px 38px 9px 14px !important;
            background-position: right 12px center !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #041741 !important;
            box-shadow: 0 0 0 3px rgba(4, 23, 65, 0.08) !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field::placeholder {
            color: #94a3b8 !important;
        }

        /* ── Results List ── */
        .select2-results__options {
            max-height: 260px !important;
            padding: 4px 6px !important;
            scrollbar-width: thin;
            scrollbar-color: rgba(4, 23, 65, 0.15) transparent;
        }

        .select2-results__options::-webkit-scrollbar {
            width: 5px;
        }

        .select2-results__options::-webkit-scrollbar-track {
            background: transparent;
        }

        .select2-results__options::-webkit-scrollbar-thumb {
            background: rgba(4, 23, 65, 0.15);
            border-radius: 10px;
        }

        .select2-results__options::-webkit-scrollbar-thumb:hover {
            background: rgba(4, 23, 65, 0.3);
        }

        .select2-container--default .select2-results__option {
            padding: 9px 14px !important;
            font-size: 13.5px !important;
            font-weight: 450 !important;
            color: #334155 !important;
            border-radius: 0.4rem !important;
            margin: 1px 0 !important;
            transition: all 0.15s ease !important;
            cursor: pointer;
            position: relative;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: rgba(4, 23, 65, 0.06) !important;
            color: #041741 !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #041741 !important;
            color: #fff !important;
            font-weight: 600 !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true]::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-size: contain;
        }

        [dir="rtl"] .select2-container--default .select2-results__option[aria-selected=true]::after {
            right: auto;
            left: 12px;
        }

        .select2-container--default .select2-results__option[aria-selected=true]:hover {
            background-color: #08286b !important;
        }

        /* ── "No results" message ── */
        .select2-container--default .select2-results__message {
            color: #94a3b8 !important;
            font-size: 13px !important;
            padding: 16px 12px !important;
            text-align: center;
        }

        /* ── Multiple Selection Tags ── */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: rgba(4, 23, 65, 0.07) !important;
            border: 1px solid rgba(4, 23, 65, 0.15) !important;
            border-radius: 0.375rem !important;
            color: #041741 !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            padding: 3px 8px !important;
            margin: 3px 4px 3px 0 !important;
        }

        [dir="rtl"] .select2-container--default .select2-selection--multiple .select2-selection__choice {
            margin: 3px 0 3px 4px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #041741 !important;
            font-weight: 700 !important;
            margin-right: 5px !important;
            transition: color 0.15s ease;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #dc3545 !important;
        }

        /* ── Modal Fix: Ensure dropdown appears above modals ── */
        .select2-container--open {
            z-index: 9999 !important;
        }

        /* ── Disabled State ── */
        .select2-container--disabled .select2-selection--single,
        .select2-container--disabled .select2-selection--multiple {
            background-color: #f1f5f9 !important;
            cursor: not-allowed !important;
            opacity: 0.7;
        }

        /* ── Loading Indicator ── */
        .select2-container--default .select2-results__option.loading-results {
            text-align: center !important;
            color: #94a3b8 !important;
            padding: 14px !important;
        }

        /* ── Validation Error State ── */
        .is-invalid + .select2-container--default .select2-selection--single,
        .is-invalid + .select2-container--default .select2-selection--multiple {
            border-color: #dc3545 !important;
        }

        .is-invalid + .select2-container--default.select2-container--open .select2-selection--single,
        .is-invalid + .select2-container--default.select2-container--focus .select2-selection--single {
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.12) !important;
        }
    </style>

    {{-- ═══ PREMIUM TRANSLATION BUTTON ═══ --}}
    <style>
        /* ══════════════════════════════════════════════════════════════ */
        /* AUTO-TRANSLATE BUTTON — Premium Pill Badge                   */
        /* ══════════════════════════════════════════════════════════════ */
        .translate-auto-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px 3px 7px;
            border: none;
            border-radius: 20px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #fff;
            font-size: 11.5px;
            font-weight: 600;
            font-family: 'Inter', 'Cairo', 'Segoe UI', system-ui, sans-serif;
            line-height: 1.4;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 4px rgba(99, 102, 241, 0.25);
            position: relative;
            overflow: hidden;
            text-decoration: none !important;
        }

        .translate-auto-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 60%);
            border-radius: inherit;
            pointer-events: none;
        }

        .translate-auto-btn:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            box-shadow: 0 3px 12px rgba(99, 102, 241, 0.35);
            transform: translateY(-1px);
        }

        .translate-auto-btn:active {
            transform: translateY(0) scale(0.97);
            box-shadow: 0 1px 4px rgba(99, 102, 241, 0.2);
        }

        .translate-auto-btn:disabled {
            cursor: wait;
            opacity: 0.85;
            transform: none !important;
        }

        /* ── Icon Container ── */
        .translate-auto-btn__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            flex-shrink: 0;
        }

        .translate-auto-btn__icon svg {
            width: 13px;
            height: 13px;
        }

        /* ── Text ── */
        .translate-auto-btn__text {
            letter-spacing: 0.02em;
        }

        /* ── Flag Emoji ── */
        .translate-auto-btn__flag {
            font-size: 13px;
            line-height: 1;
            margin-inline-start: 1px;
        }

        /* ── Loading State ── */
        .translate-auto-btn--loading {
            background: linear-gradient(135deg, #818cf8 0%, #a78bfa 100%);
            pointer-events: none;
        }

        .translate-auto-btn__spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: translateBtnSpin 0.6s linear infinite;
            flex-shrink: 0;
        }

        @keyframes translateBtnSpin {
            to { transform: rotate(360deg); }
        }

        /* ── Success State ── */
        .translate-auto-btn--success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3) !important;
            animation: translateBtnPulse 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes translateBtnPulse {
            0% { transform: scale(0.95); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* ── Form Label Alignment Fix ── */
        .form-group label:has(.translate-auto-btn),
        .mb-3 label:has(.translate-auto-btn),
        .form-label:has(.translate-auto-btn) {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 6px;
        }

        /* ── Mobile Responsive ── */
        @media (max-width: 480px) {
            .translate-auto-btn {
                padding: 2px 8px 2px 5px;
                font-size: 10.5px;
                gap: 4px;
            }

            .translate-auto-btn__icon {
                width: 18px;
                height: 18px;
            }

            .translate-auto-btn__icon svg {
                width: 11px;
                height: 11px;
            }

            .translate-auto-btn__flag {
                font-size: 11px;
            }
        }
    </style>

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

    <style>
        /* ============================================================ */
        /* PREMIUM PAGE TITLES LAYOUT                                   */
        /* ============================================================ */
        .page-titles {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            background: #ffffff !important;
            padding: 1.25rem 1.5rem !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03) !important;
            margin-bottom: 2rem !important;
            border: 1px solid #f1f5f9 !important;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-titles .breadcrumb {
            margin-bottom: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            display: flex;
            align-items: center;
        }

        .page-titles .breadcrumb .breadcrumb-item {
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .page-titles .breadcrumb .breadcrumb-item a {
            color: #64748b;
            text-decoration: none;
            transition: color 0.2s;
        }

        .page-titles .breadcrumb .breadcrumb-item a:hover {
            color: #041741;
        }

        .page-titles .breadcrumb .breadcrumb-item.active a {
            color: #041741;
            font-weight: 700;
        }

        /* Customize breadcrumb separator */
        .page-titles .breadcrumb-item + .breadcrumb-item::before {
            content: "\f105" !important; /* FontAwesome angle-right */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            font-size: 1rem;
            line-height: 1;
            vertical-align: middle;
            color: #cbd5e1;
            padding: 0 0.7rem;
            float: none;
        }

        [dir="rtl"] .page-titles .breadcrumb-item + .breadcrumb-item::before {
            content: "\f104" !important; /* FontAwesome angle-left for RTL */
        }

        /* Ensure buttons inside page-titles align properly */
        .page-titles > button, 
        .page-titles > a.btn,
        .page-titles .btn-primary {
            margin: 0 !important;
            padding: 0.6rem 1.5rem !important;
            font-weight: 600 !important;
            display: inline-flex !important;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(4, 23, 65, 0.15) !important;
            transition: all 0.3s ease !important;
            border-radius: 8px !important;
        }

        .page-titles > button:hover, 
        .page-titles > a.btn:hover,
        .page-titles .btn-primary:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 15px rgba(4, 23, 65, 0.25) !important;
        }
    </style>

    @stack('styles')
    {{-- ═══ PREMIUM GLOBAL SELECT2 FILTERS ═══ --}}
    <style>
        /* Modern Select2 Container for Filters */
        .filter-wrapper {
            position: relative;
            display: inline-block;
            min-width: 180px;
        }
        .filter-wrapper .select2-container--default .select2-selection--single {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 50px !important;
            height: 42px !important;
            padding: 5px 40px 5px 20px !important; /* LTR padding */
            display: flex;
            align-items: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02) !important;
            transition: all 0.3s ease !important;
            font-weight: 500 !important;
            color: #334155 !important;
            font-size: 13px !important;
        }
        .rtl .filter-wrapper .select2-container--default .select2-selection--single {
            padding: 5px 20px 5px 40px !important; /* RTL padding */
        }
        .filter-wrapper .select2-container--default .select2-selection--single:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
        }
        .filter-wrapper .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1) !important;
        }
        .filter-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 15px !important; /* LTR arrow */
        }
        .rtl .filter-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
            right: auto !important;
            left: 15px !important; /* RTL arrow */
        }
        .filter-wrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #475569 !important;
            line-height: 28px !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        /* Icon inside wrapper */
        .filter-wrapper .filter-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 15px;
            color: #94a3b8;
            font-size: 13px;
            z-index: 5;
            pointer-events: none;
            transition: color 0.3s ease;
        }
        .rtl .filter-wrapper .filter-icon {
            left: auto;
            right: 15px;
        }
        .filter-wrapper:hover .filter-icon {
            color: #3b82f6;
        }
        .rtl .filter-wrapper .select2-container--default .select2-selection--single {
            padding-right: 40px !important;
            padding-left: 20px !important;
        }
        /* Dropdown Styling */
        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
            overflow: hidden !important;
            margin-top: 5px !important;
            animation: fadeInDrop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            background: #f8fafc !important;
        }
        .select2-search--dropdown .select2-search__field:focus {
            outline: none !important;
            border-color: #3b82f6 !important;
            background: #fff !important;
        }
        .select2-results__option {
            padding: 10px 16px !important;
            font-size: 13px !important;
            color: #475569 !important;
            transition: all 0.2s ease !important;
        }
        .select2-results__option--highlighted[aria-selected] {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 600 !important;
        }
        @keyframes fadeInDrop {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

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

                <!-- Profile Completion Banner -->
                @if(auth()->check() && auth()->user()->isCustomer() && !auth()->user()->isProfileComplete() && !request()->routeIs('profile.complete.*') && !request()->routeIs('customer.profile*'))
                    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(255, 193, 7, 0.15); border: 1px solid #ffc107;">
                        <i class="fas fa-exclamation-circle fs-24 me-3 rtl-flip" style="color: #d39e00;"></i>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="alert-heading mb-1 fw-bold text-dark">{{ __('يجب إكمال بيانات حسابك') }}</h5>
                            <p class="mb-0 text-dark">{{ __('للاستفادة من كافة مزايا المنصة وإتمام حجوزاتك، يرجى إكمال بيانات ملفك الشخصي.') }}</p>
                        </div>
                        <a href="{{ route('profile.complete.form') }}" class="btn btn-dark btn-sm px-4 ms-auto" style="white-space: nowrap; border-radius: 8px;">
                            {{ __('إكمال البيانات') }}
                        </a>
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

    <script>
    (function() {
        function initTranslations() {
            // Find all inputs and textareas that end with _ar or are named 'name'
            document.querySelectorAll('input[name$="_ar"], textarea[name$="_ar"], input[name="name"], textarea[name="name"]').forEach(arField => {
                const nameAr = arField.getAttribute('name');
                if (!nameAr) return;
                
                // Determine the matching English field name
                let nameEn;
                if (nameAr === 'name') {
                    nameEn = 'en_name';
                } else {
                    nameEn = nameAr.replace(/_ar(\]?)$/, '_en$1');
                }
                
                // Find the closest container (form, modal, or document) to prevent matching across different modals
                const container = arField.closest('form, .modal, .card, body') || document;
                const enField = container.querySelector(`[name="${nameEn}"]`);
                
                if (enField) {
                    // If we found a pair, let's add translate buttons if not already added
                    addTranslationButton(arField, enField, 'ar');
                    addTranslationButton(enField, arField, 'en');
                }
            });
        }
        
        function addTranslationButton(field, targetField, type) {
            // Check if button is already added to prevent duplicates
            if (field.dataset.hasTranslateBtn === 'true') return;
            
            // Find parent form-group or input container
            const parent = field.closest('.form-group, .mb-3, .mb-4, .col-md-12, .col-md-6, .col-md-4, .col-12, .col-lg-6, .col-lg-12, .col-xl-6, .col-xl-12, .tab-pane, td');
            if (!parent) return;
            
            let label = parent.querySelector('label');
            
            // If no label, create a small container for the translate button above the field
            if (!label) {
                const wrapper = document.createElement('div');
                wrapper.className = 'translate-btn-wrapper mb-1';
                wrapper.style.display = 'flex';
                wrapper.style.justifyContent = 'flex-end';
                field.parentNode.insertBefore(wrapper, field);
                label = wrapper;
            }
            
            // Mark field as handled
            field.dataset.hasTranslateBtn = 'true';
            
            // Check if it's a floating label (used in Banner modal)
            const isFloating = field.closest('.form-floating') !== null;
            
            // For standard labels, ensure flex alignment
            if (!isFloating) {
                label.style.display = 'flex';
                label.style.justifyContent = 'space-between';
                label.style.alignItems = 'center';
                label.style.width = '100%';
                label.style.flexWrap = 'wrap';
                label.style.gap = '4px';
            }
            
            // Create premium translation button
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'translate-auto-btn';
            btn.setAttribute('title', type === 'ar' ? 'ترجمة تلقائية للإنجليزية' : 'ترجمة تلقائية للعربية');

            // Flag + label based on translation direction
            const flagIcon = type === 'ar' ? '🇬🇧' : '🇸🇦';
            const labelText = type === 'ar' ? 'ترجمة EN' : 'ترجمة AR';
            
            btn.innerHTML = `
                <span class="translate-auto-btn__icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 8l6 6"/>
                        <path d="M4 14l6-6 2-3"/>
                        <path d="M2 5h12"/>
                        <path d="M7 2h1"/>
                        <path d="M22 22l-5-10-5 10"/>
                        <path d="M14 18h6"/>
                    </svg>
                </span>
                <span class="translate-auto-btn__text">${labelText}</span>
                <span class="translate-auto-btn__flag">${flagIcon}</span>
            `;
            
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const sourceText = field.value.trim();
                if (!sourceText) {
                    if (typeof toastr !== 'undefined') {
                        toastr.warning(type === 'ar' ? 'يرجى كتابة نص للترجمة أولاً' : 'Please type text to translate first');
                    } else {
                        alert(type === 'ar' ? 'يرجى كتابة نص للترجمة أولاً' : 'Please type text to translate first');
                    }
                    return;
                }
                
                // Show loading state
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.classList.add('translate-auto-btn--loading');
                btn.innerHTML = `
                    <span class="translate-auto-btn__spinner"></span>
                    <span class="translate-auto-btn__text">{{ __('جاري الترجمة') }}</span>
                `;
                
                $.ajax({
                    url: '{{ route("admin.translate") }}',
                    type: 'POST',
                    data: {
                        text: sourceText,
                        target: type === 'ar' ? 'en' : 'ar',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            targetField.value = response.translated;
                            // Trigger change event to notify any plugins/editors
                            targetField.dispatchEvent(new Event('change'));
                            
                            // Show success flash on button
                            btn.classList.add('translate-auto-btn--success');
                            btn.innerHTML = `
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span class="translate-auto-btn__text">{{ __('تمت الترجمة') }}</span>
                            `;
                            setTimeout(function() {
                                btn.classList.remove('translate-auto-btn--success');
                                btn.innerHTML = originalHtml;
                            }, 2000);
                            
                            if (typeof toastr !== 'undefined') {
                                toastr.success(type === 'ar' ? 'تمت الترجمة بنجاح' : 'Translated successfully');
                            }
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'Translation failed');
                            }
                        }
                    },
                    error: function(xhr) {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Translation service error');
                        }
                        console.error(xhr);
                    },
                    complete: function() {
                        btn.disabled = false;
                        btn.classList.remove('translate-auto-btn--loading');
                        // Only restore if not showing success state
                        if (!btn.classList.contains('translate-auto-btn--success')) {
                            btn.innerHTML = originalHtml;
                        }
                    }
                });
            });
            if (isFloating) {
                const floatingContainer = field.closest('.form-floating');
                
                // Convert form-floating to standard layout:
                // 1. Remove form-floating class so Bootstrap doesn't force the label inside
                floatingContainer.classList.remove('form-floating');
                
                // 2. Move the label BEFORE the input (out of the floating container, above it)
                const floatingLabel = floatingContainer.querySelector('label');
                if (floatingLabel) {
                    // Style the label like a standard form-label
                    floatingLabel.classList.add('form-label', 'fw-semibold');
                    floatingLabel.style.display = 'flex';
                    floatingLabel.style.justifyContent = 'space-between';
                    floatingLabel.style.alignItems = 'center';
                    floatingLabel.style.width = '100%';
                    floatingLabel.style.flexWrap = 'wrap';
                    floatingLabel.style.gap = '4px';
                    
                    // Insert label before the container (now just a position-relative div)
                    parent.insertBefore(floatingLabel, floatingContainer);
                    
                    // Append translate button inside the label
                    floatingLabel.appendChild(btn);
                } else {
                    // Fallback: insert button before the container
                    parent.insertBefore(btn, floatingContainer);
                }
            } else {
                // Default behavior for normal labels
                label.appendChild(btn);
            }
        }

        
        // Initial scan on load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTranslations);
        } else {
            initTranslations();
        }
        
        // Mutation observer to handle dynamically added fields (e.g. dynamic forms, modals, etc.)
        const observer = new MutationObserver(function(mutations) {
            initTranslations();
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    })();
    </script>

    {{-- ═══ GLOBAL SELECT2 AUTO-INIT ═══ --}}
    <script>
    (function() {
        'use strict';

        var isRtl = document.documentElement.dir === 'rtl';

        /**
         * Initialize Select2 on all .form-select elements that have
         * the .select2 or .select-search class.
         * Also auto-inits any .form-select inside modals when shown.
         */
        function initSelect2Elements(container) {
            container = container || document;
            $(container).find('select:not(.dataTables_wrapper select):not(.nice-select):not(.swal2-select):not([data-no-select2="true"])').each(function() {
                var $el = $(this);
                if ($el.data('select2')) return; // already initialized

                var $modal = $el.closest('.modal');
                var config = {
                    placeholder: $el.attr('placeholder') || $el.find('option[value=""]').text() || '{{ __("اختر...") }}',
                    allowClear: !$el.prop('required'),
                    width: '100%',
                    dir: isRtl ? 'rtl' : 'ltr',
                    minimumResultsForSearch: $el.attr('data-hide-search') === 'true' ? Infinity : 0,
                    language: {
                        noResults: function() {
                            return '{{ __("لا توجد نتائج") }}';
                        },
                        searching: function() {
                            return '{{ __("جاري البحث...") }}';
                        },
                        inputTooShort: function(args) {
                            return '{{ __("اكتب حرفاً واحداً على الأقل") }}';
                        }
                    }
                };

                // If inside a modal, attach dropdown to the modal so it stays above
                if ($modal.length) {
                    config.dropdownParent = $modal;
                }

                $el.select2(config);
            });
        }

        // Init on DOM ready
        $(document).ready(function() {
            initSelect2Elements();
        });

        // Re-init when modals open (handles dynamic content)
        $(document).on('shown.bs.modal', function(e) {
            initSelect2Elements(e.target);
        });

        // Re-init on AJAX/dynamic content (MutationObserver)
        var observer = new MutationObserver(function(mutations) {
            var shouldInit = false;
            mutations.forEach(function(m) {
                if (m.addedNodes.length) shouldInit = true;
            });
            if (shouldInit) {
                setTimeout(function() { initSelect2Elements(); }, 50);
            }
        });

        observer.observe(document.body, { childList: true, subtree: true });
    })();
    </script>

    @stack('scripts')
</body>
</html>
