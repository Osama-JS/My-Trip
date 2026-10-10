@extends('admin_v2.layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'تحليلات وإحصائيات الرحلة' : 'Trip Journey Analytics') . ': ' . ($trip->title_ar ?: $trip->title_en))

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.trips.index') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع للرحلات' : 'Back to Trips' }}">
                <i class="fa-solid fa-arrow-right rtl:rotate-0"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ app()->getLocale() == 'ar' ? 'تحليلات أداء الرحلة والمسافرين' : 'Trip Journey & Booking Analytics' }}
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ $trip->title_ar ?: $trip->title_en }} (ID: #{{ $trip->id }})
                </span>
            </div>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.trips.edit', $trip->id) }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-pen-to-square me-1"></i> {{ app()->getLocale() == 'ar' ? 'تعديل الرحلة' : 'Edit Trip' }}
            </a>
            <a href="{{ route('admin.trips.pricing', $trip->id) }}" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-money-bill-wave me-1"></i> {{ app()->getLocale() == 'ar' ? 'جدول التسعير' : 'Pricing Matrix' }}
            </a>
        </div>
    </div>

    <!-- Hero Card -->
    <div class="admin-card p-4 mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                    <span class="badge bg-primary px-3 py-2" style="border-radius: 20px;">
                        <i class="fa-solid fa-tag me-1"></i> {{ $trip->categories->first()->name ?? (app()->getLocale() == 'ar' ? 'برنامج سياحي' : 'Tour Package') }}
                    </span>
                    <span class="badge {{ $trip->active ? 'bg-success' : 'bg-danger' }} px-3 py-2" style="border-radius: 20px;">
                        {{ $trip->active ? (app()->getLocale() == 'ar' ? 'نشط ومتاح' : 'Active Trip') : (app()->getLocale() == 'ar' ? 'غير متاح' : 'Inactive Trip') }}
                    </span>
                    <div class="text-muted d-flex align-items-center gap-1 ms-2" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-eye text-primary"></i> {{ $stats['page_views'] }} {{ app()->getLocale() == 'ar' ? 'مشاهدة' : 'Views' }}
                    </div>
                </div>

                <h3 style="font-weight: 800; color: var(--text-main); margin-bottom: 0.75rem;">
                    {{ $trip->title_ar ?: $trip->title_en }}
                </h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6; max-width: 650px; margin-bottom: 1.5rem;">
                    {{ \Illuminate\Support\Str::limit($trip->description_ar ?: $trip->description_en, 200) }}
                </p>

                <!-- Route Flow Visualization -->
                <div class="d-flex align-items-center p-3 rounded" style="background: var(--bg-body); border: 1px solid var(--border-light); max-width: 520px;">
                    <div class="text-center" style="min-width: 100px;">
                        <div style="width: 12px; height: 12px; background: #ef4444; border-radius: 50%; margin: 0 auto 0.4rem; box-shadow: 0 0 10px rgba(239,68,68,0.4);"></div>
                        <strong style="color: var(--text-main); font-size: 0.85rem;">{{ $trip->fromCity->name ?? $trip->fromCountry->name }}</strong>
                    </div>
                    <div class="flex-grow-1 mx-3 position-relative text-center">
                        <hr style="border-top: 2px dashed var(--border-light); margin: 0.6rem 0;">
                        <i class="fa-solid fa-plane text-primary position-absolute" style="top: -2px; left: 50%; transform: translateX(-50%); background: var(--bg-body); padding: 0 0.5rem;"></i>
                    </div>
                    <div class="text-center" style="min-width: 100px;">
                        <div style="width: 12px; height: 12px; background: #0ea5e9; border-radius: 50%; margin: 0 auto 0.4rem; box-shadow: 0 0 10px rgba(14,165,233,0.4);"></div>
                        <strong style="color: var(--text-main); font-size: 0.85rem;">{{ $trip->toCity->name ?? $trip->toCountry->name }}</strong>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 text-lg-end">
                @if($trip->image_url)
                    <div class="mb-3">
                        <img src="{{ $trip->image_url }}" alt="Trip Image" class="img-fluid rounded" style="max-height: 140px; object-fit: cover; border: 1px solid var(--border-light);">
                    </div>
                @endif
                <div class="mb-3">
                    <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'سعر البرنامج' : 'Package Price' }}</small>
                    <div style="font-size: 1.6rem; font-weight: 800; color: var(--primary);">
                        {{ number_format($trip->price, 2) }} <small style="font-size: 0.9rem;">{{ app()->getLocale() == 'ar' ? 'ر.س' : 'SAR' }}</small>
                    </div>
                    @if($trip->price_before_discount)
                        <div class="text-muted text-decoration-line-through small">{{ number_format($trip->price_before_discount, 2) }}</div>
                    @endif
                </div>

                <div style="font-size: 0.85rem; display: inline-flex; flex-direction: column; gap: 0.4rem; text-align: start;">
                    <div><i class="fa-solid fa-clock text-primary me-2"></i> <span class="text-muted">{{ app()->getLocale() == 'ar' ? 'المدة:' : 'Duration:' }}</span> <strong style="color: var(--text-main);">{{ $trip->duration ?? '---' }}</strong></div>
                    <div><i class="fa-solid fa-users text-primary me-2"></i> <span class="text-muted">{{ app()->getLocale() == 'ar' ? 'السعة:' : 'Capacity:' }}</span> <strong style="color: var(--text-main);">{{ $trip->personnel_capacity ?? (app()->getLocale() == 'ar' ? 'غير محدد' : 'Unlimited') }}</strong></div>
                    <div><i class="fa-solid fa-building text-primary me-2"></i> <span class="text-muted">{{ app()->getLocale() == 'ar' ? 'الشركة:' : 'Company:' }}</span> <strong style="color: var(--text-main);">{{ $trip->company->name ?? '---' }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- Occupancy Rate -->
        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4 text-center h-100 d-flex flex-column justify-content-between">
                <h6 style="color: var(--text-muted); font-weight: 700; margin-bottom: 0.75rem;">
                    {{ app()->getLocale() == 'ar' ? 'نسبة الإشغال' : 'Occupancy Rate' }}
                </h6>
                <div class="my-2">
                    <div style="font-size: 2.2rem; font-weight: 900; color: var(--primary);">
                        {{ $stats['occupancy_rate'] }}%
                    </div>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $stats['occupancy_rate']) }}%"></div>
                    </div>
                </div>
                <div class="d-flex justify-content-between text-start border-top pt-2 mt-2" style="font-size: 0.8rem;">
                    <div>
                        <span class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'المحجوز' : 'Occupied' }}</span>
                        <strong style="color: var(--text-main);">{{ $stats['occupied_seats'] }}</strong>
                    </div>
                    <div class="text-end">
                        <span class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'المتبقي' : 'Remaining' }}</span>
                        <strong style="color: var(--text-main);">{{ $stats['remaining_seats'] }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(34,197,94,0.1); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <h6 style="color: var(--text-muted); font-weight: 700; margin: 0;">{{ app()->getLocale() == 'ar' ? 'إجمالي الإيرادات' : 'Total Revenue' }}</h6>
                </div>
                <div class="my-3">
                    <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">
                        {{ number_format($stats['total_revenue'], 2) }} <small style="font-size: 0.85rem; color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'ر.س' : 'SAR' }}</small>
                    </h3>
                </div>
                <div class="text-warning small border-top pt-2" style="font-size: 0.8rem;">
                    <i class="fa-solid fa-hourglass-half me-1"></i> {{ number_format($stats['pending_revenue'], 2) }} {{ app()->getLocale() == 'ar' ? 'معلق' : 'Pending' }}
                </div>
            </div>
        </div>

        <!-- Total Passengers -->
        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(14,165,233,0.1); color: #0ea5e9; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-suitcase-rolling"></i>
                    </div>
                    <h6 style="color: var(--text-muted); font-weight: 700; margin: 0;">{{ app()->getLocale() == 'ar' ? 'إجمالي المسافرين' : 'Total Passengers' }}</h6>
                </div>
                <div class="my-3">
                    <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">{{ $stats['total_passengers'] }}</h3>
                </div>
                <div class="text-muted small border-top pt-2" style="font-size: 0.8rem;">
                    <i class="fa-solid fa-users text-primary me-1"></i> {{ app()->getLocale() == 'ar' ? 'المسافرون المسجلون' : 'Registered Travelers' }}
                </div>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(99,102,241,0.1); color: #6366f1; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <h6 style="color: var(--text-muted); font-weight: 700; margin: 0;">{{ app()->getLocale() == 'ar' ? 'إجمالي الحجوزات' : 'Total Bookings' }}</h6>
                </div>
                <div class="my-3">
                    <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">{{ $stats['total_bookings'] }}</h3>
                </div>
                <div class="text-success small border-top pt-2" style="font-size: 0.8rem;">
                    <i class="fa-solid fa-circle-check me-1"></i> {{ $stats['confirmed_bookings'] }} {{ app()->getLocale() == 'ar' ? 'حجز مؤكد' : 'Confirmed' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Container Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="p-3 border-bottom" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <ul class="nav nav-pills gap-2 flex-wrap" id="statsTabs" role="tablist">
                <li class="nav-item">
                    <button class="btn btn-sm btn-primary active" id="manifest-tab" data-bs-toggle="pill" data-bs-target="#manifest" type="button" role="tab" style="font-weight: 700; border-radius: var(--radius-md);">
                        <i class="fa-solid fa-users me-1"></i> {{ app()->getLocale() == 'ar' ? 'بيان المسافرين والحجوزات' : 'Passenger Manifest' }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="btn btn-sm btn-outline-secondary" id="itinerary-tab" data-bs-toggle="pill" data-bs-target="#itinerary" type="button" role="tab" style="font-weight: 700; border-radius: var(--radius-md);">
                        <i class="fa-solid fa-timeline me-1"></i> {{ app()->getLocale() == 'ar' ? 'الجدول اليومي' : 'Daily Itinerary' }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="btn btn-sm btn-outline-secondary" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab" style="font-weight: 700; border-radius: var(--radius-md);">
                        <i class="fa-solid fa-circle-info me-1"></i> {{ app()->getLocale() == 'ar' ? 'الوصف الكامل للبرنامج' : 'Full Description' }}
                    </button>
                </li>
            </ul>
        </div>

        <div class="p-4">
            <div class="tab-content" id="statsTabsContent">
                <!-- Manifest Tab -->
                <div class="tab-pane fade show active" id="manifest" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                            <thead style="background: rgba(0,0,0,0.02);">
                                <tr>
                                    <th>{{ app()->getLocale() == 'ar' ? 'الحجز' : 'Booking' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'حجز بواسطة' : 'Booked By' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'قائمة المسافرين' : 'Travelers List' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                                    <th class="text-end">{{ app()->getLocale() == 'ar' ? 'الإجمالي' : 'Total' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBookings as $booking)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.trip_bookings.show', $booking->id) }}" class="font-bold text-primary">#{{ $booking->id }}</a>
                                            <div class="small text-muted">{{ $booking->created_at->format('Y-m-d') }}</div>
                                        </td>
                                        <td>
                                            <div class="font-bold text-main">{{ $booking->user->name ?? '---' }}</div>
                                            <div class="small text-muted">{{ $booking->user->email ?? '' }}</div>
                                        </td>
                                        <td>
                                            @if($booking->passengers->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($booking->passengers as $passenger)
                                                        <span class="badge bg-light text-dark border">
                                                            <i class="fa-solid fa-user text-primary me-1" style="font-size: 0.75rem;"></i>
                                                            {{ $passenger->name ?: ($passenger->first_name . ' ' . $passenger->last_name) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted small">---</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $stColor = 'warning';
                                                if($booking->status == 'confirmed') $stColor = 'success';
                                                if($booking->status == 'cancelled') $stColor = 'danger';
                                            @endphp
                                            <span class="badge bg-{{ $stColor }}">{{ __(ucfirst($booking->status)) }}</span>
                                        </td>
                                        <td class="text-end font-bold text-main">
                                            {{ number_format($booking->total_price, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            {{ app()->getLocale() == 'ar' ? 'لا توجد حجوزات مسجلة لهذه الرحلة حتى الآن.' : 'No bookings registered yet.' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Itinerary Tab -->
                <div class="tab-pane fade" id="itinerary" role="tabpanel">
                    <div class="d-flex flex-column gap-3">
                        @forelse($trip->itineraries as $day)
                            <div class="admin-card p-3 d-flex align-items-start gap-3" style="border: 1px solid var(--border-light);">
                                <div class="text-center px-3 py-2 rounded" style="background: rgba(59,130,246,0.1); color: var(--primary); min-width: 70px;">
                                    <div style="font-size: 1.4rem; font-weight: 800; line-height: 1;">{{ $day->day_number }}</div>
                                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">{{ app()->getLocale() == 'ar' ? 'اليوم' : 'Day' }}</div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">{{ $day->title }}</h6>
                                    <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0; line-height: 1.6;">{{ $day->description }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fa-solid fa-calendar-xmark fa-2x mb-2 d-block opacity-50"></i>
                                {{ app()->getLocale() == 'ar' ? 'لم يتم إدخال تفاصيل جدول يومي لهذه الرحلة بعد.' : 'No specific daily program uploaded for this trip.' }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Overview Tab -->
                <div class="tab-pane fade" id="overview" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 style="font-weight: 800; color: var(--text-main); border-bottom: 2px solid var(--border-light); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                                {{ app()->getLocale() == 'ar' ? 'الوصف بالعربية' : 'Arabic Details' }}
                            </h6>
                            <div class="p-3 rounded" style="background: var(--bg-body); border: 1px solid var(--border-light); line-height: 1.8; font-size: 0.88rem; color: var(--text-main);">
                                {!! nl2br(e($trip->description_ar)) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 style="font-weight: 800; color: var(--text-main); border-bottom: 2px solid var(--border-light); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                                {{ app()->getLocale() == 'ar' ? 'الوصف بالإنجليزية' : 'English Details' }}
                            </h6>
                            <div class="p-3 rounded" style="background: var(--bg-body); border: 1px solid var(--border-light); line-height: 1.8; font-size: 0.88rem; color: var(--text-main);">
                                {!! nl2br(e($trip->description_en)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#statsTabs button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
            $('#statsTabs button').removeClass('btn-primary').addClass('btn-outline-secondary');
            $(e.target).removeClass('btn-outline-secondary').addClass('btn-primary');
        });
    });
</script>
@endpush
