<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Flight Invoice') }} - {{ $booking->booking_reference }}</title>
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
        
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #e2e8f0; padding: 10px; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; }
        .data-table th { background-color: #f1f5f9; color: #475569; font-weight: 800; font-size: 13px; }
        .total-row { background-color: #0f4c81; color: white; font-weight: bold; }
        .total-row td { border-color: #0f4c81; color: white; font-size: 16px; }

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
                    <div style="font-size: 13px; opacity: 0.9; margin-top: 5px;">{{ __('Flight Invoice') }}</div>
                </td>
                <td width="50%" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                    <div class="doc-title">{{ __('Tax Invoice') }}</div>
                    <div style="font-size: 13px; opacity: 0.9; margin-top: 5px;">{{ __('Issue Date') }}: {{ $booking->created_at->format('Y-m-d H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="container">
        <!-- Main Info -->
        <table class="layout-table" style="margin-bottom: 20px;">
            <tr>
                <td width="50%">
                    <div class="section" style="margin-bottom: 0;">
                        <div class="section-title">{{ __('Customer Details') }}</div>
                        <div class="section-content">
                            <table class="info-table">
                                <tr>
                                    <td>
                                        <span class="label">{{ __('Name') }}:</span>
                                        <span class="value">{{ $booking->passengers->first()->first_name ?? 'N/A' }} {{ $booking->passengers->first()->last_name ?? '' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="label">{{ __('Phone') }}:</span>
                                        <span class="value">{{ $booking->contact_phone ?? '—' }}</span>
                                    </td>
                                </tr>
                                @if(isset($booking->user) && $booking->user->email)
                                <tr>
                                    <td>
                                        <span class="label">{{ __('Email') }}:</span>
                                        <span class="value">{{ $booking->user->email }}</span>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </td>
                <td width="50%" style="padding-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 15px;">
                    <div class="section" style="margin-bottom: 0;">
                        <div class="section-title">{{ __('Invoice Details') }}</div>
                        <div class="section-content">
                            <table class="info-table">
                                <tr>
                                    <td><span class="label">{{ __('PNR') }}:</span><span class="value">{{ $booking->booking_reference }}</span></td>
                                </tr>
                                <tr>
                                    <td><span class="label">{{ __('Booking Date') }}:</span><span class="value">{{ $booking->created_at->format('Y-m-d') }}</span></td>
                                </tr>
                                <tr>
                                    <td><span class="label">{{ __('Payment Status') }}:</span><span class="value" style="color: #10b981;">{{ __('Confirmed') }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Flight Details -->
        <div class="section">
            <div class="section-title">{{ __('Flight Details') }}</div>
            <div class="section-content" style="padding-top: 5px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="75%">{{ __('Description') }}</th>
                            <th width="25%">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="font-weight: 800; color: #0f4c81;">{{ __('Flight Ticket') }}</div>
                                <div style="margin-top: 5px; font-size: 12px; color: #64748b;">
                                    {{ __('PNR') }}: {{ $booking->booking_reference }}
                                </div>
                            </td>
                            <td>{{ number_format($booking->total_amount, 2) }} {{ $booking->currency }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; padding-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 20px;">{{ __('Total') }}</td>
                            <td>{{ number_format($booking->total_amount, 2) }} {{ $booking->currency }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($booking->passengers && $booking->passengers->count() > 0)
        <!-- Passengers -->
        <div class="section">
            <div class="section-title">{{ __('Passengers List') }}</div>
            <div class="section-content" style="padding-top: 5px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="10%">#</th>
                            <th width="50%">{{ __('Name') }}</th>
                            <th width="40%">{{ __('Passenger Type') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->passengers as $pax)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td style="font-weight: bold;">{{ $pax->title }} {{ $pax->first_name }} {{ $pax->last_name }}</td>
                            <td>{{ ucfirst($pax->passenger_type) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Notes -->
        <div class="notes">
            <strong style="font-size: 14px; display: block; margin-bottom: 5px;">⚠️ {{ __('Important Notes:') }}</strong>
            - {{ __('This invoice is automatically generated by the system and does not require a signature or stamp.') }}<br>
            - {{ __('Thank you for choosing Fly Vio. Have a safe and pleasant trip.') }}
        </div>

        <!-- Footer -->
        <div class="footer">
            {{ __('This document is electronically generated and legally binding under the terms of') }} <strong>Fly Vio</strong><br>
            &copy; {{ date('Y') }} Fly Vio | {{ config('app.url') }}
        </div>
    </div>
</body>
</html>
