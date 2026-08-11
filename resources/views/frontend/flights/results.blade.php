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
                    {{ $searchParams['from'] ?? '' }} <i class="fas fa-long-arrow-alt-right"></i> {{ $searchParams['to'] ?? '' }}
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
                            $options = $itin['OriginDestinationOptions'];
                            if (isset($options['OriginDestinationOption'])) {
                                $options = [$options];
                            }
                            
                            // Extract max stops for filtering
                            $maxStops = 0;
                            foreach($options as $opt) {
                                $segs = isset($opt['OriginDestinationOption']['FlightSegment']) ? [$opt['OriginDestinationOption']] : $opt['OriginDestinationOption'];
                                $sCount = count($segs) - 1;
                                if($sCount > $maxStops) $maxStops = $sCount;
                            }
                        @endphp
                        
                        <div class="card flight-card scroll-animate flight-item" 
                             data-price="{{ $price }}" 
                             data-airline="{{ $validatingCarrier }}" 
                             data-stops="{{ $maxStops }}"
                             @php
                                 $firstDepTime = \Carbon\Carbon::parse($itineraries[0]['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['DepartureDateTime'] ?? $itineraries[0]['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['DepartureDateTime'] ?? now())->format('H:i');
                                 
                                 $totalDurationMinutes = 0;
                                 foreach($options as $opt) {
                                     $segs = isset($opt['OriginDestinationOption']['FlightSegment']) ? [$opt['OriginDestinationOption']] : $opt['OriginDestinationOption'];
                                     $d1 = \Carbon\Carbon::parse($segs[0]['FlightSegment']['DepartureDateTime']);
                                     $d2 = \Carbon\Carbon::parse(end($segs)['FlightSegment']['ArrivalDateTime']);
                                     $totalDurationMinutes += $d1->diffInMinutes($d2);
                                 }
                             @endphp
                             data-dep-time="{{ $firstDepTime }}"
                             data-duration-min="{{ $totalDurationMinutes }}"
                             style="animation-delay: {{ $index * 0.1 }}s">
                            <div class="flight-card-body">
                                <div class="airline-info">
                                    <img src="https://travelnext.works/api/airlines/{{ $validatingCarrier }}.gif" alt="{{ $validatingCarrier }}" class="airline-logo">
                                    <span class="airline-name">{{ $validatingCarrier }}</span>
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
                                        @endphp

                                        <div class="flight-leg {{ $optIndex > 0 ? 'return-leg' : '' }}">
                                            <div class="leg-time dep">
                                                <span class="time">{{ \Carbon\Carbon::parse($firstSeg['DepartureDateTime'])->format('H:i') }}</span>
                                                <span class="airport">{{ $firstSeg['DepartureAirportLocationCode'] }}</span>
                                            </div>

                                            <div class="leg-path">
                                                <div class="duration">
                                                    @php
                                                        $dep = \Carbon\Carbon::parse($firstSeg['DepartureDateTime']);
                                                        $arr = \Carbon\Carbon::parse($lastSeg['ArrivalDateTime']);
                                                        $duration = $dep->diff($arr)->format('%h' . __('h') . ' %i' . __('m'));
                                                    @endphp
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
                                                <span class="time">{{ \Carbon\Carbon::parse($lastSeg['ArrivalDateTime'])->format('H:i') }}</span>
                                                <span class="airport">{{ $lastSeg['ArrivalAirportLocationCode'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="pricing-info">
                                    <div class="price-tag">
                                        <span class="amount">{{ number_format($price, 2) }}</span>
                                        <span class="currency">{{ $currency }}</span>
                                    </div>
                                    <a href="{{ route('flights.booking.form', array_merge($searchParams, ['fare_source_code' => $fareInfo['FareSourceCode'], 'session_id' => $results['AirSearchResponse']['session_id'] ?? ($results['AirSearchResponse']['AirSearchResult']['SessionId'] ?? ''), 'total_amount' => $price])) }}" class="btn btn-primary btn-sm">
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

    // 1. Initialize Price Range
    let minPrices = [], maxPrices = [];
    let minDurations = [], maxDurations = [];
    flightItems.forEach(item => {
        minPrices.push(parseFloat(item.dataset.price));
        maxPrices.push(parseFloat(item.dataset.price));
        minDurations.push(parseInt(item.dataset.durationMin));
        maxDurations.push(parseInt(item.dataset.durationMin));
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
    flightItems.forEach(item => {
        const code = item.dataset.airline;
        airlines[code] = (airlines[code] || 0) + 1;
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

    // 3. Filter Function
    function applyFilters() {
        const maxPrice = parseFloat(priceRange.value);
        const maxDuration = parseInt(durationRange.value);
        const selectedStops = Array.from(stopFilters).filter(i => i.checked).map(i => parseInt(i.value));
        const selectedAirlines = Array.from(airlineFilters).filter(i => i.checked).map(i => i.value);
        const selectedTimes = Array.from(timeFilters).filter(i => i.checked).map(i => i.value);

        flightItems.forEach(item => {
            const price = parseFloat(item.dataset.price);
            const airline = item.dataset.airline;
            const stops = parseInt(item.dataset.stops);
            const duration = parseInt(item.dataset.durationMin);
            const depTime = item.dataset.depTime;
            
            // Time logic
            const hour = parseInt(depTime.split(':')[0]);
            let timeCat = 'night';
            if (hour >= 6 && hour < 12) timeCat = 'morning';
            else if (hour >= 12 && hour < 18) timeCat = 'afternoon';
            else if (hour >= 18 && hour < 24) timeCat = 'evening';

            let matchStops = selectedStops.includes(stops);
            if (selectedStops.includes(2) && stops >= 2) matchStops = true;

            const matchPrice = price <= maxPrice;
            const matchDuration = duration <= maxDuration;
            const matchAirline = selectedAirlines.includes(airline);
            const matchTime = selectedTimes.includes(timeCat);

            if (matchPrice && matchStops && matchAirline && matchTime && matchDuration) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // 4. Event Listeners
    priceRange.addEventListener('input', function() {
        priceValue.textContent = this.value + ' SAR';
        applyFilters();
    });
    
    durationRange.addEventListener('input', function() {
        durationValue.textContent = Math.floor(this.value/60) + 'h ' + (this.value%60) + 'm';
        applyFilters();
    });

    stopFilters.forEach(i => i.addEventListener('change', applyFilters));
    timeFilters.forEach(i => i.addEventListener('change', applyFilters));
    
    // Delegate for dynamic checkboxes
    if(airlinesList) {
        airlinesList.addEventListener('change', function(e) {
            if (e.target.classList.contains('filter-airline')) {
                applyFilters();
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
