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

    <title>{{ __('Create New Password') }} - {{ config('app.name', 'Fly Vio') }}</title>

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
            font-family: 'Poppins', 'Tajawal', sans-serif;
            background: #f8fafc;
        }

        .auth-page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.06);
            width: 100%;
            max-width: 500px;
            padding: 3rem;
            animation: fadeInUp 0.6s ease;
        }

        .brand-logo {
            margin-bottom: 2rem;
            text-align: center;
        }

        .brand-logo img {
            max-height: 50px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-header h3 {
            font-weight: 800;
            color: #1e293b;
            font-size: 1.75rem;
            margin-bottom: 0.75rem;
        }

        .auth-header p {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .form-control {
            height: 55px;
            border-radius: 12px;
            border: 1px solid #eee;
            background: #fcfcfc;
            padding: 0 1.25rem;
            font-size: 0.95rem;
            transition: all 0.3s;
            width: 100%;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #0f4c81;
            box-shadow: 0 0 0 4px rgba(15, 76, 129, 0.05);
            background: #fff;
            outline: none;
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
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 1.5rem;
        }

        .btn-primary:hover {
            background-color: #0a3560;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(15, 76, 129, 0.2);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="auth-page-wrapper">
    <div class="auth-card">
        <div class="brand-logo">
            <a href="{{ route('home') }}">
                <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="Logo">
            </a>
        </div>

        <div class="auth-header">
            <h3>{{ __('Create New Password') }}</h3>
            <p>{{ __('Please enter your new password below.') }}</p>
        </div>

        @if ($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fee2e2; color: #ef4444; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.9rem;">
                <ul style="margin: 0; padding-inline-start: 1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div style="margin-bottom: 1.25rem;">
                <label class="mb-2" style="font-weight: 600; color: #475569; display: block; font-size: 0.9rem;">{{ __('Email Address') }}</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $request->email) }}" required readonly style="background-color: #f1f5f9; color: #64748b;">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label class="mb-2" style="font-weight: 600; color: #475569; display: block; font-size: 0.9rem;">{{ __('New Password') }}</label>
                <input type="password" name="password" class="form-control" required autofocus placeholder="••••••••">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label class="mb-2" style="font-weight: 600; color: #475569; display: block; font-size: 0.9rem;">{{ __('Confirm New Password') }}</label>
                <input type="password" name="password_confirmation" class="form-control" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-primary">
                {{ __('Update Password') }} <i class="fas fa-lock ms-2"></i>
            </button>
        </form>
    </div>
</div>

</body>
</html>
