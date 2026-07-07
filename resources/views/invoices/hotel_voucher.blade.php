<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>Voucher - {{ $booking->supplier_confirmation_num }}</title>
    <style>
        @page { margin: 0; padding: 0; }
        body { font-family: 'Cairo', sans-serif; direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; color: #1e293b; line-height: 1.5; margin: 0; padding: 0; font-size: 14px; background: #ffffff; }
        .container { padding: 30px; }
        .header { background: #0f4c81; color: #ffffff; padding: 25px 30px; }
        .logo { font-size: 28px; font-weight: 800; }
        .doc-title { font-size: 22px; margin: 0; font-weight: 800; }
        .layout-table { width: 100%; border-collapse: collapse; border: none; }
        .layout-table td { vertical-align: top; border: none; }
        
        .badge { background: #10b981; color: white; padding: 6px 12px; border-radius: 4px; display: inline-block; font-size: 13px; font-weight: bold; }
        .section { border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 20px; background: #f8fafc; overflow: hidden; }
        .section-title { background: #e2e8f0; padding: 10px 15px; font-weight: 800; color: #0f4c81; font-size: 15px; margin: 0; border-bottom: 1px solid #cbd5e1; }
        .section-content { padding: 15px; }
        
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 8px 5px; border-bottom: 1px dashed #e2e8f0; }
        .info-table td:last-child { border-bottom: none; }
        .label { color: #64748b; font-size: 12px; font-weight: bold; display: block; margin-bottom: 3px; }
        .value { font-weight: 700; color: #1e293b; font-size: 14px; }
        
        .guest-item { padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
        .guest-item:last-child { border-bottom: none; padding-bottom: 0; }
        .pax-type { background: #cbd5e1; color: #1e293b; padding: 2px 6px; border-radius: 3px; font-size: 11px; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 5px; }
        
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .notes { background: #fffbeb; border: 1px solid #fde68a; border-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 4px solid #f59e0b; padding: 15px; border-radius: 4px; color: #92400e; font-size: 13px; margin-top: 20px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <table class="layout-table">
            <tr>
                <td width="50%">
                    <div class="logo">Fly Vio</div>
                    <div style="font-size: 13px; opacity: 0.9; margin-top: 5px;">{{ __('Hotel Voucher') }}</div>
                </td>
                <td width="50%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                    <div class="doc-title">{{ __('Booking Confirmation') }}</div>
                    <div style="font-size: 13px; opacity: 0.9; margin-top: 5px;">{{ __('Issue Date') }}: {{ now()->format('Y-m-d H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="container">
        <!-- Main Info -->
        <table class="layout-table" style="margin-bottom: 20px;">
            <tr>
                <td width="60%">
                    <div style="font-size: 18px; font-weight: 800; color: #0f4c81; margin-bottom: 5px;">{{ $booking->hotel_name }}</div>
                    <div style="color: #64748b; font-size: 14px;">📍 {{ $booking->city_name }}, {{ $booking->country_name }}</div>
                </td>
                <td width="40%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                    <div style="margin-bottom: 5px; color: #64748b; font-size: 12px; font-weight: bold;">{{ __('SUPPLIER REF') }}</div>
                    <div class="badge">{{ $booking->supplier_confirmation_num }}</div>
                </td>
            </tr>
        </table>

        <!-- Details Grid -->
        <table class="layout-table">
            <tr>
                <td width="55%" style="padding-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 10px;">
                    <!-- Stay Details -->
                    <div class="section">
                        <div class="section-title">{{ __('Stay Details') }}</div>
                        <div class="section-content">
                            <table class="info-table">
                                <tr>
                                    <td>
                                        <span class="label">{{ __('Check-in') }}</span>
                                        <span class="value">{{ $booking->check_in->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="label">{{ __('Check-out') }}</span>
                                        <span class="value">{{ $booking->check_out->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="label">{{ __('Nights') }}</span>
                                        <span class="value">{{ $booking->check_in->diffInDays($booking->check_out) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <span class="label">{{ __('Room Type') }}</span>
                                        <span class="value">{{ $booking->room_name }}</span>
                                    </td>
                                    <td>
                                        <span class="label">{{ __('Board') }}</span>
                                        <span class="value">{{ $booking->board_type ?? 'Bed & Breakfast' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <span class="label">{{ __('Internal Ref') }}</span>
                                        <span class="value">{{ $booking->reference_num }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
                
                <td width="45%" style="padding-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 10px;">
                    <!-- Guest Details -->
                    <div class="section" style="min-height: 195px;">
                        <div class="section-title">{{ __('Guests') }}</div>
                        <div class="section-content">
                            @if($booking->pax_details && is_array($booking->pax_details))
                                @foreach($booking->pax_details as $room)
                                    <div style="margin-bottom: 10px;">
                                        <div style="font-size: 13px; font-weight: bold; color: #0f4c81; margin-bottom: 5px; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px;">{{ __('Room') }} {{ $room['room_no'] ?? $loop->iteration }}:</div>
                                        @php $passengers = $booking->passengers; @endphp
                                        @if($passengers->isNotEmpty())
                                            @foreach($passengers as $pax)
                                                <div class="guest-item">
                                                    <span class="pax-type">{{ __($pax->passenger_type) }}</span> <span class="value">{{ $pax->name }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            @if(isset($room['pax']))
                                                @foreach($room['pax'] as $pax)
                                                    <div class="guest-item">
                                                        <span class="pax-type">{{ __((($pax['type'] ?? 'AD') === 'CH') ? 'Child' : 'Adult') }}</span>
                                                        <span class="value">{{ $pax['Title'] ?? '' }} {{ $pax['FirstName'] ?? '' }} {{ $pax['LastName'] ?? '' }}</span>
                                                    </div>
                                                @endforeach
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="guest-item">
                                    <span class="value">{{ $booking->user->name ?? 'Guest' }}</span>
                                </div>
                            @endif

                            <div style="margin-top: 15px; background: #e0f2fe; padding: 10px; border-radius: 4px; border: 1px solid #bae6fd;">
                                <table class="layout-table">
                                    <tr>
                                        <td><strong style="color: #0369a1;">{{ __('Total Price (Paid)') }}</strong></td>
                                        <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};"><strong style="font-size: 16px; color: #0f4c81;">{{ number_format($booking->total_price, 2) }} {{ $booking->currency }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Notes -->
        <div class="notes">
            <strong style="font-size: 14px; display: block; margin-bottom: 5px;">⚠️ {{ __('Important Notes:') }}</strong>
            - {{ __('Please present this voucher along with original ID upon arrival.') }}<br>
            - {{ __('The booking is fully paid. Any additional services (e.g. city tax) are paid directly to the hotel.') }}<br>
            - {{ __('For support, please contact Fly Vio customer service.') }}
        </div>

        <!-- Footer -->
        <div class="footer">
            {{ __('This document is electronically generated and legally binding under the terms of') }} <strong>Fly Vio</strong><br>
            &copy; {{ date('Y') }} Fly Vio
        </div>
    </div>
</body>
</html>
