@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'تقرير أرباح البرامج السياحية' : 'Tour Package Profits Report')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                {{ app()->getLocale() == 'ar' ? 'تقرير أرباح ومبيعات البرامج السياحية' : 'Tour Package Profits Report' }}
            </h1>
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                {{ app()->getLocale() == 'ar' ? 'متابعة صافي أرباح المنصة ومستحقات الشركات المزودة للرحلات.' : 'Monitor platform profits, margins, and provider payouts.' }}
            </span>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.trips.analytics') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-chart-line me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحليلات الحجوزات' : 'Analytics' }}
            </a>
            <a href="{{ route('admin.trip-bookings.index') }}" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-calendar-check me-1"></i> {{ app()->getLocale() == 'ar' ? 'حجوزات الرحلات' : 'Bookings List' }}
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="admin-card p-4 mb-4">
        <form action="{{ route('admin.trips.profits') }}" method="GET">
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
                <div class="col-md-3 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill" style="background: var(--primary); border: none; font-weight: 700;">
                        <i class="fa-solid fa-filter me-1"></i> {{ app()->getLocale() == 'ar' ? 'تطبيق الفلترة' : 'Filter' }}
                    </button>
                    <a href="{{ route('admin.trips.profits') }}" class="btn btn-secondary" style="font-weight: 700;">
                        {{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- 3 Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small mb-1">{{ app()->getLocale() == 'ar' ? 'أرباح المنصة من البرامج' : 'Total Platform Profit' }}</span>
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

        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي مبيعات البرامج' : 'Total Package Sales' }}</span>
                        <h3 style="font-weight: 900; color: var(--primary); margin: 0;">
                            {{ number_format($totalRevenue ?? 0, 2) }} <small style="font-size: 0.85rem; color: var(--text-muted);">SAR</small>
                        </h3>
                    </div>
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small mb-1">{{ app()->getLocale() == 'ar' ? 'مستحقات الشركات المزودة' : 'Total Company Earnings' }}</span>
                        <h3 style="font-weight: 900; color: #0ea5e9; margin: 0;">
                            {{ number_format($totalProviderEarnings ?? 0, 2) }} <small style="font-size: 0.85rem; color: var(--text-muted);">SAR</small>
                        </h3>
                    </div>
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(14,165,233,0.1); color: #0ea5e9; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                        <i class="fa-solid fa-building"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="admin-card p-0" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-list-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'سجلات أرباح حجوزات الرحلات' : 'Tour Package Profit Records' }}
            </h6>
            <div style="min-width: 250px;">
                <input type="text" id="custom-search" class="form-control form-control-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث في السجلات...' : 'Search records...' }}">
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table id="trip-profits-table" class="table align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th>#</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'المرجع' : 'Reference' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'البرنامج السياحي' : 'Tour Package' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الشركة' : 'Company' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'التاريخ' : 'Date' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'حصة الشركة' : 'Company Share' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'المبلغ الإجمالي' : 'Total Amount' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'ربح المنصة' : 'Platform Profit' }}</th>
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
            table = new V2Table('#trip-profits-table', {
                ajax: {
                    url: "{{ route('admin.trips.profits.data') }}" + qs,
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'id' },
                    { data: 'reference' },
                    { data: 'trip' },
                    { data: 'company' },
                    { data: 'customer' },
                    { data: 'date' },
                    { data: 'provider_price' },
                    { data: 'total_amount' },
                    { data: 'profit' },
                    { data: 'actions' }
                ]
            });
            $('#custom-search').on('keyup', function() {
                table.search(this.value);
            });
        } else if ($.fn.DataTable) {
            table = $('#trip-profits-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: "{{ route('admin.trips.profits.data') }}" + qs,
                columns: [
                    { data: 'id' },
                    { data: 'reference' },
                    { data: 'trip' },
                    { data: 'company' },
                    { data: 'customer' },
                    { data: 'date' },
                    { data: 'provider_price' },
                    { data: 'total_amount' },
                    { data: 'profit' },
                    { data: 'actions', orderable: false, searchable: false }
                ],
                dom: 'rtip'
            });
            $('#custom-search').on('keyup', function() {
                table.search(this.value).draw();
            });
        }
    });
</script>
@endpush
