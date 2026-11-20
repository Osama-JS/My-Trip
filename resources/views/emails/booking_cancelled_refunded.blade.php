@extends('emails.layouts.master')

@section('content')
@php
    $isRtl = app()->isLocale('ar');
    $currency = $booking->currency ?? 'SAR';
    $user = $booking->user;
    $userName = $user->name ?? ($user->full_name ?? __('عزيزنا العميل'));
    
    // Total original amount
    $originalTotal = (float)($bookingType === 'hotel' ? ($booking->total_price ?? 0) : ($booking->total_amount ?? 0));
    $refCode = $bookingType === 'hotel' 
        ? ($booking->supplier_confirmation_num ?? ($booking->reference_num ?? '#' . $booking->id))
        : ($booking->pnr_code ?: ($booking->booking_reference ?: '#' . $booking->id));
@endphp

<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; line-height: 1.6; direction: {{ $isRtl ? 'rtl' : 'ltr' }}; text-align: {{ $isRtl ? 'right' : 'left' }};">

    <!-- Status Header -->
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="display: inline-block; width: 60px; height: 60px; line-height: 60px; border-radius: 50%; background-color: #ecfdf5; color: #059669; font-size: 30px; font-weight: bold; margin-bottom: 12px; border: 2px solid #a7f3d0;">
            ↺
        </div>
        <h2 style="color: #041741; margin: 0 0 6px 0; font-size: 22px; font-weight: 800;">
            {{ __('تم إلغاء الحجز واسترداد المبلغ إلى المحفظة') }}
        </h2>
        <p style="color: #64748b; margin: 0; font-size: 14px;">
            {{ __('مرحباً :name، تم تأكيد إلغاء حجزك بنجاح وإيداع المبلغ المسترد في محفظتك الإلكترونية في المنصة.', ['name' => $userName]) }}
        </p>
    </div>

    <!-- Refund & Wallet Highlight Card -->
    <div style="background: linear-gradient(135deg, #041741 0%, #0c2556 100%); border-radius: 14px; padding: 22px; margin-bottom: 24px; color: #ffffff; box-shadow: 0 4px 15px rgba(4, 23, 65, 0.12);">
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
            <tr>
                <td style="vertical-align: middle;">
                    <span style="font-size: 11px; font-weight: 700; color: #93c5fd; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 4px;">
                        💳 {{ __('المبلغ المسترد المودع في المحفظة') }} ({{ __('Wallet Credit') }})
                    </span>
                    <span style="font-size: 26px; font-weight: 900; color: #f2cb57; letter-spacing: 0.5px;">
                        +{{ number_format($refundAmount, 2) }} <span style="font-size: 16px; font-weight: 700;">{{ $currency }}</span>
                    </span>
                </td>
                @if($newWalletBalance !== null)
                <td style="text-align: {{ $isRtl ? 'left' : 'right' }}; vertical-align: middle;">
                    <div style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 10px; padding: 10px 14px; display: inline-block;">
                        <span style="font-size: 10px; color: #cbd5e1; display: block; margin-bottom: 2px;">
                            {{ __('رصيد المحفظة الحالي') }}
                        </span>
                        <span style="font-size: 15px; font-weight: 800; color: #a7f3d0;">
                            {{ number_format($newWalletBalance, 2) }} {{ $currency }}
                        </span>
                    </div>
                </td>
                @endif
            </tr>
        </table>
        <div style="border-top: 1px dashed rgba(255, 255, 255, 0.2); margin-top: 14px; padding-top: 10px; font-size: 11px; color: #cbd5e1;">
            ✓ {{ __('الرصيد متاح في حسابك فوراً ويمكنك استخدامه في أي حجز طيران أو فندق قادم.') }}
        </div>
    </div>

    <!-- Booking Reference Bar -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; margin-bottom: 24px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-size: 13px;">
            <tr>
                <td style="vertical-align: middle;">
                    <span style="color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 2px;">
                        {{ $bookingType === 'hotel' ? __('رقم حجز الفندق') : __('رقم مرجع الحجز / PNR') }}
                    </span>
                    <strong style="color: #041741; font-size: 16px; font-family: monospace;">
                        {{ $refCode }}
                    </strong>
                </td>
                <td style="text-align: {{ $isRtl ? 'left' : 'right' }}; vertical-align: middle;">
                    <span style="background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 20px; display: inline-block;">
                        ✕ {{ __('ملغى ومسترد') }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Financial Breakdown Table -->
    <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 24px;">
        <div style="background-color: #f1f5f9; padding: 12px 18px; font-weight: 800; font-size: 13px; color: #334155; border-bottom: 1px solid #e2e8f0;">
            📊 {{ __('البيان المالي للعملية') }} ({{ __('Financial Summary') }})
        </div>
        <div style="padding: 16px 18px; background-color: #ffffff;">
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-size: 13px;">
                <tr>
                    <td style="padding: 8px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">
                        {{ __('إجمالي المبلغ المدفوع سابقاً') }}:
                    </td>
                    <td style="padding: 8px 0; text-align: {{ $isRtl ? 'left' : 'right' }}; font-weight: 700; color: #1e293b; border-bottom: 1px solid #f1f5f9;">
                        {{ number_format($originalTotal, 2) }} {{ $currency }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">
                        {{ __('رسوم / غرامة الإلغاء') }}:
                    </td>
                    <td style="padding: 8px 0; text-align: {{ $isRtl ? 'left' : 'right' }}; font-weight: 700; color: {{ $penalty > 0 ? '#dc2626' : '#16a34a' }}; border-bottom: 1px solid #f1f5f9;">
                        @if($penalty > 0)
                            -{{ number_format($penalty, 2) }} {{ $currency }}
                        @else
                            {{ __('إلغاء مجاني بدون رسوم') }} (0.00 {{ $currency }})
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0 4px 0; color: #041741; font-size: 14px; font-weight: 800;">
                        {{ __('صافي المبلغ المسترد للمحفظة') }}:
                    </td>
                    <td style="padding: 12px 0 4px 0; text-align: {{ $isRtl ? 'left' : 'right' }}; font-size: 16px; font-weight: 900; color: #059669;">
                        {{ number_format($refundAmount, 2) }} {{ $currency }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Cancelled Booking Details Box -->
    <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 24px;">
        <div style="background-color: #041741; color: #ffffff; padding: 12px 18px; font-weight: 800; font-size: 13px;">
            @if($bookingType === 'hotel')
                🏨 {{ __('تفاصيل حجز الفندق الملغى') }}
            @else
                ✈️ {{ __('تفاصيل حجز الطيران الملغى') }}
            @endif
        </div>
        <div style="padding: 16px 18px; background-color: #ffffff;">
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-size: 13px;">
                @if($bookingType === 'hotel')
                    <tr>
                        <td style="padding: 7px 0; color: #64748b; width: 35%;"><strong>{{ __('الفندق') }}:</strong></td>
                        <td style="padding: 7px 0; color: #0f172a; font-weight: 700;">{{ $booking->hotel_name }}</td>
                    </tr>
                    @if(!empty($booking->city_name))
                    <tr>
                        <td style="padding: 7px 0; color: #64748b;"><strong>{{ __('المدينة / الدولة') }}:</strong></td>
                        <td style="padding: 7px 0; color: #0f172a; font-weight: 700;">{{ $booking->city_name }}, {{ $booking->country_name }}</td>
                    </tr>
                    @endif
                    @if($booking->check_in && $booking->check_out)
                    <tr>
                        <td style="padding: 7px 0; color: #64748b;"><strong>{{ __('تواريخ الإقامة') }}:</strong></td>
                        <td style="padding: 7px 0; color: #0f172a; font-weight: 700;">
                            {{ $booking->check_in->translatedFormat('d M Y') }} ➔ {{ $booking->check_out->translatedFormat('d M Y') }} ({{ $booking->check_in->diffInDays($booking->check_out) }} {{ __('ليالٍ') }})
                        </td>
                    </tr>
                    @endif
                    @if(!empty($booking->room_name))
                    <tr>
                        <td style="padding: 7px 0; color: #64748b;"><strong>{{ __('نوع الغرفة') }}:</strong></td>
                        <td style="padding: 7px 0; color: #0f172a; font-weight: 700;">{{ $booking->room_name }}</td>
                    </tr>
                    @endif
                @else
                    @if($booking->airline_name)
                    <tr>
                        <td style="padding: 7px 0; color: #64748b; width: 35%;"><strong>{{ __('شركة الطيران') }}:</strong></td>
                        <td style="padding: 7px 0; color: #0f172a; font-weight: 700;">{{ $booking->airline_name }}</td>
                    </tr>
                    @endif
                    @if($booking->flightBooking)
                    <tr>
                        <td style="padding: 7px 0; color: #64748b;"><strong>{{ __('مسار الرحلة') }}:</strong></td>
                        <td style="padding: 7px 0; color: #0f172a; font-weight: 700;">
                            {{ $booking->flightBooking->origin }} ➔ {{ $booking->flightBooking->destination }}
                        </td>
                    </tr>
                    @endif
                    @if($booking->departure_date)
                    <tr>
                        <td style="padding: 7px 0; color: #64748b;"><strong>{{ __('تاريخ الإقلاع') }}:</strong></td>
                        <td style="padding: 7px 0; color: #0f172a; font-weight: 700;">
                            {{ \Carbon\Carbon::parse($booking->departure_date)->translatedFormat('l, d M Y') }}
                        </td>
                    </tr>
                    @endif
                    @if($booking->return_date)
                    <tr>
                        <td style="padding: 7px 0; color: #64748b;"><strong>{{ __('تاريخ العودة') }}:</strong></td>
                        <td style="padding: 7px 0; color: #0f172a; font-weight: 700;">
                            {{ \Carbon\Carbon::parse($booking->return_date)->translatedFormat('l, d M Y') }}
                        </td>
                    </tr>
                    @endif
                    @if($booking->passengers && $booking->passengers->count() > 0)
                    <tr>
                        <td style="padding: 7px 0; color: #64748b;"><strong>{{ __('المسافرون') }}:</strong></td>
                        <td style="padding: 7px 0; color: #0f172a; font-weight: 700;">
                            {{ $booking->passengers->pluck('first_name')->implode(', ') }} ({{ $booking->passengers->count() }})
                        </td>
                    </tr>
                    @endif
                @endif
                @if(!empty($reason))
                <tr>
                    <td style="padding: 7px 0; color: #64748b;"><strong>{{ __('سبب الإلغاء') }}:</strong></td>
                    <td style="padding: 7px 0; color: #64748b;">{{ $reason }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Notice / Wallet instructions -->
    <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px;">
        <div style="font-weight: 800; color: #166534; font-size: 13px; margin-bottom: 4px;">
            💡 {{ __('كيف تستفيد من رصيدك في المحفظة؟') }}
        </div>
        <p style="margin: 0; color: #15803d; font-size: 12.5px; line-height: 1.5;">
            {{ __('رصيدك المسترد متاح في محفظتك الإلكترونية ويمكنك استخدامه مباشرة عند إتمام أي حجز طيران أو فندق قادم دون الحاجة لإدخال بطاقتك البنكية.') }}
        </p>
    </div>

    <!-- Action Buttons -->
    <div style="text-align: center; margin-top: 28px; margin-bottom: 10px;">
        <a href="{{ route('customer.wallet.index') }}"
           style="background-color: #041741; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: bold; display: inline-block; font-size: 14px; margin: 0 6px 10px 6px;">
            👛 {{ __('عرض المحفظة والمعاملات') }}
        </a>
        <a href="{{ url('/') }}"
           style="background-color: #f1f5f9; color: #334155 !important; border: 1px solid #cbd5e1; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; display: inline-block; font-size: 14px; margin: 0 6px 10px 6px;">
            🔍 {{ __('حجز رحلة جديدة') }}
        </a>
    </div>

</div>
@endsection
