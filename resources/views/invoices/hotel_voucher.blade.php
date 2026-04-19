<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Voucher - {{ $booking->supplier_confirmation_num }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }
        body { 
            font-family: 'Cairo', sans-serif; 
            direction: rtl; 
            text-align: right; 
            color: #1e293b; 
            line-height: 1.35; 
            margin: 0;
            padding: 0;
            background: #fff;
            width: 100%;
        }
        .container {
            padding: 30px;
            width: 100%;
        }
        .header-bg {
            background: #0f4c81;
            height: 110px;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: -1;
        }
        .logo { 
            font-size: 26px; 
            font-weight: 900; 
            color: #ffffff; 
        }
        .voucher-header {
            background: #ffffff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
        }
        .voucher-title { 
            font-size: 20px; 
            font-weight: 800; 
            color: #0f4c81; 
            margin: 0;
        }
        .issued-date { font-size: 0.75rem; color: #64748b; }
        
        .main-table { width: 100%; border-collapse: collapse; }
        .card { background: #f8fafc; border-radius: 8px; padding: 12px; border: 1px solid #f1f5f9; }
        .section-title { 
            font-size: 0.85rem; font-weight: 800; color: #0f4c81; 
            border-bottom: 1.5px solid #e2e8f0; margin-bottom: 10px; padding-bottom: 4px; 
        }
        
        .label { color: #64748b; font-size: 0.7rem; margin-bottom: 1px; font-weight: 600; }
        .value { font-weight: 700; color: #1e293b; font-size: 0.9rem; }
        .hotel-name { font-size: 1.1rem; font-weight: 800; color: #0f4c81; margin-bottom: 3px; }
        
        .conf-badge { background: #10b981; color: white; padding: 8px; border-radius: 5px; text-align: center; }
        .conf-num { font-size: 1.3rem; font-weight: 900; }

        .guest-item { padding: 4px 0; border-bottom: 1px solid #edf2f7; font-size: 0.8rem; }
        .pax-type { font-size: 0.65rem; background: #e2e8f0; padding: 1px 5px; border-radius: 3px; margin-right: 4px; }
        
        .footer { margin-top: 20px; font-size: 0.7rem; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header-bg"></div>
    <div class="container">
        <!-- Header -->
        <table width="100%" style="margin-bottom: 15px;">
            <tr>
                <td><div class="logo">{{ config('app.name') }}</div></td>
                <td align="left" style="color: white; font-size: 0.75rem;">Hotel Accommodation Voucher</td>
            </tr>
        </table>

        <!-- Summary Title area -->
        <div class="voucher-header">
            <table width="100%">
                <tr>
                    <td>
                        <h1 class="voucher-title">تأكيد حجز الفندق (Hotel Voucher)</h1>
                        <span class="issued-date">تاريخ الإصدار: {{ now()->format('Y-m-d H:i') }}</span>
                    </td>
                    <td align="left" width="35%">
                        <div class="conf-badge">
                            <div style="font-size: 0.6rem; opacity: 0.9;">رقم تأكيد المورد (SUPPLIER REF)</div>
                            <div class="conf-num">{{ $booking->supplier_confirmation_num }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Details Grid -->
        <table width="100%" cellpadding="4">
            <tr>
                <!-- Left: Hotel & Stay -->
                <td width="55%" valign="top">
                    <div class="card" style="margin-bottom: 15px;">
                        <div class="section-title">بيانات الفندق والوجهة</div>
                        <div class="hotel-name">{{ $booking->hotel_name }}</div>
                        <div class="value" style="font-size: 0.8rem;">📍 {{ $booking->city_name }}, {{ $booking->country_name }}</div>
                        <div class="label" style="margin-top: 8px;">رقم المرجع الداخلي: <span class="value">{{ $booking->reference_num }}</span></div>
                    </div>

                    <div class="card">
                        <div class="section-title">تفاصيل الإقامة (Stay Details)</div>
                        <table width="100%">
                            <tr>
                                <td><div class="label">تاريخ الدخول</div><div class="value">{{ $booking->check_in->format('d/m/Y') }}</div></td>
                                <td><div class="label">تاريخ الخروج</div><div class="value">{{ $booking->check_out->format('d/m/Y') }}</div></td>
                                <td><div class="label">الليالي</div><div class="value">{{ $booking->check_in->diffInDays($booking->check_out) }}</div></td>
                            </tr>
                            <tr><td colspan="3" style="height: 10px;"></td></tr>
                            <tr>
                                <td colspan="2"><div class="label">نوع الغرفة</div><div class="value">{{ $booking->room_name }}</div></td>
                                <td><div class="label">الوجبات</div><div class="value">{{ $booking->board_type ?? 'Bed & Breakfast' }}</div></td>
                            </tr>
                        </table>
                    </div>
                </td>

                <!-- Right: Guest Data -->
                <td width="45%" valign="top">
                    <div class="card" style="height: 100%;">
                        <div class="section-title">بيانات النزلاء (Guests)</div>
                        @if($booking->pax_details && is_array($booking->pax_details))
                            @foreach($booking->pax_details as $room)
                                <div style="margin-bottom: 6px;">
                                    <div style="font-size: 0.75rem; font-weight: bold; color: #64748b; margin-bottom: 2px;">غرفة {{ $room['room_no'] ?? $loop->iteration }}:</div>
                                    @php $passengers = $booking->passengers; @endphp
                                    @if($passengers->isNotEmpty())
                                        @foreach($passengers as $pax)
                                            <div class="guest-item">
                                                <span class="pax-type">{{ __($pax->passenger_type) }}</span> {{ $pax->name }}
                                            </div>
                                        @endforeach
                                    @else
                                        @if(isset($room['pax']))
                                            @foreach($room['pax'] as $pax)
                                                <div class="guest-item">
                                                    <span class="pax-type">{{ ($pax['type'] ?? 'AD') === 'CH' ? 'طفل' : 'بالغ' }}</span>
                                                    {{ $pax['Title'] ?? '' }} {{ $pax['FirstName'] ?? '' }} {{ $pax['LastName'] ?? '' }}
                                                </div>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="guest-item">{{ $booking->user->name ?? 'Guest' }}</div>
                        @endif

                        <div style="margin-top: 10px; background: #e2e8f0; padding: 8px; border-radius: 5px;">
                            <div class="label">إجمالي السعر (مدفوع)</div>
                            <div class="value" style="font-size: 1rem; color: #0f4c81;">{{ number_format($booking->total_price, 2) }} {{ $booking->currency }}</div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Notes -->
        <div style="margin-top: 15px; border-right: 3px solid #0f4c81; background: #f8fafc; padding: 10px; border-radius: 4px;">
            <div style="font-size: 0.7rem; color: #475569; line-height: 1.5;">
                <strong>⚠️ ملاحظات هامة:</strong><br>
                - يجب تقديم هذا الفوتشر (مطبوعاً أو رقمياً) مع الهوية الأصلية عند موظف الاستقبال.<br>
                - الحجز مدفوع بالكامل شامل الرسوم والضرائب، أي خدمات إضافية تُدفع للفندق مباشرة.<br>
                - في حال واجهت أي استفسار، يرجى الاتصال بخدمة عملاء {{ config('app.name') }}.
            </div>
        </div>

        <div class="footer">
            تم إصدار هذا المستند إلكترونياً وهو ملزم قانوناً بموجب شروط منصة <strong>{{ config('app.name') }}</strong><br>
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>
</html>
