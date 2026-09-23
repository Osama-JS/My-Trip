@extends('emails.layouts.master')

@section('content')
<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; line-height: 1.6;">

    <!-- Status Header -->
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="display: inline-block; width: 56px; height: 56px; line-height: 56px; border-radius: 50%; background-color: #ecfdf5; color: #10b981; font-size: 28px; font-weight: bold; margin-bottom: 12px;">
            ✓
        </div>
        <h2 style="color: #0f172a; margin: 0 0 6px 0; font-size: 22px; font-weight: 800;">
            {{ __('Flight Booking Confirmed!') }}
        </h2>
        <p style="color: #64748b; margin: 0; font-size: 14px;">
            {{ __('Your electronic flight tickets have been issued successfully.') }}
        </p>
    </div>

    <!-- Booking Reference & PNR Box -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 24px; text-align: center;">
        <div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 12px;">
            @if($booking->pnr_code)
            <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px;">
                    {{ __('Airline PNR') }}
                </span>
                <span style="font-size: 18px; font-weight: 900; color: #2563eb; letter-spacing: 1px;">
                    {{ $booking->pnr_code }}
                </span>
            </div>
            @endif
            <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px;">
                    {{ __('Booking Reference') }}
                </span>
                <span style="font-size: 18px; font-weight: 900; color: #0f172a; letter-spacing: 0.5px;">
                    #{{ $booking->booking_reference ?: $booking->id }}
                </span>
            </div>
        </div>
    </div>

    <!-- Flight Route Box -->
    <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 24px;">
        <div style="background-color: #0f172a; color: #ffffff; padding: 14px 20px; font-weight: 800; font-size: 15px;">
            ✈️ {{ $booking->airline_name ?: __('Flight Itinerary') }}
        </div>
        <div style="padding: 18px 20px; background-color: #ffffff;">
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-size: 13px;">
                @if($booking->flightBooking)
                <tr>
                    <td style="padding: 8px 0; color: #64748b; border-bottom: 1px solid #f1f5f9; width: 40%;">
                        <strong>{{ __('Route') }}:</strong>
                    </td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 700; border-bottom: 1px solid #f1f5f9;">
                        {{ $booking->flightBooking->origin }} ➔ {{ $booking->flightBooking->destination }}
                    </td>
                </tr>
                @endif
                @if($booking->departure_date)
                <tr>
                    <td style="padding: 8px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">
                        <strong>{{ __('Departure Date') }}:</strong>
                    </td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 700; border-bottom: 1px solid #f1f5f9;">
                        {{ \Carbon\Carbon::parse($booking->departure_date)->format('D, d M Y') }}
                    </td>
                </tr>
                @endif
                @if($booking->return_date)
                <tr>
                    <td style="padding: 8px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">
                        <strong>{{ __('Return Date') }}:</strong>
                    </td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 700; border-bottom: 1px solid #f1f5f9;">
                        {{ \Carbon\Carbon::parse($booking->return_date)->format('D, d M Y') }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 10px 0; color: #64748b; font-size: 14px;">
                        <strong>{{ __('Total Paid') }}:</strong>
                    </td>
                    <td style="padding: 10px 0; color: #10b981; font-weight: 900; font-size: 16px;">
                        {{ number_format((float)($booking->total_amount ?? 0), 2) }} {{ $booking->currency ?? 'SAR' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Passengers & E-Tickets List -->
    @if($booking->passengers && $booking->passengers->count() > 0)
    <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 24px;">
        <div style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 12px 16px; font-weight: 700; font-size: 14px; color: #334155; display: flex; justify-content: space-between; align-items: center;">
            <span>👥 {{ __('Travelers & E-Tickets') }} ({{ $booking->passengers->count() }})</span>
        </div>
        <div style="padding: 0; background-color: #ffffff; overflow-x: auto;">
            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse; font-size: 13px; text-align: {{ app()->isLocale('ar') ? 'right' : 'left' }};">
                <thead>
                    <tr style="background-color: #f1f5f9; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; width: 5%;">#</th>
                        <th style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; width: 35%;">{{ __('Passenger Name') }}</th>
                        <th style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; width: 15%;">{{ __('Type') }}</th>
                        <th style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; width: 20%;">{{ __('Passport') }}</th>
                        <th style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; width: 25%;">{{ __('E-Ticket No') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($booking->passengers as $index => $p)
                    @php
                        $ticketNo = $p->e_ticket_no 
                                 ?: ($booking->ticket_numbers[$index] 
                                 ?? ($booking->ticket_numbers[0] ?? null));
                    @endphp
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px 14px; color: #64748b; font-weight: 700;">
                            {{ $loop->iteration }}
                        </td>
                        <td style="padding: 12px 14px; font-weight: 800; color: #0f172a;">
                            {{ $p->title ? $p->title . ' ' : '' }}{{ $p->first_name }} {{ $p->last_name }}
                        </td>
                        <td style="padding: 12px 14px;">
                            <span style="font-size: 11px; background: #e2e8f0; color: #334155; padding: 3px 8px; border-radius: 4px; font-weight: 700; text-transform: uppercase;">
                                {{ __($p->passenger_type ?: 'Adult') }}
                            </span>
                        </td>
                        <td style="padding: 12px 14px; color: #475569; font-weight: 600;">
                            {{ $p->passport_number ?: '-' }}
                        </td>
                        <td style="padding: 12px 14px;">
                            @if($ticketNo)
                                <span style="display: inline-block; background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: 6px; font-family: monospace; font-size: 13px; font-weight: 800; letter-spacing: 0.5px;">
                                    🎫 {{ $ticketNo }}
                                </span>
                            @else
                                <span style="color: #64748b; font-size: 12px; font-style: italic;">
                                    {{ $booking->pnr_code ? __('Confirmed') . ' (' . $booking->pnr_code . ')' : __('Confirmed') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- E-Ticket PDF Attachment Note -->
    <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 6px; padding: 14px 16px; margin-bottom: 24px;">
        <div style="font-weight: 800; color: #1e40af; font-size: 14px; margin-bottom: 4px;">
            📄 {{ __('Official E-Ticket / Voucher Attached') }}
        </div>
        <p style="margin: 0; color: #3b82f6; font-size: 13px; line-height: 1.5;">
            {{ __('Your official Electronic Flight Ticket / Voucher is attached to this email as a PDF document. Please print or present this document along with your passport at the airport check-in counter.') }}
        </p>
    </div>

    <!-- View Button -->
    <div style="text-align: center; margin-top: 28px;">
        <a href="{{ route('customer.bookings.show', ['id' => $booking->id, 'type' => 'flight']) }}"
           class="btn"
           style="background-color: #2563eb; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: bold; display: inline-block; font-size: 14px;">
            {{ __('View Flight Booking Online') }}
        </a>
    </div>

</div>
@endsection
