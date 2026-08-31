<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Hotel Voucher') }} - {{ $booking->supplier_confirmation_num ?? $booking->reference_num }}</title>
    <style>
        @page {
            margin: 0px;
            footer: page-footer;
        }

        body {
            font-family: 'tajawal', sans-serif;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            color: #2d3748;
            line-height: 1.7;
            font-size: 12px;
            background: #edf0f4;
            margin: 0;
            padding: 0;
        }

        /* ── Main Paper ── */
        .paper {
            background: #ffffff;
            margin: 30px 35px;
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

        /* ── Tables ── */
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 0; vertical-align: top; }

        /* ── Hotel Name Card ── */
        .hotel-card {
            border: 2px solid #041741;
            border-radius: 10px;
            padding: 0;
            margin-bottom: 28px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .hotel-card-top {
            background: #f9f8f3;
            padding: 18px 22px;
            border-bottom: 1px dashed #d5d0c4;
        }
        .hotel-name {
            font-size: 22px;
            font-weight: 900;
            color: #041741;
            margin-bottom: 4px;
        }
        .hotel-location {
            font-size: 12px;
            color: #7d879c;
        }
        .hotel-card-bottom {
            padding: 12px 22px;
            background: #ffffff;
        }

        /* ── Ref Box ── */
        .ref-box {
            text-align: center;
            padding: 18px 14px;
        }
        .ref-label {
            font-size: 9px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }
        .ref-value {
            font-size: 22px;
            font-weight: 900;
            color: #041741;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace, 'tajawal';
        }
        .ref-status {
            display: inline-block;
            color: #16a34a;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 8px;
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

        /* ── Stay Card ── */
        .stay-card {
            background: #041741;
            border-radius: 10px;
            padding: 24px 20px 18px 20px;
            margin-bottom: 28px;
            color: #ffffff;
            page-break-inside: avoid;
        }
        .date-big {
            font-size: 28px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 1px;
        }
        .date-label {
            font-size: 9px;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .date-detail {
            font-size: 12px;
            color: #f2cb57;
            font-weight: 700;
            margin-top: 4px;
        }
        .nights-badge {
            display: inline-block;
            color: #f2cb57;
            font-size: 16px;
            font-weight: 900;
            letter-spacing: 1px;
        }
        .stay-info-strip {
            border-top: 1px dashed rgba(255,255,255,0.15);
            margin-top: 16px;
            padding-top: 12px;
        }
        .stay-info-strip-label {
            font-size: 9px;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .stay-info-strip-value {
            font-size: 13px;
            color: #ffffff;
            font-weight: 700;
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

        /* ── Pax Type Badge ── */
        .pax-badge {
            display: inline-block;
            background: #f9f8f3;
            border: 1px solid #e8e4d9;
            padding: 3px 12px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            color: #041741;
            letter-spacing: 0.5px;
        }

        /* ── Notes ── */
        .notes-section {
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
        .notes-title {
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

        /* Clearfix */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

    </style>
</head>
<body>

<!-- WATERMARK -->
<watermarktext content="{{ __('Fly Vio') }}" alpha="0.03" />

<div class="paper">

    <!-- ═══ TOP BANNER ═══ -->
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
                    <div class="doc-type">{{ __('Hotel Voucher') }}</div>
                    <div class="doc-date">{{ __('Issued on') }}: {{ now()->translatedFormat('d M Y, H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>
    <div class="gold-strip"></div>

    <!-- ═══ CONTENT ═══ -->
    <div class="content">

        @php
            $qrData = "REF: " . ($booking->supplier_confirmation_num ?? $booking->reference_num) . "\nHOTEL: {$booking->hotel_name}\nGUEST: " . ($booking->user->name ?? 'Guest') . "\nCHECK-IN: " . $booking->check_in->format('d M Y');
            $qrDataEncoded = urlencode($qrData);
        @endphp

        <!-- ─── HOTEL & REFERENCE BLOCK ─── -->
        <div class="hotel-card">
            <div class="hotel-card-top">
                <table width="100%">
                    <tr>
                        <td width="55%" valign="middle">
                            <div class="hotel-name">
                                <svg width="22" height="22" viewBox="0 0 24 24" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px;">
                                    <path fill="#f2cb57" d="M19 2H5c-1.1 0-2 .9-2 2v18h18V4c0-1.1-.9-2-2-2zm-8 14H7v-2h4v2zm0-4H7v-2h4v2zm0-4H7V6h4v2zm6 8h-4v-2h4v2zm0-4h-4v-2h4v2zm0-4h-4V6h4v2z"/>
                                </svg>
                                {{ $booking->hotel_name }}
                                <span style="font-family: 'DejaVuSans', 'freesans', sans-serif; color: #f2cb57; font-size: 15px; letter-spacing: 2px; margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 8px;">
                                    &#9733;&#9733;&#9733;&#9733;&#9733;
                                </span>
                            </div>
                            <div class="hotel-location">
                                <svg width="14" height="14" viewBox="0 0 24 24" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px;">
                                    <path fill="#f2cb57" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                {{ $booking->city_name }}, {{ $booking->country_name }}
                            </div>
                        </td>
                        <td width="20%" align="center" valign="middle">
                            <div style="border: 1px solid #f2cb57; border-radius: 8px; padding: 6px; display: inline-block; background: rgba(242, 203, 87, 0.05); text-align: center;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $qrDataEncoded }}&color=041741" alt="QR" width="70" style="background: white; border-radius: 4px;">
                                <div style="font-size: 9px; color: #041741; font-weight: 700; margin-top: 4px; text-transform: uppercase; white-space: nowrap;">{{ __('Scan at Reception') }}</div>
                            </div>
                        </td>
                        <td width="25%" valign="middle">
                            <div class="ref-box">
                                <div class="ref-label">{{ __('Supplier Ref') }}</div>
                                <div class="ref-value">{{ $booking->supplier_confirmation_num ?? __('Pending') }}</div>
                                <div class="ref-status">{{ __('Confirmed') }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="hotel-card-bottom">
                <table width="100%">
                    <tr>
                        <td width="33%">
                            <span class="info-label">{{ __('Internal Ref') }}</span><br>
                            <span class="info-value">{{ $booking->reference_num }}</span>
                        </td>
                        <td width="33%" align="center">
                            <span class="info-label">{{ __('Room Type') }}</span><br>
                            <span class="info-value" style="color: #041741;">
                                <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f6cf.png" width="14" height="14" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px;">
                                {{ $booking->room_name }}
                            </span>
                        </td>
                        <td width="34%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                            <span class="info-label">{{ __('Board') }}</span><br>
                            <span class="info-value">
                                <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f37d.png" width="14" height="14" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px;">
                                {{ $booking->board_type ?? __('Room Only') }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- ─── STAY DATES CARD ─── -->
        <div class="stay-card">
            <table width="100%">
                <tr>
                    <td width="35%" align="center" valign="middle">
                        <div class="date-big">{{ $booking->check_in->translatedFormat('d M') }}</div>
                        <div class="date-label">{{ __('Check-in') }}</div>
                        <div class="date-detail" dir="ltr">{{ $booking->check_in->translatedFormat('l, Y') }}</div>
                    </td>
                    <td width="30%" align="center" valign="middle">
                        <div style="margin-bottom: 8px; white-space: nowrap;">
                            <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #f2cb57; vertical-align: middle;"></span>
                            <span style="display: inline-block; width: 30px; border-top: 1.5px dashed rgba(242, 203, 87, 0.5); vertical-align: middle; margin: 0 4px;"></span>
                            <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f319.png" width="14" height="14" style="vertical-align: middle;">
                            <span style="display: inline-block; width: 30px; border-top: 1.5px dashed rgba(242, 203, 87, 0.5); vertical-align: middle; margin: 0 4px;"></span>
                            <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #f2cb57; vertical-align: middle;"></span>
                        </div>
                        <div class="nights-badge">{{ $booking->check_in->diffInDays($booking->check_out) }} {{ __('Nights') }}</div>
                    </td>
                    <td width="35%" align="center" valign="middle">
                        <div class="date-big">{{ $booking->check_out->translatedFormat('d M') }}</div>
                        <div class="date-label">{{ __('Check-out') }}</div>
                        <div class="date-detail" dir="ltr">{{ $booking->check_out->translatedFormat('l, Y') }}</div>
                    </td>
                </tr>
            </table>
            <div class="stay-info-strip">
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <span class="stay-info-strip-label">{{ __('Payment Status') }}</span><br>
                            <span style="color: #f2cb57; font-weight: 900; font-size: 13px;">{{ __('PREPAID - Fully Paid') }}</span>
                        </td>
                        <td width="50%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                            <span class="stay-info-strip-label">{{ __('Total Amount') }}</span><br>
                            <span style="color: #f2cb57; font-weight: 900; font-size: 18px;">{{ number_format($booking->total_price, 2) }} {{ $booking->currency }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- ─── GUEST DETAILS ─── -->
        <div class="section-title">{{ __('Guest Details') }}</div>
        <table class="p-table">
            <thead>
                <tr>
                    <th width="10%" style="text-align: center;">#</th>
                    <th width="15%" style="text-align: center;">{{ __('Room') }}</th>
                    <th width="50%">{{ __('Guest Name') }}</th>
                    <th width="25%">{{ __('Pax Type') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $guestIndex = 0; @endphp
                @if($booking->pax_details && is_array($booking->pax_details))
                    @foreach($booking->pax_details as $room)
                        @php $passengers = $booking->passengers; @endphp
                        @if($passengers->isNotEmpty())
                            @foreach($passengers as $pax)
                                @php $guestIndex++; @endphp
                                <tr>
                                    <td style="text-align: center; color:#9ca3af; font-weight:700;">{{ str_pad($guestIndex, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td style="text-align: center; font-weight: 700; color: #041741;">{{ $room['room_no'] ?? 1 }}</td>
                                    <td style="font-weight: 700; color: #1a1f36;">{{ $pax->name }}</td>
                                    <td><span class="pax-badge">{{ __(ucfirst(strtolower($pax->passenger_type))) }}</span></td>
                                </tr>
                            @endforeach
                        @else
                            @if(isset($room['pax']))
                                @foreach($room['pax'] as $pax)
                                    @php $guestIndex++; @endphp
                                    <tr>
                                        <td style="text-align: center; color:#9ca3af; font-weight:700;">{{ str_pad($guestIndex, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td style="text-align: center; font-weight: 700; color: #041741;">{{ $room['room_no'] ?? $loop->parent->iteration }}</td>
                                        <td style="font-weight: 700; color: #1a1f36;">{{ $pax['Title'] ?? '' }} {{ $pax['FirstName'] ?? '' }} {{ $pax['LastName'] ?? '' }}</td>
                                        <td><span class="pax-badge">{{ ($pax['type'] ?? 'AD') === 'CH' ? __('Child') : __('Adult') }}</span></td>
                                    </tr>
                                @endforeach
                            @endif
                        @endif
                    @endforeach
                @else
                    <tr>
                        <td style="text-align: center; color:#9ca3af; font-weight:700;">01</td>
                        <td style="text-align: center; font-weight: 700; color: #041741;">1</td>
                        <td style="font-weight: 700; color: #1a1f36;">{{ $booking->user->name ?? __('Primary Guest') }}</td>
                        <td><span class="pax-badge">{{ __('Adult') }}</span></td>
                    </tr>
                @endif
            </tbody>
        </table>

        @php
            $insAmount = (float)($booking->insurance_amount ?? 0);
            $policy = $booking->insurancePolicy;
        @endphp
        @if($insAmount > 0 || $policy)
        <!-- ─── TRAVEL INSURANCE BADGE ─── -->
        <div style="margin-top: 15px; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 8px; padding: 10px 14px;">
            <table width="100%">
                <tr>
                    <td width="72%">
                        <div style="font-weight: 800; color: #166534; font-size: 11.5px;">
                            🛡️ {{ __('Comprehensive Travel & Medical Insurance Included') }}
                        </div>
                        <div style="font-size: 9.5px; color: #15803d; margin-top: 2px;">
                            {{ __('Policy No:') }} <strong>{{ $policy->policy_number ?? ('POL-'.strtoupper(substr(md5($booking->id), 0, 8))) }}</strong> · 
                            {{ __('Coverage Limit:') }} <strong>$500,000 USD</strong> · 
                            {{ __('Status:') }} <strong style="color:#059669;">{{ __('Active / Insured') }}</strong>
                        </div>
                        <div style="font-size: 9px; color: #16a34a; margin-top: 1px;">
                            {{ __('Emergency Medical, Trip Delay, Baggage Protection & 24/7 Telehealth') }}
                        </div>
                    </td>
                    <td width="28%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; vertical-align: middle;">
                        <span style="background: #16a34a; color: #fff; font-size: 9.5px; font-weight: 800; padding: 4px 8px; border-radius: 12px; text-transform: uppercase;">
                            ✓ {{ __('INSURED') }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        @endif

        <!-- ─── IMPORTANT NOTES ─── -->
        <div class="notes-section">
            <div class="notes-title">
                <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/26a0.png" width="12" height="12" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px;">
                {{ __('Important Notes:') }}
            </div>
            1. {{ __('Please present this voucher along with original ID upon arrival.') }}<br>
            2. {{ __('This booking is PREPAID in full by our agency. Do not collect any room charges for the booked stay from the guest.') }}<br>
            3. {{ __('Any extra incidentals (mini-bar, room service, etc.) are to be settled by the guest directly with the hotel.') }}
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
