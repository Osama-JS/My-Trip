@extends('frontend.layouts.app')

@section('title', __('Flight Search Results'))

@section('content')
    {{-- Page Header --}}
    <section class="page-header" style="position: relative; padding-top: calc(var(--space-24) + 60px); padding-bottom: var(--space-10); background: var(--color-primary); overflow: hidden;">
        <div style="position: absolute; inset: 0; background: var(--gradient-primary); z-index: 0;"></div>
        <div class="container" style="position: relative; z-index: 1;">
            <div class="text-center" style="color: white !important;">
                <h1 style="font-size: var(--text-4xl); font-weight: var(--font-bold); margin-bottom: var(--space-4); color: white !important;">
                    {{ __('Flight Search Results') }}
                </h1>
                <p style="font-size: var(--text-lg); opacity: 0.9; max-width: 600px; margin: 0 auto; color: white !important;">
                    {{ $searchParams['from'] ?? '' }} <i class="fas fa-long-arrow-alt-{{ app()->isLocale('ar') ? 'left' : 'right' }}"></i> {{ $searchParams['to'] ?? '' }}
                </p>
            </div>

            {{-- Breadcrumb --}}
            <nav class="breadcrumb" style="justify-content: center; margin-top: var(--space-6);" aria-label="Breadcrumb">
                <span class="breadcrumb-item">
                    <a href="{{ route('home') }}" style="color: rgba(255,255,255,0.7) !important;">{{ __('Home') }}</a>
                </span>
                <span class="breadcrumb-separator" style="color: rgba(255,255,255,0.5) !important;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </span>
                <span class="breadcrumb-item">
                    <a href="{{ route('flights') }}" style="color: rgba(255,255,255,0.7) !important;">{{ __('Flights') }}</a>
                </span>
                <span class="breadcrumb-separator" style="color: rgba(255,255,255,0.5) !important;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </span>
                <span class="breadcrumb-item active" style="color: white !important;">{{ __('Results') }}</span>
            </nav>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="trips-grid-wrapper">

                {{-- Premium Sidebar Filters --}}
                <aside id="filtersSidebar" class="fe-results-sidebar">
                    <div class="fe-sidebar-card">
                        <div class="fe-sidebar-header">
                            <h3><i class="fas fa-filter"></i> {{ __('Filters') }}</h3>
                            <button type="button" onclick="resetFilters()" class="fe-reset-btn">
                                {{ __('Reset All') }}
                            </button>
                        </div>

                        {{-- Price Range --}}
                        <div class="fe-filter-group">
                            <div class="fe-filter-label">
                                <span><i class="fas fa-tag"></i> {{ __('Price Range') }}</span>
                                <span id="priceValue" class="fe-val-highlight"></span>
                            </div>
                            <div class="fe-slider-wrapper">
                                <input type="range" id="priceRange" class="fe-range-slider">
                                <div class="fe-range-labels">
                                    <span id="minPriceLabel"></span>
                                    <span id="maxPriceLabel"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Stops --}}
                        <div class="fe-filter-group">
                            <label class="fe-filter-label"><i class="fas fa-plane"></i> {{ __('Stops') }}</label>
                            <div class="fe-check-list">
                                <label class="fe-custom-check">
                                    <input type="checkbox" class="filter-stop" value="0" checked>
                                    <span class="checkmark"></span>
                                    <span class="label-text">{{ __('Non-stop') }}</span>
                                </label>
                                <label class="fe-custom-check">
                                    <input type="checkbox" class="filter-stop" value="1" checked>
                                    <span class="checkmark"></span>
                                    <span class="label-text">{{ __('1 Stop') }}</span>
                                </label>
                                <label class="fe-custom-check">
                                    <input type="checkbox" class="filter-stop" value="2" checked>
                                    <span class="checkmark"></span>
                                    <span class="label-text">{{ __('2+ Stops') }}</span>
                                </label>
                            </div>
                        </div>

                        {{-- Airlines --}}
                        <div class="fe-filter-group" id="airlinesFilterSection">
                            <label class="fe-filter-label"><i class="fas fa-building"></i> {{ __('Airlines') }}</label>
                            <div class="fe-check-list airlines-scroll" id="airlinesList">
                                {{-- Dynamically populated --}}
                            </div>
                        </div>

                        {{-- Time of Day --}}
                        <div class="fe-filter-group">
                            <label class="fe-filter-label"><i class="fas fa-clock"></i> {{ __('Departure Time') }}</label>
                            <div class="fe-check-list">
                                <label class="fe-custom-check">
                                    <input type="checkbox" class="filter-time" value="morning" checked>
                                    <span class="checkmark"></span>
                                    <span class="label-text">{{ __('Morning') }} <small>(06:00-12:00)</small></span>
                                </label>
                                <label class="fe-custom-check">
                                    <input type="checkbox" class="filter-time" value="afternoon" checked>
                                    <span class="checkmark"></span>
                                    <span class="label-text">{{ __('Afternoon') }} <small>(12:00-18:00)</small></span>
                                </label>
                                <label class="fe-custom-check">
                                    <input type="checkbox" class="filter-time" value="evening" checked>
                                    <span class="checkmark"></span>
                                    <span class="label-text">{{ __('Evening') }} <small>(18:00-00:00)</small></span>
                                </label>
                                <label class="fe-custom-check">
                                    <input type="checkbox" class="filter-time" value="night" checked>
                                    <span class="checkmark"></span>
                                    <span class="label-text">{{ __('Night') }} <small>(00:00-06:00)</small></span>
                                </label>
                            </div>
                        </div>

                        {{-- Duration slider --}}
                        <div class="fe-filter-group">
                            <div class="fe-filter-label">
                                <span><i class="fas fa-hourglass-half"></i> {{ __('Max Duration') }}</span>
                                <span id="durationValue" class="fe-val-highlight"></span>
                            </div>
                            <div class="fe-slider-wrapper">
                                <input type="range" id="durationRange" class="fe-range-slider">
                            </div>
                        </div>
                    </div>
                </aside>

                {{-- Flights List --}}
                <div id="flights-container">
                    @php
                        $itineraries = $results['AirSearchResponse']['AirSearchResult']['FareItineraries']['FareItinerary'] ?? [];
                        if (!empty($itineraries) && !is_array(reset($itineraries))) {
                            $itineraries = [$itineraries]; // Wrap single result
                        }
                    @endphp

                    @forelse($itineraries as $index => $itin)
                        @php
                            $fareInfo = $itin['AirItineraryFareInfo'];
                            $price = floatval($fareInfo['ItinTotalFares']['TotalFare']['Amount']);
                            $currency = $fareInfo['ItinTotalFares']['TotalFare']['CurrencyCode'];
                            $validatingCarrier = $itin['ValidatingAirlineCode'];
                            $rawOptions = $itin['OriginDestinationOptions'] ?? [];
                            $options = [];
                            if (isset($rawOptions['OriginDestinationOption'])) {
                                $options = [$rawOptions];
                            } elseif (is_array($rawOptions)) {
                                $options = $rawOptions;
                            }
                            
                            $isRoundTrip = (count($options) > 1) || (($searchParams['journeyType'] ?? '') === 'Return') || !empty($searchParams['returnDate']);
                            $maxStops = 0;
                            $totalDurationMinutes = 0;
                            $firstDepTime = null;
                            $lastArrTime = null;
                            $totalDurationStr = '';
                            $allSegmentsData = [];

                            foreach($options as $optIdx => $opt) {
                                $rawSegs = $opt['OriginDestinationOption'] ?? [];
                                $segs = isset($rawSegs['FlightSegment']) ? [$rawSegs] : (is_array($rawSegs) ? $rawSegs : []);
                                if (empty($segs)) continue;

                                $d1 = \Carbon\Carbon::parse($segs[0]['FlightSegment']['DepartureDateTime']);
                                $d2 = \Carbon\Carbon::parse(end($segs)['FlightSegment']['ArrivalDateTime']);
                                $totalDurationMinutes += $d1->diffInMinutes($d2);

                                if ($optIdx === 0) {
                                    $firstDepTime = $d1->format('H:i');
                                    $totalDurationStr = $d1->diff($d2)->format('%hh %im');
                                }
                                $lastArrTime = $d2->format('H:i');

                                $sCount = count($segs) - 1;
                                if ($sCount > $maxStops) $maxStops = $sCount;

                                $segsCount = count($segs);
                                for ($si = 0; $si < $segsCount; $si++) {
                                    $s = $segs[$si]['FlightSegment'];
                                    $layStr = null;
                                    if ($si < $segsCount - 1) {
                                        $curArr = \Carbon\Carbon::parse($s['ArrivalDateTime']);
                                        $nextDep = \Carbon\Carbon::parse($segs[$si+1]['FlightSegment']['DepartureDateTime']);
                                        $diffM = $curArr->diffInMinutes($nextDep);
                                        $lh = floor($diffM / 60);
                                        $lm = $diffM % 60;
                                        $layStr = ($lh > 0 ? "{$lh}h " : '') . "{$lm}m";
                                    }

                                    $allSegmentsData[] = [
                                        'leg_index' => $optIdx,
                                        'is_return' => ($optIdx > 0),
                                        'leg_type' => ($optIdx > 0 ? 'return' : 'outbound'),
                                        'from' => $s['DepartureAirportLocationCode'] ?? '',
                                        'to' => $s['ArrivalAirportLocationCode'] ?? '',
                                        'dep' => \Carbon\Carbon::parse($s['DepartureDateTime'])->format('H:i'),
                                        'arr' => \Carbon\Carbon::parse($s['ArrivalDateTime'])->format('H:i'),
                                        'dep_datetime' => $s['DepartureDateTime'] ?? '',
                                        'arr_datetime' => $s['ArrivalDateTime'] ?? '',
                                        'layover' => $layStr,
                                        'layover_airport' => $s['ArrivalAirportLocationCode'] ?? '',
                                        'flight_no' => ($s['MarketingAirlineCode'] ?? $validatingCarrier) . ' ' . ($s['FlightNumber'] ?? ''),
                                    ];
                                }
                            }

                            $retDateVal = $searchParams['returnDate'] ?? null;
                            if (!$retDateVal && $isRoundTrip && isset($options[1])) {
                                $rSegs = isset($options[1]['OriginDestinationOption']['FlightSegment']) ? [$options[1]['OriginDestinationOption']] : ($options[1]['OriginDestinationOption'] ?? []);
                                if (!empty($rSegs)) {
                                    $retDateVal = \Carbon\Carbon::parse($rSegs[0]['FlightSegment']['DepartureDateTime'] ?? now())->format('Y-m-d');
                                }
                            }

                            $fareBreakdowns = $fareInfo['FareBreakdown'] ?? [];
                            $firstFb = isset($fareBreakdowns[0]) ? $fareBreakdowns[0] : (is_array($fareBreakdowns) ? $fareBreakdowns : []);
                            $fbCabin = isset($firstFb['CabinBaggage'][0]) ? $firstFb['CabinBaggage'][0] : ($firstFb['CabinBaggage'] ?? '7 KG');
                            $fbBaggage = isset($firstFb['Baggage'][0]) ? $firstFb['Baggage'][0] : ($firstFb['Baggage'] ?? __('Per Airline Policy'));
                        @endphp
                        
                        <div class="card flight-card scroll-animate flight-item" 
                             data-price="{{ $price }}" 
                             data-airline="{{ $validatingCarrier }}" 
                             data-stops="{{ $maxStops }}"
                             data-dep-time="{{ $firstDepTime }}"
                             data-duration-min="{{ $totalDurationMinutes }}"
                             style="animation-delay: {{ $index * 0.1 }}s">
                            <div class="flight-card-body">
                                <div class="airline-info">
                                    <img src="https://travelnext.works/api/airlines/{{ $validatingCarrier }}.gif" alt="{{ $validatingCarrier }}" class="airline-logo">
                                    <span class="airline-name">{{ $validatingCarrier }}</span>
                                    @if($isRoundTrip)
                                        <span class="badge bg-primary-subtle text-primary mt-2" style="font-size: 0.72rem; padding: 3px 8px; border-radius: 12px; border: 1px solid #bfdbfe; font-weight: 800;">
                                            <i class="fas fa-sync-alt me-1"></i> {{ __('Round Trip') }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary mt-2" style="font-size: 0.72rem; padding: 3px 8px; border-radius: 12px; font-weight: 700;">
                                            <i class="fas fa-plane me-1"></i> {{ __('One Way') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="itinerary-details">
                                    @foreach($options as $optIndex => $option)
                                        @php
                                            $segments = $option['OriginDestinationOption'];
                                            if (isset($segments['FlightSegment'])) {
                                                $segments = [$segments];
                                            }
                                            $firstSeg = $segments[0]['FlightSegment'];
                                            $lastSeg = end($segments)['FlightSegment'];
                                            $stops = count($segments) - 1;
                                            $dep = \Carbon\Carbon::parse($firstSeg['DepartureDateTime']);
                                            $arr = \Carbon\Carbon::parse($lastSeg['ArrivalDateTime']);
                                            $duration = $dep->diff($arr)->format('%h' . __('h') . ' %i' . __('m'));
                                        @endphp

                                        @if($optIndex > 0)
                                            <div style="font-size: 0.78rem; font-weight: 800; color: #0369a1; background: #e0f2fe; padding: 2px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; margin: 6px 0 2px;">
                                                <i class="fas fa-plane-arrival"></i> {{ __('Return Flight') }} - {{ $dep->format('d M Y') }}
                                            </div>
                                        @elseif($isRoundTrip)
                                            <div style="font-size: 0.78rem; font-weight: 800; color: #1d4ed8; background: #eff6ff; padding: 2px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 2px;">
                                                <i class="fas fa-plane-departure"></i> {{ __('Outbound Flight') }} - {{ $dep->format('d M Y') }}
                                            </div>
                                        @endif

                                        <div class="flight-leg {{ $optIndex > 0 ? 'return-leg' : '' }}" style="{{ $optIndex > 0 ? 'margin-top: 6px;' : '' }}">
                                            <div class="leg-time dep">
                                                <span class="time">{{ $dep->format('H:i') }}</span>
                                                <span class="airport">{{ $firstSeg['DepartureAirportLocationCode'] }}</span>
                                                <span style="font-size: 0.7rem; color: #94a3b8;">{{ $dep->format('d M') }}</span>
                                            </div>

                                            <div class="leg-path">
                                                <div class="duration">
                                                    {{ $duration }}
                                                </div>
                                                <div class="path-viz">
                                                    <span class="dot"></span>
                                                    <span class="line"></span>
                                                    <i class="fas fa-plane"></i>
                                                    <span class="line"></span>
                                                    <span class="dot"></span>
                                                </div>
                                                <div class="stops">
                                                    {{ $stops == 0 ? __('Non-stop') : ($stops . ' ' . ($stops == 1 ? __('Stop') : __('Stops'))) }}
                                                </div>
                                            </div>

                                            <div class="leg-time arr">
                                                <span class="time">{{ $arr->format('H:i') }}</span>
                                                <span class="airport">{{ $lastSeg['ArrivalAirportLocationCode'] }}</span>
                                                <span style="font-size: 0.7rem; color: #94a3b8;">{{ $arr->format('d M') }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    {{-- Baggage Allowance & Fare Rules Bar in Card Body --}}
                                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-top: 10px; padding-top: 8px; border-top: 1px dashed var(--gray-200); font-size: 0.78rem;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <span style="color: #0f766e; font-weight: 700; background: #f0fdfa; padding: 2px 8px; border-radius: 5px; border: 1px solid #ccfbf1;">
                                                <i class="fas fa-suitcase-rolling"></i> {{ $fbBaggage ?? __('Per Airline Policy') }}
                                            </span>
                                        </div>
                                        <button type="button" class="btn-fare-rules-trigger"
                                                data-session="{{ $results['AirSearchResponse']['session_id'] ?? ($results['AirSearchResponse']['AirSearchResult']['SessionId'] ?? '') }}" 
                                                data-fare-source="{{ $fareInfo['FareSourceCode'] }}"
                                                data-inbound="{{ $inboundCode ?? '' }}"
                                                data-airline="{{ $validatingCarrier }}"
                                                data-airline-name="{{ $firstSeg['MarketingAirlineName'] ?? $validatingCarrier }}"
                                                data-from="{{ $searchParams['from'] ?? '' }}"
                                                data-to="{{ $searchParams['to'] ?? '' }}"
                                                data-refundable="{{ $itin['AirItineraryFareInfo']['IsRefundable'] ?? '' }}"
                                                data-change-allowed="{{ isset($firstFb['PenaltyDetails']['ChangeAllowed']) ? ($firstFb['PenaltyDetails']['ChangeAllowed'] ? 'true' : 'false') : '' }}"
                                                data-change-penalty="{{ $firstFb['PenaltyDetails']['ChangePenaltyAmount'] ?? '0.00' }}"
                                                data-refund-allowed="{{ isset($firstFb['PenaltyDetails']['RefundAllowed']) ? ($firstFb['PenaltyDetails']['RefundAllowed'] ? 'true' : 'false') : '' }}"
                                                data-refund-penalty="{{ $firstFb['PenaltyDetails']['RefundPenaltyAmount'] ?? '0.00' }}"
                                                data-baggage="{{ $fbBaggage ?? __('Per Airline Policy') }}"
                                                data-cabin-baggage="{{ $fbCabin ?? '7 KG' }}"
                                                data-fare-basis="{{ $firstFb['FareBasisCode'] ?? '' }}"
                                                title="{{ __('View ticket cancellation and refund rules') }}"
                                                style="background: #f0f9ff; border: 1px solid #bae6fd; color: #0284c7; font-size: 0.75rem; font-weight: 700; cursor: pointer; padding: 3px 9px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s;">
                                            <i class="fas fa-file-contract text-primary"></i> {{ __('Fare Rules') }}
                                        </button>
                                    </div>
                                </div>

                                <div class="pricing-info">
                                    <div class="price-tag">
                                        <span class="amount">{{ number_format($price, 2) }}</span>
                                        <span class="currency">{{ $currency }}</span>
                                    </div>
                                    @php
                                        $inboundCode = $fareInfo['FareSourceCodeInbound'] ?? ($fareInfo['fare_source_code_inbound'] ?? ($itin['FareSourceCodeInbound'] ?? ($itin['fare_source_code_inbound'] ?? null)));
                                    @endphp
                                    <a href="{{ route('flights.booking.form', array_merge($searchParams, [
                                        'fare_source_code' => $fareInfo['FareSourceCode'], 
                                        'fare_source_code_inbound' => $inboundCode,
                                        'session_id' => $results['AirSearchResponse']['session_id'] ?? ($results['AirSearchResponse']['AirSearchResult']['SessionId'] ?? ''), 
                                        'fareType' => $fareInfo['FareType'] ?? ($itin['FareType'] ?? 'Private'),
                                        'total_amount' => $price,
                                        'airline' => $validatingCarrier,
                                        'dep_time' => $firstDepTime,
                                        'arr_time' => $lastArrTime,
                                        'stops' => $maxStops,
                                        'duration' => $totalDurationStr,
                                        'journeyType' => $isRoundTrip ? 'Return' : 'OneWay',
                                        'returnDate' => $retDateVal,
                                        'segments' => $allSegmentsData,
                                        'refundable' => $itin['AirItineraryFareInfo']['IsRefundable'] ?? '',
                                        'change_allowed' => isset($firstFb['PenaltyDetails']['ChangeAllowed']) ? ($firstFb['PenaltyDetails']['ChangeAllowed'] ? 'true' : 'false') : '',
                                        'change_penalty' => $firstFb['PenaltyDetails']['ChangePenaltyAmount'] ?? '',
                                        'refund_allowed' => isset($firstFb['PenaltyDetails']['RefundAllowed']) ? ($firstFb['PenaltyDetails']['RefundAllowed'] ? 'true' : 'false') : '',
                                        'refund_penalty' => $firstFb['PenaltyDetails']['RefundPenaltyAmount'] ?? '',
                                        'fare_basis' => is_array($firstFb['FareBasisCode'] ?? '') ? implode('/', array_filter($firstFb['FareBasisCode'])) : ($firstFb['FareBasisCode'] ?? '')
                                    ])) }}" class="btn btn-primary btn-sm">
                                        {{ __('Select') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card empty-state">
                            <div class="card-body">
                                <i class="fas fa-plane-slash"></i>
                                <h3>{{ __('No Flights Found') }}</h3>
                                <p>{{ __('We couldn\'t find any flights for your selected dates and route. Please try different dates or search criteria.') }}</p>
                                <a href="{{ route('flights') }}" class="btn btn-outline">{{ __('Back to Search') }}</a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- Global Fare Rules Modal for Flights --}}
    <div class="fe-modal-overlay d-none" id="globalFareRulesModal" style="z-index: 10050;">
        <div class="fe-modal-dialog">
            <div class="fe-modal-content">
                <div class="fe-modal-header">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #38bdf8;">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <h4 style="margin: 0; font-weight: 800; font-size: 1.2rem; color: white;">{{ __('Fare Rules & Ticket Policies') }}</h4>
                                <span id="fareRulesAirlineBadge" class="badge" style="background: rgba(56, 189, 248, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); font-size: 0.75rem; padding: 3px 8px; border-radius: 6px;"></span>
                            </div>
                            <span id="fareRulesAirlineSubtitle" style="font-size: 0.8rem; color: #cbd5e1;">{{ __('Official airline cancellation, refund, and change terms') }}</span>
                        </div>
                    </div>
                    <button type="button" class="fe-btn-close" onclick="closeFareRulesModal()" aria-label="{{ __('Close') }}"><i class="fas fa-times"></i></button>
                </div>
                
                <div class="fe-modal-body">
                    <div id="fareRulesModalLoader" style="padding: 40px 20px; text-align: center;">
                        <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem; border-width: 3px;"></div>
                        <p class="mt-3 text-muted fw-bold" style="font-size: 0.92rem;">{{ __('Loading official airline fare rules...') }}</p>
                    </div>
                    
                    <div id="fareRulesModalBody" style="display: none;"></div>

                    <div id="fareRulesModalError" style="display: none; padding: 35px 20px; text-align: center;">
                        <i class="fas fa-exclamation-triangle mb-3" style="font-size: 2.5rem; color: #d97706;"></i>
                        <h5 class="fw-bold text-dark" style="margin-bottom: 8px;">{{ __('Fare Rules Unavailable') }}</h5>
                        <p class="text-muted" id="fareRulesErrorDesc" style="font-size: 0.88rem; max-width: 480px; margin: 0 auto;">{{ __('Unable to retrieve fare rules from the airline provider for this flight.') }}</p>
                    </div>
                </div>

                <div class="fe-modal-footer">
                    <span style="font-size: 0.78rem; color: #64748b; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-shield-alt text-success"></i> {{ __('Verified Airline Tariff Rules') }}
                    </span>
                    <button type="button" class="fe-btn fe-btn-secondary" onclick="closeFareRulesModal()" style="padding: 8px 24px; font-weight: 700; border-radius: 8px;">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const flightItems = document.querySelectorAll('.flight-item');
    const priceRange = document.getElementById('priceRange');
    const priceValue = document.getElementById('priceValue');
    const minPriceLabel = document.getElementById('minPriceLabel');
    const maxPriceLabel = document.getElementById('maxPriceLabel');
    const airlinesList = document.getElementById('airlinesList');
    const stopFilters = document.querySelectorAll('.filter-stop');

    if (flightItems.length === 0) return;

    // 1. Initialize Cached Flight Data
    let minPrices = [], maxPrices = [];
    let minDurations = [], maxDurations = [];
    const cachedItems = Array.from(flightItems).map(item => {
        const p = parseFloat(item.dataset.price) || 0;
        const d = parseInt(item.dataset.durationMin) || 0;
        const s = parseInt(item.dataset.stops) || 0;
        const a = item.dataset.airline || '';
        const depTime = item.dataset.depTime || '';
        const hour = parseInt(depTime.split(':')[0]) || 0;
        let timeCat = 'night';
        if (hour >= 6 && hour < 12) timeCat = 'morning';
        else if (hour >= 12 && hour < 18) timeCat = 'afternoon';
        else if (hour >= 18 && hour < 24) timeCat = 'evening';

        minPrices.push(p);
        maxPrices.push(p);
        minDurations.push(d);
        maxDurations.push(d);

        return { el: item, price: p, duration: d, stops: s, airline: a, timeCat: timeCat };
    });

    const minP = Math.floor(Math.min(...minPrices));
    const maxP = Math.ceil(Math.max(...maxPrices));
    const minD = Math.floor(Math.min(...minDurations));
    const maxD = Math.ceil(Math.max(...maxDurations));

    priceRange.min = minP;
    priceRange.max = maxP;
    priceRange.value = maxP;
    priceValue.textContent = maxP + ' SAR';
    minPriceLabel.textContent = minP + ' SAR';
    maxPriceLabel.textContent = maxP + ' SAR';
    
    // Duration
    const durationRange = document.getElementById('durationRange');
    const durationValue = document.getElementById('durationValue');
    durationRange.min = minD;
    durationRange.max = maxD;
    durationRange.value = maxD;
    durationValue.textContent = Math.floor(maxD/60) + 'h ' + (maxD%60) + 'm';

    // 2. Populate Airlines
    const airlines = {};
    cachedItems.forEach(item => {
        airlines[item.airline] = (airlines[item.airline] || 0) + 1;
    });

    const airlineNames = {
        "XY": "Flynas (طيران ناس)", "SV": "Saudia (الخطوط السعودية)", "F3": "Flyadeal (طيران أديل)",
        "EK": "Emirates (الإمارات)", "EY": "Etihad (الاتحاد)", "QR": "Qatar Airways (القطرية)",
        "FZ": "Flydubai (فلاي دبي)", "G9": "Air Arabia (العربية)", "MS": "EgyptAir (مصر للطيران)",
        "J9": "Jazeera (الجزيرة)", "RJ": "Royal Jordanian (الملكية الأردنية)", "ME": "MEA",
        "TK": "Turkish Airlines (التركية)", "PC": "Pegasus (بيغاسوس)", "WY": "Oman Air (العماني)",
        "GF": "Gulf Air (الخليج)", "KU": "Kuwait Airways (الكويتية)", "W6": "Wizz Air",
        "BA": "British Airways", "LH": "Lufthansa", "AF": "Air France", "KL": "KLM",
        "AA": "American Airlines", "DL": "Delta", "UA": "United", "6E": "IndiGo",
        "AI": "Air India", "PK": "PIA", "BG": "Biman", "UL": "SriLankan", "SQ": "Singapore Airlines"
    };

    Object.keys(airlines).forEach(code => {
        const div = document.createElement('label');
        div.className = 'checkbox-label';
        const name = airlineNames[code] || code;
        div.innerHTML = `
            <input type="checkbox" class="filter-airline" value="${code}" checked>
            <span>${name} (${airlines[code]})</span>
        `;
        airlinesList.appendChild(div);
    });

    const airlineFilters = document.querySelectorAll('.filter-airline');
    const timeFilters = document.querySelectorAll('.filter-time');

    let filterDebounceTimer = null;
    function scheduleFilters() {
        if (filterDebounceTimer) clearTimeout(filterDebounceTimer);
        filterDebounceTimer = setTimeout(() => {
            requestAnimationFrame(applyFilters);
        }, 25);
    }

    // 3. Ultra Fast Filter Function
    function applyFilters() {
        const maxPrice = parseFloat(priceRange.value);
        const maxDuration = parseInt(durationRange.value);
        const selectedStops = new Set(Array.from(stopFilters).filter(i => i.checked).map(i => parseInt(i.value)));
        const selectedAirlines = new Set(Array.from(document.querySelectorAll('.filter-airline:checked')).map(i => i.value));
        const selectedTimes = new Set(Array.from(timeFilters).filter(i => i.checked).map(i => i.value));
        const hasTwoPlus = selectedStops.has(2);

        const len = cachedItems.length;
        for (let i = 0; i < len; i++) {
            const item = cachedItems[i];
            const matchStops = selectedStops.has(item.stops) || (hasTwoPlus && item.stops >= 2);
            const matchPrice = item.price <= maxPrice;
            const matchDuration = item.duration <= maxDuration;
            const matchAirline = selectedAirlines.has(item.airline);
            const matchTime = selectedTimes.has(item.timeCat);

            const show = matchPrice && matchStops && matchAirline && matchTime && matchDuration;
            const curDisplay = item.el.style.display;
            const newDisplay = show ? 'block' : 'none';
            if (curDisplay !== newDisplay) {
                item.el.style.display = newDisplay;
            }
        }
    }

    // 4. Event Listeners
    priceRange.addEventListener('input', function() {
        priceValue.textContent = this.value + ' SAR';
        scheduleFilters();
    });
    
    durationRange.addEventListener('input', function() {
        durationValue.textContent = Math.floor(this.value/60) + 'h ' + (this.value%60) + 'm';
        scheduleFilters();
    });

    stopFilters.forEach(i => i.addEventListener('change', scheduleFilters));
    timeFilters.forEach(i => i.addEventListener('change', scheduleFilters));
    
    // Delegate for dynamic checkboxes
    if(airlinesList) {
        airlinesList.addEventListener('change', function(e) {
            if (e.target.classList.contains('filter-airline')) {
                scheduleFilters();
            }
        });
    }

    window.resetFilters = function() {
        priceRange.value = maxP;
        priceValue.textContent = maxP + ' SAR';
        durationRange.value = maxD;
        durationValue.textContent = Math.floor(maxD/60) + 'h ' + (maxD%60) + 'm';
        stopFilters.forEach(i => i.checked = true);
        timeFilters.forEach(i => i.checked = true);
        document.querySelectorAll('.filter-airline').forEach(i => i.checked = true);
        applyFilters();
    };

    // ─── Fare Rules Modal Interaction ───
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
@endpush

@push('styles')
<style>
    .trips-grid-wrapper {
        display: grid;
        grid-template-columns: 1fr;
        gap: var(--space-8);
    }
    @media (min-width: 1024px) {
        .trips-grid-wrapper {
            grid-template-columns: 280px 1fr;
        }
    }

    #filtersSidebar {
        height: min-content;
        position: sticky;
        top: 100px;
    }

    .flight-card {
        margin-bottom: var(--space-4);
        border: 1px solid var(--color-border);
        transition: all 0.3s ease;
    }
    .flight-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        border-color: var(--color-primary-light);
    }

    .flight-card-body {
        display: grid;
        grid-template-columns: 100px 1fr 150px;
        padding: var(--space-4);
        align-items: center;
        gap: var(--space-4);
    }

    @media (max-width: 768px) {
        .flight-card-body {
            grid-template-columns: 1fr;
            text-align: center;
        }
    }

    .airline-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .airline-logo {
        width: 48px;
        height: auto;
        border-radius: 4px;
    }
    .airline-name {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--color-text-muted);
    }

    .itinerary-details {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .flight-leg {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .leg-time {
        display: flex;
        flex-direction: column;
        min-width: 70px;
    }
    .leg-time.dep { text-align: left; }
    .leg-time.arr { text-align: right; }
    
    .leg-time .time {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--color-text);
    }
    .leg-time .airport {
        font-size: 0.875rem;
        color: var(--color-text-muted);
        font-weight: 600;
    }

    .leg-path {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
    .leg-path .duration {
        font-size: 0.75rem;
        color: var(--color-text-muted);
    }
    .leg-path .path-viz {
        display: flex;
        align-items: center;
        width: 100%;
        max-width: 150px;
        gap: 8px;
    }
    .path-viz .line {
        flex: 1;
        height: 1px;
        background: var(--color-border);
    }
    .path-viz .dot {
        width: 6px;
        height: 6px;
        border: 1px solid var(--color-border);
        border-radius: 50%;
    }
    .path-viz i {
        color: var(--color-primary);
        font-size: 0.875rem;
    }
    [dir="rtl"] .path-viz i,
    html[dir="rtl"] .path-viz i {
        transform: scaleX(-1);
    }
    .leg-path .stops {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--color-primary);
    }

    .pricing-info {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        border-left: 1px solid var(--color-border);
        padding-left: var(--space-4);
        gap: 12px;
    }

    @media (max-width: 768px) {
        .pricing-info {
            border-left: 0;
            border-top: 1px solid var(--color-border);
            padding-left: 0;
            padding-top: var(--space-4);
            align-items: center;
        }
    }

    .price-tag {
        text-align: right;
    }
    @media (max-width: 768px) { .price-tag { text-align: center; } }

    .price-tag .amount {
        display: block;
        font-size: 1.5rem;
        font-weight: 900;
        color: var(--color-primary);
    }
    .price-tag .currency {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--color-text-muted);
    }

    .empty-state {
        text-align: center;
        padding: var(--space-16);
    }
    .empty-state i {
        font-size: 4rem;
        color: var(--color-border);
        margin-bottom: var(--space-4);
    }
    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: var(--space-2);
    }

    .fe-results-sidebar {
        position: sticky;
        top: 100px;
        height: fit-content;
    }
    .fe-sidebar-card {
        background: white;
        border-radius: var(--radius-xl);
        padding: var(--space-6);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-100);
    }
    .fe-sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-6);
        border-bottom: 2px solid var(--gray-50);
        padding-bottom: var(--space-4);
    }
    .fe-sidebar-header h3 { font-size: 1.1rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px; }
    .fe-sidebar-header i { color: var(--primary); }
    
    .fe-reset-btn {
        background: transparent;
        border: none;
        color: var(--primary);
        font-weight: 700;
        font-size: 0.8rem;
        cursor: pointer;
    }
    .fe-reset-btn:hover { text-decoration: underline; }

    .fe-filter-group {
        margin-bottom: var(--space-8);
    }
    .fe-filter-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 800;
        font-size: 0.9rem;
        color: var(--dark);
        margin-bottom: var(--space-4);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .fe-filter-label i { color: var(--gray-400); margin-inline-end: 6px; }
    .fe-val-highlight { color: var(--primary); font-family: monospace; font-size: 1rem; }

    /* Custom Checkboxes */
    .fe-check-list { display: flex; flex-direction: column; gap: 10px; }
    .fe-custom-check {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        position: relative;
        padding: 4px 0;
    }
    .fe-custom-check input { display: none; }
    .checkmark {
        width: 20px;
        height: 20px;
        border: 2px solid var(--gray-300);
        border-radius: 6px;
        position: relative;
        transition: all 0.2s ease;
    }
    .fe-custom-check input:checked + .checkmark { border-color: var(--primary); background: var(--primary); }
    .fe-custom-check input:checked + .checkmark::after {
        content: "\f00c";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        color: white;
        font-size: 10px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .label-text { font-size: 0.9rem; font-weight: 600; color: var(--gray-700); }
    .label-text small { color: var(--gray-400); margin-inline-start: 4px; }

    /* Sliders */
    .fe-slider-wrapper { padding: 10px 0; }
    .fe-range-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 6px;
        background: var(--gray-200);
        border-radius: 3px;
        outline: none;
    }
    .fe-range-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        background: var(--primary);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 0 0 4px rgba(15,76,129,0.1);
        transition: all 0.2s ease;
    }
    .fe-range-slider::-webkit-slider-thumb:hover { transform: scale(1.2); box-shadow: 0 0 0 6px rgba(15,76,129,0.2); }
    
    .fe-range-labels { display: flex; justify-content: space-between; margin-top: 8px; font-size: 0.75rem; color: var(--gray-400); font-weight: 700; }

    .airlines-scroll { max-height: 250px; overflow-y: auto; padding-right: 8px; }
    .airlines-scroll::-webkit-scrollbar { width: 4px; }
    .airlines-scroll::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 10px; }
</style>
@endpush
