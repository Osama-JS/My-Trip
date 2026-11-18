<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Hotel Voucher') }} - {{ $booking->supplier_confirmation_num ?? $booking->reference_num }}</title>
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
            line-height: 1.6;
            font-size: 11px;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* ── Main Paper ── */
        .paper {
            background: #ffffff;
            padding: 0;
            border-radius: 2px;
            width: 100%;
        }

        /* ── Top Banner ── */
        .top-banner {
            background-color: #041741;
            padding: 20px 30px 16px 30px;
            color: #ffffff;
        }
        .top-banner-inner {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-name {
            font-size: 22px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .brand-tag {
            font-size: 8px;
            color: #f2cb57;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 700;
            margin-top: 2px;
        }
        .doc-type {
            font-size: 15px;
            color: #f2cb57;
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .doc-date {
            font-size: 10px;
            color: rgba(255,255,255,0.55);
            margin-top: 3px;
        }

        /* ── Gold Accent Strip ── */
        .gold-strip {
            height: 3px;
            background-color: #f2cb57;
        }

        /* ── Content Area ── */
        .content {
            padding: 22px 30px 16px 30px;
        }

        /* ── Tables ── */
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 0; vertical-align: top; }

        /* ── Hotel Name Card ── */
        .hotel-card {
            border: 1.5px solid #041741;
            border-radius: 8px;
            padding: 0;
            margin-bottom: 20px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .hotel-card-top {
            background: #f9f8f3;
            padding: 14px 18px;
            border-bottom: 1px dashed #d5d0c4;
        }
        .hotel-name {
            font-size: 18px;
            font-weight: 900;
            color: #041741;
            margin-bottom: 3px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .hotel-location {
            font-size: 11px;
            color: #7d879c;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .hotel-card-bottom {
            padding: 10px 18px;
            background: #ffffff;
        }

        /* ── Ref Box ── */
        .ref-box {
            text-align: center;
            padding: 14px 10px;
        }
        .ref-label {
            font-size: 8px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 3px;
        }
        .ref-value {
            font-size: 18px;
            font-weight: 900;
            color: #041741;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace, 'tajawal';
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .ref-status {
            display: inline-block;
            color: #16a34a;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 6px;
        }

        /* ── Section Title ── */
        .section-title {
            font-size: 10px;
            font-weight: 800;
            color: #041741;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding-bottom: 6px;
            margin-bottom: 10px;
            border-bottom: 2px solid #f2cb57;
            page-break-after: avoid;
        }

        /* ── Info Grid ── */
        .info-label {
            font-size: 8px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1px;
        }
        .info-value {
            font-size: 11px;
            color: #1a1f36;
            font-weight: 700;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .info-row {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        /* ── Stay Card ── */
        .stay-card {
            background-color: #041741;
            background: #041741;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #ffffff;
            page-break-inside: avoid;
            width: 100%;
        }
        .date-big {
            font-size: 22px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 1px;
        }
        .date-label {
            font-size: 8px;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .date-detail {
            font-size: 10px;
            color: #f2cb57;
            font-weight: 700;
            margin-top: 3px;
        }
        .nights-badge {
            display: inline-block;
            color: #f2cb57;
            font-size: 14px;
            font-weight: 900;
            letter-spacing: 1px;
        }
        .stay-info-strip {
            border-top: 1px dashed rgba(255,255,255,0.15);
            margin-top: 12px;
            padding-top: 10px;
        }
        .stay-info-strip-label {
            font-size: 8px;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .stay-info-strip-value {
            font-size: 11px;
            color: #ffffff;
            font-weight: 700;
        }

        /* ── Premium Tables ── */
        .p-table {
            width: 100%;
            margin-bottom: 18px;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .p-table tr {
            page-break-inside: avoid;
        }
        .p-table th {
            background: #041741;
            color: #f2cb57;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 8px 12px;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            font-weight: 700;
        }
        .p-table td {
            padding: 9px 12px;
            font-size: 11px;
            border-bottom: 1px solid #f0f1f5;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .p-table tr:nth-child(even) td {
            background: #fafbfc;
        }

        /* ── Pax Type Badge ── */
        .pax-badge {
            display: inline-block;
            background: #f9f8f3;
            border: 1px solid #e8e4d9;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            color: #041741;
            letter-spacing: 0.5px;
        }

        /* ── Insurance Badge ── */
        .insurance-badge {
            margin-top: 12px;
            margin-bottom: 10px;
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 6px;
            padding: 8px 12px;
            page-break-inside: avoid;
        }

        /* ── Notes ── */
        .notes-section {
            margin-top: 18px;
            padding: 10px 14px;
            background: #fffcf2;
            border: 1px solid #f9eed3;
            border-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 3px solid #f2cb57;
            border-radius: 6px;
            font-size: 9px;
            color: #5c5541;
            line-height: 1.6;
            page-break-inside: avoid;
        }
        .notes-title {
            font-size: 9px;
            font-weight: 800;
            color: #041741;
            margin-bottom: 4px;
        }

        /* ── Bottom Gold Bar ── */
        .bottom-bar {
            height: 3px;
            background: #f2cb57;
            border-radius: 0 0 2px 2px;
        }

        /* Clearfix */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* ── Check-in/out Time ── */
        .checkin-time {
            font-size: 9px;
            font-weight: 700;
            margin-top: 3px;
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
            <table class="top-banner-inner" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="28%" valign="middle" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">
                        <div class="brand-name">{{ __('Fly Vio') }}</div>
                        <div class="brand-tag">{{ __('Premium Travel Services') }}</div>
                    </td>
                    <td width="44%" valign="middle" style="text-align: center;">
                        @php
                            $siteLogoPath = \App\Models\Setting::get('site_logo', 'images/logo-full.png');
                            if (filter_var($siteLogoPath, FILTER_VALIDATE_URL)) {
                                $logoImgSrc = $siteLogoPath;
                            } else {
                                $logoImgSrc = public_path($siteLogoPath);
                            }
                        @endphp
                        @if(filter_var($siteLogoPath, FILTER_VALIDATE_URL) || file_exists(public_path($siteLogoPath)))
                            <img src="{{ $logoImgSrc }}" alt="Logo" style="max-height: 48px; max-width: 130px;">
                        @endif
                    </td>
                    <td width="28%" valign="middle" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                        <div class="doc-type">{{ __('Hotel Voucher') }}</div>
                        <div class="doc-date">{{ __('Issued on') }}: {{ now()->translatedFormat('d M Y, H:i') }}</div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="gold-strip"></div>
    </htmlpageheader>

    <!-- ═══ CONTENT ═══ -->
    <div class="content">

        @php
            $passengers = $booking->passengers ?? collect();
            $paxDetails = is_string($booking->pax_details ?? null) ? json_decode($booking->pax_details, true) : ($booking->pax_details ?? []);
            
            $leadPax = $passengers->isNotEmpty() ? $passengers->first() : null;
            $leadName = null;
            if ($leadPax) {
                $leadName = trim(($leadPax->title ? $leadPax->title . ' ' : '') . ($leadPax->name ?: ($leadPax->first_name . ' ' . $leadPax->last_name)));
            }
            if (empty($leadName) && !empty($paxDetails) && is_array($paxDetails)) {
                if (isset($paxDetails[0]['pax'][0])) {
                    $p = $paxDetails[0]['pax'][0];
                    $leadName = trim(($p['Title'] ?? '') . ' ' . ($p['FirstName'] ?? '') . ' ' . ($p['LastName'] ?? ''));
                } elseif (isset($paxDetails[0]['FirstName'])) {
                    $leadName = trim(($paxDetails[0]['Title'] ?? '') . ' ' . ($paxDetails[0]['FirstName'] ?? '') . ' ' . ($paxDetails[0]['LastName'] ?? ''));
                } elseif (isset($paxDetails[0]['adult']['firstName'][0])) {
                    $t = $paxDetails[0]['adult']['title'][0] ?? 'Mr';
                    $fn = $paxDetails[0]['adult']['firstName'][0] ?? '';
                    $ln = $paxDetails[0]['adult']['lastName'][0] ?? '';
                    $leadName = trim("$t $fn $ln");
                }
            }
            if (empty($leadName)) {
                $leadName = $booking->user->full_name ?? ($booking->user->name ?? __('Primary Guest'));
            }

            $contactPhone = $booking->contact_phone ?: ($leadPax->phone ?? ($booking->user->phone ?? null));
            $contactEmail = $booking->contact_email ?: ($booking->user->email ?? null);
            $leadNationality = $leadPax->nationality ?? null;

            $qrData = "REF: " . ($booking->supplier_confirmation_num ?? $booking->reference_num) . "\nHOTEL: {$booking->hotel_name}\nLEAD: {$leadName}\nTEL: " . ($contactPhone ?: 'N/A') . "\nCHECK-IN: " . $booking->check_in->format('d M Y');
            $qrDataEncoded = urlencode($qrData);
            $insAmount = (float)($booking->insurance_amount ?? 0);
            $policy = $booking->insurancePolicy;
            $baseHotelPrice = max(0, (float)$booking->total_price - $insAmount);
        @endphp

        <!-- ─── HOTEL & REFERENCE BLOCK ─── -->
        <div class="hotel-card">
            <div class="hotel-card-top">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="48%" valign="middle">
                            <div class="hotel-name">
                                <svg width="18" height="18" viewBox="0 0 24 24" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 5px;">
                                    <path fill="#f2cb57" d="M19 2H5c-1.1 0-2 .9-2 2v18h18V4c0-1.1-.9-2-2-2zm-8 14H7v-2h4v2zm0-4H7v-2h4v2zm0-4H7V6h4v2zm6 8h-4v-2h4v2zm0-4h-4v-2h4v2zm0-4h-4V6h4v2z"/>
                                </svg>
                                {{ $booking->hotel_name }}
                                <span style="font-family: 'DejaVuSans', 'freesans', sans-serif; color: #f2cb57; font-size: 12px; letter-spacing: 2px; margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 6px;">
                                    &#9733;&#9733;&#9733;&#9733;&#9733;
                                </span>
                            </div>
                            <div class="hotel-location">
                                <svg width="12" height="12" viewBox="0 0 24 24" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 3px;">
                                    <path fill="#f2cb57" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                {{ $booking->city_name }}, {{ $booking->country_name }}
                            </div>
                        </td>
                        <td width="22%" align="center" valign="middle">
                            <div style="border: 1px solid #f2cb57; border-radius: 6px; padding: 4px; display: inline-block; background: rgba(242, 203, 87, 0.05); text-align: center;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $qrDataEncoded }}&color=041741" alt="QR" width="56" style="background: white; border-radius: 3px;">
                                <div style="font-size: 7px; color: #041741; font-weight: 700; margin-top: 3px; text-transform: uppercase; white-space: nowrap;">{{ __('Scan at Reception') }}</div>
                            </div>
                        </td>
                        <td width="30%" valign="middle">
                            <div class="ref-box">
                                <div class="ref-label">{{ __('Booking Confirmation No.') }}</div>
                                <div class="ref-value">{{ $booking->supplier_confirmation_num ?? ($booking->reference_num ?? __('Pending')) }}</div>
                                <div class="ref-status">{{ __('Confirmed') }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="hotel-card-bottom">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="33%" valign="top">
                            <span class="info-label">{{ __('Booking Reference') }}</span><br>
                            <span class="info-value">{{ $booking->reference_num }}</span>
                            @if(!empty($booking->supplier_confirmation_num))
                                <br><span style="font-size: 8px; color: #64748b;">{{ __('Supplier Ref:') }} {{ $booking->supplier_confirmation_num }}</span>
                            @endif
                        </td>
                        <td width="34%" align="center" valign="top">
                            <span class="info-label">{{ __('Room Type') }}</span><br>
                            <span class="info-value" style="color: #041741;">
                                <svg width="12" height="12" viewBox="0 0 24 24" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 3px;"><path fill="#041741" d="M19 7h-8v8H3V5H1v15h2v-3h18v3h2v-9a4 4 0 0 0-4-4zm-8 6h6a2 2 0 0 1 2 2v1H11v-3zM7 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                                {{ $booking->room_name }}
                            </span>
                        </td>
                        <td width="33%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};" valign="top">
                            <span class="info-label">{{ __('Board') }}</span><br>
                            <span class="info-value">
                                <svg width="12" height="12" viewBox="0 0 24 24" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 3px;"><path fill="#041741" d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
                                {{ $booking->board_type ?? __('Room Only') }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- ─── STAY DATES CARD ─── -->
        <table width="100%" cellpadding="0" cellspacing="0" class="stay-card" style="background-color: #041741; background: #041741; border-radius: 8px; margin-bottom: 20px; page-break-inside: avoid;">
            <tr>
                <td style="padding: 18px 16px 14px 16px; color: #ffffff; background-color: #041741; border-radius: 8px;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="32%" align="center" valign="middle">
                                <div class="date-big">{{ $booking->check_in->translatedFormat('d M') }}</div>
                                <div class="date-label">{{ __('Check-in') }}</div>
                                <div class="date-detail" dir="ltr">{{ $booking->check_in->translatedFormat('l, Y') }}</div>
                                <div class="checkin-time" style="color: #34d399;">{{ __('From 14:00') }}</div>
                            </td>
                            <td width="36%" align="center" valign="middle">
                                <div style="margin-bottom: 6px; white-space: nowrap;">
                                    <span style="display: inline-block; width: 5px; height: 5px; border-radius: 50%; background: #f2cb57; vertical-align: middle;"></span>
                                    <span style="display: inline-block; width: 25px; border-top: 1.5px dashed rgba(242, 203, 87, 0.5); vertical-align: middle; margin: 0 3px;"></span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" style="vertical-align: middle;"><path fill="#f2cb57" d="M12.3 2a10 10 0 0 0-.19 20 10 10 0 0 0 8.7-5.1 1 1 0 0 0-1-1.4 7.7 7.7 0 0 1-5.6-2.2 7.7 7.7 0 0 1-2.2-5.6 7.7 7.7 0 0 1 2.2-5.6 1 1 0 0 0-.4-1.6 10 10 0 0 0-1.51-.1z"/></svg>
                                    <span style="display: inline-block; width: 25px; border-top: 1.5px dashed rgba(242, 203, 87, 0.5); vertical-align: middle; margin: 0 3px;"></span>
                                    <span style="display: inline-block; width: 5px; height: 5px; border-radius: 50%; background: #f2cb57; vertical-align: middle;"></span>
                                </div>
                                <div class="nights-badge">{{ $booking->check_in->diffInDays($booking->check_out) }} {{ __('Nights') }}</div>
                            </td>
                            <td width="32%" align="center" valign="middle">
                                <div class="date-big">{{ $booking->check_out->translatedFormat('d M') }}</div>
                                <div class="date-label">{{ __('Check-out') }}</div>
                                <div class="date-detail" dir="ltr">{{ $booking->check_out->translatedFormat('l, Y') }}</div>
                                <div class="checkin-time" style="color: #f87171;">{{ __('Until 12:00') }}</div>
                            </td>
                        </tr>
                    </table>
                    <div class="stay-info-strip">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="50%" valign="top">
                                    <span class="stay-info-strip-label">{{ __('Payment Status') }}</span><br>
                                    <span style="color: #f2cb57; font-weight: 900; font-size: 11px;">{{ __('PREPAID - Fully Paid') }}</span>
                                </td>
                                <td width="50%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};" valign="top">
                                    <span class="stay-info-strip-label">{{ __('Total Amount') }}</span><br>
                                    <span style="color: #f2cb57; font-weight: 900; font-size: 16px;">{{ number_format($booking->total_price, 2) }} {{ $booking->currency }}</span>
                                    @if($insAmount > 0 || $policy)
                                        <div style="font-size: 7.5px; color: #a7f3d0; margin-top: 2px;">
                                            🛡️ {{ __('Includes Travel Insurance: :amt :cur', ['amt' => number_format($insAmount, 2), 'cur' => $booking->currency]) }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- ─── PASSENGER / TRAVELLER CONTACT INFORMATION ─── -->
        <div class="section-title">{{ __('Passenger / Traveller Contact Information') }}</div>
        <div style="background: #ffffff; border: 1.5px solid #041741; border-radius: 6px; margin-bottom: 18px; overflow: hidden; page-break-inside: avoid;">
            <div style="background: #f9f8f3; padding: 10px 14px; border-bottom: 1px dashed #d5d0c4;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="38%" valign="middle">
                            <span class="info-label">{{ __('Lead Passenger / Primary Guest') }}</span><br>
                            <span class="info-value" style="color: #041741; font-size: 11.5px; font-weight: 800;">
                                <svg width="11" height="11" viewBox="0 0 24 24" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px;"><path fill="#041741" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                {{ $leadName }}
                            </span>
                        </td>
                        <td width="31%" valign="middle">
                            <span class="info-label">{{ __('Contact Phone') }}</span><br>
                            <span class="info-value" dir="ltr" style="color: #041741; font-size: 11px; font-weight: 800; font-family: 'Courier New', monospace, 'tajawal';">
                                <svg width="11" height="11" viewBox="0 0 24 24" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px;"><path fill="#041741" d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                                {{ $contactPhone ?: '—' }}
                            </span>
                        </td>
                        <td width="31%" valign="middle">
                            <span class="info-label">{{ __('Contact Email') }}</span><br>
                            <span class="info-value" style="color: #041741; font-size: 11px; font-weight: 800;">
                                <svg width="11" height="11" viewBox="0 0 24 24" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 4px;"><path fill="#041741" d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                {{ $contactEmail ?: '—' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="background: #ffffff; padding: 7px 14px; font-size: 9.5px; color: #4b5563;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="55%" valign="middle">
                            <span style="color: #9ca3af; text-transform: uppercase; font-size: 8px; letter-spacing: 0.5px;">{{ __('Guests Summary') }}:</span>
                            <strong style="color: #041741;">{{ $booking->adults }} {{ __('Adult(s)') }}@if($booking->childs > 0), {{ $booking->childs }} {{ __('Child(ren)') }}@endif</strong>
                            <span style="color: #cbd5e1; margin: 0 6px;">|</span>
                            <span style="color: #9ca3af; text-transform: uppercase; font-size: 8px; letter-spacing: 0.5px;">{{ __('Rooms') }}:</span>
                            <strong style="color: #041741;">{{ $booking->rooms }}</strong>
                        </td>
                        <td width="45%" valign="middle" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                            @if(!empty($leadNationality))
                                <span style="color: #9ca3af; text-transform: uppercase; font-size: 8px; letter-spacing: 0.5px;">{{ __('Nationality') }}:</span>
                                <strong style="color: #041741;">{{ $leadNationality }}</strong>
                            @else
                                <span style="color: #16a34a; font-weight: 700;">✓ {{ __('Verified Reservation Details') }}</span>
                            @endif
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
                    <th width="8%" style="text-align: center;">#</th>
                    <th width="12%" style="text-align: center;">{{ __('Room') }}</th>
                    <th width="48%">{{ __('Guest Name') }}</th>
                    <th width="18%">{{ __('Pax Type') }}</th>
                    <th width="14%" style="text-align: center;">{{ __('Role') }}</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $guestIndex = 0; 
                @endphp
                @if($passengers->isNotEmpty())
                    @foreach($passengers as $pax)
                        @php 
                            $guestIndex++; 
                            $isChild = strtolower($pax->passenger_type ?? '') === 'child';
                            $displayName = trim($pax->name);
                            if ($isChild) {
                                if (preg_match('/^(mr|mr\.)\s+/i', $displayName)) {
                                    $displayName = preg_replace('/^(mr|mr\.)\s+/i', 'Master ', $displayName);
                                } elseif (preg_match('/^(ms|ms\.)\s+/i', $displayName)) {
                                    $displayName = preg_replace('/^(ms|ms\.)\s+/i', 'Miss ', $displayName);
                                }
                            }
                        @endphp
                        <tr>
                            <td style="text-align: center; color:#9ca3af; font-weight:700;">{{ str_pad($guestIndex, 2, '0', STR_PAD_LEFT) }}</td>
                            <td style="text-align: center; font-weight: 700; color: #041741;">1</td>
                            <td style="font-weight: 700; color: #1a1f36;">{{ $displayName }}</td>
                            <td><span class="pax-badge">{{ __(ucfirst(strtolower($pax->passenger_type))) }}</span></td>
                            <td style="text-align: center;">
                                @if($guestIndex === 1)
                                    <span style="background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: 800;">{{ __('Lead Guest') }}</span>
                                @else
                                    <span style="color: #94a3b8; font-size: 8px;">{{ __('Guest') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @elseif(!empty($paxDetails) && is_array($paxDetails))
                    @foreach($paxDetails as $room)
                        @if(isset($room['pax']) && is_array($room['pax']))
                            @foreach($room['pax'] as $pax)
                                @php 
                                    $guestIndex++; 
                                    $isChild = ($pax['type'] ?? 'AD') === 'CH';
                                    $title = $pax['Title'] ?? '';
                                    if ($isChild) {
                                        if (in_array(strtoupper($title), ['MR', 'MR.'])) {
                                            $title = 'Master';
                                        } elseif (in_array(strtoupper($title), ['MS', 'MS.'])) {
                                            $title = 'Miss';
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td style="text-align: center; color:#9ca3af; font-weight:700;">{{ str_pad($guestIndex, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td style="text-align: center; font-weight: 700; color: #041741;">{{ $room['room_no'] ?? $loop->parent->iteration }}</td>
                                    <td style="font-weight: 700; color: #1a1f36;">{{ $title ? $title . ' ' : '' }}{{ $pax['FirstName'] ?? '' }} {{ $pax['LastName'] ?? '' }}</td>
                                    <td><span class="pax-badge">{{ $isChild ? __('Child') : __('Adult') }}</span></td>
                                    <td style="text-align: center;">
                                        @if($guestIndex === 1)
                                            <span style="background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: 800;">{{ __('Lead Guest') }}</span>
                                        @else
                                            <span style="color: #94a3b8; font-size: 8px;">{{ __('Guest') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @elseif(isset($room['adult']) || isset($room['child']))
                            @if(isset($room['adult']))
                                @php
                                    $adultFirstNames = isset($room['adult']['firstName']) 
                                        ? (is_array($room['adult']['firstName']) ? $room['adult']['firstName'] : [$room['adult']['firstName']]) 
                                        : [];
                                    $adultLastNames = isset($room['adult']['lastName']) 
                                        ? (is_array($room['adult']['lastName']) ? $room['adult']['lastName'] : [$room['adult']['lastName']]) 
                                        : [];
                                    $adultTitles = isset($room['adult']['title']) 
                                        ? (is_array($room['adult']['title']) ? $room['adult']['title'] : [$room['adult']['title']]) 
                                        : [];
                                @endphp
                                @foreach($adultFirstNames as $ai => $afn)
                                    @php $guestIndex++; @endphp
                                    <tr>
                                        <td style="text-align: center; color:#9ca3af; font-weight:700;">{{ str_pad($guestIndex, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td style="text-align: center; font-weight: 700; color: #041741;">{{ $room['room_no'] ?? $loop->parent->iteration }}</td>
                                        <td style="font-weight: 700; color: #1a1f36;">{{ $adultTitles[$ai] ?? 'Mr' }} {{ $afn }} {{ $adultLastNames[$ai] ?? '' }}</td>
                                        <td><span class="pax-badge">{{ __('Adult') }}</span></td>
                                        <td style="text-align: center;">
                                            @if($guestIndex === 1)
                                                <span style="background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: 800;">{{ __('Lead Guest') }}</span>
                                            @else
                                                <span style="color: #94a3b8; font-size: 8px;">{{ __('Guest') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            @if(isset($room['child']))
                                @php
                                    $childFirstNames = isset($room['child']['firstName']) 
                                        ? (is_array($room['child']['firstName']) ? $room['child']['firstName'] : [$room['child']['firstName']]) 
                                        : [];
                                    $childLastNames = isset($room['child']['lastName']) 
                                        ? (is_array($room['child']['lastName']) ? $room['child']['lastName'] : [$room['child']['lastName']]) 
                                        : [];
                                    $childTitles = isset($room['child']['title']) 
                                        ? (is_array($room['child']['title']) ? $room['child']['title'] : [$room['child']['title']]) 
                                        : [];
                                @endphp
                                @foreach($childFirstNames as $ci => $cfn)
                                    @php 
                                        $guestIndex++; 
                                        $cTitle = $childTitles[$ci] ?? 'Master';
                                        if (in_array(strtoupper($cTitle), ['MR', 'MR.'])) {
                                            $cTitle = 'Master';
                                        } elseif (in_array(strtoupper($cTitle), ['MS', 'MS.'])) {
                                            $cTitle = 'Miss';
                                        }
                                    @endphp
                                    <tr>
                                        <td style="text-align: center; color:#9ca3af; font-weight:700;">{{ str_pad($guestIndex, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td style="text-align: center; font-weight: 700; color: #041741;">{{ $room['room_no'] ?? $loop->parent->iteration }}</td>
                                        <td style="font-weight: 700; color: #1a1f36;">{{ $cTitle }} {{ $cfn }} {{ $childLastNames[$ci] ?? '' }}</td>
                                        <td><span class="pax-badge">{{ __('Child') }}</span></td>
                                        <td style="text-align: center;">
                                            @if($guestIndex === 1)
                                                <span style="background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: 800;">{{ __('Lead Guest') }}</span>
                                            @else
                                                <span style="color: #94a3b8; font-size: 8px;">{{ __('Guest') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endif
                    @endforeach
                @else
                    <tr>
                        <td style="text-align: center; color:#9ca3af; font-weight:700;">01</td>
                        <td style="text-align: center; font-weight: 700; color: #041741;">1</td>
                        <td style="font-weight: 700; color: #1a1f36;">{{ $leadName }}</td>
                        <td><span class="pax-badge">{{ __('Adult') }}</span></td>
                        <td style="text-align: center;">
                            <span style="background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: 800;">{{ __('Lead Guest') }}</span>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- ─── CHARGES & PAYMENT BREAKDOWN ─── -->
        <div class="section-title">{{ __('Payment & Charges Breakdown') }}</div>
        <table class="p-table" style="margin-bottom: 12px;">
            <thead>
                <tr>
                    <th width="65%">{{ __('Description') }}</th>
                    <th width="35%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ __('Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div style="font-weight: 700; color: #1a1f36;">{{ __('Hotel Accommodation') }} ({{ $booking->check_in->diffInDays($booking->check_out) }} {{ __('Nights') }})</div>
                        <div style="font-size: 9px; color: #9ca3af; margin-top: 2px;">
                            {{ $booking->hotel_name }} · {{ $booking->room_name }} · {{ $booking->board_type ?? __('Room Only') }}
                        </div>
                    </td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; font-weight: 800; color: #1a1f36; font-size: 12px; vertical-align: middle;">
                        {{ number_format($baseHotelPrice, 2) }} {{ $booking->currency }}
                    </td>
                </tr>
                @if($insAmount > 0 || $policy)
                <tr>
                    <td>
                        <div style="font-weight: 700; color: #1a1f36;">🛡️ {{ __('Comprehensive Travel & Medical Insurance') }}</div>
                        <div style="font-size: 9px; color: #9ca3af; margin-top: 2px;">
                            {{ __('Coverage Limit:') }} $500,000 USD · {{ __('Emergency Medical, Trip Delay, Baggage Protection') }}
                        </div>
                    </td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; font-weight: 800; color: #1a1f36; font-size: 12px; vertical-align: middle;">
                        {{ number_format($insAmount, 2) }} {{ $booking->currency }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="background: #041741; color: #ffffff; font-size: 11px; font-weight: 700; padding: 10px 14px; text-transform: uppercase; letter-spacing: 1px;">
                        {{ __('Total Paid') }}
                    </td>
                    <td style="background: #041741; color: #f2cb57; font-size: 15px; font-weight: 900; padding: 10px 14px; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                        {{ number_format($booking->total_price, 2) }} {{ $booking->currency }}
                    </td>
                </tr>
            </tbody>
        </table>

        @if($insAmount > 0 || $policy)
        <!-- ─── TRAVEL INSURANCE BADGE ─── -->
        <div class="insurance-badge">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="70%" valign="middle">
                        <div style="font-weight: 800; color: #166534; font-size: 10px;">
                            🛡️ {{ __('Comprehensive Travel & Medical Insurance Included') }}
                        </div>
                        <div style="font-size: 8.5px; color: #15803d; margin-top: 2px; word-wrap: break-word;">
                            {{ __('Policy No:') }} <strong>{{ $policy->policy_number ?? ('POL-'.strtoupper(substr(md5($booking->id), 0, 8))) }}</strong> · 
                            {{ __('Coverage Limit:') }} <strong>$500,000 USD</strong> · 
                            {{ __('Status:') }} <strong style="color:#059669;">{{ __('Active / Insured') }}</strong>
                        </div>
                        <div style="font-size: 8px; color: #16a34a; margin-top: 1px;">
                            {{ __('Emergency Medical, Trip Delay, Baggage Protection & 24/7 Telehealth') }}
                        </div>
                    </td>
                    <td width="30%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; vertical-align: middle;">
                        <span style="background: #16a34a; color: #fff; font-size: 8.5px; font-weight: 800; padding: 3px 7px; border-radius: 10px; text-transform: uppercase;">
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
                <svg width="10" height="10" viewBox="0 0 24 24" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 3px;"><path fill="#f59e0b" d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
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
    <div style="height: 3px; background: #f2cb57; margin: 0 30px; border-radius: 3px;"></div>
    <div style="padding-top: 6px; font-size: 8px; color: #9ca3af; margin: 0 30px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="33%" style="color: #041741; font-weight: 700;">{{ __('Fly Vio') }} &copy; {{ date('Y') }}</td>
                <td width="34%" align="center">{{ __('System Generated Document') }}</td>
                <td width="33%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; direction: ltr;">
                    Page {PAGENO} of {nbpg}
                </td>
            </tr>
        </table>
    </div>
</htmlpagefooter>

</body>
</html>
