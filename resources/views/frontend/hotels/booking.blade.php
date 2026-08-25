@extends('frontend.layouts.app')

@section('title', __('Complete Your Booking'))

@section('content')
<div class="fe-page-header fe-booking-header">
    <div class="fe-container">
        <h1>{{ __('Complete Your Booking') }}</h1>
        <p>{{ __('Please enter the guest details exactly as they appear on their passports/IDs.') }}</p>
    </div>
</div>

<div class="fe-container" style="margin-top: -40px; margin-bottom: 80px;">
    {{-- Alerts --}}
    @if(session('error'))
        <div class="alert alert-danger" style="background: #fee2e2; border: 1px solid #fecaca; color: #b91c1c; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 700;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success" style="background: #dcfce7; border: 1px solid #bbf7d0; color: #15803d; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 700;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger" style="background: #fee2e2; border: 1px solid #fecaca; color: #b91c1c; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-inline-start: 20px; font-weight: 700;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
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
                        {{ $currLocale == 'ar' ? 'تسجيل الدخول مطلوب لتأكيد وحفظ بيانات الحجز الفندقي' : 'Sign in required to complete and secure your hotel booking' }}
                    </h4>
                    <p class="fe-guest-auth-desc">
                        {{ $currLocale == 'ar' 
                            ? 'يمكنك تعبئة ومراجعة بيانات النزلاء الآن بكل حرية، وسيتعين عليك تسجيل الدخول أو إنشاء حساب سريع عند الخطوة الأخيرة لحفظ التذاكر ومتابعة الدفع.' 
                            : 'You can freely fill and review guest details now. You will need to sign in or create a quick account at the final step to save your vouchers and complete payment.' }}
                    </p>
                    
                    <div class="fe-guest-auth-perks">
                        <div class="fe-perk-item">
                            <i class="fas fa-bolt text-warning"></i>
                            <span>{{ $currLocale == 'ar' ? 'حفظ تلقائي لبيانات النزلاء' : 'Auto-save booking details' }}</span>
                        </div>
                        <div class="fe-perk-item">
                            <i class="fas fa-shield-alt text-success"></i>
                            <span>{{ $currLocale == 'ar' ? 'تأكيد ودفع مشفر 100%' : '100% Encrypted Checkout' }}</span>
                        </div>
                        <div class="fe-perk-item">
                            <i class="fas fa-receipt text-primary"></i>
                            <span>{{ $currLocale == 'ar' ? 'إصدار فوري لقسيمة الفندق' : 'Instant Hotel Voucher' }}</span>
                        </div>
                    </div>
                </div>

                <div class="fe-guest-auth-cta-box">
                    <a href="{{ route('login', ['return_url' => url()->full()]) }}" class="fe-btn-guest-login btn-save-draft-and-login" onclick="if(window.hotelDraftManager) window.hotelDraftManager.saveDraft();">
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

    <form action="{{ route('hotels.book.process') }}" method="POST" id="hotelBookingForm">
        @csrf
        {{-- Hidden Params from Search/Selection --}}
        {{-- Hidden Params from Search/Selection --}}
        @foreach($details as $key => $value)
            @if(!in_array($key, ['pax', '_token']))
                @if(is_array($value))
                    @foreach($value as $subKey => $subValue)
                        @if(is_array($subValue))
                            @foreach($subValue as $subSubKey => $subSubValue)
                                @if(is_array($subSubValue))
                                    @foreach($subSubValue as $s4k => $s4v)
                                        <input type="hidden" name="{{ $key }}[{{ $subKey }}][{{ $subSubKey }}][{{ $s4k }}]" value="{{ $s4v }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}[{{ $subKey }}][{{ $subSubKey }}]" value="{{ $subSubValue }}">
                                @endif
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}[{{ $subKey }}]" value="{{ $subValue }}">
                        @endif
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endif
        @endforeach

        <div class="fe-booking-grid">
            {{-- Left Side: Passenger Forms --}}
            <div class="fe-booking-main">
                @php 
                    $distributionMode = $details['distribution_mode'] ?? 'auto';
                    $roomsCount = (int)($details['rooms'] ?? (isset($details['occupancy']) ? count($details['occupancy']) : 1));
                    $roomsOccupancy = [];

                    // Extract and split room names if they are concatenated with |t|
                    $rawRoomName = $details['roomName'] ?? __('Standard Room');
                    $availableRoomNames = explode('|t|', $rawRoomName);

                    if ($distributionMode === 'manual' && isset($details['occupancy'])) {
                        for ($i = 0; $i < $roomsCount; $i++) {
                            $roomsOccupancy[] = [
                                'room_no' => $i + 1,
                                'adult' => (int)($details["occupancy"][$i]["adult"] ?? 1),
                                'child' => (int)($details["occupancy"][$i]["child"] ?? 0),
                                'child_ages' => $details["occupancy"][$i]["child_age"] ?? []
                            ];
                        }
                    } else {
                        // Automatic distribution logic
                        $totalAdults = (int)($details['adults'] ?? 2);
                        $totalChilds = (int)($details['childs'] ?? 0);
                        $childAges = $details['childAge'] ?? [];
                        
                        $remainingAdults = $totalAdults;
                        $remainingChilds = $totalChilds;
                        $ageIndex = 0;

                        for ($i = 1; $i <= $roomsCount; $i++) {
                            $roomAdults = ceil($remainingAdults / ($roomsCount - $i + 1));
                            $remainingAdults -= $roomAdults;
                            
                            $roomChilds = ceil($remainingChilds / ($roomsCount - $i + 1));
                            $remainingChilds -= $roomChilds;

                            $roomAges = [];
                            for ($j = 0; $j < $roomChilds; $j++) {
                                $roomAges[] = $childAges[$ageIndex++] ?? 0;
                            }
                            
                            $roomsOccupancy[] = [
                                'room_no' => $i,
                                'adult' => $roomAdults,
                                'child' => $roomChilds,
                                'child_ages' => $roomAges
                            ];
                        }
                    }
                @endphp

                @foreach($roomsOccupancy as $room)
                    @php 
                        $roomIdx = $loop->iteration; 
                        // Pick the specific room name for this index, fallback to first one or raw name
                        $currentRoomName = $availableRoomNames[$roomIdx - 1] ?? ($availableRoomNames[0] ?? $rawRoomName);
                    @endphp
                    <div class="fe-booking-card">
                        <div class="fe-card-header">
                            <i class="fas fa-bed"></i> 
                            <h3>{{ __('Room') }} {{ $roomIdx }} - {{ $currentRoomName }}</h3>
                        </div>
                        <div class="fe-card-body">
                            {{-- Adults --}}
                            @for($a = 1; $a <= $room['adult']; $a++)
                                <h4 class="fe-guest-title" style="margin-top: {{ $a > 1 ? '30px' : '0' }}">
                                    {{ __('Adult') }} {{ $a }} {{ ($roomIdx == 1 && $a == 1) ? '(' . __('Lead Guest') . ')' : '' }}
                                </h4>
                                <div class="fe-form-row">
                                    <div class="fe-form-group">
                                        <label class="fe-label">{{ __('Title') }}</label>
                                        <select name="pax[{{ $roomIdx }}][adult][{{ $a }}][title]" class="fe-input" required>
                                            <option value="Mr">{{ __('Mr.') }}</option>
                                            <option value="Ms">{{ __('Ms.') }}</option>
                                            <option value="Mrs">{{ __('Mrs.') }}</option>
                                        </select>
                                    </div>
                                    <div class="fe-form-group">
                                        <label class="fe-label">{{ __('First Name') }}</label>
                                        <input type="text" name="pax[{{ $roomIdx }}][adult][{{ $a }}][firstName]" value="{{ old('pax.'.$roomIdx.'.adult.'.$a.'.firstName') }}" class="fe-input" placeholder="{{ __('First name') }}" required>
                                    </div>
                                    <div class="fe-form-group">
                                        <label class="fe-label">{{ __('Last Name') }}</label>
                                        <input type="text" name="pax[{{ $roomIdx }}][adult][{{ $a }}][lastName]" value="{{ old('pax.'.$roomIdx.'.adult.'.$a.'.lastName') }}" class="fe-input" placeholder="{{ __('Last name') }}" required>
                                    </div>
                                </div>
                            @endfor

                            {{-- Children --}}
                            @if($room['child'] > 0)
                                @for($c = 1; $c <= $room['child']; $c++)
                                    @php $childAge = $room['child_ages'][$c-1] ?? 0; @endphp
                                    <h4 class="fe-guest-title" style="margin-top: 30px;">
                                        {{ __('Child') }} {{ $c }} ({{ __('Age') }}: {{ $childAge }})
                                    </h4>
                                    <div class="fe-form-row">
                                        <div class="fe-form-group">
                                            <label class="fe-label">{{ __('Title') }}</label>
                                            <select name="pax[{{ $roomIdx }}][child][{{ $c }}][title]" class="fe-input" required>
                                                <option value="Mr">{{ __('Master') }}</option>
                                                <option value="Ms">{{ __('Miss') }}</option>
                                            </select>
                                        </div>
                                        <div class="fe-form-group">
                                            <label class="fe-label">{{ __('First Name') }}</label>
                                            <input type="text" name="pax[{{ $roomIdx }}][child][{{ $c }}][firstName]" value="{{ old('pax.'.$roomIdx.'.child.'.$c.'.firstName') }}" class="fe-input" placeholder="{{ __('First name') }}" required>
                                        </div>
                                        <div class="fe-form-group">
                                            <label class="fe-label">{{ __('Last Name') }}</label>
                                            <input type="text" name="pax[{{ $roomIdx }}][child][{{ $c }}][lastName]" value="{{ old('pax.'.$roomIdx.'.child.'.$c.'.lastName') }}" class="fe-input" placeholder="{{ __('Last name') }}" required>
                                        </div>
                                        <input type="hidden" name="pax[{{ $roomIdx }}][child][{{ $c }}][age]" value="{{ $childAge }}">
                                    </div>
                                @endfor
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="fe-booking-card">
                    <div class="fe-card-header">
                        <i class="fas fa-envelope"></i>
                        <h3>{{ __('Contact Details') }}</h3>
                    </div>
                    <div class="fe-card-body">
                        <div class="fe-form-row fe-form-row-2">
                            <div class="fe-form-group">
                                <label class="fe-label">{{ __('Email Address') }}</label>
                                <input type="email" name="customerEmail" class="fe-input" placeholder="{{ __('Your email for confirmation') }}" required value="{{ old('customerEmail', auth()->user()->email ?? '') }}">
                            </div>
                            <div class="fe-form-group">
                                <label class="fe-label">{{ __('Phone Number') }}</label>
                                <input type="tel" name="customerPhone" class="fe-input" placeholder="{{ __('Phone number') }}" required value="{{ old('customerPhone', auth()->user()->phone ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fe-booking-action">
                    @auth
                        @if(!auth()->user()->canBookDirectly())
                            <div class="alert alert-warning border-0 rounded-4 p-3 text-start mb-3 shadow-sm" style="font-size: 0.88rem; background: #fffbeb; color: #92400e;">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                {{ app()->getLocale() == 'ar' ? 'حسابك الحالي (وكيل / مسؤول) مخصص لإدارة العمليات ولا يمكنه إنشاء حجوزات استهلاكية مباشرة. يرجى استخدام حساب عميل.' : 'Your account is an Agent/Admin management account and cannot place consumer bookings.' }}
                            </div>
                        @else
                            <button type="submit" class="fe-btn fe-btn-primary fe-btn-lg fe-btn-block">
                                <i class="fas fa-check-circle"></i> {{ __('Proceed to Payment') }}
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login', ['return_url' => url()->full()]) }}" class="fe-btn fe-btn-primary fe-btn-lg fe-btn-block btn-save-draft-and-login" onclick="if(window.hotelDraftManager) window.hotelDraftManager.saveDraft();" style="background: linear-gradient(135deg, #f59e0b, #d97706); text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: none;">
                            <i class="fas fa-sign-in-alt"></i> {{ __('Sign in to complete booking') }}
                        </a>
                    @endauth
                    <p class="fe-terms-fine">{{ __('By clicking "Proceed to Payment", you agree to the') }} <a href="#">{{ __('Terms & Conditions') }}</a> {{ __('and') }} <a href="#">{{ __('Booking Policy') }}</a>.</p>
                </div>
            </div>

            {{-- Right Side: Summary --}}
            <div class="fe-booking-sidebar">
                <div class="fe-summary-card">
                    <div class="fe-summary-header">
                        <h3>{{ __('Booking Summary') }}</h3>
                    </div>
                    <div class="fe-summary-body">
                        <div class="fe-summary-hotel">
                            <h4 class="fe-hotel-name">{{ $details['hotelName'] ?? 'Hotel' }}</h4>
                            <div class="fe-hotel-location">
                                <i class="fas fa-map-marker-alt"></i> {{ $details['cityName'] ?? '' }}, {{ $details['countryName'] ?? '' }}
                            </div>
                        </div>
                        
                        <div class="fe-summary-details">
                            <div class="fe-summary-item">
                                <span class="label">{{ __('Check-in') }}</span>
                                <span class="value">{{ $details['checkIn'] ?? '' }}</span>
                            </div>
                            <div class="fe-summary-item">
                                <span class="label">{{ __('Check-out') }}</span>
                                <span class="value">{{ $details['checkOut'] ?? '' }}</span>
                            </div>
                            <div class="fe-summary-item">
                                <span class="label">{{ __('Guests') }}</span>
                                <span class="value">{{ $details['adults'] ?? 1 }} {{ __('Adults') }}, {{ $details['childs'] ?? 0 }} {{ __('Children') }}</span>
                            </div>
                            <div class="fe-summary-item">
                                <span class="label">{{ __('Duration') }}</span>
                                <span class="value">
                                    @if(isset($details['checkIn']) && isset($details['checkOut']))
                                        @php 
                                            $checkin = \Carbon\Carbon::parse($details['checkIn']);
                                            $checkout = \Carbon\Carbon::parse($details['checkOut']);
                                            $nights = $checkin->diffInDays($checkout);
                                        @endphp
                                        {{ $nights }} {{ __('Nights') }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="fe-summary-room">
                            <div class="room-title">{{ $details['roomName'] ?? '' }}</div>
                            <div class="room-meta"><i class="fas fa-utensils"></i> {{ $details['boardType'] ?? __('Room Only') }}</div>
                        </div>

                        <div class="fe-summary-total">
                            <div class="total-label">{{ __('Total for :n Nights', ['n' => $nights ?? 1]) }}</div>
                            <div class="total-value">
                                <span class="currency">{{ $details['currency'] ?? 'SAR' }}</span>
                                <span class="amount">{{ number_format($details['total_amount'] ?? 0, 2) }}</span>
                            </div>
                            <p class="total-note">{{ __('Includes all taxes and fees') }}</p>
                        </div>
                    </div>
                </div>

                @if(isset($details['cancelPolicy']))
                <div class="fe-policy-card">
                    <h4><i class="fas fa-info-circle"></i> {{ __('Cancellation Policy') }}</h4>
                    <p>{{ $details['cancelPolicy'] }}</p>
                </div>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .fe-booking-header { background: linear-gradient(135deg, var(--primary) 0%, #2c3e50 100%); padding: 80px 0 100px; color: white; text-align: center; }
    .fe-booking-header h1 { color: white; margin-bottom: 10px; font-weight: 900; }

    .fe-booking-grid { display: grid; grid-template-columns: 1fr 380px; gap: 30px; }
    .fe-booking-card { background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid var(--gray-100); margin-bottom: 30px; overflow: hidden; }
    .fe-card-header { background: var(--gray-50); padding: 20px 24px; border-bottom: 1px solid var(--gray-100); display: flex; align-items: center; gap: 12px; }
    .fe-card-header i { color: var(--primary); font-size: 1.2rem; }
    .fe-card-header h3 { font-size: 1.15rem; font-weight: 800; margin: 0; color: var(--dark); }
    .fe-card-body { padding: 30px; }

    .fe-guest-title { font-size: 0.9rem; font-weight: 800; margin-bottom: 20px; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px dashed var(--gray-200); padding-bottom: 10px; }
    .fe-form-row { display: grid; grid-template-columns: 1fr 2fr 2fr; gap: 20px; margin-bottom: 20px; }
    .fe-label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--dark); margin-bottom: 8px; }
    .fe-input { width: 100%; height: 50px; background: var(--gray-50); border: 1.5px solid var(--gray-100); border-radius: 12px; padding: 0 16px; font-weight: 600; transition: all 0.2s; }
    .fe-input:focus { border-color: var(--primary); outline: none; background: white; box-shadow: 0 0 0 4px var(--primary-50); }

    /* SUMMARY CARD */
    .fe-summary-card { background: white; border-radius: 20px; box-shadow: 0 4px 25px rgba(0,0,0,0.08); border: 1px solid var(--gray-100); position: sticky; top: 100px; }
    .fe-summary-header { padding: 24px; border-bottom: 1px solid var(--gray-100); background: var(--dark); color: white; border-radius: 20px 20px 0 0; }
    .fe-summary-header h3 { margin: 0; font-size: 1.2rem; font-weight: 800; color: white; }
    .fe-summary-body { padding: 24px; }
    .fe-hotel-name { font-size: 1.3rem; font-weight: 900; color: var(--dark); margin-bottom: 6px; }
    .fe-hotel-location { color: var(--gray-500); font-size: 0.85rem; margin-bottom: 20px; }
    
    .fe-summary-details { border-top: 1px solid var(--gray-100); border-bottom: 1px solid var(--gray-100); padding: 20px 0; margin-bottom: 20px; display: flex; flex-direction: column; gap: 12px; }
    .fe-summary-item { display: flex; justify-content: space-between; font-size: 0.9rem; }
    .fe-summary-item .label { color: var(--gray-500); font-weight: 600; }
    .fe-summary-item .value { color: var(--dark); font-weight: 800; }

    .fe-summary-room { background: var(--gray-50); padding: 15px; border-radius: 12px; margin-bottom: 24px; }
    .room-title { font-weight: 800; font-size: 0.95rem; color: var(--primary); margin-bottom: 4px; }
    .room-meta { font-size: 0.8rem; color: var(--gray-600); font-weight: 600; }

    .fe-summary-total { text-align: center; background: var(--primary-50); padding: 24px; border-radius: 15px; }
    .total-label { font-size: 0.85rem; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 8px; }
    .total-value { color: var(--primary); }
    .total-value .currency { font-size: 1.1rem; font-weight: 800; margin-inline-end: 4px; }
    .total-value .amount { font-size: 2.2rem; font-weight: 900; }
    .total-note { font-size: 0.75rem; color: var(--gray-500); margin-top: 8px; font-weight: 600; }

    .fe-policy-card { background: #fffcf0; border: 1px solid #ffeeba; border-radius: 15px; padding: 20px; margin-top: 20px; }
    .fe-policy-card h4 { font-size: 0.95rem; font-weight: 800; color: #856404; margin-bottom: 10px; }
    .fe-policy-card p { font-size: 0.85rem; color: #856404; line-height: 1.5; margin: 0; }

    .fe-booking-action { margin-top: 30px; text-align: center; }
    .fe-terms-fine { font-size: 0.75rem; color: var(--gray-500); margin-top: 15px; }
    .fe-terms-fine a { color: var(--primary); font-weight: 700; }

    /* Responsive form helper */
    .fe-form-row-2 { grid-template-columns: 1fr 1fr; }

    /* Mobile Responsiveness */
    @media (max-width: 991px) {
        .fe-booking-grid { grid-template-columns: 1fr; gap: 20px; display: flex; flex-direction: column; }
        .fe-summary-card { position: static; }
        .fe-booking-main { order: 2; }
        .fe-booking-sidebar { order: 1; }
    }

    @media (max-width: 767px) {
        .fe-form-row, .fe-form-row-2 { grid-template-columns: 1fr; gap: 15px; }
        .fe-card-body, .fe-summary-header, .fe-summary-body { padding: 20px; }
        .fe-booking-header { padding: 60px 0 80px; }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof BookingDraftManager !== 'undefined') {
            window.hotelDraftManager = new BookingDraftManager('#hotelBookingForm', 'hotel_{{ md5(url()->full()) }}');
        }
    });
</script>
@endpush
