@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'تحليلات البرامج السياحية' : 'Tour Packages Analytics')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                {{ app()->getLocale() == 'ar' ? 'تحليلات أداء ومبيعات البرامج السياحية' : 'Tour Packages & Trips Analytics' }}
            </h1>
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                {{ app()->getLocale() == 'ar' ? 'تقارير المبيعات، نمو الحجوزات، والشركات الأكثر نشاطاً.' : 'Monitor sales trends, package booking volume, and top providers.' }}
            </span>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.trips.profits') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-hand-holding-dollar me-1"></i> {{ app()->getLocale() == 'ar' ? 'تقرير الأرباح' : 'Profits Report' }}
            </a>
            <a href="{{ route('admin.trips.index') }}" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-plane-departure me-1"></i> {{ app()->getLocale() == 'ar' ? 'قائمة البرامج السياحية' : 'Tour Packages' }}
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="admin-card p-4 mb-4">
        <form method="GET" action="{{ route('admin.trips.analytics') }}">
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
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'قيد الانتظار' : 'Pending' }}</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'مؤكد / مكتمل' : 'Confirmed / Completed' }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'ملغي' : 'Cancelled' }}</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الشركة' : 'Company' }}</label>
                    <select name="company_id" class="form-select">
                        <option value="">{{ app()->getLocale() == 'ar' ? 'كل الشركات' : 'All Companies' }}</option>
                        @foreach($companies ?? [] as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('admin.trips.analytics') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                        {{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Clear' }}
                    </a>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                        <i class="fa-solid fa-filter me-1"></i> {{ app()->getLocale() == 'ar' ? 'تطبيق الفلترة' : 'Apply Filters' }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 6 KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="admin-card p-3 text-center">
                <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'إجمالي الحجوزات' : 'Total Bookings' }}</small>
                <div style="font-size: 1.5rem; font-weight: 900; color: var(--primary); margin: 0.25rem 0;">
                    {{ number_format($stats['total'] ?? 0) }}
                </div>
                <i class="fa-solid fa-calendar-check text-muted opacity-50"></i>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="admin-card p-3 text-center">
                <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'إجمالي المبيعات' : 'Total Sales' }}</small>
                <div style="font-size: 1.5rem; font-weight: 900; color: #22c55e; margin: 0.25rem 0;">
                    {{ number_format($stats['revenue'] ?? 0, 2) }}
                </div>
                <small class="text-muted" style="font-size: 0.75rem;">SAR</small>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="admin-card p-3 text-center">
                <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'أرباح المنصة' : 'Platform Profit' }}</small>
                <div style="font-size: 1.5rem; font-weight: 900; color: #22c55e; margin: 0.25rem 0;">
                    +{{ number_format($stats['profit'] ?? 0, 2) }}
                </div>
                <small class="text-muted" style="font-size: 0.75rem;">SAR</small>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="admin-card p-3 text-center">
                <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'إجمالي المسافرين' : 'Total Travelers' }}</small>
                <div style="font-size: 1.5rem; font-weight: 900; color: #0ea5e9; margin: 0.25rem 0;">
                    {{ number_format($stats['passengers'] ?? 0) }}
                </div>
                <i class="fa-solid fa-users text-muted opacity-50"></i>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="admin-card p-3 text-center">
                <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'حجوزات مؤكدة' : 'Confirmed' }}</small>
                <div style="font-size: 1.5rem; font-weight: 900; color: #16a34a; margin: 0.25rem 0;">
                    {{ number_format($stats['confirmed'] ?? 0) }}
                </div>
                <i class="fa-solid fa-circle-check text-success opacity-50"></i>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="admin-card p-3 text-center">
                <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'حجوزات ملغاة' : 'Cancelled' }}</small>
                <div style="font-size: 1.5rem; font-weight: 900; color: #ef4444; margin: 0.25rem 0;">
                    {{ number_format($stats['cancelled'] ?? 0) }}
                </div>
                <i class="fa-solid fa-circle-xmark text-danger opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row g-4 mb-4">
        <!-- Trend Chart -->
        <div class="col-xl-8">
            <div class="admin-card p-4">
                <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1rem;">
                    <i class="fa-solid fa-chart-line text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'منحنى حجوزات البرامج السياحية' : 'Tour Bookings Trend' }}
                </h6>
                <div id="bookingsTrendApexChart" style="min-height: 280px;"></div>
            </div>
        </div>
        <!-- Status Doughnut -->
        <div class="col-xl-4">
            <div class="admin-card p-4">
                <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1rem;">
                    <i class="fa-solid fa-chart-pie text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'توزيع حالات الحجز' : 'Booking Statuses' }}
                </h6>
                <div id="statusApexChart" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="admin-card p-4">
                <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1rem;">
                    <i class="fa-solid fa-building text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'أكثر الشركات تنظيماً للرحلات' : 'Top Organizing Companies' }}
                </h6>
                <div id="companiesApexChart" style="min-height: 280px;"></div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="admin-card p-4">
                <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1rem;">
                    <i class="fa-solid fa-cubes text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'أكثر البرامج السياحية طلباً' : 'Top Requested Tour Packages' }}
                </h6>
                <div id="packagesApexChart" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    <!-- Tables: Top Customers & Recent Bookings -->
    <div class="row g-4 mb-4">
        <!-- Top Customers -->
        <div class="col-xl-6">
            <div class="admin-card p-0" style="overflow: hidden;">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background: var(--bg-card); border-color: var(--border-light) !important;">
                    <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                        <i class="fa-solid fa-trophy text-warning me-2"></i> {{ app()->getLocale() == 'ar' ? 'أعلى العملاء حجزاً للرحلات' : 'Top Customers (Tour Packages)' }}
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                        <thead style="background: rgba(0,0,0,0.02);">
                            <tr>
                                <th>{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'الحجوزات' : 'Bookings' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'إجمالي الإنفاق' : 'Total Spent' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'أرباح المنصة' : 'Platform Profit' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topCustomers ?? [] as $customer)
                                <tr>
                                    <td>
                                        <div class="font-bold text-main">{{ $customer->name }}</div>
                                        <small class="text-muted">{{ $customer->email }}</small>
                                    </td>
                                    <td><span class="badge bg-primary">{{ $customer->bookings_count }}</span></td>
                                    <td class="font-bold text-main">{{ number_format($customer->total_spent, 2) }} SAR</td>
                                    <td class="font-bold text-success">+{{ number_format($customer->platform_profit, 2) }} SAR</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات متاحة' : 'No customers data available' }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="col-xl-6">
            <div class="admin-card p-0" style="overflow: hidden;">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background: var(--bg-card); border-color: var(--border-light) !important;">
                    <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                        <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'أحدث حجوزات البرامج' : 'Recent Tour Bookings' }}
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                        <thead style="background: rgba(0,0,0,0.02);">
                            <tr>
                                <th>{{ app()->getLocale() == 'ar' ? 'المرجع' : 'Reference' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'البرنامج السياحي' : 'Tour Package' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings ?? [] as $rb)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.trip_bookings.show', $rb->id) }}" class="font-bold text-primary">#TRIP-{{ str_pad($rb->id, 5, '0', STR_PAD_LEFT) }}</a>
                                    </td>
                                    <td>{{ optional($rb->trip)->title_ar ?? (optional($rb->trip)->title ?? (app()->getLocale() == 'ar' ? 'رحلة' : 'Tour')) }}</td>
                                    <td>{{ optional($rb->user)->full_name ?? (app()->getLocale() == 'ar' ? 'زائر' : 'Guest') }}</td>
                                    <td class="font-bold text-main">{{ number_format($rb->total_price, 2) }} SAR</td>
                                    <td>
                                        @php
                                            $stBg = match($rb->status) {
                                                'confirmed', 'paid' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                                default => 'bg-warning text-dark'
                                            };
                                        @endphp
                                        <span class="badge {{ $stBg }}">{{ __(ucfirst($rb->status ?? 'pending')) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">{{ app()->getLocale() == 'ar' ? 'لا توجد حجوزات حديثة' : 'No recent bookings' }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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
        if (document.querySelector("#bookingsTrendApexChart")) {
            new ApexCharts(document.querySelector("#bookingsTrendApexChart"), {
                chart: { type: 'area', height: 280, toolbar: { show: false } },
                series: [{ name: '{{ app()->getLocale() == "ar" ? "عدد الحجوزات" : "Bookings" }}', data: trendData }],
                xaxis: { categories: trendLabels },
                colors: ['#041741'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } }
            }).render();
        }

        // 2. Status Donut Chart
        const statusLabels = {!! json_encode($stats['statusLabels'] ?? []) !!};
        const statusData = {!! json_encode($stats['statusData'] ?? []) !!};
        if (document.querySelector("#statusApexChart") && statusData.length > 0) {
            new ApexCharts(document.querySelector("#statusApexChart"), {
                chart: { type: 'donut', height: 280 },
                series: statusData,
                labels: statusLabels,
                colors: ['#10b981', '#f59e0b', '#ef4444', '#64748b'],
                legend: { position: 'bottom' }
            }).render();
        }

        // 3. Top Companies Bar Chart
        const companiesLabels = {!! json_encode($stats['companiesLabels'] ?? []) !!};
        const companiesData = {!! json_encode($stats['companiesData'] ?? []) !!};
        if (document.querySelector("#companiesApexChart")) {
            new ApexCharts(document.querySelector("#companiesApexChart"), {
                chart: { type: 'bar', height: 280, toolbar: { show: false } },
                series: [{ name: '{{ app()->getLocale() == "ar" ? "الحجوزات" : "Bookings" }}', data: companiesData }],
                xaxis: { categories: companiesLabels },
                colors: ['#3b82f6'],
                plotOptions: { bar: { borderRadius: 6, horizontal: true } }
            }).render();
        }

        // 4. Top Packages Bar Chart
        const packagesLabels = {!! json_encode($stats['packagesLabels'] ?? []) !!};
        const packagesData = {!! json_encode($stats['packagesData'] ?? []) !!};
        if (document.querySelector("#packagesApexChart")) {
            new ApexCharts(document.querySelector("#packagesApexChart"), {
                chart: { type: 'bar', height: 280, toolbar: { show: false } },
                series: [{ name: '{{ app()->getLocale() == "ar" ? "الطلبات" : "Requests" }}', data: packagesData }],
                xaxis: { categories: packagesLabels },
                colors: ['#041741'],
                plotOptions: { bar: { borderRadius: 6, horizontal: false } }
            }).render();
        }
    });
</script>
@endpush
