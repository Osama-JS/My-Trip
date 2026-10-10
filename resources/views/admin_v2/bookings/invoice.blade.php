@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'فاتورة حجز طيران' : 'Flight Booking Invoice')

@section('content')
<div class="container-fluid p-0">

    <!-- Screen Navigation Header -->
    <div class="d-print-none" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع لتفاصيل الحجز' : 'Back to Booking' }}">
                <i class="fa-solid fa-arrow-right rtl:rotate-0"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ app()->getLocale() == 'ar' ? 'سند وفاتورة حجز الطيران' : 'Flight Booking Invoice / Ticket' }}
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ app()->getLocale() == 'ar' ? 'رقم السجل PNR / المرجع:' : 'PNR / Booking Reference:' }} #{{ $booking->booking_reference }}
                </span>
            </div>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-eye me-1"></i> {{ app()->getLocale() == 'ar' ? 'عرض تفاصيل الحجز' : 'View Booking' }}
            </a>
            <button type="button" class="btn btn-primary" onclick="window.print()" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-print me-1"></i> {{ app()->getLocale() == 'ar' ? 'طباعة الفاتورة' : 'Print Invoice' }}
            </button>
        </div>
    </div>

    <!-- Printable Invoice Container -->
    <div class="admin-card p-5 mb-5" id="invoiceArea" style="max-width: 950px; margin: 0 auto; background: #fff; color: #1e293b;">
        <!-- Header Banner -->
        <div class="d-flex justify-content-between align-items-center border-bottom pb-4 mb-4">
            <div>
                <h2 style="font-weight: 900; color: #041741; margin-bottom: 0.25rem;">{{ config('app.name', 'MyTrip') }}</h2>
                <span class="badge bg-primary text-white" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">
                    {{ app()->getLocale() == 'ar' ? 'فاتورة تذكرة طيران إلكترونية' : 'Flight E-Ticket & Invoice' }}
                </span>
            </div>
            <div class="text-end">
                <div style="font-size: 0.85rem; color: #64748b;">{{ app()->getLocale() == 'ar' ? 'تاريخ الإصدار:' : 'Date Issued:' }}</div>
                <strong style="color: #041741; font-size: 1rem;">{{ date('d M Y') }}</strong>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="row g-4 mb-4">
            <div class="col-sm-4">
                <div class="p-3 rounded h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <h6 style="font-weight: 800; color: #041741; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.4rem; margin-bottom: 0.75rem;">
                        <i class="fa-solid fa-user me-1 text-primary"></i> {{ app()->getLocale() == 'ar' ? 'بيانات المسافر الرئيسي' : 'Customer Details' }}
                    </h6>
                    <div style="font-size: 0.85rem; line-height: 1.7;">
                        <div><strong>{{ app()->getLocale() == 'ar' ? 'الاسم:' : 'Name:' }}</strong> {{ $booking->user->full_name ?? ($booking->user->name ?? (app()->getLocale() == 'ar' ? 'ضيف' : 'Guest')) }}</div>
                        <div><strong>{{ app()->getLocale() == 'ar' ? 'البريد:' : 'Email:' }}</strong> {{ $booking->contact_email }}</div>
                        <div><strong>{{ app()->getLocale() == 'ar' ? 'الهاتف:' : 'Phone:' }}</strong> {{ $booking->contact_phone }}</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="p-3 rounded h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <h6 style="font-weight: 800; color: #041741; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.4rem; margin-bottom: 0.75rem;">
                        <i class="fa-solid fa-plane-departure me-1 text-primary"></i> {{ app()->getLocale() == 'ar' ? 'معلومات الحجز' : 'Booking Details' }}
                    </h6>
                    <div style="font-size: 0.85rem; line-height: 1.7;">
                        <div><strong>{{ app()->getLocale() == 'ar' ? 'رمز الحجز (PNR):' : 'PNR / Ref:' }}</strong> <span style="color: #041741; font-weight: 800;">{{ $booking->booking_reference }}</span></div>
                        <div><strong>{{ app()->getLocale() == 'ar' ? 'حالة الحجز:' : 'Status:' }}</strong> <span class="badge bg-success">{{ strtoupper($booking->status) }}</span></div>
                        <div><strong>{{ app()->getLocale() == 'ar' ? 'تاريخ الإنشاء:' : 'Date Issued:' }}</strong> {{ $booking->pnr_created_at ? \Carbon\Carbon::parse($booking->pnr_created_at)->format('d M Y, h:i A') : 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="p-3 rounded h-100 text-center d-flex flex-column justify-content-center" style="background: rgba(59,130,246,0.06); border: 1px solid rgba(59,130,246,0.2);">
                    <small style="color: #64748b; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'المبلغ الإجمالي المدفوع' : 'Total Amount Paid' }}</small>
                    <div style="font-size: 1.75rem; font-weight: 900; color: #041741; margin-top: 0.25rem;">
                        {{ number_format($booking->total_amount, 2) }} <small style="font-size: 0.9rem;">{{ $booking->currency }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flight Details Table -->
        <h6 style="font-weight: 800; color: #041741; margin-bottom: 0.75rem;">
            <i class="fa-solid fa-plane me-1 text-primary"></i> {{ app()->getLocale() == 'ar' ? 'تفاصيل رحلة الطيران' : 'Flight Details' }}
        </h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle mb-0" style="font-size: 0.85rem; border-color: #e2e8f0;">
                <thead style="background: #041741; color: #ffffff;">
                    <tr>
                        <th>{{ app()->getLocale() == 'ar' ? 'خط السير' : 'Route' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الخطوط الجوية' : 'Airline' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'تاريخ ووقت الإقلاع' : 'Departure' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'درجة السفر' : 'Flight Class' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if($booking->flightBooking)
                        <tr>
                            <td>
                                <strong>{{ $booking->flightBooking->origin }}</strong> 
                                <i class="fa-solid fa-arrow-right mx-2 text-primary"></i> 
                                <strong>{{ $booking->flightBooking->destination }}</strong>
                            </td>
                            @php
                                $airlineName = $booking->airline_name ?? $booking->flightBooking->airline_name ?? null;
                                if (!$airlineName && !empty($booking->flightBooking->itinerary_data)) {
                                    $itin = is_string($booking->flightBooking->itinerary_data) ? json_decode($booking->flightBooking->itinerary_data, true) : $booking->flightBooking->itinerary_data;
                                    $airlineName = $itin['Itineraries']['Itinerary'][0]['ValidatingAirlineCode'] ?? null;
                                    if (!$airlineName && isset($itin[0]['airportOriginCode'])) {
                                        $airlineName = $itin[0]['airportOriginCode'] . ' - ' . ($itin[0]['airportDestinationCode'] ?? '');
                                    }
                                }
                            @endphp
                            <td><strong style="color: #041741;">{{ $airlineName ?? 'N/A' }}</strong></td>
                            <td>{{ $booking->flightBooking->departure_date ? \Carbon\Carbon::parse($booking->flightBooking->departure_date)->format('d M Y H:i') : '' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $booking->flightBooking->flight_class }}</span></td>
                        </tr>
                    @else
                        <tr><td colspan="4" class="text-center py-3 text-muted">{{ app()->getLocale() == 'ar' ? 'تفاصيل الرحلة غير متوفرة' : 'Flight details not found' }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Passenger Roster Table -->
        <h6 style="font-weight: 800; color: #041741; margin-bottom: 0.75rem;">
            <i class="fa-solid fa-users me-1 text-primary"></i> {{ app()->getLocale() == 'ar' ? 'قائمة المسافرين والتذاكر' : 'Passenger Roster & Tickets' }}
        </h6>
        <div class="table-responsive mb-5">
            <table class="table table-bordered align-middle mb-0" style="font-size: 0.85rem; border-color: #e2e8f0;">
                <thead style="background: #f8fafc; color: #475569;">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'اسم المسافر' : 'Passenger Name' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الفئة' : 'Type' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'تاريخ الميلاد' : 'DOB' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'بيانات الجواز / الهوية' : 'Document Info' }}</th>
                        @if($booking->ticket_status === 'ticketed')
                            <th>{{ app()->getLocale() == 'ar' ? 'رقم التذكرة الإلكترونية' : 'Ticket No' }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($booking->passengers as $index => $pax)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong style="color: #041741;">{{ $pax->title }} {{ $pax->first_name }} {{ $pax->last_name }}</strong></td>
                            <td><span class="badge bg-light text-dark border">{{ ucfirst($pax->passenger_type ?? $pax->type ?? 'N/A') }}</span></td>
                            <td>{{ $pax->dob ? \Carbon\Carbon::parse($pax->dob)->format('d M Y') : 'N/A' }}</td>
                            <td>
                                {{ $pax->passport_number ?? $pax->passport_no ?? 'N/A' }} 
                                <span class="text-muted small">{{ $pax->nationality ? '('.$pax->nationality.')' : '' }}</span>
                            </td>
                            @php
                                $ticketNumber = $pax->e_ticket_no ?? $pax->ticket_number ?? (is_array($booking->ticket_numbers) && isset($booking->ticket_numbers[$index]) ? $booking->ticket_numbers[$index] : null);
                            @endphp
                            @if(in_array($booking->ticket_status, ['ticketed', 'booked', 'confirmed']) || $ticketNumber)
                                <td><strong class="text-success">{{ $ticketNumber ?? 'N/A' }}</strong></td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ in_array($booking->ticket_status, ['ticketed', 'booked', 'confirmed']) ? '6' : '5' }}" class="text-center py-3 text-muted">
                                {{ app()->getLocale() == 'ar' ? 'لا يوجد مسافرون مسجلون' : 'No passengers recorded' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Notice -->
        <div class="border-top pt-4 text-center text-muted" style="font-size: 0.8rem;">
            {{ app()->getLocale() == 'ar' ? 'شكراً لاختيارك الحجز عبر' : 'Thank you for booking with' }} <strong>{{ config('app.name', 'MyTrip') }}</strong>. 
            {{ app()->getLocale() == 'ar' ? 'هذه الفاتورة صادرة إلكترونياً ولا تحتاج إلى توقيع خطي.' : 'This is an electronically generated invoice.' }}
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden !important; }
        #invoiceArea, #invoiceArea * { visibility: visible !important; }
        #invoiceArea {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
        }
        .d-print-none, .admin-sidebar, .admin-topbar, .admin-footer { display: none !important; }
    }
</style>
@endsection
