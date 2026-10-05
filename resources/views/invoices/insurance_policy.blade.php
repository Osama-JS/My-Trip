<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Certificate of Travel Insurance') }} - {{ $policy->policy_number }}</title>
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
            font-size: 14px;
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

        /* ── Policy Block (PNR-style) ── */
        .policy-block {
            border: 1.5px solid #041741;
            border-radius: 8px;
            padding: 0;
            margin-bottom: 20px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .policy-block-top {
            background: #f9f8f3;
            padding: 10px 16px;
            border-bottom: 1px dashed #d5d0c4;
        }
        .policy-label {
            font-size: 8px;
            color: #8a8575;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 2px;
        }
        .policy-value {
            font-size: 20px;
            font-weight: 900;
            color: #041741;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace, 'Cairo';
        }
        .policy-status {
            display: inline-block;
            color: #16a34a;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 6px;
        }
        .policy-block-bottom {
            padding: 8px 16px;
            background: #ffffff;
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

        /* ── Info Labels/Values ── */
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

        /* ── Coverage Shield Card ── */
        .coverage-card {
            background: #041741;
            border-radius: 8px;
            padding: 14px 16px 10px 16px;
            margin-bottom: 18px;
            color: #ffffff;
            page-break-inside: avoid;
            width: 100%;
            box-sizing: border-box;
        }
        .coverage-destination {
            font-size: 22px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 1px;
        }
        .coverage-sub {
            font-size: 8px;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .coverage-plan-badge {
            display: inline-block;
            color: #f2cb57;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 1px;
            margin-top: 3px;
        }
        .coverage-date {
            font-size: 10px;
            color: #f2cb57;
            font-weight: 700;
            margin-top: 3px;
        }
        .coverage-divider {
            border-top: 1px dashed rgba(255,255,255,0.15);
            margin-top: 10px;
            padding-top: 6px;
            text-align: center;
            font-size: 10px;
            color: rgba(255,255,255,0.6);
        }
        .coverage-divider strong {
            color: #f2cb57;
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

        /* ── Passport Badge ── */
        .passport-badge {
            font-family: 'Courier New', monospace, 'Cairo';
            background: #f9f8f3;
            border: 1px solid #e8e4d9;
            padding: 2px 8px;
            border-radius: 4px;
            color: #041741;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* ── Emergency Box ── */
        .emergency-box {
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 18px;
            page-break-inside: avoid;
        }
        .emergency-title {
            color: #991b1b;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .emergency-phone {
            font-size: 16px;
            font-weight: 900;
            color: #b91c1c;
            direction: ltr;
            display: inline-block;
            letter-spacing: 1px;
        }

        /* ── Schengen Compliance Badge ── */
        .compliance-badge {
            margin-bottom: 18px;
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 6px;
            padding: 8px 12px;
            page-break-inside: avoid;
        }

        /* ── Details Grid ── */
        .details-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            page-break-inside: avoid;
        }
        .details-grid td {
            vertical-align: top;
        }
        .details-cell {
            padding: 14px;
        }
        .details-section-title {
            font-size: 11px;
            font-weight: 800;
            color: #041741;
            margin-bottom: 10px;
            border: none;
        }

        /* ── Terms Section ── */
        .terms-section {
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
        .terms-title {
            font-size: 9px;
            font-weight: 800;
            color: #041741;
            margin-bottom: 4px;
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

    <!-- ═══ PAGE HEADER ═══ -->
    <htmlpageheader name="page-header">
        <div class="top-banner">
            <table class="top-banner-inner">
                <tr>
                    <td width="28%" valign="middle" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">
                        <div class="brand-name">{{ config('app.name', __('Fly Vio')) }}</div>
                        <div class="brand-tag">{{ __('Global Travel Safe & Assistance') }}</div>
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
                        <div class="doc-type">{{ __('Insurance Certificate') }}</div>
                        <div class="doc-date">{{ __('Issued on') }}: {{ $policy->issued_at ? $policy->issued_at->translatedFormat('d M Y, H:i') : $policy->created_at->translatedFormat('d M Y, H:i') }}</div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="gold-strip"></div>
    </htmlpageheader>

    <!-- ═══ CONTENT ═══ -->
    <div class="content">

        @php
            // Prepare QR code data
            $qrData = "POLICY: {$policy->policy_number}\nCERT: " . ($policy->certificate_number ?: 'CERT-' . substr(md5($policy->id), 0, 8)) . "\nCOVER: " . ucfirst($policy->coverage_type) . "\nDEST: " . ($policy->destination_country_name ?? 'GLOBAL');
            $qrDataEncoded = urlencode($qrData);

            // Determine linked booking info
            $linkedBookingRef = '-';
            $linkedBookingType = '-';
            if ($policy->flightBooking) {
                $linkedBookingRef = $policy->flightBooking->booking_reference ?? ('BK-' . $policy->booking_id);
                $linkedBookingType = __('Flight Booking');
            } elseif ($policy->tripBooking) {
                $linkedBookingRef = $policy->tripBooking->booking_number ?? ('TB-' . $policy->trip_booking_id);
                $linkedBookingType = __('Trip Booking');
            } elseif ($policy->hotelBooking) {
                $linkedBookingRef = $policy->hotelBooking->booking_reference ?? ('HB-' . $policy->hotel_booking_id);
                $linkedBookingType = __('Hotel Booking');
            }
        @endphp

        <!-- ─── POLICY BLOCK (PNR-Style) ─── -->
        <div class="policy-block">
            <div class="policy-block-top">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="50%" valign="middle">
                            <div class="policy-label">{{ __('Policy Number') }}</div>
                            <div class="policy-value">{{ $policy->policy_number }}</div>
                            <div class="policy-status">✓ {{ strtoupper($policy->status) }} · {{ __('Verified') }}</div>
                        </td>
                        <td width="22%" align="center" valign="middle">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #041741 0%, #0369a1 100%); border-radius: 10px; display: inline-block; text-align: center; line-height: 48px;">
                                <span style="font-size: 22px; color: #f2cb57;">🛡️</span>
                            </div>
                        </td>
                        <td width="28%" align="{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}" valign="middle">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $qrDataEncoded }}&color=041741" alt="QR" width="52" style="border: 1.5px solid #041741; padding: 2px; background: white; border-radius: 5px;">
                        </td>
                    </tr>
                </table>
            </div>
            <div class="policy-block-bottom">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="33%" valign="top">
                            <span class="info-label">{{ __('Certificate No') }}</span><br>
                            <span class="info-value">{{ $policy->certificate_number ?: 'CERT-' . strtoupper(substr(md5($policy->id), 0, 8)) }}</span>
                        </td>
                        <td width="34%" align="center" valign="top">
                            <span class="info-label">{{ __('Coverage Type') }}</span><br>
                            <span class="info-value" style="color: #d9a01c;">{{ ucfirst($policy->coverage_type) }} Travel Safe</span>
                        </td>
                        <td width="33%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};" valign="top">
                            <span class="info-label">{{ __('Currency') }}</span><br>
                            <span class="info-value">{{ strtoupper($policy->currency ?? 'USD') }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- ─── COVERAGE SHIELD CARD ─── -->
        <div class="coverage-card">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="32%" align="center" valign="middle">
                        <div class="coverage-destination">{{ strtoupper($policy->destination_country ?: 'WW') }}</div>
                        <div style="font-size: 11px; font-weight: 700; color: #ffffff; margin-bottom: 3px;">{{ $policy->destination_country_name }}</div>
                        <div class="coverage-sub">{{ __('Destination') }}</div>
                        <div class="coverage-date" dir="ltr">{{ $policy->departure_date ? $policy->departure_date->translatedFormat('d M Y') : '-' }}</div>
                    </td>
                    <td width="36%" align="center" valign="middle">
                        <div style="font-size: 10px; color: rgba(255,255,255,0.35); margin-bottom: 3px;">
                            ─────
                            <svg width="12" height="12" viewBox="0 0 24 24" style="vertical-align: middle; margin: 0 3px;">
                                <path fill="rgba(255,255,255,0.35)" d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                            </svg>
                            ─────
                        </div>
                        <div class="coverage-plan-badge">{{ strtoupper(ucfirst($policy->coverage_type)) }} SAFE</div>
                        <div style="font-size: 9px; font-weight: 700; color: #34d399; margin-top: 4px;">
                            {{ $policy->duration_days }} {{ __('Days Coverage') }}
                        </div>
                    </td>
                    <td width="32%" align="center" valign="middle">
                        <div class="coverage-destination" style="font-size: 16px;">{{ __('GLOBAL') }}</div>
                        <div style="font-size: 11px; font-weight: 700; color: #ffffff; margin-bottom: 3px;">{{ __('Worldwide Coverage') }}</div>
                        <div class="coverage-sub">{{ __('Valid Through') }}</div>
                        <div class="coverage-date" dir="ltr">{{ $policy->return_date ? $policy->return_date->translatedFormat('d M Y') : '-' }}</div>
                    </td>
                </tr>
            </table>
            <div class="coverage-divider">
                <svg width="10" height="10" viewBox="0 0 24 24" style="vertical-align: text-bottom; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 3px;">
                    <path fill="rgba(255,255,255,0.6)" d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                </svg>
                {{ __('Maximum Medical Coverage') }}: <strong>USD $100,000 / EUR €90,000</strong>
            </div>
        </div>

        <!-- ─── SCHENGEN COMPLIANCE BADGE ─── -->
        <div class="compliance-badge">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="70%" valign="middle">
                        <div style="font-weight: 800; color: #166534; font-size: 10px;">
                            ✓ {{ __('Schengen Visa Compliant Certificate') }} (REG. EC 810/2009)
                        </div>
                        <div style="font-size: 8.5px; color: #15803d; margin-top: 2px;">
                            {{ __('Minimum coverage exceeds €30,000 EUR across all Schengen Territory Member States and worldwide destinations.') }}
                        </div>
                    </td>
                    <td width="30%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; vertical-align: middle;">
                        <span style="background: #16a34a; color: #fff; font-size: 8.5px; font-weight: 800; padding: 3px 7px; border-radius: 10px; text-transform: uppercase;">
                            ✓ {{ __('COMPLIANT') }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- ─── DETAILS GRID: Policyholder & Linked Booking ─── -->
        <table class="details-grid" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="48%" valign="top">
                    <div class="details-cell">
                        <div class="details-section-title">
                            👤 {{ __('Policyholder Details') }}
                        </div>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="50%" valign="top" style="padding-bottom: 8px;">
                                    <span class="info-label">{{ __('Full Name') }}</span><br>
                                    <span class="info-value">{{ strtoupper($policy->user->name ?? __('PRIMARY TRAVELER')) }}</span>
                                </td>
                                <td width="50%" valign="top" style="padding-bottom: 8px;">
                                    <span class="info-label">{{ __('Phone') }}</span><br>
                                    <span class="info-value" dir="ltr">{{ $policy->user->phone ?? '—' }}</span>
                                </td>
                            </tr>
                            @if(isset($policy->user) && $policy->user->email)
                            <tr>
                                <td colspan="2" valign="top">
                                    <span class="info-label">{{ __('Email') }}</span><br>
                                    <span class="info-value">{{ $policy->user->email }}</span>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </td>
                <td width="4%"></td>
                <td width="48%" valign="top">
                    <div class="details-cell">
                        <div class="details-section-title">
                            📋 {{ __('Policy & Booking Info') }}
                        </div>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="50%" valign="top" style="padding-bottom: 8px;">
                                    <span class="info-label">{{ __('Linked Booking') }}</span><br>
                                    <span class="info-value">{{ $linkedBookingRef }}</span>
                                </td>
                                <td width="50%" valign="top" style="padding-bottom: 8px;">
                                    <span class="info-label">{{ __('Booking Type') }}</span><br>
                                    <span class="info-value">{{ $linkedBookingType }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%" valign="top">
                                    <span class="info-label">{{ __('Issue Date') }}</span><br>
                                    <span class="info-value">{{ $policy->issued_at ? $policy->issued_at->translatedFormat('d M Y') : $policy->created_at->translatedFormat('d M Y') }}</span>
                                </td>
                                <td width="50%" valign="top">
                                    <span class="info-label">{{ __('Premium Paid') }}</span><br>
                                    <span class="info-value" style="color: #d9a01c; font-size: 14px; font-weight: 900;">{{ number_format($policy->selling_price, 2) }} {{ strtoupper($policy->currency ?? 'USD') }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- ─── INSURED TRAVELERS TABLE ─── -->
        <div class="section-title" style="margin-top: 4px;">
            🧳 {{ __('Insured Persons / Travelers') }}
        </div>
        <table class="p-table">
            <thead>
                <tr>
                    <th width="6%">#</th>
                    <th width="32%">
                        👤 {{ __('Full Name (As in Passport)') }}
                    </th>
                    <th width="18%">
                        🎫 {{ __('Passport Number') }}
                    </th>
                    <th width="14%">
                        🌍 {{ __('Nationality') }}
                    </th>
                    <th width="16%">
                        📅 {{ __('Date of Birth') }}
                    </th>
                    <th width="14%">
                        {{ __('Type') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @php
                    $travelers = $policy->insured_passengers ?? [];
                @endphp
                @if(count($travelers) > 0)
                    @foreach($travelers as $idx => $t)
                        <tr>
                            <td style="color:#9ca3af; font-weight:700;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td style="font-weight: 700; color: #1a1f36;">{{ strtoupper($t['first_name'] ?? ($t['name'] ?? 'TRAVELER')) }} {{ strtoupper($t['last_name'] ?? '') }}</td>
                            <td>
                                <span class="passport-badge">{{ strtoupper($t['passport_no'] ?? ($t['passport'] ?? ($t['passport_number'] ?? '-'))) }}</span>
                            </td>
                            <td>{{ strtoupper($t['nationality'] ?? 'SA') }}</td>
                            <td dir="ltr">{{ $t['dob'] ?? ($t['birth_date'] ?? '-') }}</td>
                            <td>{{ ucfirst($t['type'] ?? 'Adult') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="color:#9ca3af; font-weight:700;">01</td>
                        <td style="font-weight: 700; color: #1a1f36;">{{ strtoupper($policy->user->name ?? 'PRIMARY TRAVELER') }}</td>
                        <td>
                            <span class="passport-badge">{{ $policy->user->passport_number ?? 'REGISTERED' }}</span>
                        </td>
                        <td>{{ strtoupper($policy->user->nationality ?? 'SA') }}</td>
                        <td dir="ltr">-</td>
                        <td>{{ __('Primary') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- ─── 24/7 EMERGENCY ASSISTANCE ─── -->
        <div class="emergency-box">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="65%" valign="middle">
                        <div class="emergency-title">
                            🚨 {{ __('24/7 Worldwide Emergency Medical & Claims Assistance') }}
                        </div>
                        <div style="font-size: 9.5px; color: #7f1d1d; line-height: 1.5;">
                            {{ __('In case of hospitalization, accident, or immediate medical assistance, contact our emergency operations center prior to inpatient admission.') }}
                        </div>
                    </td>
                    <td width="35%" valign="middle" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                        <div class="emergency-phone">{{ $policy->emergency_phone ?: '+1-800-456-7890' }}</div>
                        <div style="font-size: 9px; color: #991b1b; margin-top: 3px;">{{ __('Email') }}: assistance@sitata.com</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- ─── SCHEDULE OF BENEFITS TABLE ─── -->
        <div class="section-title">
            💎 {{ __('Schedule of Benefits & Coverage Limits') }}
        </div>
        <table class="p-table">
            <thead>
                <tr>
                    <th width="50%">{{ __('Benefit Description') }}</th>
                    <th width="30%">{{ __('Sum Insured / Maximum Limit') }}</th>
                    <th width="20%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ __('Deductible / Excess') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div style="font-weight: 700; color: #1a1f36;">{{ __('Emergency Medical Treatment & Hospitalization') }}</div>
                        <div style="font-size: 9px; color: #9ca3af; margin-top: 2px;">{{ __('Inpatient & Outpatient care, surgery, prescribed drugs') }}</div>
                    </td>
                    <td style="font-weight: 800; color: #041741;">USD $100,000 / EUR €90,000</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">USD $0 (Nil)</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight: 700; color: #1a1f36;">{{ __('Emergency Medical Evacuation & Repatriation') }}</div>
                        <div style="font-size: 9px; color: #9ca3af; margin-top: 2px;">{{ __('Medical transportation back to home country') }}</div>
                    </td>
                    <td style="font-weight: 800; color: #041741;">USD $50,000</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">USD $0 (Nil)</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight: 700; color: #1a1f36;">{{ __('Trip Cancellation & Curtailment') }}</div>
                        <div style="font-size: 9px; color: #9ca3af; margin-top: 2px;">{{ __('Reimbursement of non-refundable prepaid costs') }}</div>
                    </td>
                    <td style="font-weight: 800; color: #041741;">USD $5,000</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">USD $50</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight: 700; color: #1a1f36;">{{ __('Baggage Loss & Stolen Personal Effects') }}</div>
                        <div style="font-size: 9px; color: #9ca3af; margin-top: 2px;">{{ __('Checked luggage loss by airline/carrier') }}</div>
                    </td>
                    <td style="font-weight: 800; color: #041741;">USD $1,500</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">USD $25</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight: 700; color: #1a1f36;">{{ __('Baggage Delay (Over 6 hours)') }}</div>
                        <div style="font-size: 9px; color: #9ca3af; margin-top: 2px;">{{ __('Emergency purchases of essential toiletries & clothing') }}</div>
                    </td>
                    <td style="font-weight: 800; color: #041741;">USD $300</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">USD $0 (Nil)</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight: 700; color: #1a1f36;">{{ __('24/7 Global Telemedicine & Doctor Access') }}</div>
                        <div style="font-size: 9px; color: #9ca3af; margin-top: 2px;">{{ __('Instant consultation in multiple languages') }}</div>
                    </td>
                    <td style="font-weight: 800; color: #041741;">{{ __('UNLIMITED ACCESS') }}</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">USD $0 (Nil)</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight: 700; color: #1a1f36;">{{ __('Accidental Death & Permanent Disability') }}</div>
                        <div style="font-size: 9px; color: #9ca3af; margin-top: 2px;">{{ __('Coverage during the insured travel period') }}</div>
                    </td>
                    <td style="font-weight: 800; color: #041741;">USD $25,000</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">USD $0 (Nil)</td>
                </tr>
                <!-- Total Row -->
                <tr>
                    <td style="background: #041741; color: #ffffff; font-size: 11px; font-weight: 700; padding: 12px 14px; text-transform: uppercase; letter-spacing: 1px;">
                        {{ __('Insurance Premium Paid') }}
                    </td>
                    <td colspan="2" style="background: #041741; color: #f2cb57; font-size: 16px; font-weight: 900; padding: 12px 14px; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                        {{ number_format($policy->selling_price, 2) }} {{ strtoupper($policy->currency ?? 'USD') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- ─── OFFICIAL COMPLIANCE DECLARATION ─── -->
        <div class="terms-section">
            <div class="terms-title">
                ⚠️ {{ __('Official Visa Compliance Declaration:') }}
            </div>
            {{ __('This insurance certificate confirms that the named insured person(s) are covered by a policy meeting all required standards of the European Union (Regulation EC No 810/2009 establishing a Community Code on Visas). The policy covers emergency medical expenses and medical repatriation with a minimum coverage limit exceeding €30,000 Euros across all Schengen Territory Member States and worldwide destinations throughout the specified coverage period.') }}
        </div>

        <!-- ─── IMPORTANT NOTES ─── -->
        <div class="terms-section" style="margin-top: 10px;">
            <div class="terms-title">
                📌 {{ __('Important Notes:') }}
            </div>
            1. {{ __('This certificate is an official electronic document. No physical signature or stamp is required for validity.') }}<br>
            2. {{ __('In case of emergency, always contact the 24/7 assistance hotline before seeking treatment when possible.') }}<br>
            3. {{ __('Keep this certificate accessible during your entire trip. Present it at hospitals, embassies, or border control upon request.') }}<br>
            4. {{ __('Pre-existing medical conditions are excluded unless specifically endorsed on this policy.') }}
        </div>

    </div><!-- /content -->

</div><!-- /paper -->

<!-- ═══ PAGE FOOTER ═══ -->
<htmlpagefooter name="page-footer">
    <div style="height: 3px; background: #f2cb57; margin: 0 30px; border-radius: 3px;"></div>
    <div style="padding-top: 6px; font-size: 8px; color: #9ca3af; margin: 0 30px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="33%" style="color: #041741; font-weight: 700;">{{ config('app.name', __('Fly Vio')) }} &copy; {{ date('Y') }}</td>
                <td width="34%" align="center">{{ __('System Generated Insurance Certificate') }}</td>
                <td width="33%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; direction: ltr;">
                    Page {PAGENO} of {nbpg}
                </td>
            </tr>
        </table>
    </div>
</htmlpagefooter>

</body>
</html>
