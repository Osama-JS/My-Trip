<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Hotel Voucher') }} - {{ $booking->supplier_confirmation_num ?? $booking->reference_num }}</title>
    <style>
        @page {
            margin: 0px;
            header: page-header;
            footer: page-footer;
        }
        body { 
            font-family: 'Cairo', sans-serif; 
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}; 
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; 
            color: #334155; 
            line-height: 1.6; 
            font-size: 13px;
            background: #ffffff; 
            margin: 0;
            padding: 0;
        }
        
        /* Typography */
        .text-primary { color: #1e3a8a; } /* Dark premium blue */
        .text-accent { color: #3b82f6; }
        .text-muted { color: #64748b; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-lg { font-size: 18px; }
        .text-xl { font-size: 22px; }
        .text-2xl { font-size: 26px; }
        .uppercase { text-transform: uppercase; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 8px 0; vertical-align: top; }
        
        /* Header Section */
        .header-bg {
            background-color: #1e3a8a;
            color: #ffffff;
            padding: 30px 40px;
        }
        .header-table td {
            color: #ffffff;
        }
        .app-name { 
            font-size: 32px; 
            font-weight: 900; 
            color: #ffffff; 
            margin-bottom: 5px;
        }
        .voucher-title { 
            font-size: 16px; 
            color: #93c5fd; 
            letter-spacing: 2px; 
            text-transform: uppercase; 
            font-weight: bold; 
        }

        /* Content Wrapper */
        .content-wrapper {
            padding: 40px;
        }

        /* Info Boxes */
        .info-box { 
            background: #ffffff; 
            border: 1px solid #e2e8f0; 
            border-radius: 8px; 
            margin-bottom: 25px; 
            overflow: hidden;
        }
        .info-box-title { 
            background: #f8fafc; 
            color: #1e3a8a; 
            padding: 12px 20px; 
            font-size: 15px; 
            font-weight: bold; 
            border-bottom: 1px solid #e2e8f0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-box-content {
            padding: 20px;
        }
        
        /* Reference & Hotel Banner */
        .hotel-banner {
            background-color: #f8fafc;
            border-left: 5px solid #3b82f6;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 0 8px 8px 0;
        }
        html[dir="rtl"] .hotel-banner {
            border-left: none;
            border-right: 5px solid #3b82f6;
            border-radius: 8px 0 0 8px;
        }
        
        .ref-box { 
            background: #1e3a8a; 
            color: #ffffff; 
            text-align: center; 
            padding: 20px 15px; 
            border-radius: 8px; 
            height: auto; 
        }
        .ref-label { 
            font-size: 12px; 
            color: #93c5fd; 
            margin-bottom: 8px; 
            text-transform: uppercase;
            letter-spacing: 1px; 
        }
        .ref-number { 
            font-size: 24px; 
            font-weight: bold; 
            letter-spacing: 1px; 
            color: #ffffff;
        }

        /* Data Grids */
        .data-label { 
            color: #64748b; 
            font-size: 11px; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .data-value { 
            font-size: 15px; 
            font-weight: bold; 
            color: #0f172a; 
        }
        
        .border-bottom { border-bottom: 1px dashed #cbd5e1; }
        
        /* Tables for Guests */
        .pax-table {
            width: 100%;
        }
        .pax-table th { 
            background: #f8fafc; 
            color: #475569; 
            font-size: 12px; 
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; 
            padding: 12px 20px; 
            border-bottom: 2px solid #e2e8f0;
            text-transform: uppercase;
            font-weight: bold;
        }
        .pax-table td { 
            padding: 12px 20px; 
            border-bottom: 1px solid #e2e8f0; 
            font-size: 14px; 
        }
        .pax-table tr:last-child td {
            border-bottom: none;
        }
        
        /* Stamp */
        .stamp-prepaid {
            display: inline-block;
            border: 3px solid #10b981;
            color: #10b981;
            font-size: 18px;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 12px;
            text-transform: uppercase;
            transform: rotate(-5deg);
            text-align: center;
            background: rgba(16, 185, 129, 0.05);
        }
        
        /* Notes */
        .notes-box { 
            background: #fffbeb; 
            border: 1px solid #fde68a; 
            border-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 5px solid #f59e0b; 
            padding: 20px; 
            border-radius: 8px; 
            color: #92400e; 
            font-size: 13px; 
            margin-top: 30px; 
        }
        
        /* Footer */
        .footer-line {
            border-top: 2px solid #1e3a8a;
            padding-top: 15px;
            font-size: 11px;
            color: #64748b;
            margin: 0 40px;
        }
    </style>
</head>
<body>

    <!-- HEADER WITH BLUE BACKGROUND -->
    <div class="header-bg">
        <table class="header-table">
            <tr>
                <td width="50%" valign="middle">
                    <div class="app-name">{{ config('app.name', 'Fly Vio') }}</div>
                    <div style="font-size: 14px; color: #cbd5e1;">{{ __('Hotel Voucher') }}</div>
                </td>
                <td width="50%" class="{{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}" valign="middle">
                    <div class="voucher-title">{{ __('Booking Confirmation') }}</div>
                    <div style="margin-top: 8px; font-size: 12px; color: #e2e8f0;">
                        {{ __('Issue Date') }}: {{ now()->format('d M Y, H:i') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="content-wrapper">

        <!-- IMPORTANT REFERENCES -->
        <table width="100%" style="margin-bottom: 25px;">
            <tr>
                <td width="65%" valign="top" style="padding-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 20px;">
                    <div class="hotel-banner">
                        <div class="text-primary text-2xl font-bold" style="margin-bottom: 10px;">{{ $booking->hotel_name }}</div>
                        <div style="color: #475569; font-size: 14px; margin-bottom: 8px;">
                            📍 {{ $booking->city_name }}, {{ $booking->country_name }}
                        </div>
                        <div style="color: #64748b; font-size: 12px;">
                            {{ __('Internal Ref') }}: <strong style="color: #0f172a;">{{ $booking->reference_num }}</strong>
                        </div>
                    </div>
                </td>
                <td width="35%" valign="top">
                    <div class="ref-box">
                        <div class="ref-label">{{ __('SUPPLIER REF') }}</div>
                        <div class="ref-number">{{ $booking->supplier_confirmation_num ?? 'PENDING' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- STAY DETAILS -->
        <div class="info-box">
            <div class="info-box-title">{{ __('Stay Details') }}</div>
            <div class="info-box-content">
                <table width="100%" cellpadding="8">
                    <tr>
                        <td width="25%" class="border-bottom">
                            <div class="data-label">{{ __('Check-in') }}</div>
                            <div class="data-value">{{ $booking->check_in->format('d M Y') }}</div>
                        </td>
                        <td width="25%" class="border-bottom">
                            <div class="data-label">{{ __('Check-out') }}</div>
                            <div class="data-value">{{ $booking->check_out->format('d M Y') }}</div>
                        </td>
                        <td width="25%" class="border-bottom">
                            <div class="data-label">{{ __('Nights') }}</div>
                            <div class="data-value">{{ $booking->check_in->diffInDays($booking->check_out) }} {{ __('Nights') }}</div>
                        </td>
                        <td width="25%" class="border-bottom text-center" rowspan="2" valign="middle">
                            <div class="stamp-prepaid">PREPAID<br><span style="font-size:11px; font-weight:normal; letter-spacing:1px; color:#10b981;">{{ __('Fully Paid') }}</span></div>
                        </td>
                    </tr>
                    <tr><td colspan="3" style="height: 10px; padding: 0;"></td></tr>
                    <tr>
                        <td colspan="2">
                            <div class="data-label">{{ __('Room Type') }}</div>
                            <div class="data-value text-accent">{{ $booking->room_name }}</div>
                        </td>
                        <td colspan="1">
                            <div class="data-label">{{ __('Board') }}</div>
                            <div class="data-value">{{ $booking->board_type ?? 'Room Only' }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- GUESTS -->
        <div class="info-box">
            <div class="info-box-title">{{ __('Guest Details') }}</div>
            <table class="pax-table">
                <thead>
                    <tr>
                        <th width="15%" class="text-center">{{ __('Room') }}</th>
                        <th width="55%">{{ __('Guest Name') }}</th>
                        <th width="30%">{{ __('Pax Type') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if($booking->pax_details && is_array($booking->pax_details))
                        @foreach($booking->pax_details as $room)
                            @php $passengers = $booking->passengers; @endphp
                            @if($passengers->isNotEmpty())
                                @foreach($passengers as $pax)
                                    <tr>
                                        <td class="text-center font-bold" style="color: #3b82f6;">{{ $room['room_no'] ?? 1 }}</td>
                                        <td class="font-bold">{{ $pax->name }}</td>
                                        <td><span style="background: #f1f5f9; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold;">{{ __($pax->passenger_type) }}</span></td>
                                    </tr>
                                @endforeach
                            @else
                                @if(isset($room['pax']))
                                    @foreach($room['pax'] as $pax)
                                        <tr>
                                            <td class="text-center font-bold" style="color: #3b82f6;">{{ $room['room_no'] ?? $loop->parent->iteration }}</td>
                                            <td class="font-bold">{{ $pax['Title'] ?? '' }} {{ $pax['FirstName'] ?? '' }} {{ $pax['LastName'] ?? '' }}</td>
                                            <td><span style="background: #f1f5f9; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold;">{{ ($pax['type'] ?? 'AD') === 'CH' ? __('Child') : __('Adult') }}</span></td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center font-bold" style="color: #3b82f6;">1</td>
                            <td class="font-bold">{{ $booking->user->name ?? 'Primary Guest' }}</td>
                            <td><span style="background: #f1f5f9; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold;">{{ __('Adult') }}</span></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- NOTES -->
        <div class="notes-box">
            <strong style="display: block; margin-bottom: 8px; font-size: 14px; text-transform: uppercase;">⚠️ {{ __('Important Notes:') }}</strong>
            <ul style="margin: 0; padding-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 20px; line-height: 1.8;">
                <li>{{ __('Please present this voucher along with original ID upon arrival.') }}</li>
                <li><strong>{{ __('This booking is PREPAID in full by our agency. Do not collect any room charges for the booked stay from the guest.') }}</strong></li>
                <li>{{ __('Any extra incidentals (mini-bar, room service, etc.) are to be settled by the guest directly with the hotel.') }}</li>
            </ul>
            <div style="margin-top: 15px; font-weight: bold;">
                {{ __('For support, please contact :name customer service.', ['name' => config('app.name')]) }}
            </div>
        </div>

    </div>

    <!-- PAGE FOOTER (mPDF feature) -->
    <htmlpagefooter name="page-footer">
        <table class="footer-line" width="100%">
            <tr>
                <td width="33%"><strong>{{ config('app.name') }}</strong> &copy; {{ date('Y') }}</td>
                <td width="33%" align="center">{{ __('Generated electronically') }}</td>
                <td width="33%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; direction: ltr;">Page {PAGENO} of {nbpg}</td>
            </tr>
        </table>
    </htmlpagefooter>

</body>
</html>
