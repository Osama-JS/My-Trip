<!DOCTYPE html>
@php
    $locale = session('locale', app()->getLocale());
    $dir = $locale == 'ar' ? 'rtl' : 'ltr';
@endphp
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('Login')) - {{ config('app.name', 'My Trip') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset(\App\Models\Setting::get('site_favicon', 'images/favicon.png')) }}">

    <!-- Global Scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

    <!-- CSS Assets -->
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/font-awesome/css/all.min.css') }}" rel="stylesheet">

    <!-- Tailwind / Vite -->
    @vite(['resources/css/app.css'])

    <style>
        body, html {
            height: 100%;
            overflow-x: hidden;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .login-split-screen {
            min-height: 100vh;
            display: flex;
            flex-wrap: wrap;
            width: 100%;
        }

        /* Left Side (Banner) */
        .login-banner {
            flex: 0 0 65%;
            max-width: 65%;
            background-image: url('https://images.pexels.com/photos/2033343/pexels-photo-2033343.jpeg');
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: flex-end;
            padding: 4rem;
            color: white;
        }

        .login-banner::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.1) 100%);
            z-index: 1;
        }

        /* RTL specific gradient */
        [dir="rtl"] .login-banner::after {
            background: linear-gradient(to left, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.1) 100%);
        }

        .banner-content {
            position: relative;
            z-index: 2;
            max-width: 600px;
            animation: fadeInUp 1s ease;
        }

        .banner-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
            line-height: 1.2;
        }

        .banner-content p {
            font-size: 1.25rem;
            opacity: 0.95;
            text-shadow: 1px 1px 5px rgba(0,0,0,0.3);
            font-weight: 300;
        }

        /* Right Side (Form) */
        .login-form-container {
            flex: 0 0 35%;
            max-width: 35%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            box-shadow: -10px 0 30px rgba(0,0,0,0.05);
            z-index: 10;
        }

        /* In RTL, box shadow should be on the RIGHT side */
        [dir="rtl"] .login-form-container {
            box-shadow: 10px 0 30px rgba(0,0,0,0.05);
        }

        .login-content {
            width: 100%;
            max-width: 450px;
            padding: 2rem;
        }

        .brand-logo {
            margin-bottom: 2.5rem;
            display: block;
        }

        .brand-logo img {
            max-height: 50px;
        }

        .welcome-text h3 {
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }

        .welcome-text p {
            color: #777;
            margin-bottom: 2.5rem;
        }

        .form-control {
            height: 55px;
            border-radius: 12px;
            border: 1px solid #eee;
            background: #fcfcfc;
            padding-left: 1.5rem; /* LTR default */
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        [dir="rtl"] .form-control {
            padding-left: 0.75rem;
            padding-right: 1.5rem;
        }

        .form-control:focus {
            border-color: #fa1600;
            box-shadow: 0 0 0 4px rgba(250, 22, 0, 0.05);
            background: #fff;
        }

        .btn-primary {
            background-color: #fa1600;
            border-color: #fa1600;
            height: 55px;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary:hover {
            background-color: #d41300;
            border-color: #d41300;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(250, 22, 0, 0.2);
        }

        /* Language Switcher */
        .lang-switch-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            border-radius: 30px;
            border: 1px solid #e0e0e0;
            background: white;
            color: #666;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
            text-decoration: none;
            margin-top: 20px;
        }

        .lang-switch-btn:hover {
            background: #f8f9fa;
            color: #fa1600;
            border-color: #fa1600;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        /* Arrow icon flip in RTL */
        [dir="rtl"] .fa-arrow-right {
            transform: rotate(180deg);
        }

        /* Responsive */
        @media (max-width: 991px) {
            .login-banner {
                /* Hide banner on mobile to focus on login */
                display: none;
            }
            .login-form-container {
                flex: 0 0 100%;
                max-width: 100%;
                /* Remove shadow on mobile as it takes full width */
                box-shadow: none;
            }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="login-split-screen">

    <!-- Banner Side -->
    <div class="login-banner">
        <div class="banner-content">
            <h1>{{ __('Start Your Next') }} <br> {{ __('Adventure Details') }}</h1>
            <p>{{ __('Manage your flights, bookings, and entire platform efficiently.') }}</p>
        </div>
    </div>

    <!-- Form Side -->
    <div class="login-form-container">

        <div class="login-content">
            <div class="brand-logo">
                <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="Logo">
            </div>

            <div class="welcome-text">
                <h3>{{ __('Welcome Back!') }}</h3>
                <p>{{ __('Please sign in to continue.') }}</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show mb-4">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label class="mb-2 font-weight-bold text-muted">{{ __('Email Address') }}</label>
                    <div class="position-relative">
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                    </div>
                    @error('email')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="mb-2 font-weight-bold text-muted">{{ __('Password') }}</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                    @error('password')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                        <label class="form-check-label text-muted" for="remember_me">
                            {{ __('Remember me') }}
                        </label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-danger small font-weight-bold">{{ __('Forgot Password?') }}</a>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ __('Sign In') }} <i class="fa fa-arrow-right ms-2"></i>
                </button>

                <div class="text-center">
                    @if($locale == 'ar')
                        <a href="{{ route('lang.switch', 'en') }}" class="lang-switch-btn">
                            <i class="fa fa-globe"></i> English
                        </a>
                    @else
                        <a href="{{ route('lang.switch', 'ar') }}" class="lang-switch-btn">
                            <i class="fa fa-globe"></i> العربية
                        </a>
                    @endif
                </div>

            </form>

            <div class="mt-5 text-center text-muted small">
                &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
            </div>
        </div>
    </div>
</div>

<!-- Vite JS -->
{{-- @vite(['resources/js/app.js']) --}}
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>
