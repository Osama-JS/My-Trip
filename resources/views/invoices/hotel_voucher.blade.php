<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>قسيمة حجز فندق - {{ $booking->supplier_confirmation_num }}</title>
    <style>
        body { font-family: 'Cairo', sans-serif; direction: rtl; text-align: right; color: #333; line-height: 1.6; }
        .header { border-bottom: 2px solid #0f4c81; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #0f4c81; }
        .voucher-title { font-size: 28px; font-weight: 800; text-align: center; margin-bottom: 40px; color: #1e293b; text-transform: uppercase; }
        
        .section { margin-bottom: 30px; }
        .section-title { background: #f1f5f9; padding: 10px 15px; font-weight: 700; border-right: 4px solid #0f4c81; margin-bottom: 15px; font-size: 1.1rem; }
        
        .grid { display: flex; flex-wrap: wrap; }
        .col { width: 48%; margin-bottom: 15px; }
        .label { color: #64748b; font-size: 0.85rem; margin-bottom: 2px; }
        .value { font-weight: 700; color: #1e293b; }
        
        .hotel-name { font-size: 1.4rem; font-weight: 800; color: #0f4c81; margin-bottom: 5px; }
        .confirmation-box { background: #10b981; color: white; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 30px; }
        .confirmation-label { font-size: 0.9rem; opacity: 0.9; }
        .confirmation-num { font-size: 1.8rem; font-weight: 950; letter-spacing: 2px; }
        
        .footer { margin-top: 50px; font-size: 0.8rem; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">{{ config('app.name') }}</div>
        <div style="float: left; text-align: left; font-size: 0.8rem; color: #64748b;">
            تاريخ الإصدار: {{ now()->format('Y-m-d') }}
        </div>
    </div>

    <div class="voucher-title">قسيمة تأكيد الحجز (Voucher)</div>

    <div class="confirmation-box">
        <div style="display: flex; justify-content: space-around;">
            <div>
                <div class="confirmation-label">رقم مرجع الفندق (Supplier Ref)</div>
                <div class="confirmation-num">{{ $booking->supplier_confirmation_num }}</div>
            </div>
            @if($booking->reference_num)
            <div>
                <div class="confirmation-label">رقم الحجز (Booking ID)</div>
                <div class="confirmation-num" style="font-size: 1.4rem;">{{ $booking->reference_num }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">تفاصيل الفندق</div>
        <div class="hotel-name">{{ $booking->hotel_name }}</div>
        <div class="label">العنوان</div>
        <div class="value">{{ $booking->city_name }}, {{ $booking->country_name }}</div>
    </div>

    <div class="section">
        <div class="section-title">تفاصيل الإقامة</div>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="33%">
                    <div class="label">تاريخ الدخول</div>
                    <div class="value">{{ $booking->check_in->format('Y-m-d') }}</div>
                </td>
                <td width="33%">
                    <div class="label">تاريخ الخروج</div>
                    <div class="value">{{ $booking->check_out->format('Y-m-d') }}</div>
                </td>
                <td width="33%">
                    <div class="label">عدد الليالي</div>
                    <div class="value">{{ $booking->check_in->diffInDays($booking->check_out) }}</div>
                </td>
            </tr>
            <tr><td colspan="3" style="height: 15px;"></td></tr>
            <tr>
                <td>
                    <div class="label">نوع الغرفة</div>
                    <div class="value">{{ $booking->room_name }}</div>
                </td>
                <td>
                    <div class="label">نوع الوجبة</div>
                    <div class="value">{{ $booking->board_type ?? 'N/A' }}</div>
                </td>
                <td>
                    <div class="label">حالة الحجز</div>
                    <div class="value" style="color: #10b981;">{{ strtoupper($booking->status) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">تفاصيل النزلاء (Guest Details)</div>
        @if($booking->pax_details && is_array($booking->pax_details))
            @foreach($booking->pax_details as $room)
                <div style="margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">
                    <strong>الغرفة {{ $room['room_no'] ?? $loop->iteration }}:</strong>
                    @if(isset($room['pax']))
                        @foreach($room['pax'] as $pax)
                            <span style="display: inline-block; margin-right: 15px; font-size: 0.9rem;">
                                {{ $pax['Title'] ?? $pax['title'] ?? '' }} {{ $pax['FirstName'] ?? $pax['firstName'] ?? '' }} {{ $pax['LastName'] ?? $pax['lastName'] ?? '' }}
                                ({{ ($pax['type'] ?? 'AD') === 'CH' ? 'طفل' : 'بالغ' }})
                            </span>
                        @endforeach
                    @endif
                </div>
            @endforeach
        @else
            <div class="label">الاسم</div>
            <div class="value">{{ $booking->user->name ?? 'Guest' }}</div>
            <div class="label">رقم التواصل</div>
            <div class="value">{{ $booking->user->phone ?? 'N/A' }}</div>
        @endif
    </div>

    <div class="section" style="margin-top: 40px; border: 1px dashed #cbd5e1; padding: 15px; border-radius: 8px;">
        <div style="font-size: 0.85rem; color: #475569;">
            <strong>ملاحظات هامة:</strong><br>
            - يرجى إبراز هذه القسيمة عند موظف الاستقبال في الفندق عند الدخول.<br>
            - الأسعار تشمل الضرائب والرسوم إلا إذا ذكر غير ذلك.<br>
            - في حال واجهت أي مشكلة، يرجى التواصل مع دعم عملاء {{ config('app.name') }}.
        </div>
    </div>

    <div class="footer">
        هذا المستند تم إنشاؤه آلياً من منصة {{ config('app.name') }}.<br>
        &copy; {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة.
    </div>
</body>
</html>
