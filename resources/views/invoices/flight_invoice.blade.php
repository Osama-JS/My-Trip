<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Flight Invoice') }} - {{ $booking->booking_reference }}</title>
    <style>
        @page {
            margin-top: 35mm;
            margin-bottom: 25mm;
            margin-left: 10mm;
            margin-right: 10mm;
            header: page-header;
            footer: page-footer;
        }

        body {
            font-family: 'tajawal', sans-serif;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            color: #2d3748;
            line-height: 1.7;
            font-size: 12px;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* ── Main Paper ── */
        .paper {
            background: #ffffff;
            padding: 0;
            border-radius: 2px;
        }

        /* ── Top Banner ── */
        .top-banner {
            background-color: #041741;
            padding: 28px 40px 22px 40px;
            color: #ffffff;
        }
        .top-banner-inner {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-name {
            font-size: 26px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .brand-tag {
            font-size: 9px;
            color: #f2cb57;
            letter-spacing: 4px;
            text-transform: uppercase;
            font-weight: 700;
            margin-top: 2px;
        }
        .doc-type {
            font-size: 18px;
            color: #f2cb57;
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .doc-date {
            font-size: 11px;
            color: rgba(255,255,255,0.55);
            margin-top: 4px;
        }

        /* ── Gold Accent Strip ── */
        .gold-strip {
            height: 4px;
            background-color: #f2cb57;
        }

        /* ── Content Area ── */
        .content {
            padding: 30px 40px 20px 40px;
        }

        /* ── PNR Block ── */
        .pnr-block {
            border: 2px solid #041741;
            border-radius: 10px;
            padding: 0;
            margin-bottom: 28px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .pnr-block-top {
            background: #f9f8f3;
            padding: 12px 18px;
            border-bottom: 1px dashed #d5d0c4;
        }
        .pnr-label {
            font-size: 9px;
            color: #8a8575;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 2px;
        }
        .pnr-value {
            font-size: 24px;
            font-weight: 900;
            color: #041741;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace, 'Cairo';
        }
        .pnr-status {
            display: inline-block;
            color: #16a34a;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 8px;
        }
        .pnr-block-bottom {
            padding: 10px 18px;
            background: #ffffff;
        }

        /* ── Section Title ── */
        .section-title {
            font-size: 11px;
            font-weight: 800;
            color: #041741;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding-bottom: 8px;
            margin-bottom: 14px;
            border-bottom: 2px solid #f2cb57;
            page-break-after: avoid;
        }

        /* ── Info Grid ── */
        .info-label {
            font-size: 9px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1px;
        }
        .info-value {
            font-size: 13px;
            color: #1a1f36;
            font-weight: 700;
        }
        .info-row {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        /* ── Flight Route Card ── */
        .route-card {
            background: #041741;
            border-radius: 10px;
            padding: 16px 20px 12px 20px;
            margin-bottom: 24px;
            color: #ffffff;
            page-break-inside: avoid;
        }
        .airport-code {
            font-size: 26px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 1px;
        }
        .airport-label {
            font-size: 9px;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .airport-time {
            font-size: 12px;
            color: #f2cb57;
            font-weight: 700;
            margin-top: 4px;
        }
        .flight-badge {
            display: inline-block;
            color: #f2cb57;
            font-size: 14px;
            font-weight: 900;
            letter-spacing: 1px;
            margin-top: 4px;
        }
        .baggage-strip {
            border-top: 1px dashed rgba(255,255,255,0.15);
            margin-top: 12px;
            padding-top: 8px;
            text-align: center;
            font-size: 11px;
            color: rgba(255,255,255,0.6);
        }
        .baggage-strip strong {
            color: #f2cb57;
        }

        /* ── Premium Tables ── */
        .p-table {
            width: 100%;
            margin-bottom: 24px;
            border-collapse: collapse;
        }
        .p-table tr {
            page-break-inside: avoid;
        }
        .p-table th {
            background: #041741;
            color: #f2cb57;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 10px 14px;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            font-weight: 700;
        }
        .p-table td {
            padding: 11px 14px;
            font-size: 12px;
            border-bottom: 1px solid #f0f1f5;
        }
        .p-table tr:nth-child(even) td {
            background: #fafbfc;
        }

        /* ── Ticket Number Badge ── */
        .e-ticket-badge {
            font-family: 'Courier New', monospace, 'Cairo';
            background: #f9f8f3;
            border: 1px solid #e8e4d9;
            padding: 3px 10px;
            border-radius: 4px;
            color: #041741;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* ── Total Box ── */
        .total-row {
            margin-top: 6px;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }
        .total-box {
            background: #041741;
            padding: 16px 22px;
            border-radius: 8px;
            width: 280px;
            float: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
            page-break-inside: avoid;
        }
        .total-label {
            font-size: 9px;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .total-amount {
            font-size: 26px;
            font-weight: 900;
            color: #f2cb57;
            letter-spacing: 1px;
        }

        /* Clearfix */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* ── Terms ── */
        .terms-section {
            margin-top: 24px;
            padding: 12px 18px;
            background: #fffcf2;
            border: 1px solid #f9eed3;
            border-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 4px solid #f2cb57;
            border-radius: 6px;
            font-size: 9.5px;
            color: #5c5541;
            line-height: 1.7;
        }
        .terms-title {
            font-size: 10px;
            font-weight: 800;
            color: #041741;
            margin-bottom: 6px;
        }



        /* ── Bottom Gold Bar ── */
        .bottom-bar {
            height: 4px;
            background: #f2cb57;
            border-radius: 0 0 2px 2px;
        }

    </style>
</head>
<body>

<!-- WATERMARK -->
<watermarktext content="{{ __('Fly Vio') }}" alpha="0.03" />

<div class="paper">

    <!-- ═══ PAGE HEADER ═══ -->
    <htmlpageheader name="page-header">
        <div class="top-banner">
            <table class="top-banner-inner">
                <tr>
                    <td width="30%" valign="middle" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">
                        <div class="brand-name">{{ __('Fly Vio') }}</div>
                        <div class="brand-tag">{{ __('Premium Travel Services') }}</div>
                    </td>
                    <td width="40%" valign="middle" style="text-align: center;">
                        @php
                            $siteLogoPath = \App\Models\Setting::get('site_logo', 'images/logo-full.png');
                            if (filter_var($siteLogoPath, FILTER_VALIDATE_URL)) {
                                $logoImgSrc = $siteLogoPath;
                            } else {
                                $logoImgSrc = public_path($siteLogoPath);
                            }
                        @endphp
                        @if(filter_var($siteLogoPath, FILTER_VALIDATE_URL) || file_exists(public_path($siteLogoPath)))
                            <img src="{{ $logoImgSrc }}" alt="Logo" style="max-height: 55px; max-width: 140px;">
                        @endif
                    </td>
                    <td width="30%" valign="middle" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                        <div class="doc-type">{{ __('E-Ticket Receipt') }}</div>
                        <div class="doc-date">{{ __('Issued on') }}: {{ $booking->created_at->translatedFormat('d M Y, H:i') }}</div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="gold-strip"></div>
    </htmlpageheader>

    <!-- ═══ CONTENT ═══ -->
    <div class="content">

        <!-- DATA EXTRACTION -->
        @php
            $fb = $booking->flightBooking;
            $origin = $fb->origin ?? 'N/A';
            $destination = $fb->destination ?? 'N/A';
            $airlineCode = $booking->airline_code;
            $baggageInfo = null;

            if (!$airlineCode && $booking->airline_name) {
                $dummyCodes = ['Saudia' => 'SV', 'Flynas' => 'XY', 'Emirates' => 'EK', 'Qatar Airways' => 'QR'];
                $airlineCode = $dummyCodes[$booking->airline_name] ?? null;
            }
            $airlineLogo = $airlineCode ? "https://travelnext.works/api/airlines/{$airlineCode}.gif" : null;

            $primaryPax = $booking->passengers->first();
            $paxFullName = $primaryPax ? trim(strtoupper($primaryPax->first_name . ' ' . $primaryPax->last_name)) : 'N/A';
            $qrData = "PNR: {$booking->booking_reference}\nPAX: {$paxFullName}\nFLIGHT: {$booking->airline_name}";
            $qrDataEncoded = urlencode($qrData);

            $legs = [];
            $itinData = $fb->itinerary_data ?? [];
            if (isset($itinData['FareItineraries']['FareItinerary']['OriginDestinationOptions'])) {
                $options = $itinData['FareItineraries']['FareItinerary']['OriginDestinationOptions'];
                if (isset($options['OriginDestinationOption']['FlightSegment'])) {
                    $options = [$options['OriginDestinationOption']];
                } else {
                    $options = $options['OriginDestinationOption'] ?? [];
                }
                
                foreach($options as $legIndex => $opt) {
                    $segs = $opt['FlightSegment'] ?? $opt;
                    $legSegments = [];
                    if (isset($segs['FlightNumber'])) { 
                        $legSegments[] = $segs; 
                    } else { 
                        foreach($segs as $s) { 
                            $legSegments[] = $s['FlightSegment'] ?? $s; 
                        } 
                    }
                    if (!empty($legSegments)) {
                        $legs[] = $legSegments;
                    }
                }

                if (isset($itinData['FareItineraries']['FareItinerary']['AirItineraryPricingInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown']['PassengerFare']['Baggage'])) {
                    $baggageInfo = current((array)$itinData['FareItineraries']['FareItinerary']['AirItineraryPricingInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown']['PassengerFare']['Baggage']);
                } elseif (isset($itinData['FareItineraries']['FareItinerary']['AirItineraryPricingInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown'][0]['PassengerFare']['Baggage'])) {
                    $baggageInfo = current((array)$itinData['FareItineraries']['FareItinerary']['AirItineraryPricingInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown'][0]['PassengerFare']['Baggage']);
                }
            }

            if (!$baggageInfo) $baggageInfo = "1x 23KG (Checked) + 1x 7KG (Cabin)";
        @endphp

        <!-- ─── PNR BLOCK ─── -->
        <div class="pnr-block">
            <div class="pnr-block-top">
                <table width="100%">
                    <tr>
                        <td width="55%" valign="middle">
                            <div class="pnr-label">{{ __('Booking Reference (PNR)') }}</div>
                            <div class="pnr-value">{{ $booking->booking_reference }}</div>
                            <div class="pnr-status">{{ __('Confirmed & Paid') }}</div>
                        </td>
                        <td width="20%" align="center" valign="middle">
                            @if($airlineLogo)
                                <img src="{{ $airlineLogo }}" alt="{{ $airlineCode }}" height="32" style="border-radius: 4px; border: 1px solid #e8e4d9; padding: 3px; background: #fff;">
                            @endif
                        </td>
                        <td width="25%" align="{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}" valign="middle">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $qrDataEncoded }}&color=041741" alt="QR" width="60" style="border: 2px solid #041741; padding: 3px; background: white; border-radius: 6px;">
                        </td>
                    </tr>
                </table>
            </div>
            <div class="pnr-block-bottom">
                <table width="100%">
                    <tr>
                        <td width="33%">
                            <span class="info-label">{{ __('Airline') }}</span><br>
                            <span class="info-value">{{ $booking->airline_name ?? 'N/A' }}</span>
                        </td>
                        <td width="33%" align="center">
                            <span class="info-label">{{ __('Invoice No.') }}</span><br>
                            <span class="info-value">INV-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td width="34%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                            <span class="info-label">{{ __('Payment Status') }}</span><br>
                            <span class="info-value" style="color: #059669;">{{ __('Fully Paid') }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- ─── FLIGHT ROUTE CARD ─── -->
        @if(!empty($legs))
            @foreach($legs as $legIndex => $segments)
                @php
                    $firstSeg = $segments[0];
                    $lastSeg = end($segments);
                    $depCode = $firstSeg['DepartureAirportLocationCode'] ?? ($firstSeg['DepartureAirport']['LocationCode'] ?? '');
                    $arrCode = $lastSeg['ArrivalAirportLocationCode'] ?? ($lastSeg['ArrivalAirport']['LocationCode'] ?? '');
                    
                    $depAir = \App\Models\Airport::where('airport_code', $depCode)->first();
                    $arrAir = \App\Models\Airport::where('airport_code', $arrCode)->first();
                    $depCity = $depAir ? (app()->getLocale() == 'ar' ? $depAir->city_ar : $depAir->city) : $depCode;
                    $arrCity = $arrAir ? (app()->getLocale() == 'ar' ? $arrAir->city_ar : $arrAir->city) : $arrCode;

                    $depDate = isset($firstSeg['DepartureDateTime']) ? \Carbon\Carbon::parse($firstSeg['DepartureDateTime'])->translatedFormat('d M Y, H:i') : 'N/A';
                    $arrDate = isset($lastSeg['ArrivalDateTime']) ? \Carbon\Carbon::parse($lastSeg['ArrivalDateTime'])->translatedFormat('d M Y, H:i') : 'N/A';
                    $flightNo = ($firstSeg['MarketingAirlineCode'] ?? '') . ' ' . ($firstSeg['FlightNumber'] ?? '');
                @endphp
                <div class="route-card">
                    @if(count($legs) > 1)
                        <div style="font-size: 11px; color: #f2cb57; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; border-bottom: 1px dashed rgba(255,255,255,0.2); padding-bottom: 6px;">
                            {{ $legIndex == 0 ? __('Outbound Flight') : __('Return Flight') }}
                        </div>
                    @endif
                    <table width="100%">
                        <tr>
                            <td width="35%" align="center" valign="middle">
                                <div class="airport-code">{{ $depCode }}</div>
                                <div style="font-size: 13px; font-weight: 700; margin-bottom: 4px;">{{ $depCity }}</div>
                                <div class="airport-label">{{ __('Departure') }}</div>
                                <div class="airport-time" dir="ltr">{{ $depDate }}</div>
                            </td>
                            <td width="30%" align="center" valign="middle">
                                <div style="font-size: 11px; color: rgba(255,255,255,0.35); margin-bottom: 4px;">
                                    ──────
                                    <svg width="14" height="14" viewBox="0 0 24 24" style="vertical-align: middle; margin: 0 4px;">
                                        <path fill="rgba(255,255,255,0.35)" d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                                    </svg>
                                    ──────
                                </div>
                                <div class="flight-badge">{{ $flightNo }} {{ count($segments) > 1 ? '(' . (count($segments)-1) . ' ' . __('Stops') . ')' : '' }}</div>
                            </td>
                            <td width="35%" align="center" valign="middle">
                                <div class="airport-code">{{ $arrCode }}</div>
                                <div style="font-size: 13px; font-weight: 700; margin-bottom: 4px;">{{ $arrCity }}</div>
                                <div class="airport-label">{{ __('Arrival') }}</div>
                                <div class="airport-time" dir="ltr">{{ $arrDate }}</div>
                            </td>
                        </tr>
                    </table>
                    <div class="baggage-strip">
                        <svg width="12" height="12" viewBox="0 0 24 24" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px;">
                            <path fill="rgba(255,255,255,0.6)" d="M17 6h-2V4c0-1.1-.9-2-2-2h-2c-1.1 0-2 .9-2 2v2H7c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM10 4h4v2h-4V4zm7 15H7V8h10v11z"/>
                            <path fill="rgba(255,255,255,0.6)" d="M9 10h2v7H9zm4 0h2v7h-2z"/>
                        </svg>
                        {{ __('Baggage Allowance') }}: <strong>{{ is_array($baggageInfo) ? implode(', ', $baggageInfo) : $baggageInfo }}</strong>
                    </div>
                </div>
            @endforeach
        @endif

        <!-- ─── DETAILS GRID ─── -->
        <table style="margin-bottom: 26px;">
            <tr>
                <td width="48%" valign="top">
                    <div style="padding: 18px; min-height: 140px;">
                        <div class="section-title" style="margin-bottom: 14px; font-size: 13px; color: #041741; border: none;">
                            <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f464.png" width="14" height="14" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px;">
                            {{ __('Passenger Details') }}
                        </div>
                        <table width="100%">
                            <tr>
                                <td width="50%" valign="top">
                                    <span class="info-label">{{ __('Primary Contact') }}</span><br>
                                    <span class="info-value" style="color: #1a1f36; font-weight: 700;">{{ $booking->passengers->first()->title ?? '' }} {{ $booking->passengers->first()->first_name ?? 'N/A' }} {{ $booking->passengers->first()->last_name ?? '' }}</span>
                                </td>
                                <td width="50%" valign="top">
                                    <span class="info-label">{{ __('Phone') }}</span><br>
                                    <span class="info-value" dir="ltr" style="font-weight: 700;">{{ $booking->contact_phone ?? '—' }}</span>
                                </td>
                            </tr>
                            @if(isset($booking->user) && $booking->user->email)
                            <tr>
                                <td colspan="2" style="padding-top: 12px;">
                                    <span class="info-label">{{ __('Email') }}</span><br>
                                    <span class="info-value">{{ $booking->user->email }}</span>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </td>
                <td width="4%"></td>
                <td width="48%" valign="top">
                    <div style="padding: 18px; min-height: 140px;">
                        <div class="section-title" style="margin-bottom: 14px; font-size: 13px; color: #041741; border: none;">
                            <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f4b3.png" width="14" height="14" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px;">
                            {{ __('Payment Summary') }}
                        </div>
                        <table width="100%">
                            <tr>
                                <td width="50%" valign="top">
                                    <span class="info-label">{{ __('Payment Method') }}</span><br>
                                    <span class="info-value" style="font-weight: 700;">{{ __('Electronic Payment') }}</span>
                                </td>
                                <td width="50%" valign="top">
                                    <span class="info-label">{{ __('Booking Date') }}</span><br>
                                    <span class="info-value" style="font-weight: 700;">{{ $booking->created_at->translatedFormat('d M Y') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 12px;">
                                    <span class="info-label">{{ __('Total Amount') }}</span><br>
                                    <span class="info-value" style="color: #d9a01c; font-size: 18px; font-weight: 900;">{{ number_format($booking->total_amount, 2) }} {{ $booking->currency }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- ─── PASSENGERS TABLE ─── -->
        @if($booking->passengers && $booking->passengers->count() > 0)
        <div class="section-title" style="margin-top: 4px;">
            <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f9f3.png" width="14" height="14" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px;">
            {{ __('Travelers List') }}
        </div>
        <table class="p-table">
            <thead>
                <tr>
                    <th width="8%">#</th>
                    <th width="42%">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f464.png" width="10" height="10" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px; opacity: 0.8;">
                        {{ __('Passenger Name') }}
                    </th>
                    <th width="20%">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f3ab.png" width="10" height="10" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px; opacity: 0.8;">
                        {{ __('Type') }}
                    </th>
                    <th width="30%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f4c4.png" width="10" height="10" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px; opacity: 0.8;">
                        {{ __('E-Ticket Number') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->passengers as $paxIndex => $pax)
                @php
                    $paxName = trim(strtoupper($pax->first_name ?? '') . ' ' . strtoupper($pax->last_name ?? ''));
                    $ticket = isset($eTickets) && isset($eTickets[$paxName]) ? $eTickets[$paxName] : null;

                    $extraServices = $booking->flightBooking->extra_services ?? [];
                    if (is_string($extraServices)) $extraServices = json_decode($extraServices, true);
                    $paxExtras = $extraServices[$paxIndex]['extra_services_details'] ?? [];
                @endphp
                <tr>
                    <td style="color:#9ca3af; font-weight:700;">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                    <td style="font-weight: 700; color: #1a1f36;">{{ $pax->title }} {{ $pax->first_name }} {{ $pax->last_name }}</td>
                    <td>{{ __(ucfirst(strtolower($pax->passenger_type))) }}</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                        @if($ticket)
                            <span class="e-ticket-badge">{{ $ticket }}</span>
                        @else
                            <span style="color:#c0c5d0; font-style:italic; font-size:11px;">{{ __('Pending') }}</span>
                        @endif
                    </td>
                </tr>
                @if(!empty($paxExtras))
                <tr>
                    <td style="border-top: none;"></td>
                    <td colspan="3" style="border-top: none; padding-top: 2px; padding-bottom: 8px;">
                        <div style="font-size: 10px; color: #4b5563;">
                            <strong style="color: #041741;">{{ __('Extra Services') }}:</strong>
                            @foreach($paxExtras as $extra)
                                <span style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px; display: inline-block; margin-bottom: 4px; border: 1px solid #e5e7eb;">
                                    {{ $extra['desc'] ?? $extra['code'] ?? 'Extra Service' }}
                                    @if(isset($extra['price']) && $extra['price'] > 0)
                                        <span style="color: #10b981; font-weight: 700;">(+{{ $extra['price'] }} {{ $extra['currency'] ?? 'SAR' }})</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- ─── FARE BREAKDOWN ─── -->
        <div class="section-title" style="margin-top: 10px;">
            <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f4b8.png" width="14" height="14" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px;">
            {{ __('Fare Breakdown') }}
        </div>
        <table class="p-table">
            <thead>
                <tr>
                    <th width="70%">{{ __('Description') }}</th>
                    <th width="30%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ __('Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div style="font-weight: 700; color: #1a1f36;">{{ __('Flight Ticket Fare') }}</div>
                        <div style="font-size:10px; color:#9ca3af; margin-top:2px;">
                            @if($airlineLogo)
                                <img src="{{ $airlineLogo }}" height="11" style="vertical-align: middle; margin-right: 4px; margin-left: 4px;">
                            @endif
                            {{ $booking->airline_name ?? 'N/A' }} · {{ $origin }} → {{ $destination }} · {{ __('Reference') }}: {{ $booking->booking_reference }}
                        </div>
                    </td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; font-weight: 800; color: #1a1f36; font-size: 14px; vertical-align: middle;">
                        {{ number_format($booking->total_amount, 2) }} {{ $booking->currency }}
                    </td>
                </tr>
                <tr>
                    <td style="background: #041741; color: #ffffff; font-size: 13px; font-weight: 700; padding: 14px 18px; text-transform: uppercase; letter-spacing: 1px;">
                        {{ __('Total Paid') }}
                    </td>
                    <td style="background: #041741; color: #f2cb57; font-size: 18px; font-weight: 900; padding: 14px 18px; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                        {{ number_format($booking->total_amount, 2) }} {{ $booking->currency }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- ─── TERMS ─── -->
        <div class="terms-section">
            <div class="terms-title">
                <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/26a0.png" width="12" height="12" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px;">
                {{ __('Important Notes:') }}
            </div>
            1. {{ __('This document serves as an e-ticket receipt and tax invoice. No signature is required.') }}<br>
            2. {{ __('Check-in opens 3 hours prior to departure. Boarding gates close 20 minutes before departure.') }}<br>
            3. {{ __('Please ensure you have valid travel documents (passport, visa) for your entire journey.') }}
        </div>



    </div><!-- /content -->



</div><!-- /paper -->

<!-- ═══ PAGE FOOTER ═══ -->
<htmlpagefooter name="page-footer">
    <div style="height: 4px; background: #f2cb57; margin: 0 40px; border-radius: 4px;"></div>
    <div style="padding-top: 8px; font-size: 9px; color: #9ca3af; margin: 0 40px;">
        <table width="100%">
            <tr>
                <td width="33%" style="color: #041741; font-weight: 700;">{{ __('Fly Vio') }} &copy; {{ date('Y') }}</td>
                <td width="33%" align="center">{{ __('System Generated Document') }}</td>
                <td width="33%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; direction: ltr;">
                    Page {PAGENO} of {nbpg}
                </td>
            </tr>
        </table>
    </div>
</htmlpagefooter>

</body>
</html>
