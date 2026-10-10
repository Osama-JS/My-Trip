@extends('admin_v2.layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'تفاصيل حجز الطيران #' : 'Flight Booking #') . ($booking->booking_reference ?? $booking->id))

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.bookings.flights.index') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع للقائمة' : 'Back' }}">
                <i class="fa-solid fa-arrow-right"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ app()->getLocale() == 'ar' ? 'تفاصيل حجز الطيران' : 'Flight Booking Details' }} <span style="color: var(--primary);">#{{ $booking->booking_reference }}</span>
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ app()->getLocale() == 'ar' ? 'تاريخ الحجز:' : 'Booked on:' }} {{ optional($booking->created_at)->format('Y-m-d H:i') ?? 'N/A' }}
                </span>
            </div>
        </div>

        <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
            <a href="{{ route('admin.bookings.invoice', $booking->id) }}" target="_blank" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-file-invoice me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحميل الفاتورة PDF' : 'Invoice PDF' }}
            </a>
            <button type="button" class="btn btn-outline-secondary" onclick="window.print();" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-print me-1"></i> {{ app()->getLocale() == 'ar' ? 'طباعة' : 'Print' }}
            </button>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;" class="charts-grid">
        <!-- Left Column: Flight Route, Passenger Details & Segments -->
        <div>
            <!-- Status & PNR Overview Card -->
            <div class="admin-card">
                <div class="card-header-flex">
                    <div class="card-title">
                        <i class="fa-solid fa-plane-departure text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'بيانات التذكرة ومزود الطيران' : 'Airline & Ticket Info' }}
                    </div>
                    <div>
                        @if(in_array(strtolower($booking->status), ['confirmed', 'ticketed', 'completed']))
                            <span class="badge-v2 badge-success"><i class="fa-solid fa-check"></i> {{ $booking->status }}</span>
                        @elseif(in_array(strtolower($booking->status), ['pending', 'processing']))
                            <span class="badge-v2 badge-warning"><i class="fa-solid fa-clock"></i> {{ $booking->status }}</span>
                        @else
                            <span class="badge-v2 badge-danger"><i class="fa-solid fa-xmark"></i> {{ $booking->status }}</span>
                        @endif
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1.25rem; background: var(--bg-input); padding: 1.25rem; border-radius: var(--radius-md);">
                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'رقم PNR العميل' : 'Passenger PNR' }}</span>
                        <strong style="font-size: 1.15rem; font-family: monospace; color: var(--primary);">{{ $booking->pnr_code ?? $booking->pnr ?? '---' }}</strong>
                    </div>

                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'شركة الطيران' : 'Airline' }}</span>
                        <strong style="font-size: 1rem; color: var(--text-main);">{{ $booking->airline_name ?? '---' }} ({{ $booking->airline_code ?? '' }})</strong>
                    </div>

                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'حالة التذكرة' : 'Ticketing' }}</span>
                        <strong style="font-size: 1rem; color: var(--text-main);">{{ ucfirst($booking->ticket_status ?? 'Pending') }}</strong>
                    </div>

                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'المهلة الزمنية' : 'Ticketing Limit' }}</span>
                        <strong style="font-size: 0.9rem; color: var(--warning);">{{ $booking->ticketing_time_limit ? $booking->ticketing_time_limit->format('Y-m-d H:i') : '---' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Passengers List Card -->
            <div class="admin-card">
                <div class="card-header-flex">
                    <div class="card-title">
                        <i class="fa-solid fa-users text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'بيانات المسافرين والتذاكر' : 'Passengers & Tickets' }}
                    </div>
                    <span class="badge-v2 badge-primary">{{ count($booking->passengers) }} {{ app()->getLocale() == 'ar' ? 'مسافر' : 'Pax' }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table-v2">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() == 'ar' ? 'اسم المسافر' : 'Passenger Name' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'النوع' : 'Type' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'رقم الوثيقة / الجواز' : 'Passport / ID' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'الجنسية' : 'Nationality' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'رقم التذكرة الإلكترونية' : 'eTicket Number' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->passengers as $pax)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-main);">
                                        <i class="fa-solid fa-user-circle text-muted me-1"></i> {{ $pax->title }} {{ $pax->first_name }} {{ $pax->last_name }}
                                    </div>
                                    <small style="color: var(--text-muted);">{{ $pax->dob ? \Carbon\Carbon::parse($pax->dob)->format('Y-m-d') : '' }}</small>
                                </td>
                                <td>
                                    <span class="badge-v2 badge-info">{{ ucfirst($pax->passenger_type ?? $pax->type ?? 'Adult') }}</span>
                                </td>
                                <td>
                                    <span style="font-family: monospace; font-weight: 600;">{{ $pax->passport_number ?? $pax->passport_no ?? '---' }}</span>
                                    @if($pax->passport_expiry)
                                        <small style="display: block; color: var(--text-muted);">Exp: {{ \Carbon\Carbon::parse($pax->passport_expiry)->format('Y-m-d') }}</small>
                                    @endif
                                </td>
                                <td>{{ $pax->nationality ?? '---' }}</td>
                                <td>
                                    @if($pax->e_ticket_no)
                                        <span class="badge-v2 badge-success" style="font-family: monospace; font-size: 0.85rem;">{{ $pax->e_ticket_no }}</span>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.85rem;">---</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                                    {{ app()->getLocale() == 'ar' ? 'لا توجد بيانات مسافرين مسجلة' : 'No passengers recorded' }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Customer Info & Financial Breakdown -->
        <div>
            <!-- Customer Contact Card -->
            <div class="admin-card">
                <div class="card-header-flex">
                    <div class="card-title">
                        <i class="fa-solid fa-address-card text-info me-2"></i> {{ app()->getLocale() == 'ar' ? 'بيانات العميل / جهة الاتصال' : 'Customer Details' }}
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <div class="user-avatar" style="width: 44px; height: 44px; font-size: 1.1rem;">
                        {{ strtoupper(substr($booking->user->first_name ?? 'G', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight: 800; color: var(--text-main);">{{ $booking->user->full_name ?? $booking->contact_name ?? __('Guest User') }}</div>
                        <small style="color: var(--text-muted);">{{ $booking->user->user_type ?? 'Customer' }}</small>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.875rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-envelope text-muted" style="width: 20px;"></i>
                        <span style="color: var(--text-main);">{{ $booking->contact_email ?? $booking->user->email ?? '---' }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-phone text-muted" style="width: 20px;"></i>
                        <span style="color: var(--text-main); direction: ltr;">{{ $booking->contact_phone ?? $booking->user->phone ?? '---' }}</span>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Card -->
            <div class="admin-card">
                <div class="card-header-flex">
                    <div class="card-title">
                        <i class="fa-solid fa-sack-dollar text-success me-2"></i> {{ app()->getLocale() == 'ar' ? 'التفاصيل المالية والأرباح' : 'Financial Breakdown' }}
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'سعر المزود (Provider Price):' : 'Provider Price:' }}</span>
                        <strong style="color: var(--text-main);">{{ number_format($booking->provider_price ?? 0, 2) }} {{ $booking->currency }}</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'هامش ربح المنصة (Profit):' : 'Platform Profit:' }}</span>
                        <strong style="color: var(--success);">+{{ number_format($booking->platform_profit ?? 0, 2) }} {{ $booking->currency }}</strong>
                    </div>

                    @if($booking->insurance_amount > 0)
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'التأمين السياحي:' : 'Travel Insurance:' }}</span>
                        <strong style="color: var(--info);">+{{ number_format($booking->insurance_amount, 2) }} {{ $booking->currency }}</strong>
                    </div>
                    @endif

                    <hr style="border-color: var(--border-light); margin: 0.5rem 0;">

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 800; color: var(--text-main); font-size: 1rem;">{{ app()->getLocale() == 'ar' ? 'المبلغ الإجمالي المحصل:' : 'Total Amount:' }}</span>
                        <strong style="font-size: 1.35rem; color: var(--primary);">{{ number_format($booking->total_amount, 2) }} <small style="font-size: 0.85rem;">{{ $booking->currency }}</small></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
