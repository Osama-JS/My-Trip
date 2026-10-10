@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'فاتورة حجز فندقي' : 'Hotel Booking Voucher / Invoice')

@section('content')
<div class="container-fluid p-0">

    <!-- Screen Navigation Header -->
    <div class="d-print-none" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.bookings.hotels.show', $hotel->id) }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع لتفاصيل الحجز' : 'Back to Booking' }}">
                <i class="fa-solid fa-arrow-right rtl:rotate-0"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ app()->getLocale() == 'ar' ? 'سند وفاتورة حجز الفندق' : 'Hotel Booking Voucher / Invoice' }}
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ app()->getLocale() == 'ar' ? 'رقم السند المرجعي:' : 'Voucher Reference:' }} #{{ $hotel->reference_num ?? $hotel->id }}
                </span>
            </div>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.bookings.hotels.show', $hotel->id) }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-eye me-1"></i> {{ app()->getLocale() == 'ar' ? 'عرض الحجز' : 'View Details' }}
            </a>
            <button type="button" class="btn btn-primary" onclick="window.print()" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-print me-1"></i> {{ app()->getLocale() == 'ar' ? 'طباعة الفاتورة / القسيمة' : 'Print Voucher' }}
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
                    {{ app()->getLocale() == 'ar' ? 'قسيمة حجز فندقي إلكترونية' : 'Electronic Hotel Voucher' }}
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
                        <i class="fa-solid fa-user me-1 text-primary"></i> {{ app()->getLocale() == 'ar' ? 'بيانات العميل' : 'Customer Details' }}
                    </h6>
                    <div style="font-size: 0.85rem; line-height: 1.7;">
                        <div><strong>{{ app()->getLocale() == 'ar' ? 'الاسم:' : 'Name:' }}</strong> {{ $hotel->user->full_name ?? ($hotel->user->name ?? (app()->getLocale() == 'ar' ? 'ضيف' : 'Guest')) }}</div>
                        <div><strong>{{ app()->getLocale() == 'ar' ? 'البريد:' : 'Email:' }}</strong> {{ $hotel->user->email ?? 'N/A' }}</div>
                        <div><strong>{{ app()->getLocale() == 'ar' ? 'الهاتف:' : 'Phone:' }}</strong> {{ $hotel->user->phone ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="p-3 rounded h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <h6 style="font-weight: 800; color: #041741; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.4rem; margin-bottom: 0.75rem;">
                        <i class="fa-solid fa-ticket me-1 text-primary"></i> {{ app()->getLocale() == 'ar' ? 'بيانات الحجز' : 'Booking Details' }}
                    </h6>
                    <div style="font-size: 0.85rem; line-height: 1.7;">
                        <div><strong>{{ app()->getLocale() == 'ar' ? 'الرقم المرجعي:' : 'System Ref:' }}</strong> <span style="color: #041741; font-weight: 800;">{{ $hotel->reference_num ?? $hotel->id }}</span></div>
                        <div><strong>{{ app()->getLocale() == 'ar' ? 'مرجع المزود:' : 'Supplier Ref:' }}</strong> {{ $hotel->supplier_confirmation_num ?? 'Pending' }}</div>
                        <div><strong>{{ app()->getLocale() == 'ar' ? 'الحالة:' : 'Status:' }}</strong> <span class="badge bg-success">{{ strtoupper($hotel->status) }}</span></div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="p-3 rounded h-100 text-center d-flex flex-column justify-content-center" style="background: rgba(59,130,246,0.06); border: 1px solid rgba(59,130,246,0.2);">
                    <small style="color: #64748b; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'المبلغ الإجمالي المدفوع' : 'Total Amount Paid' }}</small>
                    <div style="font-size: 1.75rem; font-weight: 900; color: #041741; margin-top: 0.25rem;">
                        {{ number_format($hotel->total_price, 2) }} <small style="font-size: 0.9rem;">{{ $hotel->currency }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stay Details Table -->
        <h6 style="font-weight: 800; color: #041741; margin-bottom: 0.75rem;">
            <i class="fa-solid fa-hotel me-1 text-primary"></i> {{ app()->getLocale() == 'ar' ? 'تفاصيل الإقامة والفندق' : 'Stay Details' }}
        </h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle mb-0" style="font-size: 0.85rem; border-color: #e2e8f0;">
                <thead style="background: #041741; color: #ffffff;">
                    <tr>
                        <th>{{ app()->getLocale() == 'ar' ? 'الفندق والوجهة' : 'Property' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'تسجيل الوصول' : 'Check In' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'تسجيل المغادرة' : 'Check Out' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الغرف والنزلاء' : 'Rooms / Guests' }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong style="color: #041741; font-size: 0.95rem;">{{ $hotel->hotel_name }}</strong><br>
                            <small class="text-muted">{{ $hotel->city_name }}, {{ $hotel->country_name }}</small>
                        </td>
                        <td>{{ $hotel->check_in ? $hotel->check_in->format('D, d M Y') : 'N/A' }}</td>
                        <td>{{ $hotel->check_out ? $hotel->check_out->format('D, d M Y') : 'N/A' }}</td>
                        <td>
                            <strong>{{ $hotel->rooms }} {{ app()->getLocale() == 'ar' ? 'غرفة' : 'Rooms' }}</strong><br>
                            <small class="text-muted">{{ $hotel->adults }} {{ app()->getLocale() == 'ar' ? 'بالغين' : 'Adults' }}, {{ $hotel->childs }} {{ app()->getLocale() == 'ar' ? 'أطفال' : 'Children' }}</small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Guest Roster Table -->
        <h6 style="font-weight: 800; color: #041741; margin-bottom: 0.75rem;">
            <i class="fa-solid fa-users me-1 text-primary"></i> {{ app()->getLocale() == 'ar' ? 'قائمة النزلاء' : 'Guest Roster' }}
        </h6>
        <div class="table-responsive mb-5">
            <table class="table table-bordered align-middle mb-0" style="font-size: 0.85rem; border-color: #e2e8f0;">
                <thead style="background: #f8fafc; color: #475569;">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'اسم النزيل' : 'Guest Name' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الفئة' : 'Type' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hotel->passengers ?? [] as $guest)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong style="color: #041741;">{{ $guest->title }} {{ $guest->first_name }} {{ $guest->last_name }}</strong></td>
                            <td><span class="badge bg-light text-dark border">{{ ucfirst($guest->passenger_type) }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-3 text-muted">{{ app()->getLocale() == 'ar' ? 'لم يتم تحديد بيانات النزلاء' : 'Guest details not specified' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Notice -->
        <div class="border-top pt-4 text-center text-muted" style="font-size: 0.8rem;">
            {{ app()->getLocale() == 'ar' ? 'شكراً لاختيارك الحجز عبر' : 'Thank you for booking with' }} <strong>{{ config('app.name', 'MyTrip') }}</strong>. 
            {{ app()->getLocale() == 'ar' ? 'هذه القسيمة صادرة إلكترونياً ولا تحتاج إلى توقيع خطي.' : 'This is an electronically generated voucher.' }}
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
