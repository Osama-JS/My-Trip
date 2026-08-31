{{-- Flight Results Partial - loaded via AJAX into fe-results-section --}}
@php
    // The Travelopro response for this specific version/search returns 
    // FareItineraries as a sequential array where each item contains a 'FareItinerary' key.
    $itineraries = $results['AirSearchResponse']['AirSearchResult']['FareItineraries'] ?? [];
    
    // Normalize to array of itineraries
    if (!empty($itineraries) && !isset($itineraries[0])) {
        $itineraries = [$itineraries];
    }

    $sessionId = $results['AirSearchResponse']['session_id'] ?? ($results['AirSearchResponse']['AirSearchResult']['SessionId'] ?? '');
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
            <button type="button" class="fr-sort-btn active" data-sort="price">
                <span>{{ __('Price') }}</span>
                <i class="fas fa-arrow-up-long ms-1 sort-dir-icon" id="sortDirIcon_price"></i>
            </button>
            <button type="button" class="fr-sort-btn" data-sort="duration">
                <span>{{ __('Duration') }}</span>
                <i class="fas fa-arrow-up-long ms-1 sort-dir-icon" id="sortDirIcon_duration" style="display:none;"></i>
            </button>
            <button type="button" class="fr-sort-btn" data-sort="departure">
                <span>{{ __('Departure') }}</span>
                <i class="fas fa-arrow-up-long ms-1 sort-dir-icon" id="sortDirIcon_departure" style="display:none;"></i>
            </button>
            
            <button type="button" class="fr-order-toggle-btn" id="sortOrderToggleBtn" title="{{ __('Toggle Ascending / Descending') }}">
                <i class="fas fa-sort-amount-down-alt" id="mainSortOrderIcon"></i>
                <span id="sortOrderText">{{ __('Ascending') }}</span>
            </button>
        </div>
    </div>

    {{-- Flights List --}}
    <div id="flightResultsList">
        @foreach($itineraries as $index => $itin)
            @php
                // Handle nested structure: each item is usually ['FareItinerary' => [...]]
                $itineraryData = $itin['FareItinerary'] ?? $itin;
                
                $fareInfo = $itineraryData['AirItineraryFareInfo'];
                $price = floatval($fareInfo['ItinTotalFares']['TotalFare']['Amount']);
                $currency = $fareInfo['ItinTotalFares']['TotalFare']['CurrencyCode'];
                $validatingCarrier = $itineraryData['ValidatingAirlineCode'];
                $options = $itineraryData['OriginDestinationOptions'];
                if (isset($options['OriginDestinationOption'])) {
                    $options = [$options];
                }
                $maxStops = 0;
                $totalDurationMinutes = 0;
                $firstDepTime = null;
                $lastArrTime = null;
                $totalDurationStr = '';
                $outboundSegmentsData = [];
                $mainBaggageDisplay = '1 Piece (23 KG)';

                foreach($options as $opt) {
                    $segs = isset($opt['OriginDestinationOption']['FlightSegment'])
                        ? [$opt['OriginDestinationOption']]
                        : $opt['OriginDestinationOption'];
                    $d1 = \Carbon\Carbon::parse($segs[0]['FlightSegment']['DepartureDateTime']);
                    $d2 = \Carbon\Carbon::parse(end($segs)['FlightSegment']['ArrivalDateTime']);
                    $totalDurationMinutes += $d1->diffInMinutes($d2);
                    if (!$firstDepTime) {
                        $firstDepTime = $d1->format('H:i');
                        $totalDurationStr = $d1->diff($d2)->format('%hh %im');
                        
                        // Extract outbound segments for summary display
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

                            $rawB = $s['Baggage'] ?? ($fareInfo['Baggage'] ?? ($itineraryData['Baggage'] ?? null));
                            $bDisplay = '1 Piece (23 KG)';
                            if (!empty($rawB)) {
                                $bUpper = strtoupper(trim(strval($rawB)));
                                if (preg_match('/(\d+)\s*(K|KG|KGS)/i', $bUpper, $m)) {
                                    $bDisplay = $m[1] . ' KG';
                                } elseif (preg_match('/(\d+)\s*(P|PC|PIECE|PIECES)/i', $bUpper, $m)) {
                                    $bDisplay = $m[1] . ' ' . ($m[1] > 1 ? __('Pieces') : __('Piece')) . ' (23 KG)';
                                } elseif ($bUpper === '0' || $bUpper === '0P' || $bUpper === '0K') {
                                    $bDisplay = __('Cabin Bag Only');
                                } elseif (is_numeric($bUpper)) {
                                    $bDisplay = $bUpper . ' KG';
                                } else {
                                    $bDisplay = $rawB;
                                }
                            }
                            if ($si === 0) $mainBaggageDisplay = $bDisplay;

                            $outboundSegmentsData[] = [
                                'from' => $s['DepartureAirportLocationCode'],
                                'to' => $s['ArrivalAirportLocationCode'],
                                'dep' => \Carbon\Carbon::parse($s['DepartureDateTime'])->format('H:i'),
                                'arr' => \Carbon\Carbon::parse($s['ArrivalDateTime'])->format('H:i'),
                                'dep_datetime' => $s['DepartureDateTime'],
                                'arr_datetime' => $s['ArrivalDateTime'],
                                'layover' => $layStr,
                                'layover_airport' => $s['ArrivalAirportLocationCode'],
                                'baggage' => $bDisplay,
                                'flight_no' => ($s['MarketingAirlineCode'] ?? $validatingCarrier) . ' ' . ($s['FlightNumber'] ?? ''),
                            ];
                        }
                    }
                    $lastArrTime = $d2->format('H:i');
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

                            $optLayovers = [];
                            $optSegCount = count($segments);
                            for ($si = 0; $si < $optSegCount - 1; $si++) {
                                $cArr = \Carbon\Carbon::parse($segments[$si]['FlightSegment']['ArrivalDateTime']);
                                $nDep = \Carbon\Carbon::parse($segments[$si+1]['FlightSegment']['DepartureDateTime']);
                                $dM = $cArr->diffInMinutes($nDep);
                                $lh = floor($dM / 60);
                                $lm = $dM % 60;
                                $optLayovers[] = $segments[$si]['FlightSegment']['ArrivalAirportLocationCode'] . ' (' . ($lh > 0 ? "{$lh}h " : '') . "{$lm}m)";
                            }
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
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <span class="fr-stop-badge {{ $stops == 0 ? 'nonstop' : '' }}">
                                        {{ $stops == 0 ? __('Non-stop') : ($stops . ' ' . ($stops == 1 ? __('Stop') : __('Stops'))) }}
                                    </span>
                                    @if(!empty($optLayovers))
                                        <span class="fr-layover-badge" style="font-size: 0.72rem; color: #b45309; font-weight: 700; background: #fffbeb; padding: 2px 6px; border-radius: 4px; border: 1px solid #fde68a;">
                                            <i class="far fa-clock me-1"></i>{{ __('Layover') }}: {{ implode(', ', $optLayovers) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="fr-leg-time" style="text-align: end;">
                                <span class="fr-time">{{ $arr->format('H:i') }}</span>
                                <span class="fr-airport">{{ $lastSeg['ArrivalAirportLocationCode'] }}</span>
                                <span class="fr-date-label">{{ $arr->format('d M') }}</span>
                            </div>
                        </div>
                    @endforeach
                    
                    {{-- Baggage Allowance info bar --}}
                    <div class="fr-card-perks" style="display: flex; align-items: center; gap: 15px; margin-top: 8px; padding-top: 8px; border-top: 1px dashed var(--gray-100); font-size: 0.78rem; color: var(--gray-600);">
                        <span style="display: inline-flex; align-items: center; gap: 5px; color: #0f766e; font-weight: 700; background: #f0fdfa; padding: 3px 8px; border-radius: 5px; border: 1px solid #ccfbf1;">
                            <i class="fas fa-suitcase-rolling"></i> {{ __('Baggage') }}: {{ $mainBaggageDisplay }}
                        </span>
                        <span style="display: inline-flex; align-items: center; gap: 5px; color: #475569;">
                            <i class="fas fa-couch"></i> {{ $searchParams['class'] ?? 'Economy' }}
                        </span>
                    </div>
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
                        'total_amount' => $price,
                        'airline' => $validatingCarrier,
                        'dep_time' => $firstDepTime,
                        'arr_time' => $lastArrTime,
                        'stops' => $maxStops,
                        'duration' => $totalDurationStr,
                        'baggage' => $mainBaggageDisplay,
                        'segments' => $outboundSegmentsData
                    ])) }}" class="fe-btn fe-btn-primary fr-select-btn">
                        {{ __('Select') }} <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif
