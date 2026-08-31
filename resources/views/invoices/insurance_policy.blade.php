<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Certificate of Travel Insurance') }} - {{ $policy->policy_number }}</title>
    <style>
        @page {
            margin-top: 15mm;
            margin-bottom: 15mm;
            margin-left: 12mm;
            margin-right: 12mm;
        }

        body {
            font-family: 'tajawal', sans-serif;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            color: #1e293b;
            line-height: 1.5;
            font-size: 11px;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* ── Header Banner ── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border-bottom: 3px solid #0f172a;
            padding-bottom: 12px;
        }
        .brand-title {
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .brand-subtitle {
            font-size: 10px;
            color: #0ea5e9;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .doc-badge {
            background-color: #0f172a;
            color: #ffffff;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 800;
            text-align: center;
            display: inline-block;
        }
        .schengen-badge {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            margin-top: 4px;
            text-align: center;
        }

        /* ── Policy Main Box ── */
        .policy-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 16px;
        }
        .policy-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .policy-grid td {
            padding: 5px 8px;
            vertical-align: top;
        }
        .label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            display: block;
            margin-bottom: 2px;
        }
        .value {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
        }
        .value-highlight {
            color: #0284c7;
        }

        /* ── Tables ── */
        .section-title {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            border-left: 4px solid #0284c7;
            padding-left: 8px;
            margin: 14px 0 8px 0;
        }
        [dir="rtl"] .section-title {
            border-left: none;
            border-right: 4px solid #0284c7;
            padding-left: 0;
            padding-right: 8px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
            padding: 7px 10px;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            border: 1px solid #0f172a;
        }
        .data-table td {
            padding: 7px 10px;
            font-size: 10px;
            border: 1px solid #e2e8f0;
            color: #334155;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* ── Emergency Box ── */
        .emergency-box {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fca5a5;
            border-radius: 6px;
            padding: 10px 14px;
            margin-top: 14px;
        }
        .emergency-title {
            color: #991b1b;
            font-size: 11px;
            font-weight: 800;
            margin-bottom: 4px;
        }
        .emergency-phone {
            font-size: 14px;
            font-weight: 900;
            color: #b91c1c;
            direction: ltr;
            display: inline-block;
        }

        /* ── Compliance Note ── */
        .compliance-box {
            font-size: 8.5px;
            color: #64748b;
            line-height: 1.4;
            margin-top: 12px;
            padding: 8px;
            background-color: #f1f5f9;
            border-radius: 4px;
            border-left: 3px solid #0ea5e9;
        }
        [dir="rtl"] .compliance-box {
            border-left: none;
            border-right: 3px solid #0ea5e9;
        }

        .footer-note {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="brand-title">{{ config('app.name', 'FLY VIO') }}</div>
                <div class="brand-subtitle">GLOBAL TRAVEL SAFE & ASSISTANCE</div>
            </td>
            <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; vertical-align: middle;">
                <div class="doc-badge">{{ __('TRAVEL INSURANCE CERTIFICATE') }}</div>
                <div class="schengen-badge">✓ SCHENGEN VISA COMPLIANT (REG. EC 810/2009)</div>
            </td>
        </tr>
    </table>

    <!-- Main Policy Overview -->
    <div class="policy-box">
        <table class="policy-grid">
            <tr>
                <td style="width: 25%;">
                    <span class="label">{{ __('Policy Number') }}</span>
                    <span class="value value-highlight">{{ $policy->policy_number }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="label">{{ __('Certificate No') }}</span>
                    <span class="value">{{ $policy->certificate_number ?: 'CERT-' . substr(md5($policy->id), 0, 8) }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="label">{{ __('Coverage Status') }}</span>
                    <span class="value" style="color: #10b981;">{{ strtoupper($policy->status) }} ✓</span>
                </td>
                <td style="width: 25%;">
                    <span class="label">{{ __('Issue Date') }}</span>
                    <span class="value">{{ $policy->issued_at ? $policy->issued_at->format('Y-m-d') : date('Y-m-d') }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">{{ __('Coverage Type') }}</span>
                    <span class="value">{{ ucfirst($policy->coverage_type) }} Safe</span>
                </td>
                <td>
                    <span class="label">{{ __('Destination') }}</span>
                    <span class="value">{{ $policy->destination_country_name }} ({{ strtoupper($policy->destination_country ?: 'WORLDWIDE') }})</span>
                </td>
                <td>
                    <span class="label">{{ __('Period of Cover') }}</span>
                    <span class="value">{{ $policy->departure_date ? $policy->departure_date->format('d M Y') : '-' }} → {{ $policy->return_date ? $policy->return_date->format('d M Y') : '-' }}</span>
                </td>
                <td>
                    <span class="label">{{ __('Duration') }}</span>
                    <span class="value">{{ $policy->duration_days }} {{ __('Days') }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Insured Travelers Section -->
    <div class="section-title">{{ __('Insured Persons / Travelers') }}</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 35%;">{{ __('Full Name (As in Passport)') }}</th>
                <th style="width: 20%;">{{ __('Passport Number') }}</th>
                <th style="width: 15%;">{{ __('Nationality') }}</th>
                <th style="width: 15%;">{{ __('Date of Birth') }}</th>
                <th style="width: 10%;">{{ __('Type') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $travelers = $policy->insured_passengers ?? [];
            @endphp
            @if(count($travelers) > 0)
                @foreach($travelers as $idx => $t)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td><strong>{{ strtoupper($t['first_name'] ?? ($t['name'] ?? 'TRAVELER')) }} {{ strtoupper($t['last_name'] ?? '') }}</strong></td>
                        <td><span style="font-family: monospace; font-weight: bold;">{{ strtoupper($t['passport_no'] ?? ($t['passport'] ?? ($t['passport_number'] ?? '-'))) }}</span></td>
                        <td>{{ strtoupper($t['nationality'] ?? 'SA') }}</td>
                        <td>{{ $t['dob'] ?? ($t['birth_date'] ?? '-') }}</td>
                        <td>{{ ucfirst($t['type'] ?? 'Adult') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>1</td>
                    <td><strong>{{ strtoupper($policy->user->name ?? 'PRIMARY TRAVELER') }}</strong></td>
                    <td><span style="font-family: monospace; font-weight: bold;">{{ $policy->user->passport_number ?? 'REGISTERED' }}</span></td>
                    <td>{{ strtoupper($policy->user->nationality ?? 'SA') }}</td>
                    <td>-</td>
                    <td>Primary</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Schedule of Benefits Table -->
    <div class="section-title">{{ __('Schedule of Benefits & Coverage Limits') }}</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50%;">{{ __('Benefit Description') }}</th>
                <th style="width: 30%;">{{ __('Sum Insured / Maximum Limit') }}</th>
                <th style="width: 20%;">{{ __('Deductible / Excess') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Emergency Medical Treatment & Hospitalization</strong><br><small style="color:#64748b;">Inpatient & Outpatient care, surgery, prescribed drugs</small></td>
                <td><strong>USD $100,000 / EUR €90,000</strong></td>
                <td>USD $0 (Nil)</td>
            </tr>
            <tr>
                <td><strong>Emergency Medical Evacuation & Repatriation</strong><br><small style="color:#64748b;">Medical transportation back to home country</small></td>
                <td><strong>USD $50,000</strong></td>
                <td>USD $0 (Nil)</td>
            </tr>
            <tr>
                <td><strong>Trip Cancellation & Curtailment</strong><br><small style="color:#64748b;">Reimbursement of non-refundable prepaid costs</small></td>
                <td><strong>USD $5,000</strong></td>
                <td>USD $50</td>
            </tr>
            <tr>
                <td><strong>Baggage Loss & Stolen Personal Effects</strong><br><small style="color:#64748b;">Checked luggage loss by airline/carrier</small></td>
                <td><strong>USD $1,500</strong></td>
                <td>USD $25</td>
            </tr>
            <tr>
                <td><strong>Baggage Delay (Over 6 hours)</strong><br><small style="color:#64748b;">Emergency purchases of essential toiletries & clothing</small></td>
                <td><strong>USD $300</strong></td>
                <td>USD $0 (Nil)</td>
            </tr>
            <tr>
                <td><strong>24/7 Global Telemedicine & Doctor Access</strong><br><small style="color:#64748b;">Instant consultation in multiple languages</small></td>
                <td><strong>UNLIMITED ACCESS</strong></td>
                <td>USD $0 (Nil)</td>
            </tr>
        </tbody>
    </table>

    <!-- 24/7 Emergency Assistance Details -->
    <div class="emergency-box">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: middle; width: 65%;">
                    <div class="emergency-title">🚨 24/7 WORLDWIDE EMERGENCY MEDICAL & CLAIMS ASSISTANCE</div>
                    <div style="font-size: 9.5px; color: #7f1d1d;">
                        In case of hospitalization, accident, or immediate assistance, contact our emergency operations center prior to inpatient admission.
                    </div>
                </td>
                <td style="vertical-align: middle; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; width: 35%;">
                    <div class="emergency-phone">{{ $policy->emergency_phone ?: '+1-800-456-7890' }}</div>
                    <div style="font-size: 9px; color: #991b1b;">Email: assistance@sitata.com</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Official Compliance Statement -->
    <div class="compliance-box">
        <strong>OFFICIAL VISA COMPLIANCE DECLARATION:</strong><br>
        This insurance certificate confirms that the named insured person(s) are covered by a policy meeting all required standards of the European Union (Regulation EC No 810/2009 establishing a Community Code on Visas). The policy covers emergency medical expenses and medical repatriation with a minimum coverage limit exceeding €30,000 Euros across all Schengen Territory Member States and worldwide destinations throughout the specified coverage period.
    </div>

    <!-- Footer -->
    <div class="footer-note">
        This document is an electronic certificate issued automatically by {{ config('app.name', 'FLY VIO') }} in partnership with Sitata Global Travel Safe. To verify authenticity, visit https://flyvio.net or scan the digital certificate code.
    </div>

</body>
</html>
