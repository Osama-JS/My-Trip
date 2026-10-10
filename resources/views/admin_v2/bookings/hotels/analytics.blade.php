@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'تحليلات حجوزات الفنادق' : 'Hotel Analytics')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                {{ app()->getLocale() == 'ar' ? 'تحليلات وإحصائيات حجوزات الفنادق' : 'Hotel Bookings & Revenue Analytics' }}
            </h1>
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                {{ app()->getLocale() == 'ar' ? 'متابعة حركة الإشغال، الإيرادات، وتوزيع الحجوزات حسب المدن والفنادق.' : 'Monitor occupancy volume, booking revenue trends, and top destinations.' }}
            </span>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.bookings.hotels.profits') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-hand-holding-dollar me-1"></i> {{ app()->getLocale() == 'ar' ? 'تقرير الأرباح' : 'Profits Report' }}
            </a>
            <a href="{{ route('admin.bookings.hotels.index') }}" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-hotel me-1"></i> {{ app()->getLocale() == 'ar' ? 'قائمة الحجوزات' : 'Hotel Bookings' }}
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="admin-card p-4 mb-4">
        <form method="GET" action="{{ route('admin.bookings.hotels.analytics') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'من تاريخ' : 'From Date' }}</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'إلى تاريخ' : 'To Date' }}</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'حالة الحجز' : 'Booking Status' }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ app()->getLocale() == 'ar' ? 'كل الحالات' : 'All Statuses' }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'معلق / قيد المراجعة' : 'Pending' }}</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'مؤكد' : 'Confirmed' }}</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'مدفوع' : 'Paid' }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'ملغي' : 'Cancelled' }}</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم الفندق' : 'Hotel Name' }}</label>
                    <input type="text" name="hotel" class="form-control" placeholder="{{ app()->getLocale() == 'ar' ? 'مثال: هيلتون' : 'e.g. Hilton' }}" value="{{ request('hotel') }}">
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('admin.bookings.hotels.analytics') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                        {{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Clear' }}
                    </a>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                        <i class="fa-solid fa-filter me-1"></i> {{ app()->getLocale() == 'ar' ? 'تطبيق الفلترة' : 'Apply Filters' }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 5 KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl col-md-4 col-sm-6">
            <div class="admin-card p-3 text-center">
                <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'إجمالي الحجوزات' : 'Total Bookings' }}</small>
                <div style="font-size: 1.5rem; font-weight: 900; color: var(--primary); margin: 0.25rem 0;">
                    {{ number_format($stats['total'] ?? 0) }}
                </div>
                <i class="fa-solid fa-bed text-muted opacity-50"></i>
            </div>
        </div>
        <div class="col-xl col-md-4 col-sm-6">
            <div class="admin-card p-3 text-center">
                <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'إجمالي الإيرادات' : 'Total Revenue' }}</small>
                <div style="font-size: 1.5rem; font-weight: 900; color: #22c55e; margin: 0.25rem 0;">
                    {{ number_format($stats['revenue'] ?? 0, 2) }}
                </div>
                <small class="text-muted" style="font-size: 0.75rem;">SAR</small>
            </div>
        </div>
        <div class="col-xl col-md-4 col-sm-6">
            <div class="admin-card p-3 text-center">
                <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'مؤكدة' : 'Confirmed' }}</small>
                <div style="font-size: 1.5rem; font-weight: 900; color: #16a34a; margin: 0.25rem 0;">
                    {{ number_format($stats['confirmed'] ?? 0) }}
                </div>
                <i class="fa-solid fa-circle-check text-success opacity-50"></i>
            </div>
        </div>
        <div class="col-xl col-md-4 col-sm-6">
            <div class="admin-card p-3 text-center">
                <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'معلقة' : 'Pending' }}</small>
                <div style="font-size: 1.5rem; font-weight: 900; color: #f59e0b; margin: 0.25rem 0;">
                    {{ number_format($stats['pending'] ?? 0) }}
                </div>
                <i class="fa-solid fa-clock text-warning opacity-50"></i>
            </div>
        </div>
        <div class="col-xl col-md-4 col-sm-6">
            <div class="admin-card p-3 text-center">
                <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'ملغاة' : 'Cancelled' }}</small>
                <div style="font-size: 1.5rem; font-weight: 900; color: #ef4444; margin: 0.25rem 0;">
                    {{ number_format($stats['cancelled'] ?? 0) }}
                </div>
                <i class="fa-solid fa-circle-xmark text-danger opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row g-4 mb-4">
        <!-- Trend Line Chart -->
        <div class="col-xl-8">
            <div class="admin-card p-4">
                <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1rem;">
                    <i class="fa-solid fa-chart-line text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'منحنى حجوزات الفنادق' : 'Hotel Bookings Trend' }}
                </h6>
                <div id="hotelTrendApexChart" style="min-height: 280px;"></div>
            </div>
        </div>
        <!-- Top Hotels Doughnut -->
        <div class="col-xl-4">
            <div class="admin-card p-4">
                <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1rem;">
                    <i class="fa-solid fa-hotel text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'أكثر الفنادق حجزاً' : 'Top Hotels' }}
                </h6>
                <div id="topHotelsApexChart" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-4 mb-4">
        <!-- Top Cities Bar Chart -->
        <div class="col-xl-8">
            <div class="admin-card p-4">
                <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1rem;">
                    <i class="fa-solid fa-city text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'أكثر المدن طلباً' : 'Top Cities' }}
                </h6>
                <div id="topCitiesApexChart" style="min-height: 280px;"></div>
            </div>
        </div>
        <!-- Status Doughnut -->
        <div class="col-xl-4">
            <div class="admin-card p-4">
                <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1rem;">
                    <i class="fa-solid fa-chart-pie text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'توزيع حالات الحجز' : 'Status Distribution' }}
                </h6>
                <div id="hotelStatusApexChart" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    <!-- Top Customers Table -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-crown text-warning me-2"></i> {{ app()->getLocale() == 'ar' ? 'أعلى العملاء حجوزات للفنادق المؤكدة' : 'Top Customers (Confirmed Bookings)' }}
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                <thead style="background: rgba(0,0,0,0.02);">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'اسم العميل' : 'Customer Name' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email' }}</th>
                        <th class="text-center">{{ app()->getLocale() == 'ar' ? 'عدد الحجوزات' : 'Total Bookings' }}</th>
                        <th class="text-end">{{ app()->getLocale() == 'ar' ? 'إجمالي الإنفاق' : 'Total Spent' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topCustomers ?? [] as $index => $customer)
                        <tr>
                            <td><strong>{{ $index + 1 }}</strong></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 800;">
                                        {{ strtoupper(substr($customer['name'], 0, 1)) }}
                                    </div>
                                    <strong style="color: var(--text-main);">{{ $customer['name'] }}</strong>
                                </div>
                            </td>
                            <td class="text-muted">{{ $customer['email'] ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $customer['bookings_count'] }}</span>
                            </td>
                            <td class="text-end font-bold text-main">
                                {{ number_format($customer['total_spent'], 2) }} <small class="text-muted">{{ $customer['currency'] }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات متاحة وفقاً للفلترة المحددة.' : 'No confirmed bookings found for the selected filters.' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Trend Area Chart
        const trendLabels = {!! json_encode($stats['chartLabels'] ?? []) !!};
        const trendData = {!! json_encode($stats['chartData'] ?? []) !!};
        if (document.querySelector("#hotelTrendApexChart")) {
            new ApexCharts(document.querySelector("#hotelTrendApexChart"), {
                chart: { type: 'area', height: 280, toolbar: { show: false } },
                series: [{ name: '{{ app()->getLocale() == "ar" ? "الحجوزات" : "Bookings" }}', data: trendData }],
                xaxis: { categories: trendLabels },
                colors: ['#0d6efd'],
                stroke: { curve: 'smooth', width: 3 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } }
            }).render();
        }

        // 2. Top Hotels Doughnut
        const hotelsLabels = {!! json_encode($stats['hotelsLabels'] ?? []) !!};
        const hotelsData = {!! json_encode($stats['hotelsData'] ?? []) !!};
        if (document.querySelector("#topHotelsApexChart") && hotelsData.length > 0) {
            new ApexCharts(document.querySelector("#topHotelsApexChart"), {
                chart: { type: 'donut', height: 280 },
                series: hotelsData,
                labels: hotelsLabels,
                colors: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6c757d'],
                legend: { position: 'bottom' }
            }).render();
        }

        // 3. Top Cities Bar Chart
        const citiesLabels = {!! json_encode($stats['citiesLabels'] ?? []) !!};
        const citiesData = {!! json_encode($stats['citiesData'] ?? []) !!};
        if (document.querySelector("#topCitiesApexChart")) {
            new ApexCharts(document.querySelector("#topCitiesApexChart"), {
                chart: { type: 'bar', height: 280, toolbar: { show: false } },
                series: [{ name: '{{ app()->getLocale() == "ar" ? "الحجوزات" : "Bookings" }}', data: citiesData }],
                xaxis: { categories: citiesLabels },
                colors: ['#198754'],
                plotOptions: { bar: { borderRadius: 6, horizontal: false } }
            }).render();
        }

        // 4. Status Pie/Donut Chart
        const statusLabels = {!! json_encode($stats['statusLabels'] ?? []) !!};
        const statusData = {!! json_encode($stats['statusData'] ?? []) !!};
        if (document.querySelector("#hotelStatusApexChart") && statusData.length > 0) {
            new ApexCharts(document.querySelector("#hotelStatusApexChart"), {
                chart: { type: 'donut', height: 280 },
                series: statusData,
                labels: statusLabels,
                colors: ['#198754', '#ffc107', '#dc3545', '#0d6efd'],
                legend: { position: 'bottom' }
            }).render();
        }
    });
</script>
@endpush
