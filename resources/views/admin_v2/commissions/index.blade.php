@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'عمولات وأرباح المنصة' : 'Platform Commissions & Profits')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                {{ app()->getLocale() == 'ar' ? 'عمولات وأرباح المنصة المجمعة' : 'Platform Commissions & Profits' }}
            </h1>
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                {{ app()->getLocale() == 'ar' ? 'نظرة شاملة ومفصلة على أرباح المنصة الناتجة عن حجوزات الطيران، الفنادق، والرحلات.' : 'Comprehensive overview of margins across flight, hotel, and package bookings.' }}
            </span>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.bookings.flights.profits') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-plane me-1"></i> {{ app()->getLocale() == 'ar' ? 'أرباح الطيران' : 'Flight Profits' }}
            </a>
            <a href="{{ route('admin.bookings.hotels.profits') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-hotel me-1"></i> {{ app()->getLocale() == 'ar' ? 'أرباح الفنادق' : 'Hotel Profits' }}
            </a>
            <a href="{{ route('admin.trips.profits') }}" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-suitcase-rolling me-1"></i> {{ app()->getLocale() == 'ar' ? 'أرباح الرحلات' : 'Trip Profits' }}
            </a>
        </div>
    </div>

    <!-- 4 Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي الأرباح الكلية' : 'Total Profit' }}</small>
                        <h3 style="font-weight: 900; color: var(--primary); margin: 0;">
                            +{{ number_format($totalOverallProfit ?? 0, 2) }} <small style="font-size: 0.85rem; color: var(--text-muted);">SAR</small>
                        </h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'أرباح تذاكر الطيران' : 'Flights Profit' }}</small>
                        <h3 style="font-weight: 900; color: #0ea5e9; margin: 0;">
                            +{{ number_format($totalFlightProfit ?? 0, 2) }} <small style="font-size: 0.85rem; color: var(--text-muted);">SAR</small>
                        </h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(14,165,233,0.1); color: #0ea5e9; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-plane"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'أرباح حجوزات الفنادق' : 'Hotels Profit' }}</small>
                        <h3 style="font-weight: 900; color: #22c55e; margin: 0;">
                            +{{ number_format($totalHotelProfit ?? 0, 2) }} <small style="font-size: 0.85rem; color: var(--text-muted);">SAR</small>
                        </h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(34,197,94,0.1); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-hotel"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'أرباح البرامج السياحية' : 'Trips Profit' }}</small>
                        <h3 style="font-weight: 900; color: #f59e0b; margin: 0;">
                            +{{ number_format($totalTripProfit ?? 0, 2) }} <small style="font-size: 0.85rem; color: var(--text-muted);">SAR</small>
                        </h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-suitcase-rolling"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Container Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="p-3 border-bottom" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <ul class="nav nav-pills gap-2 flex-wrap" id="profitTabs" role="tablist">
                <li class="nav-item">
                    <button class="btn btn-sm btn-primary active" id="flights-tab" data-bs-toggle="pill" data-bs-target="#flights" type="button" role="tab" style="font-weight: 700; border-radius: var(--radius-md);">
                        <i class="fa-solid fa-plane me-1"></i> {{ app()->getLocale() == 'ar' ? 'أرباح الطيران' : 'Flights' }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="btn btn-sm btn-outline-secondary" id="hotels-tab" data-bs-toggle="pill" data-bs-target="#hotels" type="button" role="tab" style="font-weight: 700; border-radius: var(--radius-md);">
                        <i class="fa-solid fa-hotel me-1"></i> {{ app()->getLocale() == 'ar' ? 'أرباح الفنادق' : 'Hotels' }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="btn btn-sm btn-outline-secondary" id="trips-tab" data-bs-toggle="pill" data-bs-target="#trips" type="button" role="tab" style="font-weight: 700; border-radius: var(--radius-md);">
                        <i class="fa-solid fa-suitcase-rolling me-1"></i> {{ app()->getLocale() == 'ar' ? 'أرباح الرحلات' : 'Tour Packages' }}
                    </button>
                </li>
            </ul>
        </div>

        <div class="p-4">
            <div class="tab-content" id="profitTabsContent">
                <!-- Flights Tab -->
                <div class="tab-pane fade show active" id="flights" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                            <thead style="background: rgba(0,0,0,0.02);">
                                <tr>
                                    <th>{{ app()->getLocale() == 'ar' ? 'مرجع الحجز' : 'Booking Ref' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'إجمالي السعر' : 'Total Price' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'نوع الهامش' : 'Margin Type' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'الربح' : 'Profit' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($flightBookings ?? [] as $b)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $b->id) }}" class="font-bold text-primary">{{ $b->booking_reference }}</a>
                                        </td>
                                        <td>{{ $b->user->name ?? $b->contact_email }}</td>
                                        <td class="font-bold text-main">{{ number_format($b->total_amount, 2) }} {{ $b->currency }}</td>
                                        <td>
                                            @if(($flightMarginType ?? '') == 'fixed')
                                                <span class="badge bg-light text-dark border">{{ $flightMargin }} SAR Fixed</span>
                                            @else
                                                <span class="badge bg-light text-dark border">{{ $flightMargin }} % Percentage</span>
                                            @endif
                                        </td>
                                        <td class="font-bold text-success">+{{ number_format($b->profit, 2) }} SAR</td>
                                        <td><span class="badge bg-success">{{ ucfirst($b->status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات متاحة' : 'No flight bookings found' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Hotels Tab -->
                <div class="tab-pane fade" id="hotels" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                            <thead style="background: rgba(0,0,0,0.02);">
                                <tr>
                                    <th>{{ app()->getLocale() == 'ar' ? 'مرجع الحجز' : 'Booking Ref' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'اسم الفندق' : 'Hotel Name' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'إجمالي السعر' : 'Total Price' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'نوع الهامش' : 'Margin Type' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'الربح' : 'Profit' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hotelBookings ?? [] as $b)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.bookings.hotels.show', $b->id) }}" class="font-bold text-primary">{{ $b->reference_num ?? ('#' . $b->id) }}</a>
                                        </td>
                                        <td><strong>{{ Str::limit($b->hotel_name, 30) }}</strong></td>
                                        <td>{{ $b->user->name ?? '-' }}</td>
                                        <td class="font-bold text-main">{{ number_format($b->total_price, 2) }} {{ $b->currency }}</td>
                                        <td>
                                            @if(($hotelMarginType ?? '') == 'fixed')
                                                <span class="badge bg-light text-dark border">{{ $hotelMargin }} SAR Fixed</span>
                                            @else
                                                <span class="badge bg-light text-dark border">{{ $hotelMargin }} % Percentage</span>
                                            @endif
                                        </td>
                                        <td class="font-bold text-success">+{{ number_format($b->profit, 2) }} SAR</td>
                                        <td><span class="badge bg-success">{{ ucfirst($b->status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات متاحة' : 'No hotel bookings found' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Trips Tab -->
                <div class="tab-pane fade" id="trips" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                            <thead style="background: rgba(0,0,0,0.02);">
                                <tr>
                                    <th>{{ app()->getLocale() == 'ar' ? 'رقم الحجز' : 'Booking ID' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'البرنامج' : 'Trip' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'الشركة' : 'Company' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'إجمالي السعر' : 'Total Price' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'العمولة' : 'Commission' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'الربح' : 'Profit' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tripBookings ?? [] as $b)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.trip_bookings.show', $b->id) }}" class="font-bold text-primary">#{{ $b->id }}</a>
                                        </td>
                                        <td><strong>{{ Str::limit($b->trip->title ?? 'N/A', 30) }}</strong></td>
                                        <td>{{ $b->trip->company->name ?? ($b->trip->company->en_name ?? 'N/A') }}</td>
                                        <td>{{ $b->user->name ?? '-' }}</td>
                                        <td class="font-bold text-main">{{ number_format($b->total_price, 2) }} SAR</td>
                                        <td>
                                            @if(($b->commission_type ?? 'percentage') === 'fixed')
                                                <span class="badge bg-light text-dark border">{{ number_format($b->commission_value ?? 0, 2) }} SAR Fixed</span>
                                            @else
                                                <span class="badge bg-light text-dark border">{{ number_format($b->commission_value ?? $b->commission_rate ?? 0, 2) }} %</span>
                                            @endif
                                        </td>
                                        <td class="font-bold text-success">+{{ number_format($b->profit, 2) }} SAR</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات متاحة' : 'No trip bookings found' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
        $('#profitTabs button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
            $('#profitTabs button').removeClass('btn-primary').addClass('btn-outline-secondary');
            $(e.target).removeClass('btn-outline-secondary').addClass('btn-primary');
        });
    });
</script>
@endpush
