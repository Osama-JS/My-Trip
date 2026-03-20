{{-- Flight Results Partial - loaded via AJAX into fe-results-section --}}
@php
    $itineraries = $results['AirSearchResponse']['AirSearchResult']['FareItineraries']['FareItinerary'] ?? [];
    if (!empty($itineraries) && !is_array(reset($itineraries))) {
        $itineraries = [$itineraries];
    }
    $sessionId = $results['AirSearchResponse']['AirSearchResult']['SessionId'] ?? '';
@endphp

@if(empty($itineraries))
    {{-- No Results --}}
    <div class="fr-no-results">
        <div class="fr-no-results-icon"><i class="fas fa-plane-slash"></i></div>
        <h3>{{ __('No Flights Found') }}</h3>
        <p>{{ __("We couldn't find flights for your route and dates. Try different dates or airports.") }}</p>
        <a href="{{ route('flights') }}" class="fe-btn fe-btn-outline" style="margin-top:var(--space-4);">
            <i class="fas fa-arrow-left"></i> {{ __('New Search') }}
        </a>
    </div>
@else
    {{-- Results Header --}}
    <div class="fr-results-header">
        <div class="fr-results-count">
            <span class="fr-count-badge">{{ count($itineraries) }}</span>
            <span>{{ __('flights found') }}</span>
            @if(!empty($searchParams['from']) && !empty($searchParams['to']))
                <span class="fr-route">
                    <strong>{{ $searchParams['from'] }}</strong>
                    <i class="fas fa-long-arrow-alt-right" style="margin:0 8px;color:var(--accent)"></i>
                    <strong>{{ $searchParams['to'] }}</strong>
                </span>
            @endif
        </div>
        <div class="fr-sort-bar">
            <span>{{ __('Sort by') }}:</span>
            <button class="fr-sort-btn active" data-sort="price">{{ __('Price') }}</button>
            <button class="fr-sort-btn" data-sort="duration">{{ __('Duration') }}</button>
            <button class="fr-sort-btn" data-sort="departure">{{ __('Departure') }}</button>
        </div>
    </div>

    {{-- Flights List --}}
    <div id="flightResultsList">
        @foreach($itineraries as $index => $itin)
            @php
                $fareInfo = $itin['AirItineraryFareInfo'];
                $price = floatval($fareInfo['ItinTotalFares']['TotalFare']['Amount']);
                $currency = $fareInfo['ItinTotalFares']['TotalFare']['CurrencyCode'];
                $validatingCarrier = $itin['ValidatingAirlineCode'];
                $options = $itin['OriginDestinationOptions'];
                if (isset($options['OriginDestinationOption'])) {
                    $options = [$options];
                }
                $maxStops = 0;
                $totalDurationMinutes = 0;
                $firstDepTime = null;
                foreach($options as $opt) {
                    $segs = isset($opt['OriginDestinationOption']['FlightSegment'])
                        ? [$opt['OriginDestinationOption']]
                        : $opt['OriginDestinationOption'];
                    $d1 = \Carbon\Carbon::parse($segs[0]['FlightSegment']['DepartureDateTime']);
                    $d2 = \Carbon\Carbon::parse(end($segs)['FlightSegment']['ArrivalDateTime']);
                    $totalDurationMinutes += $d1->diffInMinutes($d2);
                    if (!$firstDepTime) $firstDepTime = $d1->format('H:i');
                    $sCount = count($segs) - 1;
                    if ($sCount > $maxStops) $maxStops = $sCount;
                }
            @endphp

            <div class="fr-flight-card flight-item"
                 data-price="{{ $price }}"
                 data-airline="{{ $validatingCarrier }}"
                 data-stops="{{ $maxStops }}"
                 data-dep-time="{{ $firstDepTime }}"
                 data-duration-min="{{ $totalDurationMinutes }}"
                 style="animation-delay:{{ $index * 0.07 }}s">

                <div class="fr-airline-col">
                    <img src="https://travelnext.works/api/airlines/{{ $validatingCarrier }}.gif"
                         alt="{{ $validatingCarrier }}" class="fr-airline-logo"
                         onerror="this.src='https://via.placeholder.com/48x30?text={{ $validatingCarrier }}'">
                    <span class="fr-airline-code">{{ $validatingCarrier }}</span>
                </div>

                <div class="fr-legs-col">
                    @foreach($options as $optIndex => $option)
                        @php
                            $segments = $option['OriginDestinationOption'];
                            if (isset($segments['FlightSegment'])) {
                                $segments = [$segments];
                            }
                            $firstSeg = $segments[0]['FlightSegment'];
                            $lastSeg  = end($segments)['FlightSegment'];
                            $stops    = count($segments) - 1;
                            $dep  = \Carbon\Carbon::parse($firstSeg['DepartureDateTime']);
                            $arr  = \Carbon\Carbon::parse($lastSeg['ArrivalDateTime']);
                            $dur  = $dep->diff($arr)->format('%hh %im');
                        @endphp
                        @if($optIndex > 0)
                            <div class="fr-return-divider"><i class="fas fa-undo"></i> {{ __('Return') }}</div>
                        @endif
                        <div class="fr-leg">
                            <div class="fr-leg-time">
                                <span class="fr-time">{{ $dep->format('H:i') }}</span>
                                <span class="fr-airport">{{ $firstSeg['DepartureAirportLocationCode'] }}</span>
                                <span class="fr-date-label">{{ $dep->format('d M') }}</span>
                            </div>
                            <div class="fr-leg-middle">
                                <span class="fr-dur-label">{{ $dur }}</span>
                                <div class="fr-path">
                                    <span class="fr-dot"></span>
                                    <span class="fr-line"></span>
                                    <i class="fas fa-plane fr-plane-icon"></i>
                                    <span class="fr-line"></span>
                                    <span class="fr-dot"></span>
                                </div>
                                <span class="fr-stop-badge {{ $stops == 0 ? 'nonstop' : '' }}">
                                    {{ $stops == 0 ? __('Non-stop') : ($stops . ' ' . ($stops == 1 ? __('Stop') : __('Stops'))) }}
                                </span>
                            </div>
                            <div class="fr-leg-time" style="text-align: end;">
                                <span class="fr-time">{{ $arr->format('H:i') }}</span>
                                <span class="fr-airport">{{ $lastSeg['ArrivalAirportLocationCode'] }}</span>
                                <span class="fr-date-label">{{ $arr->format('d M') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="fr-price-col">
                    <div class="fr-price">
                        <span class="fr-price-amount">{{ number_format($price, 0) }}</span>
                        <span class="fr-price-currency">{{ $currency }}</span>
                    </div>
                    <span class="fr-price-note">{{ __('per person') }}</span>
                    <a href="{{ route('flights.booking.form', array_merge($searchParams ?? [], [
                        'fare_source_code' => $fareInfo['FareSourceCode'],
                        'session_id' => $sessionId,
                        'total_amount' => $price
                    ])) }}" class="fe-btn fe-btn-primary fr-select-btn">
                        {{ __('Select') }} <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif
