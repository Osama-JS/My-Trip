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
    <form action="{{ route('flights.book.process') }}" method="POST" id="flightBookingForm">
        @csrf
        <input type="hidden" name="flight_session_id" value="{{ $details['session_id'] ?? '' }}">
        <input type="hidden" name="fare_source_code" value="{{ $details['fare_source_code'] ?? '' }}">
        <input type="hidden" name="total_amount" value="{{ $details['total_amount'] ?? 0 }}">
        <input type="hidden" name="from" value="{{ $details['from'] ?? '' }}">
        <input type="hidden" name="to" value="{{ $details['to'] ?? '' }}">
        <input type="hidden" name="departDate" value="{{ $details['departDate'] ?? '' }}">
        <input type="hidden" name="airline" value="{{ $details['airline'] ?? '' }}">
        <input type="hidden" name="dep_time" value="{{ $details['dep_time'] ?? '' }}">
        <input type="hidden" name="arr_time" value="{{ $details['arr_time'] ?? '' }}">
        <input type="hidden" name="stops" value="{{ $details['stops'] ?? 0 }}">
        <input type="hidden" name="duration" value="{{ $details['duration'] ?? '' }}">
        @if(!empty($details['segments']) && is_array($details['segments']))
            @foreach($details['segments'] as $i => $seg)
                <input type="hidden" name="segments[{{ $i }}][from]" value="{{ $seg['from'] ?? '' }}">
                <input type="hidden" name="segments[{{ $i }}][to]" value="{{ $seg['to'] ?? '' }}">
                <input type="hidden" name="segments[{{ $i }}][dep]" value="{{ $seg['dep'] ?? '' }}">
                <input type="hidden" name="segments[{{ $i }}][arr]" value="{{ $seg['arr'] ?? '' }}">
            @endforeach
        @endif
        
        @php
            $adultCount = (int)(is_array($details['adults'] ?? 1) ? reset($details['adults']) : ($details['adults'] ?? 1));
            $childCount = (int)(is_array($details['childs'] ?? 0) ? reset($details['childs']) : ($details['childs'] ?? 0));
            $infantCount = (int)(is_array($details['infants'] ?? 0) ? reset($details['infants']) : ($details['infants'] ?? 0));
            $totalPax = $adultCount + $childCount + $infantCount;
        @endphp
        
        <input type="hidden" name="adults" value="{{ $adultCount }}">
        <input type="hidden" name="childs" value="{{ $childCount }}">
        <input type="hidden" name="infants" value="{{ $infantCount }}">

        <div class="fe-booking-grid">
            {{-- Main Form Content --}}
            <div class="fe-booking-main">
                
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
                        @include('frontend.flights.partials.pax-fields', ['type' => 'adult', 'index' => $paxIndex++, 'num' => $i + 1, 'countries' => $countries])
                    @endfor

                    {{-- Children --}}
                    @for($i = 0; $i < $childCount; $i++)
                        @include('frontend.flights.partials.pax-fields', ['type' => 'child', 'index' => $paxIndex++, 'num' => $i + 1, 'countries' => $countries])
                    @endfor

                    {{-- Infants --}}
                    @for($i = 0; $i < $infantCount; $i++)
                        @include('frontend.flights.partials.pax-fields', ['type' => 'infant', 'index' => $paxIndex++, 'num' => $i + 1, 'countries' => $countries])
                    @endfor
                </div>

                {{-- ═══ EXTRA SERVICES SECTION (Baggage / Meals / Seats) ═══ --}}
                <div class="fe-booking-card" id="extraServicesCard" style="display:none;">
                    <div class="fe-card-header">
                        <i class="fas fa-concierge-bell"></i>
                        <h3>{{ __('Extra Services') }}</h3>
                        <span style="margin-inline-start:auto;font-size:0.75rem;color:var(--gray-400);font-weight:600;">{{ __('Optional') }}</span>
                    </div>
                    <div class="fe-card-body" id="extraServicesBody">
                        <div style="text-align:center;padding:30px;color:var(--gray-400);">
                            <i class="fas fa-spinner fa-spin fa-2x" style="margin-bottom:12px;"></i>
                            <p style="font-weight:600;">{{ __('Loading available services...') }}</p>
                        </div>
                    </div>
                </div>

                <div class="fe-booking-action">
                    <button type="submit" class="fe-btn fe-btn-primary fe-btn-lg fe-btn-block">
                        <i class="fas fa-check-circle"></i> {{ __('Complete Reservation') }}
                    </button>
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
                    <div class="fe-summary-header">
                        <h3>{{ __('Flight Summary') }}</h3>
                    </div>
                    <div class="fe-summary-body">
                        <div class="fe-summary-flight">
                            <div class="summary-route-visual">
                                <div class="city">
                                    <span class="code">{{ $details['from'] ?? '---' }}</span>
                                </div>
                                <div class="path">
                                    <span class="line"></span>
                                    <i class="fas fa-plane"></i>
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
                                    <span class="label"><i class="far fa-calendar-alt"></i> {{ __('Departure') }}</span>
                                    <span class="value">{{ $details['departDate'] ?? '' }}</span>
                                </div>
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
                                
                                @if(!empty($details['segments']) && is_array($details['segments']))
                                    <div class="fe-summary-segments" style="margin: 10px 0 15px; background: var(--gray-50); border-radius: 8px; padding: 12px; font-size: 0.85rem; border: 1px dashed var(--gray-200);">
                                        <div style="font-weight: 700; color: var(--dark); margin-bottom: 8px; font-size: 0.8rem; text-transform: uppercase;">{{ __('Flight Route Details') }}</div>
                                        @foreach($details['segments'] as $index => $seg)
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: {{ $loop->last ? '0' : '8px' }};">
                                                <span style="font-weight: 700; color: var(--primary);">
                                                    {{ $seg['from'] ?? '' }} 
                                                    <i class="fas fa-arrow-right" style="font-size:0.7rem; margin:0 6px; color: var(--gray-400);"></i> 
                                                    {{ $seg['to'] ?? '' }}
                                                </span>
                                                <span style="color: var(--dark-600); font-weight: 600;">{{ $seg['dep'] ?? '' }} - {{ $seg['arr'] ?? '' }}</span>
                                            </div>
                                        @endforeach
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
                                    <span class="label"><i class="fas fa-users"></i> {{ __('Travelers') }}</span>
                                    <span class="value">{{ $totalPax }} {{ __('Person(s)') }}</span>
                                </div>
                                <div class="fe-summary-item">
                                    <span class="label"><i class="fas fa-couch"></i> {{ __('Class') }}</span>
                                    <span class="value">{{ $details['class'] ?? 'Economy' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="fe-summary-total" style="margin-top: 20px;">
                            <div class="total-label">{{ __('Total Amount') }}</div>
                            <div class="total-value">
                                <span class="currency">SAR</span>
                                <span class="amount">{{ number_format(floatval($details['total_amount'] ?? 0), 2) }}</span>
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
@endsection

@push('styles')
<style>
    .fe-booking-header { background: linear-gradient(135deg, var(--primary) 0%, #1a3a5a 100%); padding: 80px 0 100px; color: white; text-align: center; }
    .fe-booking-header h1 { color: white; margin-bottom: 10px; font-weight: 900; font-size: 2.5rem; }
    .fe-booking-header p { opacity: 0.9; font-size: 1.1rem; color: white; }

    .fe-booking-grid { display: grid; grid-template-columns: 1fr 380px; gap: 30px; align-items: start; }
    @media (max-width: 1024px) {
        .fe-booking-grid { grid-template-columns: 1fr; }
        .fe-booking-sidebar { order: -1; }
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
    .es-options { display: flex; flex-wrap: wrap; gap: 10px; }
    .es-option {
        display: flex; align-items: center; gap: 8px;
        background: var(--gray-50); border: 1.5px solid var(--gray-200);
        border-radius: 12px; padding: 10px 14px; cursor: pointer;
        transition: all 0.2s; font-size: 0.85rem; font-weight: 600;
    }
    .es-option input[type="radio"] { accent-color: var(--primary); }
    .es-option:has(input:checked) {
        border-color: var(--primary); background: var(--primary-50);
        color: var(--primary);
    }
    .es-option-price { color: var(--primary); font-weight: 800; font-size: 0.8rem; }
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
</style>
@endpush
@push('scripts')
<script>
$(document).ready(function() {
    // ═══ EXTRA SERVICES LOADER ═══
    const sessionId    = $('input[name="flight_session_id"]').val();
    const fareCode     = $('input[name="fare_source_code"]').val();
    const totalPax     = {{ $totalPax }};
    let extraServicesTotal = 0;

    if (sessionId && fareCode) {
        $('#extraServicesCard').show();
        $.ajax({
            url: '{{ route("api.flights.extra-services") }}',
            method: 'POST',
            data: { session_id: sessionId, fare_source_code: fareCode, _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (!res.error && res.data && res.data.data && res.data.data.length > 0) {
                    renderExtraServices(res.data.data);
                } else {
                    $('#extraServicesBody').html('<div class="es-no-services"><i class="fas fa-info-circle" style="margin-inline-end:8px;"></i>{{ __("No extra services available for this flight.") }}</div>');
                }
            },
            error: function() {
                $('#extraServicesCard').hide();
            }
        });
    }

    function renderExtraServices(services) {
        // Group by type then by flight_type
        const grouped = {};
        services.forEach(svc => {
            const key = svc.type + '_' + svc.flight_type;
            if (!grouped[key]) grouped[key] = { label: svc.type, flight: svc.flight_type, items: [] };
            grouped[key].items.push(svc);
        });

        const typeIcons = {
            baggage: 'fa-suitcase-rolling',
            meal:    'fa-utensils',
            seat:    'fa-chair',
            unknown: 'fa-concierge-bell',
        };
        const typeLabels = {
            baggage: '{{ __("Extra Baggage") }}',
            meal:    '{{ __("Meal Preference") }}',
            seat:    '{{ __("Seat Selection") }}',
            unknown: '{{ __("Additional Service") }}',
        };
        const flightLabels = {
            outbound: '{{ __("Outbound") }}',
            inbound:  '{{ __("Return") }}',
        };

        let html = '';
        Object.entries(grouped).forEach(([key, group]) => {
            const icon  = typeIcons[group.label] || typeIcons.unknown;
            const label = (typeLabels[group.label] || group.label) + (group.flight === 'inbound' ? ' — ' + flightLabels.inbound : '');

            html += `<div class="es-group">
                <div class="es-group-title"><i class="fas ${icon}"></i> ${label}</div>`;

            for (let paxIdx = 0; paxIdx < totalPax; paxIdx++) {
                const fieldName = group.flight === 'inbound'
                    ? `passengers[${paxIdx}][extra_services_inbound][]`
                    : `passengers[${paxIdx}][extra_services_outbound][]`;

                html += `<div class="es-passenger-row">
                    <div class="es-pax-label"><i class="fas fa-user-circle"></i> {{ __("Passenger") }} #${paxIdx + 1}</div>
                    <div class="es-options">
                        <label class="es-option">
                            <input type="radio" name="extra_${key}_pax${paxIdx}" value="" data-price="0" checked onchange="updateExtraTotal()">
                            <span>{{ __("None") }}</span>
                        </label>`;

                group.items.forEach(svc => {
                    html += `<label class="es-option">
                        <input type="radio"
                               name="extra_${key}_pax${paxIdx}"
                               value="${svc.code}"
                               data-field="${fieldName}"
                               data-price="${svc.price}"
                               onchange="updateExtraTotal()">
                        <span>${svc.description}</span>
                        <span class="es-option-price">+${svc.price} ${svc.currency}</span>
                    </label>`;
                });

                html += `</div></div>`; // .es-options + .es-passenger-row
            }

            html += `</div>`; // .es-group
        });

        html += `<div id="esTotal" class="es-total-badge" style="display:none;">
            <i class="fas fa-tag"></i>
            {{ __("Extra Services Total") }}: <strong id="esTotalValue">0 SAR</strong>
        </div>`;

        $('#extraServicesBody').html(html);
    }

    window.updateExtraTotal = function() {
        let total = 0;
        $('input[name^="extra_"]:checked').each(function() {
            const price = parseFloat($(this).data('price')) || 0;
            total += price;

            // Sync to actual form field
            const fieldName = $(this).data('field');
            if (fieldName && $(this).val()) {
                $(`input[name="${fieldName}"]`).remove();
                $('<input>').attr({ type: 'hidden', name: fieldName, value: $(this).val() }).appendTo('#flightBookingForm');
            }
        });
        extraServicesTotal = total;
        if (total > 0) {
            $('#esTotal').show();
            $('#esTotalValue').text(new Intl.NumberFormat().format(total) + ' SAR');
        } else {
            $('#esTotal').hide();
        }
    };

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

    // Initialize Flatpickr for Passport Expiry (Future only)
    flatpickr(".expiry-picker", {
        dateFormat: "Y-m-d",
        minDate: "today",
        locale: "{{ app()->getLocale() }}",
    });

    // Initialize Flatpickr for Passport Issue Date (Past only)
    flatpickr(".issue-date-picker", {
        dateFormat: "Y-m-d",
        maxDate: "today",
        locale: "{{ app()->getLocale() }}",
    });
});
</script>
@endpush
