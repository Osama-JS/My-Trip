<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Hotel Voucher') }} - {{ $booking->supplier_confirmation_num ?? $booking->reference_num }}</title>
    <style>
        @page {
            margin: 25px;
            header: page-header;
            footer: page-footer;
        }
        body { 
            font-family: 'Cairo', sans-serif; 
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}; 
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; 
            color: #334155; 
            line-height: 1.5; 
            font-size: 13px;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 6px 0; }
        
        .text-primary { color: #0f4c81; }
        .text-muted { color: #64748b; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-lg { font-size: 16px; }
        .text-xl { font-size: 20px; }
        
        /* Layout Blocks */
        .header-table { margin-bottom: 25px; border-bottom: 2px solid #0f4c81; padding-bottom: 15px; }
        .app-name { font-size: 28px; font-weight: 900; color: #0f4c81; }
        .voucher-title { font-size: 14px; color: #64748b; letter-spacing: 1px; text-transform: uppercase; font-weight: bold; }
        
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px; margin-bottom: 20px; }
        .info-box-title { background: #0f4c81; color: #ffffff; padding: 8px 12px; font-size: 14px; font-weight: bold; border-radius: 4px 4px 0 0; margin: -15px -15px 15px -15px; }
        
        /* Key Reference Highlight */
        .ref-box { background: #0f4c81; color: #ffffff; text-align: center; padding: 15px; border-radius: 6px; margin-bottom: 20px; height: 75px; }
        .ref-label { font-size: 11px; opacity: 0.8; margin-bottom: 5px; text-transform: uppercase; }
        .ref-number { font-size: 22px; font-weight: bold; letter-spacing: 1px; }

        /* Grid emulation with tables */
        .data-label { color: #64748b; font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .data-value { font-size: 14px; font-weight: bold; color: #0f172a; }
        
        .border-bottom { border-bottom: 1px solid #e2e8f0; }
        
        .pax-table th { background: #e2e8f0; color: #0f4c81; font-size: 12px; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; padding: 8px; border: 1px solid #cbd5e1; }
        .pax-table td { padding: 8px; border: 1px solid #cbd5e1; font-size: 13px; }
        
        .stamp-prepaid {
            display: inline-block;
            border: 3px solid #10b981;
            color: #10b981;
            font-size: 16px;
            font-weight: bold;
            padding: 6px 15px;
            border-radius: 8px;
            text-transform: uppercase;
            transform: rotate(-5deg);
            margin-top: 5px;
            text-align: center;
        }
        
        .notes-box { background: #fffbeb; border: 1px solid #fde68a; border-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 4px solid #f59e0b; padding: 15px; border-radius: 4px; color: #92400e; font-size: 12px; margin-top: 20px; }
    </style>
</head>
<body>

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td width="50%">
                <div class="app-name">{{ config('app.name', 'Fly Vio') }}</div>
                <div style="font-size: 12px; opacity: 0.8; margin-top: 5px;">{{ __('Hotel Voucher') }}</div>
            </td>
            <td width="50%" class="{{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">
                <div class="voucher-title">{{ __('Booking Confirmation') }}</div>
                <div style="margin-top: 5px; font-size: 11px; color: #94a3b8;">{{ __('Issue Date') }}: {{ now()->format('Y-m-d H:i') }}</div>
            </td>
        </tr>
    </table>

    <!-- IMPORTANT REFERENCES -->
    <table width="100%">
        <tr>
            <td width="60%" valign="top" style="padding-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 20px;">
                <!-- Hotel Details -->
                <div class="info-box" style="height: 75px;">
                    <div class="text-primary text-xl font-bold" style="margin-bottom: 8px;">{{ $booking->hotel_name }}</div>
                    <div class="text-muted" style="margin-bottom: 4px;">📍 {{ $booking->city_name }}, {{ $booking->country_name }}</div>
                    <div class="text-muted" style="font-size: 11px;">{{ __('Internal Ref') }}: <strong>{{ $booking->reference_num }}</strong></div>
                </div>
            </td>
            <td width="40%" valign="top">
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
        <table width="100%" cellpadding="5">
            <tr>
                <td width="25%" class="border-bottom">
                    <div class="data-label">{{ __('Check-in') }}</div>
                    <div class="data-value">{{ $booking->check_in->format('d/m/Y') }}</div>
                </td>
                <td width="25%" class="border-bottom">
                    <div class="data-label">{{ __('Check-out') }}</div>
                    <div class="data-value">{{ $booking->check_out->format('d/m/Y') }}</div>
                </td>
                <td width="25%" class="border-bottom">
                    <div class="data-label">{{ __('Nights') }}</div>
                    <div class="data-value">{{ $booking->check_in->diffInDays($booking->check_out) }}</div>
                </td>
                <td width="25%" class="border-bottom text-center">
                    <div class="stamp-prepaid">PREPAID<br><span style="font-size:11px;">{{ __('Fully Paid') }}</span></div>
                </td>
            </tr>
            <tr><td colspan="4" style="height: 10px;"></td></tr>
            <tr>
                <td colspan="2">
                    <div class="data-label">{{ __('Room Type') }}</div>
                    <div class="data-value">{{ $booking->room_name }}</div>
                </td>
                <td colspan="2">
                    <div class="data-label">{{ __('Board') }}</div>
                    <div class="data-value">{{ $booking->board_type ?? 'Room Only' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- GUESTS -->
    <div class="info-box">
        <div class="info-box-title">{{ __('Guests') }}</div>
        
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
                                    <td class="text-center font-bold">{{ $room['room_no'] ?? 1 }}</td>
                                    <td class="font-bold">{{ $pax->name }}</td>
                                    <td>{{ __($pax->passenger_type) }}</td>
                                </tr>
                            @endforeach
                        @else
                            @if(isset($room['pax']))
                                @foreach($room['pax'] as $pax)
                                    <tr>
                                        <td class="text-center font-bold">{{ $room['room_no'] ?? $loop->parent->iteration }}</td>
                                        <td class="font-bold">{{ $pax['Title'] ?? '' }} {{ $pax['FirstName'] ?? '' }} {{ $pax['LastName'] ?? '' }}</td>
                                        <td>{{ ($pax['type'] ?? 'AD') === 'CH' ? __('Child') : __('Adult') }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endif
                    @endforeach
                @else
                    <tr>
                        <td class="text-center font-bold">1</td>
                        <td class="font-bold">{{ $booking->user->name ?? 'Primary Guest' }}</td>
                        <td>{{ __('Adult') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- NOTES -->
    <div class="notes-box">
        <strong style="display: block; margin-bottom: 5px; font-size: 13px;">⚠️ {{ __('Important Notes:') }}</strong>
        - {{ __('Please present this voucher along with original ID upon arrival.') }}<br>
        - {{ __('This booking is PREPAID in full by our agency. Do not collect any room charges for the booked stay from the guest.') }}<br>
        - {{ __('Any extra incidentals (mini-bar, room service, etc.) are to be settled by the guest directly with the hotel.') }}<br><br>
        {{ __('For support, please contact :name customer service.', ['name' => config('app.name')]) }}
    </div>

    <!-- PAGE FOOTER (mPDF feature) -->
    <htmlpagefooter name="page-footer">
        <table width="100%" style="border-top: 1px solid #e2e8f0; padding-top: 5px; font-size: 10px; color: #94a3b8;">
            <tr>
                <td width="33%">{{ config('app.name') }} &copy; {{ date('Y') }}</td>
                <td width="33%" align="center">{{ __('Generated electronically') }}</td>
                <td width="33%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; direction: ltr;">Page {PAGENO} of {nbpg}</td>
            </tr>
        </table>
    </htmlpagefooter>

</body>
</html>
