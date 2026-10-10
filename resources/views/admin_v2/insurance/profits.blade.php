@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'أرباح وتحليلات التأمين على السفر' : 'Travel Insurance Profits & Analytics')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                {{ app()->getLocale() == 'ar' ? 'تحليلات وأرباح وثائق التأمين' : 'Travel Insurance Profits & Analytics' }}
            </h1>
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                {{ app()->getLocale() == 'ar' ? 'متابعة مبيعات التأمين المتقاطع، تكلفة المزود، وصافي هوامش المنصة.' : 'Analyze Sitata cross-sell policy revenues, provider costs, and net margins.' }}
            </span>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.insurance.settings') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-gear me-1"></i> {{ app()->getLocale() == 'ar' ? 'إعدادات التأمين' : 'Settings' }}
            </a>
            <a href="{{ route('admin.insurance.index') }}" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-shield-halved me-1"></i> {{ app()->getLocale() == 'ar' ? 'كافة الوثائق' : 'Policies List' }}
            </a>
        </div>
    </div>

    <!-- Date Filter Card -->
    <div class="admin-card p-4 mb-4">
        <form action="{{ route('admin.insurance.profits') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-4 col-sm-6">
                    <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'من تاريخ' : 'Start Date' }}</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'إلى تاريخ' : 'End Date' }}</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill" style="background: var(--primary); border: none; font-weight: 700;">
                        <i class="fa-solid fa-filter me-1"></i> {{ app()->getLocale() == 'ar' ? 'تطبيق الفلترة' : 'Apply Filter' }}
                    </button>
                    <a href="{{ route('admin.insurance.profits') }}" class="btn btn-secondary" style="font-weight: 700;">
                        {{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- 4 KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي الوثائق المباعة' : 'Total Policies Sold' }}</small>
                <h3 style="font-weight: 900; color: var(--primary); margin: 0;">{{ number_format($totalCount) }}</h3>
                <small class="text-muted mt-2 d-block" style="font-size: 0.75rem;">{{ app()->getLocale() == 'ar' ? 'خلال الفترة المحددة' : 'In selected period' }}</small>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي مبيعات التأمين' : 'Gross Insurance Sales' }}</small>
                <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">
                    {{ number_format($totalRevenue, 2) }} <small style="font-size: 0.85rem; color: var(--text-muted);">SAR</small>
                </h3>
                <small class="text-muted mt-2 d-block" style="font-size: 0.75rem;">{{ app()->getLocale() == 'ar' ? 'المبلغ المدفوع من العملاء' : 'Total customer paid' }}</small>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'تكلفة مزود التأمين' : 'Total Provider Cost' }}</small>
                <h3 style="font-weight: 900; color: #64748b; margin: 0;">
                    {{ number_format($totalCost, 2) }} <small style="font-size: 0.85rem; color: var(--text-muted);">SAR</small>
                </h3>
                <small class="text-muted mt-2 d-block" style="font-size: 0.75rem;">{{ app()->getLocale() == 'ar' ? 'صافي تكلفة Sitata' : 'Sitata Net Cost' }}</small>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'صافي أرباح المنصة' : 'Net Platform Profit' }}</small>
                <h3 style="font-weight: 900; color: #22c55e; margin: 0;">
                    +{{ number_format($totalProfit, 2) }} <small style="font-size: 0.85rem; color: var(--text-muted);">SAR</small>
                </h3>
                <small class="text-success mt-2 d-block font-bold" style="font-size: 0.75rem;">
                    {{ round($avgProfitMargin, 1) }}% {{ app()->getLocale() == 'ar' ? 'متوسط هامش الربح' : 'Average Margin Rate' }}
                </small>
            </div>
        </div>
    </div>

    <!-- Product Channel Breakdown Table -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="p-3 border-bottom" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-layer-group text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'توزيع الأرباح والمبيعات حسب قناة الحجز' : 'Insurance Revenue & Profit by Product Channel' }}
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                <thead style="background: rgba(0,0,0,0.02);">
                    <tr>
                        <th>{{ app()->getLocale() == 'ar' ? 'قناة المنتج' : 'Booking Product Channel' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الوثائق المباعة' : 'Policies Sold' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'إجمالي المبيعات' : 'Gross Revenue' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'التكلفة الصافية' : 'Net Cost' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'صافي الربح' : 'Net Profit' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'هامش الربح' : 'Profit Margin' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($byType ?? [] as $item)
                        @php
                            $itemMargin = $item->revenue > 0 ? (($item->profit / $item->revenue) * 100) : 0;
                        @endphp
                        <tr>
                            <td>
                                <strong style="color: var(--text-main);">
                                    @if($item->booking_type == 'flight')
                                        <i class="fa-solid fa-plane text-info me-2"></i>{{ app()->getLocale() == 'ar' ? 'تأمين تذاكر الطيران' : 'Flights Cross-Sell' }}
                                    @elseif($item->booking_type == 'trip')
                                        <i class="fa-solid fa-suitcase text-warning me-2"></i>{{ app()->getLocale() == 'ar' ? 'تأمين البرامج السياحية' : 'Tour Packages Cross-Sell' }}
                                    @elseif($item->booking_type == 'hotel')
                                        <i class="fa-solid fa-hotel text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'تأمين حجوزات الفنادق' : 'Hotels Cross-Sell' }}
                                    @else
                                        <i class="fa-solid fa-shield-halved text-success me-2"></i>{{ app()->getLocale() == 'ar' ? 'تأمين سفر مستقل' : 'Standalone Insurance' }}
                                    @endif
                                </strong>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $item->count }}</span></td>
                            <td class="font-bold text-main">{{ number_format($item->revenue, 2) }} SAR</td>
                            <td class="text-muted">{{ number_format($item->cost, 2) }} SAR</td>
                            <td>
                                <span class="badge bg-success" style="font-size: 0.85rem;">
                                    +{{ number_format($item->profit, 2) }} SAR
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; width: 70px;">
                                        <div class="progress-bar bg-success" style="width: {{ min(100, $itemMargin) }}%;"></div>
                                    </div>
                                    <strong style="color: var(--text-main);">{{ round($itemMargin, 1) }}%</strong>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">{{ app()->getLocale() == 'ar' ? 'لا توجد معاملات خلال هذه الفترة' : 'No transactions in this period' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Profit Entries Table -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'أحدث عمليات أرباح وثائق التأمين' : 'Recent Insurance Profit Entries' }}
            </h6>
            <a href="{{ route('admin.insurance.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: var(--radius-md);">
                {{ app()->getLocale() == 'ar' ? 'عرض كل الوثائق' : 'View All Policies' }}
            </a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                <thead style="background: rgba(0,0,0,0.02);">
                    <tr>
                        <th>{{ app()->getLocale() == 'ar' ? 'رقم الوثيقة' : 'Policy No' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'القناة' : 'Channel' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'سعر البيع' : 'Selling Price' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'التكلفة' : 'Cost' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'صافي هامش الربح' : 'Net Margin Profit' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'التاريخ' : 'Date' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPolicies ?? [] as $rp)
                        <tr>
                            <td>
                                <a href="{{ route('admin.insurance.show', $rp->id) }}" class="font-bold text-primary">{{ $rp->policy_number }}</a>
                            </td>
                            <td>{{ $rp->user ? $rp->user->name : (app()->getLocale() == 'ar' ? 'زائر' : 'Guest') }}</td>
                            <td><span class="badge bg-light text-dark border">{{ ucfirst($rp->booking_type) }}</span></td>
                            <td class="font-bold text-main">{{ number_format($rp->selling_price, 2) }} SAR</td>
                            <td class="text-muted">{{ number_format($rp->net_cost, 2) }} SAR</td>
                            <td class="font-bold text-success">+{{ number_format($rp->platform_profit, 2) }} SAR</td>
                            <td class="text-muted">{{ $rp->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">{{ app()->getLocale() == 'ar' ? 'لا توجد وثائق مسجلة حديثاً' : 'No recent policies' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
