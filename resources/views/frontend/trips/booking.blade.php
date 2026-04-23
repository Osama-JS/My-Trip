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
                            
                            <div class="fe-input-row full">
                                <div class="fe-input-group">
                                    <label>{{ __('Full Name') }} <span>*</span></label>
                                    <div class="fe-input-icon">
                                        <i class="fas fa-user-tie"></i>
                                        <input type="text" name="passengers[{{ $i }}][name]" placeholder="{{ __('Exactly as in passport') }}" value="{{ old('passengers.'.$i.'.name') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="fe-input-row">
                                <div class="fe-input-group">
                                    <label>{{ __('WhatsApp/Phone') }} <span>*</span></label>
                                    <div class="fe-input-icon">
                                        <i class="fab fa-whatsapp"></i>
                                        <input type="text" name="passengers[{{ $i }}][phone]" placeholder="05xxxxxxxx" value="{{ old('passengers.'.$i.'.phone') }}" required>
                                    </div>
                                </div>
                                <div class="fe-input-group">
                                    <label>{{ __('Nationality') }} <span>*</span></label>
                                    <div class="fe-input-icon">
                                        <i class="fas fa-globe-americas"></i>
                                        <input type="text" name="passengers[{{ $i }}][nationality]" placeholder="{{ __('e.g., Saudi') }}" value="{{ old('passengers.'.$i.'.nationality') }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="fe-input-row">
                                <div class="fe-input-group">
                                    <label>{{ __('Passport Number') }} <span>*</span></label>
                                    <div class="fe-input-icon">
                                        <i class="fas fa-id-card"></i>
                                        <input type="text" name="passengers[{{ $i }}][passport_number]" value="{{ old('passengers.'.$i.'.passport_number') }}" required>
                                    </div>
                                </div>
                                <div class="fe-input-group">
                                    <label>{{ __('Passport Expiry') }} <span>*</span></label>
                                    <div class="fe-input-icon">
                                        <i class="fas fa-calendar-day"></i>
                                        <input type="date" name="passengers[{{ $i }}][passport_expiry]" value="{{ old('passengers.'.$i.'.passport_expiry') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="fe-upload-zone">
                                <input type="file" name="passengers[{{ $i }}][passport_image]" id="passport_img_{{ $i }}" accept="image/*,application/pdf" class="fe-upload-input" onchange="updateFileName(this, {{ $i }})">
                                <label for="passport_img_{{ $i }}" class="fe-upload-label">
                                    <div class="fe-upload-icon"><i class="fas fa-camera-retro"></i></div>
                                    <div class="fe-upload-text">
                                        <b id="file_name_{{ $i }}">{{ __('Scan/Upload Passport') }}</b>
                                        <span>{{ __('Required for international insurance') }}</span>
                                    </div>
                                </label>
                            </div>

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

    .fe-upload-zone { margin-top: 5px; }
    .fe-upload-input { display: none; }
    .fe-upload-label { cursor: pointer; display: flex; align-items: center; gap: 15px; border: 2px dashed #e2e8f0; padding: 15px; border-radius: 12px; background: #f8fafc; transition: all 0.3s; }
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
@endpush
