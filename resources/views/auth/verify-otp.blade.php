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

    <title>{{ __('Verify OTP') }} - {{ config('app.name', 'Fly Vio') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset(\App\Models\Setting::get('site_favicon', 'images/favicon.png')) }}">

    <!-- CSS Assets -->
    <link href="{{ asset('vendor/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/font-awesome/css/all.min.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css'])

    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Tajawal', sans-serif;
            background: #f8fafc;
        }

        .otp-page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .otp-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.06);
            width: 100%;
            max-width: 500px;
            padding: 3rem;
            text-align: center;
            animation: fadeInUp 0.6s ease;
        }

        .brand-logo {
            margin-bottom: 2rem;
        }

        .brand-logo img {
            max-height: 50px;
        }

        .otp-header h3 {
            font-weight: 800;
            color: #1e293b;
            font-size: 1.75rem;
            margin-bottom: 0.75rem;
        }

        .otp-header p {
            color: #64748b;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .otp-header span.email-highlight {
            color: #0f4c81;
            font-weight: 700;
        }

        .otp-input-container {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 2.5rem;
            direction: ltr; /* Always LTR for code input */
        }

        .otp-input {
            width: 55px;
            height: 65px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            transition: all 0.2s;
        }

        .otp-input:focus {
            border-color: #0f4c81;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(15, 76, 129, 0.05);
            outline: none;
        }

        .btn-primary {
            background-color: #0f4c81;
            border-color: #0f4c81;
            height: 55px;
            border-radius: 14px;
            font-size: 1.1rem;
            font-weight: 700;
            width: 100%;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #0a3560;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(15, 76, 129, 0.2);
        }

        .resend-container {
            margin-top: 2rem;
            font-size: 0.95rem;
            color: #64748b;
        }

        .resend-link {
            color: #0f4c81;
            font-weight: 700;
            text-decoration: none;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            transition: color 0.2s;
        }

        .resend-link:hover {
            color: #0a3560;
            text-decoration: underline;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Error Message Styles */
        .error-message {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: #ef4444;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>

<div class="otp-page-wrapper">
    <div class="otp-card">
        <div class="brand-logo">
            <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="Logo">
        </div>

        <div class="otp-header">
            <h3>{{ __('Verify Your Email') }}</h3>
            <p>{{ __('We\'ve sent a 6-digit verification code to') }} <br> <span class="email-highlight">{{ $email }}</span></p>
        </div>

        @if ($errors->any())
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div style="background: #f0fdf4; border: 1px solid #dcfce7; color: #16a34a; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.9rem;">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('auth.verify-otp') }}" id="otpForm">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div class="otp-input-container">
                <input type="text" name="otp[]" class="otp-input" maxlength="1" required pattern="\d*">
                <input type="text" name="otp[]" class="otp-input" maxlength="1" required pattern="\d*">
                <input type="text" name="otp[]" class="otp-input" maxlength="1" required pattern="\d*">
                <input type="text" name="otp[]" class="otp-input" maxlength="1" required pattern="\d*">
                <input type="text" name="otp[]" class="otp-input" maxlength="1" required pattern="\d*">
                <input type="text" name="otp[]" class="otp-input" maxlength="1" required pattern="\d*">
            </div>

            <button type="submit" class="btn-primary">
                {{ __('Verify Account') }} <i class="fas fa-chevron-{{ app()->isLocale('ar') ? 'left' : 'right' }} ms-1"></i>
            </button>
        </form>

        <div class="resend-container">
            {{ __('Didn\'t receive the code?') }}
            <form method="POST" action="{{ route('auth.resend-otp') }}" style="display: inline;">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit" class="resend-link">{{ __('Resend Code') }}</button>
            </form>
        </div>

        <div style="margin-top: 2rem;">
            <a href="{{ route('register') }}" style="color: #64748b; font-size: 0.9rem; text-decoration: none;">
                <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'right' : 'left' }} me-1"></i> {{ __('Back to Register') }}
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.otp-input');
        
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Handle paste
            input.addEventListener('paste', (e) => {
                const data = e.clipboardData.getData('text').slice(0, 6).split('');
                if (data.length > 0) {
                    data.forEach((char, i) => {
                        if (inputs[i]) inputs[i].value = char;
                    });
                    inputs[Math.min(data.length, 5)].focus();
                    e.preventDefault();
                }
            });
        });
    });
</script>

</body>
</html>
