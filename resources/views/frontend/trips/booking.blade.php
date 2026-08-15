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
                            
                            <div class="fe-upload-zone mt-2 mb-3" id="passport_dropzone_{{ $i }}" onclick="openPassportModal({{ $i }})" style="cursor: pointer;">
                                <input type="file" name="passengers[{{ $i }}][passport_image]" id="hidden_passport_input_{{ $i }}" accept="image/*" class="d-none">
                                <label class="fe-upload-label" style="pointer-events: none;">
                                    <div class="fe-upload-icon"><i class="fas fa-camera-retro"></i></div>
                                    <div class="fe-upload-text">
                                        <b id="file_name_display_{{ $i }}">{{ __('Scan/Upload Passport') }}</b>
                                        <span>{{ __('Extract details automatically via AI') }}</span>
                                    </div>
                                </label>
                                
                                <div class="ai-loading-overlay d-none align-items-center justify-content-center" id="ai-loading-{{ $i }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 10; border-radius: 14px;">
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
                                <div class="fe-form-group" style="max-width: 200px;">
                                    <label class="fe-label">{{ __('Title') }}</label>
                                    <select name="passengers[{{ $i }}][title]" class="fe-input" onchange="document.getElementById('hidden_title_{{ $i }}').value = this.value">
                                        <option value="Mr">{{ __('Mr') }}</option>
                                        <option value="Mrs">{{ __('Mrs') }}</option>
                                        <option value="Ms">{{ __('Ms') }}</option>
                                        <option value="Master">{{ __('Master') }}</option>
                                        <option value="Miss">{{ __('Miss') }}</option>
                                    </select>
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
                            @endif
                        </div>
                    </div>
                @endfor

                <div class="fe-booking-nav">
                    <a href="{{ route('trips.show', $trip->id) }}" class="fe-btn-back">
                        <i class="fas fa-arrow-left"></i> {{ __('Edit Selection') }}
                    </a>
                    <button type="submit" class="fe-btn-confirm" id="submitBtn">
                        {{ __('Confirm & Secure Payment') }} <i class="fas fa-chevron-right"></i>
                    </button>
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
                        <div class="fe-price-divider"></div>
                        <div class="fe-price-total">
                            <span>{{ __('Total Investment') }}</span>
                            <div class="fe-total-amount">
                                <sub>{{ env('HYPERPAY_CURRENCY', 'SAR') }}</sub>
                                {{ number_format($unitPrice * $tickets_count, 2) }}
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
@endsection

@push('styles')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.8);
        --glass-border: rgba(255, 255, 255, 0.2);
        --accent: var(--color-primary, #0ea5e9);
        --pax-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
        --dark-text: #0f172a;
    }

    body { background: #f1f5f9; }

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

    .fe-upload-zone { margin-top: 5px; position: relative; }
    .fe-upload-input { display: none; }
    .fe-upload-label { cursor: pointer; display: flex; align-items: center; gap: 15px; border: 2px dashed #e2e8f0; padding: 15px; border-radius: 12px; background: #f8fafc; transition: all 0.3s; position: relative; }
    .fe-upload-label:hover { border-color: var(--accent); background: white; }
    .fe-upload-icon { width: 42px; height: 42px; background: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1.1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .fe-upload-text b { display: block; font-size: 0.85rem; color: var(--dark-text); }
    .fe-upload-text span { font-size: 0.7rem; color: #94a3b8; font-weight: 600; }

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
</style>
@endpush

@push('scripts')
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
    // OCR Logic    
    let currentUploadIndex = null;
    let currentModalFile = null;
    let cropper = null;

    function openPassportModal(index) {
        currentUploadIndex = index;
        const hiddenInput = document.getElementById('hidden_passport_input_' + index);
        // We use a separate input for file selection to trigger onChange repeatedly if needed
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        fileInput.onchange = function(e) {
            if(e.target.files && e.target.files.length > 0) {
                currentModalFile = e.target.files[0];
                showCropperModal();
            }
        };
        fileInput.click();
    }

    function showCropperModal() {
        if(!currentModalFile) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            $('#cropperImage').attr('src', e.target.result);
            
            // Re-initialize modal completely manually to avoid BS4/BS5 conflicts
            $('#passportUploadModal').css({
                'display': 'block',
                'background': 'rgba(0,0,0,0.5)',
                'opacity': '1'
            }).addClass('show');
            $('body').append('<div class="modal-backdrop fade show"></div>');
            $('body').addClass('modal-open');

            if (cropper) cropper.destroy();
            const image = document.getElementById('cropperImage');
            cropper = new Cropper(image, {
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        }
        reader.readAsDataURL(currentModalFile);
    }
    
    function closePassportModal() {
        $('#passportUploadModal').removeClass('show').css('display', 'none');
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        if(cropper) cropper.destroy();
    }
    
    $('#cancelCropBtn').on('click', closePassportModal);

    $('#confirmCropBtn').on('click', function() {
        if (!cropper) return;
        const btn = $(this);
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> {{ __("Processing...") }}').prop('disabled', true);

        cropper.getCroppedCanvas({
            maxWidth: 2000,
            maxHeight: 2000,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        }).toBlob(function(blob) {
            if (!blob) {
                btn.html(originalText).prop('disabled', false);
                return;
            }
            
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
        $('#file_name_display_' + currentUploadIndex).text(file.name);

        triggerTripOcr(hiddenInput, currentUploadIndex);
    }

    function triggerTripOcr(input, index) {
        const file = input.files[0];
        if (!file) return;

        $('#scan_error_' + index).remove();

        const loader = $('#ai-loading-' + index);
        loader.removeClass('d-none').css('display', 'flex').show();
        
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
                            $('select[name="passengers['+index+'][title]"]').val(title).trigger('change');
                            $('#hidden_title_'+index).val(title);
                            $('.ai-display-title-'+index).text(title);
                        }
                    }
                    
                    if(typeof toastr !== 'undefined') {
                        toastr.success('{{ __("Passport data extracted successfully.") }}');
                    } else if(typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: '{{ __("Passport data extracted successfully.") }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                    }
                } else {
                    if(typeof toastr !== 'undefined') {
                        toastr.warning('{{ __("Could not extract data perfectly. Please fill manually.") }}');
                    }
                }
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', title: '{{ __("Scan Failed") }}', text: '{{ __("Error scanning passport. Please try again.") }}', confirmButtonColor: '#0ea5e9' });
                $('#hidden_passport_input_' + index).val('');
                const dropzone = $('#passport_dropzone_' + index);
                dropzone.removeClass('has-file');
                
                $('#scan_error_' + index).remove();
                const errorHtml = `
                <div id="scan_error_${index}" class="mt-3 d-flex align-items-center" style="border-radius: 12px; background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 16px; box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.1); animation: fadeIn 0.3s ease;">
                    <i class="fas fa-exclamation-triangle fa-lg" style="color: #ef4444; margin-inline-end: 12px;"></i>
                    <div style="text-align: start;">
                        <div style="font-weight: 800; font-size: 0.95rem; margin-bottom: 2px;">{{ __("Scan Failed") }}</div>
                        <div style="font-weight: 500; font-size: 0.85rem; opacity: 0.9;">{{ __("Please upload a clearer image of the passport data page.") }}</div>
                    </div>
                </div>`;
                dropzone.after(errorHtml);
            },
            complete: function() {
                loader.addClass('d-none').hide();
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
</style>
@endpush
