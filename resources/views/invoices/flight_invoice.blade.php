<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Flight Invoice') }} - {{ $booking->booking_reference }}</title>
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
        .uppercase { text-transform: uppercase; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; border: none; }
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

        /* Banner / Reference */
        .ref-box { 
            background: #f8fafc; 
            border-left: 5px solid #3b82f6; 
            padding: 20px; 
            border-radius: 0 8px 8px 0;
            margin-bottom: 25px;
        }
        html[dir="rtl"] .ref-box {
            border-left: none;
            border-right: 5px solid #3b82f6;
            border-radius: 8px 0 0 8px;
        }
        .ref-label { font-size: 13px; color: #64748b; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
        .ref-number { font-size: 26px; font-weight: bold; color: #1e3a8a; }
        
        .status-badge { background: #10b981; color: white; padding: 6px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block; }

        /* Sections */
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

        /* Small Info Tables */
        .info-table td { padding: 8px 5px; border-bottom: 1px dashed #e2e8f0; }
        .info-table tr:last-child td { border-bottom: none; }
        .label { color: #64748b; font-size: 12px; font-weight: bold; display: block; margin-bottom: 4px; text-transform: uppercase; }
        .value { font-weight: 700; color: #0f172a; font-size: 14px; }
        
        /* Data Table */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 12px 20px; border-bottom: 1px solid #e2e8f0; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; }
        .data-table th { background-color: #f8fafc; color: #475569; font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .data-table tr:last-child td { border-bottom: none; }
        .total-row { background-color: #1e3a8a; color: white; }
        .total-row td { color: white; font-size: 16px; font-weight: bold; border-color: #1e3a8a; }

        /* E-Ticket Highlight */
        .eticket-badge { background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; display: inline-block; border: 1px solid rgba(16, 185, 129, 0.2); }

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
    
    <!-- HEADER -->
    <div class="header-bg">
        <table class="header-table">
            <tr>
                <td width="50%" valign="middle">
                    <div class="app-name">{{ config('app.name', 'Fly Vio') }}</div>
                    <div style="font-size: 14px; color: #cbd5e1;">{{ __('Flight Invoice') }}</div>
                </td>
                <td width="50%" class="{{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}" valign="middle">
                    <div class="voucher-title">{{ __('Tax Invoice') }}</div>
                    <div style="margin-top: 8px; font-size: 12px; color: #e2e8f0;">
                        {{ __('Issue Date') }}: {{ $booking->created_at->format('d M Y, H:i') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="content-wrapper">

        <!-- REFERENCE BANNER -->
        <table width="100%" style="margin-bottom: 25px;">
            <tr>
                <td width="60%">
                    <div class="ref-box">
                        <div class="ref-label">{{ __('Booking Reference (PNR)') }}</div>
                        <div class="ref-number">{{ $booking->booking_reference }}</div>
                    </div>
                </td>
                <td width="40%" class="{{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}" valign="middle">
                    <div class="status-badge">✓ {{ __('Confirmed & Paid') }}</div>
                </td>
            </tr>
        </table>

        <!-- TWO COLUMN INFO -->
        <table width="100%" style="margin-bottom: 25px;">
            <tr>
                <td width="50%" style="padding-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 15px;">
                    <div class="info-box" style="margin-bottom: 0;">
                        <div class="info-box-title">{{ __('Customer Details') }}</div>
                        <div class="info-box-content">
                            <table class="info-table">
                                <tr>
                                    <td>
                                        <span class="label">{{ __('Name') }}</span>
                                        <span class="value">{{ $booking->passengers->first()->first_name ?? 'N/A' }} {{ $booking->passengers->first()->last_name ?? '' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="label">{{ __('Phone') }}</span>
                                        <span class="value">{{ $booking->contact_phone ?? '—' }}</span>
                                    </td>
                                </tr>
                                @if(isset($booking->user) && $booking->user->email)
                                <tr>
                                    <td>
                                        <span class="label">{{ __('Email') }}</span>
                                        <span class="value">{{ $booking->user->email }}</span>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </td>
                <td width="50%" style="padding-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 15px;">
                    <div class="info-box" style="margin-bottom: 0; height: 100%;">
                        <div class="info-box-title">{{ __('Invoice Details') }}</div>
                        <div class="info-box-content">
                            <table class="info-table">
                                <tr>
                                    <td>
                                        <span class="label">{{ __('Invoice Date') }}</span>
                                        <span class="value">{{ $booking->created_at->format('d M Y') }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="label">{{ __('Payment Method') }}</span>
                                        <span class="value">{{ __('Electronic Payment') }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="label">{{ __('Payment Status') }}</span>
                                        <span class="value" style="color: #10b981;">{{ __('Fully Paid') }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- FLIGHT CHARGES -->
        <div class="info-box">
            <div class="info-box-title">{{ __('Charges Breakdown') }}</div>
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
                            <div style="font-weight: 800; color: #1e3a8a; font-size: 14px;">{{ __('Flight Ticket') }}</div>
                            <div style="margin-top: 5px; font-size: 12px; color: #64748b;">
                                {{ __('Booking PNR') }}: <strong style="color:#0f172a;">{{ $booking->booking_reference }}</strong>
                            </div>
                        </td>
                        <td style="font-size: 15px; font-weight: bold;">{{ number_format($booking->total_amount, 2) }} {{ $booking->currency }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; padding-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 20px;">{{ __('Total Amount Paid') }}</td>
                        <td>{{ number_format($booking->total_amount, 2) }} {{ $booking->currency }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- PASSENGERS -->
        @if($booking->passengers && $booking->passengers->count() > 0)
        <div class="info-box">
            <div class="info-box-title">{{ __('Passengers List') }}</div>
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
                    @php
                        $paxName = trim(strtoupper($pax->first_name ?? '') . ' ' . strtoupper($pax->last_name ?? ''));
                        $ticket = isset($eTickets) && isset($eTickets[$paxName]) ? $eTickets[$paxName] : null;
                    @endphp
                    <tr>
                        <td style="font-weight: bold; color: #3b82f6;">{{ $loop->iteration }}</td>
                        <td>
                            <div style="font-weight: bold; font-size: 14px; color:#0f172a;">{{ $pax->title }} {{ $pax->first_name }} {{ $pax->last_name }}</div>
                            @if($ticket)
                                <div style="margin-top: 6px;"><span class="eticket-badge">✈️ {{ __('eTicket') }}: {{ $ticket }}</span></div>
                            @endif
                        </td>
                        <td><span style="background: #f1f5f9; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold;">{{ ucfirst($pax->passenger_type) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- NOTES -->
        <div class="notes-box">
            <strong style="font-size: 14px; display: block; margin-bottom: 8px; text-transform: uppercase;">⚠️ {{ __('Important Notes:') }}</strong>
            <ul style="margin: 0; padding-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 20px; line-height: 1.8;">
                <li>{{ __('This invoice is automatically generated by the system and does not require a signature or stamp.') }}</li>
                <li>{{ __('Please present your eTicket and valid passport/ID at the check-in counter.') }}</li>
            </ul>
            <div style="margin-top: 15px; font-weight: bold;">
                {{ __('Thank you for choosing :name. Have a safe and pleasant trip.', ['name' => config('app.name')]) }}
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
