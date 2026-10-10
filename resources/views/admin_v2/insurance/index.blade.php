@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'عمليات تأمين السفر' : 'Travel Insurance Operations')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fa-solid fa-shield-alt text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'عمليات وتأمين السفر' : 'Travel Insurance Operations' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">
                {{ app()->getLocale() == 'ar' ? 'إصدار ومتابعة وثائق التأمين، استخراج الشهادات، ومتابعة الأرباح والعمولات.' : 'Manage insurance policies, certificates, revenue, and claims.' }}
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('admin.insurance.profits') }}" class="btn btn-outline-success" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-chart-pie me-1"></i> {{ app()->getLocale() == 'ar' ? 'الأرباح والتحليلات' : 'Profits & Analytics' }}
            </a>
            <a href="{{ route('admin.insurance.settings') }}" class="btn btn-primary shadow-sm" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-gear me-1"></i> {{ app()->getLocale() == 'ar' ? 'إعدادات التأمين' : 'Insurance Settings' }}
            </a>
        </div>
    </div>

    <!-- KPI Stats -->
    <div class="stat-grid mb-4">
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي الوثائق الصادرة' : 'Total Policies Issued' }}</div>
                <div class="stat-value">{{ number_format($stats['total_policies'] ?? 0) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-file-contract"></i> {{ app()->getLocale() == 'ar' ? 'كل الوثائق' : 'All policies' }}</div>
            </div>
            <div class="stat-icon icon-primary">
                <i class="fa-solid fa-file-contract"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي الإيرادات' : 'Total Revenue' }}</div>
                <div class="stat-value text-primary">{{ number_format($stats['total_revenue'] ?? 0, 2) }} <small style="font-size: 0.75rem;">SAR</small></div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-coins"></i> {{ app()->getLocale() == 'ar' ? 'مبيعات' : 'Sales' }}</div>
            </div>
            <div class="stat-icon icon-info">
                <i class="fa-solid fa-coins"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'تكلفة المزود' : 'Provider Cost' }}</div>
                <div class="stat-value text-muted">{{ number_format($stats['total_cost'] ?? 0, 2) }} <small style="font-size: 0.75rem;">SAR</small></div>
                <div class="stat-trend"><i class="fa-solid fa-file-invoice-dollar"></i> {{ app()->getLocale() == 'ar' ? 'صافي التكلفة' : 'Cost' }}</div>
            </div>
            <div class="stat-icon icon-secondary">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'أرباح المنصة' : 'Platform Profit' }}</div>
                <div class="stat-value text-success">+{{ number_format($stats['total_profit'] ?? 0, 2) }} <small style="font-size: 0.75rem;">SAR</small></div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-chart-line"></i> {{ app()->getLocale() == 'ar' ? 'صافي الربح' : 'Net profit' }}</div>
            </div>
            <div class="stat-icon icon-success">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>
    </div>

    <!-- Filter Card (Separated & Non-Overlapping) -->
    <div class="v2-filter-card mb-4">
        <form action="{{ route('admin.insurance.index') }}" method="GET">
            <div class="v2-filter-grid">
                <div>
                    <label class="form-label">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> {{ app()->getLocale() == 'ar' ? 'بحث بالرقم أو الاسم' : 'Search Policy' }}
                    </label>
                    <input type="text" name="search" class="form-control" placeholder="{{ app()->getLocale() == 'ar' ? 'رقم الوثيقة، الاسم، الهاتف...' : 'Policy No, Name, Phone...' }}" value="{{ request('search') }}">
                </div>

                <div>
                    <label class="form-label">
                        <i class="fa-solid fa-tags me-1"></i> {{ app()->getLocale() == 'ar' ? 'حالة الوثيقة' : 'Status' }}
                    </label>
                    <select name="status" class="select2 form-select" data-placeholder="{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Statuses' }}">
                        <option value="">{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'فعالة (Active)' : 'Active' }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'معلقة (Pending)' : 'Pending' }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'ملغية (Cancelled)' : 'Cancelled' }}</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">
                        <i class="fa-solid fa-layer-group me-1"></i> {{ app()->getLocale() == 'ar' ? 'نوع الحجز' : 'Booking Type' }}
                    </label>
                    <select name="booking_type" class="select2 form-select" data-placeholder="{{ app()->getLocale() == 'ar' ? 'جميع الأنواع' : 'All Types' }}">
                        <option value="">{{ app()->getLocale() == 'ar' ? 'جميع الأنواع' : 'All Types' }}</option>
                        <option value="flight" {{ request('booking_type') == 'flight' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'طيران (Flight)' : 'Flight' }}</option>
                        <option value="trip" {{ request('booking_type') == 'trip' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'رحلة سياحية (Tour Package)' : 'Tour Package' }}</option>
                        <option value="hotel" {{ request('booking_type') == 'hotel' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'فندق (Hotel)' : 'Hotel' }}</option>
                        <option value="standalone" {{ request('booking_type') == 'standalone' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'مستقل (Standalone)' : 'Standalone' }}</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">
                        <i class="fa-solid fa-calendar-day me-1"></i> {{ app()->getLocale() == 'ar' ? 'من تاريخ' : 'Date From' }}
                    </label>
                    <input type="text" name="date_from" class="form-control flatpickr-date" placeholder="YYYY-MM-DD" value="{{ request('date_from') }}">
                </div>

                <div>
                    <label class="form-label">
                        <i class="fa-solid fa-calendar-check me-1"></i> {{ app()->getLocale() == 'ar' ? 'إلى تاريخ' : 'Date To' }}
                    </label>
                    <input type="text" name="date_to" class="form-control flatpickr-date" placeholder="YYYY-MM-DD" value="{{ request('date_to') }}">
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary w-100" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700; height: 38px;">
                        <i class="fa-solid fa-filter me-1"></i> {{ app()->getLocale() == 'ar' ? 'فلترة' : 'Filter' }}
                    </button>
                    <a href="{{ route('admin.insurance.index') }}" class="btn btn-icon" title="{{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Reset' }}" style="height: 38px; width: 38px; flex-shrink: 0;">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="card-header-flex p-3 m-0" style="background: var(--bg-card); border-bottom: 1px solid var(--border-color);">
            <div>
                <div class="card-title">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'وثائق تأمين السفر الصادرة' : 'Issued Insurance Policies' }}
                </div>
            </div>
            <div class="v2-header-tools">
                @if(isset($policies) && method_exists($policies, 'perPage'))
                    @include('admin_v2.partials.per_page', ['paginator' => $policies])
                @endif
                <a href="{{ route('admin.insurance.index') }}" class="btn btn-sm btn-icon" title="{{ app()->getLocale() == 'ar' ? 'تحديث' : 'Refresh' }}" style="height: 34px; width: 34px;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </a>
            </div>
        </div>

        <div class="v2-table-responsive">
            <table class="v2-table w-full">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() == 'ar' ? 'رقم الوثيقة / المرجع' : 'Policy No / Ref' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'النوع والوجهة' : 'Type & Destination' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'المدة والتواريخ' : 'Period & Duration' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'التكلفة' : 'Cost' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'سعر البيع' : 'Selling Price' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الربح' : 'Profit' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($policies ?? [] as $policy)
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 0.35rem;">
                                    <i class="fa-solid fa-shield-halved text-primary"></i>
                                    <span>{{ $policy->policy_number }}</span>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">{{ optional($policy->created_at)->format('Y-m-d H:i') }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--text-main);">{{ optional($policy->user)->name ?? (app()->getLocale() == 'ar' ? 'زائر' : 'Guest') }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ optional($policy->user)->phone ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge-v2 badge-info mb-1">{{ ucfirst($policy->booking_type) }}</span>
                                <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-main);">{{ $policy->destination_country_name }}</div>
                            </td>
                            <td>
                                <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-main);">
                                    {{ optional($policy->departure_date)->format('d M') ?? '-' }} → {{ optional($policy->return_date)->format('d M Y') ?? '-' }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $policy->duration_days }} {{ app()->getLocale() == 'ar' ? 'يوم' : 'Days' }}</div>
                            </td>
                            <td><span style="color: var(--text-muted); font-family: monospace; font-size: 0.85rem;">{{ number_format($policy->net_cost, 2) }} SAR</span></td>
                            <td><span style="font-weight: 800; color: var(--text-main);">{{ number_format($policy->selling_price, 2) }} SAR</span></td>
                            <td><span class="badge-v2 badge-success">+{{ number_format($policy->platform_profit, 2) }} SAR</span></td>
                            <td>{!! $policy->status_badge !!}</td>
                            <td style="text-align: center;">
                                <div class="btn-action-group">
                                    <a href="{{ route('admin.insurance.show', $policy->id) }}" class="btn-action btn-action-view" title="{{ app()->getLocale() == 'ar' ? 'عرض التفاصيل' : 'View Details' }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.insurance.pdf', $policy->id) }}" class="btn-action btn-action-pdf" title="{{ app()->getLocale() == 'ar' ? 'تحميل الوثيقة PDF' : 'Download PDF' }}">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-0">
                                <div class="v2-empty-state">
                                    <div class="v2-empty-icon text-muted"><i class="fa-solid fa-shield-slash"></i></div>
                                    <div class="v2-empty-title">{{ app()->getLocale() == 'ar' ? 'لم يتم العثور على أي وثائق تأمين' : 'No insurance policies found' }}</div>
                                    <div class="v2-empty-text">{{ app()->getLocale() == 'ar' ? 'لا توجد وثائق تطابق شروط البحث أو الفلترة الحالية.' : 'No protection policies match your current filters.' }}</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($policies) && method_exists($policies, 'links'))
            @include('admin_v2.partials.pagination', ['paginator' => $policies])
        @endif
    </div>
</div>
@endsection
