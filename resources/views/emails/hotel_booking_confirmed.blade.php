@extends('emails.layouts.master')

@section('content')
<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; line-height: 1.6;">

    <!-- Status Header -->
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="display: inline-block; width: 56px; height: 56px; line-height: 56px; border-radius: 50%; background-color: #ecfdf5; color: #10b981; font-size: 28px; font-weight: bold; margin-bottom: 12px;">
            ✓
        </div>
        <h2 style="color: #0f172a; margin: 0 0 6px 0; font-size: 22px; font-weight: 800;">
            {{ __('Booking Confirmed!') }}
        </h2>
        <p style="color: #64748b; margin: 0; font-size: 14px;">
            {{ __('Thank you for booking with us. Your hotel reservation is confirmed.') }}
        </p>
    </div>

    <!-- Booking Reference Badge -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 24px; text-align: center;">
        <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">
            {{ __('Booking Reference') }}
        </span>
        <span style="font-size: 18px; font-weight: 900; color: #1e293b; letter-spacing: 1px;">
            #{{ $booking->reference_num ?? $booking->id }}
        </span>
        @if($booking->supplier_confirmation_num)
            <div style="margin-top: 8px; font-size: 13px; color: #10b981; font-weight: 700;">
                {{ __('Supplier Confirmation') }}: <span>{{ $booking->supplier_confirmation_num }}</span>
            </div>
        @endif
    </div>

    <!-- Hotel Details Box -->
    <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 24px;">
        <div style="background-color: #1e293b; color: #ffffff; padding: 14px 20px; font-weight: 800; font-size: 15px;">
            🏨 {{ $booking->hotel_name }}
        </div>
        <div style="padding: 18px 20px; background-color: #ffffff;">
            @if($booking->city_name || $booking->country_name)
                <p style="margin: 0 0 14px 0; font-size: 13px; color: #64748b;">
                    📍 {{ $booking->city_name }}{{ $booking->country_name ? ', ' . $booking->country_name : '' }}
                </p>
            @endif

            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-size: 13px;">
                <tr>
                    <td style="padding: 8px 0; color: #64748b; border-bottom: 1px solid #f1f5f9; width: 40%;">
                        <strong>{{ __('Check-in') }}:</strong>
                    </td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 700; border-bottom: 1px solid #f1f5f9;">
                        {{ $booking->check_in ? \Carbon\Carbon::parse($booking->check_in)->format('D, d M Y') : 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">
                        <strong>{{ __('Check-out') }}:</strong>
                    </td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 700; border-bottom: 1px solid #f1f5f9;">
                        {{ $booking->check_out ? \Carbon\Carbon::parse($booking->check_out)->format('D, d M Y') : 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">
                        <strong>{{ __('Guest Name') }}:</strong>
                    </td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 700; border-bottom: 1px solid #f1f5f9;">
                        {{ $booking->user->name ?? ($booking->user->full_name ?? ($booking->contact_name ?? 'Guest User')) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">
                        <strong>{{ __('Rooms & Guests') }}:</strong>
                    </td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 700; border-bottom: 1px solid #f1f5f9;">
                        {{ $booking->rooms ?? 1 }} {{ __('Room(s)') }} · {{ $booking->adults ?? 1 }} {{ __('Adult(s)') }}
                        @if(($booking->childs ?? 0) > 0)
                            · {{ $booking->childs }} {{ __('Child(ren)') }}
                        @endif
                    </td>
                </tr>
                @if($booking->room_type)
                <tr>
                    <td style="padding: 8px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">
                        <strong>{{ __('Room Type') }}:</strong>
                    </td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 700; border-bottom: 1px solid #f1f5f9;">
                        {{ $booking->room_type }}
                    </td>
                </tr>
                @endif
                @if($booking->board_type)
                <tr>
                    <td style="padding: 8px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">
                        <strong>{{ __('Meal Plan') }}:</strong>
                    </td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 700; border-bottom: 1px solid #f1f5f9;">
                        {{ $booking->board_type }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 10px 0; color: #64748b; font-size: 14px;">
                        <strong>{{ __('Total Paid') }}:</strong>
                    </td>
                    <td style="padding: 10px 0; color: #10b981; font-weight: 900; font-size: 16px;">
                        {{ number_format((float)($booking->total_price ?? ($booking->total_amount ?? 0)), 2) }} {{ $booking->currency ?? 'SAR' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Voucher Attachment Note -->
    <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 6px; padding: 14px 16px; margin-bottom: 24px;">
        <div style="font-weight: 800; color: #1e40af; font-size: 14px; margin-bottom: 4px;">
            📄 {{ __('Official Hotel Voucher Attached') }}
        </div>
        <p style="margin: 0; color: #3b82f6; font-size: 13px; line-height: 1.5;">
            {{ __('Your official Hotel Stay Voucher has been generated and attached to this email as a PDF document. Please present this voucher or provide the reference number upon check-in at the hotel.') }}
        </p>
    </div>

    <!-- View Button -->
    <div style="text-align: center; margin-top: 28px;">
        <a href="{{ route('customer.bookings.show', ['id' => $booking->id, 'type' => 'hotel']) }}"
           class="btn"
           style="background-color: #2563eb; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: bold; display: inline-block; font-size: 14px;">
            {{ __('View Booking Online') }}
        </a>
    </div>

</div>
@endsection
