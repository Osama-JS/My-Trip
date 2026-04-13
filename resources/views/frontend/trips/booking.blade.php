@extends('frontend.layouts.app')

@section('title', __('Complete Your Booking') . ': ' . $trip->title)

@section('content')
<div class="fe-page-header fe-booking-header">
    <div class="fe-container">
        <h1>{{ __('Complete Your Booking') }}</h1>
        <p>{{ __('Please enter the details for all travelers as they appear in their passports.') }}</p>
    </div>
</div>

<div class="fe-container" style="margin-top: -40px; margin-bottom: 80px;">
    
    @if ($errors->any())
        <div class="alert alert-danger" style="border-radius: 12px; margin-bottom: 30px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 15px;">
            <div style="font-weight: 700; margin-bottom: 5px;"><i class="fas fa-exclamation-circle me-2"></i> {{ __('Please correct the following errors:') }}</div>
            <ul class="mb-0" style="font-size: 0.9rem; padding-inline-start: 20px;">
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
        <input type="hidden" name="booking_date" value="{{ $booking_date }}">
        <input type="hidden" name="notes" value="{{ $notes ?? '' }}">

        <div class="fe-booking-grid">
            {{-- Left Side: Passenger Forms --}}
            <div class="fe-booking-main">
                
                @for($i = 1; $i <= $tickets_count; $i++)
                    <div class="fe-booking-card">
                        <div class="fe-card-header">
                            <i class="fas fa-user"></i> 
                            <h3>{{ __('Traveler') }} {{ $i }} {{ $i == 1 ? '('.__('Lead Traveler').')' : '' }}</h3>
                        </div>
                        <div class="fe-card-body">
                            
                            <div class="fe-form-row" style="grid-template-columns: 1fr;">
                                <div class="fe-form-group">
                                    <label class="fe-label">{{ __('Full Name') }} <span style="color:red;">*</span></label>
                                    <input type="text" name="passengers[{{ $i }}][name]" class="fe-input" placeholder="{{ __('As in passport') }}" value="{{ old('passengers.'.$i.'.name') }}" required>
                                </div>
                            </div>

                            <div class="fe-form-row">
                                <div class="fe-form-group">
                                    <label class="fe-label">{{ __('Phone') }} <span style="color:red;">*</span></label>
                                    <div class="fe-input-with-icon" style="position: relative;">
                                        <i class="fas fa-phone-alt" style="position: absolute; top: 12px; background: transparent; left: 15px; color: #94a3b8;"></i>
                                        <input type="text" name="passengers[{{ $i }}][phone]" class="fe-input" style="padding-left: 45px;" placeholder="05xxxxxxxx" value="{{ old('passengers.'.$i.'.phone') }}" required>
                                    </div>
                                </div>
                                <div class="fe-form-group">
                                    <label class="fe-label">{{ __('Nationality') }} <span style="color:red;">*</span></label>
                                    <input type="text" name="passengers[{{ $i }}][nationality]" class="fe-input" placeholder="{{ __('e.g., Saudi') }}" value="{{ old('passengers.'.$i.'.nationality') }}" required>
                                </div>
                            </div>
                            
                            <div class="fe-form-row">
                                <div class="fe-form-group">
                                    <label class="fe-label">{{ __('Passport Number') }} <span style="color:red;">*</span></label>
                                    <input type="text" name="passengers[{{ $i }}][passport_number]" class="fe-input" placeholder="" value="{{ old('passengers.'.$i.'.passport_number') }}" required>
                                </div>
                                <div class="fe-form-group">
                                    <label class="fe-label">{{ __('Passport Expiry Date') }} <span style="color:red;">*</span></label>
                                    <input type="date" name="passengers[{{ $i }}][passport_expiry]" class="fe-input" value="{{ old('passengers.'.$i.'.passport_expiry') }}" required>
                                </div>
                            </div>

                            <div class="fe-form-row" style="grid-template-columns: 1fr; margin-top: 15px;">
                                <div class="fe-form-group">
                                    <label class="fe-label">{{ __('Passport Image') }} <span style="color: #94a3b8; font-weight: normal; font-size: 0.8rem;">({{ __('Optional') }})</span></label>
                                    <div style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 15px; text-align: center; background: #f8fafc;">
                                        <input type="file" name="passengers[{{ $i }}][passport_image]" id="passport_img_{{ $i }}" accept="image/jpeg,image/png,image/jpg,application/pdf" style="display:none;" onchange="updateFileName(this, {{ $i }})">
                                        <label for="passport_img_{{ $i }}" style="cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                            <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: var(--color-primary);"></i>
                                            <span id="file_name_{{ $i }}" style="color: #475569; font-weight: 600;">{{ __('Click to upload passport copy') }}</span>
                                            <span style="font-size: 0.75rem; color: #94a3b8;">{{ __('Supported formats: JPG, PNG, PDF (Max: 5MB)') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @endfor

                <div class="fe-booking-actions">
                    <a href="{{ url()->previous() }}" class="fe-btn fe-btn-outline">{{ __('Back') }}</a>
                    <button type="submit" class="fe-btn fe-btn-primary" id="submitBtn">
                        {{ __('Proceed to Payment') }} <i class="fas fa-credit-card" style="margin-inline-start:8px;"></i>
                    </button>
                </div>
            </div>

            {{-- Right Side: Summary --}}
            <aside class="fe-booking-sidebar">
                <div class="fe-summary-card">
                    <div class="fe-summary-header">
                        <i class="fas fa-suitcase-rolling"></i>
                        <h3>{{ __('Trip Details') }}</h3>
                    </div>
                    
                    <div class="fe-summary-body">
                        <h4 class="fe-summary-title">{{ $trip->title }}</h4>
                        
                        <div class="fe-summary-info">
                            <div class="fe-info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <span class="fe-info-label">{{ __('Destination') }}</span>
                                    <strong class="fe-info-value">{{ optional($trip->toCountry)->name ?? __('Specified Destination') }}</strong>
                                </div>
                            </div>
                            
                            <div class="fe-info-item">
                                <i class="fas fa-calendar-alt"></i>
                                <div>
                                    <span class="fe-info-label">{{ __('Booking Date') }}</span>
                                    <strong class="fe-info-value">{{ \Carbon\Carbon::parse($booking_date)->format('d M, Y') }}</strong>
                                </div>
                            </div>
                            
                            <div class="fe-info-item">
                                <i class="fas fa-users"></i>
                                <div>
                                    <span class="fe-info-label">{{ __('Travelers') }}</span>
                                    <strong class="fe-info-value">{{ $tickets_count }} {{ __('Person(s)') }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="fe-summary-total">
                            <div class="fe-total-row">
                                <span class="fe-total-label">{{ __('Price per traveler') }}</span>
                                <span class="fe-total-val">{{ number_format($trip->price, 2) }} {{ env('HYPERPAY_CURRENCY', 'SAR') }}</span>
                            </div>
                            
                            @if($trip->base_capacity && $tickets_count > $trip->base_capacity && $trip->extra_passenger_price)
                            <div class="fe-total-row" style="color: #ef4444;">
                                <span class="fe-total-label">{{ __('Extra Passengers (') }}{{ $tickets_count - $trip->base_capacity }}{{ __(')') }}</span>
                                <span class="fe-total-val">{{ number_format($trip->extra_passenger_price * ($tickets_count - $trip->base_capacity), 2) }} {{ env('HYPERPAY_CURRENCY', 'SAR') }}</span>
                            </div>
                            @endif

                            <div class="fe-total-final">
                                <span>{{ __('Total Due') }}</span>
                                <strong>
                                    @php
                                        $total = $trip->price * $tickets_count;
                                        if ($trip->base_capacity && $tickets_count > $trip->base_capacity && $trip->extra_passenger_price) {
                                            $total = ($trip->price * $trip->base_capacity) + ($trip->extra_passenger_price * ($tickets_count - $trip->base_capacity));
                                        }
                                    @endphp
                                    {{ number_format($total, 2) }} 
                                    <sup>{{ env('HYPERPAY_CURRENCY', 'SAR') }}</sup>
                                </strong>
                            </div>
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
        --fe-primary: var(--color-primary, #0ea5e9);
        --fe-primary-50: rgba(14, 165, 233, 0.05);
        --fe-dark: #1e293b;
        --fe-gray-50: #f8fafc;
        --fe-gray-100: #f1f5f9;
        --fe-gray-200: #e2e8f0;
        --fe-gray-500: #64748b;
        --fe-bg: #f8fafc;
    }
    
    body { background-color: var(--fe-bg); }

    .fe-booking-header { background: linear-gradient(135deg, var(--fe-primary) 0%, var(--fe-dark) 100%); padding: 80px 0 100px; color: white; text-align: center; }
    .fe-booking-header h1 { color: white; margin-bottom: 10px; font-weight: 900; }
    .fe-container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }

    .fe-booking-grid { display: grid; grid-template-columns: 1fr 380px; gap: 30px; }
    .fe-booking-card { background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid var(--fe-gray-100); margin-bottom: 30px; overflow: hidden; }
    .fe-card-header { background: var(--fe-gray-50); padding: 20px 24px; border-bottom: 1px solid var(--fe-gray-100); display: flex; align-items: center; gap: 12px; }
    .fe-card-header i { color: var(--fe-primary); font-size: 1.2rem; }
    .fe-card-header h3 { font-size: 1.15rem; font-weight: 800; margin: 0; color: var(--fe-dark); }
    .fe-card-body { padding: 30px; }

    .fe-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .fe-form-group { margin-bottom: 5px; }
    .fe-label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--fe-dark); margin-bottom: 8px; }
    .fe-input { width: 100%; height: 50px; background: var(--fe-gray-50); border: 1.5px solid var(--fe-gray-100); border-radius: 12px; padding: 0 16px; font-weight: 600; transition: all 0.2s; color: var(--fe-dark); }
    .fe-input:focus { border-color: var(--fe-primary); outline: none; background: white; box-shadow: 0 0 0 4px var(--fe-primary-50); }

    .fe-booking-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 30px; }
    .fe-btn { display: inline-flex; align-items: center; justify-content: center; height: 54px; padding: 0 30px; border-radius: 14px; font-weight: 800; font-size: 1rem; cursor: pointer; transition: all 0.3s; text-decoration: none; border: none; }
    .fe-btn-primary { background: var(--fe-primary); color: white; box-shadow: 0 10px 20px var(--fe-primary-50); }
    .fe-btn-primary:hover { background: #0284c7; transform: translateY(-2px); box-shadow: 0 12px 25px rgba(2, 132, 199, 0.2); color: white; }
    .fe-btn-outline { background: white; color: var(--fe-gray-500); border: 2px solid var(--fe-gray-200); }
    .fe-btn-outline:hover { background: var(--fe-gray-50); border-color: var(--fe-gray-500); color: var(--fe-dark); }

    /* SUMMARY CARD */
    .fe-summary-card { background: white; border-radius: 20px; box-shadow: 0 4px 25px rgba(0,0,0,0.05); border: 1px solid var(--fe-gray-100); position: sticky; top: 100px; }
    .fe-summary-header { padding: 24px; border-bottom: 1px solid var(--fe-gray-100); background: var(--fe-dark); color: white; border-radius: 20px 20px 0 0; display: flex; align-items: center; gap: 10px; }
    .fe-summary-header h3 { margin: 0; font-size: 1.2rem; font-weight: 800; color: white; }
    .fe-summary-body { padding: 24px; }
    .fe-summary-title { font-size: 1.15rem; font-weight: 900; color: var(--fe-dark); margin-bottom: 20px; line-height: 1.4; }
    
    .fe-summary-info { display: flex; flex-direction: column; gap: 15px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--fe-gray-100); }
    .fe-info-item { display: flex; align-items: flex-start; gap: 12px; }
    .fe-info-item i { color: var(--fe-primary); margin-top: 4px; font-size: 0.9rem; }
    .fe-info-item div { display: flex; flex-direction: column; gap: 2px; }
    .fe-info-label { font-size: 0.75rem; color: var(--fe-gray-500); font-weight: 700; text-transform: uppercase; }
    .fe-info-value { font-size: 0.9rem; font-weight: 800; color: var(--fe-dark); }

    .fe-summary-total { background: var(--fe-gray-50); padding: 20px; border-radius: 15px; }
    .fe-total-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.9rem; }
    .fe-total-label { color: var(--fe-gray-500); font-weight: 600; }
    .fe-total-val { font-weight: 800; color: var(--fe-dark); }
    
    .fe-total-final { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding-top: 15px; border-top: 1px dashed var(--fe-gray-200); }
    .fe-total-final span { font-weight: 800; font-size: 1rem; color: var(--fe-dark); }
    .fe-total-final strong { font-size: 1.5rem; font-weight: 900; color: var(--fe-primary); display: flex; align-items: center; gap: 5px; }
    .fe-total-final sup { font-size: 0.8rem; font-weight: 800; top: -0.5em; }

    @media (max-width: 991px) {
        .fe-booking-grid { grid-template-columns: 1fr; display: flex; flex-direction: column; }
        .fe-summary-card { position: static; }
        .fe-booking-main { order: 2; }
        .fe-booking-sidebar { order: 1; margin-bottom: 20px; }
    }

    @media (max-width: 767px) {
        .fe-form-row { grid-template-columns: 1fr; gap: 15px; }
        .fe-card-body, .fe-summary-header, .fe-summary-body { padding: 20px; }
        .fe-booking-header { padding: 60px 0 80px; }
        .fe-booking-actions { flex-direction: column; gap: 15px; }
        .fe-btn { width: 100%; }
    }
</style>
@endpush

@push('scripts')
<script>
    function updateFileName(input, index) {
        const nameLabel = document.getElementById('file_name_' + index);
        if(input.files && input.files.length > 0) {
            nameLabel.innerHTML = '<i class="fas fa-check-circle" style="color: #10b981; margin-right: 5px;"></i> ' + input.files[0].name;
        } else {
            nameLabel.innerHTML = '{{ __("Click to upload passport copy") }}';
        }
    }
    
    // Prevent double submission
    document.getElementById('tripBookingForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> {{ __("Processing...") }}';
        btn.disabled = true;
    });
</script>
@endpush
