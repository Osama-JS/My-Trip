<!DOCTYPE html>
@php
    $locale = session('locale', app()->getLocale());
    $dir = $locale == 'ar' ? 'rtl' : 'ltr';
    $otpMethod = \App\Models\Setting::get('otp_method', 'email');
@endphp
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('Login')) - {{ config('app.name', 'Fly Vio') }}</title>

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
        /* Custom Country Picker */
        .country-picker-wrapper { position: relative; display: flex; align-items: stretch; }
        .country-picker-btn {
            display: flex; align-items: center; gap: 6px; padding: 10px 12px;
            background: #f8f9fa; border: 1px solid #dee2e6; border-right: none;
            border-radius: 8px 0 0 8px; cursor: pointer; white-space: nowrap;
            font-size: 14px; min-width: 95px; transition: background 0.2s;
        }
        .country-picker-btn:hover { background: #e9ecef; }
        .country-picker-dropdown {
            position: absolute; top: 100%; left: 0; z-index: 9999;
            background: #fff; border: 1px solid #dee2e6; border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12); width: 280px;
            display: none; overflow: hidden;
        }
        .country-picker-dropdown.open { display: block; }
        .country-search-box {
            width: 100%; padding: 10px 14px; border: none; border-bottom: 1px solid #dee2e6;
            outline: none; font-size: 14px; border-radius: 10px 10px 0 0;
        }
        .country-list { max-height: 220px; overflow-y: auto; }
        .country-item {
            padding: 9px 14px; cursor: pointer; display: flex; align-items: center;
            gap: 10px; font-size: 13px; transition: background 0.15s;
        }
        .country-item:hover, .country-item.highlighted { background: #f0f4ff; }
        .country-item .dial { color: #888; font-size: 12px; margin-left: auto; }
        #phone_input { border-radius: 0 8px 8px 0; border-left: none; }
    </style>
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
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .brand-logo img {
            max-height: 70px;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.06));
        }

        .welcome-text {
            text-align: center;
        }

        .welcome-text h3 {
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }

        .welcome-text p {
            color: #64748b;
            margin-bottom: 2.5rem;
            font-size: 0.95rem;
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
        }

        .btn-primary:hover {
            background-color: #0a3560;
            border-color: #0a3560;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(15, 76, 129, 0.2);
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
            color: #0f4c81;
            border-color: #0f4c81;
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

            @php
                $maintenanceMode = \App\Models\Setting::get('auth_maintenance_mode') == '1';
                $secretKey = \App\Models\Setting::get('auth_maintenance_secret');
                $isBypassed = !empty($secretKey) && request()->query('secret') === $secretKey;
            @endphp
            @if($maintenanceMode && !$isBypassed)
                <div class="maintenance-banner text-center py-5">
                    <div class="icon-box mx-auto mb-4" style="width: 80px; height: 80px; background: linear-gradient(135deg, rgba(245,166,35,0.2) 0%, rgba(245,166,35,0) 100%); border: 1px solid rgba(245, 166, 35, 0.3); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #f5a623;">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3 style="font-weight: 800; font-size: 2rem; color: #333; margin-bottom: 1rem;">{{ __('Coming Soon') }}</h3>
                    <p style="color: #777; font-size: 1.1rem; line-height: 1.6; max-width: 400px; margin: 0 auto;">{{ __('We are preparing something amazing. Our new platform will be launched very soon.') }}</p>
                    <a href="{{ route('home') }}" class="btn btn-primary mt-4 mx-auto" style="max-width: 200px;">
                        {{ __('Back to Home') }}
                    </a>
                </div>
            @else
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

            <!-- Tabs Navigation -->
            <ul class="nav nav-pills mb-4 d-flex" id="loginTab" role="tablist">
                <li class="nav-item flex-fill text-center" role="presentation">
                    <button class="nav-link w-100 active" id="phone-tab" data-bs-toggle="pill" data-bs-target="#phone-login" type="button" role="tab" style="font-weight:600; border-radius:10px;">
                        @if($otpMethod === 'email')
                            <i class="fa fa-envelope"></i> {{ __('Email (OTP)') }}
                        @else
                            <i class="fa fa-phone"></i> {{ __('Phone (OTP)') }}
                        @endif
                    </button>
                </li>
                <li class="nav-item flex-fill text-center" role="presentation">
                    <button class="nav-link w-100" id="email-tab" data-bs-toggle="pill" data-bs-target="#email-login" type="button" role="tab" style="font-weight:600; border-radius:10px;">
                        <i class="fa fa-envelope"></i> {{ __('Email & Password') }}
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="loginTabContent">
                
                <!-- OTP Login Tab (Email or Phone based on settings) -->
                <div class="tab-pane fade show active" id="phone-login" role="tabpanel">
                    
                    <div id="otp-request-section">
                        @if($otpMethod === 'email')
                        <div class="mb-4">
                            <label class="mb-2 font-weight-bold text-muted">{{ __('Email Address') }}</label>
                            <input type="email" id="email_input" class="form-control" placeholder="name@example.com" required>
                            <span class="text-danger small mt-1 d-block" id="phone_error" style="display:none;"></span>
                        </div>
                        <button type="button" id="btn-request-otp" class="btn btn-primary w-100">
                            <i class="fas fa-envelope me-2"></i> {{ __('Send OTP via Email') }}
                        </button>
                        @else
                        <div class="mb-3">
                            <div class="d-flex align-items-center gap-2 mb-3 p-3" style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); border-radius: 10px; color: white;">
                                <i class="fab fa-whatsapp" style="font-size: 22px;"></i>
                                <span style="font-size: 13px; font-weight: 500;">{{ __('The OTP code will be sent via WhatsApp') }}</span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="mb-2 font-weight-bold text-muted">{{ __('Phone Number') }}</label>
                            <div class="country-picker-wrapper" dir="ltr">
                                <div class="country-picker-btn" id="countryPickerBtn">
                                    <span id="selectedFlag">🇸🇦</span>
                                    <span id="selectedDial">+966</span>
                                    <i class="fa fa-chevron-down" style="font-size:10px; color:#aaa;"></i>
                                </div>
                                <div class="country-picker-dropdown" id="countryDropdown">
                                    <input type="text" class="country-search-box" id="countrySearch" placeholder="{{ __('Search country...') }}" autocomplete="off">
                                    <div class="country-list" id="countryList"></div>
                                </div>
                                <input type="tel" id="phone_input" class="form-control" placeholder="500000000" required>
                                <input type="hidden" id="country_code" value="966">
                            </div>
                            <span class="text-danger small mt-1 d-block" id="phone_error" style="display:none;"></span>
                        </div>
                        <button type="button" id="btn-request-otp" class="btn btn-primary w-100">
                            <i class="fab fa-whatsapp me-2"></i> {{ __('Send OTP via WhatsApp') }}
                        </button>
                        @endif
                    </div>

                    <div id="otp-verify-section" style="display:none;">
                        <div class="alert alert-info small mb-3">
                            {{ $otpMethod === 'email' ? __('An OTP has been sent to your email.') : __('An OTP has been sent to your WhatsApp.') }}
                        </div>
                        <div class="mb-4">
                            <label class="mb-2 font-weight-bold text-muted">{{ __('OTP Code') }}</label>
                            <input type="text" id="otp_input" class="form-control" placeholder="••••" required maxlength="6" style="letter-spacing:10px; font-size:24px; text-align:center;">
                            <span class="text-danger small mt-1 d-block" id="otp_error" style="display:none;"></span>
                        </div>
                        <button type="button" id="btn-verify-otp" class="btn btn-primary">
                            {{ __('Verify & Login') }} <i class="fa fa-check ms-2"></i>
                        </button>
                        <div class="text-center mt-3">
                            <a href="#" id="btn-back-to-phone" class="text-muted small"><i class="fa fa-arrow-left"></i> {{ $otpMethod === 'email' ? __('Change Email Address') : __('Change Phone Number') }}</a>
                        </div>
                    </div>

                </div>

                <!-- Email Login Tab -->
                <div class="tab-pane fade" id="email-login" role="tabpanel">
                    <form method="POST" action="{{ route('login', request()->has('secret') ? ['secret' => request()->query('secret')] : []) }}">
                        @csrf
                        <div class="mb-4">
                            <label class="mb-2 font-weight-bold text-muted">{{ __('Email Address') }}</label>
                            <div class="position-relative">
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="name@example.com">
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
                    </form>
                </div>
            </div>
            @endif

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

            <div class="mt-5 text-center text-muted small">
                &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
            </div>
        </div>
    </div>
</div>

<!-- Vite JS -->
{{-- @vite(['resources/js/app.js']) --}}
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<script>
// Country data
const COUNTRIES = [
  {code:'SA',dial:'966',flag:'🇸🇦',name:'Saudi Arabia / السعودية'},
  {code:'AE',dial:'971',flag:'🇦🇪',name:'UAE / الإمارات'},
  {code:'KW',dial:'965',flag:'🇰🇼',name:'Kuwait / الكويت'},
  {code:'BH',dial:'973',flag:'🇧🇭',name:'Bahrain / البحرين'},
  {code:'OM',dial:'968',flag:'🇴🇲',name:'Oman / عمان'},
  {code:'QA',dial:'974',flag:'🇶🇦',name:'Qatar / قطر'},
  {code:'YE',dial:'967',flag:'🇾🇪',name:'Yemen / اليمن'},
  {code:'EG',dial:'20',flag:'🇪🇬',name:'Egypt / مصر'},
  {code:'JO',dial:'962',flag:'🇯🇴',name:'Jordan / الأردن'},
  {code:'LB',dial:'961',flag:'🇱🇧',name:'Lebanon / لبنان'},
  {code:'SY',dial:'963',flag:'🇸🇾',name:'Syria / سوريا'},
  {code:'IQ',dial:'964',flag:'🇮🇶',name:'Iraq / العراق'},
  {code:'MA',dial:'212',flag:'🇲🇦',name:'Morocco / المغرب'},
  {code:'TN',dial:'216',flag:'🇹🇳',name:'Tunisia / تونس'},
  {code:'DZ',dial:'213',flag:'🇩🇿',name:'Algeria / الجزائر'},
  {code:'LY',dial:'218',flag:'🇱🇾',name:'Libya / ليبيا'},
  {code:'SD',dial:'249',flag:'🇸🇩',name:'Sudan / السودان'},
  {code:'SO',dial:'252',flag:'🇸🇴',name:'Somalia / الصومال'},
  {code:'TR',dial:'90',flag:'🇹🇷',name:'Turkey / تركيا'},
  {code:'PK',dial:'92',flag:'🇵🇰',name:'Pakistan'},
  {code:'IN',dial:'91',flag:'🇮🇳',name:'India'},
  {code:'BD',dial:'880',flag:'🇧🇩',name:'Bangladesh'},
  {code:'PH',dial:'63',flag:'🇵🇭',name:'Philippines'},
  {code:'ID',dial:'62',flag:'🇮🇩',name:'Indonesia'},
  {code:'MY',dial:'60',flag:'🇲🇾',name:'Malaysia'},
  {code:'SG',dial:'65',flag:'🇸🇬',name:'Singapore'},
  {code:'NG',dial:'234',flag:'🇳🇬',name:'Nigeria'},
  {code:'KE',dial:'254',flag:'🇰🇪',name:'Kenya'},
  {code:'ET',dial:'251',flag:'🇪🇹',name:'Ethiopia'},
  {code:'GH',dial:'233',flag:'🇬🇭',name:'Ghana'},
  {code:'TZ',dial:'255',flag:'🇹🇿',name:'Tanzania'},
  {code:'ZA',dial:'27',flag:'🇿🇦',name:'South Africa'},
  {code:'US',dial:'1',flag:'🇺🇸',name:'United States'},
  {code:'GB',dial:'44',flag:'🇬🇧',name:'United Kingdom'},
  {code:'CA',dial:'1',flag:'🇨🇦',name:'Canada'},
  {code:'AU',dial:'61',flag:'🇦🇺',name:'Australia'},
  {code:'DE',dial:'49',flag:'🇩🇪',name:'Germany / ألمانيا'},
  {code:'FR',dial:'33',flag:'🇫🇷',name:'France / فرنسا'},
  {code:'IT',dial:'39',flag:'🇮🇹',name:'Italy / إيطاليا'},
  {code:'ES',dial:'34',flag:'🇪🇸',name:'Spain / إسبانيا'},
  {code:'RU',dial:'7',flag:'🇷🇺',name:'Russia / روسيا'},
  {code:'CN',dial:'86',flag:'🇨🇳',name:'China / الصين'},
  {code:'JP',dial:'81',flag:'🇯🇵',name:'Japan / اليابان'},
  {code:'KR',dial:'82',flag:'🇰🇷',name:'South Korea'},
  {code:'BR',dial:'55',flag:'🇧🇷',name:'Brazil'},
  {code:'MX',dial:'52',flag:'🇲🇽',name:'Mexico'},
  {code:'AR',dial:'54',flag:'🇦🇷',name:'Argentina'},
  {code:'NL',dial:'31',flag:'🇳🇱',name:'Netherlands'},
  {code:'SE',dial:'46',flag:'🇸🇪',name:'Sweden'},
  {code:'NO',dial:'47',flag:'🇳🇴',name:'Norway'},
  {code:'CH',dial:'41',flag:'🇨🇭',name:'Switzerland'},
  {code:'AT',dial:'43',flag:'🇦🇹',name:'Austria'},
  {code:'BE',dial:'32',flag:'🇧🇪',name:'Belgium'},
];

let selectedDial = '966';
let countryDropdownOpen = false;

function renderCountryList(filter) {
    const list = document.getElementById('countryList');
    list.innerHTML = '';
    const lower = (filter || '').toLowerCase();
    COUNTRIES.filter(c => !lower || c.name.toLowerCase().includes(lower) || c.dial.includes(lower))
    .forEach(c => {
        const item = document.createElement('div');
        item.className = 'country-item';
        item.innerHTML = `<span>${c.flag}</span><span>${c.name}</span><span class="dial">+${c.dial}</span>`;
        item.addEventListener('click', function() {
            selectedDial = c.dial;
            document.getElementById('selectedFlag').textContent = c.flag;
            document.getElementById('selectedDial').textContent = '+' + c.dial;
            document.getElementById('country_code').value = c.dial;
            closeDropdown();
        });
        list.appendChild(item);
    });
}

function closeDropdown() {
    document.getElementById('countryDropdown').classList.remove('open');
    countryDropdownOpen = false;
}

document.addEventListener('DOMContentLoaded', function() {
    renderCountryList('');

    document.getElementById('countryPickerBtn').addEventListener('click', function(e) {
        e.stopPropagation();
        const dd = document.getElementById('countryDropdown');
        countryDropdownOpen = !countryDropdownOpen;
        dd.classList.toggle('open', countryDropdownOpen);
        if (countryDropdownOpen) {
            document.getElementById('countrySearch').focus();
        }
    });

    document.getElementById('countrySearch').addEventListener('input', function() {
        renderCountryList(this.value);
    });

    document.addEventListener('click', function(e) {
        if (!document.getElementById('countryPickerBtn').contains(e.target) &&
            !document.getElementById('countryDropdown').contains(e.target)) {
            closeDropdown();
        }
    });
});

    $(document).ready(function() {
        let currentPhone = '';
        let currentCountryCode = '';
        let currentEmail = '';

        // Step 1: Request OTP
        $('#btn-request-otp').on('click', function() {
            let data = {};
            @if($otpMethod === 'email')
                let email = $('#email_input').val().trim();
                if(!email) {
                    $('#phone_error').text('{{ __("Please enter a valid email address") }}').show();
                    return;
                }
                data = { email: email, _token: '{{ csrf_token() }}' };
            @else
                let phone = $('#phone_input').val().trim();
                if (phone.startsWith('0')) { phone = phone.substring(1); }
                let countryCode = '+' + $('#country_code').val();
                if(!phone) {
                    $('#phone_error').text('{{ __("Please enter a valid phone number") }}').show();
                    return;
                }
                data = { phone: phone, country_code: countryCode, _token: '{{ csrf_token() }}' };
            @endif

            let btn = $(this);
            const originalBtnText = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ __("Sending...") }}');

            $.ajax({
                url: '{{ route("web.request.otp") }}',
                method: 'POST',
                data: data,
                success: function(res) {
                    @if($otpMethod === 'email')
                        currentEmail = email;
                    @else
                        currentPhone = phone;
                        currentCountryCode = countryCode;
                    @endif
                    $('#otp-request-section').slideUp();
                    $('#otp-verify-section').slideDown();
                    btn.html(originalBtnText).prop('disabled', false);
                },
                error: function(err) {
                    let msg = err.responseJSON?.message || "{{ __('Failed to send OTP') }}";
                    $('#phone_error').text(msg).show();
                    btn.html(originalBtnText).prop('disabled', false);
                }
            });
        });

        // Step 2: Verify OTP
        $('#btn-verify-otp').click(function() {
            let otp = $('#otp_input').val().trim();
            if(!otp) {
                $('#otp_error').text("{{ __('OTP is required') }}").show();
                return;
            }
            $('#otp_error').hide();

            const btn = $(this);
            const originalText = btn.html();
            btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

            let data = {};
            @if($otpMethod === 'email')
                data = { email: currentEmail, otp_code: otp, _token: '{{ csrf_token() }}' };
            @else
                data = { phone: currentPhone, country_code: currentCountryCode, otp_code: otp, _token: '{{ csrf_token() }}' };
            @endif

            $.ajax({
                url: '{{ route("web.verify.otp") }}',
                method: 'POST',
                data: data,
            success: function(res) {
                if(res.success) {
                    window.location.href = res.redirect || '/';
                }
            },
            error: function(err) {
                let msg = err.responseJSON?.message || "{{ __('Invalid OTP') }}";
                $('#otp_error').text(msg).show();
                btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Back to Phone
    $('#btn-back-to-phone').on('click', function(e) {
        e.preventDefault();
        $('#otp_input').val('');
        $('#otp_error').hide();
        $('#otp-verify-section').slideUp();
        $('#otp-request-section').slideDown();
    });
});
</script>

<!-- Vite JS -->
{{-- @vite(['resources/js/app.js']) --}}
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>
