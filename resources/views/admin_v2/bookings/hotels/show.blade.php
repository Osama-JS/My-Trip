@extends('admin_v2.layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'تفاصيل حجز الفندق #' : 'Hotel Booking #') . ($hotelBooking->reference_num ?? $hotelBooking->id))

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.bookings.hotels.index') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع لقائمة الحجوزات' : 'Back to List' }}">
                <i class="fa-solid fa-arrow-right rtl:rotate-0"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ app()->getLocale() == 'ar' ? 'تفاصيل حجز الفندق' : 'Hotel Booking Details' }} 
                    <span style="color: var(--primary);">#{{ $hotelBooking->reference_num ?? $hotelBooking->id }}</span>
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    <i class="fa-solid fa-hotel text-primary me-1"></i> {{ $hotelBooking->hotel_name ?? __('Hotel Booking') }} &bull; 
                    <i class="fa-solid fa-location-dot text-danger me-1 ms-1"></i> {{ $hotelBooking->city_name ?? '' }}{{ $hotelBooking->city_name && $hotelBooking->country_name ? ', ' : '' }}{{ $hotelBooking->country_name ?? '' }}
                </span>
            </div>
        </div>

        <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
            @if($hotelBooking->status === 'confirmed')
                <a href="{{ route('admin.bookings.hotels.invoice', $hotelBooking->id) }}" target="_blank" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                    <i class="fa-solid fa-file-invoice me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحميل الفاتورة PDF' : 'Download Invoice' }}
                </a>
            @endif
            <button type="button" class="btn btn-outline-secondary" onclick="window.print();" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-print me-1"></i> {{ app()->getLocale() == 'ar' ? 'طباعة' : 'Print' }}
            </button>
        </div>
    </div>

    <!-- Manual Intervention Alert if needed -->
    @if($hotelBooking->status === 'paid' && empty($hotelBooking->supplier_confirmation_num))
    <div class="admin-card p-4 mb-4" style="border-inline-start: 4px solid var(--warning); background: rgba(245, 158, 11, 0.08);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(245, 158, 11, 0.2); color: var(--warning); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h5 style="font-size: 1rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                        {{ app()->getLocale() == 'ar' ? 'يتطلب تدخل يدوي وتأكيد مع المزود' : 'Manual Intervention Required' }}
                    </h5>
                    <p style="font-size: 0.825rem; color: var(--text-muted); margin: 0;">
                        {{ app()->getLocale() == 'ar' ? 'تم الدفع بنجاح ولكن جلسة الحجز مع المزود انتهت أو تأخر الرد. العميل لم يستلم القسيمة بعد.' : 'Payment succeeded but supplier session timed out. Voucher not issued yet.' }}
                    </p>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-warning" onclick="retrySupplierBooking({{ $hotelBooking->id }})" style="font-weight: 700; border-radius: var(--radius-md);">
                    <i class="fa-solid fa-arrows-rotate me-1"></i> {{ app()->getLocale() == 'ar' ? 'إعادة الإرسال للمزود' : 'Retry API' }}
                </button>
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#forceConfirmModal" style="font-weight: 700; border-radius: var(--radius-md);">
                    <i class="fa-solid fa-check-double me-1"></i> {{ app()->getLocale() == 'ar' ? 'تأكيد إجباري' : 'Force Confirm' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Content 2-Column Grid -->
    <div class="row g-4">
        <!-- Left Main Column (Hotel details, stay dates, guest list) -->
        <div class="col-lg-8">
            <!-- Stay & Hotel Overview Card -->
            <div class="admin-card p-4 mb-4">
                <div class="card-header-flex p-0 pb-3 mb-4" style="border-bottom: 1px solid var(--border-color);">
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--primary);">
                            {{ app()->getLocale() == 'ar' ? 'بيانات الإقامة والفندق' : 'Stay & Hotel Information' }}
                        </span>
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-top: 0.25rem; margin-bottom: 0;">
                            {{ $hotelBooking->hotel_name ?? __('Hotel') }}
                        </h3>
                    </div>
                    <div>
                        @if($hotelBooking->status === 'confirmed')
                            <span class="badge-v2 badge-success"><i class="fa-solid fa-circle-check"></i> {{ __('Confirmed') }}</span>
                        @elseif($hotelBooking->status === 'paid' && empty($hotelBooking->supplier_confirmation_num))
                            <span class="badge-v2 badge-warning"><i class="fa-solid fa-clock"></i> {{ __('Paid - Awaiting Voucher') }}</span>
                        @elseif($hotelBooking->status === 'cancelled')
                            <span class="badge-v2 badge-danger"><i class="fa-solid fa-circle-xmark"></i> {{ __('Cancelled') }}</span>
                        @else
                            <span class="badge-v2 badge-info">{{ ucfirst($hotelBooking->status) }}</span>
                        @endif
                    </div>
                </div>

                <!-- Stay Dates Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="p-3 text-center rounded-3" style="background: var(--bg-input); border: 1px solid var(--border-color);">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(34, 197, 94, 0.1); color: #22c55e; margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">{{ app()->getLocale() == 'ar' ? 'تاريخ الوصول' : 'Check-in' }}</span>
                            <div style="font-size: 1rem; font-weight: 800; color: var(--text-main); margin-top: 0.25rem;">
                                {{ optional($hotelBooking->check_in)->format('Y-m-d') ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="p-3 text-center rounded-3" style="background: var(--bg-input); border: 1px solid var(--border-color);">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(239, 68, 68, 0.1); color: #ef4444; margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-calendar-xmark"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">{{ app()->getLocale() == 'ar' ? 'تاريخ المغادرة' : 'Check-out' }}</span>
                            <div style="font-size: 1rem; font-weight: 800; color: var(--text-main); margin-top: 0.25rem;">
                                {{ optional($hotelBooking->check_out)->format('Y-m-d') ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="p-3 text-center rounded-3" style="background: var(--bg-input); border: 1px solid var(--border-color);">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(59, 130, 246, 0.1); color: var(--primary); margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-bed"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">{{ app()->getLocale() == 'ar' ? 'سعة الحجز' : 'Occupancy' }}</span>
                            <div style="font-size: 1rem; font-weight: 800; color: var(--text-main); margin-top: 0.25rem;">
                                {{ $hotelBooking->rooms }} {{ app()->getLocale() == 'ar' ? 'غرف' : 'Rooms' }}
                            </div>
                            <small style="color: var(--text-muted); font-size: 0.75rem;">
                                ({{ $hotelBooking->adults }} {{ app()->getLocale() == 'ar' ? 'بالغين' : 'Adults' }}, {{ $hotelBooking->childs }} {{ app()->getLocale() == 'ar' ? 'أطفال' : 'Children' }})
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Room Type & Board Info Banner -->
                <div class="p-3 rounded-3" style="background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.2); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                    <div>
                        <small style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--primary);">
                            {{ app()->getLocale() == 'ar' ? 'نوع الغرفة' : 'Room Type' }}
                        </small>
                        <div style="font-size: 0.95rem; font-weight: 800; color: var(--text-main);">
                            {{ $hotelBooking->room_name ?? 'Standard Room' }}
                        </div>
                    </div>
                    @if($hotelBooking->board_type)
                    <div>
                        <span class="badge-v2 badge-primary">
                            <i class="fa-solid fa-utensils me-1"></i> {{ $hotelBooking->board_type }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Guest Information Card -->
            <div class="admin-card p-4 mb-4">
                <div class="card-header-flex p-0 pb-3 mb-3" style="border-bottom: 1px solid var(--border-color);">
                    <div class="card-title">
                        <i class="fa-solid fa-users text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'بيانات النزلاء والمسافرين' : 'Guest Information' }}
                    </div>
                    @if(is_array($hotelBooking->pax_details))
                        <span class="badge-v2 badge-primary">{{ count($hotelBooking->pax_details) }} {{ app()->getLocale() == 'ar' ? 'نزيل' : 'Guests' }}</span>
                    @endif
                </div>

                <div class="v2-table-responsive">
                    <table class="v2-table w-100">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">#</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'الصفة' : 'Type' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'الاسم الكامل للنزيل' : 'Full Name' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'اللقب' : 'Title' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(is_array($hotelBooking->pax_details) && count($hotelBooking->pax_details) > 0)
                                @foreach($hotelBooking->pax_details as $index => $pax)
                                <tr>
                                    <td style="text-align: center; font-weight: 700; color: var(--text-muted);">{{ $index + 1 }}</td>
                                    <td>
                                        @if(($pax['Type'] ?? 'Adult') == 'Adult')
                                            <span class="badge-v2 badge-primary">{{ app()->getLocale() == 'ar' ? 'بالغ' : 'Adult' }}</span>
                                        @else
                                            <span class="badge-v2 badge-warning">{{ app()->getLocale() == 'ar' ? 'طفل' : 'Child' }}</span>
                                        @endif
                                    </td>
                                    <td style="font-weight: 800; color: var(--text-main);">
                                        {{ $pax['Title'] ?? '' }} {{ $pax['FirstName'] ?? '' }} {{ $pax['LastName'] ?? '' }}
                                    </td>
                                    <td style="color: var(--text-muted);">{{ $pax['Title'] ?? '---' }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                        <i class="fa-solid fa-user-slash" style="font-size: 2rem; opacity: 0.3; margin-bottom: 0.5rem; display: block;"></i>
                                        {{ app()->getLocale() == 'ar' ? 'لم يتم العثور على سجلات نزلاء منفصلة لهذا الحجز.' : 'No guest records found for this booking.' }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Sidebar Column (Supplier, Financial, Customer) -->
        <div class="col-lg-4">
            <!-- Supplier & API Card -->
            <div class="admin-card p-4 mb-4" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(13, 148, 136, 0.04) 100%); border: 1px solid rgba(16, 185, 129, 0.25);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #10b981;">
                        <i class="fa-solid fa-handshake me-1"></i> {{ app()->getLocale() == 'ar' ? 'بيانات المزوّد وAPI' : 'Supplier & API' }}
                    </span>
                    <span class="badge-v2 badge-success">Travelopro</span>
                </div>

                <small style="color: var(--text-muted); font-size: 0.75rem; display: block; font-weight: 700;">
                    {{ app()->getLocale() == 'ar' ? 'رقم تأكيد المزود (Supplier Ref)' : 'Confirmation Number' }}
                </small>
                <div style="font-size: 1.35rem; font-weight: 900; font-family: monospace; color: var(--text-main); margin-top: 0.25rem; display: flex; align-items: center; justify-content: space-between;">
                    <span>{{ $hotelBooking->supplier_confirmation_num ?? __('Pending') }}</span>
                    @if($hotelBooking->supplier_confirmation_num)
                    <button type="button" class="btn btn-sm btn-icon" onclick="copyToClipboard('{{ $hotelBooking->supplier_confirmation_num }}')" title="{{ __('Copy') }}" style="height: 30px; width: 30px;">
                        <i class="fa-regular fa-copy text-success"></i>
                    </button>
                    @endif
                </div>

                <div class="mt-3 pt-3" style="border-top: 1px solid var(--border-color); font-size: 0.8rem; display: flex; flex-direction: column; gap: 0.4rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'معرف المنتج:' : 'Product ID:' }}</span>
                        <strong style="font-family: monospace; color: var(--text-main);">{{ $hotelBooking->product_id ?? 'N/A' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'بوابة الدفع:' : 'Payment Gateway:' }}</span>
                        <strong style="color: var(--primary);">{{ strtoupper($hotelBooking->payment_method ?? 'Moyasar') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Financial Pricing Summary Card -->
            <div class="admin-card p-4 mb-4">
                <div class="card-header-flex p-0 pb-3 mb-3" style="border-bottom: 1px solid var(--border-color);">
                    <div class="card-title">
                        <i class="fa-solid fa-receipt text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'الملخص المالي والأسعار' : 'Pricing Summary' }}
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'المبلغ الإجمالي للإقامة' : 'Gross Amount' }}</span>
                        <strong style="color: var(--text-main);">{{ number_format($hotelBooking->total_price, 2) }} {{ $hotelBooking->currency }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'الضرائب والرسوم' : 'Taxes & Fees' }}</span>
                        <span style="color: #10b981; font-weight: 700;">0.00 {{ $hotelBooking->currency }}</span>
                    </div>
                    <div class="pt-2" style="border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: baseline;">
                        <span style="font-size: 1rem; font-weight: 800; color: var(--text-main);">{{ app()->getLocale() == 'ar' ? 'المبلغ المدفوع الكلي' : 'Final Total' }}</span>
                        <strong style="font-size: 1.35rem; font-weight: 900; color: var(--primary);">{{ number_format($hotelBooking->total_price, 2) }} <small style="font-size: 0.8rem;">{{ $hotelBooking->currency }}</small></strong>
                    </div>
                </div>

                <div class="p-3 text-center rounded-3" style="background: var(--bg-input); font-size: 0.75rem; color: var(--text-muted);">
                    <div>{{ app()->getLocale() == 'ar' ? 'تاريخ ووقت إنشاء الحجز' : 'Booking Created' }}</div>
                    <strong style="color: var(--text-main); margin-top: 0.2rem; display: block;">{{ optional($hotelBooking->created_at)->format('Y-m-d H:i') ?? 'N/A' }}</strong>
                </div>
            </div>

            <!-- Customer Details Card -->
            <div class="admin-card p-4 mb-4">
                <div class="card-header-flex p-0 pb-3 mb-3" style="border-bottom: 1px solid var(--border-color);">
                    <div class="card-title">
                        <i class="fa-solid fa-user-circle text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'بيانات صاحب الحجز' : 'Customer Details' }}
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                    <img src="{{ optional($hotelBooking->user)->profile_photo_url ?? asset('images/default-avatar.png') }}" class="rounded-circle shadow-sm" style="width: 44px; height: 44px; object-fit: cover;" alt="">
                    <div>
                        <strong style="color: var(--text-main); display: block; font-size: 0.95rem;">{{ optional($hotelBooking->user)->full_name ?? __('Guest') }}</strong>
                        <small style="color: var(--text-muted); font-size: 0.75rem;"><i class="fa-solid fa-envelope me-1"></i> {{ optional($hotelBooking->user)->email ?? 'N/A' }}</small>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.825rem; border-top: 1px solid var(--border-color); padding-top: 0.75rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);"><i class="fa-solid fa-phone me-1"></i> {{ app()->getLocale() == 'ar' ? 'رقم الهاتف:' : 'Phone:' }}</span>
                        <strong style="color: var(--text-main); font-family: monospace;">{{ optional($hotelBooking->user)->phone ?? '---' }}</strong>
                    </div>
                    @if($hotelBooking->user_id)
                    <div class="mt-2 text-center">
                        <a href="{{ route('admin.users.activity', $hotelBooking->user_id) }}" class="btn btn-sm btn-outline-secondary w-100" style="border-radius: var(--radius-md); font-weight: 700;">
                            <i class="fa-solid fa-chart-line me-1"></i> {{ app()->getLocale() == 'ar' ? 'عرض سجل نشاط العميل' : 'View User Activity' }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Force Confirm Modal -->
<div class="modal fade" id="forceConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg);">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title font-bold mb-0" style="color: var(--text-main);">
                    <i class="fa-solid fa-check-double text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'تأكيد الحجز يدوياً وإصدار القسيمة' : 'Force Confirm Manually' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="forceConfirmForm">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-xs text-muted mb-3">
                        {{ app()->getLocale() == 'ar' ? 'استخدم هذا الإجراء فقط إذا تم تأكيد الحجز مباشرة مع مزود الفنادق (Travelopro) عبر الهاتف أو البريد.' : 'Use this only if you confirmed the booking directly with the supplier.' }}
                    </p>
                    <div>
                        <label class="form-label font-bold text-xs" style="color: var(--text-main);">
                            {{ app()->getLocale() == 'ar' ? 'رقم تأكيد المزود (Supplier Confirmation Ref)' : 'Supplier Confirmation Number' }} <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="supplier_ref" class="form-control" placeholder="e.g. TP-12345678" required>
                    </div>
                </div>
                <div class="modal-footer border-top p-3" style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">
                        <i class="fa-solid fa-save me-1"></i> {{ app()->getLocale() == 'ar' ? 'حفظ وتأكيد الحجز' : 'Save & Confirm' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            Notify.success('{{ app()->getLocale() == "ar" ? "تم نسخ الرقم بنجاح!" : "Copied to clipboard!" }}');
        });
    }

    function retrySupplierBooking(id) {
        Notify.confirm({
            title: '{{ __("Are you sure?") }}',
            text: '{{ app()->getLocale() == "ar" ? "سيتم محاولة إرسال الحجز لمزود Travelopro مرة أخرى." : "This will attempt to book with Travelopro API again." }}',
            icon: 'info',
            confirmText: '{{ app()->getLocale() == "ar" ? "نعم، أعد المحاولة" : "Yes, Retry" }}'
        }).then((result) => {
            if (result.isConfirmed) {
                Notify.loading('{{ app()->getLocale() == "ar" ? "جاري الإرسال للمزود..." : "Submitting to API..." }}');
                fetch(`{{ url("admin/bookings/hotels") }}/${id}/retry`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        Notify.success(data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Notify.error(data.message || '{{ __("Failed to retry supplier booking") }}');
                    }
                })
                .catch(() => {
                    Swal.close();
                    Notify.error('{{ __("Network or server error") }}');
                });
            }
        });
    }

    $('#forceConfirmForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i> {{ __("Saving...") }}');

        $.ajax({
            url: "{{ route('admin.bookings.hotels.force_confirm', $hotelBooking->id) }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(res) {
                if(res.success) {
                    $('#forceConfirmModal').modal('hide');
                    Notify.success(res.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Notify.error(res.message);
                }
            },
            error: function() {
                Notify.error('{{ __("Something went wrong.") }}');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fa-solid fa-save me-1"></i> {{ app()->getLocale() == "ar" ? "حفظ وتأكيد الحجز" : "Save & Confirm" }}');
            }
        });
    });
</script>
@endpush
