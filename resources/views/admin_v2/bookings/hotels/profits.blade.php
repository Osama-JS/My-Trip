@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'تقرير أرباح حجوزات الفنادق' : 'Hotel Profits Report')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                {{ app()->getLocale() == 'ar' ? 'تقرير أرباح وهوامش حجوزات الفنادق' : 'Hotel Bookings Profit Report' }}
            </h1>
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                {{ app()->getLocale() == 'ar' ? 'متابعة هوامش الربح وصافي أرباح المنصة من حجوزات الفنادق.' : 'Analyze platform margins and net revenue from hotel reservations.' }}
            </span>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.bookings.hotels.analytics') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-chart-line me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحليلات الفنادق' : 'Hotel Analytics' }}
            </a>
            <a href="{{ route('admin.bookings.hotels.index') }}" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-hotel me-1"></i> {{ app()->getLocale() == 'ar' ? 'قائمة الحجوزات' : 'Hotel Bookings' }}
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="admin-card p-4 mb-4">
        <form action="{{ route('admin.bookings.hotels.profits') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-4 col-sm-6">
                    <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'من تاريخ' : 'From Date' }}</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'إلى تاريخ' : 'To Date' }}</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-4 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill" style="background: var(--primary); border: none; font-weight: 700;">
                        <i class="fa-solid fa-filter me-1"></i> {{ app()->getLocale() == 'ar' ? 'تطبيق الفلترة' : 'Filter' }}
                    </button>
                    <a href="{{ route('admin.bookings.hotels.profits') }}" class="btn btn-secondary" style="font-weight: 700;">
                        {{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary KPI Card -->
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي أرباح المنصة (فنادق)' : 'Total Platform Profit (Hotels)' }}</span>
                        <h3 style="font-weight: 900; color: #22c55e; margin: 0;">
                            +{{ number_format($totalProfit ?? 0, 2) }} <small style="font-size: 0.85rem; color: var(--text-muted);">SAR</small>
                        </h3>
                    </div>
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(34,197,94,0.1); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="admin-card p-0" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-list-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'سجلات أرباح حجوزات الفنادق' : 'Hotel Profit Records' }}
            </h6>
            <div style="min-width: 250px;">
                <input type="text" id="custom-search" class="form-control form-control-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث في السجلات...' : 'Search records...' }}">
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table id="profits-table" class="table align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th>#</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'المرجع' : 'Reference' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'اسم الفندق' : 'Hotel Name' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'التاريخ' : 'Date' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'السعر الأساسي' : 'Base Price' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'المبلغ الإجمالي' : 'Total Amount' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الربح' : 'Profit' }}</th>
                            <th class="text-end">{{ app()->getLocale() == 'ar' ? 'إجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let qs = window.location.search;
        let table = null;

        if (typeof V2Table !== 'undefined') {
            table = new V2Table('#profits-table', {
                ajax: {
                    url: "{{ route('admin.bookings.hotels.profits.data') }}" + qs,
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'id' },
                    { data: 'reference' },
                    { data: 'hotel' },
                    { data: 'customer' },
                    { data: 'date' },
                    { data: 'base_price' },
                    { data: 'total_amount' },
                    { data: 'profit' },
                    { data: 'actions' }
                ]
            });
            $('#custom-search').on('keyup', function() {
                table.search(this.value);
            });
        } else if ($.fn.DataTable) {
            table = $('#profits-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: "{{ route('admin.bookings.hotels.profits.data') }}" + qs,
                columns: [
                    { data: 'id' },
                    { data: 'reference' },
                    { data: 'hotel' },
                    { data: 'customer' },
                    { data: 'date' },
                    { data: 'base_price' },
                    { data: 'total_amount' },
                    { data: 'profit' },
                    { data: 'actions', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                dom: 'rtip'
            });
            $('#custom-search').on('keyup', function() {
                table.search(this.value).draw();
            });
        }
    });
</script>
@endpush
