@extends('frontend.layouts.app')

@section('title', __('Complete Your Booking') . ': ' . $trip->title)

@section('content')
<div class="fe-booking-hero">
    <div class="fe-container">
        <div class="fe-booking-badge">
            <i class="fas fa-shield-check"></i> {{ __('Secure Checkout') }}
        </div>
        <h1>{{ __('Finalize Your Journey') }}</h1>
        <p>{{ __('Please provide the traveler details as they appear on official documents.') }}</p>
        
        <div class="fe-steps">
            <div class="fe-step active">
                <div class="fe-step-num">1</div>
                <span>{{ __('Trip Details') }}</span>
            </div>
            <div class="fe-step-line"></div>
            <div class="fe-step current">
                <div class="fe-step-num">2</div>
                <span>{{ __('Travelers') }}</span>
            </div>
            <div class="fe-step-line"></div>
            <div class="fe-step">
                <div class="fe-step-num">3</div>
                <span>{{ __('Payment') }}</span>
            </div>
        </div>
    </div>
</div>

<div class="fe-container fe-booking-wrapper">
    
    @php
        $currLocale = app()->getLocale();
    @endphp

    @guest
        <div class="fe-guest-auth-card mb-4">
            <div class="fe-guest-auth-glow"></div>
            <div class="fe-guest-auth-inner">
                <div class="fe-guest-auth-icon-wrap">
                    <div class="fe-guest-auth-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <span class="fe-guest-pulse"></span>
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
                    <a href="{{ route('login', ['return_url' => url()->full()]) }}" class="fe-btn-guest-login btn-save-draft-and-login" onclick="if(window.tripDraftManager) window.tripDraftManager.saveDraft();">
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
    @endguest

    @if ($errors->any())
        <div class="fe-error-card">
            <div class="fe-error-header"><i class="fas fa-exclamation-triangle"></i> {{ __('Needs Attention') }}</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('book.trip') }}" method="POST" id="tripBookingForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="trip_id" value="{{ $trip->id }}">
        <input type="hidden" name="tickets_count" value="{{ $tickets_count }}">
        <input type="hidden" name="package_id" value="{{ $package_id }}">
        <input type="hidden" name="season_id" value="{{ $season_id }}">
        <input type="hidden" name="occupancy_type" value="{{ $occupancy_type }}">
        <input type="hidden" name="booking_date" value="{{ $booking_date }}">
        <input type="hidden" name="notes" value="{{ $notes ?? '' }}">
        @if(isset($selectedAddons))
            @foreach($selectedAddons as $addon)
                <input type="hidden" name="addons[]" value="{{ $addon->id }}">
            @endforeach
        @endif

        <div class="fe-booking-grid">
            {{-- Main Form Side --}}
            <div class="fe-booking-main">
                
                @for($i = 1; $i <= $tickets_count; $i++)
                    <div class="fe-pax-card" style="--delay: {{ $i * 0.1 }}s">
                        <div class="fe-pax-header">
                            <div class="fe-pax-rank">#{{ $i }}</div>
                            <div class="fe-pax-title">
                                <h3>{{ __('Traveler') }} {{ $i }}</h3>
                                @if($i == 1) <span class="fe-lead-badge">{{ __('Lead Traveler') }}</span> @endif
                            </div>
                        </div>
                        <div class="fe-pax-body">
                            
                            <div class="passport-dropzone mb-3" id="passport_dropzone_{{ $i }}" onclick="openPassportModal({{ $i }})">
                                <div class="dropzone-content">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-primary"></i>
                                    <h5>{{ __('Click or Drag & Drop Passport') }}</h5>
                                    <p class="text-muted mb-0">{{ __('Supports JPG, PNG for AI auto-fill') }}</p>

                                    <div class="success-indicator mt-2" id="success_indicator_{{ $i }}" style="display:none;">
                                        <i class="fas fa-check-circle text-success fa-3x mb-2"></i>
                                        <h6 class="text-success fw-bold mb-0 mt-1" id="file_name_display_{{ $i }}"></h6>
                                        <p class="text-muted small mt-2"><i class="fas fa-pen"></i> {{ __('Click to change') }}</p>
                                    </div>
                                </div>

                                <input type="file" name="passengers[{{ $i }}][passport_image]" id="hidden_passport_input_{{ $i }}" accept="image/*" style="display:none;">

                                <div class="ai-loading-overlay" id="ai-loading-{{ $i }}" style="display:none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 10; border-radius: 14px; align-items: center; justify-content: center;">
                                    <div class="text-center">
                                        <i class="fas fa-circle-notch fa-spin fa-3x mb-3" style="color: var(--primary);"></i>
                                        <div style="font-weight: 800; color: var(--dark); font-size: 1.1rem;">{{ __('Scanning AI Data...') }}</div>
                                        <p class="text-muted small mt-1">{{ __('Extracting your details automatically') }}</p>
                                    </div>
                                </div>
                            </div>

                            @if(\App\Models\Setting::get('allow_manual_passport_edit', '1') == '1')
                            
                            <div class="text-center mb-3" style="position: relative; border-bottom: 1px solid #e2e8f0; margin-top: -10px;">
                                <span style="background: #fff; padding: 0 15px; font-size: 0.85rem; color: #64748b; position: relative; top: 10px;">{{ __('Or enter details manually') }}</span>
                            </div>

                            <div class="fe-form-row mt-4">
                                <div class="fe-input-group" style="max-width: 200px;">
                                    <label>{{ __('Title') }}</label>
                                    <div class="fe-input-icon">
                                        <i class="fas fa-user-tag"></i>
                                        <select name="passengers[{{ $i }}][title]" id="title_select_{{ $i }}" class="fe-input fe-select2" style="width: 100%;">
                                            <option value="Mr">{{ __('Mr') }}</option>
                                            <option value="Mrs">{{ __('Mrs') }}</option>
                                            <option value="Ms">{{ __('Ms') }}</option>
                                            <option value="Master">{{ __('Master') }}</option>
                                            <option value="Miss">{{ __('Miss') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="fe-input-row">
                                <div class="fe-input-group">
                                    <label>{{ __('First Name') }} <span>*</span></label>
                                    <div class="fe-input-icon">
                                        <i class="fas fa-user"></i>
                                        <input type="text" name="passengers[{{ $i }}][first_name]" placeholder="{{ __('As in passport') }}" value="{{ old('passengers.'.$i.'.first_name') }}" required>
                                    </div>
                                </div>
                                <div class="fe-input-group">
                                    <label>{{ __('Last Name') }} <span>*</span></label>
                                    <div class="fe-input-icon">
                                        <i class="fas fa-user-tag"></i>
                                        <input type="text" name="passengers[{{ $i }}][last_name]" placeholder="{{ __('As in passport') }}" value="{{ old('passengers.'.$i.'.last_name') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="fe-input-row">
                                <div class="fe-input-group">
                                    <label>{{ __('Date of Birth') }} <span>*</span></label>
                                    <div class="fe-input-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                        <input type="date" name="passengers[{{ $i }}][dob]" value="{{ old('passengers.'.$i.'.dob') }}" required>
                                    </div>
                                </div>
                                <div class="fe-input-group">
                                    <label>{{ __('WhatsApp/Phone') }} <span>*</span></label>
                                    <div class="fe-input-icon">
                                        <i class="fab fa-whatsapp"></i>
                                        <input type="text" name="passengers[{{ $i }}][phone]" placeholder="05xxxxxxxx" value="{{ old('passengers.'.$i.'.phone') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="fe-input-row">
                                <div class="fe-input-group">
                                    <label>{{ __('Nationality') }} <span>*</span></label>
                                    <div class="fe-input-icon">
                                        <i class="fas fa-globe-americas"></i>
                                        <input type="text" name="passengers[{{ $i }}][nationality]" placeholder="{{ __('e.g., SA') }}" value="{{ old('passengers.'.$i.'.nationality') }}" required>
                                    </div>
                                </div>
                                <div class="fe-input-group">
                                    <label>{{ __('Passport Number') }} <span>*</span></label>
                                    <div class="fe-input-icon">
                                        <i class="fas fa-id-card"></i>
                                        <input type="text" name="passengers[{{ $i }}][passport_number]" value="{{ old('passengers.'.$i.'.passport_number') }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="fe-input-row">
                                <div class="fe-input-group">
                                    <label>{{ __('Passport Expiry') }} <span>*</span></label>
                                    <div class="fe-input-icon">
                                        <i class="fas fa-calendar-day"></i>
                                        <input type="date" name="passengers[{{ $i }}][passport_expiry]" value="{{ old('passengers.'.$i.'.passport_expiry') }}" required>
                                    </div>
                                </div>
                                <div class="fe-input-group">
                                    <label>{{ __('Issue Country') }} <span>*</span></label>
                                    <div class="fe-input-icon">
                                        <i class="fas fa-flag"></i>
                                        <input type="text" name="passengers[{{ $i }}][passport_issue_country]" placeholder="{{ __('e.g., SA') }}" value="{{ old('passengers.'.$i.'.passport_issue_country') }}" required>
                                    </div>
                                </div>
                            </div>
                            @else
                            <!-- Hidden Inputs for AI Data -->
                            <input type="hidden" name="passengers[{{ $i }}][title]" id="hidden_title_{{ $i }}" value="Mr">
                            <input type="hidden" name="passengers[{{ $i }}][first_name]" id="hidden_first_name_{{ $i }}">
                            <input type="hidden" name="passengers[{{ $i }}][last_name]" id="hidden_last_name_{{ $i }}">
                            <input type="hidden" name="passengers[{{ $i }}][dob]" id="hidden_dob_{{ $i }}">
                            <input type="hidden" name="passengers[{{ $i }}][nationality]" id="hidden_nationality_{{ $i }}">
                            <input type="hidden" name="passengers[{{ $i }}][passport_number]" id="hidden_passport_number_{{ $i }}">
                            <input type="hidden" name="passengers[{{ $i }}][passport_expiry]" id="hidden_passport_expiry_{{ $i }}">
                            <input type="hidden" name="passengers[{{ $i }}][passport_issue_country]" id="hidden_passport_issue_country_{{ $i }}">
                            
                            <!-- Phone is still manual -->
                            <div class="fe-input-row full mt-3">
                                <div class="fe-input-group">
                                    <label>{{ __('WhatsApp/Phone') }} <span>*</span></label>
                                    <div class="fe-input-icon">
                                        <i class="fab fa-whatsapp"></i>
                                        <input type="text" name="passengers[{{ $i }}][phone]" placeholder="05xxxxxxxx" value="{{ old('passengers.'.$i.'.phone') }}" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Readonly List View -->
                            <div class="fe-form-row mt-2 mb-3">
                                <div class="fe-form-group" style="width: 100%;">
                                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
                                        <h6 style="color: #334155; font-weight: 700; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                                            <i class="fas fa-passport text-primary" style="margin-inline-end: 8px;"></i>{{ __('Extracted Passport Data') }}
                                        </h6>
                                        
                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                            <div>
                                                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Title') }}</span>
                                                <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-title-{{ $i }}">---</div>
                                            </div>
                                            <div>
                                                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('First Name') }}</span>
                                                <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-first_name-{{ $i }}">---</div>
                                            </div>
                                            <div>
                                                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Last Name') }}</span>
                                                <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-last_name-{{ $i }}">---</div>
                                            </div>
                                            <div>
                                                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Date of Birth') }}</span>
                                                <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-dob-{{ $i }}">---</div>
                                            </div>
                                            <div>
                                                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Passport Number') }}</span>
                                                <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-passport_number-{{ $i }}">---</div>
                                            </div>
                                            <div>
                                                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Nationality') }}</span>
                                                <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-nationality-{{ $i }}">---</div>
                                            </div>
                                            <div>
                                                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Issue Country') }}</span>
                                                <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-passport_issue_country-{{ $i }}">---</div>
                                            </div>
                                            <div>
                                                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Passport Expiry') }}</span>
                                                <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-passport_expiry-{{ $i }}">---</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                {{-- ═══ TRAVEL INSURANCE CROSS-SELL COMPONENT ═══ --}}
                <div class="fe-insurance-component" id="insuranceSection">
                    <div class="fe-insurance-card">
                        {{-- Header --}}
                        <div class="fe-ins-header">
                            <div class="fe-ins-header-main">
                                <div class="fe-ins-shield-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="fe-ins-titles">
                                    <div class="fe-ins-title-row">
                                        <h4 class="fe-ins-title">{{ __('Comprehensive Travel & Medical Protection') }}</h4>
                                        <span class="fe-ins-badge">✓ {{ __('Schengen & Worldwide Approved') }}</span>
                                    </div>
                                    <p class="fe-ins-subtitle">{{ __('Global coverage for medical emergencies, trip delays, and lost luggage.') }}</p>
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
                                <div class="fe-ins-benefit-text">{{ __('Trip cancellation & luggage protection') }}</div>
                            </div>
                            <div class="fe-ins-benefit-item">
                                <div class="fe-ins-benefit-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="fe-ins-benefit-text">{{ __('24/7 global telemedicine doctor access') }}</div>
                            </div>
                            <div class="fe-ins-benefit-item">
                                <div class="fe-ins-benefit-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="fe-ins-benefit-text">{{ __('Official certificate for Visa applications') }}</div>
                            </div>
                        </div>

                        {{-- Selectable Choices --}}
                        <div class="fe-ins-options">
                            <label class="fe-ins-option-card" id="optAddInsuranceWrapper">
                                <div class="fe-ins-opt-left">
                                    <input type="radio" name="include_insurance" value="1" id="optAddInsurance" class="fe-ins-radio">
                                    <div class="fe-ins-opt-info">
                                        <span class="fe-ins-opt-title">{{ __('Yes, add comprehensive travel insurance for all travelers') }}</span>
                                        <span class="fe-ins-opt-desc">{{ __('Highly recommended by 94% of international travelers') }}</span>
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
                                        <span class="fe-ins-opt-title" style="color: #64748b; font-weight: 700;">{{ __('No, I decline travel protection (I assume all cancellation & medical risks)') }}</span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <input type="hidden" name="insurance_quote_id" id="hiddenInsuranceQuoteId" value="">
                        <input type="hidden" name="insurance_amount" id="hiddenInsuranceAmount" value="0">
                    </div>
                </div>

                <div class="fe-booking-nav">
                    <a href="{{ route('trips.show', $trip->id) }}" class="fe-btn-back">
                        <i class="fas fa-arrow-left"></i> {{ __('Edit Selection') }}
                    </a>
                    @auth
                        @if(!auth()->user()->canBookDirectly())
                            <div class="alert alert-warning border-0 rounded-4 p-3 text-start mb-0 flex-grow-1 shadow-sm" style="font-size: 0.88rem; background: #fffbeb; color: #92400e;">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                {{ app()->getLocale() == 'ar' ? 'حسابك الحالي (وكيل / مسؤول) مخصص لإدارة العمليات ولا يمكنه إنشاء حجوزات استهلاكية مباشرة. يرجى استخدام حساب عميل.' : 'Your account is an Agent/Admin management account and cannot place consumer bookings.' }}
                            </div>
                        @else
                            <button type="submit" class="fe-btn-confirm" id="submitBtn">
                                {{ __('Confirm & Secure Payment') }} <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login', ['return_url' => url()->full()]) }}" class="fe-btn-confirm btn-save-draft-and-login" onclick="if(window.tripDraftManager) window.tripDraftManager.saveDraft();" style="background: linear-gradient(135deg, #f59e0b, #d97706); text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                            <i class="fas fa-sign-in-alt"></i> {{ __('Sign in to complete booking') }}
                        </a>
                    @endauth
                </div>
            </div>

            {{-- Sidebar Summary --}}
            <aside class="fe-booking-sidebar">
                <div class="fe-summary-glass">
                    <div class="fe-sum-header">
                        <div class="fe-sum-img">
                            @if($trip->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $trip->images->first()->image_path) }}" alt="{{ $trip->title }}">
                            @endif
                        </div>
                        <div class="fe-sum-title">
                            <h3>{{ $trip->title }}</h3>
                            <p><i class="fas fa-map-marker-alt"></i> {{ optional($trip->toCountry)->name_ar }}</p>
                        </div>
                    </div>

                    <div class="fe-sum-details">
                        <div class="fe-sum-row">
                            <span class="fe-sum-label"><i class="fas fa-calendar-check"></i> {{ __('Travel Date') }}</span>
                            <span class="fe-sum-val">{{ \Carbon\Carbon::parse($booking_date)->format('d M, Y') }}</span>
                        </div>
                        
                        @if($selectedPackage)
                        <div class="fe-sum-row active">
                            <span class="fe-sum-label"><i class="fas fa-gem"></i> {{ __('Selected Plan') }}</span>
                            <span class="fe-sum-val">{{ app()->getLocale() == 'ar' ? $selectedPackage->name_ar : $selectedPackage->name_en }}</span>
                            <div class="mt-1 small text-muted" style="white-space: pre-line;">
                                {!! nl2br(e($selectedPackage->hotel_name)) !!}
                            </div>
                            @if($selectedPackage->hotel_website)
                                <a href="{{ $selectedPackage->hotel_website }}" target="_blank" class="mt-2 d-block text-primary font-w700" style="font-size: 0.75rem;">
                                    <i class="fas fa-external-link-alt"></i> {{ __('Visit Hotel Website') }}
                                </a>
                            @endif
                        </div>
                        @endif

                        @if($selectedSeason)
                        <div class="fe-sum-row">
                            <span class="fe-sum-label"><i class="fas fa-clock"></i> {{ __('Pricing Era') }}</span>
                            <span class="fe-sum-val">{{ app()->getLocale() == 'ar' ? $selectedSeason->name_ar : $selectedSeason->name_en }}</span>
                        </div>
                        @endif

                        @if($occupancy_type)
                        <div class="fe-sum-row">
                            <span class="fe-sum-label"><i class="fas fa-bed"></i> {{ __('Room Type') }}</span>
                            <span class="fe-sum-val">{{ ucfirst($occupancy_type) }} {{ __('Room') }}</span>
                        </div>
                        @endif

                        @if(isset($selectedAddons) && count($selectedAddons) > 0)
                        <div class="fe-sum-row">
                            <span class="fe-sum-label"><i class="fas fa-plus-circle"></i> {{ __('Selected Extras') }}</span>
                            @foreach($selectedAddons as $addon)
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <span class="fe-sum-val small">{{ app()->getLocale() == 'ar' ? $addon->name_ar : $addon->name_en }}</span>
                                    <span class="badge bg-light text-dark font-w800">+{{ number_format($addon->extra_cost, 0) }}</span>
                                </div>
                            @endforeach
                        </div>
                        @endif

                        <div class="fe-sum-row">
                            <span class="fe-sum-label"><i class="fas fa-user-friends"></i> {{ __('Group Size') }}</span>
                            <span class="fe-sum-val">{{ $tickets_count }} {{ __('Traveler(s)') }}</span>
                        </div>
                    </div>

                    <div class="fe-price-card">
                        <div class="fe-price-row">
                            <span>{{ __('Unit Price') }}</span>
                            <strong>{{ number_format($unitPrice, 2) }} {{ env('HYPERPAY_CURRENCY', 'SAR') }}</strong>
                        </div>
                        <div class="fe-price-row" id="sidebarTripInsuranceRow" style="display:none; background:#f0fdf4; border-radius:6px; padding:6px 10px; margin-top:8px; border:1px solid #bbf7d0;">
                            <span style="color:#15803d; font-weight:700;"><i class="fas fa-shield-alt text-success me-1"></i> {{ __('Travel Insurance') }}</span>
                            <strong style="color:#15803d; font-weight:800;" id="sidebarTripInsuranceAmount">+ 0.00 SAR</strong>
                        </div>
                        <div class="fe-price-divider"></div>
                        <div class="fe-price-total">
                            <span>{{ __('Total Investment') }}</span>
                            <div class="fe-total-amount">
                                <sub>{{ env('HYPERPAY_CURRENCY', 'SAR') }}</sub>
                                <span id="tripGrandTotalAmount" data-base="{{ floatval($totalPrice ?? ($unitPrice * $tickets_count)) }}">{{ number_format($totalPrice ?? ($unitPrice * $tickets_count), 2) }}</span>
                            </div>
                        </div>
                        <div class="fe-safe-footer">
                            <i class="fas fa-lock"></i> {{ __('Encryption active for this page') }}
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </form>
</div>

{{-- Passport Upload & Crop Modal (Custom - matches flights/booking) --}}
<div class="fe-modal-overlay d-none" id="passportUploadModal">
  <div class="fe-modal-dialog">
    <div class="fe-modal-content">
      <div class="fe-modal-header">
        <h5 style="margin: 0; font-weight: 800; color: #0f172a;"><i class="fas fa-id-card text-primary me-2"></i> {{ __('Upload & Edit Passport') }}</h5>
        <button type="button" class="fe-btn-close" onclick="closePassportModal()"><i class="fas fa-times"></i></button>
      </div>
      <div class="fe-modal-body bg-white">
        {{-- Drag & Drop Area (Visible Initially) --}}
        <div id="modalDropArea" class="p-5 text-center" style="border: 2px dashed #cbd5e1; margin: 20px; border-radius: 15px; background: white; cursor: pointer; transition: all 0.3s;">
            <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #94a3b8; margin-bottom: 15px;"></i>
            <h4 style="font-weight: 700; color: #334155;">{{ __('Drag & Drop here or Click to Browse') }}</h4>
            <p style="color: #64748b; font-size: 0.9rem;">{{ __('Ensure the data page is clear and readable.') }}</p>
            <input type="file" id="modalFileInput" accept="image/*" style="display:none;">
        </div>
        {{-- Cropper Area (Hidden Initially - content injected by JS on file select) --}}
        <div id="modalCropperArea" style="display:none; padding: 20px;"></div>
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
    :root {
        --glass-bg: rgba(255, 255, 255, 0.8);
        --glass-border: rgba(255, 255, 255, 0.2);
        --accent: var(--color-primary, #0ea5e9);
        --pax-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
        --dark-text: #0f172a;
    }

    body { background: #f1f5f9; }

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

    .fe-guest-cta-hint {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
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

    /* HERO SECTION */
    .fe-booking-hero { background: var(--dark-text); padding: 60px 0 100px; color: white; text-align: center; position: relative; overflow: hidden; }
    .fe-booking-hero::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('https://www.transparenttextures.com/patterns/cubes.png') opacity 0.1; }
    .fe-booking-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 6px 15px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.3); margin-bottom: 20px; }
    .fe-booking-hero h1 { color: white; font-size: 2.5rem; font-weight: 900; margin-bottom: 10px; }
    .fe-booking-hero p { color: #94a3b8; font-size: 1.1rem; }

    /* STEPS */
    .fe-steps { display: flex; align-items: center; justify-content: center; gap: 15px; margin-top: 40px; }
    .fe-step { display: flex; flex-direction: column; align-items: center; gap: 10px; }
    .fe-step-num { width: 32px; height: 32px; border-radius: 50%; border: 2px solid #334155; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #475569; position: relative; z-index: 1; background: var(--dark-text); }
    .fe-step span { font-size: 0.75rem; color: #475569; font-weight: 700; text-transform: uppercase; }
    .fe-step.active .fe-step-num { border-color: #10b981; color: #10b981; }
    .fe-step.active span { color: #10b981; }
    .fe-step.current .fe-step-num { background: var(--accent); border-color: var(--accent); color: white; box-shadow: 0 0 15px rgba(14, 165, 233, 0.4); }
    .fe-step.current span { color: white; }
    .fe-step-line { flex: 1; max-width: 50px; height: 2px; background: #334155; }

    /* WRAPPER */
    .fe-booking-wrapper { margin-top: -50px; margin-bottom: 80px; position: relative; z-index: 10; }
    .fe-booking-grid { display: grid; grid-template-columns: 1fr 380px; gap: 30px; }

    /* ERROR CARD */
    .fe-error-card { background: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 20px; margin-bottom: 30px; color: #991b1b; }
    .fe-error-header { font-weight: 900; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }

    /* PAX CARD */
    .fe-pax-card { 
        background: white; 
        border-radius: 24px; 
        box-shadow: var(--pax-shadow); 
        margin-bottom: 25px; 
        overflow: hidden; 
        border: 1px solid #e2e8f0;
        animation: slideUp 0.6s ease-out forwards;
        opacity: 0;
        transform: translateY(20px);
        animation-delay: var(--delay);
    }
    @keyframes slideUp { to { opacity: 1; transform: translateY(0); } }

    .fe-pax-header { background: #f8fafc; padding: 18px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px; }
    .fe-pax-rank { width: 36px; height: 36px; background: white; border: 2px solid #e2e8f0; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 900; color: #64748b; font-size: 0.9rem; }
    .fe-pax-title { flex: 1; display: flex; align-items: center; gap: 10px; }
    .fe-pax-title h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--dark-text); }
    .fe-lead-badge { background: #dcfce7; color: #15803d; font-size: 0.65rem; font-weight: 800; padding: 4px 10px; border-radius: 5px; text-transform: uppercase; }

    .fe-pax-body { padding: 25px; }
    .fe-input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .fe-input-row.full { grid-template-columns: 1fr; }
    .fe-input-group label { display: block; font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; }
    .fe-input-group label span { color: #ef4444; }
    
    .fe-input-icon { position: relative; }
    .fe-input-icon i { position: absolute; left: 18px; top: 16px; color: #94a3b8; font-size: 0.9rem; transition: color 0.3s; }
    .fe-input-icon input { width: 100%; height: 52px; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 0 45px; font-weight: 700; color: var(--dark-text); transition: all 0.3s; }
    .fe-input-icon input:focus { border-color: var(--accent); box-shadow: 0 0 0 5px rgba(14, 165, 233, 0.1); outline: none; }
    .fe-input-icon input:focus + i { color: var(--accent); }
    
    /* Select2 overriding inside fe-input-icon to match input style */
    .fe-input-icon .select2-container--default .select2-selection--single { height: 52px; border: 1.5px solid #e2e8f0; border-radius: 12px; display: flex; align-items: center; outline: none; transition: all 0.3s; }
    .fe-input-icon .select2-container--default.select2-container--focus .select2-selection--single { border-color: var(--accent); box-shadow: 0 0 0 5px rgba(14, 165, 233, 0.1); }
    .fe-input-icon .select2-container--default .select2-selection--single .select2-selection__rendered { padding: 0 45px; font-weight: 700; color: var(--dark-text); line-height: normal; width: 100%; }
    .fe-input-icon .select2-container--default .select2-selection--single .select2-selection__arrow { height: 50px; right: 15px; }
    html[dir="rtl"] .fe-input-icon .select2-container--default .select2-selection--single .select2-selection__arrow { left: 15px; right: auto; }
    html[dir="rtl"] .fe-input-icon i { left: auto; right: 18px; }

    .passport-dropzone { border: 2px dashed var(--accent, #0ea5e9); border-radius: 16px; background: #f8fafc; padding: 30px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease; position: relative; overflow: hidden; }
    .passport-dropzone:hover { background: #f0f9ff; border-color: var(--accent); }
    .passport-dropzone h5 { font-weight: 800; font-size: 1.1rem; color: #1e293b; margin-top: 10px; }
    .passport-dropzone.has-file { border: 2px solid #10b981; background: #f0fdf4; }
    .passport-dropzone.has-file h5, .passport-dropzone.has-file p.text-muted { display: none; }
    .passport-dropzone.has-file i.fa-cloud-upload-alt { display: none; }
    .passport-dropzone.has-file .success-indicator { display: flex; flex-direction: column; align-items: center; justify-content: center; }

    /* ACTIONS */
    .fe-booking-nav { display: flex; align-items: center; justify-content: space-between; margin-top: 30px; }
    .fe-btn-back { display: flex; align-items: center; gap: 8px; color: #64748b; font-weight: 700; text-decoration: none; font-size: 0.9rem; transition: color 0.3s; }
    .fe-btn-back:hover { color: var(--accent); }
    .fe-btn-confirm { height: 56px; padding: 0 40px; background: var(--dark-text); color: white; border: none; border-radius: 16px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: all 0.3s; }
    .fe-btn-confirm:hover { background: #000; transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }

    /* SIDEBAR */
    .fe-summary-glass { background: white; padding: 25px; border-radius: 24px; box-shadow: var(--pax-shadow); border: 1px solid #e2e8f0; position: sticky; top: 100px; }
    .fe-sum-header { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9; }
    .fe-sum-img { width: 70px; height: 70px; border-radius: 15px; overflow: hidden; background: #eee; }
    .fe-sum-img img { width: 100%; height: 100%; object-fit: cover; }
    .fe-sum-title h3 { margin: 0; font-size: 1rem; font-weight: 900; color: var(--dark-text); line-height: 1.3; }
    .fe-sum-title p { margin: 4px 0 0; font-size: 0.75rem; color: #64748b; font-weight: 700; }

    .fe-sum-details { display: flex; flex-direction: column; gap: 12px; margin-bottom: 25px; }
    .fe-sum-row { display: flex; flex-direction: column; gap: 4px; padding: 12px; border-radius: 12px; transition: background 0.3s; }
    .fe-sum-row.active { background: #f0f9ff; border: 1px solid #bae6fd; }
    .fe-sum-label { font-size: 0.7rem; color: #64748b; font-weight: 700; text-transform: uppercase; display: flex; align-items: center; gap: 6px; }
    .fe-sum-label i { color: var(--accent); }
    .fe-sum-val { font-size: 0.85rem; font-weight: 800; color: var(--dark-text); }

    .fe-price-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 20px; padding: 20px; }
    .fe-price-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    .fe-price-row span { font-size: 0.8rem; color: #64748b; font-weight: 700; }
    .fe-price-row strong { font-size: 0.95rem; font-weight: 800; color: var(--dark-text); }
    .fe-price-divider { border-top: 1px dashed #cbd5e1; margin-bottom: 15px; }
    .fe-price-total span { font-size: 0.85rem; color: #1e293b; font-weight: 900; display: block; margin-bottom: 5px; }
    .fe-total-amount { font-size: 1.8rem; font-weight: 950; color: var(--accent); display: flex; align-items: baseline; gap: 5px; }
    .fe-total-amount sub { font-size: 0.8rem; font-weight: 800; bottom: 0; color: #94a3b8; }
    .fe-safe-footer { display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 20px; font-size: 0.65rem; font-weight: 800; color: #10b981; }

    /* RTL OVERRIDES */
    [dir="rtl"] .fe-input-icon i { left: auto; right: 18px; }
    [dir="rtl"] .fe-input-icon input { padding: 0 45px 0 16px; }

    @media (max-width: 991px) {
        .fe-booking-grid { grid-template-columns: 1fr; }
        .fe-booking-sidebar { order: -1; }
        .fe-summary-glass { position: static; }
    }
    .readonly-field-1, .readonly-field-2, .readonly-field-3, .readonly-field-4, .readonly-field-5, .readonly-field-6, .readonly-field-7, .readonly-field-8, .readonly-field-9 {
        pointer-events: none !important;
        background-color: #f8fafc !important;
        opacity: 0.8 !important;
        border-color: #e2e8f0 !important;
    }

    /* ─── Custom Modal (Passport Upload) ─── */
    .fe-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
    .fe-modal-overlay.show { opacity: 1; visibility: visible; }
    .fe-modal-overlay.d-none { display: none !important; }
    .fe-modal-dialog { background: white; border-radius: 20px; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto; transform: translateY(-20px); transition: all 0.3s ease; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
    .fe-modal-overlay.show .fe-modal-dialog { transform: translateY(0); }
    .fe-modal-header { padding: 20px 25px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
    .fe-modal-body { padding: 0; background: #f8fafc; }
    .fe-modal-footer { padding: 15px 25px; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; align-items: center; background: #f8fafc; }
    .fe-btn-close { background: transparent; border: none; font-size: 1.5rem; color: #64748b; cursor: pointer; transition: color 0.2s; }
    .fe-btn-close:hover { color: #0f172a; }
    .fe-btn { display: inline-flex; align-items: center; gap: 6px; border-radius: 10px; font-weight: 700; font-size: 0.9rem; cursor: pointer; border: none; transition: all 0.2s; }
    .fe-btn-primary { background: var(--accent, #0ea5e9); color: white; padding: 10px 20px; }
    .fe-btn-primary:hover:not(:disabled) { background: #0284c7; }
    .fe-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
    .fe-btn-outline { background: transparent; color: #64748b; border: 1.5px solid #e2e8f0; padding: 10px 20px; }
    .fe-btn-outline:hover { background: #f8fafc; }
    .fe-btn-light { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .fe-btn-light:hover { background: #e2e8f0; }
    .ms-auto { margin-inline-start: auto; }
    .fw-bold { font-weight: 700 !important; }
    .p-5 { padding: 3rem !important; }
    .me-2 { margin-inline-end: 0.5rem !important; }
    .ms-1 { margin-inline-start: 0.25rem !important; }
    .bg-light { background-color: #f8fafc !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js"></script>
<script>
    function updateFileName(input, index) {
        const nameLabel = document.getElementById('file_name_' + index);
        if(input.files && input.files.length > 0) {
            nameLabel.innerHTML = '<i class="fas fa-check-circle" style="color: #10b981; margin-right: 5px;"></i> ' + input.files[0].name;
            nameLabel.closest('.fe-upload-label').style.borderColor = "#10b981";
            nameLabel.closest('.fe-upload-label').style.background = "#f0fdf4";
        }
    }
    
    document.getElementById('tripBookingForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> {{ __("Securing Booking...") }}';
        btn.disabled = true;
    });
</script>
<script>
    // ═══ PASSPORT OCR LOGIC WITH MODAL & CROPPER (matches flights/booking) ═══
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
        $('#modalDropArea').show();
        $('#modalCropperArea').hide().empty();
        $('#btnChangeImage').hide();
        $('#btnConfirmUpload').prop('disabled', true).html('{{ __("Confirm & Scan") }} <i class="fas fa-magic ms-1"></i>');
    }

    // Handle Drag & Drop on Modal
    const dropArea = document.getElementById('modalDropArea');
    dropArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropArea.style.borderColor = 'var(--accent)';
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
        document.getElementById('modalFileInput').click();
    });
    document.getElementById('modalFileInput').addEventListener('change', function() {
        if(this.files.length) {
            handleModalFile(this.files[0]);
        }
    });

    function handleModalFile(file) {
        currentModalFile = file;
        $('#modalDropArea').hide();
        $('#btnChangeImage').show();
        $('#btnConfirmUpload').prop('disabled', false);

        const cropperHtml = '<div style="max-height: 50vh; display:flex; justify-content:center; background: #000; border-radius: 10px; overflow: hidden;"><img id="modalImageToCrop" src="" style="max-width: 100%;"></div><p class="text-muted text-center mt-3 mb-0" style="font-size: 0.85rem;"><i class="fas fa-crop-alt"></i> {{ __("Adjust the frame to focus tightly on the passport data page for optimal AI scanning.") }}</p>';
        $('#modalCropperArea').html(cropperHtml).show();

        const reader = new FileReader();
        reader.onload = function(event) {
            const img = document.getElementById('modalImageToCrop');
            img.src = event.target.result;
            if(cropper) { cropper.destroy(); cropper = null; }
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
        dropzone.find('.success-indicator').show();
        $('#file_name_display_' + currentUploadIndex).text(file.name);

        triggerTripOcr(hiddenInput, currentUploadIndex);
    }

    function triggerTripOcr(input, index) {
        const file = input.files[0];
        if (!file) return;

        $('#scan_error_' + index).remove();

        const loader = $('#ai-loading-' + index);
        loader.css('display', 'flex').show();
        
        const formData = new FormData();
        formData.append('passport_image', file);
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
                        dropzone.find('.success-indicator').hide();
                        
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
                    
                    // Fill text fields
                    let fullName = '';
                    if (data.first_name) fullName += data.first_name + ' ';
                    if (data.last_name) fullName += data.last_name;
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
                    if (data.dob) {
                        $('input[name="passengers['+index+'][dob]"]').val(data.dob);
                        $('#hidden_dob_'+index).val(data.dob);
                        $('.ai-display-dob-'+index).text(data.dob);
                    }
                    if (data.nationality) {
                        $('input[name="passengers['+index+'][nationality]"]').val(data.nationality);
                        $('#hidden_nationality_'+index).val(data.nationality);
                        $('.ai-display-nationality-'+index).text(data.nationality);
                    }
                    if (data.passport_no) {
                        $('input[name="passengers['+index+'][passport_number]"]').val(data.passport_no);
                        $('#hidden_passport_number_'+index).val(data.passport_no);
                        $('.ai-display-passport_number-'+index).text(data.passport_no);
                    }
                    if (data.passport_expiry_date) {
                        $('input[name="passengers['+index+'][passport_expiry]"]').val(data.passport_expiry_date);
                        $('#hidden_passport_expiry_'+index).val(data.passport_expiry_date);
                        $('.ai-display-passport_expiry-'+index).text(data.passport_expiry_date);
                    }
                    if (data.passport_issue_country) {
                        $('input[name="passengers['+index+'][passport_issue_country]"]').val(data.passport_issue_country);
                        $('#hidden_passport_issue_country_'+index).val(data.passport_issue_country);
                        $('.ai-display-passport_issue_country-'+index).text(data.passport_issue_country);
                    }
                    
                    if (data.gender) {
                        let gender = data.gender.toUpperCase().trim();
                        let title = 'Mr';
                        
                        if (gender === 'M' || gender === 'MALE' || gender === 'ذكر' || gender === 'ذ') {
                            title = 'Mr';
                        } else if (gender === 'F' || gender === 'FEMALE' || gender === 'أنثى' || gender === 'أ') {
                            title = 'Ms';
                        }
                        
                        if (title) {
                            // Use id-based selector to avoid triggering broken onchange
                            $('#title_select_' + index).val(title);
                            $('select[name="passengers['+index+'][title]"]').val(title);
                            $('.ai-display-title-'+index).text(title);
                        }
                    }
                    
                    if(typeof toastr !== 'undefined') {
                        toastr.success('{{ __("Passport data extracted successfully.") }}');
                    } else if(typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: '{{ __("Passport data extracted successfully.") }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                    }
                } else {
                    let warnMsg = (response && response.message) ? response.message : '{{ __("Could not extract data perfectly. Please fill manually.") }}';
                    if(typeof toastr !== 'undefined') {
                        toastr.warning(warnMsg);
                    } else if(typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'warning', title: '{{ __("Scan Notice") }}', text: warnMsg, confirmButtonColor: '#0ea5e9' });
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
                }

                $('#hidden_passport_input_' + index).val('');
                const dropzone = $('#passport_dropzone_' + index);
                dropzone.removeClass('has-file');
                dropzone.find('.success-indicator').hide();
                
                $('#scan_error_' + index).remove();
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
                loader.css('display', 'none');
            }
        });
    }

    // Form Validation before submit
    $('#tripBookingForm').on('submit', function(e) {
        let allowManualEdit = "{{ \App\Models\Setting::get('allow_manual_passport_edit', '1') }}";
        if (allowManualEdit !== '1') {
            let isValid = true;
            let totalPax = {{ $tickets_count ?? 1 }};
            
            for (let i = 0; i < totalPax; i++) {
                let requiredFields = [
                    '#hidden_first_name_' + i,
                    '#hidden_last_name_' + i,
                    '#hidden_dob_' + i,
                    '#hidden_nationality_' + i,
                    '#hidden_passport_number_' + i,
                    '#hidden_passport_expiry_' + i,
                    '#hidden_passport_issue_country_' + i
                ];
                
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

    window.tripInsuranceQuoteData = null;

    function fetchTripInsuranceQuote() {
        $('#insurancePriceDisplay').html('<span class="spinner-border spinner-border-sm text-primary" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span> <span class="fs-12 text-muted ms-1">{{ __("Calculating...") }}</span>');
        $('#insuranceTotalOptionPrice').html('<span class="spinner-border spinner-border-sm text-primary me-1" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span> <span class="fs-12 text-muted">{{ __("Calculating...") }}</span>');

        $.ajax({
            url: '{{ route("insurance.quote") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                destination_country: '{{ optional($trip->toCountry)->iso ?? "GLOBAL" }}',
                departure_date: '{{ \Carbon\Carbon::parse($booking_date)->format("Y-m-d") }}',
                return_date: '{{ \Carbon\Carbon::parse($booking_date)->addDays($trip->duration_days ?: 7)->format("Y-m-d") }}',
                trip_cost: {{ floatval($totalPrice ?? ($unitPrice * $tickets_count)) }},
                passengers_count: {{ $tickets_count ?? 1 }},
                booking_type: 'trip'
            },
            success: function(res) {
                if (res && res.success) {
                    window.tripInsuranceQuoteData = res;
                    $('#insurancePriceDisplay').text(res.unit_price.toFixed(2) + ' SAR');
                    $('#insuranceTotalOptionPrice').text('+ ' + res.selling_price.toFixed(2) + ' SAR');
                    $('#hiddenInsuranceQuoteId').val(res.quote_id);
                } else {
                    $('#insurancePriceDisplay').text('-- SAR');
                    $('#insuranceTotalOptionPrice').text('-- SAR');
                }
            },
            error: function() {
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
        updateTripGrandTotal();
    });

    function updateTripGrandTotal() {
        let baseTotal = parseFloat($('#tripGrandTotalAmount').data('base')) || 0;
        let insuranceTotal = ($('input[name="include_insurance"]:checked').val() == '1') ? (window.tripInsuranceQuoteData?.selling_price || 0) : 0;

        $('#hiddenInsuranceAmount').val(insuranceTotal.toFixed(2));

        if (insuranceTotal > 0) {
            $('#sidebarTripInsuranceRow').slideDown(200);
            $('#sidebarTripInsuranceAmount').text('+ ' + insuranceTotal.toFixed(2) + ' SAR');
        } else {
            $('#sidebarTripInsuranceRow').slideUp(200);
        }

        let finalTotal = baseTotal + insuranceTotal;
        $('#tripGrandTotalAmount').text(finalTotal.toFixed(2));
    }

    $(document).ready(function() {
        if($.fn.select2) {
            $('.fe-select2').select2({ width: '100%' });
        }

        fetchTripInsuranceQuote();

        // Initialize Auto-Save & Auto-Restore for Tour Package Draft
        if (typeof BookingDraftManager !== 'undefined') {
            window.tripDraftManager = new BookingDraftManager('#tripBookingForm', 'trip_{{ $trip->id }}_{{ $tickets_count }}');
        }
    });

</script>
<style>
    /* ─── Travel Insurance Cross-Sell Component ─────────────────────────── */
    .fe-insurance-component {
        margin: 25px 0;
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
</style>
@endpush
