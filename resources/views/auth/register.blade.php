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

    <title>{{ __('Register') }} - {{ config('app.name', 'Fly Vio') }}</title>

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

        .register-split-screen {
            min-height: 100vh;
            display: flex;
            flex-wrap: wrap;
            width: 100%;
        }

        /* Left Side (Banner) */
        .register-banner {
            flex: 0 0 50%;
            max-width: 50%;
            background-image: url('https://images.pexels.com/photos/2108845/pexels-photo-2108845.jpeg'); /* Travel-themed image */
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: flex-end;
            padding: 4rem;
            color: white;
        }

        .register-banner::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.2) 100%);
            z-index: 1;
        }

        /* RTL specific gradient */
        [dir="rtl"] .register-banner::after {
            background: linear-gradient(to left, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.2) 100%);
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
        .register-form-container {
            flex: 0 0 50%;
            max-width: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            position: relative;
            box-shadow: -10px 0 30px rgba(0,0,0,0.05);
            z-index: 10;
        }

        /* In RTL, box shadow should be on the RIGHT side */
        [dir="rtl"] .register-form-container {
            box-shadow: 10px 0 30px rgba(0,0,0,0.05);
        }

        .register-content {
            width: 100%;
            max-width: 600px;
            padding: 1rem;
        }

        .brand-logo {
            margin-bottom: 2rem;
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
            margin-bottom: 2rem;
        }

        .form-control {
            height: 50px;
            border-radius: 12px;
            border: 1px solid #eee;
            background: #fcfcfc;
            padding: 0 1.25rem;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #0f4c81;
            box-shadow: 0 0 0 4px rgba(15, 76, 129, 0.05);
            background: #fff;
        }

        .btn-primary {
            background-color: #0f4c81;
            border-color: #0f4c81;
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
            margin-top: 1rem;
        }

        .btn-primary:hover {
            background-color: #0a3560;
            border-color: #0a3560;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(15, 76, 129, 0.2);
        }

        .row-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Language Switcher */
        .lang-switch-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 30px;
            border: 1px solid #e0e0e0;
            background: white;
            color: #666;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s;
            text-decoration: none;
            margin-top: 15px;
        }

        .lang-switch-btn:hover {
            background: #f8f9fa;
            color: #0f4c81;
            border-color: #0f4c81;
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 1100px) {
            .register-banner {
                display: none;
            }
            .register-form-container {
                flex: 0 0 100%;
                max-width: 100%;
                box-shadow: none;
            }
        }

        @media (max-width: 600px) {
            .row-grid {
                grid-template-columns: 1fr;
            }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="register-split-screen">

    <!-- Banner Side -->
    <div class="register-banner">
        <div class="banner-content">
            <h1>{{ __('Join the World\'s') }} <br> {{ __('Elite Travelers') }}</h1>
            <p>{{ __('Create your account and discover premium tour packages, easy flights and seamless hotel bookings across the globe.') }}</p>
        </div>
    </div>

    <!-- Form Side -->
    <div class="register-form-container">

        <div class="register-content">
            <div class="brand-logo">
                <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="Logo">
            </div>

            <div class="welcome-text">
                <h3>{{ __('Create an Account') }}</h3>
                <p>{{ __('Start your journey with us today.') }}</p>
            </div>

            {{-- Unverified Account Alert --}}
            @if (session('unverified_email'))
                <div class="alert alert-warning mb-4" style="border-radius: 12px; padding: 15px; border: 1px solid #ffeeba; background: #fff3cd; color: #856404; font-size: .9rem;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-info-circle"></i>
                        <strong>{{ __('This email is already registered but not verified.') }}</strong>
                    </div>
                    <p class="mb-3">{{ __('Would you like to resend the verification code?') }}</p>
                    <form method="POST" action="{{ route('auth.resend-otp') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('unverified_email') }}">
                        <button type="submit" class="btn btn-sm" style="background: #856404; color: #fff; border-radius: 8px; font-weight: 600;">
                            {{ __('Resend Code') }}
                        </button>
                    </form>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="row-grid">
                    <div class="mb-3">
                        <label class="mb-2 font-weight-bold text-muted small" style="text-transform:uppercase; letter-spacing:1px">{{ __('First Name') }}</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required autofocus placeholder="{{ __('First Name') }}">
                        @error('first_name')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="mb-2 font-weight-bold text-muted small" style="text-transform:uppercase; letter-spacing:1px">{{ __('Last Name') }}</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required placeholder="{{ __('Last Name') }}">
                        @error('last_name')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="mb-2 font-weight-bold text-muted small" style="text-transform:uppercase; letter-spacing:1px">{{ __('Email Address') }}</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="name@example.com">
                    @error('email')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="mb-2 font-weight-bold text-muted small" style="text-transform:uppercase; letter-spacing:1px">{{ __('Phone Number') }}</label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" required placeholder="+966 5x xxx xxxx">
                    @error('phone')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row-grid">
                    <div class="mb-3">
                        <label class="mb-2 font-weight-bold text-muted small" style="text-transform:uppercase; letter-spacing:1px">{{ __('Password') }}</label>
                        <input type="password" name="password" class="form-control" required placeholder="••••••••" minlength="8">
                        @error('password')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="mb-2 font-weight-bold text-muted small" style="text-transform:uppercase; letter-spacing:1px">{{ __('Confirm Password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="••••••••" minlength="8">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ __('Create Account') }} <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'left' : 'right' }} ms-2"></i>
                </button>

                <div class="text-center mt-4">
                    <div class="text-muted small">
                        {{ __('Already have an account?') }}
                        <a href="{{ route('login') }}" class="text-danger font-weight-bold ml-1">{{ __('Sign In') }}</a>
                    </div>

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

<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>
