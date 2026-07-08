<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>Hotel Voucher - {{ $booking->supplier_confirmation_num }}</title>
    <style>
<<<<<<< HEAD
        @page {
            margin: 25px;
            header: page-header;
            footer: page-footer;
        }
        body { 
            font-family: 'Cairo', sans-serif; 
            direction: rtl; 
            text-align: right; 
            color: #334155; 
            line-height: 1.5; 
            font-size: 13px;
            background: #ffffff;
        }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 6px 0; }
        
        .text-primary { color: #001841; }
        .text-muted { color: #64748b; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .text-lg { font-size: 16px; }
        .text-xl { font-size: 20px; }
        
        /* Layout Blocks */
        .header-table { margin-bottom: 25px; border-bottom: 2px solid #001841; padding-bottom: 15px; }
        .app-name { font-size: 28px; font-weight: 900; color: #001841; }
        .voucher-title { font-size: 14px; color: #64748b; letter-spacing: 1px; text-transform: uppercase; }
        
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px; margin-bottom: 20px; }
        .info-box-title { background: #001841; color: #ffffff; padding: 6px 12px; font-size: 14px; font-weight: bold; border-radius: 4px 4px 0 0; margin: -15px -15px 15px -15px; }
        
        /* Key Reference Highlight */
        .ref-box { background: #001841; color: #ffffff; text-align: center; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .ref-label { font-size: 11px; opacity: 0.8; margin-bottom: 5px; }
        .ref-number { font-size: 24px; font-weight: bold; letter-spacing: 1px; }

        /* Grid emulation with tables */
        .data-label { color: #64748b; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .data-value { font-size: 14px; font-weight: bold; color: #0f172a; margin-top: 2px; }
        
        .border-bottom { border-bottom: 1px solid #e2e8f0; }
        
        .pax-table th { background: #f1f5f9; color: #475569; font-size: 12px; text-align: right; padding: 8px; border: 1px solid #e2e8f0; }
        .pax-table td { padding: 8px; border: 1px solid #e2e8f0; font-size: 13px; }
        
        .stamp-prepaid {
            display: inline-block;
            border: 3px solid #10b981;
            color: #10b981;
            font-size: 18px;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 8px;
            text-transform: uppercase;
            transform: rotate(-5deg);
            margin-top: 10px;
        }
        
        .footer-note { font-size: 11px; color: #64748b; background: #f8fafc; border-right: 3px solid #001841; padding: 10px; margin-top: 30px; }
    </style>
</head>
<body>

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td width="50%">
                <div class="app-name">{{ config('app.name') }}</div>
            </td>
            <td width="50%" class="text-left">
                <div class="voucher-title">Hotel Accommodation Voucher</div>
                <div class="voucher-title">قسيمة حجز فندق</div>
                <div style="margin-top: 5px; font-size: 11px; color: #94a3b8;">Issue Date: {{ now()->format('d M Y - H:i') }}</div>
            </td>
        </tr>
    </table>

    <!-- IMPORTANT REFERENCES -->
    <table width="100%">
        <tr>
            <td width="60%" valign="top" style="padding-left: 20px;">
                <!-- Hotel Details -->
                <div class="info-box" style="height: 100px;">
                    <div class="text-primary text-xl font-bold" style="margin-bottom: 8px;">{{ $booking->hotel_name }}</div>
                    <div class="text-muted" style="margin-bottom: 4px;">📍 {{ $booking->city_name }}, {{ $booking->country_name }}</div>
                    <div class="text-muted" style="font-size: 11px;">Agency Ref / المرجع الداخلي: <strong>{{ $booking->reference_num }}</strong></div>
                </div>
            </td>
            <td width="40%" valign="top">
                <div class="ref-box" style="height: 100px;">
                    <div class="ref-label">SUPPLIER CONFIRMATION NO.<br>رقم تأكيد الفندق</div>
                    <div class="ref-number">{{ $booking->supplier_confirmation_num }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- STAY DETAILS -->
    <div class="info-box">
        <div class="info-box-title">تفاصيل الإقامة / Stay Details</div>
        <table width="100%" cellpadding="5">
            <tr>
                <td width="25%" class="border-bottom">
                    <div class="data-label">Check-in / الدخول</div>
                    <div class="data-value">{{ $booking->check_in->format('d M Y') }}</div>
                </td>
                <td width="25%" class="border-bottom">
                    <div class="data-label">Check-out / الخروج</div>
                    <div class="data-value">{{ $booking->check_out->format('d M Y') }}</div>
                </td>
                <td width="25%" class="border-bottom">
                    <div class="data-label">Nights / الليالي</div>
                    <div class="data-value">{{ $booking->check_in->diffInDays($booking->check_out) }} Nights</div>
                </td>
                <td width="25%" class="border-bottom text-center">
                    <div class="stamp-prepaid">PREPAID<br><span style="font-size:12px;">مدفوع بالكامل</span></div>
                </td>
            </tr>
            <tr><td colspan="4" style="height: 10px;"></td></tr>
            <tr>
                <td colspan="2">
                    <div class="data-label">Room Type / نوع الغرفة</div>
                    <div class="data-value">{{ $booking->room_name }}</div>
                </td>
                <td colspan="2">
                    <div class="data-label">Meal Plan / الوجبات</div>
                    <div class="data-value">{{ $booking->board_type ?? 'Room Only / بدون وجبات' }}</div>
=======
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
>>>>>>> 50ccf0b5eeb248d0a7daff20523a4cc5e5b423c3
                </td>
            </tr>
        </table>
    </div>

<<<<<<< HEAD
    <!-- GUESTS -->
    <div class="info-box">
        <div class="info-box-title">أسماء النزلاء / Guest Names</div>
        
        <table class="pax-table">
            <thead>
                <tr>
                    <th width="15%">الغرفة / Room</th>
                    <th width="55%">اسم النزيل / Guest Name</th>
                    <th width="30%">نوع النزيل / Pax Type</th>
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
                                    <td>{{ __($pax->passenger_type) }} ({{ strtoupper($pax->passenger_type) }})</td>
                                </tr>
                            @endforeach
                        @else
                            @if(isset($room['pax']))
                                @foreach($room['pax'] as $pax)
                                    <tr>
                                        <td class="text-center font-bold">{{ $room['room_no'] ?? $loop->parent->iteration }}</td>
                                        <td class="font-bold">{{ $pax['Title'] ?? '' }} {{ $pax['FirstName'] ?? '' }} {{ $pax['LastName'] ?? '' }}</td>
                                        <td>{{ ($pax['type'] ?? 'AD') === 'CH' ? 'طفل (Child)' : 'بالغ (Adult)' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endif
                    @endforeach
                @else
                    <tr>
                        <td class="text-center font-bold">1</td>
                        <td class="font-bold">{{ $booking->user->name ?? 'Primary Guest' }}</td>
                        <td>بالغ (Adult)</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- FOOTER NOTES -->
    <div class="footer-note">
        <strong>⚠️ ملاحظات هامة / Important Notes:</strong><br>
        - يرجى تقديم هذه القسيمة (Voucher) مطبوعة أو رقمية عند تسجيل الدخول في الفندق.<br>
        - Please present this voucher (printed or digital) upon check-in at the hotel.<br>
        - هذا الحجز مدفوع بالكامل مسبقاً عن طريق وكالتنا (Prepaid). لا تقم بتحصيل أي مبالغ من النزيل تخص تكلفة الغرفة المحجوزة أعلاه.<br>
        - This booking is PREPAID in full by our agency. Do not collect any room charges for the booked stay from the guest.<br>
        - أي خدمات إضافية يطلبها النزيل (ميني بار، خدمة غرف، إلخ) تُدفع مباشرة للفندق بواسطة النزيل.<br>
        - Any extra incidentals (mini-bar, room service, etc.) are to be settled by the guest directly with the hotel.<br><br>
        لأي استفسارات، يرجى التواصل مع الدعم الفني: <strong>{{ config('app.name') }}</strong>
    </div>

    <!-- PAGE FOOTER (mPDF feature) -->
    <htmlpagefooter name="page-footer">
        <table width="100%" style="border-top: 1px solid #e2e8f0; padding-top: 5px; font-size: 10px; color: #94a3b8;">
            <tr>
                <td width="33%">{{ config('app.name') }} &copy; {{ date('Y') }}</td>
                <td width="33%" align="center">Generated electronically / مصدر إلكترونياً</td>
                <td width="33%" style="text-align: left; direction: ltr;">Page {PAGENO} of {nbpg}</td>
=======
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
>>>>>>> 50ccf0b5eeb248d0a7daff20523a4cc5e5b423c3
            </tr>
        </table>
    </htmlpagefooter>

<<<<<<< HEAD
=======
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
>>>>>>> 50ccf0b5eeb248d0a7daff20523a4cc5e5b423c3
</body>
</html>
