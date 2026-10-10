@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة حجوزات الرحلات السياحية' : 'Trip Bookings Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fa-solid fa-suitcase-rolling text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'حجوزات الرحلات السياحية' : 'Trip Bookings' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">
                {{ app()->getLocale() == 'ar' ? 'متابعة حجوزات وتذاكر الرحلات، الحالات المالية، وتفاصيل المسافرين.' : 'Review and manage tour package reservations and passenger details.' }}
            </p>
        </div>
    </div>

    <!-- KPI Stats -->
    <div class="stat-grid mb-4">
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي الحجوزات' : 'Total Bookings' }}</div>
                <div class="stat-value">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-calendar-check"></i> {{ app()->getLocale() == 'ar' ? 'كل الحجوزات' : 'All bookings' }}</div>
            </div>
            <div class="stat-icon icon-primary">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'مؤكدة' : 'Confirmed' }}</div>
                <div class="stat-value text-success">{{ number_format($stats['confirmed'] ?? 0) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() == 'ar' ? 'مكتملة' : 'Confirmed' }}</div>
            </div>
            <div class="stat-icon icon-success">
                <i class="fa-solid fa-check-circle"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'قيد المراجعة' : 'Pending' }}</div>
                <div class="stat-value text-warning">{{ number_format($stats['pending'] ?? 0) }}</div>
                <div class="stat-trend"><i class="fa-solid fa-clock"></i> {{ app()->getLocale() == 'ar' ? 'قيد الانتظار' : 'Pending' }}</div>
            </div>
            <div class="stat-icon icon-warning">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'ملغية' : 'Cancelled' }}</div>
                <div class="stat-value text-danger">{{ number_format($stats['cancelled'] ?? 0) }}</div>
                <div class="stat-trend trend-down"><i class="fa-solid fa-circle-xmark"></i> {{ app()->getLocale() == 'ar' ? 'ملغية' : 'Cancelled' }}</div>
            </div>
            <div class="stat-icon icon-danger">
                <i class="fa-solid fa-times-circle"></i>
            </div>
        </div>
    </div>

    <!-- Filter Card (Separated & Non-Overlapping) -->
    <div class="v2-filter-card mb-4">
        <div class="v2-filter-grid">
            <div style="grid-column: span 2;">
                <label class="form-label">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> {{ app()->getLocale() == 'ar' ? 'بحث سريع' : 'Search Booking' }}
                </label>
                <input type="text" id="custom-search" class="form-control" placeholder="{{ app()->getLocale() == 'ar' ? 'رقم الحجز، اسم العميل أو عنوان الرحلة...' : 'Search by user, trip, ID...' }}">
            </div>
        </div>
    </div>

    <!-- Bookings Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="card-header-flex p-3 m-0" style="background: var(--bg-card); border-bottom: 1px solid var(--border-color);">
            <div>
                <div class="card-title">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة حجوزات الرحلات السياحية' : 'Customer Bookings List' }}
                </div>
            </div>
            <div class="v2-header-tools">
                <button class="btn btn-sm btn-icon" id="refreshBookingsBtn" title="{{ app()->getLocale() == 'ar' ? 'تحديث الجدول' : 'Refresh' }}" style="height: 34px; width: 34px;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </div>

        <div class="v2-table-responsive">
            <table id="trip-bookings-table" class="v2-table w-full">
                <thead>
                    <tr>
                        <th data-sort="id" class="w-16">#</th>
                        <th data-sort="user">{{ app()->getLocale() == 'ar' ? 'العميل' : 'User' }}</th>
                        <th data-sort="trip">{{ app()->getLocale() == 'ar' ? 'الرحلة' : 'Trip' }}</th>
                        <th data-sort="price">{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Price' }}</th>
                        <th data-sort="tickets">{{ app()->getLocale() == 'ar' ? 'التذاكر' : 'Tickets' }}</th>
                        <th data-sort="status">{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th data-sort="created_at">{{ app()->getLocale() == 'ar' ? 'التاريخ' : 'Date' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let tripBookingsTable;

    document.addEventListener('DOMContentLoaded', function() {
        tripBookingsTable = new V2Table('#trip-bookings-table', {
            ajax: {
                url: "{{ parse_url(route('admin.trip-bookings.data'), PHP_URL_PATH) }}"
            },
            searchInput: '#custom-search',
            perPage: 15,
            perPageOptions: [10, 25, 50, 100],
            defaultSort: { key: 'id', order: 'desc' },
            columns: [
                { data: 'id', className: 'font-mono font-bold' },
                { data: 'user' },
                { data: 'trip' },
                { data: 'price', className: 'font-bold' },
                { data: 'tickets' },
                { data: 'status' },
                { data: 'created_at' },
                { data: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ]
        });

        $('#refreshBookingsBtn').on('click', function() {
            tripBookingsTable.reload();
            Notify.info('{{ app()->getLocale() == "ar" ? "تم تحديث البيانات" : "Data refreshed" }}');
        });
    });

    function changeBookingStatus(id, status) {
        Notify.confirm({
            title: '{{ __("Change Booking Status?") }}',
            text: '{{ __("Are you sure you want to update this booking status?") }}',
            confirmText: '{{ __("Yes, Update") }}'
        }).then((res) => {
            if (res.isConfirmed) {
                $.ajax({
                    url: "{{ url('admin/trip-bookings') }}/" + id + "/status",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            tripBookingsTable.reload();
                            Notify.success(response.message);
                        } else {
                            Notify.error(response.message || '{{ __("Failed to update status") }}');
                        }
                    },
                    error: function(xhr) {
                        Notify.error(xhr.responseJSON?.message || '{{ __("Something went wrong") }}');
                    }
                });
            }
        });
    }
</script>
@endpush
