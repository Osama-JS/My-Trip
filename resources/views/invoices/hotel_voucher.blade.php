<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Hotel Voucher - {{ $booking->supplier_confirmation_num }}</title>
    <style>
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
                </td>
            </tr>
        </table>
    </div>

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
            </tr>
        </table>
    </htmlpagefooter>

</body>
</html>
