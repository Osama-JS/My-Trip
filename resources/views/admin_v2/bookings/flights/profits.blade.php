@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'تقرير أرباح الطيران' : 'Flight Profits Report')

@section('content')
<div class="content-body-inner">
    <!-- Page Header -->
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">
                <i class="fa-solid fa-coins text-warning me-2"></i>
                {{ app()->getLocale() == 'ar' ? 'تقرير أرباح حجوزات الطيران' : 'Flight Profits Report' }}
            </h1>
            <p class="text-muted mb-0">
                {{ app()->getLocale() == 'ar' ? 'متابعة أرباح المنصة وعمولات التذاكر المصدرة وتفاصيل المبيعات' : 'Track platform profits, ticket commissions, and net earnings' }}
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.bookings.flights.index') }}" class="btn-v2 btn-secondary">
                <i class="fa-solid fa-plane me-1"></i>
                {{ app()->getLocale() == 'ar' ? 'حجوزات الطيران' : 'Flight Bookings' }}
            </a>
            <a href="{{ route('admin.bookings.flights.analytics') }}" class="btn-v2 btn-info">
                <i class="fa-solid fa-chart-line me-1"></i>
                {{ app()->getLocale() == 'ar' ? 'التحليلات' : 'Analytics' }}
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="v2-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'صافي أرباح المنصة' : 'Total Platform Profit' }}</span>
                    <span class="w-10 h-10 rounded-xl bg-success-subtle text-success d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-dollar-sign text-lg"></i>
                    </span>
                </div>
                <h2 class="font-extrabold text-2xl mb-1 text-success">
                    +{{ number_format($totalProfit ?? 0, 2) }} <span class="text-sm font-semibold text-muted">SAR</span>
                </h2>
                <small class="text-muted text-xs">
                    <i class="fa-solid fa-shield-check text-success me-1"></i>
                    {{ app()->getLocale() == 'ar' ? 'محسوب للحجوزات المؤكدة والمدفوعة' : 'Calculated for confirmed and paid bookings' }}
                </small>
            </div>
        </div>
        <div class="col-md-8">
            <div class="v2-card p-4 h-100">
                <h6 class="font-bold text-sm mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-filter text-primary"></i>
                    {{ app()->getLocale() == 'ar' ? 'تصفية حسب الفترة الزمنية' : 'Filter by Date Range' }}
                </h6>
                <form id="profit-filter-form" class="row g-2 align-items-end">
                    <div class="col-sm-5">
                        <label class="form-label text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'من تاريخ' : 'From Date' }}</label>
                        <input type="text" id="profit_date_from" name="date_from" class="form-control" data-datepicker="true" placeholder="YYYY-MM-DD" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-sm-5">
                        <label class="form-label text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'إلى تاريخ' : 'To Date' }}</label>
                        <input type="text" id="profit_date_to" name="date_to" class="form-control" data-datepicker="true" placeholder="YYYY-MM-DD" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-sm-2 d-flex gap-1">
                        <button type="submit" class="btn-v2 btn-primary w-100" title="{{ app()->getLocale() == 'ar' ? 'تطبيق' : 'Apply' }}">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <button type="button" id="resetProfitFilter" class="btn-v2 btn-secondary" title="{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Reset' }}">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Profits Table Card (Pure V2Table, Zero DataTables) -->
    <div class="v2-card">
        <div class="v2-card-header d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-table-list text-primary"></i>
                <h6 class="mb-0 font-bold">{{ app()->getLocale() == 'ar' ? 'سجل الأرباح التفصيلي' : 'Profit Transactions Log' }}</h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="v2-search-box">
                    <i class="fa-solid fa-magnifying-glass v2-search-icon"></i>
                    <input type="text" id="custom-search" class="form-control v2-search-input" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث بالمرجع أو العميل...' : 'Search reference or customer...' }}">
                </div>
                <button type="button" id="refreshProfitsBtn" class="btn-v2 btn-secondary" title="{{ app()->getLocale() == 'ar' ? 'تحديث' : 'Refresh' }}">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="flight-profits-table" class="v2-table w-100">
                <thead>
                    <tr>
                        <th style="width: 80px;">#</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'رقم الحجز' : 'Reference' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'تاريخ الحجز' : 'Date' }}</th>
                        <th class="text-end">{{ app()->getLocale() == 'ar' ? 'سعر المزود' : 'Base Price' }}</th>
                        <th class="text-end">{{ app()->getLocale() == 'ar' ? 'المبلغ الإجمالي' : 'Total Amount' }}</th>
                        <th class="text-end">{{ app()->getLocale() == 'ar' ? 'ربح المنصة' : 'Profit' }}</th>
                        <th class="text-center" style="width: 100px;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
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
let flightProfitsTable;

document.addEventListener('DOMContentLoaded', function() {
    flightProfitsTable = new V2Table('#flight-profits-table', {
        ajax: {
            url: "{{ route('admin.bookings.flights.profits.data') }}",
            data: function(d) {
                d.date_from = $('#profit_date_from').val();
                d.date_to = $('#profit_date_to').val();
            }
        },
        searchInput: '#custom-search',
        perPage: 15,
        perPageOptions: [10, 25, 50, 100],
        defaultSort: { key: 'id', order: 'desc' },
        columns: [
            { data: 'id', className: 'font-mono' },
            { data: 'reference', className: 'font-mono font-bold' },
            { data: 'customer' },
            { data: 'date', className: 'text-xs text-muted' },
            { data: 'base_price', className: 'text-end font-mono' },
            { data: 'total_amount', className: 'text-end font-mono font-bold' },
            { data: 'profit', className: 'text-end font-mono font-bold' },
            { 
                data: 'actions', 
                orderable: false, 
                searchable: false, 
                className: 'text-center',
                render: function(data, row) {
                    if (!row) return data || '';
                    const showUrlTemplate = "{{ route('admin.bookings.flights.show', ':id') }}";
                    const showUrl = showUrlTemplate.replace(':id', row.id);
                    const isAr = document.documentElement.lang === 'ar';
                    return `
                        <div class="v2-actions-inline">
                            <a href="${showUrl}" class="v2-action-btn v2-action-btn-view" title="${isAr ? 'عرض التفاصيل' : 'View'}">
                                <i class="fa-solid fa-eye text-primary"></i>
                            </a>
                        </div>
                    `;
                }
            }
        ]
    });

    $('#profit-filter-form').on('submit', function(e) {
        e.preventDefault();
        flightProfitsTable.reload(true);
    });

    $('#resetProfitFilter').on('click', function() {
        $('#profit_date_from').val('');
        $('#profit_date_to').val('');
        flightProfitsTable.reload(true);
    });

    $('#refreshProfitsBtn').on('click', function() {
        flightProfitsTable.reload();
        Notify.info('{{ app()->getLocale() == "ar" ? "تم تحديث البيانات" : "Data refreshed" }}');
    });
});
</script>
@endpush
