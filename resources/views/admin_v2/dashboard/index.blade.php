@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'لوحة المؤشرات والتحليلات' : 'Dashboard & Analytics')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Title & Quick Actions Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                {{ app()->getLocale() == 'ar' ? 'لوحة المؤشرات والتحليلات' : 'Dashboard Overview' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">
                {{ app()->getLocale() == 'ar' ? 'مرحباً بك، نظرة عامة شاملة ومباشرة على أداء الحجوزات والعمليات المالية.' : 'Welcome back, comprehensive live overview of bookings and finance.' }}
            </p>
        </div>

        <!-- Filter Controls -->
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('admin.bookings.flights.index') }}" class="btn btn-outline-primary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-plane me-1"></i> {{ app()->getLocale() == 'ar' ? 'حجوزات الطيران' : 'Flights' }}
            </a>
            <a href="{{ route('admin.bookings.hotels.index') }}" class="btn btn-outline-success" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-hotel me-1"></i> {{ app()->getLocale() == 'ar' ? 'حجوزات الفنادق' : 'Hotels' }}
            </a>
            <a href="{{ route('admin.trip-bookings.index') }}" class="btn btn-outline-warning" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-suitcase-rolling me-1"></i> {{ app()->getLocale() == 'ar' ? 'حجوزات الباقات' : 'Trips' }}
            </a>
            <button class="btn btn-primary" onclick="window.location.reload();" style="background: var(--primary); border: none; border-radius: var(--radius-md); padding: 0.55rem 1.25rem; font-weight: 700; white-space: nowrap;">
                <i class="fa-solid fa-arrows-rotate me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحديث' : 'Refresh' }}
            </button>
        </div>
    </div>

    <!-- 1. KPI Stats Grid -->
    <div class="stat-grid">
        <!-- Total Revenue -->
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي الإيرادات المؤكدة' : 'Total Revenue' }}</div>
                <div class="stat-value">{{ number_format($stats['total_revenue'] ?? 0, 2) }} <small style="font-size: 0.9rem; font-weight: 600;">SAR</small></div>
                <div class="stat-trend trend-up">
                    <i class="fa-solid fa-wallet"></i> {{ app()->getLocale() == 'ar' ? 'مجموع مبيعات الطيران والفنادق والباقات' : 'Flights + Hotels + Tours' }}
                </div>
            </div>
            <div class="stat-icon icon-primary">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>

        <!-- Flight Bookings -->
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'حجوزات الطيران' : 'Flight Bookings' }}</div>
                <div class="stat-value">{{ number_format($stats['flight_count'] ?? 0) }}</div>
                <div class="stat-trend trend-up">
                    <i class="fa-solid fa-circle-check text-success"></i> {{ number_format($stats['flight_revenue'] ?? 0, 2) }} <span style="font-size: 0.75rem;">SAR</span>
                </div>
            </div>
            <div class="stat-icon icon-info">
                <i class="fa-solid fa-plane-departure"></i>
            </div>
        </div>

        <!-- Hotel Bookings -->
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'حجوزات الفنادق' : 'Hotel Bookings' }}</div>
                <div class="stat-value">{{ number_format($stats['hotel_count'] ?? 0) }}</div>
                <div class="stat-trend trend-up">
                    <i class="fa-solid fa-circle-check text-success"></i> {{ number_format($stats['hotel_revenue'] ?? 0, 2) }} <span style="font-size: 0.75rem;">SAR</span>
                </div>
            </div>
            <div class="stat-icon icon-success">
                <i class="fa-solid fa-hotel"></i>
            </div>
        </div>

        <!-- Active Users / Agents -->
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'المستخدمين والعملاء' : 'Active Users / Clients' }}</div>
                <div class="stat-value">{{ number_format($stats['users_count'] ?? 0) }}</div>
                <div class="stat-trend trend-up">
                    <i class="fa-solid fa-users"></i> {{ app()->getLocale() == 'ar' ? 'حسابات مسجلة' : 'Registered users' }}
                </div>
            </div>
            <div class="stat-icon icon-warning">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>

    <!-- 2. Interactive Charts Section -->
    <div class="charts-grid">
        <!-- Revenue Analytics (ApexCharts) -->
        <div class="admin-card mb-0">
            <div class="card-header-flex">
                <div class="card-title">
                    <i class="fa-solid fa-chart-area text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'تحليلات الإيرادات الشهرية' : 'Monthly Revenue Analytics' }}
                </div>
                <div class="badge-v2 badge-primary">{{ date('Y') }}</div>
            </div>
            <div id="revenueChart" style="min-height: 320px;"></div>
        </div>

        <!-- Bookings Share Donut (ApexCharts) -->
        <div class="admin-card mb-0">
            <div class="card-header-flex">
                <div class="card-title">
                    <i class="fa-solid fa-chart-pie text-success me-2"></i> {{ app()->getLocale() == 'ar' ? 'توزيع الحجوزات حسب الخدمة' : 'Bookings Share' }}
                </div>
            </div>
            <div id="shareChart" style="min-height: 320px;"></div>
        </div>
    </div>

    <!-- 3. Recent Bookings Interactive Table -->
    <div class="admin-card">
        <div class="card-header-flex">
            <div>
                <div class="card-title">
                    <i class="fa-solid fa-receipt text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'أحدث الحجوزات والعمليات' : 'Recent Bookings & Transactions' }}
                </div>
                <small style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'قائمة بأحدث الحجوزات المسجلة في النظام' : 'Live list of recent bookings across the platform' }}</small>
            </div>
            <a href="{{ route('admin.bookings.flights.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: var(--radius-full); font-weight: 700; font-size: 0.8rem;">
                {{ app()->getLocale() == 'ar' ? 'عرض كافة الحجوزات' : 'View All Bookings' }} <i class="fa-solid fa-arrow-left ms-1 rtl:rotate-180"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table-v2 v2-table">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() == 'ar' ? 'رقم الحجز / PNR' : 'Booking / PNR' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'النوع والخدمة' : 'Type & Service' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'حالة الحجز' : 'Status' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'حالة الدفع' : 'Payment' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'التاريخ' : 'Date' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentFlightBookings ?? [] as $booking)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);">#{{ $booking->booking_reference ?? $booking->id }}</div>
                            <small style="color: var(--primary); font-family: monospace; font-weight: 700;">{{ $booking->pnr ?? $booking->unique_id ?? '---' }}</small>
                        </td>
                        <td>
                            <div style="font-weight: 600;">{{ $booking->user->first_name ?? $booking->contact_name ?? 'زائر' }} {{ $booking->user->last_name ?? '' }}</div>
                            <small style="color: var(--text-muted);">{{ $booking->user->email ?? $booking->contact_email ?? '' }}</small>
                        </td>
                        <td>
                            <span class="badge-v2 badge-primary"><i class="fa-solid fa-plane"></i> {{ app()->getLocale() == 'ar' ? 'طيران' : 'Flight' }}</span>
                        </td>
                        <td>
                            <strong style="color: var(--text-main);">{{ number_format($booking->total_amount ?? 0, 2) }}</strong>
                            <small style="color: var(--text-muted);">{{ $booking->currency ?? 'SAR' }}</small>
                        </td>
                        <td>
                            @if(in_array(strtolower($booking->status ?? ''), ['confirmed', 'ticketed', 'completed']))
                                <span class="badge-v2 badge-success"><i class="fa-solid fa-check"></i> {{ $booking->status }}</span>
                            @elseif(in_array(strtolower($booking->status ?? ''), ['pending', 'processing']))
                                <span class="badge-v2 badge-warning"><i class="fa-solid fa-clock"></i> {{ $booking->status }}</span>
                            @else
                                <span class="badge-v2 badge-danger"><i class="fa-solid fa-xmark"></i> {{ $booking->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if(in_array(strtolower($booking->payment_status ?? ''), ['paid', 'completed']))
                                <span class="badge-v2 badge-success">{{ app()->getLocale() == 'ar' ? 'مدفوع' : 'Paid' }}</span>
                            @else
                                <span class="badge-v2 badge-warning">{{ $booking->payment_status ?? 'Pending' }}</span>
                            @endif
                        </td>
                        <td>
                            <small style="color: var(--text-muted);">{{ $booking->created_at ? $booking->created_at->format('Y-m-d H:i') : '---' }}</small>
                        </td>
                        <td style="text-align: center;">
                            <div class="v2-actions-inline">
                                <a href="{{ route('admin.bookings.flights.show', $booking->flightBooking->id ?? $booking->id) }}" class="v2-action-btn v2-action-btn-view" title="{{ app()->getLocale() == 'ar' ? 'عرض التفاصيل' : 'View Details' }}">
                                    <i class="fa-solid fa-eye text-primary"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            <i class="fa-regular fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                            {{ app()->getLocale() == 'ar' ? 'لا توجد حجوزات مسجلة حالياً.' : 'No bookings available.' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const flightData = {!! json_encode($flightSeries ?? [0,0,0,0,0,0,0,0,0,0,0,0]) !!};
    const hotelData = {!! json_encode($hotelSeries ?? [0,0,0,0,0,0,0,0,0,0,0,0]) !!};
    const shareData = {!! json_encode($shareSeries ?? [0,0,0]) !!};

    // 1. Revenue Analytics Chart
    const revenueOptions = {
        series: [{
            name: '{{ app()->getLocale() == "ar" ? "مبيعات الطيران" : "Flight Sales" }}',
            data: flightData
        }, {
            name: '{{ app()->getLocale() == "ar" ? "مبيعات الفنادق" : "Hotel Sales" }}',
            data: hotelData
        }],
        chart: {
            height: 320,
            type: 'area',
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        colors: ['#2563eb', '#10b981'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: {
            type: 'gradient',
            gradient: { opacityFrom: 0.45, opacityTo: 0.05 }
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
        tooltip: {
            y: { formatter: val => Number(val).toLocaleString() + ' SAR' }
        }
    };
    const revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
    revenueChart.render();

    // 2. Bookings Distribution Donut Chart
    const shareOptions = {
        series: (shareData[0] === 0 && shareData[1] === 0 && shareData[2] === 0) ? [1, 1, 1] : shareData,
        labels: [
            '{{ app()->getLocale() == "ar" ? "طيران" : "Flights" }}',
            '{{ app()->getLocale() == "ar" ? "فنادق" : "Hotels" }}',
            '{{ app()->getLocale() == "ar" ? "باقات" : "Trips" }}'
        ],
        chart: {
            type: 'donut',
            height: 320,
            fontFamily: 'inherit'
        },
        colors: ['#2563eb', '#10b981', '#f59e0b'],
        legend: { position: 'bottom' },
        dataLabels: { enabled: true }
    };
    const shareChart = new ApexCharts(document.querySelector("#shareChart"), shareOptions);
    shareChart.render();
});
</script>
@endpush
