@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة وتفاصيل حجوزات الطيران' : 'Flight Bookings Management')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fa-solid fa-plane-departure text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'حجوزات الطيران والتذاكر' : 'Flight Bookings & Tickets' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">
                {{ app()->getLocale() == 'ar' ? 'متابعة وإدارة كافة حجوزات الطيران الصادرة والتحكم بالتذاكر وحالات الـ PNR.' : 'Monitor and manage all issued flight bookings, eTickets, and PNR statuses.' }}
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('admin.bookings.flights.analytics') }}" class="btn btn-outline-primary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-chart-pie me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحليلات الطيران' : 'Analytics' }}
            </a>
            <a href="{{ route('admin.bookings.flights.profits') }}" class="btn btn-outline-success" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-dollar-sign me-1"></i> {{ app()->getLocale() == 'ar' ? 'تقرير الأرباح' : 'Profits' }}
            </a>
        </div>
    </div>

    <!-- 1. KPI Stats Cards Grid -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي الحجوزات' : 'Total Bookings' }}</div>
                <div class="stat-value">{{ number_format($stats['total'] ?? \App\Models\Booking::count()) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-plane"></i> {{ app()->getLocale() == 'ar' ? 'كل الرحلات' : 'All flights' }}</div>
            </div>
            <div class="stat-icon icon-primary">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'مؤكدة / مصدرة' : 'Confirmed / Ticketed' }}</div>
                <div class="stat-value text-success">{{ number_format($stats['confirmed'] ?? \App\Models\Booking::whereIn('status', ['confirmed', 'ticketed', 'completed'])->count()) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() == 'ar' ? 'تذاكر فعالة' : 'Active tickets' }}</div>
            </div>
            <div class="stat-icon icon-success">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'بانتظار التأكيد' : 'Pending Bookings' }}</div>
                <div class="stat-value text-warning">{{ number_format($stats['pending'] ?? \App\Models\Booking::where('status', 'pending')->count()) }}</div>
                <div class="stat-trend"><i class="fa-solid fa-clock"></i> {{ app()->getLocale() == 'ar' ? 'تحتاج إجراء' : 'Requires action' }}</div>
            </div>
            <div class="stat-icon icon-warning">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'ملغية / مسترجعة' : 'Cancelled / Refunded' }}</div>
                <div class="stat-value text-danger">{{ number_format($stats['cancelled'] ?? \App\Models\Booking::whereIn('status', ['cancelled', 'failed', 'refunded'])->count()) }}</div>
                <div class="stat-trend trend-down"><i class="fa-solid fa-circle-xmark"></i> {{ app()->getLocale() == 'ar' ? 'ملغية' : 'Cancelled' }}</div>
            </div>
            <div class="stat-icon icon-danger">
                <i class="fa-solid fa-ban"></i>
            </div>
        </div>
    </div>

    <!-- 2. Modern Filter Card -->
    <div class="v2-filter-card">
        <div class="v2-filter-grid">
            <div>
                <label class="form-label">
                    <i class="fa-regular fa-calendar me-1"></i> {{ app()->getLocale() == 'ar' ? 'نطاق التاريخ' : 'Date Range' }}
                </label>
                <input type="text" class="daterange form-control" id="filterDateRange" placeholder="{{ app()->getLocale() == 'ar' ? 'اختر تاريخ البداية والنهاية...' : 'Select Date Range...' }}">
            </div>

            <div>
                <label class="form-label">
                    <i class="fa-solid fa-tags me-1"></i> {{ app()->getLocale() == 'ar' ? 'حالة الحجز' : 'Booking Status' }}
                </label>
                <select class="select2 form-select" id="filterStatus" data-placeholder="{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Statuses' }}">
                    <option value="">{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                    <option value="confirmed">{{ app()->getLocale() == 'ar' ? 'مؤكد (Confirmed)' : 'Confirmed' }}</option>
                    <option value="ticketed">{{ app()->getLocale() == 'ar' ? 'تذكرة صادرة (Ticketed)' : 'Ticketed' }}</option>
                    <option value="pending">{{ app()->getLocale() == 'ar' ? 'معلق (Pending)' : 'Pending' }}</option>
                    <option value="cancelled">{{ app()->getLocale() == 'ar' ? 'ملغي (Cancelled)' : 'Cancelled' }}</option>
                </select>
            </div>

            <div>
                <label class="form-label">
                    <i class="fa-solid fa-plane me-1"></i> {{ app()->getLocale() == 'ar' ? 'شركة الطيران' : 'Airline' }}
                </label>
                <select class="select2 form-select" id="filterAirline" data-placeholder="{{ app()->getLocale() == 'ar' ? 'جميع خطوط الطيران' : 'All Airlines' }}">
                    <option value="">{{ app()->getLocale() == 'ar' ? 'جميع خطوط الطيران' : 'All Airlines' }}</option>
                    <option value="SV">السعودية (Saudia - SV)</option>
                    <option value="XY">طيران ناس (flynas - XY)</option>
                    <option value="F3">طيران أديل (flyadeal - F3)</option>
                    <option value="EK">طيران الإمارات (Emirates - EK)</option>
                    <option value="QR">القطرية (Qatar Airways - QR)</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-primary w-100" id="applyFilterBtn" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700; height: 38px;">
                    <i class="fa-solid fa-filter me-1"></i> {{ app()->getLocale() == 'ar' ? 'تطبيق الفلترة' : 'Apply' }}
                </button>
                <button type="button" class="btn btn-icon" id="resetFilterBtn" title="{{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Reset' }}" style="height: 38px; width: 38px; flex-shrink: 0;">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- 3. Flight Bookings V2 Custom Table Card -->
    <div class="admin-card p-0" style="overflow: hidden;">
        <div class="card-header-flex p-3 m-0" style="background: var(--bg-card);">
            <div>
                <div class="card-title">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة حجوزات الطيران الصادرة' : 'Flight Bookings List' }}
                </div>
            </div>
            <div class="v2-header-tools">
                <div style="position: relative; width: 220px;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; inset-inline-start: 10px; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: var(--text-light);"></i>
                    <input type="text" id="quickTableSearch" class="form-control" style="padding-inline-start: 30px !important; font-size: 0.8rem; height: 34px !important;" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث سريع في الجدول...' : 'Quick search...' }}">
                </div>
                <button class="btn btn-sm btn-icon" id="refreshTableBtn" title="{{ app()->getLocale() == 'ar' ? 'تحديث الجدول' : 'Refresh' }}" style="height: 34px; width: 34px;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </div>

        <div class="v2-table-responsive">
            <table class="v2-table" id="flightBookingsTable">
                <thead>
                    <tr>
                        <th data-sort="id">#</th>
                        <th data-sort="reference">{{ app()->getLocale() == 'ar' ? 'رقم الحجز / PNR' : 'Reference / PNR' }}</th>
                        <th data-sort="user">{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                        <th data-sort="airline">{{ app()->getLocale() == 'ar' ? 'شركة الطيران' : 'Airline' }}</th>
                        <th data-sort="route">{{ app()->getLocale() == 'ar' ? 'المسار' : 'Route' }}</th>
                        <th data-sort="dates">{{ app()->getLocale() == 'ar' ? 'التواريخ' : 'Dates' }}</th>
                        <th data-sort="amount">{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                        <th data-sort="status">{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th data-sort="created_at">{{ app()->getLocale() == 'ar' ? 'تاريخ الإنشاء' : 'Created' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded dynamically via V2Table engine -->
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const flightTable = new V2Table('#flightBookingsTable', {
        ajax: {
            url: "{{ route('admin.bookings.flights.data') }}",
            data: function() {
                return {
                    status: $('#filterStatus').val(),
                    airline: $('#filterAirline').val(),
                    date_range: $('#filterDateRange').val()
                };
            }
        },
        searchInput: '#quickTableSearch',
        perPage: 15,
        perPageOptions: [10, 25, 50, 100],
        defaultSort: { key: 'id', order: 'desc' },
        columns: [
            { data: 'id' },
            { 
                data: 'reference',
                render: function(data) {
                    return `<span style="font-weight: 700; color: var(--primary); font-family: monospace;">${data || '—'}</span>`;
                }
            },
            { data: 'user' },
            { data: 'airline' },
            { data: 'route' },
            { data: 'dates' },
            { 
                data: 'amount',
                render: function(data) {
                    return `<span style="font-weight: 800; color: var(--text-main);">${data || '0'}</span>`;
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    const s = (data || '').toLowerCase();
                    if (s.includes('confirmed') || s.includes('ticketed') || s.includes('مؤكد') || s.includes('completed')) {
                        return `<span class="badge-v2 badge-success"><i class="fa-solid fa-check me-1"></i>${data}</span>`;
                    } else if (s.includes('pending') || s.includes('معلق') || s.includes('processing')) {
                        return `<span class="badge-v2 badge-warning"><i class="fa-solid fa-clock me-1"></i>${data}</span>`;
                    } else {
                        return `<span class="badge-v2 badge-danger"><i class="fa-solid fa-xmark me-1"></i>${data}</span>`;
                    }
                }
            },
            { data: 'created_at' },
            { 
                data: 'actions', 
                orderable: false, 
                searchable: false, 
                className: 'text-center' 
            }
        ]
    });

    $('#applyFilterBtn').on('click', function() {
        flightTable.reload(true);
        Notify.info('{{ app()->getLocale() == "ar" ? "تم تطبيق الفلترة" : "Filters applied" }}');
    });

    $('#resetFilterBtn').on('click', function() {
        $('#filterStatus').val('').trigger('change');
        $('#filterAirline').val('').trigger('change');
        $('#filterDateRange').val('');
        $('#quickTableSearch').val('');
        flightTable.search('');
        flightTable.reload(true);
        Notify.success('{{ app()->getLocale() == "ar" ? "تمت إعادة ضبط الفلاتر" : "Filters reset" }}');
    });

    $('#refreshTableBtn').on('click', function() {
        flightTable.reload();
        Notify.info('{{ app()->getLocale() == "ar" ? "تم تحديث البيانات" : "Data refreshed" }}');
    });
});
</script>
@endpush
