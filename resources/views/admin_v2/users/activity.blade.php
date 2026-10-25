@extends('admin_v2.layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'سجل نشاط المستخدم: ' : 'User Activity: ') . $user->full_name)

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ $user->user_type === \App\Models\User::TYPE_CUSTOMER ? route('admin.subscribers.index') : route('admin.users.index') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع' : 'Back' }}">
                <i class="fa-solid fa-arrow-right rtl:rotate-0"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ app()->getLocale() == 'ar' ? 'سجل نشاط وحجوزات المستخدم' : 'User Activity & Bookings' }} <span style="color: var(--primary);">{{ $user->full_name }}</span>
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ $user->email }} &bull; {{ app()->getLocale() == 'ar' ? 'انضم في:' : 'Joined:' }} {{ $user->created_at->format('Y-m-d') }}
                </span>
            </div>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.subscribers.index') }}" class="btn btn-outline-primary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-users me-1"></i> {{ app()->getLocale() == 'ar' ? 'المشتركون والعملاء' : 'Subscribers' }}
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-users-gear me-1"></i> {{ app()->getLocale() == 'ar' ? 'كافة المستخدمين' : 'All Users' }}
            </a>
        </div>
    </div>

    <!-- User KPI Stats Grid -->
    <div class="stat-grid mb-4">
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي الحجوزات' : 'Total Bookings' }}</div>
                <div class="stat-value" id="kpi-total-bookings">{{ number_format($stats['total_bookings'] ?? count($bookings ?? [])) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-receipt"></i> {{ app()->getLocale() == 'ar' ? 'كل الطلبات' : 'All orders' }}</div>
            </div>
            <div class="stat-icon icon-primary">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'حجوزات مؤكدة' : 'Confirmed Bookings' }}</div>
                <div class="stat-value text-success">{{ number_format($stats['confirmed_bookings'] ?? 0) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() == 'ar' ? 'ناجحة' : 'Successful' }}</div>
            </div>
            <div class="stat-icon icon-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي المشتريات والإنفاق' : 'Total Spent' }}</div>
                <div class="stat-value text-primary">{{ number_format($stats['total_spent'] ?? 0, 2) }} <small style="font-size: 0.75rem;">SAR</small></div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-coins"></i> {{ app()->getLocale() == 'ar' ? 'القيمة الإجمالية' : 'Total value' }}</div>
            </div>
            <div class="stat-icon icon-info">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'عمليات البحث المنفذة' : 'Searches Run' }}</div>
                <div class="stat-value text-warning">{{ number_format($stats['total_searches'] ?? count($searches ?? [])) }}</div>
                <div class="stat-trend"><i class="fa-solid fa-magnifying-glass"></i> {{ app()->getLocale() == 'ar' ? 'سجل البحث' : 'Search logs' }}</div>
            </div>
            <div class="stat-icon icon-warning">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2.5fr; gap: 1.5rem;" class="charts-grid">
        <!-- Left: Profile Info Card -->
        <div>
            <div class="admin-card text-center mb-4">
                <div class="user-avatar mx-auto mb-3" style="width: 72px; height: 72px; font-size: 1.75rem;">
                    {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}
                </div>
                <h4 style="font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">{{ $user->full_name }}</h4>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.75rem;">{{ $user->email }}</p>

                <div style="display: flex; justify-content: center; gap: 0.5rem; flex-wrap: wrap;">
                    <span class="badge-v2 badge-primary">{{ strtoupper($user->user_type) }}</span>
                    @if($user->email_verified_at)
                        <span class="badge-v2 badge-success"><i class="fa-solid fa-circle-check me-1"></i> {{ app()->getLocale() == 'ar' ? 'موثق' : 'Verified' }}</span>
                    @endif
                    @if($user->status === 'active')
                        <span class="badge-v2 badge-success">{{ app()->getLocale() == 'ar' ? 'نشط' : 'Active' }}</span>
                    @else
                        <span class="badge-v2 badge-danger">{{ app()->getLocale() == 'ar' ? 'متوقف' : 'Inactive' }}</span>
                    @endif
                </div>

                <hr style="border-color: var(--border-light); margin: 1.25rem 0 1rem;">

                <div style="text-align: start; font-size: 0.875rem; display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);"><i class="fa-solid fa-phone me-1"></i> {{ app()->getLocale() == 'ar' ? 'الهاتف:' : 'Phone:' }}</span>
                        <span style="direction: ltr; font-weight: 600;">{{ $user->phone ?? '—' }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);"><i class="fa-solid fa-city me-1"></i> {{ app()->getLocale() == 'ar' ? 'المدينة:' : 'City:' }}</span>
                        <span style="font-weight: 600;">{{ $user->city ?? '—' }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);"><i class="fa-solid fa-calendar me-1"></i> {{ app()->getLocale() == 'ar' ? 'تاريخ التسجيل:' : 'Joined:' }}</span>
                        <span style="font-weight: 600;">{{ $user->created_at->format('Y-m-d') }}</span>
                    </div>

                    @if(isset($stats['success_rate']))
                    <div style="margin-top: 0.5rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.35rem;">
                            <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'معدل نجاح الحجوزات:' : 'Success Rate:' }}</span>
                            <strong style="color: var(--primary);">{{ $stats['success_rate'] }}%</strong>
                        </div>
                        <div style="height: 6px; background: var(--bg-input); border-radius: var(--radius-full); overflow: hidden;">
                            <div style="height: 100%; width: {{ $stats['success_rate'] }}%; background: var(--primary); border-radius: var(--radius-full);"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Bookings & Searches Tabs -->
        <div>
            <div class="admin-card p-0" style="overflow: hidden;">
                <!-- Main Header Tab Navigation (Bookings vs Searches) -->
                <div class="card-header-flex p-3 m-0" style="background: var(--bg-card); border-bottom: 1px solid var(--border-color);">
                    <ul class="nav nav-pills gap-2" role="tablist">
                        <li class="nav-item">
                            <button class="btn btn-sm btn-primary active" style="font-weight: 700; border-radius: var(--radius-md);" data-bs-toggle="pill" data-bs-target="#tab-bookings">
                                <i class="fa-solid fa-receipt me-1"></i> {{ app()->getLocale() == 'ar' ? 'سجل الحجوزات' : 'Bookings History' }}
                                <span class="badge bg-white text-primary ms-1" id="main-badge-bookings">{{ $counts['all'] ?? count($bookings ?? []) }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-sm btn-outline-secondary" style="font-weight: 700; border-radius: var(--radius-md);" data-bs-toggle="pill" data-bs-target="#tab-searches">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> {{ app()->getLocale() == 'ar' ? 'سجل عمليات البحث' : 'Searches' }} ({{ count($searches ?? []) }})
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content">
                    <!-- ─── Bookings Main Tab ─── -->
                    <div class="tab-pane fade show active" id="tab-bookings">

                        <!-- Service Category Tabs (Sub-Tabs for Bookings) -->
                        <div class="p-3 pb-2" style="border-bottom: 1px solid var(--border-color); background: var(--bg-card);">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <button type="button" class="btn btn-sm btn-primary booking-subtab-btn" data-type="all" style="font-weight: 700; border-radius: var(--radius-md);">
                                        <i class="fa-solid fa-layer-group me-1"></i> {{ app()->getLocale() == 'ar' ? 'جميع الحجوزات' : 'All Bookings' }}
                                        <span class="badge bg-white text-primary ms-1" id="badge-count-all">{{ $counts['all'] ?? 0 }}</span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary booking-subtab-btn" data-type="flights" style="font-weight: 700; border-radius: var(--radius-md);">
                                        <i class="fa-solid fa-plane-departure me-1"></i> {{ app()->getLocale() == 'ar' ? 'حجوزات الطيران' : 'Flights' }}
                                        <span class="badge bg-primary-subtle text-primary ms-1" id="badge-count-flights">{{ $counts['flights'] ?? 0 }}</span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary booking-subtab-btn" data-type="hotels" style="font-weight: 700; border-radius: var(--radius-md);">
                                        <i class="fa-solid fa-hotel me-1"></i> {{ app()->getLocale() == 'ar' ? 'حجوزات الفنادق' : 'Hotels' }}
                                        <span class="badge bg-success-subtle text-success ms-1" id="badge-count-hotels">{{ $counts['hotels'] ?? 0 }}</span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary booking-subtab-btn" data-type="trips" style="font-weight: 700; border-radius: var(--radius-md);">
                                        <i class="fa-solid fa-suitcase me-1"></i> {{ app()->getLocale() == 'ar' ? 'الباقات والرحلات' : 'Tours & Trips' }}
                                        <span class="badge bg-warning-subtle text-warning ms-1" id="badge-count-trips">{{ $counts['trips'] ?? 0 }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- AJAX Filter & Search Bar -->
                        <div class="p-3" style="background: var(--bg-input, #f8fafc); border-bottom: 1px solid var(--border-color);">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                {{-- Search Input with Debounce --}}
                                <div style="flex: 1; min-width: 220px; position: relative;">
                                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; inset-inline-start: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
                                    <input type="text" id="bookingSearchInput" class="form-control form-control-sm" 
                                           style="padding-inline-start: 2rem; border-radius: var(--radius-md); background: #ffffff;" 
                                           placeholder="{{ app()->getLocale() == 'ar' ? 'بحث برقم الحجز، الوجهة، الفندق، خط الطيران...' : 'Search by reference, route, hotel, airline...' }}">
                                </div>

                                {{-- Status Filter Dropdown --}}
                                <div style="min-width: 140px;">
                                    <select id="bookingStatusSelect" class="form-select form-select-sm" style="border-radius: var(--radius-md); font-weight: 600; background-color: #ffffff;">
                                        <option value="all">{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                                        <option value="confirmed">{{ app()->getLocale() == 'ar' ? 'مؤكد / مسدد' : 'Confirmed / Paid' }}</option>
                                        <option value="pending">{{ app()->getLocale() == 'ar' ? 'معلق / قيد المعالجة' : 'Pending' }}</option>
                                        <option value="cancelled">{{ app()->getLocale() == 'ar' ? 'ملغي / مسترجع' : 'Cancelled' }}</option>
                                    </select>
                                </div>

                                {{-- Date From --}}
                                <div class="d-flex align-items-center gap-1">
                                    <span class="text-muted small" style="white-space: nowrap;">{{ app()->getLocale() == 'ar' ? 'من:' : 'From:' }}</span>
                                    <input type="date" id="bookingDateFrom" class="form-control form-control-sm" style="width: auto; border-radius: var(--radius-md); background: #ffffff;">
                                </div>

                                {{-- Date To --}}
                                <div class="d-flex align-items-center gap-1">
                                    <span class="text-muted small" style="white-space: nowrap;">{{ app()->getLocale() == 'ar' ? 'إلى:' : 'To:' }}</span>
                                    <input type="date" id="bookingDateTo" class="form-control form-control-sm" style="width: auto; border-radius: var(--radius-md); background: #ffffff;">
                                </div>

                                {{-- Reset Filters Button --}}
                                <button type="button" id="btnResetFilters" class="btn btn-sm btn-outline-secondary" style="border-radius: var(--radius-md); white-space: nowrap;" title="{{ app()->getLocale() == 'ar' ? 'إعادة ضبط الفلاتر' : 'Reset Filters' }}">
                                    <i class="fa-solid fa-rotate-left me-1"></i> {{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Reset' }}
                                </button>
                            </div>
                        </div>

                        <!-- Dynamic Table Container with Loader -->
                        <div id="bookingsContainerWrapper" style="position: relative; min-height: 280px;">
                            <!-- Loading Spinner Overlay -->
                            <div id="bookingsLoader" style="display: none; position: absolute; inset: 0; background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(2px); z-index: 20; align-items: center; justify-content: center;">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status" style="width: 2.2rem; height: 2.2rem;">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div class="mt-2 text-muted fw-bold small">{{ app()->getLocale() == 'ar' ? 'جارٍ تحميل البيانات...' : 'Loading bookings...' }}</div>
                                </div>
                            </div>

                            <!-- Rendered Table & Pagination via Partial -->
                            <div id="bookingsTableContainer">
                                @include('admin_v2.users.partials.bookings_table', [
                                    'bookings' => $initialBookings ?? (new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)),
                                    'type' => 'all',
                                    'user' => $user
                                ])
                            </div>
                        </div>

                    </div>

                    <!-- ─── Searches Tab ─── -->
                    <div class="tab-pane fade p-3" id="tab-searches">
                        <div class="table-responsive">
                            <table class="table-v2 w-100">
                                <thead>
                                    <tr>
                                        <th>{{ app()->getLocale() == 'ar' ? 'المسار (من / إلى)' : 'Route' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'نوع الرحلة' : 'Trip Type' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'تاريخ السفر' : 'Dates' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'المسافرين' : 'Passengers' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'وقت البحث' : 'Searched at' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($searches ?? [] as $s)
                                    <tr>
                                        <td>
                                            <strong style="font-family: monospace; color: var(--primary);">{{ $s->origin ?? 'N/A' }}</strong>
                                            <i class="fa-solid fa-arrow-right mx-1 text-muted"></i>
                                            <strong style="font-family: monospace; color: var(--text-main);">{{ $s->destination ?? 'N/A' }}</strong>
                                        </td>
                                        <td><span class="badge-v2 badge-info">{{ ucfirst($s->trip_type ?? 'One-Way') }}</span></td>
                                        <td>{{ $s->departure_date ? \Carbon\Carbon::parse($s->departure_date)->format('Y-m-d') : '—' }}</td>
                                        <td>{{ ($s->adults ?? 1) }} Adl {{ ($s->children ?? 0) ? ', ' . $s->children . ' Chd' : '' }}</td>
                                        <td><small style="color: var(--text-muted);">{{ $s->created_at ? $s->created_at->format('Y-m-d H:i') : '—' }}</small></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                            {{ app()->getLocale() == 'ar' ? 'لا توجد عمليات بحث مسجلة لهذا المستخدم.' : 'No search logs recorded.' }}
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
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const bookingsDataUrl = "{{ route('admin.users.bookings.data', $user->id) }}";

    // AJAX State
    let state = {
        type: 'all',
        status: 'all',
        search: '',
        date_from: '',
        date_to: '',
        page: 1,
        per_page: 10
    };

    const loader = document.getElementById('bookingsLoader');
    const tableContainer = document.getElementById('bookingsTableContainer');
    const searchInput = document.getElementById('bookingSearchInput');
    const statusSelect = document.getElementById('bookingStatusSelect');
    const dateFromInput = document.getElementById('bookingDateFrom');
    const dateToInput = document.getElementById('bookingDateTo');
    const resetBtn = document.getElementById('btnResetFilters');
    const subTabBtns = document.querySelectorAll('.booking-subtab-btn');

    function showLoader() {
        if (loader) loader.style.display = 'flex';
    }

    function hideLoader() {
        if (loader) loader.style.display = 'none';
    }

    let searchTimer = null;

    function fetchBookings() {
        showLoader();

        const params = new URLSearchParams({
            type: state.type,
            status: state.status,
            search: state.search,
            date_from: state.date_from,
            date_to: state.date_to,
            page: state.page,
            per_page: state.per_page
        });

        fetch(`${bookingsDataUrl}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error: ' + response.status);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (tableContainer) {
                    tableContainer.innerHTML = data.html;
                }

                // Update badge counts dynamically
                if (data.counts) {
                    const badgeAll = document.getElementById('badge-count-all');
                    const badgeFlights = document.getElementById('badge-count-flights');
                    const badgeHotels = document.getElementById('badge-count-hotels');
                    const badgeTrips = document.getElementById('badge-count-trips');
                    const mainBadge = document.getElementById('main-badge-bookings');

                    if (badgeAll) badgeAll.textContent = data.counts.all ?? 0;
                    if (badgeFlights) badgeFlights.textContent = data.counts.flights ?? 0;
                    if (badgeHotels) badgeHotels.textContent = data.counts.hotels ?? 0;
                    if (badgeTrips) badgeTrips.textContent = data.counts.trips ?? 0;
                    if (mainBadge) mainBadge.textContent = data.counts.all ?? 0;
                }
            }
        })
        .catch(err => {
            console.error('Fetch bookings error:', err);
        })
        .finally(() => {
            hideLoader();
        });
    }

    // Sub-tab button switching
    subTabBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const requestedType = this.getAttribute('data-type');
            if (state.type === requestedType) return;

            state.type = requestedType;
            state.page = 1;

            // Update button styles
            subTabBtns.forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-primary');

            fetchBookings();
        });
    });

    // Search input with debounce (350ms)
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                state.search = searchInput.value.trim();
                state.page = 1;
                fetchBookings();
            }, 350);
        });
    }

    // Status filter dropdown
    if (statusSelect) {
        statusSelect.addEventListener('change', function () {
            state.status = statusSelect.value;
            state.page = 1;
            fetchBookings();
        });
    }

    // Date From Filter
    if (dateFromInput) {
        dateFromInput.addEventListener('change', function () {
            state.date_from = dateFromInput.value;
            state.page = 1;
            fetchBookings();
        });
    }

    // Date To Filter
    if (dateToInput) {
        dateToInput.addEventListener('change', function () {
            state.date_to = dateToInput.value;
            state.page = 1;
            fetchBookings();
        });
    }

    // Reset Filters Button
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            state.search = '';
            state.status = 'all';
            state.date_from = '';
            state.date_to = '';
            state.page = 1;

            if (searchInput) searchInput.value = '';
            if (statusSelect) statusSelect.value = 'all';
            if (dateFromInput) dateFromInput.value = '';
            if (dateToInput) dateToInput.value = '';

            fetchBookings();
        });
    }

    // Delegated pagination click
    if (tableContainer) {
        tableContainer.addEventListener('click', function (e) {
            const pageBtn = e.target.closest('.booking-page-btn');
            if (pageBtn && !pageBtn.disabled) {
                const targetPage = parseInt(pageBtn.getAttribute('data-page'), 10);
                if (targetPage && targetPage !== state.page) {
                    state.page = targetPage;
                    fetchBookings();
                    const wrapper = document.getElementById('bookingsContainerWrapper');
                    if (wrapper) {
                        wrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }
            }
        });
    }
});
</script>
@endpush
