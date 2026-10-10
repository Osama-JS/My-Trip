@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة وتفاصيل حجوزات الفنادق' : 'Hotel Bookings Management')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fa-solid fa-hotel text-success me-2"></i> {{ app()->getLocale() == 'ar' ? 'حجوزات الفنادق والقسائم' : 'Hotel Bookings & Vouchers' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">
                {{ app()->getLocale() == 'ar' ? 'متابعة وإدارة كافة حجوزات الفنادق، إصدار القسائم (Vouchers)، ومتابعة حالات الدفع.' : 'Monitor and manage hotel bookings, issue vouchers, and track payment statuses.' }}
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('admin.bookings.hotels.analytics') }}" class="btn btn-outline-primary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-chart-pie me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحليلات الفنادق' : 'Analytics' }}
            </a>
            <a href="{{ route('admin.bookings.hotels.profits') }}" class="btn btn-outline-success" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-dollar-sign me-1"></i> {{ app()->getLocale() == 'ar' ? 'أرباح الفنادق' : 'Profits' }}
            </a>
        </div>
    </div>

    <!-- 1. KPI Stats Cards Grid -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي حجوزات الفنادق' : 'Total Hotel Bookings' }}</div>
                <div class="stat-value">{{ number_format($stats['total'] ?? \App\Models\HotelBooking::count()) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-bed"></i> {{ app()->getLocale() == 'ar' ? 'كل الحجوزات' : 'All bookings' }}</div>
            </div>
            <div class="stat-icon icon-success">
                <i class="fa-solid fa-hotel"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'مؤكدة / مصدرة' : 'Confirmed / Vouchers' }}</div>
                <div class="stat-value text-success">{{ number_format($stats['confirmed'] ?? \App\Models\HotelBooking::whereIn('status', ['confirmed', 'voucher_issued', 'paid'])->count()) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() == 'ar' ? 'قسائم فعالة' : 'Active vouchers' }}</div>
            </div>
            <div class="stat-icon icon-primary">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'بانتظار التأكيد' : 'Pending Confirmation' }}</div>
                <div class="stat-value text-warning">{{ number_format($stats['pending'] ?? \App\Models\HotelBooking::where('status', 'pending')->count()) }}</div>
                <div class="stat-trend"><i class="fa-solid fa-clock"></i> {{ app()->getLocale() == 'ar' ? 'تحتاج إجراء' : 'Requires action' }}</div>
            </div>
            <div class="stat-icon icon-warning">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'ملغية' : 'Cancelled' }}</div>
                <div class="stat-value text-danger">{{ number_format($stats['cancelled'] ?? \App\Models\HotelBooking::where('status', 'cancelled')->count()) }}</div>
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
                    <i class="fa-regular fa-calendar me-1"></i> {{ app()->getLocale() == 'ar' ? 'نطاق تواريخ الإقامة' : 'Stay Dates Range' }}
                </label>
                <input type="text" class="daterange form-control" id="hotelDateRange" placeholder="{{ app()->getLocale() == 'ar' ? 'اختر التواريخ...' : 'Select Dates...' }}">
            </div>

            <div>
                <label class="form-label">
                    <i class="fa-solid fa-tags me-1"></i> {{ app()->getLocale() == 'ar' ? 'حالة الحجز' : 'Booking Status' }}
                </label>
                <select class="select2 form-select" id="hotelFilterStatus" data-placeholder="{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Statuses' }}">
                    <option value="">{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                    <option value="confirmed">{{ app()->getLocale() == 'ar' ? 'مؤكد (Confirmed)' : 'Confirmed' }}</option>
                    <option value="voucher_issued">{{ app()->getLocale() == 'ar' ? 'تم إصدار القسيمة (Voucher Issued)' : 'Voucher Issued' }}</option>
                    <option value="paid">{{ app()->getLocale() == 'ar' ? 'مدفوع (Paid)' : 'Paid' }}</option>
                    <option value="pending">{{ app()->getLocale() == 'ar' ? 'معلق (Pending)' : 'Pending' }}</option>
                    <option value="cancelled">{{ app()->getLocale() == 'ar' ? 'ملغي (Cancelled)' : 'Cancelled' }}</option>
                </select>
            </div>

            <div>
                <label class="form-label">
                    <i class="fa-solid fa-city me-1"></i> {{ app()->getLocale() == 'ar' ? 'بحث الفندق والمدينة' : 'Hotel or City' }}
                </label>
                <input type="text" class="form-control" id="hotelFilterKeyword" placeholder="{{ app()->getLocale() == 'ar' ? 'اكتب اسم الفندق أو المدينة...' : 'Type hotel name or city...' }}">
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-primary w-100" id="applyHotelFilterBtn" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700; height: 38px;">
                    <i class="fa-solid fa-filter me-1"></i> {{ app()->getLocale() == 'ar' ? 'تطبيق الفلترة' : 'Apply' }}
                </button>
                <button type="button" class="btn btn-icon" id="resetHotelFilterBtn" title="{{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Reset' }}" style="height: 38px; width: 38px; flex-shrink: 0;">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- 3. Hotel Bookings Custom Table Card -->
    <div class="admin-card p-0" style="overflow: hidden;">
        <div class="card-header-flex p-3 m-0" style="background: var(--bg-card);">
            <div>
                <div class="card-title">
                    <i class="fa-solid fa-list-check text-success me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة حجوزات الفنادق الصادرة' : 'Hotel Bookings List' }}
                </div>
            </div>
            <div class="v2-header-tools">
                <div style="position: relative; width: 220px;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; inset-inline-start: 10px; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: var(--text-light);"></i>
                    <input type="text" id="quickHotelSearch" class="form-control" style="padding-inline-start: 30px !important; font-size: 0.8rem; height: 34px !important;" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث سريع...' : 'Quick search...' }}">
                </div>
                <button class="btn btn-sm btn-icon" id="refreshHotelTableBtn" title="{{ app()->getLocale() == 'ar' ? 'تحديث الجدول' : 'Refresh' }}" style="height: 34px; width: 34px;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </div>

        <div class="v2-table-responsive">
            <table class="v2-table" id="hotelBookingsTable">
                <thead>
                    <tr>
                        <th data-sort="id">#</th>
                        <th data-sort="user">{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                        <th data-sort="hotel">{{ app()->getLocale() == 'ar' ? 'الفندق والمدينة' : 'Hotel & City' }}</th>
                        <th data-sort="dates">{{ app()->getLocale() == 'ar' ? 'تواريخ الإقامة' : 'Stay Dates' }}</th>
                        <th data-sort="amount">{{ app()->getLocale() == 'ar' ? 'المبلغ الإجمالي' : 'Total Amount' }}</th>
                        <th data-sort="status">{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th data-sort="created_at">{{ app()->getLocale() == 'ar' ? 'تاريخ الحجز' : 'Created' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded dynamically via V2Table -->
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hotelTable = new V2Table('#hotelBookingsTable', {
        ajax: {
            url: "{{ route('admin.bookings.hotels.data') }}",
            data: function() {
                return {
                    status: $('#hotelFilterStatus').val(),
                    date_range: $('#hotelDateRange').val(),
                    keyword: $('#hotelFilterKeyword').val()
                };
            }
        },
        searchInput: '#quickHotelSearch',
        perPage: 15,
        perPageOptions: [10, 25, 50, 100],
        defaultSort: { key: 'id', order: 'desc' },
        columns: [
            { data: 'id' },
            { data: 'user' },
            { data: 'hotel' },
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
                    if (s.includes('confirmed') || s.includes('voucher') || s.includes('paid') || s.includes('مؤكد') || s.includes('completed')) {
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

    $('#applyHotelFilterBtn').on('click', function() {
        hotelTable.reload(true);
        Notify.info('{{ app()->getLocale() == "ar" ? "تم تطبيق الفلترة" : "Filters applied" }}');
    });

    $('#resetHotelFilterBtn').on('click', function() {
        $('#hotelFilterStatus').val('').trigger('change');
        $('#hotelDateRange').val('');
        $('#hotelFilterKeyword').val('');
        $('#quickHotelSearch').val('');
        hotelTable.search('');
        hotelTable.reload(true);
        Notify.success('{{ app()->getLocale() == "ar" ? "تمت إعادة ضبط الفلاتر" : "Filters reset" }}');
    });

    $('#refreshHotelTableBtn').on('click', function() {
        hotelTable.reload();
        Notify.info('{{ app()->getLocale() == "ar" ? "تم تحديث البيانات" : "Data refreshed" }}');
    });
});
</script>
@endpush
