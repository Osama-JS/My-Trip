@extends('admin_v2.layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'تفاصيل حجز الرحلة #' : 'Trip Booking #') . $booking->id)

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.trip-bookings.index') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع لحجوزات الباقات' : 'Back to Bookings' }}">
                <i class="fa-solid fa-arrow-right rtl:rotate-0"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ app()->getLocale() == 'ar' ? 'تفاصيل حجز البرنامج السياحي' : 'Tour Package Booking Details' }} <span style="color: var(--primary);">#{{ $booking->id }}</span>
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ app()->getLocale() == 'ar' ? 'تاريخ الحجز:' : 'Booked on:' }} {{ $booking->booking_date ? $booking->booking_date->format('Y-m-d') : $booking->created_at->format('Y-m-d') }}
                </span>
            </div>
        </div>

        <div style="display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center;">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadTicketModal" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-file-arrow-up me-1"></i> {{ app()->getLocale() == 'ar' ? 'رفع التذكرة / القسيمة' : 'Upload Ticket' }}
            </button>

            @if($booking->ticket_file)
                <form action="{{ route('admin.trip-bookings.send-ticket', $booking->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                        <i class="fa-solid fa-paper-plane me-1"></i> {{ app()->getLocale() == 'ar' ? 'إرسال التذكرة للعميل' : 'Send Ticket' }}
                    </button>
                </form>

                <a href="{{ asset('storage/' . $booking->ticket_file) }}" target="_blank" class="btn btn-outline-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                    <i class="fa-solid fa-download me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحميل التذكرة' : 'Download Ticket' }}
                </a>
            @endif

            <button type="button" class="btn btn-outline-secondary" onclick="window.print();" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-print me-1"></i> {{ app()->getLocale() == 'ar' ? 'طباعة' : 'Print' }}
            </button>
        </div>
    </div>

    <!-- Status & State Progression Card -->
    <div class="admin-card mb-4">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; background: rgba(37,99,235,0.1); color: var(--primary);">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                    <h5 style="margin: 0 0 0.25rem; font-weight: 800; color: var(--text-main);">
                        {{ app()->getLocale() == 'ar' ? 'المرحلة التشغيلية للحجز' : 'Operational Booking Stage' }}
                    </h5>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">
                        {{ app()->getLocale() == 'ar' ? 'الحالة الحالية:' : 'Current State:' }} 
                        <strong style="color: var(--primary);">{{ __($booking->booking_state) }}</strong>
                    </span>
                </div>
            </div>

            <!-- State Change Form -->
            <form action="{{ route('admin.trip-bookings.update-state', $booking->id) }}" method="POST" class="d-flex align-items-center gap-2">
                @csrf
                <label class="font-bold text-xs" style="color: var(--text-muted); white-space: nowrap;">{{ app()->getLocale() == 'ar' ? 'تحديث المرحلة:' : 'Change Stage:' }}</label>
                <select name="booking_state" class="form-select form-select-sm" style="min-width: 170px;" onchange="this.form.submit()">
                    <option value="awaiting_payment" {{ $booking->booking_state == 'awaiting_payment' ? 'selected' : '' }}>{{ __('awaiting_payment') }}</option>
                    <option value="preparing" {{ $booking->booking_state == 'preparing' ? 'selected' : '' }}>{{ __('preparing') }}</option>
                    <option value="issuing_tickets" {{ $booking->booking_state == 'issuing_tickets' ? 'selected' : '' }}>{{ __('issuing_tickets') }}</option>
                    <option value="tickets_uploaded" {{ $booking->booking_state == 'tickets_uploaded' ? 'selected' : '' }}>{{ __('tickets_uploaded') }}</option>
                    <option value="completed" {{ $booking->booking_state == 'completed' ? 'selected' : '' }}>{{ __('completed') }}</option>
                    <option value="cancelled" {{ $booking->booking_state == 'cancelled' ? 'selected' : '' }}>{{ __('cancelled') }}</option>
                </select>
            </form>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;" class="charts-grid">
        <!-- Left: Passengers, Pricing & Trip Details -->
        <div>
            <!-- Trip Overview Card -->
            <div class="admin-card mb-4">
                <div class="card-header-flex">
                    <div class="card-title">
                        <i class="fa-solid fa-umbrella-beach text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'تفاصيل البرنامج السياحي' : 'Tour Package Details' }}
                    </div>
                    @if($booking->trip)
                        <a href="{{ route('admin.trips.edit', $booking->trip->id) }}" class="btn btn-sm btn-outline-primary" style="border-radius: var(--radius-sm);">
                            <i class="fa-solid fa-pen-to-square me-1"></i> {{ app()->getLocale() == 'ar' ? 'عرض البرنامج' : 'View Package' }}
                        </a>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.25rem; background: var(--bg-input); padding: 1.25rem; border-radius: var(--radius-md);">
                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'عنوان الرحلة' : 'Trip Title' }}</span>
                        <strong style="font-size: 1rem; color: var(--text-main);">{{ $booking->trip->title ?? '—' }}</strong>
                    </div>

                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'الشركة المنظمة' : 'Tour Company' }}</span>
                        <strong style="font-size: 0.95rem; color: var(--text-main);">{{ $booking->trip->company->name ?? '—' }}</strong>
                    </div>

                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'مدة البرنامج' : 'Duration' }}</span>
                        <strong style="font-size: 0.95rem; color: var(--text-main);">{{ $booking->trip->duration_days ?? '—' }} {{ app()->getLocale() == 'ar' ? 'أيام' : 'Days' }}</strong>
                    </div>

                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'تاريخ السفر' : 'Travel Date' }}</span>
                        <strong style="font-size: 0.95rem; color: var(--primary);">{{ $booking->trip_date ? \Carbon\Carbon::parse($booking->trip_date)->format('Y-m-d') : '—' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Passengers List Table -->
            <div class="admin-card mb-4">
                <div class="card-header-flex">
                    <div class="card-title">
                        <i class="fa-solid fa-users text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة المسافرين المسجلين' : 'Passenger Manifest' }}
                    </div>
                    <span class="badge-v2 badge-primary">{{ count($booking->passengers ?? []) }} {{ app()->getLocale() == 'ar' ? 'مسافر' : 'Pax' }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table-v2">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() == 'ar' ? 'اسم المسافر' : 'Passenger Name' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'النوع' : 'Type' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'رقم الجواز / الهوية' : 'Passport / ID' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'تاريخ الميلاد' : 'DOB' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->passengers ?? [] as $pax)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-main);">
                                        <i class="fa-solid fa-user-circle text-muted me-1"></i> {{ $pax->name ?? ($pax->first_name . ' ' . $pax->last_name) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-v2 badge-info">{{ ucfirst($pax->passenger_type ?? $pax->type ?? 'Adult') }}</span>
                                </td>
                                <td>
                                    <span style="font-family: monospace; font-weight: 700;">{{ $pax->passport_number ?? $pax->national_id ?? '---' }}</span>
                                    @if($pax->passport_expiry)
                                        <small style="display: block; color: var(--text-muted);">Exp: {{ \Carbon\Carbon::parse($pax->passport_expiry)->format('Y-m-d') }}</small>
                                    @endif
                                </td>
                                <td>{{ $pax->dob ? \Carbon\Carbon::parse($pax->dob)->format('Y-m-d') : ($pax->age ? $pax->age . ' yrs' : '---') }}</td>
                                <td><span style="direction: ltr; display: inline-block;">{{ $pax->phone ?? '---' }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                                    {{ app()->getLocale() == 'ar' ? 'لا يوجد مسافرون مسجلون لهذا الحجز.' : 'No passengers recorded.' }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- State History Timeline -->
            @if(isset($booking->history) && count($booking->history) > 0)
            <div class="admin-card">
                <div class="card-header-flex mb-3 pb-2" style="border-bottom: 1px solid var(--border-light);">
                    <div class="card-title">
                        <i class="fa-solid fa-timeline text-secondary me-2"></i> {{ app()->getLocale() == 'ar' ? 'سجل تتبع الحالات والمراحل' : 'State History Log' }}
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($booking->history as $h)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: var(--bg-input); border-radius: var(--radius-sm); font-size: 0.85rem;">
                        <div>
                            <span class="badge-v2 badge-primary me-2">{{ __($h->to_state ?? $h->state) }}</span>
                            <span style="color: var(--text-muted);">{{ $h->comment ?? '' }}</span>
                        </div>
                        <small style="color: var(--text-muted);">{{ $h->created_at->format('Y-m-d H:i') }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right: Financial Breakdown & Customer Profile -->
        <div>
            <!-- Financial Breakdown Card -->
            <div class="admin-card mb-4">
                <div class="card-header-flex">
                    <div class="card-title">
                        <i class="fa-solid fa-sack-dollar text-success me-2"></i> {{ app()->getLocale() == 'ar' ? 'التفاصيل المالية والتسعير' : 'Pricing Breakdown' }}
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'السعر الأساسي للرحلة:' : 'Base Trip Price:' }}</span>
                        <strong style="color: var(--text-main);">{{ number_format($booking->base_price ?? $booking->total_price, 2) }} SAR</strong>
                    </div>

                    @if($booking->package)
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'الباقة المختارة:' : 'Selected Package:' }}</span>
                        <strong>{{ $booking->package->name }}</strong>
                    </div>
                    @endif

                    @if(isset($booking->addons) && count($booking->addons) > 0)
                    <div style="padding: 0.5rem 0; border-top: 1px dashed var(--border-light); border-bottom: 1px dashed var(--border-light);">
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700; margin-bottom: 0.25rem;">{{ app()->getLocale() == 'ar' ? 'الخدمات الإضافية:' : 'Add-ons:' }}</span>
                        @foreach($booking->addons as $addon)
                            <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.25rem;">
                                <span>{{ $addon->title ?? $addon->name }}</span>
                                <strong style="color: var(--primary);">+{{ number_format($addon->pivot->price ?? $addon->price, 2) }} SAR</strong>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.5rem; border-top: 1px solid var(--border-light);">
                        <span style="font-weight: 800; color: var(--text-main); font-size: 1rem;">{{ app()->getLocale() == 'ar' ? 'إجمالي سعر الحجز:' : 'Total Price:' }}</span>
                        <strong style="font-size: 1.35rem; color: var(--primary);">{{ number_format($booking->total_price, 2) }} <small style="font-size: 0.85rem;">SAR</small></strong>
                    </div>
                </div>
            </div>

            <!-- Customer Profile Card -->
            <div class="admin-card">
                <div class="card-header-flex">
                    <div class="card-title">
                        <i class="fa-solid fa-address-card text-info me-2"></i> {{ app()->getLocale() == 'ar' ? 'بيانات العميل' : 'Customer Profile' }}
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <div class="user-avatar" style="width: 44px; height: 44px; font-size: 1.1rem;">
                        {{ strtoupper(substr($booking->user->first_name ?? 'C', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight: 800; color: var(--text-main);">{{ $booking->user->full_name ?? ($booking->contact_name ?? __('Guest')) }}</div>
                        <small style="color: var(--text-muted);">{{ $booking->user->user_type ?? 'Customer' }}</small>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.875rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-envelope text-muted" style="width: 20px;"></i>
                        <span style="color: var(--text-main);">{{ $booking->user->email ?? ($booking->contact_email ?? '—') }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-phone text-muted" style="width: 20px;"></i>
                        <span style="color: var(--text-main); direction: ltr;">{{ $booking->user->phone ?? ($booking->contact_phone ?? '—') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Upload Ticket Modal -->
<div class="modal fade" id="uploadTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-light);">
                <h5 class="modal-title font-bold text-primary">
                    <i class="fa-solid fa-file-arrow-up me-2"></i> {{ app()->getLocale() == 'ar' ? 'رفع تذكرة / قسيمة الرحلة' : 'Upload Tour Ticket File' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.trip-bookings.upload-ticket', $booking->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اختر ملف التذكرة (PDF أو صورة)' : 'Select Ticket File (PDF or Image)' }} <span class="text-danger">*</span></label>
                        <input type="file" name="ticket_file" class="form-control" required accept=".pdf,.png,.jpg,.jpeg">
                    </div>
                    <small style="color: var(--text-muted); display: block;">
                        {{ app()->getLocale() == 'ar' ? 'بعد رفع الملف سيتم تحويل حالة الحجز إلى (تذاكر مرفوعة) ويمكنك إرسالها للعميل بضغطة زر.' : 'Uploading will mark the state as tickets_uploaded.' }}
                    </small>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-light);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">
                        <i class="fa-solid fa-upload me-1"></i> {{ app()->getLocale() == 'ar' ? 'رفع وحفظ' : 'Upload' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
