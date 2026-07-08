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
            background-image: url('https://images.pexels.com/photos/2108845/pexels-photo-2108845.jpeg');
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

        [dir="rtl"] .register-form-container {
            box-shadow: 10px 0 30px rgba(0,0,0,0.05);
        }

        .register-content {
            width: 100%;
            max-width: 600px;
            padding: 1rem;
        }

        /* Country Picker Styles (Copied from Login) */
        .country-picker-wrapper {
            display: flex;
            align-items: center;
            border: 1px solid #eee;
            border-radius: 12px;
            background: #fcfcfc;
            height: 55px;
            position: relative;
        }
        .country-picker-wrapper.focused {
            border-color: #0f4c81;
            box-shadow: 0 0 0 4px rgba(15, 76, 129, 0.05);
            background: #fff;
        }
        .country-picker-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 0 15px;
            cursor: pointer;
            border-right: 1px solid #eee;
            height: 100%;
            user-select: none;
        }
        .country-picker-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            width: 300px;
            background: white;
            border: 1px solid #eee;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            z-index: 100;
            display: none;
            flex-direction: column;
            margin-top: 5px;
        }
        .country-picker-dropdown.open {
            display: flex;
        }
        .country-search-box {
            border: none;
            border-bottom: 1px solid #eee;
            padding: 10px 15px;
            outline: none;
            font-size: 0.9rem;
        }
        .country-list {
            max-height: 200px;
            overflow-y: auto;
        }
        .country-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            cursor: pointer;
        }
        .country-item:hover {
            background: #f8f9fa;
        }
        .country-item .dial {
            margin-left: auto;
            color: #777;
            font-size: 0.85rem;
        }
        [dir="rtl"] .country-item .dial {
            margin-left: 0;
            margin-right: auto;
        }
        .country-picker-wrapper input[type="tel"] {
            flex: 1;
            border: none;
            background: transparent;
            height: 100%;
            outline: none;
            padding: 0 15px;
            font-size: 0.95rem;
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
            height: 55px;
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

            <!-- Tabs Navigation -->
            <ul class="nav nav-pills mb-4 d-flex" id="registerTab" role="tablist">
                <li class="nav-item flex-fill text-center" role="presentation">
                    <button class="nav-link w-100 active" id="guest-tab" data-bs-toggle="pill" data-bs-target="#guest-register" type="button" role="tab" style="font-weight:600; border-radius:10px;">
                        <i class="fa fa-user-clock"></i> {{ __('Guest Registration') }}
                    </button>
                </li>
                <li class="nav-item flex-fill text-center" role="presentation">
                    <button class="nav-link w-100" id="full-tab" data-bs-toggle="pill" data-bs-target="#full-register" type="button" role="tab" style="font-weight:600; border-radius:10px;">
                        <i class="fa fa-user-check"></i> {{ __('Full Registration') }}
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="registerTabContent">
                
                <!-- Guest Registration Tab -->
                <div class="tab-pane fade show active" id="guest-register" role="tabpanel">
                    
                    <div id="guest-otp-request-section">
                        <div class="mb-3">
                            <div class="d-flex align-items-center gap-2 mb-3 p-3" style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); border-radius: 10px; color: white;">
                                <i class="fab fa-whatsapp" style="font-size: 22px;"></i>
                                <span style="font-size: 13px; font-weight: 500;">{{ __('A verification code will be sent to your WhatsApp.') }}</span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="mb-2 font-weight-bold text-muted">{{ __('Phone Number') }}</label>
                            <div class="country-picker-wrapper" dir="ltr" id="guest_phone_wrapper">
                                <div class="country-picker-btn" onclick="toggleDropdown('guestDropdown')">
                                    <span id="guestSelectedFlag">🇸🇦</span>
                                    <span id="guestSelectedDial">+966</span>
                                    <i class="fa fa-chevron-down" style="font-size:10px; color:#aaa;"></i>
                                </div>
                                <div class="country-picker-dropdown" id="guestDropdown">
                                    <input type="text" class="country-search-box" onkeyup="renderCountryList(this.value, 'guest')" placeholder="{{ __('Search country...') }}" autocomplete="off">
                                    <div class="country-list" id="guestCountryList"></div>
                                </div>
                                <input type="tel" id="guest_phone_input" placeholder="500000000" required>
                                <input type="hidden" id="guest_country_code" value="966">
                            </div>
                            <span class="text-danger small mt-1 d-block" id="guest_phone_error" style="display:none;"></span>
                        </div>
                        <button type="button" id="btn-guest-request-otp" class="btn btn-primary w-100">
                            <i class="fab fa-whatsapp me-2"></i> {{ __('Register via WhatsApp') }}
                        </button>
                    </div>

                    <div id="guest-otp-verify-section" style="display:none;">
                        <div class="alert alert-info small mb-3">
                            {{ __('An OTP has been sent to your WhatsApp.') }}
                        </div>
                        <div class="mb-4">
                            <label class="mb-2 font-weight-bold text-muted">{{ __('OTP Code') }}</label>
                            <input type="text" id="guest_otp_input" class="form-control" placeholder="••••" required maxlength="6" style="letter-spacing:10px; font-size:24px; text-align:center;">
                            <span class="text-danger small mt-1 d-block" id="guest_otp_error" style="display:none;"></span>
                        </div>
                        <button type="button" id="btn-guest-verify-otp" class="btn btn-primary">
                            {{ __('Verify & Register') }} <i class="fa fa-check ms-2"></i>
                        </button>
                        <div class="text-center mt-3">
                            <a href="#" id="btn-guest-back" class="text-muted small"><i class="fa fa-arrow-left"></i> {{ __('Change Phone Number') }}</a>
                        </div>
                    </div>

                </div>

                <!-- Full Registration Tab -->
                <div class="tab-pane fade" id="full-register" role="tabpanel">
                    
                    <div id="full-otp-request-section">
                        <div class="row-grid">
                            <div class="mb-3">
                                <label class="mb-2 font-weight-bold text-muted small" style="text-transform:uppercase; letter-spacing:1px">{{ __('First Name') }}</label>
                                <input type="text" id="full_first_name" class="form-control" value="{{ old('first_name') }}" required placeholder="{{ __('First Name') }}">
                            </div>
                            <div class="mb-3">
                                <label class="mb-2 font-weight-bold text-muted small" style="text-transform:uppercase; letter-spacing:1px">{{ __('Last Name') }}</label>
                                <input type="text" id="full_last_name" class="form-control" value="{{ old('last_name') }}" required placeholder="{{ __('Last Name') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="mb-2 font-weight-bold text-muted small" style="text-transform:uppercase; letter-spacing:1px">{{ __('Email Address') }}</label>
                            <input type="email" id="full_email" class="form-control" value="{{ old('email') }}" required placeholder="name@example.com">
                        </div>

                        <div class="mb-3">
                            <label class="mb-2 font-weight-bold text-muted small" style="text-transform:uppercase; letter-spacing:1px">{{ __('Phone Number') }}</label>
                            <div class="country-picker-wrapper" dir="ltr" id="full_phone_wrapper">
                                <div class="country-picker-btn" onclick="toggleDropdown('fullDropdown')">
                                    <span id="fullSelectedFlag">🇸🇦</span>
                                    <span id="fullSelectedDial">+966</span>
                                    <i class="fa fa-chevron-down" style="font-size:10px; color:#aaa;"></i>
                                </div>
                                <div class="country-picker-dropdown" id="fullDropdown">
                                    <input type="text" class="country-search-box" onkeyup="renderCountryList(this.value, 'full')" placeholder="{{ __('Search country...') }}" autocomplete="off">
                                    <div class="country-list" id="fullCountryList"></div>
                                </div>
                                <input type="tel" id="full_phone_input" placeholder="500000000" required>
                                <input type="hidden" id="full_country_code" value="966">
                            </div>
                        </div>

                        <div class="row-grid">
                            <div class="mb-3">
                                <label class="mb-2 font-weight-bold text-muted small" style="text-transform:uppercase; letter-spacing:1px">{{ __('Password') }}</label>
                                <input type="password" id="full_password" class="form-control" required placeholder="••••••••" minlength="8">
                            </div>
                            <div class="mb-3">
                                <label class="mb-2 font-weight-bold text-muted small" style="text-transform:uppercase; letter-spacing:1px">{{ __('Confirm Password') }}</label>
                                <input type="password" id="full_password_confirmation" class="form-control" required placeholder="••••••••" minlength="8">
                            </div>
                        </div>

                        <span class="text-danger small mt-1 mb-2 d-block" id="full_error_msg" style="display:none;"></span>

                        <button type="button" id="btn-full-request-otp" class="btn btn-primary">
                            {{ __('Register') }} <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'left' : 'right' }} ms-2"></i>
                        </button>
                    </div>

                    <div id="full-otp-verify-section" style="display:none;">
                        <div class="alert alert-info small mb-3">
                            {{ __('An OTP has been sent to your WhatsApp to verify your account.') }}
                        </div>
                        <div class="mb-4">
                            <label class="mb-2 font-weight-bold text-muted">{{ __('OTP Code') }}</label>
                            <input type="text" id="full_otp_input" class="form-control" placeholder="••••" required maxlength="6" style="letter-spacing:10px; font-size:24px; text-align:center;">
                            <span class="text-danger small mt-1 d-block" id="full_otp_error" style="display:none;"></span>
                        </div>
                        <button type="button" id="btn-full-verify-otp" class="btn btn-primary">
                            {{ __('Verify & Complete Registration') }} <i class="fa fa-check ms-2"></i>
                        </button>
                        <div class="text-center mt-3">
                            <a href="#" id="btn-full-back" class="text-muted small"><i class="fa fa-arrow-left"></i> {{ __('Edit Details') }}</a>
                        </div>
                    </div>

                </div>

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

            </div>

            <div class="mt-5 text-center text-muted small">
                &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
            </div>
        </div>
    </div>
</div>

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
  {code:'IQ',dial:'964',flag:'🇮🇶',name:'العراق'},
  {code:'MA',dial:'212',flag:'🇲🇦',name:'المغرب'},
  {code:'TN',dial:'216',flag:'🇹🇳',name:'تونس'},
  {code:'DZ',dial:'213',flag:'🇩🇿',name:'الجزائر'},
  {code:'LY',dial:'218',flag:'🇱🇾',name:'ليبيا'},
  {code:'SD',dial:'249',flag:'🇸🇩',name:'السودان'},
  {code:'TR',dial:'90',flag:'🇹🇷',name:'Turkey / تركيا'},
  {code:'US',dial:'1',flag:'🇺🇸',name:'United States'},
  {code:'GB',dial:'44',flag:'🇬🇧',name:'United Kingdom'}
];

function renderCountryList(filter, prefix) {
    const list = document.getElementById(prefix + 'CountryList');
    list.innerHTML = '';
    const lower = (filter || '').toLowerCase();
    COUNTRIES.filter(c => !lower || c.name.toLowerCase().includes(lower) || c.dial.includes(lower))
    .forEach(c => {
        const item = document.createElement('div');
        item.className = 'country-item';
        item.innerHTML = `<span>${c.flag}</span><span>${c.name}</span><span class="dial">+${c.dial}</span>`;
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById(prefix + 'SelectedFlag').textContent = c.flag;
            document.getElementById(prefix + 'SelectedDial').textContent = '+' + c.dial;
            document.getElementById(prefix + '_country_code').value = c.dial;
            closeAllDropdowns();
        });
        list.appendChild(item);
    });
}

function toggleDropdown(id) {
    event.stopPropagation();
    const dd = document.getElementById(id);
    const isOpen = dd.classList.contains('open');
    closeAllDropdowns();
    if(!isOpen) {
        dd.classList.add('open');
        dd.querySelector('input').focus();
    }
}

function closeAllDropdowns() {
    document.querySelectorAll('.country-picker-dropdown').forEach(dd => dd.classList.remove('open'));
}

document.addEventListener('DOMContentLoaded', function() {
    renderCountryList('', 'guest');
    renderCountryList('', 'full');

    document.addEventListener('click', function(e) {
        closeAllDropdowns();
    });

    // Input focus styling
    ['guest', 'full'].forEach(prefix => {
        const input = document.getElementById(prefix + '_phone_input');
        const wrapper = document.getElementById(prefix + '_phone_wrapper');
        input.addEventListener('focus', () => wrapper.classList.add('focused'));
        input.addEventListener('blur', () => wrapper.classList.remove('focused'));
    });
});

$(document).ready(function() {
    // ---- Guest Registration Flow ----
    let guestPhone = '';
    let guestCountryCode = '';

    $('#btn-guest-request-otp').on('click', function() {
        let phone = $('#guest_phone_input').val().trim();
        if (phone.startsWith('0')) { phone = phone.substring(1); }
        let countryCode = '+' + $('#guest_country_code').val();

        if(!phone) {
            $('#guest_phone_error').text('{{ __("Please enter a valid phone number") }}').show();
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ __("Sending...") }}');

        $.ajax({
            url: '{{ route("web.request.otp") }}', // Same endpoint as Login for Guest
            method: 'POST',
            data: { 
                phone: phone,
                country_code: countryCode,
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                guestPhone = phone;
                guestCountryCode = countryCode;
                $('#guest-otp-request-section').slideUp();
                $('#guest-otp-verify-section').slideDown();
                btn.html('<i class="fab fa-whatsapp me-2"></i> {{ __("Register via WhatsApp") }}').prop('disabled', false);
            },
            error: function(err) {
                let msg = err.responseJSON?.message || "{{ __('Failed to send OTP') }}";
                $('#guest_phone_error').text(msg).show();
                btn.html('<i class="fab fa-whatsapp me-2"></i> {{ __("Register via WhatsApp") }}').prop('disabled', false);
            }
        });
    });

    $('#btn-guest-verify-otp').click(function() {
        let otp = $('#guest_otp_input').val().trim();
        if(!otp) {
            $('#guest_otp_error').text("{{ __('OTP is required') }}").show();
            return;
        }
        $('#guest_otp_error').hide();

        const btn = $(this);
        btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: '{{ route("web.verify.otp") }}',
            method: 'POST',
            data: { 
                phone: guestPhone, 
                country_code: guestCountryCode, 
                otp_code: otp,
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                if(res.success) {
                    window.location.href = res.redirect;
                }
            },
            error: function(err) {
                let msg = err.responseJSON?.message || "{{ __('Invalid OTP') }}";
                $('#guest_otp_error').text(msg).show();
                btn.html('{{ __("Verify & Register") }} <i class="fa fa-check ms-2"></i>').prop('disabled', false);
            }
        });
    });

    $('#btn-guest-back').click(function(e) {
        e.preventDefault();
        $('#guest-otp-verify-section').slideUp();
        $('#guest-otp-request-section').slideDown();
    });

    // ---- Full Registration Flow ----
    let fullPhone = '';
    let fullCountryCode = '';

    $('#btn-full-request-otp').on('click', function() {
        let fname = $('#full_first_name').val().trim();
        let lname = $('#full_last_name').val().trim();
        let email = $('#full_email').val().trim();
        let pass = $('#full_password').val();
        let passConf = $('#full_password_confirmation').val();
        
        let phone = $('#full_phone_input').val().trim();
        if (phone.startsWith('0')) { phone = phone.substring(1); }
        let countryCode = '+' + $('#full_country_code').val();

        if(!fname || !lname || !email || !pass || !phone) {
            $('#full_error_msg').text('{{ __("Please fill all required fields") }}').show();
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ __("Sending...") }}');

        $.ajax({
            url: '{{ route("web.register.request.otp") }}',
            method: 'POST',
            data: { 
                first_name: fname,
                last_name: lname,
                email: email,
                password: pass,
                password_confirmation: passConf,
                phone: phone,
                country_code: countryCode,
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                fullPhone = phone;
                fullCountryCode = countryCode;
                $('#full_error_msg').hide();
                $('#full-otp-request-section').slideUp();
                $('#full-otp-verify-section').slideDown();
                btn.html('{{ __("Register") }} <i class="fas fa-arrow-{{ app()->isLocale("ar") ? "left" : "right" }} ms-2"></i>').prop('disabled', false);
            },
            error: function(err) {
                let msg = err.responseJSON?.message || "{{ __('Failed to send OTP') }}";
                $('#full_error_msg').text(msg).show();
                btn.html('{{ __("Register") }} <i class="fas fa-arrow-{{ app()->isLocale("ar") ? "left" : "right" }} ms-2"></i>').prop('disabled', false);
            }
        });
    });

    $('#btn-full-verify-otp').click(function() {
        let otp = $('#full_otp_input').val().trim();
        if(!otp) {
            $('#full_otp_error').text("{{ __('OTP is required') }}").show();
            return;
        }
        $('#full_otp_error').hide();

        const btn = $(this);
        btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: '{{ route("web.register.verify.otp") }}',
            method: 'POST',
            data: { 
                phone: fullPhone, 
                country_code: fullCountryCode, 
                otp_code: otp,
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                if(res.success) {
                    window.location.href = res.redirect;
                }
            },
            error: function(err) {
                let msg = err.responseJSON?.message || "{{ __('Invalid OTP') }}";
                $('#full_otp_error').text(msg).show();
                btn.html('{{ __("Verify & Complete Registration") }} <i class="fa fa-check ms-2"></i>').prop('disabled', false);
            }
        });
    });

    $('#btn-full-back').click(function(e) {
        e.preventDefault();
        $('#full-otp-verify-section').slideUp();
        $('#full-otp-request-section').slideDown();
    });
});
</script>

</body>
</html>
