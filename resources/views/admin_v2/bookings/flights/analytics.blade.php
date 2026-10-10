@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'تحليلات حجوزات الطيران' : 'Flight Analytics')

@section('content')
<div class="content-body-inner">
    <!-- Page Header -->
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">
                <i class="fa-solid fa-chart-line text-primary me-2"></i>
                {{ app()->getLocale() == 'ar' ? 'تحليلات حجوزات الطيران' : 'Flight Bookings Analytics' }}
            </h1>
            <p class="text-muted mb-0">
                {{ app()->getLocale() == 'ar' ? 'نظرة شاملة ومفصلة على أداء الحجوزات، الإيرادات، ومؤشرات النمو' : 'Comprehensive insights into booking trends, revenue, and airline metrics' }}
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.bookings.flights.index') }}" class="btn-v2 btn-secondary">
                <i class="fa-solid fa-list me-1"></i>
                {{ app()->getLocale() == 'ar' ? 'قائمة الحجوزات' : 'Bookings List' }}
            </a>
            <a href="{{ route('admin.bookings.flights.profits') }}" class="btn-v2 btn-primary">
                <i class="fa-solid fa-coins me-1"></i>
                {{ app()->getLocale() == 'ar' ? 'تقرير الأرباح' : 'Profits Report' }}
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="v2-card mb-4">
        <div class="v2-card-header d-flex align-items-center justify-content-between pb-3 border-bottom">
            <h6 class="mb-0 font-bold d-flex align-items-center gap-2">
                <i class="fa-solid fa-filter text-primary"></i>
                {{ app()->getLocale() == 'ar' ? 'تصفية التحليلات' : 'Filter Analytics' }}
            </h6>
        </div>
        <div class="v2-card-body pt-3">
            <form method="GET" action="{{ route('admin.bookings.flights.analytics') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'من تاريخ' : 'From Date' }}</label>
                        <input type="text" name="date_from" class="form-control" data-datepicker="true" placeholder="YYYY-MM-DD" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'إلى تاريخ' : 'To Date' }}</label>
                        <input type="text" name="date_to" class="form-control" data-datepicker="true" placeholder="YYYY-MM-DD" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'حالة الحجز' : 'Booking Status' }}</label>
                        <select name="status" class="form-select select2-init">
                            <option value="">{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'معلق' : 'Pending' }}</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'مؤكد' : 'Confirmed' }}</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'مدفوع' : 'Paid' }}</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'ملغي' : 'Cancelled' }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'شركة الطيران' : 'Airline' }}</label>
                        <input type="text" name="airline" class="form-control" placeholder="e.g. Flynas, Saudia" value="{{ request('airline') }}">
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                        <a href="{{ route('admin.bookings.flights.analytics') }}" class="btn-v2 btn-secondary">
                            <i class="fa-solid fa-rotate-left me-1"></i>
                            {{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Reset' }}
                        </a>
                        <button type="submit" class="btn-v2 btn-primary">
                            <i class="fa-solid fa-magnifying-glass me-1"></i>
                            {{ app()->getLocale() == 'ar' ? 'تطبيق الفلترة' : 'Apply Filters' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Metric Cards (6 Cards) -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="v2-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'إجمالي الحجوزات' : 'Total Bookings' }}</span>
                    <span class="w-8 h-8 rounded-lg bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-plane"></i>
                    </span>
                </div>
                <h3 class="font-extrabold text-xl mb-0">{{ number_format($stats['total'] ?? 0) }}</h3>
                <small class="text-muted text-xs">{{ app()->getLocale() == 'ar' ? 'حجز مسجل' : 'Bookings recorded' }}</small>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="v2-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'إجمالي الإيرادات' : 'Total Revenue' }}</span>
                    <span class="w-8 h-8 rounded-lg bg-success-subtle text-success d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-wallet"></i>
                    </span>
                </div>
                <h3 class="font-extrabold text-xl mb-0 text-success">{{ number_format($stats['revenue'] ?? 0, 2) }}</h3>
                <small class="text-muted text-xs">SAR</small>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="v2-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'الحجوزات المؤكدة' : 'Confirmed' }}</span>
                    <span class="w-8 h-8 rounded-lg bg-info-subtle text-info d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-circle-check"></i>
                    </span>
                </div>
                <h3 class="font-extrabold text-xl mb-0 text-info">{{ number_format($stats['confirmed'] ?? 0) }}</h3>
                <small class="text-muted text-xs">{{ app()->getLocale() == 'ar' ? 'مؤكد أو مدفوع' : 'Confirmed or Paid' }}</small>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="v2-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'قيد الانتظار' : 'Pending' }}</span>
                    <span class="w-8 h-8 rounded-lg bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-clock"></i>
                    </span>
                </div>
                <h3 class="font-extrabold text-xl mb-0 text-warning">{{ number_format($stats['pending'] ?? 0) }}</h3>
                <small class="text-muted text-xs">{{ app()->getLocale() == 'ar' ? 'بانتظار التأكيد' : 'Awaiting confirmation' }}</small>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="v2-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'الملغاة' : 'Cancelled' }}</span>
                    <span class="w-8 h-8 rounded-lg bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-ban"></i>
                    </span>
                </div>
                <h3 class="font-extrabold text-xl mb-0 text-danger">{{ number_format($stats['cancelled'] ?? 0) }}</h3>
                <small class="text-muted text-xs">{{ app()->getLocale() == 'ar' ? 'ملغاة أو مستردة' : 'Cancelled or Refunded' }}</small>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="v2-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'إجمالي الركاب' : 'Passengers' }}</span>
                    <span class="w-8 h-8 rounded-lg bg-purple-subtle text-purple d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-users"></i>
                    </span>
                </div>
                <h3 class="font-extrabold text-xl mb-0">{{ number_format($stats['passengers'] ?? 0) }}</h3>
                <small class="text-muted text-xs">{{ app()->getLocale() == 'ar' ? 'مسافر' : 'Passengers' }}</small>
            </div>
        </div>
    </div>

    <!-- Charts Row 1: Trend & Status -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="v2-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 font-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-chart-area text-primary"></i>
                        {{ app()->getLocale() == 'ar' ? 'اتجاه الحجوزات مع مرور الوقت' : 'Bookings Trend Over Time' }}
                    </h6>
                </div>
                <div style="height: 300px; position: relative;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="v2-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 font-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-success"></i>
                        {{ app()->getLocale() == 'ar' ? 'توزيع الحالات' : 'Status Distribution' }}
                    </h6>
                </div>
                <div style="height: 300px; position: relative;" class="d-flex align-items-center justify-content-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: Top Airlines & Top Destinations -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="v2-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 font-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-plane-up text-info"></i>
                        {{ app()->getLocale() == 'ar' ? 'أعلى شركات الطيران طلباً' : 'Top Airlines' }}
                    </h6>
                </div>
                <div style="height: 280px; position: relative;" class="d-flex align-items-center justify-content-center">
                    <canvas id="airlinesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="v2-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 font-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-location-dot text-danger"></i>
                        {{ app()->getLocale() == 'ar' ? 'أكثر الوجهات طلباً' : 'Top Destinations' }}
                    </h6>
                </div>
                <div style="height: 280px; position: relative;">
                    <canvas id="destinationsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row: Top Customers & Recent Bookings -->
    <div class="row g-4">
        <!-- Top Customers -->
        <div class="col-lg-6">
            <div class="v2-card overflow-hidden">
                <div class="v2-card-header p-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 font-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-trophy text-warning"></i>
                        {{ app()->getLocale() == 'ar' ? 'أفضل العملاء (حسب عدد الحجوزات)' : 'Top Customers' }}
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="v2-table w-100">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                                <th class="text-center">{{ app()->getLocale() == 'ar' ? 'الحجوزات' : 'Bookings' }}</th>
                                <th class="text-end">{{ app()->getLocale() == 'ar' ? 'إجمالي الإنفاق' : 'Total Spent' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topCustomers as $customer)
                            <tr>
                                <td>
                                    <div class="font-bold">{{ $customer->name }}</div>
                                    <small class="text-muted text-xs">{{ $customer->email }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge-v2 badge-primary">{{ $customer->bookings_count }}</span>
                                </td>
                                <td class="text-end font-bold text-success">
                                    {{ number_format($customer->total_spent, 2) }} {{ $customer->currency ?? 'SAR' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    {{ app()->getLocale() == 'ar' ? 'لا توجد بيانات متاحة' : 'No customer data available' }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="col-lg-6">
            <div class="v2-card overflow-hidden">
                <div class="v2-card-header p-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 font-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-primary"></i>
                        {{ app()->getLocale() == 'ar' ? 'أحدث حجوزات الطيران' : 'Recent Bookings' }}
                    </h6>
                    <a href="{{ route('admin.bookings.flights.index') }}" class="text-xs font-bold text-primary">
                        {{ app()->getLocale() == 'ar' ? 'عرض الكل' : 'View all' }} &rarr;
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="v2-table w-100">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() == 'ar' ? 'المرجع' : 'Reference' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'الراكب / المسار' : 'Passenger / Route' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                                <th class="text-end">{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.bookings.flights.show', $booking->id) }}" class="font-mono font-bold text-primary">
                                        {{ optional($booking->booking)->booking_reference ?? 'FB-' . $booking->id }}
                                    </a>
                                    <div class="text-xs text-muted">{{ $booking->created_at->format('Y-m-d') }}</div>
                                </td>
                                <td>
                                    <div class="font-bold text-xs">{{ $booking->first_name }} {{ $booking->last_name }}</div>
                                    <small class="text-muted">{{ $booking->origin }} &rarr; {{ $booking->destination }}</small>
                                </td>
                                <td>
                                    @php
                                        $s = optional($booking->booking)->status ?? 'pending';
                                    @endphp
                                    @if(in_array($s, ['confirmed', 'paid']))
                                        <span class="badge-v2 badge-success">{{ ucfirst($s) }}</span>
                                    @elseif($s === 'pending')
                                        <span class="badge-v2 badge-warning">{{ ucfirst($s) }}</span>
                                    @else
                                        <span class="badge-v2 badge-danger">{{ ucfirst($s) }}</span>
                                    @endif
                                </td>
                                <td class="text-end font-bold">
                                    {{ number_format($booking->total_amount, 2) }} {{ $booking->currency ?? 'SAR' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    {{ app()->getLocale() == 'ar' ? 'لا توجد حجوزات مسجلة' : 'No bookings found' }}
                                </td>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

    // Trend Chart
    const trendCtx = document.getElementById('trendChart')?.getContext('2d');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($stats['chartLabels'] ?? []) !!},
                datasets: [{
                    label: '{{ app()->getLocale() == "ar" ? "عدد الحجوزات" : "Bookings Count" }}',
                    data: {!! json_encode($stats['chartData'] ?? []) !!},
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#4f46e5',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: textColor }, grid: { display: false } },
                    y: { ticks: { color: textColor, precision: 0 }, grid: { color: gridColor }, beginAtZero: true }
                }
            }
        });
    }

    // Status Chart
    const statusCtx = document.getElementById('statusChart')?.getContext('2d');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($stats['statusLabels'] ?? []) !!},
                datasets: [{
                    data: {!! json_encode($stats['statusData'] ?? []) !!},
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#6366f1', '#06b6d4'],
                    borderWidth: 2,
                    borderColor: isDark ? '#1e293b' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor, padding: 14 } }
                },
                cutout: '68%'
            }
        });
    }

    // Top Airlines Chart
    const airlinesCtx = document.getElementById('airlinesChart')?.getContext('2d');
    if (airlinesCtx) {
        new Chart(airlinesCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($stats['airlinesLabels'] ?? []) !!},
                datasets: [{
                    data: {!! json_encode($stats['airlinesData'] ?? []) !!},
                    backgroundColor: ['#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#ec4899'],
                    borderWidth: 2,
                    borderColor: isDark ? '#1e293b' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor, padding: 14 } }
                },
                cutout: '60%'
            }
        });
    }

    // Destinations Chart
    const destCtx = document.getElementById('destinationsChart')?.getContext('2d');
    if (destCtx) {
        new Chart(destCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($stats['destinationsLabels'] ?? []) !!},
                datasets: [{
                    label: '{{ app()->getLocale() == "ar" ? "الحجوزات" : "Bookings" }}',
                    data: {!! json_encode($stats['destinationsData'] ?? []) !!},
                    backgroundColor: 'rgba(14, 165, 233, 0.85)',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: textColor }, grid: { display: false } },
                    y: { ticks: { color: textColor, precision: 0 }, grid: { color: gridColor }, beginAtZero: true }
                }
            }
        });
    }
});
</script>
@endpush
