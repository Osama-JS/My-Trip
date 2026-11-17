@extends('frontend.layouts.app')

@section('title', __('Complete Your Booking'))

@section('content')
<div class="fe-page-header fe-booking-header">
    <div class="fe-container">
        <h1>{{ __('Complete Your Booking') }}</h1>
        <p>{{ __('Please enter the passenger details exactly as they appear on their passports/IDs.') }}</p>
    </div>
</div>

<div class="fe-container" style="margin-top: -40px; margin-bottom: 80px;">
    {{-- ═══ SESSION ERROR ALERT ═══ --}}
    @if(session('error'))
        <div class="fe-alert fe-alert-danger" style="margin-bottom: 25px; background: #fff1f2; color: #9f1239; padding: 18px 24px; border-radius: 16px; border: 1.5px solid #fda4af; display: flex; align-items: center; gap: 15px; font-weight: 700; box-shadow: 0 10px 25px -5px rgba(225, 29, 72, 0.1);">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #ffe4e6; color: #e11d48; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div style="flex: 1;">
                <div style="font-size: 15px; font-weight: 800; margin-bottom: 3px;">{{ app()->getLocale() == 'ar' ? 'تنبيه بشأن الحجز' : 'Booking Alert' }}</div>
                <div style="font-size: 13.5px; font-weight: 500; opacity: 0.95; line-height: 1.5;">{{ session('error') }}</div>
            </div>
            <a href="{{ route('flights') }}" class="btn btn-sm btn-danger text-white px-3 py-2" style="border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; white-space: nowrap;">
                <i class="fas fa-search me-1"></i> {{ app()->getLocale() == 'ar' ? 'إعادة البحث' : 'Search Again' }}
            </a>
        </div>
    @endif

    {{-- ═══ PAGE TIME LIMIT / SESSION COUNTDOWN BANNER ═══ --}}
    <div class="fe-session-timer-card" id="flightPageTimeLimit" data-remaining-seconds="900" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff; padding: 16px 24px; border-radius: 18px; margin-bottom: 25px; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2); border: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(59, 130, 246, 0.15); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <div style="font-size: 14px; font-weight: 800; color: #f8fafc; margin-bottom: 2px;">
                    {{ app()->getLocale() == 'ar' ? 'مهلة الجلسة لضمان السعر والمقاعد' : 'Page Time Limit: Price & Seat Guarantee' }}
                </div>
                <div style="font-size: 12.5px; color: #94a3b8;">
                    {{ app()->getLocale() == 'ar' ? 'الأسعار وتوافر المقاعد مضمونة طوال فترة العداد التنازلي' : 'Fares and seat availability are held until the timer expires' }}
                </div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 12px; color: #cbd5e1; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">{{ app()->getLocale() == 'ar' ? 'الوقت المتبقي' : 'Time Remaining' }}:</span>
            <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171; font-family: monospace; font-size: 1.3rem; font-weight: 900; padding: 6px 16px; border-radius: 10px; letter-spacing: 1px;" id="flightPageTimerDisplay">
                15:00
            </div>
        </div>
    </div>

    {{-- ═══ LUXURY GUEST AUTHENTICATION BANNER ═══ --}}
    @guest
        @php $currLocale = app()->getLocale(); @endphp
        <div class="fe-guest-auth-banner" style="margin-bottom: 25px;">
            <div class="fe-guest-auth-card">
                <div class="fe-guest-auth-glow"></div>
                <div class="fe-guest-auth-inner">
                    <div class="fe-guest-auth-icon-wrap">
                        <div class="fe-guest-auth-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="fe-guest-pulse"></div>
                    </div>

                    <div class="fe-guest-auth-content">
                        <div class="fe-guest-auth-badge">
                            <i class="fas fa-lock"></i>
                            <span>{{ $currLocale == 'ar' ? 'خطوة التحقق والأمان' : 'Security & Verification Step' }}</span>
                        </div>
                        <h4 class="fe-guest-auth-title">
                            {{ $currLocale == 'ar' ? 'تسجيل الدخول مطلوب لتأكيد وحفظ بيانات الحجز' : 'Sign in required to complete and secure your booking' }}
                        </h4>
                        <p class="fe-guest-auth-desc">
                            {{ $currLocale == 'ar' 
                                ? 'يمكنك مراجعة وتعبئة بيانات المسافرين الآن بكل حرية وسهولة، وسيتعين عليك تسجيل الدخول أو إنشاء حساب سريع عند الخطوة الأخيرة لحفظ التذاكر ومتابعة الدفع.' 
                                : 'You can freely fill and review traveler details now. You will need to sign in or create a quick account at the final step to save your vouchers and complete payment.' }}
                        </p>
                        
                        <div class="fe-guest-auth-perks">
                            <div class="fe-perk-item">
                                <i class="fas fa-bolt text-warning"></i>
                                <span>{{ $currLocale == 'ar' ? 'حفظ تلقائي لبيانات التذكرة' : 'Auto-save booking details' }}</span>
                            </div>
                            <div class="fe-perk-item">
                                <i class="fas fa-shield-alt text-success"></i>
                                <span>{{ $currLocale == 'ar' ? 'تأكيد ودفع مشفر 100%' : '100% Encrypted Checkout' }}</span>
                            </div>
                            <div class="fe-perk-item">
                                <i class="fas fa-receipt text-primary"></i>
                                <span>{{ $currLocale == 'ar' ? 'إصدار فوري للتذاكر' : 'Instant Voucher Issuance' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="fe-guest-auth-cta-box">
                        <a href="{{ route('login', ['return_url' => url()->full()]) }}" class="fe-btn-guest-login btn-save-draft-and-login" onclick="if(window.flightDraftManager) window.flightDraftManager.saveDraft();">
                            <span class="fe-btn-text">{{ $currLocale == 'ar' ? 'تسجيل الدخول / إنشاء حساب' : 'Sign In / Create Account' }}</span>
                            <i class="fas fa-arrow-left fe-arrow-icon"></i>
                        </a>
                        <span class="fe-guest-cta-hint">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            {{ $currLocale == 'ar' ? 'لن تفقد أي بيانات قمت بإدخالها' : 'Your input data will be retained' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endguest

    <form action="{{ route('flights.book.process') }}" method="POST" id="flightBookingForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="flight_session_id" value="{{ $details['session_id'] ?? '' }}">
        <input type="hidden" name="fare_source_code" value="{{ $details['fare_source_code'] ?? '' }}">
        <input type="hidden" name="fare_source_code_inbound" value="{{ $details['fare_source_code_inbound'] ?? '' }}">
        <input type="hidden" name="IsPassportMandatory" value="{{ $details['IsPassportMandatory'] ?? 'false' }}">
        <input type="hidden" name="fareType" value="{{ $details['fareType'] ?? ($details['fare_type'] ?? 'Private') }}">
        <input type="hidden" name="total_amount" value="{{ $details['total_amount'] ?? 0 }}">
        <input type="hidden" name="from" value="{{ $details['from'] ?? '' }}">
        <input type="hidden" name="to" value="{{ $details['to'] ?? '' }}">
        <input type="hidden" name="departDate" value="{{ $details['departDate'] ?? '' }}">
        <input type="hidden" name="returnDate" value="{{ $details['returnDate'] ?? '' }}">
        <input type="hidden" name="journeyType" value="{{ $details['journeyType'] ?? '' }}">
        <input type="hidden" name="flight_class" value="{{ $details['class'] ?? 'Economy' }}">
        <input type="hidden" name="airline" value="{{ $details['airline'] ?? '' }}">
        <input type="hidden" name="dep_time" value="{{ $details['dep_time'] ?? '' }}">
        <input type="hidden" name="arr_time" value="{{ $details['arr_time'] ?? '' }}">
        <input type="hidden" name="stops" value="{{ $details['stops'] ?? 0 }}">
        <input type="hidden" name="duration" value="{{ $details['duration'] ?? '' }}">
        <input type="hidden" name="refundable" value="{{ is_array($details['refundable'] ?? '') ? implode('/', $details['refundable']) : ($details['refundable'] ?? '') }}">
        <input type="hidden" name="refund_allowed" value="{{ is_array($details['refund_allowed'] ?? '') ? implode('/', $details['refund_allowed']) : ($details['refund_allowed'] ?? '') }}">
        <input type="hidden" name="refund_penalty" value="{{ is_array($details['refund_penalty'] ?? '') ? implode('/', $details['refund_penalty']) : ($details['refund_penalty'] ?? '') }}">
        <input type="hidden" name="change_allowed" value="{{ is_array($details['change_allowed'] ?? '') ? implode('/', $details['change_allowed']) : ($details['change_allowed'] ?? '') }}">
        <input type="hidden" name="change_penalty" value="{{ is_array($details['change_penalty'] ?? '') ? implode('/', $details['change_penalty']) : ($details['change_penalty'] ?? '') }}">
        <input type="hidden" name="fare_basis" value="{{ is_array($details['fare_basis'] ?? '') ? implode('/', $details['fare_basis']) : ($details['fare_basis'] ?? '') }}">
        @if(!empty($details['segments']) && is_array($details['segments']))
            @foreach($details['segments'] as $i => $seg)
                <input type="hidden" name="segments[{{ $i }}][leg_index]" value="{{ $seg['leg_index'] ?? 0 }}">
                <input type="hidden" name="segments[{{ $i }}][is_return]" value="{{ !empty($seg['is_return']) ? '1' : '0' }}">
                <input type="hidden" name="segments[{{ $i }}][leg_type]" value="{{ $seg['leg_type'] ?? '' }}">
                <input type="hidden" name="segments[{{ $i }}][from]" value="{{ $seg['from'] ?? '' }}">
                <input type="hidden" name="segments[{{ $i }}][to]" value="{{ $seg['to'] ?? '' }}">
                <input type="hidden" name="segments[{{ $i }}][dep]" value="{{ $seg['dep'] ?? '' }}">
                <input type="hidden" name="segments[{{ $i }}][arr]" value="{{ $seg['arr'] ?? '' }}">
                <input type="hidden" name="segments[{{ $i }}][dep_datetime]" value="{{ $seg['dep_datetime'] ?? '' }}">
                <input type="hidden" name="segments[{{ $i }}][arr_datetime]" value="{{ $seg['arr_datetime'] ?? '' }}">
                <input type="hidden" name="segments[{{ $i }}][flight_no]" value="{{ $seg['flight_no'] ?? '' }}">
                <input type="hidden" name="segments[{{ $i }}][baggage]" value="{{ $seg['baggage'] ?? '' }}">
                <input type="hidden" name="segments[{{ $i }}][layover]" value="{{ $seg['layover'] ?? '' }}">
            @endforeach
        @endif
        
        @php
            $rawAdults = $details['adults'] ?? ($details['adult'] ?? 1);
            $adultCount = is_array($rawAdults) ? (count($rawAdults) > 1 ? count($rawAdults) : (int)reset($rawAdults)) : (int)$rawAdults;
            if ($adultCount < 1) $adultCount = 1;

            $rawChilds = $details['childs'] ?? ($details['children'] ?? ($details['child'] ?? 0));
            $childCount = is_array($rawChilds) ? (count($rawChilds) > 1 ? count($rawChilds) : (int)reset($rawChilds)) : (int)$rawChilds;

            $rawInfants = $details['infants'] ?? ($details['infant'] ?? 0);
            $infantCount = is_array($rawInfants) ? (count($rawInfants) > 1 ? count($rawInfants) : (int)reset($rawInfants)) : (int)$rawInfants;

            $totalPax = $adultCount + $childCount + $infantCount;
            
            $isRoundTrip = !empty($details['returnDate']) || (($details['journeyType'] ?? '') === 'Return') || (!empty($details['segments']) && is_array($details['segments']) && collect($details['segments'])->contains('is_return', true));
            
            $outboundSegments = [];
            $returnSegments = [];
            if (!empty($details['segments']) && is_array($details['segments'])) {
                foreach ($details['segments'] as $s) {
                    if (!empty($s['is_return']) || ($s['leg_type'] ?? '') === 'return' || ($s['leg_index'] ?? 0) > 0) {
                        $returnSegments[] = $s;
                    } else {
                        $outboundSegments[] = $s;
                    }
                }
            }
        @endphp
        
        <input type="hidden" name="adults" value="{{ $adultCount }}">
        <input type="hidden" name="childs" value="{{ $childCount }}">
        <input type="hidden" name="infants" value="{{ $infantCount }}">

        <div class="fe-booking-grid">
            {{-- Main Form Content --}}
            <div class="fe-booking-main">

                {{-- Selected Flight Itinerary Overview Banner --}}
                <div class="fe-booking-card" style="background: linear-gradient(to right, #ffffff, #f8fafc); border-left: 4px solid var(--primary);">
                    <div class="fe-card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--gray-100); padding-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-plane text-primary"></i>
                            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800;">{{ __('Selected Flight Itinerary') }}</h3>
                        </div>
                        @if($isRoundTrip)
                            <span class="badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 20px;">
                                <i class="fas fa-sync-alt me-1"></i> {{ __('Round Trip') }}
                            </span>
                        @else
                            <span class="badge" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 20px;">
                                <i class="fas fa-plane me-1"></i> {{ __('One Way') }}
                            </span>
                        @endif
                    </div>
                    <div class="fe-card-body" style="padding-top: 15px;">
                        {{-- Outbound Leg Details --}}
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; margin-bottom: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-weight: 800; color: #1d4ed8; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-plane-departure"></i> {{ __('Outbound Flight') }}: <span class="notranslate" translate="no">{{ $details['from'] ?? '' }}</span> <i class="fas fa-long-arrow-alt-{{ app()->isLocale('ar') ? 'left' : 'right' }} mx-1"></i> <span class="notranslate" translate="no">{{ $details['to'] ?? '' }}</span>
                                </span>
                                <span style="font-size: 0.8rem; font-weight: 700; color: #475569; background: #f8fafc; padding: 2px 8px; border-radius: 6px;">
                                    <i class="far fa-calendar-alt me-1"></i> {{ $details['departDate'] ?? '' }}
                                </span>
                            </div>
                            @if(!empty($outboundSegments))
                                @foreach($outboundSegments as $seg)
                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem; color: #334155; padding: 4px 0;">
                                        <span><strong><span class="notranslate" translate="no">{{ $seg['from'] }}</span> <i class="fas fa-long-arrow-alt-{{ app()->isLocale('ar') ? 'left' : 'right' }} mx-1"></i> <span class="notranslate" translate="no">{{ $seg['to'] }}</span></strong> @if(!empty($seg['flight_no']))<small class="text-muted notranslate" translate="no">({{ $seg['flight_no'] }})</small>@endif</span>
                                        <span class="fw-bold">{{ $seg['dep'] ?? '' }} - {{ $seg['arr'] ?? '' }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: #334155;">
                                    <span><strong><span class="notranslate" translate="no">{{ $details['from'] ?? '' }}</span> <i class="fas fa-long-arrow-alt-{{ app()->isLocale('ar') ? 'left' : 'right' }} mx-1"></i> <span class="notranslate" translate="no">{{ $details['to'] ?? '' }}</span></strong> ({{ $details['airline'] ?? '' }})</span>
                                    <span class="fw-bold">{{ $details['dep_time'] ?? '' }} - {{ $details['arr_time'] ?? '' }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Return Leg Details if Round Trip --}}
                        @if($isRoundTrip)
                            <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 12px 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                    <span style="font-weight: 800; color: #0369a1; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-plane-arrival"></i> {{ __('Return Flight') }}: <span class="notranslate" translate="no">{{ $details['to'] ?? '' }}</span> <i class="fas fa-long-arrow-alt-{{ app()->isLocale('ar') ? 'left' : 'right' }} mx-1"></i> <span class="notranslate" translate="no">{{ $details['from'] ?? '' }}</span>
                                    </span>
                                    <span style="font-size: 0.8rem; font-weight: 700; color: #0284c7; background: #e0f2fe; padding: 2px 8px; border-radius: 6px;">
                                        <i class="far fa-calendar-check me-1"></i> {{ $details['returnDate'] ?? '' }}
                                    </span>
                                </div>
                                @if(!empty($returnSegments))
                                    @foreach($returnSegments as $seg)
                                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem; color: #0f172a; padding: 4px 0;">
                                            <span><strong><span class="notranslate" translate="no">{{ $seg['from'] }}</span> <i class="fas fa-long-arrow-alt-{{ app()->isLocale('ar') ? 'left' : 'right' }} mx-1"></i> <span class="notranslate" translate="no">{{ $seg['to'] }}</span></strong> @if(!empty($seg['flight_no']))<small class="text-muted notranslate" translate="no">({{ $seg['flight_no'] }})</small>@endif</span>
                                            <span class="fw-bold">{{ $seg['dep'] ?? '' }} - {{ $seg['arr'] ?? '' }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: #0f172a;">
                                        <span><strong><span class="notranslate" translate="no">{{ $details['to'] ?? '' }}</span> <i class="fas fa-long-arrow-alt-{{ app()->isLocale('ar') ? 'left' : 'right' }} mx-1"></i> <span class="notranslate" translate="no">{{ $details['from'] ?? '' }}</span></strong></span>
                                        <span class="fw-bold">{{ __('Return Included') }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Contact Information --}}
                <div class="fe-booking-card">
                    <div class="fe-card-header">
                        <i class="fas fa-envelope"></i>
                        <h3>{{ __('Contact Details') }}</h3>
                    </div>
                    <div class="fe-card-body">
                        <div class="fe-form-row two-cols">
                            <div class="fe-form-group">
                                <label class="fe-label">{{ __('Email Address') }}</label>
                                <input type="email" name="customerEmail" class="fe-input" required value="{{ auth()->user()->email ?? '' }}" placeholder="email@example.com">
                            </div>
                            <div class="fe-form-group">
                                <label class="fe-label">{{ __('Phone Number') }}</label>
                                <input type="tel" name="customerPhone" class="fe-input" required value="{{ auth()->user()->phone ?? '' }}" placeholder="+966xxxxxxxxx">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Passengers List --}}
                <div id="pax-container">
                    @php $paxIndex = 0; @endphp

                    {{-- Adults --}}
                    @for($i = 0; $i < $adultCount; $i++)
                        @include('frontend.flights.partials.pax-fields', ['type' => 'adult', 'index' => $paxIndex++, 'num' => $i + 1, 'countries' => $countries, 'isPassportMandatory' => $details['IsPassportMandatory'] ?? 'false'])
                    @endfor

                    {{-- Children --}}
                    @for($i = 0; $i < $childCount; $i++)
                        @include('frontend.flights.partials.pax-fields', ['type' => 'child', 'index' => $paxIndex++, 'num' => $i + 1, 'countries' => $countries, 'isPassportMandatory' => $details['IsPassportMandatory'] ?? 'false'])
                    @endfor

                    {{-- Infants --}}
                    @for($i = 0; $i < $infantCount; $i++)
                        @include('frontend.flights.partials.pax-fields', ['type' => 'infant', 'index' => $paxIndex++, 'num' => $i + 1, 'countries' => $countries, 'isPassportMandatory' => $details['IsPassportMandatory'] ?? 'false'])
                    @endfor
                </div>

                {{-- ═══ EXTRA SERVICES JS LOADER ═══ --}}
                <div id="esGlobalState" style="margin-bottom: 25px; text-align:center; padding:20px; background:white; border-radius:15px; border:1px solid var(--gray-200);">
                    <div id="esLoader" style="color:var(--gray-400);">
                        <i class="fas fa-spinner fa-spin fa-2x" style="margin-bottom:12px;"></i>
                        <p style="font-weight:600; margin:0;">{{ __('Loading extra services for passengers...') }}</p>
                    </div>
                    <div id="esEmptyState" style="display:none; color:var(--gray-400);">
                        <i class="fas fa-info-circle fa-2x" style="margin-bottom:12px;"></i>
                        <p style="font-weight:600; margin:0;">{{ __('No extra services available for this flight.') }}</p>
                    </div>
                    <div id="esTotal" class="es-total-badge" style="display:none; margin-top:10px; background:var(--primary-50); color:var(--primary); padding:10px 20px; border-radius:12px; display:inline-block; font-weight:bold;">
                        <i class="fas fa-tag"></i>
                        {{ __('Extra Services Total') }}: <strong id="esTotalValue">0 SAR</strong>
                    </div>
                </div>

                @if(\App\Models\Setting::get('insurance_enabled', '1') == '1')
                {{-- ═══ TRAVEL INSURANCE CROSS-SELL COMPONENT (FLIGHTS) ═══ --}}
                <div class="fe-insurance-component" id="insuranceSection">
                    <div class="fe-insurance-card">
                        {{-- Header --}}
                        <div class="fe-ins-header">
                            <div class="fe-ins-header-main">
                                <div class="fe-ins-shield-icon" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                                    <i class="fas fa-plane-departure"></i>
                                </div>
                                <div class="fe-ins-titles">
                                    <div class="fe-ins-title-row">
                                        <h4 class="fe-ins-title">{{ __('Comprehensive Flight & Travel Protection') }}</h4>
                                        <span class="fe-ins-badge">✈️ {{ __('Flight & Travel Approved') }}</span>
                                    </div>
                                    <p class="fe-ins-subtitle">{{ __('Global coverage for medical emergencies, flight delays, cancellations, and lost luggage.') }}</p>
                                </div>
                            </div>
                            <div class="fe-ins-price-pill">
                                <span class="fe-ins-price-label">{{ __('Starting from') }}</span>
                                <span class="fe-ins-price-val" id="insurancePriceDisplay">
                                    <span class="spinner-border spinner-border-sm text-primary" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span>
                                    <span class="fs-12 text-muted ms-1">{{ __('Calculating...') }}</span>
                                </span>
                                <span class="fe-ins-price-unit">/ {{ __('person') }}</span>
                            </div>
                        </div>

                        {{-- Benefits Grid --}}
                        <div class="fe-ins-benefits-grid">
                            <div class="fe-ins-benefit-item">
                                <div class="fe-ins-benefit-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="fe-ins-benefit-text">{{ __('Emergency medical & hospitalization up to $100,000') }}</div>
                            </div>
                            <div class="fe-ins-benefit-item">
                                <div class="fe-ins-benefit-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="fe-ins-benefit-text">{{ __('Flight delay & cancellation compensation') }}</div>
                            </div>
                            <div class="fe-ins-benefit-item">
                                <div class="fe-ins-benefit-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="fe-ins-benefit-text">{{ __('Lost or delayed luggage compensation') }}</div>
                            </div>
                            <div class="fe-ins-benefit-item">
                                <div class="fe-ins-benefit-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="fe-ins-benefit-text">{{ __('Official certificate for Schengen & Embassy visa applications') }}</div>
                            </div>
                        </div>

                        {{-- Selectable Choices --}}
                        <div class="fe-ins-options">
                            <label class="fe-ins-option-card" id="optAddInsuranceWrapper">
                                <div class="fe-ins-opt-left">
                                    <input type="radio" name="include_insurance" value="1" id="optAddInsurance" class="fe-ins-radio">
                                    <div class="fe-ins-opt-info">
                                        <span class="fe-ins-opt-title">{{ __('Yes, add comprehensive flight travel insurance for all passengers') }}</span>
                                        <span class="fe-ins-opt-desc">{{ __('Highly recommended for flight cancellations & lost baggage') }}</span>
                                    </div>
                                </div>
                                <div class="fe-ins-opt-price" id="insuranceTotalOptionPrice">
                                    <span class="spinner-border spinner-border-sm text-primary me-1" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span>
                                    <span class="fs-12 text-muted">{{ __('Calculating...') }}</span>
                                </div>
                            </label>

                            <label class="fe-ins-option-card selected" id="optNoInsuranceWrapper">
                                <div class="fe-ins-opt-left">
                                    <input type="radio" name="include_insurance" value="0" id="optNoInsurance" checked class="fe-ins-radio">
                                    <div class="fe-ins-opt-info">
                                        <span class="fe-ins-opt-title" style="color: #64748b; font-weight: 700;">{{ __('No, I decline flight protection (I assume all cancellation & delay risks)') }}</span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <input type="hidden" name="insurance_quote_id" id="hiddenInsuranceQuoteId" value="">
                        <input type="hidden" name="insurance_amount" id="hiddenInsuranceAmount" value="0">
                    </div>
                </div>
                @endif

                <div class="fe-booking-action">
                    @auth
                        @if(!auth()->user()->canBookDirectly())
                            <div class="alert alert-warning border-0 rounded-4 p-3 text-start mb-3 shadow-sm" style="font-size: 0.88rem; background: #fffbeb; color: #92400e;">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                {{ app()->getLocale() == 'ar' ? 'حسابك الحالي (وكيل / مسؤول) مخصص لإدارة العمليات ولا يمكنه إنشاء حجوزات استهلاكية مباشرة. يرجى استخدام حساب عميل.' : 'Your account is an Agent/Admin management account and cannot place consumer bookings.' }}
                            </div>
                        @else
                            <button type="submit" class="fe-btn fe-btn-primary fe-btn-lg fe-btn-block" id="flightSubmitBtn">
                                <i class="fas fa-check-circle"></i> {{ __('Complete Reservation') }}
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login', ['return_url' => url()->full()]) }}" class="fe-btn fe-btn-primary fe-btn-lg fe-btn-block btn-save-draft-and-login" onclick="if(window.flightDraftManager) window.flightDraftManager.saveDraft();" style="background: linear-gradient(135deg, #f59e0b, #d97706); text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: none;">
                            <i class="fas fa-sign-in-alt"></i> {{ __('Sign in to complete booking') }}
                        </a>
                    @endauth
                    <p class="fe-terms-fine">
                        {{ __('By clicking "Complete Reservation", you agree to our') }} 
                        <a href="#">{{ __('Terms and Conditions') }}</a> {{ __('and') }} 
                        <a href="#">{{ __('Privacy Policy') }}</a>.
                    </p>
                </div>
            </div>

            {{-- Sidebar Summary --}}
            <aside class="fe-booking-sidebar">
                <div class="fe-summary-card">
                    <div class="fe-summary-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="margin: 0;">{{ __('Flight Summary') }}</h3>
                        @if($isRoundTrip)
                            <span class="badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 12px;">
                                <i class="fas fa-sync-alt me-1"></i> {{ __('Round Trip') }}
                            </span>
                        @endif
                    </div>
                    <div class="fe-summary-body">
                        <div class="fe-summary-flight">
                            <div class="summary-route-visual">
                                <div class="city">
                                    <span class="code">{{ $details['from'] ?? '---' }}</span>
                                </div>
                                <div class="path">
                                    <span class="line"></span>
                                    @if($isRoundTrip)
                                        <i class="fas fa-exchange-alt" style="color: var(--primary);"></i>
                                    @else
                                        <i class="fas fa-plane"></i>
                                    @endif
                                    <span class="line"></span>
                                </div>
                                <div class="city">
                                    <span class="code">{{ $details['to'] ?? '---' }}</span>
                                </div>
                            </div>
                            
                            <div class="fe-summary-details" style="border:none; padding:15px 0 0;">
                                @if(!empty($details['airline']))
                                <div class="fe-summary-item">
                                    <span class="label"><i class="fas fa-plane-departure"></i> {{ __('Airline') }}</span>
                                    <span class="value">{{ $details['airline'] }}</span>
                                </div>
                                @endif
                                <div class="fe-summary-item">
                                    <span class="label"><i class="far fa-calendar-alt text-primary"></i> {{ __('Departure') }}</span>
                                    <span class="value">{{ $details['departDate'] ?? '' }}</span>
                                </div>
                                @if($isRoundTrip && !empty($details['returnDate']))
                                <div class="fe-summary-item">
                                    <span class="label"><i class="far fa-calendar-check text-info"></i> {{ __('Return Date') }}</span>
                                    <span class="value" style="font-weight: 700; color: #0284c7;">{{ $details['returnDate'] }}</span>
                                </div>
                                @endif
                                @if(!empty($details['dep_time']))
                                <div class="fe-summary-item">
                                    <span class="label"><i class="far fa-clock"></i> {{ __('Time') }}</span>
                                    <span class="value">{{ $details['dep_time'] }} - {{ $details['arr_time'] ?? '' }}</span>
                                </div>
                                @endif
                                @if(isset($details['stops']))
                                <div class="fe-summary-item">
                                    <span class="label"><i class="fas fa-route"></i> {{ __('Stops') }}</span>
                                    <span class="value">{{ $details['stops'] == 0 ? __('Non-stop') : $details['stops'] . ' ' . ($details['stops'] == 1 ? __('Stop') : __('Stops')) }}</span>
                                </div>
                                
                                {{-- Comprehensive Itinerary Segments Breakdown --}}
                                @if(!empty($details['segments']) && is_array($details['segments']))
                                    <div class="fe-summary-segments" style="margin: 12px 0 15px; background: var(--gray-50); border-radius: 10px; padding: 12px; font-size: 0.85rem; border: 1px solid var(--gray-200);">
                                        <div style="font-weight: 800; color: var(--dark); margin-bottom: 8px; font-size: 0.8rem; text-transform: uppercase; display: flex; align-items: center; justify-content: space-between;">
                                            <span>{{ __('Flight Route Details') }}</span>
                                            @if($isRoundTrip)
                                                <small style="color: var(--primary); font-weight: 700;">({{ __('Both Legs') }})</small>
                                            @endif
                                        </div>

                                        {{-- Outbound Segments in Sidebar --}}
                                        @if(!empty($outboundSegments))
                                            <div style="font-size: 0.75rem; font-weight: 800; color: #1d4ed8; margin: 6px 0 4px; display: flex; align-items: center; gap: 5px;">
                                                <i class="fas fa-plane-departure"></i> {{ __('Outbound') }}:
                                            </div>
                                            @foreach($outboundSegments as $index => $seg)
                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; padding-inline-start: 6px;">
                                                    <span style="font-weight: 700; color: var(--dark);">
                                                        {{ $seg['from'] ?? '' }} 
                                                        <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'left' : 'right' }}" style="font-size:0.7rem; margin:0 4px; color: var(--gray-400);"></i> 
                                                        {{ $seg['to'] ?? '' }}
                                                        @if(!empty($seg['flight_no']))
                                                            <span style="font-size:0.7rem; font-weight:600; color:var(--gray-500); margin-inline-start:2px;">({{ $seg['flight_no'] }})</span>
                                                        @endif
                                                    </span>
                                                    <span style="color: var(--dark-600); font-weight: 600;">{{ $seg['dep'] ?? '' }} - {{ $seg['arr'] ?? '' }}</span>
                                                </div>
                                            @endforeach
                                        @endif

                                        {{-- Return Segments in Sidebar --}}
                                        @if(!empty($returnSegments))
                                            <div style="font-size: 0.75rem; font-weight: 800; color: #0284c7; margin: 10px 0 4px; padding-top: 6px; border-top: 1px dashed var(--gray-200); display: flex; align-items: center; gap: 5px;">
                                                <i class="fas fa-plane-arrival"></i> {{ __('Return') }}:
                                            </div>
                                            @foreach($returnSegments as $index => $seg)
                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; padding-inline-start: 6px;">
                                                    <span style="font-weight: 700; color: var(--dark);">
                                                        {{ $seg['from'] ?? '' }} 
                                                        <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'left' : 'right' }}" style="font-size:0.7rem; margin:0 4px; color: var(--gray-400);"></i> 
                                                        {{ $seg['to'] ?? '' }}
                                                        @if(!empty($seg['flight_no']))
                                                            <span style="font-size:0.7rem; font-weight:600; color:var(--gray-500); margin-inline-start:2px;">({{ $seg['flight_no'] }})</span>
                                                        @endif
                                                    </span>
                                                    <span style="color: var(--dark-600); font-weight: 600;">{{ $seg['dep'] ?? '' }} - {{ $seg['arr'] ?? '' }}</span>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @endif
                                @endif
                                @if(!empty($details['duration']))
                                <div class="fe-summary-item">
                                    <span class="label"><i class="fas fa-hourglass-half"></i> {{ __('Duration') }}</span>
                                    <span class="value">{{ $details['duration'] }}</span>
                                </div>
                                @endif
                                <div class="fe-summary-item">
                                    <span class="label"><i class="fas fa-suitcase-rolling"></i> {{ __('Baggage') }}</span>
                                    <span class="value" style="font-weight: 700; color: #0f766e;">
                                        <i class="fas fa-check-circle me-1" style="color: #0d9488;"></i>
                                        {{ $details['baggage'] ?? ($details['segments'][0]['baggage'] ?? __('Per Airline Policy')) }}
                                    </span>
                                </div>
                                <div class="fe-summary-item">
                                    <span class="label"><i class="fas fa-users"></i> {{ __('Travelers') }}</span>
                                    <span class="value">{{ $totalPax }} {{ __('Person(s)') }}</span>
                                </div>
                                <div class="fe-summary-item">
                                    <span class="label"><i class="fas fa-couch"></i> {{ __('Class') }}</span>
                                    <span class="value">{{ $details['class'] ?? 'Economy' }}</span>
                                </div>
                                <div class="fe-summary-item" id="sidebarBaseFareRow" style="border-top: 1px dashed var(--gray-200); padding-top: 10px; margin-top: 10px;">
                                    <span class="label"><i class="fas fa-ticket-alt"></i> {{ __('Base Fare') }}</span>
                                    <span class="value" id="sidebarBaseFareAmount" style="font-weight: 800;">{{ number_format(floatval($details['total_amount'] ?? 0), 2) }} SAR</span>
                                </div>
                                <div class="fe-summary-item" id="sidebarExtraServicesRow" style="display:none; background:#f8fafc; border-radius:8px; padding:8px 12px; margin-top:10px; border:1px solid #e2e8f0;">
                                    <span class="label" style="color:#0f766e; font-weight:700;"><i class="fas fa-plus-circle text-teal me-1"></i> {{ __('Extra Services') }}</span>
                                    <span class="value" style="color:#0f766e; font-weight:800;" id="sidebarExtraServicesAmount">+ 0.00 SAR</span>
                                </div>
                                <div class="fe-summary-item" id="sidebarInsuranceRow" style="display:none; background:#f0fdf4; border-radius:8px; padding:8px 12px; margin-top:10px; border:1px solid #bbf7d0;">
                                    <span class="label" style="color:#15803d; font-weight:700;"><i class="fas fa-shield-alt text-success me-1"></i> {{ __('Travel Insurance') }}</span>
                                    <span class="value" style="color:#15803d; font-weight:800;" id="sidebarInsuranceAmount">+ 0.00 SAR</span>
                                </div>
                                <div style="margin-top: 12px; padding-top: 10px; border-top: 1px dashed var(--gray-200); text-align: center;">
                                    <button type="button" class="btn-fare-rules-trigger"
                                            data-session="{{ $details['session_id'] ?? '' }}"
                                            data-fare-source="{{ $details['fare_source_code'] ?? '' }}"
                                            data-inbound="{{ $details['fare_source_code_inbound'] ?? '' }}"
                                            data-airline="{{ $details['airline'] ?? '' }}"
                                            data-from="{{ $details['from'] ?? '' }}"
                                            data-to="{{ $details['to'] ?? '' }}"
                                            data-baggage="{{ $details['baggage'] ?? '' }}"
                                            data-cabin-baggage=""
                                            data-refundable="{{ is_array($details['refundable'] ?? '') ? implode('/', $details['refundable']) : ($details['refundable'] ?? '') }}"
                                            data-change-allowed="{{ is_array($details['change_allowed'] ?? '') ? implode('/', $details['change_allowed']) : ($details['change_allowed'] ?? '') }}"
                                            data-change-penalty="{{ is_array($details['change_penalty'] ?? '') ? implode('/', $details['change_penalty']) : ($details['change_penalty'] ?? '') }}"
                                            data-refund-allowed="{{ is_array($details['refund_allowed'] ?? '') ? implode('/', $details['refund_allowed']) : ($details['refund_allowed'] ?? '') }}"
                                            data-refund-penalty="{{ is_array($details['refund_penalty'] ?? '') ? implode('/', $details['refund_penalty']) : ($details['refund_penalty'] ?? '') }}"
                                            data-fare-basis="{{ is_array($details['fare_basis'] ?? '') ? implode('/', $details['fare_basis']) : ($details['fare_basis'] ?? '') }}"
                                            style="background: #f0f9ff; border: 1px solid #bae6fd; color: #0284c7; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; width: 100%; justify-content: center; transition: all 0.2s;">
                                        <i class="fas fa-file-contract text-primary"></i> {{ __('Fare Rules & Cancellation Policy') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="fe-summary-total" style="margin-top: 20px;">
                            <div class="total-label">{{ __('Total Amount') }}</div>
                            <div class="total-value">
                                <span class="currency">SAR</span>
                                <span class="amount" id="grandTotalAmount" data-base="{{ floatval($details['total_amount'] ?? 0) }}">{{ number_format(floatval($details['total_amount'] ?? 0), 2) }}</span>
                            </div>
                            <p class="total-note">{{ __('Includes all taxes and surcharges') }}</p>
                        </div>
                    </div>
                </div>

                <div class="fe-policy-card" style="background: #f0f7ff; border-color: #bee3f8;">
                    <h4 style="color: #2b6cb0;"><i class="fas fa-shield-alt"></i> {{ __('Trusted Booking') }}</h4>
                    <p style="color: #2c5282; font-size: 0.8rem;">{{ __('Your payment information is processed securely. We use industry-standard encryption to protect your data.') }}</p>
                </div>
            </aside>
        </div>
    </form>
</div>

<!-- Visual Seat Map Modal -->
<div class="fe-modal-overlay d-none" id="seatMapModal" style="z-index: 10050;">
    <div class="fe-modal-dialog" style="max-width: 1080px; width: 95%; height: 90vh;">
        <div class="fe-modal-content" style="height: 100%; display: flex; flex-direction: column; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 60px rgba(15,23,42,0.4); border: 1px solid #cbd5e1;">
            <div class="fe-modal-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 16px 24px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; gap: 15px;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(56, 189, 248, 0.15); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">
                            <i class="fas fa-plane"></i>
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <h5 style="margin: 0; font-weight: 800; font-size: 1.15rem; color: #ffffff;">{{ __('Interactive Aircraft Seat Map') }}</h5>
                                @php
                                    $fromAirport = $details['from'] ?? ($details['segments'][0]['from'] ?? '');
                                    $toAirport = $details['to'] ?? (isset($details['segments']) && is_array($details['segments']) ? ($details['segments'][count($details['segments']) - 1]['to'] ?? '') : '');
                                @endphp
                                @if(!empty($fromAirport) && !empty($toAirport))
                                <span class="badge" style="background: rgba(56, 189, 248, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); font-size: 0.78rem; padding: 4px 10px; border-radius: 6px;">
                                    {{ $fromAirport }} <i class="fas fa-long-arrow-alt-right mx-1"></i> {{ $toAirport }}
                                </span>
                                @endif
                            </div>
                            <span style="font-size: 0.78rem; color: #94a3b8;">{{ __('Select your preferred seat on the aircraft fuselage (Window, Aisle, Middle, or Extra Legroom)') }}</span>
                        </div>
                    </div>
                    <button type="button" class="fe-btn-close" onclick="closeVisualSeatMap()" style="color: white; opacity: 0.8; background: rgba(255,255,255,0.1); width: 34px; height: 34px; border-radius: 8px; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer;"><i class="fas fa-times"></i></button>
                </div>
            </div>
            
            <div class="fe-modal-body bg-white" style="flex: 1; overflow-y: auto; padding: 0;">
                <div class="seat-map-layout">
                    <!-- Left: Passengers List & Comprehensive Legend -->
                    <div class="seat-pax-sidebar">
                        <div class="pax-sidebar-header">
                            <span class="pax-badge-title"><i class="fas fa-users text-primary me-1"></i> {{ __('Passengers') }}</span>
                            <span class="pax-instruction-hint">{{ __('Click a passenger, then choose a seat') }}</span>
                        </div>
                        <div id="seatPaxList" class="pax-cards-container"></div>
                        
                        <!-- Rich Legend -->
                        <div class="seat-legend-box mt-3">
                            <div class="legend-header font-weight-bold mb-2">{{ __('Seat Legend & Features') }}</div>
                            <div class="legend-grid">
                                <div class="legend-item">
                                    <div class="seat-box available mock"><span class="mock-txt">Free</span></div>
                                    <div class="legend-desc">
                                        <strong>{{ __('Available') }}</strong>
                                        <small>{{ __('Free / Included') }}</small>
                                    </div>
                                </div>
                                <div class="legend-item">
                                    <div class="seat-box available mock"><div class="seat-price-pill mock">+25</div></div>
                                    <div class="legend-desc">
                                        <strong>{{ __('Paid Seat') }}</strong>
                                        <small>{{ __('Standard Fee') }}</small>
                                    </div>
                                </div>
                                <div class="legend-item">
                                    <div class="seat-box selected mock"><i class="fas fa-check" style="font-size: 0.65rem;"></i></div>
                                    <div class="legend-desc">
                                        <strong>{{ __('Your Selection') }}</strong>
                                        <small>{{ __('Active Passenger') }}</small>
                                    </div>
                                </div>
                                <div class="legend-item">
                                    <div class="seat-box companion-selected mock"><span style="font-size: 0.6rem; font-weight:800;">P2</span></div>
                                    <div class="legend-desc">
                                        <strong>{{ __('Companion') }}</strong>
                                        <small>{{ __('Traveling Group') }}</small>
                                    </div>
                                </div>
                                <div class="legend-item">
                                    <div class="seat-box occupied mock"><i class="fas fa-times" style="font-size: 0.65rem; color:#94a3b8;"></i></div>
                                    <div class="legend-desc">
                                        <strong class="text-danger">{{ __('Occupied') }}</strong>
                                        <small>{{ __('Cannot Select') }}</small>
                                    </div>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-icon-badge window"><i class="fas fa-window-frame"></i></div>
                                    <div class="legend-desc">
                                        <strong>{{ __('Window Seat') }}</strong>
                                        <small>{{ __('Scenic View (A, F)') }}</small>
                                    </div>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-icon-badge aisle"><i class="fas fa-walking"></i></div>
                                    <div class="legend-desc">
                                        <strong>{{ __('Aisle Seat') }}</strong>
                                        <small>{{ __('Easy Walkway (C, D)') }}</small>
                                    </div>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-icon-badge exit"><i class="fas fa-door-open"></i></div>
                                    <div class="legend-desc">
                                        <strong>{{ __('Exit Row') }}</strong>
                                        <small>{{ __('Extra Legroom') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Footer Summary & Action -->
                        <div class="seat-sidebar-footer mt-auto pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted font-weight-bold" style="font-size: 0.9rem;">{{ __('Total Extra Seats') }}:</span>
                                <span class="h5 m-0 font-weight-bold text-primary"><span id="seatMapTotal">0.00</span> SAR</span>
                            </div>
                            <button type="button" class="fe-btn fe-btn-primary w-100 shadow-sm" onclick="confirmSeatSelection()" style="padding: 12px; font-weight: 800; border-radius: 10px; display:flex; align-items:center; justify-content:center; gap:8px;">
                                <i class="fas fa-check-circle"></i> {{ __('Confirm Seats') }}
                            </button>
                        </div>
                    </div>
                    
                    <!-- Right: Technical Blueprint / Architectural Cabin Layout -->
                    <div class="aircraft-container">
                        <div class="blueprint-canvas">
                            <!-- Technical Drawing Title & Orientation Bar -->
                            <div class="blueprint-meta-bar">
                                <div class="meta-item">
                                    <i class="fas fa-drafting-compass text-primary me-1"></i>
                                    <strong>{{ __('Aircraft Technical Seating Plan') }}</strong> • <span class="text-muted">{{ __('Cabin Layout Plan') }}</span>
                                </div>
                                <div class="meta-item orientation-tag">
                                    <i class="fas fa-long-arrow-alt-up text-primary"></i> {{ __('Fore (Front)') }}
                                </div>
                            </div>

                            <div class="aircraft-schematic-wrap">
                                <!-- Technical Wing Outlines (Left & Right Schematic) -->
                                <div class="schematic-wing left-wing">
                                    <div class="wing-blueprint-line"></div>
                                    <span class="wing-annotation"><i class="fas fa-caret-left"></i> {{ __('Overwing Area') }}</span>
                                </div>
                                <div class="schematic-wing right-wing">
                                    <div class="wing-blueprint-line"></div>
                                    <span class="wing-annotation">{{ __('Overwing Area') }} <i class="fas fa-caret-right"></i></span>
                                </div>

                                <!-- Fuselage Technical Frame -->
                                <div class="schematic-fuselage">
                                    <!-- Cockpit Technical Outline -->
                                    <div class="schematic-cockpit">
                                        <div class="cockpit-nose-curve"></div>
                                        <div class="cockpit-windshield-outline">
                                            <div class="glass-frame"></div>
                                            <div class="glass-frame"></div>
                                            <div class="glass-frame"></div>
                                            <div class="glass-frame"></div>
                                        </div>
                                        <div class="cockpit-bulkhead-line">
                                            <span class="bulkhead-label"><i class="fas fa-plane-up"></i> {{ __('Cockpit') }}</span>
                                        </div>
                                    </div>

                                    <!-- Forward Service Zone (Galley / Lavatory / Entry Doors) -->
                                    <div class="schematic-service-zone front-zone">
                                        <div class="door-marker left"><i class="fas fa-door-open"></i> 1L</div>
                                        <div class="facility-icon" title="{{ __('Lavatory') }}"><i class="fas fa-restroom"></i> <span>WC</span></div>
                                        <div class="zone-divider-label">{{ __('Front Cabin') }}</div>
                                        <div class="facility-icon" title="{{ __('Galley') }}"><i class="fas fa-coffee"></i> <span>{{ __('Galley') }}</span></div>
                                        <div class="door-marker right">1R <i class="fas fa-door-open"></i></div>
                                    </div>

                                    <!-- Dynamic Seats Body -->
                                    <div class="aircraft-body" id="aircraftSeatsContainer">
                                        <!-- Dynamic Rows with Windows, Seats & Aisle -->
                                    </div>

                                    <!-- Aft Service Zone -->
                                    <div class="schematic-service-zone aft-zone">
                                        <div class="door-marker left"><i class="fas fa-door-open"></i> 2L</div>
                                        <div class="facility-icon" title="{{ __('Lavatory') }}"><i class="fas fa-restroom"></i> <span>WC</span></div>
                                        <div class="zone-divider-label">{{ __('Rear Cabin') }}</div>
                                        <div class="facility-icon" title="{{ __('Lavatory') }}"><i class="fas fa-restroom"></i> <span>WC</span></div>
                                        <div class="door-marker right">2R <i class="fas fa-door-open"></i></div>
                                    </div>

                                    <!-- Tail Schematic Outline -->
                                    <div class="schematic-tail">
                                        <div class="tail-datum-line"></div>
                                        <span class="tail-annotation">{{ __('Aft (Rear)') }} <i class="fas fa-long-arrow-alt-down text-primary"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Global Fare Rules Modal -->
<div class="fe-modal-overlay d-none" id="globalFareRulesModal" style="z-index: 10050;">
    <div class="fe-modal-dialog" style="max-width: 800px; width: 92%;">
        <div class="fe-modal-content">
            <div class="fe-modal-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 18px 24px; border-radius: 20px 20px 0 0;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: #38bdf8;">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <h4 style="margin: 0; font-weight: 800; font-size: 1.15rem; color: white;">{{ __('Fare Rules & Ticket Policies') }}</h4>
                            <span id="fareRulesAirlineBadge" class="badge" style="background: rgba(56, 189, 248, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); font-size: 0.75rem; padding: 3px 8px; border-radius: 6px;"></span>
                        </div>
                        <span id="fareRulesAirlineSubtitle" style="font-size: 0.76rem; color: #94a3b8;">{{ __('Official airline cancellation, refund, and change terms') }}</span>
                    </div>
                </div>
                <button type="button" class="fe-btn-close" onclick="closeFareRulesModal()" style="color: white; opacity: 0.8;"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="fe-modal-body bg-white" style="padding: 24px; max-height: 70vh; overflow-y: auto;">
                <div id="fareRulesModalLoader" style="padding: 40px 20px; text-align: center;">
                    <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem; border-width: 3px;"></div>
                    <p class="mt-3 text-muted fw-bold" style="font-size: 0.9rem;">{{ __('Loading official airline fare rules...') }}</p>
                </div>
                
                <div id="fareRulesModalBody" style="display: none;"></div>

                <div id="fareRulesModalError" style="display: none; padding: 35px 20px; text-align: center;">
                    <i class="fas fa-exclamation-triangle mb-3" style="font-size: 2.5rem; color: #d97706;"></i>
                    <h5 class="fw-bold text-dark" style="margin-bottom: 8px;">{{ __('Fare Rules Unavailable') }}</h5>
                    <p class="text-muted" id="fareRulesErrorDesc" style="font-size: 0.88rem; max-width: 480px; margin: 0 auto;">{{ __('Unable to retrieve fare rules from the airline provider for this flight.') }}</p>
                </div>
            </div>

            <div class="fe-modal-footer" style="padding: 14px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; border-radius: 0 0 20px 20px;">
                <span style="font-size: 0.75rem; color: #64748b;"><i class="fas fa-shield-alt text-success me-1"></i> {{ __('Verified Airline Tariff Rules') }}</span>
                <button type="button" class="fe-btn fe-btn-secondary" onclick="closeFareRulesModal()" style="padding: 8px 22px; font-weight: 700;">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Master Passport Upload & Crop Modal (Custom) -->
<div class="fe-modal-overlay d-none" id="passportUploadModal">
  <div class="fe-modal-dialog">
    <div class="fe-modal-content">
      <div class="fe-modal-header">
        <h5 style="margin: 0; font-weight: 800; color: #0f172a;"><i class="fas fa-id-card text-primary me-2"></i> {{ __('Upload & Edit Passport') }}</h5>
        <button type="button" class="fe-btn-close" onclick="closePassportModal()"><i class="fas fa-times"></i></button>
      </div>
      <div class="fe-modal-body bg-white">
        
        <!-- Drag & Drop Area (Visible Initially) -->
        <div id="modalDropArea" class="p-5 text-center" style="border: 2px dashed #cbd5e1; margin: 20px; border-radius: 15px; background: white; cursor: pointer; transition: all 0.3s;">
            <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #94a3b8; margin-bottom: 15px;"></i>
            <h4 style="font-weight: 700; color: #334155;">{{ __('Drag & Drop here or Click to Browse') }}</h4>
            <p style="color: #64748b; font-size: 0.9rem;">{{ __('Ensure the data page is clear and readable.') }}</p>
            <input type="file" id="modalFileInput" class="d-none" accept="image/*">
        </div>

        <!-- Cropper Area (Hidden Initially) -->
        <div id="modalCropperArea" class="d-none" style="padding: 20px;">
            <div style="max-height: 50vh; display:flex; justify-content:center; background: #000; border-radius: 10px; overflow: hidden;">
                <img id="modalImageToCrop" src="" alt="Picture" style="max-width: 100%;">
            </div>
            <p class="text-muted text-center mt-3 mb-0" style="font-size: 0.85rem;"><i class="fas fa-crop-alt"></i> {{ __('Adjust the frame to focus tightly on the passport data page for optimal AI scanning.') }}</p>
        </div>

      </div>
      <div class="fe-modal-footer">
        <button type="button" class="fe-btn fe-btn-light fw-bold" id="btnChangeImage" style="display:none; padding: 10px 20px;"><i class="fas fa-undo"></i> {{ __('Change Image') }}</button>
        <button type="button" class="fe-btn fe-btn-outline fw-bold ms-auto" onclick="closePassportModal()" style="padding: 10px 20px;">{{ __('Cancel') }}</button>
        <button type="button" class="fe-btn fe-btn-primary fw-bold" id="btnConfirmUpload" disabled style="padding: 10px 20px;">{{ __('Confirm & Scan') }} <i class="fas fa-magic ms-1"></i></button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css" rel="stylesheet">
<style>
    /* ─── Travel Insurance Cross-Sell Component ─────────────────────────── */
    .fe-insurance-component {
        margin-bottom: 28px;
        width: 100%;
    }
    .fe-insurance-card {
        background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
        border: 2px solid #bae6fd;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 8px 24px rgba(2, 132, 199, 0.06);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .fe-insurance-card:hover {
        border-color: #7dd3fc;
        box-shadow: 0 12px 30px rgba(2, 132, 199, 0.1);
    }
    .fe-ins-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        padding-bottom: 18px;
        border-bottom: 1px solid #e0f2fe;
    }
    .fe-ins-header-main {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1 1 300px;
    }
    .fe-ins-shield-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
    }
    .fe-ins-titles {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .fe-ins-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .fe-ins-title {
        margin: 0;
        font-size: 1.12rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
    }
    .fe-ins-badge {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .fe-ins-subtitle {
        margin: 0;
        font-size: 0.84rem;
        color: #64748b;
        font-weight: 500;
    }
    .fe-ins-price-pill {
        background: #ffffff;
        border: 1.5px solid #e0f2fe;
        border-radius: 14px;
        padding: 8px 16px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.05);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .fe-ins-price-label {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
    }
    .fe-ins-price-val {
        font-size: 1.25rem;
        font-weight: 900;
        color: #0284c7;
        line-height: 1.2;
    }
    .fe-ins-price-unit {
        font-size: 0.72rem;
        color: #94a3b8;
    }

    /* Benefits */
    .fe-ins-benefits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 10px;
        margin: 18px 0;
    }
    .fe-ins-benefit-item {
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid #e0f2fe;
        border-radius: 10px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .fe-ins-benefit-icon {
        color: #10b981;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .fe-ins-benefit-text {
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
        line-height: 1.3;
    }

    /* Options */
    .fe-ins-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 14px;
    }
    .fe-ins-option-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin: 0;
    }
    .fe-ins-option-card:hover {
        border-color: #93c5fd;
        background: #f8fafc;
    }
    .fe-ins-option-card.selected {
        border-color: #0284c7;
        background: #ffffff;
        box-shadow: 0 4px 16px rgba(2, 132, 199, 0.12);
    }
    .fe-ins-opt-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .fe-ins-radio {
        width: 20px;
        height: 20px;
        min-width: 20px;
        accent-color: #0284c7;
        cursor: pointer;
        margin: 0;
    }
    .fe-ins-opt-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .fe-ins-opt-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
    }
    .fe-ins-opt-desc {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 500;
    }
    .fe-ins-opt-price {
        font-size: 1.05rem;
        font-weight: 900;
        color: #0284c7;
        white-space: nowrap;
        margin-inline-start: auto;
    }
    @media (max-width: 768px) {
        .fe-ins-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .fe-ins-price-pill {
            width: 100%;
            flex-direction: row;
            gap: 8px;
            padding: 6px 12px;
        }
        .fe-ins-option-card {
            flex-direction: column;
            align-items: flex-start;
        }
        .fe-ins-opt-price {
            margin-inline-start: 34px;
        }
    }

    /* ─── Premium Guest Authentication Banner ─── */
    .fe-guest-auth-card {
        position: relative;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0f2444 100%);
        border-radius: 20px;
        padding: 24px 28px;
        box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1);
        overflow: hidden;
        color: #ffffff;
        transition: all 0.3s ease;
    }

    .fe-guest-auth-card:hover {
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35), 0 0 0 1px rgba(251, 191, 36, 0.3);
    }

    .fe-guest-auth-glow {
        position: absolute;
        top: -50%;
        right: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.18) 0%, rgba(14, 165, 233, 0.1) 50%, transparent 70%);
        pointer-events: none;
        filter: blur(40px);
    }

    .fe-guest-auth-inner {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
    }

    .fe-guest-auth-icon-wrap {
        position: relative;
        flex-shrink: 0;
    }

    .fe-guest-auth-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.35));
        border: 1.5px solid rgba(251, 191, 36, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.7rem;
        color: #fbbf24;
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.25);
    }

    .fe-guest-pulse {
        position: absolute;
        top: -3px;
        right: -3px;
        width: 14px;
        height: 14px;
        background: #10b981;
        border-radius: 50%;
        border: 2px solid #0f172a;
        animation: pulseBadge 2s infinite;
    }

    @keyframes pulseBadge {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .fe-guest-auth-content {
        flex: 1;
        min-width: 280px;
    }

    .fe-guest-auth-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(251, 191, 36, 0.12);
        border: 1px solid rgba(251, 191, 36, 0.25);
        padding: 3px 10px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 800;
        color: #fbbf24;
        margin-bottom: 8px;
        letter-spacing: 0.3px;
    }

    .fe-guest-auth-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 6px;
        line-height: 1.4;
    }

    .fe-guest-auth-desc {
        font-size: 0.86rem;
        color: #cbd5e1;
        margin: 0 0 12px;
        line-height: 1.6;
    }

    .fe-guest-auth-perks {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .fe-perk-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #e2e8f0;
        background: rgba(255, 255, 255, 0.07);
        padding: 4px 12px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .fe-guest-auth-cta-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .fe-btn-guest-login {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff !important;
        font-weight: 800;
        font-size: 0.95rem;
        padding: 13px 26px;
        border-radius: 50px;
        text-decoration: none;
        box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.4);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .fe-btn-guest-login:hover {
        background: linear-gradient(135deg, #fbbf24 0%, #b45309 100%);
        transform: translateY(-2px);
        box-shadow: 0 14px 30px -5px rgba(245, 158, 11, 0.5);
        color: #ffffff !important;
    }

    .fe-arrow-icon {
        transition: transform 0.3s ease;
    }

    html[dir="rtl"] .fe-btn-guest-login:hover .fe-arrow-icon {
        transform: translateX(-4px);
    }

    html[dir="ltr"] .fe-btn-guest-login .fe-arrow-icon {
        transform: rotate(180deg);
    }

    html[dir="ltr"] .fe-btn-guest-login:hover .fe-arrow-icon {
        transform: rotate(180deg) translateX(-4px);
    }

    @media (max-width: 991px) {
        .fe-guest-auth-inner {
            flex-direction: column;
            align-items: flex-start;
        }
        .fe-guest-auth-cta-box {
            width: 100%;
            align-items: stretch;
            margin-top: 10px;
        }
        .fe-btn-guest-login {
            width: 100%;
        }
    }

    .fe-booking-header { background: linear-gradient(135deg, var(--primary) 0%, #1a3a5a 100%); padding: 80px 0 100px; color: white; text-align: center; }
    .fe-booking-header h1 { color: white; margin-bottom: 10px; font-weight: 900; font-size: 2.5rem; }
    .fe-booking-header p { opacity: 0.9; font-size: 1.1rem; color: white; }

    /* Custom Modal Styling */
    .fe-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
    .fe-modal-overlay.show { opacity: 1; visibility: visible; }
    .fe-modal-overlay.d-none { display: none !important; }
    .fe-modal-dialog { background: white; border-radius: 20px; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto; transform: translateY(-20px); transition: all 0.3s ease; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
    .fe-modal-overlay.show .fe-modal-dialog { transform: translateY(0); }
    .fe-modal-header { padding: 20px 25px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
    .fe-btn-close { background: transparent; border: none; font-size: 1.5rem; color: #64748b; cursor: pointer; transition: color 0.2s; }
    .fe-btn-close:hover { color: #0f172a; }
    .fe-modal-body { padding: 0; background: #f8fafc; }
    .fe-modal-footer { padding: 15px 25px; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; align-items: center; background: #f8fafc; }
    .ms-auto { margin-inline-start: auto; }
    .me-2 { margin-inline-end: 8px; }
    .p-0 { padding: 0 !important; }
    .p-5 { padding: 3rem !important; }
    .text-center { text-align: center; }
    .d-none { display: none !important; }
    .bg-light { background-color: #f8fafc !important; }

    .fe-booking-grid { display: grid; grid-template-columns: 1fr 380px; gap: 30px; }
    @media (max-width: 1024px) {
        .fe-booking-grid { grid-template-columns: 1fr; }
        .fe-booking-sidebar { order: -1; }
    }

    .fe-booking-sidebar {
        position: -webkit-sticky;
        position: sticky;
        top: 100px;
        align-self: start;
        z-index: 10;
    }

    .fe-booking-card { background: white; border-radius: 20px; box-shadow: 0 4px 25px rgba(0,0,0,0.05); border: 1px solid var(--gray-100); margin-bottom: 25px; overflow: hidden; }
    .fe-card-header { background: var(--gray-50); padding: 18px 24px; border-bottom: 1px solid var(--gray-100); display: flex; align-items: center; gap: 12px; }
    .fe-card-header i { color: var(--primary); font-size: 1.1rem; }
    .fe-card-header h3 { font-size: 1.1rem; font-weight: 800; margin: 0; color: var(--dark); }
    .fe-card-body { padding: 25px; }

    .fe-form-row { display: grid; gap: 20px; margin-bottom: 15px; }
    .fe-form-row.two-cols { grid-template-columns: 1fr 1fr; }
    .fe-form-row.three-cols { grid-template-columns: 1fr 2fr 2fr; }
    @media (max-width: 768px) {
        .fe-form-row.two-cols, .fe-form-row.three-cols { grid-template-columns: 1fr; }
    }

    .fe-label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--dark-600); margin-bottom: 8px; }
    .fe-input { width: 100%; height: 48px; background: var(--gray-50); border: 1.5px solid var(--gray-100); border-radius: 12px; padding: 0 16px; font-weight: 600; font-size: 0.95rem; transition: all 0.2s; }
    .fe-input:focus { border-color: var(--primary); outline: none; background: white; box-shadow: 0 0 0 4px var(--primary-50); }

    .fe-guest-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px dashed var(--gray-200); }
    .fe-guest-badge { background: var(--primary); color: white; padding: 2px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
    .fe-guest-name { font-weight: 800; color: var(--dark); font-size: 0.95rem; }

    /* Passport Dropzone Styling */
    .passport-dropzone { border: 2px dashed var(--primary-300, #93c5fd); border-radius: 16px; background: #f8fafc; padding: 30px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease; position: relative; overflow: hidden; }
    .passport-dropzone:hover { background: #f0f9ff; border-color: var(--primary); }
    .passport-dropzone h5 { font-weight: 800; font-size: 1.1rem; color: #1e293b; margin-top: 10px; }
    .passport-dropzone.has-file { border: 2px solid #10b981; background: #f0fdf4; }
    .passport-dropzone.has-file h5, .passport-dropzone.has-file p.text-muted { display: none; }
    .passport-dropzone.has-file i.fa-cloud-upload-alt { display: none; }
    .passport-dropzone .success-indicator { display: flex !important; flex-direction: column; align-items: center; justify-content: center; }

    /* Summary Card Styling */
    .fe-summary-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid var(--gray-100); position: sticky; top: 100px; }
    .fe-summary-header { padding: 20px 24px; background: var(--dark); color: white; border-radius: 20px 20px 0 0; }
    .fe-summary-header h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: white; }
    .fe-summary-body { padding: 24px; }
    
    .summary-route-visual { display: flex; align-items: center; justify-content: space-between; gap: 15px; margin-bottom: 10px; }
    .summary-route-visual .city { flex: 1; text-align: center; }
    .summary-route-visual .code { font-size: 1.8rem; font-weight: 900; color: var(--dark); display: block; }
    .summary-route-visual .path { flex: 2; display: flex; align-items: center; gap: 10px; color: var(--primary); opacity: 0.6; }
    .summary-route-visual .line { flex: 1; height: 1px; background: currentColor; }
    
    .fe-summary-details { display: flex; flex-direction: column; gap: 10px; }
    .fe-summary-item { display: flex; justify-content: space-between; font-size: 0.85rem; }
    .fe-summary-item .label { color: var(--gray-500); font-weight: 600; display: flex; align-items: center; gap: 6px; }
    .fe-summary-item .value { color: var(--dark); font-weight: 800; }

    .fe-summary-total { background: var(--primary-50); padding: 20px; border-radius: 15px; text-align: center; }
    .total-label { font-size: 0.75rem; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 5px; }
    .total-value { color: var(--primary); }
    .total-value .currency { font-size: 1rem; font-weight: 800; margin-inline-end: 4px; }
    .total-value .amount { font-size: 2rem; font-weight: 900; }
    .total-note { font-size: 0.7rem; color: var(--gray-500); margin-top: 5px; font-weight: 600; }

    .fe-btn-light { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .fe-btn-light:hover { background: #e2e8f0; color: #334155; }
    
    .readonly-field-0, .readonly-field-1, .readonly-field-2, .readonly-field-3, .readonly-field-4, .readonly-field-5, .readonly-field-6, .readonly-field-7, .readonly-field-8, .readonly-field-9 {
        pointer-events: none !important;
        background-color: #f8fafc !important;
        opacity: 0.8 !important;
        border-color: #e2e8f0 !important;
    }

    .fe-policy-card { background: #fffcf0; border: 1px solid #ffeeba; border-radius: 15px; padding: 18px; margin-top: 20px; }
    .fe-policy-card h4 { font-size: 0.9rem; font-weight: 800; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
    .fe-policy-card p { font-size: 0.8rem; margin: 0; line-height: 1.5; }

    .fe-booking-action { margin-top: 30px; }
    .fe-terms-fine { font-size: 0.75rem; color: var(--gray-500); margin-top: 15px; text-align: center; }
    .fe-terms-fine a { color: var(--primary); font-weight: 700; text-decoration: underline; }

    /* ═══ EXTRA SERVICES ═══ */
    .es-group { margin-bottom: 28px; }
    .es-group-title {
        display: flex; align-items: center; gap: 10px;
        font-size: 0.9rem; font-weight: 900; color: var(--dark);
        text-transform: uppercase; letter-spacing: 0.5px;
        border-bottom: 2px solid var(--gray-100); padding-bottom: 10px; margin-bottom: 16px;
    }
    .es-group-title i { color: var(--primary); }
    .es-passenger-row { margin-bottom: 20px; }
    .es-pax-label {
        font-size: 0.82rem; font-weight: 800; color: var(--gray-600);
        margin-bottom: 8px; display: flex; align-items: center; gap: 6px;
    }
    .es-options { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; }
    .es-option {
        display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 8px;
        background: #ffffff; border: 2px solid var(--gray-200);
        border-radius: 12px; padding: 15px; cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative; overflow: hidden;
    }
    .es-option input[type="radio"] { position: absolute; opacity: 0; cursor: pointer; }
    .es-option:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transform: translateY(-2px); }
    .es-option:has(input:checked) {
        border-color: var(--primary); background: rgba(15,76,129,0.03);
        box-shadow: 0 0 0 1px var(--primary);
    }
    .es-option:has(input:checked)::after {
        content: '\f058'; font-family: 'Font Awesome 5 Free'; font-weight: 900;
        color: var(--primary); position: absolute; top: 10px; right: 10px; font-size: 1.1rem;
    }
    .es-option span:not(.es-option-price) { font-size: 0.85rem; font-weight: 700; color: var(--dark); line-height: 1.3; }
    .es-option-price { color: var(--primary); font-weight: 900; font-size: 0.95rem; margin-top: auto; }
    .es-total-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--primary); color: white;
        padding: 6px 14px; border-radius: 20px;
        font-size: 0.8rem; font-weight: 800; margin-top: 16px;
        transition: all 0.3s;
    }
    .es-no-services { text-align:center; padding: 24px; color: var(--gray-400); font-weight:700; }

    /* Select2 Premium Styling */
    .select2-container--default .select2-selection--single {
        height: 48px !important;
        background-color: var(--gray-50) !important;
        border: 1.5px solid var(--gray-100) !important;
        border-radius: 12px !important;
        display: flex;
        align-items: center;
        transition: all 0.2s;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-inline-start: 16px !important;
        font-weight: 600;
        color: var(--dark);
        font-size: 0.95rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
        inset-inline-end: 10px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--primary) !important;
        background-color: white !important;
        box-shadow: 0 0 0 4px var(--primary-50) !important;
    }
    .select2-dropdown {
        border: 1px solid var(--gray-200) !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        overflow: hidden;
        z-index: 1060;
    }
    .select2-results__option {
        padding: 10px 16px !important;
        font-size: 0.9rem;
        font-weight: 600;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: var(--primary) !important;
    }

    /* ══════════════════════════════════════════════════════
       FLIGHT BOOKING RTL RULES
       ══════════════════════════════════════════════════════ */
    html[dir="rtl"] .fe-booking-grid,
    body[dir="rtl"] .fe-booking-grid {
        direction: rtl;
    }

    html[dir="rtl"] .fe-booking-main,
    body[dir="rtl"] .fe-booking-main {
        direction: rtl;
        text-align: right;
    }

    html[dir="rtl"] .fe-booking-sidebar,
    body[dir="rtl"] .fe-booking-sidebar {
        direction: rtl;
        text-align: right;
    }

    /* Cards & Headers in RTL */
    html[dir="rtl"] .fe-booking-card {
        text-align: right;
        direction: rtl;
    }

    html[dir="rtl"] .fe-booking-card[style*="border-left"] {
        border-left: none !important;
        border-right: 4px solid var(--primary) !important;
        background: linear-gradient(to left, #ffffff, #f8fafc) !important;
    }

    html[dir="rtl"] .fe-card-header {
        direction: rtl;
        text-align: right;
    }

    html[dir="rtl"] .fe-card-body {
        direction: rtl;
        text-align: right;
    }

    /* Labels, inputs, form rows */
    html[dir="rtl"] .fe-form-row {
        direction: rtl;
    }

    html[dir="rtl"] .fe-label {
        text-align: right;
        direction: rtl;
    }

    html[dir="rtl"] .fe-input {
        text-align: right;
        direction: rtl;
    }

    html[dir="rtl"] .fe-input[type="email"],
    html[dir="rtl"] .fe-input[type="tel"],
    html[dir="rtl"] #add_phone,
    html[dir="rtl"] #phone,
    html[dir="rtl"] .phone-input-field {
        text-align: left !important;
        direction: ltr !important;
    }

    /* Timer card */
    html[dir="rtl"] .fe-session-timer-card {
        direction: rtl;
    }

    /* Guest Auth Banner */
    html[dir="rtl"] .fe-guest-auth-inner {
        direction: rtl;
    }

    html[dir="rtl"] .fe-guest-auth-content {
        text-align: right;
    }

    /* Insurance Cross-Sell Component */
    html[dir="rtl"] .fe-insurance-card,
    html[dir="rtl"] .fe-ins-header,
    html[dir="rtl"] .fe-ins-titles,
    html[dir="rtl"] .fe-ins-benefit-item,
    html[dir="rtl"] .fe-ins-option-card {
        direction: rtl;
        text-align: right;
    }

    /* Flight Itinerary Route Visual in Summary Card */
    html[dir="rtl"] .summary-route-visual {
        direction: rtl;
    }

    html[dir="rtl"] .summary-route-visual .path i.fa-plane {
        transform: rotate(-90deg) !important;
    }

    /* Summary Details in Sidebar */
    html[dir="rtl"] .fe-summary-card,
    html[dir="rtl"] .fe-summary-header,
    html[dir="rtl"] .fe-summary-body,
    html[dir="rtl"] .fe-summary-details {
        direction: rtl;
        text-align: right;
    }

    html[dir="rtl"] .fe-summary-item {
        direction: rtl;
    }

    /* Extra Services Options */
    html[dir="rtl"] .es-group-title {
        direction: rtl;
        text-align: right;
    }

    html[dir="rtl"] .es-option:has(input:checked)::after {
        left: 10px;
        right: auto;
    }

    html[dir="rtl"] .fe-guest-header {
        direction: rtl;
    }

    /* Select2 dropdown arrow & alignment */
    html[dir="rtl"] .select2-container--default .select2-selection--single {
        direction: rtl;
        text-align: right;
    }

    html[dir="rtl"] .select2-container--default .select2-selection--single .select2-selection__arrow {
        left: 10px !important;
        right: auto !important;
    }

    html[dir="rtl"] .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-right: 16px !important;
        padding-left: 36px !important;
        text-align: right !important;
        direction: rtl;
    }

    html[dir="rtl"] .select2-results__option {
        text-align: right;
        direction: rtl;
    }
</style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(app()->getLocale() == 'ar')
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
@endif

<script>
$(document).ready(function() {
    const isVisualSeatMapEnabled = "{{ $details['visual_seat_map'] ?? '1' }}" === "1";
    const totalPax = {{ $totalPax }};
    let extraServicesTotal = 0;

    function fetchExtraServices() {
        $('#extraServicesCard').show();
        $.ajax({
            url: '{{ route("api.flights.extra-services") }}',
            method: 'POST',
            data: { 
                session_id: '{{ $details["session_id"] ?? "" }}', 
                fare_source_code: '{{ $details["fare_source_code"] ?? "" }}', 
                _token: '{{ csrf_token() }}' 
            },
            success: function(res) {
                $('#esLoader').hide();
                $('#esContent').show();

                const serviceData = (res && res.data && Array.isArray(res.data)) ? res.data : [];
                window.rawServicesResponse = serviceData; // Store globally for visual seat map

                if (serviceData.length === 0) {
                    $('#esEmptyState').show();
                } else {
                    $('#esEmptyState').hide();
                }
                
                renderExtraServices(serviceData);
            },
            error: function() {
                $('#esLoader').hide();
                $('#esEmptyState').show();
                renderExtraServices([]);
            }
        });
    }

    function renderExtraServices(services) {
        // Filter out dummy/none items returned by provider (e.g. NoBaggage 0kg, NoMeal)
        const isDummyService = (svc) => {
            const code = String(svc.code || '').trim().toUpperCase();
            const desc = String(svc.description || '').trim().toLowerCase();
            const price = parseFloat(svc.price) || 0;
            if (['NOMEAL', 'NOBAGGAGE', 'NOSEAT', 'NONE', 'NIL', 'NO_MEAL', 'NO_BAGGAGE', 'NO_SEAT'].includes(code)) return true;
            if (code.startsWith('NO') && price <= 0 && code.length <= 10) return true;
            if (['0 kg', '0kg', '0 kgs', '0 k', '0lbs', '0', 'no meal', 'no baggage', 'none', 'no extra meal', 'no extra baggage'].includes(desc)) return true;
            if (!desc && price <= 0 && (!code || code === 'NOMEAL' || code === 'NOBAGGAGE')) return true;
            return false;
        };

        const cleanServices = (services || []).filter(s => !isDummyService(s));

        const flightTypes = new Set(cleanServices.map(s => s.flight_type));
        if (flightTypes.size === 0) flightTypes.add('outbound');

        const grouped = {};
        flightTypes.forEach(ft => {
            grouped['baggage_' + ft] = { label: 'baggage', flight: ft, items: [] };
            grouped['meal_' + ft] = { label: 'meal', flight: ft, items: [] };
            grouped['seat_' + ft] = { label: 'seat', flight: ft, items: [] };
        });

        cleanServices.forEach(svc => {
            const key = svc.type + '_' + svc.flight_type;
            if (grouped[key]) {
                grouped[key].items.push(svc);
            }
        });

        const typeIcons = { baggage: 'fa-suitcase-rolling', meal: 'fa-utensils', seat: 'fa-chair', unknown: 'fa-concierge-bell' };
        const typeLabels = { baggage: '{{ __("Baggage") }}', meal: '{{ __("Meals") }}', seat: '{{ __("Seats") }}', unknown: '{{ __("Services") }}' };
        const flightLabels = { outbound: '{{ __("Outbound") }}', inbound: '{{ __("Return") }}' };

        const extraContainers = $('[id^="passenger-extras-"]');
        const countToRender = extraContainers.length > 0 ? extraContainers.length : totalPax;

        // For each passenger, generate their own extra services card
        for (let paxIdx = 0; paxIdx < countToRender; paxIdx++) {
            let paxHtml = `<div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; margin-top: 20px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                <div style="padding: 12px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                    <h6 style="color: var(--primary); font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-plus-circle"></i>{{ __("Customize Trip") }}
                    </h6>
                    <span class="badge" style="background: rgba(15,76,129,0.1); color: var(--primary); font-size: 0.8rem; font-weight: 700; padding: 5px 12px; border-radius: 6px; border: 1px solid rgba(15,76,129,0.2);">
                        <i class="fas fa-user me-1"></i>{{ __("Passenger") }} ${paxIdx + 1}
                    </span>
                </div>
                
                <div class="fe-es-tabs" style="border-bottom: 1px solid #e2e8f0; display: flex;">
                    <button type="button" class="fe-es-tab active" onclick="window.switchPaxTab(${paxIdx}, 'baggage')"><i class="fas fa-suitcase-rolling"></i> {{ __("Baggage") }}</button>
                    <button type="button" class="fe-es-tab" onclick="window.switchPaxTab(${paxIdx}, 'meal')"><i class="fas fa-utensils"></i> {{ __("Meals") }}</button>
                    <button type="button" class="fe-es-tab" onclick="window.switchPaxTab(${paxIdx}, 'seat')"><i class="fas fa-chair"></i> {{ __("Seats") }}</button>
                </div>
                
                <div class="fe-es-tab-content-container" style="padding: 20px; background: #f8fafc;">
            `;

            ['baggage', 'meal', 'seat'].forEach(category => {
                const isActive = category === 'baggage' ? 'active' : '';
                paxHtml += `<div id="pax-${paxIdx}-${category}" class="fe-es-tab-content ${isActive}">`;

                const emptyNotice = {
                    baggage: `
                        <div style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 10px; padding: 18px 20px; text-align: center; color: #475569;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; margin-bottom: 8px;">
                                <i class="fas fa-suitcase-rolling"></i>
                            </div>
                            <div style="font-weight: 700; font-size: 0.9rem; color: #0f172a; margin-bottom: 3px;">
                                {{ __('Standard Baggage Included') }}
                            </div>
                            <div style="font-size: 0.78rem; color: #64748b; line-height: 1.4;">
                                {{ __('Standard baggage allowance is included with this ticket per airline policy. Additional dynamic baggage is not required or not offered for this fare.') }}
                            </div>
                        </div>
                    `,
                    seat: `
                        <div style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 10px; padding: 18px 20px; text-align: center; color: #475569;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #ede9fe; color: #7c3aed; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; margin-bottom: 8px;">
                                <i class="fas fa-chair"></i>
                            </div>
                            <div style="font-weight: 700; font-size: 0.9rem; color: #0f172a; margin-bottom: 3px;">
                                {{ __('Seat Assignment at Check-in') }}
                            </div>
                            <div style="font-size: 0.78rem; color: #64748b; line-height: 1.4;">
                                {{ __('Seat pre-selection is not open via GDS for this flight. Seats will be automatically assigned free of charge during airline online check-in or at airport counter.') }}
                            </div>
                        </div>
                    `,
                    meal: `
                        <div style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 10px; padding: 18px 20px; text-align: center; color: #475569;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #fef3c7; color: #d97706; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; margin-bottom: 8px;">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div style="font-weight: 700; font-size: 0.9rem; color: #0f172a; margin-bottom: 3px;">
                                {{ __('Complimentary In-Flight Service') }}
                            </div>
                            <div style="font-size: 0.78rem; color: #64748b; line-height: 1.4;">
                                {{ __('Standard in-flight meal service is served on board as scheduled by operating airline.') }}
                            </div>
                        </div>
                    `
                };

                if (category === 'seat') {
                    let hasSeats = Object.values(grouped).some(g => g.label === 'seat' && g.items && g.items.length > 0);
                    
                    if (hasSeats) {
                        // 1. Interactive Aircraft Map Banner
                        paxHtml += `
                            <div style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1.5px solid #bfdbfe; border-radius: 12px; padding: 14px 18px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #0f4c81; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                                        <i class="fas fa-plane"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 800; font-size: 0.92rem; color: #0f172a;">
                                            {{ __('Interactive Aircraft Seat Map') }}
                                        </div>
                                        <div style="font-size: 0.78rem; color: #64748b;">
                                            {{ __('Prefer to choose visually on the plane? Open the aircraft map to pick your exact seat.') }}
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="fe-btn fe-btn-primary btn-sm" onclick="openVisualSeatMap(${paxIdx})" style="padding: 8px 18px; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 8px rgba(15,76,129,0.25);">
                                    <i class="fas fa-chair"></i> {{ __('Open Aircraft Map') }}
                                </button>
                                <input type="hidden" id="visual_seat_selections_${paxIdx}" name="visual_seat_selections" value="" class="global-seat-selections-input">
                            </div>
                        `;

                        // 2. Regular/Standard Seat Selection Grid
                        flightTypes.forEach(ft => {
                            const key = 'seat_' + ft;
                            const group = grouped[key];
                            const label = (ft === 'inbound' ? '{{ __("Return") }}' : '{{ __("Outbound") }}') + ' {{ __("Seats Selection") }}';
                            const fieldName = ft === 'inbound' ? `passengers[${paxIdx}][extra_services_inbound][]` : `passengers[${paxIdx}][extra_services_outbound][]`;
                            
                            paxHtml += `
                            <div class="mb-4">
                                <div style="font-weight: 700; color: #475569; font-size: 0.88rem; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
                                    <span><i class="fas fa-chair me-1"></i> ${label}</span>
                                    <span class="text-muted small">({{ __('Or select directly from below') }})</span>
                                </div>`;

                            if (!group || group.items.length === 0) {
                                paxHtml += emptyNotice.seat;
                            } else {
                                paxHtml += `
                                <div class="es-options-grid" style="display: grid; gap: 10px; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); max-height: 420px; overflow-y: auto; padding: 4px;">
                                    <label class="es-card-option" style="cursor:pointer;">
                                        <input type="radio" name="extra_${key}_pax${paxIdx}" value="" data-price="0" checked onchange="handleSeatCardSelection(${paxIdx}, this)" style="display:none;">
                                        <div class="es-card-content" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; text-align: center; background: white; transition: all 0.2s; height:100%; display:flex; flex-direction:column; justify-content:center; align-items:center;">
                                            <i class="fas fa-times-circle" style="color:#94a3b8; font-size:1.2rem; margin-bottom:6px;"></i>
                                            <span style="font-weight:600; color:#334155; font-size:0.85rem;">{{ __("No Seat Preference") }}</span>
                                            <span style="font-size:0.75rem; color:#64748b; margin-top:2px;">{{ __("Assigned at Check-in") }}</span>
                                        </div>
                                    </label>`;
                                    
                                group.items.forEach(svc => {
                                    if (svc.available === false || svc.status === 'occupied') return;
                                    const numPrice = parseFloat(svc.price) || 0;
                                    const priceHtml = numPrice <= 0
                                        ? `<span class="badge" style="background:#ecfdf5; color:#059669; font-weight:800; font-size:0.8rem; padding:4px 8px; border-radius:6px; border:1px solid #a7f3d0;"><i class="fas fa-check-circle me-1"></i> {{ __('Free / Included') }}</span>`
                                        : `<span style="color:#0f766e; font-weight:800; font-size:0.9rem;">+${numPrice.toFixed(2)} ${svc.currency}</span>`;

                                    let typeBadge = '';
                                    if (svc.type_text) {
                                        let icon = 'fa-chair';
                                        let tLower = svc.type_text.toLowerCase();
                                        if (tLower.includes('window')) icon = 'fa-window-frame text-primary';
                                        else if (tLower.includes('aisle')) icon = 'fa-walking text-info';
                                        else if (tLower.includes('exit')) icon = 'fa-door-open text-warning';
                                        typeBadge = `<span class="badge bg-light text-dark border font-weight-normal" style="font-size:0.72rem; margin-bottom:5px; padding: 3px 6px;"><i class="fas ${icon} me-1"></i> ${svc.type_text}</span>`;
                                    }

                                    paxHtml += `
                                        <label class="es-card-option" style="cursor:pointer;" id="seat-card-opt-${paxIdx}-${svc.code}">
                                            <input type="radio"
                                                   name="extra_${key}_pax${paxIdx}"
                                                   value="${svc.code}"
                                                   data-field="${fieldName}"
                                                   data-price="${numPrice}"
                                                   data-desc="${escapeHtml(svc.description)}"
                                                   data-pax="${paxIdx}"
                                                   data-type="seat"
                                                   data-currency="${svc.currency || 'SAR'}"
                                                   data-flight-type="${ft}"
                                                   onchange="handleSeatCardSelection(${paxIdx}, this)" style="display:none;">
                                            <div class="es-card-content" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; text-align: center; background: white; transition: all 0.2s; height:100%; display:flex; flex-direction:column; justify-content:center; align-items:center;">
                                                <i class="fas fa-chair" style="color:var(--primary); font-size:1.2rem; margin-bottom:6px;"></i>
                                                <span style="font-weight:700; color:#0f172a; font-size:0.9rem; line-height:1.2; margin-bottom:3px;">${escapeHtml(svc.description)}</span>
                                                ${typeBadge}
                                                ${priceHtml}
                                            </div>
                                        </label>`;
                                });
                                
                                paxHtml += `</div>`;
                            }
                            paxHtml += `</div>`;
                        });
                    } else {
                        paxHtml += emptyNotice.seat;
                    }
                } else {
                    flightTypes.forEach(ft => {
                        const key = category + '_' + ft;
                        const group = grouped[key];
                        const label = (ft === 'inbound' ? '{{ __("Return") }}' : '{{ __("Outbound") }}') + ' ' + typeLabels[category];
                        const icon = typeIcons[category];
                        const fieldName = ft === 'inbound' ? `passengers[${paxIdx}][extra_services_inbound][]` : `passengers[${paxIdx}][extra_services_outbound][]`;
                        
                        paxHtml += `
                        <div class="mb-4">
                            <div style="font-weight: 700; color: #475569; font-size: 0.9rem; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                                <i class="fas ${icon}"></i> ${label}
                            </div>`;
                            
                        if (!group || group.items.length === 0) {
                            paxHtml += emptyNotice[category] || `
                            <div style="background: white; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 15px; text-align: center; color: #64748b; font-size: 0.9rem;">
                                <i class="fas fa-info-circle mb-2" style="font-size:1.2rem; color:#94a3b8;"></i><br>
                                {{ __("No options available for") }} ${typeLabels[category]}
                            </div>`;
                        } else {
                            paxHtml += `
                            <div class="es-options-grid" style="display: grid; gap: 10px; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                                <label class="es-card-option" style="cursor:pointer;">
                                    <input type="radio" name="extra_${key}_pax${paxIdx}" value="" data-price="0" checked onchange="updateExtraTotal()" style="display:none;">
                                    <div class="es-card-content" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; text-align: center; background: white; transition: all 0.2s; height:100%; display:flex; flex-direction:column; justify-content:center; align-items:center;">
                                        <i class="fas fa-times-circle" style="color:#94a3b8; font-size:1.2rem; margin-bottom:8px;"></i>
                                        <span style="font-weight:600; color:#334155; font-size:0.85rem;">{{ __("No Extra") }} ${typeLabels[category]}</span>
                                    </div>
                                </label>`;
                                
                            group.items.forEach(svc => {
                                const numPrice = parseFloat(svc.price) || 0;
                                const priceHtml = numPrice <= 0
                                    ? `<span class="badge" style="background:#ecfdf5; color:#059669; font-weight:800; font-size:0.8rem; padding:4px 8px; border-radius:6px; border:1px solid #a7f3d0;"><i class="fas fa-check-circle me-1"></i> {{ __('Free / Included') }}</span>`
                                    : `<span style="color:#0f766e; font-weight:800; font-size:0.9rem;">+${numPrice.toFixed(2)} ${svc.currency}</span>`;

                                paxHtml += `
                                    <label class="es-card-option" style="cursor:pointer;">
                                        <input type="radio"
                                               name="extra_${key}_pax${paxIdx}"
                                               value="${svc.code}"
                                               data-field="${fieldName}"
                                               data-price="${numPrice}"
                                               data-pax="${paxIdx}"
                                               data-type="${category}"
                                               data-desc="${escapeHtml(svc.description)}"
                                               data-currency="${svc.currency || 'SAR'}"
                                               data-flight-type="${ft}"
                                               onchange="updateExtraTotal()" style="display:none;">
                                        <div class="es-card-content" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; text-align: center; background: white; transition: all 0.2s; height:100%; display:flex; flex-direction:column; justify-content:center; align-items:center;">
                                            <i class="fas ${icon}" style="color:var(--primary); font-size:1.2rem; margin-bottom:8px;"></i>
                                            <span style="font-weight:600; color:#334155; font-size:0.85rem; line-height:1.2; margin-bottom:6px;">${escapeHtml(svc.description)}</span>
                                            ${priceHtml}
                                        </div>
                                    </label>`;
                            });
                            
                            paxHtml += `</div>`;
                        }
                        
                        paxHtml += `</div>`;
                    });
                }
                
                paxHtml += `</div>`; // Close fe-es-tab-content
            });

            paxHtml += `</div></div>`; // Close fe-es-tab-content-container and card
            
            $(`#passenger-extras-${paxIdx}`).html(paxHtml);
        }

        // Define global switchPaxTab if it doesn't exist yet
        if (!window.switchPaxTab) {
            window.switchPaxTab = function(paxIdx, tabName) {
                const container = $('#passenger-extras-' + paxIdx);
                const tabs = ['baggage', 'meal', 'seat'];
                const tabIndex = tabs.indexOf(tabName);
                
                container.find('.fe-es-tab').removeClass('active');
                container.find('.fe-es-tab-content').removeClass('active');
                
                container.find('.fe-es-tab').eq(tabIndex).addClass('active');
                container.find('#pax-' + paxIdx + '-' + tabName).addClass('active');
            };
        }

        // Add styles for the new radio cards
        if ($('#esCardStyles').length === 0) {
            $('head').append(`
                <style id="esCardStyles">
                    .es-card-option input[type="radio"]:checked + .es-card-content {
                        border-color: var(--primary) !important;
                        background: rgba(15,76,129,0.05) !important;
                        box-shadow: 0 4px 12px rgba(15,76,129,0.1) !important;
                    }
                    .es-card-option:hover .es-card-content {
                        border-color: var(--primary);
                        transform: translateY(-2px);
                    }
                </style>
            `);
        }
    }

    window.updateExtraTotal = function() {
        let total = 0;
        
        // Clear all previous hidden fields for extra services to prevent duplicates
        $('input[name*="extra_services_"]').remove();
        $('.visual-seat-input').remove();
        $('.extra-detail-input').remove();

        let paxDetails = {};

        $('input[name^="extra_"]:checked').each(function() {
            const code = $(this).val();
            const price = parseFloat($(this).data('price')) || 0;
            const fieldName = $(this).data('field');
            const paxIdx = $(this).data('pax');
            const type = $(this).data('type') || '';
            const desc = $(this).data('desc') || '';
            const curr = $(this).data('currency') || 'SAR';
            const ft = $(this).data('flight-type') || 'outbound';

            if (code && code !== '') {
                total += price;

                // Sync to actual form field
                if (fieldName) {
                    $('<input>').attr({ type: 'hidden', name: fieldName, value: code }).appendTo('#flightBookingForm');
                }

                // Collect structured detail
                if (typeof paxIdx !== 'undefined') {
                    if (!paxDetails[paxIdx]) paxDetails[paxIdx] = [];
                    paxDetails[paxIdx].push({
                        type: type || 'service',
                        code: code,
                        description: desc || code,
                        price: price,
                        currency: curr,
                        flight_type: ft
                    });
                }
            }
        });

        // Add Visual Seat Map selections if available and not already captured via standard radio
        let seatSelections = $('.global-seat-selections-input').first().val();
        if (seatSelections) {
            try {
                let parsedSeats = JSON.parse(seatSelections);
                Object.entries(parsedSeats).forEach(([paxId, seat]) => {
                    let alreadyChecked = $(`input[name*="seat_"][name*="pax${paxId}"][value="${seat.code}"]:checked`).length > 0;
                    if (!alreadyChecked && seat && seat.code) {
                        const seatPrice = parseFloat(seat.price) || 0;
                        total += seatPrice;
                        let fieldName = `passengers[${paxId}][extra_services_outbound][]`;
                        $('<input>').attr({ type: 'hidden', name: fieldName, value: seat.code, class: 'visual-seat-input' }).appendTo('#flightBookingForm');

                        if (!paxDetails[paxId]) paxDetails[paxId] = [];
                        paxDetails[paxId].push({
                            type: 'seat',
                            code: seat.code,
                            description: seat.description || ('Seat ' + seat.code),
                            price: seatPrice,
                            currency: 'SAR',
                            flight_type: 'outbound'
                        });
                    }
                });
            } catch(e) {}
        }

        // Serialize structured details to ensure backend, voucher & customer views have full data
        Object.entries(paxDetails).forEach(([paxIdx, details]) => {
            details.forEach((item, itemIdx) => {
                $('<input>').attr({ type: 'hidden', class: 'extra-detail-input', name: `passengers[${paxIdx}][extra_services_details][${itemIdx}][type]`, value: item.type }).appendTo('#flightBookingForm');
                $('<input>').attr({ type: 'hidden', class: 'extra-detail-input', name: `passengers[${paxIdx}][extra_services_details][${itemIdx}][code]`, value: item.code }).appendTo('#flightBookingForm');
                $('<input>').attr({ type: 'hidden', class: 'extra-detail-input', name: `passengers[${paxIdx}][extra_services_details][${itemIdx}][description]`, value: item.description }).appendTo('#flightBookingForm');
                $('<input>').attr({ type: 'hidden', class: 'extra-detail-input', name: `passengers[${paxIdx}][extra_services_details][${itemIdx}][price]`, value: item.price }).appendTo('#flightBookingForm');
                $('<input>').attr({ type: 'hidden', class: 'extra-detail-input', name: `passengers[${paxIdx}][extra_services_details][${itemIdx}][currency]`, value: item.currency }).appendTo('#flightBookingForm');
                $('<input>').attr({ type: 'hidden', class: 'extra-detail-input', name: `passengers[${paxIdx}][extra_services_details][${itemIdx}][flight_type]`, value: item.flight_type }).appendTo('#flightBookingForm');
            });
        });

        extraServicesTotal = total;
        if (total > 0) {
            $('#esTotal').fadeIn();
            $('#esTotalValue').text(total.toFixed(2) + ' SAR');
        } else {
            $('#esTotal').fadeOut();
        }

        // Also update main total
        updateMainTotal();
    };

    window.insuranceQuoteData = null;

    @if(\App\Models\Setting::get('insurance_enabled', '1') == '1')
    function fetchInsuranceQuote() {
        $('#insurancePriceDisplay').html('<span class="spinner-border spinner-border-sm text-primary" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span> <span class="fs-12 text-muted ms-1">{{ __("Calculating...") }}</span>');
        $('#insuranceTotalOptionPrice').html('<span class="spinner-border spinner-border-sm text-primary me-1" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span> <span class="fs-12 text-muted">{{ __("Calculating...") }}</span>');

        $.ajax({
            url: '{{ route("insurance.quote") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                origin_country: '{{ $details["from"] ?? "SA" }}',
                destination_country: '{{ $details["to"] ?? "GLOBAL" }}',
                departure_date: '{{ $details["departDate"] ?? date("Y-m-d") }}',
                return_date: '{{ $details["returnDate"] ?? date("Y-m-d", strtotime("+7 days")) }}',
                trip_cost: {{ floatval($details["total_amount"] ?? 0) }},
                passengers_count: {{ $totalPax ?? 1 }},
                booking_type: 'flight'
            },
            success: function(res) {
                if (res && res.success) {
                    window.insuranceQuoteData = res;
                    const uPrice = parseFloat(res.unit_price) || 0;
                    const sPrice = parseFloat(res.selling_price) || 0;
                    $('#insurancePriceDisplay').text(uPrice.toFixed(2) + ' SAR');
                    $('#insuranceTotalOptionPrice').text('+ ' + sPrice.toFixed(2) + ' SAR');
                    $('#hiddenInsuranceQuoteId').val(res.quote_id);
                    if ($('input[name="include_insurance"]:checked').val() == '1') {
                        updateMainTotal();
                    }
                } else {
                    $('#insurancePriceDisplay').text('-- SAR');
                    $('#insuranceTotalOptionPrice').text('-- SAR');
                }
            },
            error: function(err) {
                console.warn('Could not load insurance quote', err);
                $('#insurancePriceDisplay').text('-- SAR');
                $('#insuranceTotalOptionPrice').text('-- SAR');
            }
        });
    }

    $(document).on('change', 'input[name="include_insurance"]', function() {
        if ($(this).val() == '1') {
            $('#optAddInsuranceWrapper').addClass('selected');
            $('#optNoInsuranceWrapper').removeClass('selected');
        } else {
            $('#optNoInsuranceWrapper').addClass('selected');
            $('#optAddInsuranceWrapper').removeClass('selected');
        }
        updateMainTotal();
    });

    // Make wrapper labels clickable directly
    $('#optAddInsuranceWrapper').on('click', function(e) {
        if (e.target.tagName !== 'INPUT') {
            $('#optAddInsurance').prop('checked', true).trigger('change');
        }
    });
    $('#optNoInsuranceWrapper').on('click', function(e) {
        if (e.target.tagName !== 'INPUT') {
            $('#optNoInsurance').prop('checked', true).trigger('change');
        }
    });

    // Fetch insurance quote automatically on page load
    $(function() {
        fetchInsuranceQuote();
    });
    @endif

    function updateMainTotal() {
        const baseEl = document.getElementById('grandTotalAmount');
        let baseTotal = 0;
        if (baseEl) {
            const rawBase = baseEl.getAttribute('data-base') || $(baseEl).data('base');
            baseTotal = parseFloat(String(rawBase).replace(/,/g, '')) || 0;
        }

        const isInsChecked = $('input[name="include_insurance"]:checked').val() === '1';
        let insuranceTotal = 0;
        if (isInsChecked && window.insuranceQuoteData && window.insuranceQuoteData.selling_price) {
            insuranceTotal = parseFloat(String(window.insuranceQuoteData.selling_price).replace(/,/g, '')) || 0;
        }

        const extraTotal = parseFloat(extraServicesTotal) || 0;

        $('#hiddenInsuranceAmount').val(insuranceTotal.toFixed(2));

        if (insuranceTotal > 0) {
            $('#sidebarInsuranceRow').slideDown(200);
            $('#sidebarInsuranceAmount').text('+ ' + insuranceTotal.toFixed(2) + ' SAR');
        } else {
            $('#sidebarInsuranceRow').slideUp(200);
        }

        if (extraTotal > 0) {
            $('#sidebarExtraServicesRow').slideDown(200);
            $('#sidebarExtraServicesAmount').text('+ ' + extraTotal.toFixed(2) + ' SAR');
        } else {
            $('#sidebarExtraServicesRow').slideUp(200);
        }

        const finalTotal = baseTotal + extraTotal + insuranceTotal;
        const formatted = finalTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        $('#grandTotalAmount').text(formatted);
        $('.total-value .amount').text(formatted);
        $('input[name="total_amount"]').val(finalTotal.toFixed(2));
    }

    // ═══ VISUAL SEAT MAP LOGIC ═══
    let rawSeatServices = [];
    let currentSeatPaxIndex = 0;
    let selectedSeatsByPax = {}; // { paxIndex: { code, description, price } }
    let selectedSeatsGlobal = new Set(); // To mark seats taken by our own group

    window.openVisualSeatMap = function(targetPaxIdx) {
        if (typeof targetPaxIdx !== 'undefined' && targetPaxIdx !== null) {
            currentSeatPaxIndex = parseInt(targetPaxIdx, 10);
        }
        // Extract raw seat services
        rawSeatServices = (window.rawServicesResponse || []).filter(s => s.type === 'seat');
        if (rawSeatServices.length === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: '{{ __("No Seats Available") }}',
                    text: '{{ __("The airline has not provided interactive seat map data for this flight. You may be assigned a seat at check-in.") }}',
                    confirmButtonText: '{{ __("OK") }}',
                    confirmButtonColor: '#0f4c81'
                });
            } else {
                alert('{{ __("The airline has not provided interactive seat map data for this flight. You may be assigned a seat at check-in.") }}');
            }
            return;
        }

        buildSeatPaxList();
        buildAircraftLayout();
        const modal = document.getElementById('seatMapModal');
        if (modal) {
            modal.classList.remove('d-none');
            setTimeout(() => modal.classList.add('show'), 10);
            $('body').addClass('modal-open');
        }
    };

    window.closeVisualSeatMap = function() {
        const modal = document.getElementById('seatMapModal');
        if (modal) {
            modal.classList.remove('show');
            setTimeout(() => modal.classList.add('d-none'), 250);
            $('body').removeClass('modal-open');
        }
    };

    function buildSeatPaxList() {
        let html = '';
        const extraContainers = $('[id^="passenger-extras-"]');
        const count = extraContainers.length > 0 ? extraContainers.length : totalPax;
        for (let i = 0; i < count; i++) {
            let activeCls = i === currentSeatPaxIndex ? 'active' : '';
            let seat = selectedSeatsByPax[i];
            
            let statusHtml = '';
            if (seat && seat.code) {
                let p = parseFloat(seat.price) || 0;
                let priceTxt = p > 0 ? `+${p.toFixed(2)} SAR` : '{{ __("Free") }}';
                statusHtml = `
                    <div class="pax-seat-badge-selected">
                        <span class="badge-seat-code"><i class="fas fa-chair me-1"></i> ${escapeHtml(seat.seat_label || seat.description)}</span>
                        <span class="badge-seat-price">${priceTxt}</span>
                    </div>
                `;
            } else {
                statusHtml = `
                    <div class="pax-seat-badge-empty">
                        <i class="fas fa-plus-circle me-1"></i> {{ __("Click to select seat") }}
                    </div>
                `;
            }

            html += `
                <div class="seat-pax-card ${activeCls}" onclick="selectPaxForSeat(${i})">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="pax-card-name">
                            <i class="fas fa-user-circle me-1 text-primary"></i> {{ __("Passenger") }} ${i+1}
                        </div>
                        <span class="pax-active-pill">{{ __("Active") }}</span>
                    </div>
                    ${statusHtml}
                </div>
            `;
        }
        $('#seatPaxList').html(html);
        updateSeatMapTotal();
    }

    window.selectPaxForSeat = function(idx) {
        currentSeatPaxIndex = idx;
        $('.seat-pax-card').removeClass('active');
        $($('.seat-pax-card')[idx]).addClass('active');
        buildAircraftLayout();
    };

    function buildAircraftLayout() {
        if (!rawSeatServices || rawSeatServices.length === 0) {
            $('#aircraftSeatsContainer').html(`
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-chair fa-3x mb-3 text-secondary"></i>
                    <p>{{ __("No seat map data available from airline") }}</p>
                </div>
            `);
            return;
        }

        // Group seats by row
        let rowsMap = {};
        let hasRealRows = false;

        rawSeatServices.forEach((svc, index) => {
            let rowNum = svc.row ? parseInt(svc.row, 10) : null;
            if (rowNum && !isNaN(rowNum) && rowNum > 0) {
                hasRealRows = true;
                if (!rowsMap[rowNum]) rowsMap[rowNum] = [];
                rowsMap[rowNum].push(svc);
            }
        });

        if (!hasRealRows || Object.keys(rowsMap).length === 0) {
            const chunkSize = 6;
            for (let i = 0; i < rawSeatServices.length; i += chunkSize) {
                let rNum = Math.floor(i / chunkSize) + 1;
                rowsMap[rNum] = rawSeatServices.slice(i, i + chunkSize);
            }
        }

        let sortedRowKeys = Object.keys(rowsMap).map(Number).sort((a, b) => a - b);
        let totalRows = sortedRowKeys.length;

        let html = '';

        // Column Headers Bar (A B C | AISLE | D E F)
        html += `
            <div class="aircraft-col-headers">
                <div class="col-head-trio left-trio">
                    <div class="col-head-item window">
                        <span class="col-letter">A</span>
                        <span class="col-tag"><i class="fas fa-window-frame"></i> {{ __('Window') }}</span>
                    </div>
                    <div class="col-head-item middle">
                        <span class="col-letter">B</span>
                        <span class="col-tag">{{ __('Middle') }}</span>
                    </div>
                    <div class="col-head-item aisle">
                        <span class="col-letter">C</span>
                        <span class="col-tag"><i class="fas fa-walking"></i> {{ __('Aisle') }}</span>
                    </div>
                </div>
                <div class="col-head-aisle">
                    <span class="aisle-title"><i class="fas fa-arrows-alt-v"></i> {{ __('Aisle') }}</span>
                </div>
                <div class="col-head-trio right-trio">
                    <div class="col-head-item aisle">
                        <span class="col-letter">D</span>
                        <span class="col-tag"><i class="fas fa-walking"></i> {{ __('Aisle') }}</span>
                    </div>
                    <div class="col-head-item middle">
                        <span class="col-letter">E</span>
                        <span class="col-tag">{{ __('Middle') }}</span>
                    </div>
                    <div class="col-head-item window">
                        <span class="col-letter">F</span>
                        <span class="col-tag"><i class="fas fa-window-frame"></i> {{ __('Window') }}</span>
                    </div>
                </div>
            </div>
        `;

        sortedRowKeys.forEach((rowNum, rIdx) => {
            let rowSeats = rowsMap[rowNum] || [];
            
            let slotMap = { A: null, B: null, C: null, D: null, E: null, F: null };
            let unassignedSeats = [];

            rowSeats.forEach(s => {
                let letter = (s.seat || '').trim().toUpperCase();
                if (!letter && s.seat_label) {
                    letter = s.seat_label.replace(/[0-9]/g, '').trim().toUpperCase();
                }
                if (['A', 'B', 'C', 'D', 'E', 'F'].includes(letter) && !slotMap[letter]) {
                    slotMap[letter] = s;
                } else {
                    unassignedSeats.push(s);
                }
            });

            ['A', 'B', 'C', 'D', 'E', 'F'].forEach(letter => {
                if (!slotMap[letter] && unassignedSeats.length > 0) {
                    slotMap[letter] = unassignedSeats.shift();
                }
            });

            // Detect Exit Rows (e.g. overwing exit rows around middle of aircraft, or marked in type_text)
            let isExitRow = rowSeats.some(s => (s.type_text || '').toLowerCase().includes('exit')) 
                         || (totalRows > 15 && (rIdx === Math.floor(totalRows * 0.42) || rIdx === Math.floor(totalRows * 0.46)));

            if (isExitRow) {
                html += `
                    <div class="aircraft-exit-row-banner">
                        <div class="exit-door-pill left"><i class="fas fa-door-open"></i> EXIT</div>
                        <div class="exit-label">
                            <i class="fas fa-exclamation-circle text-warning me-1"></i> {{ __('Emergency Exit Row') }} • {{ __('Extra Legroom') }}
                        </div>
                        <div class="exit-door-pill right">EXIT <i class="fas fa-door-open"></i></div>
                    </div>
                `;
            }

            html += `<div class="aircraft-cabin-row ${isExitRow ? 'is-exit-row' : ''}">`;

            // Left Fuselage Airplane Oval Window
            html += `<div class="cabin-window left-window" title="{{ __('Window') }} (A)"><div class="oval-inner"></div></div>`;

            // Left Trio (A, B, C)
            html += `<div class="cabin-trio left-trio">`;
            ['A', 'B', 'C'].forEach(l => {
                html += renderSingleSeat(slotMap[l], rowNum, l);
            });
            html += `</div>`;

            // Central Aisle with Walking Floor Carpet Track and Row Number
            html += `
                <div class="cabin-aisle-track">
                    <span class="row-num-indicator" title="{{ __('Row') }} ${rowNum}">${rowNum}</span>
                </div>
            `;

            // Right Trio (D, E, F)
            html += `<div class="cabin-trio right-trio">`;
            ['D', 'E', 'F'].forEach(l => {
                html += renderSingleSeat(slotMap[l], rowNum, l);
            });
            html += `</div>`;

            // Right Fuselage Airplane Oval Window
            html += `<div class="cabin-window right-window" title="{{ __('Window') }} (F)"><div class="oval-inner"></div></div>`;

            html += `</div>`; // Close row
        });

        $('#aircraftSeatsContainer').html(html);
    }

    function renderSingleSeat(svc, rowNum, defaultLetter) {
        if (!svc) {
            return `<div class="seat-box-empty"></div>`;
        }

        let isOccupied = (svc.available === false || svc.status === 'occupied');
        let seatLabel = svc.seat_label || (rowNum + defaultLetter);
        let numPrice = parseFloat(svc.price) || 0;
        let priceFormatted = numPrice > 0 ? `${numPrice} ${svc.currency || 'SAR'}` : '{{ __("Free") }}';
        
        let typeText = svc.type_text || '';
        let typeBadge = '';
        let typeIcon = '';
        if (['A', 'F'].includes(defaultLetter) || typeText.toLowerCase().includes('window')) {
            typeBadge = 'is-window';
            typeIcon = '<i class="fas fa-window-frame seat-feature-icon" title="{{ __("Window Seat") }}"></i>';
        } else if (['C', 'D'].includes(defaultLetter) || typeText.toLowerCase().includes('aisle')) {
            typeBadge = 'is-aisle';
            typeIcon = '<i class="fas fa-walking seat-feature-icon" title="{{ __("Aisle Seat") }}"></i>';
        } else {
            typeBadge = 'is-middle';
        }

        let isSelectedByMe = selectedSeatsByPax[currentSeatPaxIndex] && selectedSeatsByPax[currentSeatPaxIndex].code === svc.code;
        
        let companionPaxIdx = null;
        Object.keys(selectedSeatsByPax).forEach(paxId => {
            if (paxId != currentSeatPaxIndex && selectedSeatsByPax[paxId] && selectedSeatsByPax[paxId].code === svc.code) {
                companionPaxIdx = parseInt(paxId, 10);
            }
        });

        let seatClass = 'available';
        let clickAttr = `onclick="handleAircraftSeatClick(this)"`;
        let statusTitle = `${seatLabel} (${typeText || defaultLetter}) - ${priceFormatted} - {{ __("Available") }}`;
        let innerStatus = '';

        if (isOccupied) {
            seatClass = 'occupied';
            clickAttr = `onclick="handleOccupiedSeatClick('${escapeHtml(seatLabel)}')"` ;
            statusTitle = `${seatLabel} (${typeText || defaultLetter}) - {{ __("Occupied / Unavailable") }}`;
            innerStatus = '<i class="fas fa-times occupied-x"></i>';
        } else if (isSelectedByMe) {
            seatClass = 'selected';
            statusTitle = `${seatLabel} - {{ __("Selected for current passenger") }}`;
            innerStatus = '<i class="fas fa-check selected-check"></i>';
        } else if (companionPaxIdx !== null) {
            seatClass = 'companion-selected';
            statusTitle = `${seatLabel} - {{ __("Selected for Passenger") }} ${companionPaxIdx + 1}`;
            innerStatus = `<span class="companion-pax-tag">P${companionPaxIdx + 1}</span>`;
        }

        return `
            <div class="seat-box ${seatClass} ${typeBadge}"
                 data-code="${escapeHtml(svc.code)}"
                 data-desc="${escapeHtml(svc.description)}"
                 data-price="${numPrice}"
                 data-label="${escapeHtml(seatLabel)}"
                 data-type="${escapeHtml(typeText || defaultLetter)}"
                 ${clickAttr}
                 title="${escapeHtml(statusTitle)}">
                <div class="seat-headrest"></div>
                <div class="seat-inner-cushion">
                    <span class="seat-lbl">${seatLabel}</span>
                    ${innerStatus}
                </div>
                ${typeIcon}
                <div class="seat-cost-tag">${numPrice > 0 ? numPrice : '{{ __("Free") }}'}</div>
            </div>
        `;
    }

    window.handleOccupiedSeatClick = function(seatLabel) {
        if (typeof toastr !== 'undefined') {
            toastr.error(`{{ __('This seat is already occupied and cannot be selected.') }} (${seatLabel})`);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: '{{ __("Occupied Seat") }}',
                text: `{{ __("This seat is already occupied and cannot be selected.") }} (${seatLabel})`,
                confirmButtonColor: '#0f4c81'
            });
        } else {
            alert(`{{ __('This seat is already occupied and cannot be selected.') }} (${seatLabel})`);
        }
    };

    window.handleAircraftSeatClick = function(el) {
        const code = $(el).data('code');
        const desc = $(el).data('desc');
        const price = parseFloat($(el).data('price')) || 0;
        toggleSeatSelection(code, desc, price);
    };

    window.toggleSeatSelection = function(code, desc, price) {
        // Is it already taken by a companion?
        let takenByOther = false;
        let otherPaxName = '';
        Object.keys(selectedSeatsByPax).forEach(paxId => {
            if (paxId != currentSeatPaxIndex && selectedSeatsByPax[paxId] && selectedSeatsByPax[paxId].code === code) {
                takenByOther = true;
                otherPaxName = `{{ __("Passenger") }} ${parseInt(paxId, 10) + 1}`;
            }
        });
        if (takenByOther) {
            if (typeof toastr !== 'undefined') {
                toastr.warning(`{{ __('Seat already selected by another passenger in your group.') }} (${otherPaxName})`);
            }
            return;
        }

        // Toggle selection for current pax
        if (selectedSeatsByPax[currentSeatPaxIndex] && selectedSeatsByPax[currentSeatPaxIndex].code === code) {
            delete selectedSeatsByPax[currentSeatPaxIndex]; // Deselect
        } else {
            selectedSeatsByPax[currentSeatPaxIndex] = { code, description: desc, price: parseFloat(price) || 0 }; // Select
            
            // Auto advance to next passenger if available
            const extraContainers = $('[id^="passenger-extras-"]');
            const maxPax = extraContainers.length > 0 ? extraContainers.length : totalPax;
            if (currentSeatPaxIndex < maxPax - 1) {
                currentSeatPaxIndex++;
            }
        }
        
        buildSeatPaxList();
        buildAircraftLayout();
    };

    function updateSeatMapTotal() {
        let total = 0;
        Object.values(selectedSeatsByPax).forEach(seat => {
            total += parseFloat(seat.price) || 0;
        });
        $('#seatMapTotal').text(new Intl.NumberFormat().format(total));
    }

    window.confirmSeatSelection = function() {
        $('.global-seat-selections-input').val(JSON.stringify(selectedSeatsByPax));
        
        // Synchronize selected seats with standard radio cards
        Object.entries(selectedSeatsByPax).forEach(([paxId, seat]) => {
            const radio = $(`input[name*="seat_"][name*="pax${paxId}"][value="${seat.code}"]`);
            if (radio.length) {
                radio.prop('checked', true);
            }
        });

        updateExtraTotal();
        closeVisualSeatMap();
        
        if (typeof toastr !== 'undefined') {
            toastr.success('{{ __("Seats confirmed successfully") }}');
        }
    };

    window.handleSeatCardSelection = function(paxIdx, elOrCode, maybeDesc, maybePrice) {
        let code, desc, price;
        if (elOrCode && typeof elOrCode === 'object' && ('value' in elOrCode || elOrCode.nodeType)) {
            code = $(elOrCode).val();
            desc = $(elOrCode).data('desc') || '';
            price = parseFloat($(elOrCode).data('price')) || 0;
        } else {
            code = elOrCode;
            desc = maybeDesc || '';
            price = parseFloat(maybePrice) || 0;
        }

        if (!code) {
            delete selectedSeatsByPax[paxIdx];
        } else {
            selectedSeatsByPax[paxIdx] = { code, description: desc, price: parseFloat(price) || 0 };
        }
        $('.global-seat-selections-input').val(JSON.stringify(selectedSeatsByPax));
        updateExtraTotal();
    };

    // ═══ PASSPORT OCR LOGIC WITH MODAL & CROPPER ═══
    let currentUploadIndex = null;
    let currentModalFile = null;
    let cropper = null;

    window.openPassportModal = function(index) {
        currentUploadIndex = index;
        resetModalState();
        const modal = document.getElementById('passportUploadModal');
        modal.classList.remove('d-none');
        setTimeout(() => modal.classList.add('show'), 10);
        document.body.style.overflow = 'hidden';
    };

    window.closePassportModal = function() {
        const modal = document.getElementById('passportUploadModal');
        modal.classList.remove('show');
        setTimeout(() => {
            modal.classList.add('d-none');
            document.body.style.overflow = '';
            if(cropper) {
                cropper.destroy();
                cropper = null;
            }
        }, 300);
    };

    function resetModalState() {
        currentModalFile = null;
        if(cropper) {
            cropper.destroy();
            cropper = null;
        }
        $('#modalFileInput').val('');
        $('#modalImageToCrop').attr('src', '');
        $('#modalDropArea').removeClass('d-none');
        $('#modalCropperArea').addClass('d-none');
        $('#btnChangeImage').hide();
        $('#btnConfirmUpload').prop('disabled', true).html('{{ __("Confirm & Scan") }} <i class="fas fa-magic ms-1"></i>');
    }

    // Handle Drag & Drop on Modal
    const dropArea = document.getElementById('modalDropArea');
    dropArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropArea.style.borderColor = 'var(--primary)';
        dropArea.style.background = '#f0f9ff';
    });
    dropArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropArea.style.borderColor = '#cbd5e1';
        dropArea.style.background = 'white';
    });
    dropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        dropArea.style.borderColor = '#cbd5e1';
        dropArea.style.background = 'white';
        if(e.dataTransfer.files.length) {
            handleModalFile(e.dataTransfer.files[0]);
        }
    });
    dropArea.addEventListener('click', () => {
        $('#modalFileInput').click();
    });
    $('#modalFileInput').on('change', function() {
        if(this.files.length) {
            handleModalFile(this.files[0]);
        }
    });

    function handleModalFile(file) {
        currentModalFile = file;
        $('#modalDropArea').addClass('d-none');
        $('#btnChangeImage').show();
        $('#btnConfirmUpload').prop('disabled', false);

        $('#modalCropperArea').removeClass('d-none').html('<div style="max-height: 50vh; display:flex; justify-content:center; background: #000; border-radius: 10px; overflow: hidden;"><img id="modalImageToCrop" src="" style="max-width: 100%;"></div><p class="text-muted text-center mt-3 mb-0" style="font-size: 0.85rem;"><i class="fas fa-crop-alt"></i> {{ __("Adjust the frame to focus tightly on the passport data page for optimal AI scanning.") }}</p>');
        
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = document.getElementById('modalImageToCrop');
            img.src = event.target.result;
            cropper = new Cropper(img, {
                viewMode: 2,
                autoCropArea: 1,
                responsive: true,
                background: false
            });
        };
        reader.readAsDataURL(file);
    }

    $('#btnChangeImage').on('click', function() {
        resetModalState();
    });

    $('#btnConfirmUpload').on('click', function() {
        if(!currentModalFile) return;

        const btn = $(this);
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> {{ __("Processing...") }}').prop('disabled', true);

        if(!cropper) return;
        cropper.getCroppedCanvas({ maxWidth: 2000, maxHeight: 2000 }).toBlob(function(blob) {
            let originalName = currentModalFile.name;
            if(!originalName.toLowerCase().endsWith('.jpg') && !originalName.toLowerCase().endsWith('.jpeg') && !originalName.toLowerCase().endsWith('.png')) {
                originalName += '.jpg';
            }
            const croppedFile = new File([blob], originalName, { type: 'image/jpeg' });
            
            commitFile(croppedFile);
            
            btn.html(originalText).prop('disabled', false);
            closePassportModal();
        }, 'image/jpeg', 0.85);
    });

    function commitFile(file) {
        const hiddenInput = document.getElementById('hidden_passport_input_' + currentUploadIndex);
        const dt = new DataTransfer();
        dt.items.add(file);
        hiddenInput.files = dt.files;

        // Update UI
        const dropzone = $('#passport_dropzone_' + currentUploadIndex);
        dropzone.addClass('has-file');
        dropzone.find('.success-indicator').removeClass('d-none');
        $('#file_name_display_' + currentUploadIndex).text(file.name);

        processOcr(hiddenInput, file, currentUploadIndex);
    }

    function processOcr(inputElement, fileBlob, index) {
        const loader = $('#ai-loading-' + index);
        loader.removeClass('d-none').show();
        
        $('#scan_error_' + index).remove();
        
        const formData = new FormData();
        const fileName = (fileBlob.name) ? fileBlob.name : "passport_cropped.jpg";
        formData.append('passport_image', fileBlob, fileName);
        formData.append('_token', '{{ csrf_token() }}');
        
        $.ajax({
            url: '{{ route("ocr.passport") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    
                    if (data.error) {
                        Swal.fire({ icon: 'error', title: '{{ __("Scan Failed") }}', text: data.error, confirmButtonColor: '#0ea5e9' });
                        
                        $('#hidden_passport_input_' + index).val('');
                        const dropzone = $('#passport_dropzone_' + index);
                        dropzone.removeClass('has-file');
                        dropzone.find('.success-indicator').addClass('d-none');
                        
                        $('#scan_error_' + index).remove();
                        const errorHtml = `
                        <div id="scan_error_${index}" class="mt-3 d-flex align-items-center" style="border-radius: 12px; background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 16px; box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.1); animation: fadeIn 0.3s ease;">
                            <i class="fas fa-exclamation-triangle fa-lg" style="color: #ef4444; margin-inline-end: 12px;"></i>
                            <div style="text-align: start;">
                                <div style="font-weight: 800; font-size: 0.95rem; margin-bottom: 2px;">{{ __("Scan Failed") }}</div>
                                <div style="font-weight: 500; font-size: 0.85rem; opacity: 0.9;">${data.error}</div>
                            </div>
                        </div>`;
                        dropzone.after(errorHtml);
                        return;
                    }

                    // Fill text fields & list spans
                    if (data.first_name) {
                        $('input[name="passengers['+index+'][first_name]"]').val(data.first_name);
                        $('#hidden_first_name_'+index).val(data.first_name);
                        $('.ai-display-first_name-'+index).text(data.first_name);
                    }
                    if (data.last_name) {
                        $('input[name="passengers['+index+'][last_name]"]').val(data.last_name);
                        $('#hidden_last_name_'+index).val(data.last_name);
                        $('.ai-display-last_name-'+index).text(data.last_name);
                    }
                    if (data.passport_no) {
                        $('input[name="passengers['+index+'][passport_no]"]').val(data.passport_no);
                        $('#hidden_passport_number_'+index).val(data.passport_no);
                        $('.ai-display-passport_no-'+index).text(data.passport_no);
                    }
                    
                    // Fill date fields
                    if (data.dob) {
                        const dobInput = $('input[name="passengers['+index+'][dob]"]')[0];
                        if (dobInput && dobInput._flatpickr) {
                            dobInput._flatpickr.setDate(data.dob);
                        } else {
                            $('input[name="passengers['+index+'][dob]"]').val(data.dob);
                        }
                        $('#hidden_dob_'+index).val(data.dob);
                        $('.ai-display-dob-'+index).text(data.dob);
                    }
                    let isExpiredPassport = false;
                    if (data.passport_expiry_date) {
                        let expDate = new Date(data.passport_expiry_date);
                        let today = new Date();
                        today.setHours(0, 0, 0, 0);

                        if (!isNaN(expDate.getTime()) && expDate <= today) {
                            isExpiredPassport = true;
                            // Do not accept expired date into field or hidden input
                            const expInput = $('input[name="passengers['+index+'][passport_expiry_date]"]')[0];
                            if (expInput) {
                                if (expInput._flatpickr) {
                                    expInput._flatpickr.clear();
                                } else {
                                    $(expInput).val('');
                                }
                                $(expInput).addClass('is-invalid');
                            }
                            $('#hidden_passport_expiry_'+index).val('');
                            $('.ai-display-passport_expiry_date-'+index).html('<span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> ' + data.passport_expiry_date + ' ({{ __("Expired") }})</span>');
                            $('.passport-expiry-error-'+index).show().text('{{ __("The scanned passport is expired. Please enter a valid future expiration date.") }}');
                        } else {
                            const expInput = $('input[name="passengers['+index+'][passport_expiry_date]"]')[0];
                            if (expInput && expInput._flatpickr) {
                                expInput._flatpickr.setDate(data.passport_expiry_date);
                            } else {
                                $('input[name="passengers['+index+'][passport_expiry_date]"]').val(data.passport_expiry_date);
                            }
                            $('#hidden_passport_expiry_'+index).val(data.passport_expiry_date);
                            $('.ai-display-passport_expiry_date-'+index).text(data.passport_expiry_date);
                            if (expInput) {
                                $(expInput).removeClass('is-invalid');
                            }
                            $('.passport-expiry-error-'+index).hide().text('');
                        }
                    }
                    if (data.passport_issue_date) {
                        const issueInput = $('input[name="passengers['+index+'][passport_issue_date]"]')[0];
                        if (issueInput && issueInput._flatpickr) {
                            issueInput._flatpickr.setDate(data.passport_issue_date);
                        } else {
                            $('input[name="passengers['+index+'][passport_issue_date]"]').val(data.passport_issue_date);
                        }
                        $('#hidden_passport_issue_date_'+index).val(data.passport_issue_date);
                        // optional issue date list view text here if it was added
                    }

                    // Fill selects (Nationality & Issue Country)
                    if (data.nationality) {
                        $('input[name="passengers['+index+'][nationality]"]').val(data.nationality.toUpperCase());
                        $('select[name="passengers['+index+'][nationality]"]').val(data.nationality.toUpperCase()).trigger('change');
                        $('#hidden_nationality_'+index).val(data.nationality.toUpperCase());
                        $('.ai-display-nationality-'+index).text(data.nationality.toUpperCase());
                    }
                    if (data.passport_issue_country) {
                        $('input[name="passengers['+index+'][passport_issue_country]"]').val(data.passport_issue_country.toUpperCase());
                        $('select[name="passengers['+index+'][passport_issue_country]"]').val(data.passport_issue_country.toUpperCase()).trigger('change');
                        $('#hidden_passport_issue_country_'+index).val(data.passport_issue_country.toUpperCase());
                        $('.ai-display-passport_issue_country-'+index).text(data.passport_issue_country.toUpperCase());
                    }

                    // Map gender to title
                    if (data.gender) {
                        let gender = data.gender.toUpperCase().trim();
                        let type = $('input[name="passengers['+index+'][type]"]').val();
                        let title = '';
                        
                        if (gender === 'M' || gender === 'MALE' || gender === 'ذكر' || gender === 'ذ') {
                            title = type === 'adult' ? 'Mr' : 'Master';
                        } else if (gender === 'F' || gender === 'FEMALE' || gender === 'أنثى' || gender === 'أ') {
                            title = type === 'adult' ? 'Ms' : 'Miss';
                        }
                        
                        if (title) {
                            $('select[name="passengers['+index+'][title]"]').val(title).trigger('change');
                            $('#hidden_title_'+index).val(title);
                            $('.ai-display-title-'+index).text(title);
                        }
                    }
                    
                    if (isExpiredPassport) {
                        let warnMsg = '{{ __("Passport data extracted, but the passport is expired. Please enter a valid future expiration date.") }}' + ' (' + data.passport_expiry_date + ')';
                        if(typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: '{{ __("Passport Expired") }}',
                                text: warnMsg,
                                confirmButtonColor: '#f59e0b',
                                confirmButtonText: '{{ __("OK") }}'
                            });
                        } else if(typeof toastr !== 'undefined') {
                            toastr.warning(warnMsg);
                        } else {
                            alert(warnMsg);
                        }
                    } else {
                        if(typeof toastr !== 'undefined') {
                            toastr.success('{{ __("Passport data extracted successfully.") }}');
                        } else if(typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: '{{ __("Passport data extracted successfully.") }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                        } else {
                            alert('{{ __("Passport data extracted successfully.") }}');
                        }
                    }
                } else {
                    if(typeof toastr !== 'undefined') {
                        toastr.warning('{{ __("Could not extract data perfectly. Please fill manually.") }}');
                    } else if(typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'warning', title: '{{ __("Warning") }}', text: '{{ __("Could not extract data perfectly. Please fill manually.") }}', confirmButtonColor: '#f59e0b' });
                    } else {
                        alert('{{ __("Could not extract data perfectly. Please fill manually.") }}');
                    }
                }
            },
            error: function(xhr) {
                let errorMsg = '{{ __("Could not extract passport data. Please upload a clear photo or enter details manually.") }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }

                if(typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("Scan Notice") }}',
                        html: errorMsg,
                        confirmButtonColor: '#0ea5e9',
                        confirmButtonText: '{{ __("OK") }}'
                    });
                } else if(typeof toastr !== 'undefined') {
                    toastr.error(errorMsg);
                } else {
                    alert(errorMsg);
                }
                
                // Clear the input and show visual error
                $('#hidden_passport_input_' + index).val('');
                const dropzone = $('#passport_dropzone_' + index);
                dropzone.removeClass('has-file');
                dropzone.find('.success-indicator').addClass('d-none');
                
                // Remove existing error if any
                $('#scan_error_' + index).remove();
                // Append error message
                const errorHtml = `
                <div id="scan_error_${index}" class="mt-3 d-flex align-items-center" style="border-radius: 12px; background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 16px; box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.1); animation: fadeIn 0.3s ease;">
                    <i class="fas fa-exclamation-triangle fa-lg" style="color: #ef4444; margin-inline-end: 12px; flex-shrink: 0;"></i>
                    <div style="text-align: start;">
                        <div style="font-weight: 800; font-size: 0.95rem; margin-bottom: 2px;">{{ __("Scan Notice") }}</div>
                        <div style="font-weight: 500; font-size: 0.85rem; opacity: 0.9;">${errorMsg}</div>
                    </div>
                </div>`;
                dropzone.after(errorHtml);
            },
            complete: function() {
                loader.addClass('d-none').hide();
            }
        });
    }

    // ═══ DATE PICKERS ═══
    // Initialize Select2 for Countries (legacy — keep for pax-fields)
    if ($.fn.select2) {
        $('.fe-select2').select2({
            dir: '{{ app()->getLocale() == "ar" ? "rtl" : "ltr" }}',
            placeholder: '{{ __("Select Country") }}',
            allowClear: true,
            width: '100%'
        });
    }

    // Initialize Auto-Save & Auto-Restore for Flight Draft
    if (typeof BookingDraftManager !== 'undefined') {
        window.flightDraftManager = new BookingDraftManager('#flightBookingForm', 'flight_{{ md5(url()->full()) }}');
    }

    // Initialize Flatpickr for Birthday - Adult (> 12y)
    flatpickr(".dob-picker-adult", {
        dateFormat: "Y-m-d",
        maxDate: new Date().setFullYear(new Date().getFullYear() - 12),
        defaultDate: new Date().setFullYear(new Date().getFullYear() - 30),
        locale: "{{ app()->getLocale() }}",
    });

    // Initialize Flatpickr for Birthday - Child (2y to 12y)
    flatpickr(".dob-picker-child", {
        dateFormat: "Y-m-d",
        minDate: new Date().setFullYear(new Date().getFullYear() - 12),
        maxDate: new Date().setFullYear(new Date().getFullYear() - 2),
        locale: "{{ app()->getLocale() }}",
    });

    // Initialize Flatpickr for Birthday - Infant (< 2y)
    flatpickr(".dob-picker-infant", {
        dateFormat: "Y-m-d",
        minDate: new Date().setFullYear(new Date().getFullYear() - 2),
        maxDate: "today",
        locale: "{{ app()->getLocale() }}",
    });

    // Initialize Flatpickr for Passport Expiry (Strictly future dates only)
    const minExpiryDate = new Date();
    minExpiryDate.setDate(minExpiryDate.getDate() + 1);
    const minExpiryStr = minExpiryDate.toISOString().split('T')[0];

    flatpickr(".expiry-picker", {
        dateFormat: "Y-m-d",
        minDate: minExpiryStr,
        disableMobile: "true",
        locale: "{{ app()->getLocale() }}",
        onChange: function(selectedDates, dateStr, instance) {
            validateSingleExpiry($(instance.element));
        },
        onClose: function(selectedDates, dateStr, instance) {
            validateSingleExpiry($(instance.element));
        }
    });

    function validateSingleExpiry($input) {
        if (!$input || !$input.length) return true;
        let val = $input.val().trim();
        let nameAttr = $input.attr('name') || '';
        let match = nameAttr.match(/passengers\[(\d+)\]/);
        let idx = match ? match[1] : '';
        let $err = idx !== '' ? $('.passport-expiry-error-' + idx) : $input.siblings('.invalid-feedback');

        if (!val) {
            $input.removeClass('is-invalid');
            if ($err.length) $err.hide().text('');
            return true;
        }

        let inputDate = new Date(val);
        let today = new Date();
        today.setHours(0, 0, 0, 0);

        if (isNaN(inputDate.getTime()) || inputDate <= today) {
            $input.addClass('is-invalid');
            let msg = '{{ __("Passport expiry date must be in the future and cannot be in the past.") }}';
            if ($err.length) {
                $err.text(msg).show();
            }
            if (idx !== '') {
                $('#hidden_passport_expiry_' + idx).val('');
            }
            return false;
        } else {
            $input.removeClass('is-invalid');
            if ($err.length) $err.hide().text('');
            if (idx !== '') {
                $('#hidden_passport_expiry_' + idx).val(val);
                $('.ai-display-passport_expiry_date-' + idx).text(val);
            }
            return true;
        }
    }

    $(document).on('change input blur', '.expiry-picker', function() {
        validateSingleExpiry($(this));
    });

    // Initialize Flatpickr for Passport Issue Date (Past only)
    flatpickr(".issue-date-picker", {
        dateFormat: "Y-m-d",
        maxDate: "today",
        disableMobile: "true",
        locale: "{{ app()->getLocale() }}",
    });

    // Helper to display submit error
    function showSubmitError(msg) {
        let errorContainer = $('#form-submit-error');
        if (errorContainer.length === 0) {
            $('.fe-booking-action').prepend('<div id="form-submit-error" class="alert alert-danger" style="border-radius: 12px; background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 16px; margin-bottom: 15px; text-align: center; font-weight: 700; box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.1);"><i class="fas fa-exclamation-circle me-2"></i> <span>' + msg + '</span></div>');
        } else {
            errorContainer.find('span').text(msg);
            errorContainer.addClass('shake');
            setTimeout(function() {
                errorContainer.removeClass('shake');
            }, 500);
        }
        $('html, body').animate({
            scrollTop: $('.fe-booking-action').offset().top - 100
        }, 500);
    }

    // Form Validation before submit (especially for AI extracted data & expired passport prevention)
    $('#flightBookingForm').on('submit', function(e) {
        let allowManualEdit = "{{ \App\Models\Setting::get('allow_manual_passport_edit', '1') }}";
        let totalPax = {{ $totalPax }};
        let isPassportMandatory = "{{ $details['IsPassportMandatory'] ?? 'false' }}" === "true" || "{{ $details['IsPassportMandatory'] ?? 'false' }}" === "1";
        let today = new Date();
        today.setHours(0, 0, 0, 0);

        let flightDepartStr = "{{ $details['departDate'] ?? '' }}";
        let flightDepartDate = flightDepartStr ? new Date(flightDepartStr) : null;
        if (flightDepartDate) flightDepartDate.setHours(0, 0, 0, 0);

        // 1. Validate Passport Expiry Date for ALL passengers (cannot be in past/expired)
        for (let i = 0; i < totalPax; i++) {
            let expInput = $('input[name="passengers[' + i + '][passport_expiry_date]"]');
            let expVal = (expInput.length && expInput.val() ? expInput.val() : $('#hidden_passport_expiry_' + i).val()) || '';
            expVal = expVal.trim();

            if (isPassportMandatory && !expVal) {
                e.preventDefault();
                showSubmitError('{{ __("Please enter the passport expiry date for passenger") }} #' + (i + 1));
                if (expInput.length && expInput.is(':visible')) {
                    expInput.addClass('is-invalid').focus();
                }
                return false;
            }

            if (expVal) {
                let expDate = new Date(expVal);
                expDate.setHours(0, 0, 0, 0);

                if (isNaN(expDate.getTime()) || expDate <= today) {
                    e.preventDefault();
                    let errMsg = '{{ __("Passport expiry date for passenger") }} #' + (i + 1) + ' (' + expVal + ') {{ __("is expired. Please enter a valid future date.") }}';
                    showSubmitError(errMsg);
                    if (expInput.length && expInput.is(':visible')) {
                        expInput.addClass('is-invalid').focus();
                    }
                    return false;
                }

                if (flightDepartDate && expDate < flightDepartDate) {
                    e.preventDefault();
                    let errMsg = '{{ __("Passport for passenger") }} #' + (i + 1) + ' (' + expVal + ') {{ __("expires before the flight departure date") }} (' + flightDepartStr + '). {{ __("Please enter a valid passport date.") }}';
                    showSubmitError(errMsg);
                    if (expInput.length && expInput.is(':visible')) {
                        expInput.addClass('is-invalid').focus();
                    }
                    return false;
                }
            }
        }

        if (allowManualEdit !== '1') {
            let isValid = true;
            let totalPax = {{ $totalPax }};
            
            for (let i = 0; i < totalPax; i++) {
                let requiredFields = [
                    '#hidden_first_name_' + i,
                    '#hidden_last_name_' + i,
                    '#hidden_dob_' + i,
                    '#hidden_nationality_' + i
                ];
                
                let isPassportMandatory = "{{ $details['IsPassportMandatory'] ?? 'false' }}" === "true" || "{{ $details['IsPassportMandatory'] ?? 'false' }}" === "1";
                if (isPassportMandatory) {
                    requiredFields.push('#hidden_passport_number_' + i);
                    requiredFields.push('#hidden_passport_expiry_' + i);
                    requiredFields.push('#hidden_passport_issue_country_' + i);
                }

                for (let j = 0; j < requiredFields.length; j++) {
                    let field = $(requiredFields[j]);
                    if (field.length > 0 && !field.val().trim()) {
                        isValid = false;
                        break;
                    }
                }
                
                if (!isValid) break;
            }
            
            if (!isValid) {
                e.preventDefault();
                
                // Show error message above the submit button
                let errorContainer = $('#form-submit-error');
                if (errorContainer.length === 0) {
                    $('.fe-booking-action').prepend('<div id="form-submit-error" class="alert alert-danger" style="border-radius: 12px; background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 16px; margin-bottom: 15px; text-align: center; font-weight: 700; box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.1);"><i class="fas fa-exclamation-circle me-2"></i> {{ __("Please ensure all passenger data is filled out by uploading clear passport images for all passengers.") }}</div>');
                    
                    // Scroll to the error message
                    $('html, body').animate({
                        scrollTop: $('.fe-booking-action').offset().top - 100
                    }, 500);
                } else {
                    // Shake effect if already visible
                    errorContainer.addClass('shake');
                    setTimeout(function() {
                        errorContainer.removeClass('shake');
                    }, 500);
                }
            }
        }
    });

    // Fetch Extra Services on page load
    fetchExtraServices();

    // ═══ PAGE TIME LIMIT / SESSION COUNTDOWN TIMER ═══
    (function initFlightPageTimer() {
        const timerDisplay = document.getElementById('flightPageTimerDisplay');
        if (!timerDisplay) return;

        const sessionDuration = 15 * 60; // 15 minutes (900 seconds)
        const storageKey = 'flight_active_timer_start';
        const sessionKey = 'flight_timer_session_id';
        const currentSessionId = "{{ $details['session_id'] ?? '' }}";
        
        let storedSessionId = sessionStorage.getItem(sessionKey);
        let startTime = sessionStorage.getItem(storageKey);

        // Reset timer if:
        // 1. No startTime exists in storage.
        // 2. A new search session_id is detected (different flight selected).
        // 3. Stored timer is already older than 15 minutes on fresh page load.
        const isStaleOrExpired = !startTime 
            || (currentSessionId && storedSessionId && storedSessionId !== currentSessionId)
            || (Date.now() - parseInt(startTime, 10) > sessionDuration * 1000);

        if (isStaleOrExpired) {
            startTime = Date.now();
            sessionStorage.setItem(storageKey, startTime);
            if (currentSessionId) {
                sessionStorage.setItem(sessionKey, currentSessionId);
            }
        } else {
            startTime = parseInt(startTime, 10);
            if (isNaN(startTime)) {
                startTime = Date.now();
                sessionStorage.setItem(storageKey, startTime);
            }
        }

        function updateTimer() {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            const remaining = Math.max(0, sessionDuration - elapsed);

            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;

            timerDisplay.textContent = (minutes < 10 ? '0' + minutes : minutes) + ':' + (seconds < 10 ? '0' + seconds : seconds);

            if (remaining <= 60) {
                timerDisplay.style.background = 'rgba(239, 68, 68, 0.4)';
                timerDisplay.style.color = '#fff';
            }

            if (remaining <= 0) {
                if (window.__flightPageTimerInterval) clearInterval(window.__flightPageTimerInterval);
                sessionStorage.removeItem(storageKey);
                sessionStorage.removeItem(sessionKey);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ app()->getLocale() == "ar" ? "انتهت مهلة الجلسة" : "Session Expired" }}',
                        text: '{{ app()->getLocale() == "ar" ? "انتهت مهلة الجلسة المحددة بـ 15 دقيقة لضمان المقاعد والأسعار. يرجى إعادة البحث لتحديث الأسعار." : "Your 15-minute booking session has expired. Please search again to revalidate fares and seat availability." }}',
                        confirmButtonText: '{{ app()->getLocale() == "ar" ? "إعادة البحث" : "Search Again" }}',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then(() => {
                        window.location.href = "{{ route('flights') }}";
                    });
                } else {
                    alert('{{ app()->getLocale() == "ar" ? "انتهت مهلة الجلسة. يرجى إعادة البحث." : "Session expired. Please search again." }}');
                    window.location.href = "{{ route('flights') }}";
                }
            }
        }

        updateTimer();
        if (window.__flightPageTimerInterval) clearInterval(window.__flightPageTimerInterval);
        window.__flightPageTimerInterval = setInterval(updateTimer, 1000);
    })();

    // ═══ FARE RULES MODAL LOGIC ═══
    window.closeFareRulesModal = function() {
        const m = document.getElementById('globalFareRulesModal');
        if (m) {
            m.classList.remove('show');
            setTimeout(() => m.classList.add('d-none'), 200);
        }
    };

    // Close on backdrop click
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('globalFareRulesModal');
        if (modal && e.target === modal) {
            closeFareRulesModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFareRulesModal();
        }
    });

    $(document).on('click', '.btn-fare-rules-trigger', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const btn = $(this);
        const cardData = {
            sessionId: btn.data('session'),
            fareSource: btn.data('fare-source'),
            inbound: btn.data('inbound') || '',
            airline: btn.data('airline') || '',
            airlineName: btn.data('airline-name') || btn.data('airline') || '',
            from: btn.data('from') || '',
            to: btn.data('to') || '',
            refundable: btn.data('refundable'),
            changeAllowed: btn.data('change-allowed'),
            changePenalty: btn.data('change-penalty') || '',
            refundAllowed: btn.data('refund-allowed'),
            refundPenalty: btn.data('refund-penalty') || '',
            baggage: btn.data('baggage') || '',
            cabinBaggage: btn.data('cabin-baggage') || '',
            fareBasis: btn.data('fare-basis') || ''
        };

        const modal = document.getElementById('globalFareRulesModal');
        if (!modal) return;

        $('#fareRulesAirlineBadge').text(cardData.airline || '{{ __("Airline") }}');
        let subText = cardData.airlineName || cardData.airline || '';
        if (cardData.from && cardData.to) {
            subText += ' (' + cardData.from + ' ➔ ' + cardData.to + ')';
        }
        subText += ' - {{ __("Official terms, penalties & cancellation rules") }}';
        $('#fareRulesAirlineSubtitle').text(subText);

        $('#fareRulesModalLoader').show();
        $('#fareRulesModalBody').hide().empty();
        $('#fareRulesModalError').hide();

        modal.classList.remove('d-none');
        setTimeout(() => modal.classList.add('show'), 10);

        $.ajax({
            url: '{{ route("flights.fare-rules") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                session_id: cardData.sessionId,
                fare_source_code: cardData.fareSource,
                fare_source_code_inbound: cardData.inbound
            },
            success: function(res) {
                $('#fareRulesModalLoader').hide();
                if (res && res.success) {
                    if (res.new_fare_source_code) {
                        btn.data('fare-source', res.new_fare_source_code);
                        btn.attr('data-fare-source', res.new_fare_source_code);
                    }
                    renderFareRulesContent(res, cardData);
                } else {
                    const msg = (res && res.message) ? res.message : '{{ __("Unable to retrieve fare rules from the airline provider for this flight.") }}';
                    $('#fareRulesErrorDesc').text(msg);
                    $('#fareRulesModalError').show();
                }
            },
            error: function() {
                $('#fareRulesModalLoader').hide();
                $('#fareRulesErrorDesc').text('{{ __("Unable to retrieve fare rules from the airline provider for this flight.") }}');
                $('#fareRulesModalError').show();
            }
        });
    });

    function renderFareRulesContent(res, cardData) {
        const container = $('#fareRulesModalBody');
        container.empty();

        const isRefundable = (cardData.refundable === 'Yes' || cardData.refundAllowed === true || cardData.refundAllowed === 'true');
        const isNonRefundable = (cardData.refundable === 'No' || cardData.refundAllowed === false || cardData.refundAllowed === 'false');
        const isChangeable = (cardData.changeAllowed === true || cardData.changeAllowed === 'true');
        const isNonChangeable = (cardData.changeAllowed === false || cardData.changeAllowed === 'false');

        // 1. Policy Overview Cards (Strictly based on Travelopro API search results)
        let summaryHtml = `
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 22px;">
                <!-- Refundability Card -->
                <div style="background: ${isRefundable ? '#f0fdf4' : (isNonRefundable ? '#fff7ed' : '#f8fafc')}; border: 1.5px solid ${isRefundable ? '#bbf7d0' : (isNonRefundable ? '#fed7aa' : '#e2e8f0')}; border-radius: 12px; padding: 14px; display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: ${isRefundable ? '#22c55e' : (isNonRefundable ? '#ea580c' : '#64748b')}; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                        <i class="fas ${isRefundable ? 'fa-check-circle' : (isNonRefundable ? 'fa-times-circle' : 'fa-info-circle')}"></i>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 0.88rem; color: ${isRefundable ? '#15803d' : (isNonRefundable ? '#9a3412' : '#334155')};">
                            ${isRefundable ? '{{ __("Refundable Ticket") }}' : (isNonRefundable ? '{{ __("Non-Refundable Ticket") }}' : '{{ __("Refund Policy") }}')}
                        </div>
                        <div style="font-size: 0.76rem; color: ${isRefundable ? '#166534' : (isNonRefundable ? '#c2410c' : '#64748b')}; font-weight: 600;">
                            ${isRefundable ? (cardData.refundPenalty && cardData.refundPenalty !== '0.00' ? ('{{ __("Penalty Fee:") }} ' + cardData.refundPenalty + ' SAR') : '{{ __("Subject to airline conditions") }}') : (isNonRefundable ? '{{ __("No refund on cancellation") }}' : '{{ __("Subject to airline conditions") }}')}
                        </div>
                    </div>
                </div>

                <!-- Change / Reschedule Card -->
                <div style="background: ${isChangeable ? '#eff6ff' : (isNonChangeable ? '#fff7ed' : '#f8fafc')}; border: 1.5px solid ${isChangeable ? '#bfdbfe' : (isNonChangeable ? '#fed7aa' : '#e2e8f0')}; border-radius: 12px; padding: 14px; display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: ${isChangeable ? '#2563eb' : (isNonChangeable ? '#ea580c' : '#64748b')}; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                        <i class="fas ${isChangeable ? 'fa-calendar-alt' : (isNonChangeable ? 'fa-calendar-times' : 'fa-calendar')}"></i>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 0.88rem; color: ${isChangeable ? '#1d4ed8' : (isNonChangeable ? '#9a3412' : '#334155')};">
                            ${isChangeable ? '{{ __("Flight Changes Permitted") }}' : (isNonChangeable ? '{{ __("Changes Restricted") }}' : '{{ __("Flight Changes") }}')}
                        </div>
                        <div style="font-size: 0.76rem; color: ${isChangeable ? '#1e40af' : (isNonChangeable ? '#c2410c' : '#64748b')}; font-weight: 600;">
                            ${isChangeable ? (cardData.changePenalty && cardData.changePenalty !== '0.00' ? ('{{ __("Change Fee:") }} ' + cardData.changePenalty + ' SAR') : '{{ __("Subject to airline conditions") }}') : (isNonChangeable ? '{{ __("Non-changeable ticket") }}' : '{{ __("Subject to airline conditions") }}')}
                        </div>
                    </div>
                </div>

                <!-- Baggage Card -->
                <div style="background: #f0fdfa; border: 1.5px solid #99f6e4; border-radius: 12px; padding: 14px; display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #0d9488; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                        <i class="fas fa-suitcase-rolling"></i>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 0.88rem; color: #0f766e;">
                            {{ __("Baggage Allowance") }}: ${cardData.baggage || '{{ __("Per Airline Policy") }}'}
                        </div>
                        ${cardData.cabinBaggage ? `
                        <div style="font-size: 0.76rem; color: #115e59; font-weight: 600;">
                            {{ __("Cabin Baggage") }}: ${cardData.cabinBaggage}
                        </div>` : ''}
                    </div>
                </div>
            </div>
        `;
        container.append(summaryHtml);

        // 2. Baggage Infos from Travelopro Provider API (Live response)
        if (res.baggage_infos && res.baggage_infos.length > 0) {
            let bagHtml = `
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 20px;">
                    <div style="font-weight: 800; font-size: 0.9rem; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-plane-departure text-primary"></i>
                        <span>{{ __("Sector Baggage Allowance") }} ({{ __("Official Provider Data") }})</span>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
            `;
            res.baggage_infos.forEach(b => {
                bagHtml += `
                    <div style="background: white; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="font-weight: 800; color: #1e293b; font-size: 0.85rem;">${b.flight_no ? b.flight_no + ': ' : ''}${b.departure} ➔ ${b.arrival}</span>
                        </div>
                        <span class="badge" style="background: #0284c7; color: white; font-weight: 700; font-size: 0.8rem; padding: 4px 8px; border-radius: 6px;">
                            <i class="fas fa-weight-hanging me-1"></i> ${b.baggage || cardData.baggage}
                        </span>
                    </div>
                `;
            });
            bagHtml += `</div></div>`;
            container.append(bagHtml);
        }

        // 3. Airline Tariff Rules from Travelopro Provider API (Live response)
        const validRules = (res.fare_rules || []).filter(r => r.rules && typeof r.rules === 'string' && r.rules.trim().length > 0);
        if (validRules.length > 0) {
            let rulesListHtml = '<div style="display: flex; flex-direction: column; gap: 14px;">';
            validRules.forEach((r, idx) => {
                const title = r.category || r.city_pair || (`{{ __('Policy') }} #${idx + 1}`);
                const rawText = r.rules.trim();
                const formattedHtml = formatFareRuleText(rawText);
                const ruleCardId = 'fareRuleBlock_' + idx;

                rulesListHtml += `
                    <div style="border: 1px solid #cbd5e1; border-radius: 12px; overflow: hidden; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                        <div style="padding: 10px 16px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-weight: 800; font-size: 0.88rem; color: #0f172a;">
                                    <i class="fas fa-file-alt text-primary me-1"></i> ${r.airline ? r.airline + ' - ' : ''}${r.city_pair ? r.city_pair + ' - ' : ''}${title}
                                </span>
                                ${cardData.fareBasis ? `<span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.72rem;">${cardData.fareBasis}</span>` : ''}
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-light border py-1 px-2 btn-copy-fare-rule" data-rule-text="${encodeURIComponent(rawText)}" title="{{ __('Copy Rules') }}" style="font-size: 0.72rem; font-weight: 700; color: #475569;">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Formatted View (Enhanced typography & table structure, 100% original text preserved) -->
                        <div id="${ruleCardId}_formatted" class="fare-rule-view-formatted" style="padding: 16px; font-size: 0.84rem; color: #334155; line-height: 1.7; max-height: 400px; overflow-y: auto;">
                            ${formattedHtml}
                        </div>
                    </div>
                `;
            });
            rulesListHtml += '</div>';
            container.append(rulesListHtml);
        } else {
            // Absolutely NO generic assumed text. State clearly that the airline returned no detailed textual rules.
            container.append(`
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px 20px; text-align: center; color: #64748b; margin-top: 15px;">
                    <i class="fas fa-info-circle text-primary mb-2" style="font-size: 1.6rem;"></i>
                    <div style="font-weight: 700; color: #334155; font-size: 0.9rem; margin-bottom: 4px;">
                        {{ __("No textual fare rules were provided by the airline for this fare.") }}
                    </div>
                    <div style="font-size: 0.8rem; color: #64748b;">
                        {{ __("The carrier did not return detailed rule paragraphs for this fare source.") }}
                    </div>
                </div>
            `);
        }

        container.show();
    }

    // Copy original fare rule text
    $(document).on('click', '.btn-copy-fare-rule', function() {
        const text = decodeURIComponent($(this).data('rule-text') || '');
        if (navigator.clipboard && text) {
            navigator.clipboard.writeText(text);
            const icon = $(this).find('i');
            icon.removeClass('fa-copy far').addClass('fa-check fas text-success');
            setTimeout(() => icon.removeClass('fa-check fas text-success').addClass('far fa-copy'), 2000);
        }
    });

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function formatFareRuleText(rawText) {
        if (!rawText || typeof rawText !== 'string') return '';
        let content = rawText.trim();

        const hasHtmlTags = /<table|<ul|<li|<fieldset|<legend|<br|<p\b|<div\b/i.test(content);

        if (!hasHtmlTags) {
            // Pure plain text: escape HTML entities, convert newlines to <br>
            content = escapeHtml(content).replace(/\r?\n/g, '<br>');
        } else {
            // HTML already present: preserve tags, normalize newlines in text sections
            content = content.replace(/(<\/(?:table|ul|ol|fieldset|legend|div|p|tr|td|th)>)\s*\r?\n/gi, '$1')
                             .replace(/\r?\n\s*(<(?:table|ul|ol|fieldset|legend|div|p|tr|td|th))/gi, '$1')
                             .replace(/\r?\n/g, '<br>');
        }

        // Replace repetitive dashes with a clean divider line
        content = content.replace(/-{8,}/g, '<hr style="border: 0; border-top: 1px dashed #cbd5e1; margin: 12px 0;">');

        // Style "NOTE -" with clean badge
        content = content.replace(/\bNOTE\s*-\s*/gi, '<span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-inline-end: 4px;">NOTE</span> ');

        // Highlight "FAIL TO BOARD FEES"
        content = content.replace(/\b(FAIL TO BOARD FEES\s+SAR\s+\d+)/gi, '<span class="badge" style="background: #fee2e2; color: #b91c1c; font-weight: 800; padding: 2px 7px; border-radius: 4px; font-size: 0.82rem;">$1</span>');

        // Style GDS key-value tags (e.g. Booking Class: A, RateClass: ...)
        content = content.replace(/\b(RateClass|FareClass|marketing Company|Booking Class|Origin|destination|ruleSectionLocalId|ruleCategoryCode)\s*:\s*([^\s<]+)/gi, '<span style="display:inline-flex; align-items:center; gap:4px; background:#f1f5f9; border:1px solid #e2e8f0; padding:2px 7px; border-radius:5px; font-size:0.75rem; margin:2px 3px;"><strong style="color:#64748b;">$1:</strong> <span style="color:#0f172a; font-weight:700;">$2</span></span>');

        return content;
    }
});
</script>
<style>
@keyframes shake {
  0% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  50% { transform: translateX(5px); }
  75% { transform: translateX(-5px); }
  100% { transform: translateX(0); }
}
.shake {
  animation: shake 0.4s;
}

/* Tabs CSS */
.fe-es-tabs {
    display: flex;
    border-bottom: 2px solid var(--gray-200);
    background: var(--gray-50);
}
.fe-es-tab {
    flex: 1;
    padding: 15px 20px;
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    font-weight: 700;
    color: var(--gray-500);
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 1rem;
}
.fe-es-tab:hover {
    color: var(--primary);
    background: rgba(15,76,129,0.05);
}
.fe-es-tab.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
    background: white;
}
.fe-es-tab-content {
    display: none;
}
.fe-es-tab-content.active {
    display: block;
    animation: fadeInTab 0.3s ease;
}
@keyframes fadeInTab {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Visual Seat Map Professional Aircraft CSS */
.seat-map-layout {
    display: flex;
    height: 100%;
    min-height: 580px;
}
.seat-pax-sidebar {
    width: 300px;
    background: #ffffff;
    border-right: 1px solid #e2e8f0;
    padding: 18px;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    flex-shrink: 0;
}
.pax-sidebar-header {
    margin-bottom: 12px;
}
.pax-badge-title {
    font-size: 0.95rem;
    font-weight: 800;
    color: #0f172a;
    display: block;
}
.pax-instruction-hint {
    font-size: 0.74rem;
    color: #64748b;
    display: block;
    margin-top: 2px;
}
.pax-cards-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.seat-pax-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f8fafc;
}
.seat-pax-card:hover {
    border-color: #cbd5e1;
    background: #f1f5f9;
}
.seat-pax-card.active {
    border-color: #0284c7;
    background: #f0f9ff;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15);
}
.pax-card-name {
    font-weight: 700;
    color: #0f172a;
    font-size: 0.85rem;
}
.pax-active-pill {
    display: none;
    font-size: 0.65rem;
    font-weight: 800;
    background: #0284c7;
    color: white;
    padding: 2px 7px;
    border-radius: 12px;
}
.seat-pax-card.active .pax-active-pill {
    display: inline-block;
}
.pax-seat-badge-selected {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #e0f2fe;
    color: #0369a1;
    padding: 5px 8px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    margin-top: 4px;
}
.badge-seat-code {
    display: inline-flex;
    align-items: center;
}
.badge-seat-price {
    font-size: 0.75rem;
}
.pax-seat-badge-empty {
    color: #94a3b8;
    font-size: 0.74rem;
    padding: 4px 0;
}

/* Legend Box */
.seat-legend-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px;
}
.legend-header {
    font-size: 0.82rem;
    color: #334155;
}
.legend-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
}
.legend-item .mock {
    width: 30px;
    height: 32px;
    min-width: 30px;
    font-size: 0.6rem;
    cursor: default;
    box-shadow: none !important;
    transform: none !important;
}
.mock-txt {
    font-size: 0.58rem;
    font-weight: 800;
}
.legend-desc strong {
    display: block;
    font-size: 0.74rem;
    color: #1e293b;
    line-height: 1.1;
}
.legend-desc small {
    display: block;
    font-size: 0.65rem;
    color: #64748b;
    line-height: 1.1;
}
.legend-icon-badge {
    width: 30px;
    height: 32px;
    min-width: 30px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
}
.legend-icon-badge.window {
    background: #e0f2fe;
    color: #0284c7;
    border: 1px solid #bae6fd;
}
.legend-icon-badge.aisle {
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #bbf7d0;
}
.legend-icon-badge.exit {
    background: #fef3c7;
    color: #d97706;
    border: 1px solid #fde68a;
}

/* Architectural Technical Blueprint / Cabin Layout Styles */
.aircraft-container {
    flex: 1;
    background-color: #f8fafc;
    background-image: 
        linear-gradient(to right, rgba(203, 213, 225, 0.5) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(203, 213, 225, 0.5) 1px, transparent 1px);
    background-size: 20px 20px;
    padding: 25px 20px;
    display: flex;
    justify-content: center;
    overflow-y: auto;
    position: relative;
}
.blueprint-canvas {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin: 0 auto;
    min-width: 560px;
    position: relative;
}

/* Blueprint Header Title Block */
.blueprint-meta-bar {
    width: 100%;
    max-width: 520px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    margin-bottom: 16px;
    font-size: 0.78rem;
    color: #334155;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.orientation-tag {
    font-weight: 800;
    color: #0284c7;
    text-transform: uppercase;
    font-size: 0.72rem;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Aircraft Schematic Wrap */
.aircraft-schematic-wrap {
    position: relative;
    display: flex;
    justify-content: center;
    width: 100%;
}

/* Technical Wing Outlines (Schematic Drafting Style) */
.schematic-wing {
    position: absolute;
    top: 36%;
    width: 120px;
    height: 260px;
    pointer-events: none;
    border: 1.5px dashed #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(241, 245, 249, 0.45);
    z-index: 1;
}
.schematic-wing.left-wing {
    right: calc(100% - 2px);
    border-right: none;
    border-radius: 16px 0 0 16px;
    transform: skewY(12deg);
}
.schematic-wing.right-wing {
    left: calc(100% - 2px);
    border-left: none;
    border-radius: 0 16px 16px 0;
    transform: skewY(-12deg);
}
.wing-annotation {
    font-size: 0.65rem;
    font-weight: 800;
    color: #64748b;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    writing-mode: vertical-rl;
    text-orientation: mixed;
    opacity: 0.85;
}

/* Fuselage Technical Frame */
.schematic-fuselage {
    width: 520px;
    background: #ffffff;
    border-radius: 140px 140px 35px 35px;
    border: 2px solid #334155;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
    position: relative;
    padding-bottom: 25px;
    z-index: 2;
}

/* Cockpit Schematic */
.schematic-cockpit {
    padding-top: 22px;
    text-align: center;
    position: relative;
}
.cockpit-nose-curve {
    width: 50px;
    height: 10px;
    border-radius: 20px 20px 0 0;
    background: #f1f5f9;
    border-top: 2px solid #475569;
    margin: 0 auto 10px auto;
}
.cockpit-windshield-outline {
    display: flex;
    justify-content: center;
    gap: 4px;
    margin-bottom: 12px;
}
.cockpit-windshield-outline .glass-frame {
    height: 24px;
    border: 1.5px solid #0284c7;
    background: #e0f2fe;
}
.cockpit-windshield-outline .glass-frame:nth-child(1) { width: 18px; border-radius: 6px 2px 2px 4px; transform: skewY(12deg); }
.cockpit-windshield-outline .glass-frame:nth-child(2) { width: 30px; border-radius: 2px; transform: skewY(4deg); }
.cockpit-windshield-outline .glass-frame:nth-child(3) { width: 30px; border-radius: 2px; transform: skewY(-4deg); }
.cockpit-windshield-outline .glass-frame:nth-child(4) { width: 18px; border-radius: 2px 6px 4px 2px; transform: skewY(-12deg); }
.cockpit-bulkhead-line {
    border-bottom: 1.5px dashed #cbd5e1;
    margin: 0 30px 12px 30px;
    padding-bottom: 5px;
}
.bulkhead-label {
    font-size: 0.68rem;
    font-weight: 800;
    color: #64748b;
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* Amenities & Service Zones */
.schematic-service-zone {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 20px;
    background: #f8fafc;
    border-top: 1.5px solid #e2e8f0;
    border-bottom: 1.5px solid #e2e8f0;
    margin: 0 15px 12px 15px;
    border-radius: 6px;
}
.door-marker {
    background: #059669;
    color: white;
    padding: 2px 7px;
    border-radius: 4px;
    font-weight: 800;
    font-size: 0.65rem;
    letter-spacing: 0.5px;
}
.facility-icon {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 4px;
}
.zone-divider-label {
    font-size: 0.72rem;
    font-weight: 800;
    color: #334155;
    background: #e2e8f0;
    padding: 2px 10px;
    border-radius: 12px;
}

/* Column Headers */
.aircraft-col-headers {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 6px 12px;
    background: #f1f5f9;
    border-radius: 6px;
    margin: 0 15px 10px 15px;
    border: 1px solid #e2e8f0;
}
.col-head-trio {
    display: flex;
    gap: 6px;
}
.col-head-item {
    width: 44px;
    text-align: center;
}
.col-letter {
    display: block;
    font-weight: 900;
    font-size: 0.85rem;
    color: #0f172a;
    line-height: 1;
}
.col-tag {
    display: block;
    font-size: 0.62rem;
    color: #64748b;
    font-weight: 700;
    line-height: 1.1;
    margin-top: 2px;
}
.col-head-aisle {
    width: 44px;
    text-align: center;
}
.aisle-title {
    font-size: 0.65rem;
    font-weight: 800;
    color: #0284c7;
    text-transform: uppercase;
}

/* Cabin Rows */
.aircraft-cabin-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 4px 10px;
    position: relative;
}
.aircraft-cabin-row.is-exit-row {
    background: rgba(16, 185, 129, 0.06);
    border-radius: 6px;
}
.cabin-trio {
    display: flex;
    gap: 6px;
    align-items: center;
}

/* Airplane Windows on Fuselage Wall (Blueprint Notch) */
.cabin-window {
    width: 8px;
    height: 24px;
    border-radius: 4px;
    background: #0284c7;
    border: 1.5px solid #0369a1;
    flex-shrink: 0;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.2);
}

/* Central Aisle Track */
.cabin-aisle-track {
    width: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.row-num-indicator {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    color: #334155;
    font-weight: 800;
    font-size: 0.72rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

/* Seat Boxes (Clean Architectural Drafting Style) */
.seat-box {
    width: 44px;
    height: 50px;
    border-radius: 8px 8px 6px 6px;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    padding: 3px 2px 2px 2px;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    border: 1.5px solid #94a3b8;
    background: #ffffff;
}
.seat-box-empty {
    width: 44px;
    height: 50px;
}
.seat-headrest {
    width: 72%;
    height: 4px;
    border-radius: 2px;
    background: rgba(0,0,0,0.08);
}
.seat-inner-cushion {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    width: 100%;
}
.seat-lbl {
    font-weight: 900;
    font-size: 0.78rem;
    line-height: 1;
}
.seat-cost-tag {
    font-size: 0.6rem;
    font-weight: 800;
    padding: 1px 4px;
    border-radius: 3px;
    line-height: 1;
    background: rgba(0,0,0,0.05);
}
.seat-feature-icon {
    position: absolute;
    top: 2px;
    font-size: 0.58rem;
    opacity: 0.7;
}
.seat-box.is-window .seat-feature-icon {
    left: 3px;
    color: #0284c7;
}
.seat-box.is-aisle .seat-feature-icon {
    right: 3px;
    color: #16a34a;
}

/* Seat States */
.seat-box.available {
    background: #ffffff;
    border-color: #0284c7;
    color: #0f172a;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.seat-box.available:hover {
    background: #f0f9ff;
    border-color: #0369a1;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);
    z-index: 10;
}
.seat-box.selected {
    background: #0284c7 !important;
    border-color: #0369a1 !important;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4) !important;
    z-index: 5;
}
.seat-box.selected .seat-headrest {
    background: rgba(255,255,255,0.3);
}
.seat-box.selected .seat-cost-tag {
    background: rgba(255,255,255,0.25);
    color: white;
}
.selected-check {
    font-size: 0.65rem;
    color: #e0f2fe;
}
.seat-box.companion-selected {
    background: #6366f1 !important;
    border-color: #4f46e5 !important;
    color: white !important;
}
.companion-pax-tag {
    font-size: 0.6rem;
    font-weight: 900;
    background: rgba(255,255,255,0.25);
    padding: 0 3px;
    border-radius: 2px;
}
.seat-box.occupied {
    background: repeating-linear-gradient(45deg, #f8fafc, #f8fafc 4px, #e2e8f0 4px, #e2e8f0 8px) !important;
    border-color: #cbd5e1 !important;
    color: #94a3b8 !important;
    cursor: not-allowed !important;
    opacity: 0.85;
}
.seat-box.occupied:hover {
    transform: none !important;
    box-shadow: none !important;
    border-color: #ef4444 !important;
}
.occupied-x {
    font-size: 0.72rem;
    color: #ef4444;
}

/* Exit Row Banner */
.aircraft-exit-row-banner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ecfdf5;
    border: 1.5px dashed #10b981;
    border-radius: 6px;
    padding: 5px 12px;
    margin: 8px 15px 4px 15px;
    color: #047857;
    font-size: 0.72rem;
    font-weight: 700;
}
.exit-door-pill {
    background: #059669;
    color: white;
    padding: 2px 7px;
    border-radius: 4px;
    font-weight: 800;
    font-size: 0.65rem;
}

/* Tail Schematic Section */
.schematic-tail {
    margin-top: 15px;
    text-align: center;
    position: relative;
}
.tail-datum-line {
    width: 48px;
    height: 10px;
    border-radius: 0 0 20px 20px;
    border-bottom: 2px solid #64748b;
    margin: 0 auto 6px auto;
}
.tail-annotation {
    font-size: 0.68rem;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>
@endpush
