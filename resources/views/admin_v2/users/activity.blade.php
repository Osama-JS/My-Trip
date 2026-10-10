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
                <div class="stat-value">{{ number_format($stats['total_bookings'] ?? count($bookings ?? [])) }}</div>
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
                <!-- Tab Navigation -->
                <div class="card-header-flex p-3 m-0" style="background: var(--bg-card); border-bottom: 1px solid var(--border-color);">
                    <ul class="nav nav-pills gap-2" role="tablist">
                        <li class="nav-item">
                            <button class="btn btn-sm btn-primary active" style="font-weight: 700; border-radius: var(--radius-md);" data-bs-toggle="pill" data-bs-target="#tab-bookings">
                                <i class="fa-solid fa-receipt me-1"></i> {{ app()->getLocale() == 'ar' ? 'سجل الحجوزات' : 'Bookings' }} ({{ count($bookings ?? []) }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-sm btn-outline-secondary" style="font-weight: 700; border-radius: var(--radius-md);" data-bs-toggle="pill" data-bs-target="#tab-searches">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> {{ app()->getLocale() == 'ar' ? 'سجل عمليات البحث' : 'Searches' }} ({{ count($searches ?? []) }})
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content p-3">
                    <!-- Bookings Tab -->
                    <div class="tab-pane fade show active" id="tab-bookings">
                        <div class="table-responsive">
                            <table class="table-v2 v2-table w-100">
                                <thead>
                                    <tr>
                                        <th>{{ app()->getLocale() == 'ar' ? 'رقم الحجز' : 'Ref' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'الخدمة' : 'Service' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'التاريخ' : 'Date' }}</th>
                                        <th class="text-center">{{ app()->getLocale() == 'ar' ? 'عرض' : 'View' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bookings ?? [] as $b)
                                    <tr>
                                        <td>
                                            <span style="font-family: monospace; font-weight: 700; color: var(--primary);">
                                                #{{ $b->booking_reference ?? $b->reference_num ?? $b->id }}
                                            </span>
                                        </td>
                                        <td>
                                            @if(($b->type ?? '') === 'flight' || isset($b->flight_booking_id))
                                                <span class="badge-v2 badge-primary"><i class="fa-solid fa-plane"></i> {{ app()->getLocale() == 'ar' ? 'طيران' : 'Flight' }}</span>
                                            @elseif(isset($b->hotel_name))
                                                <span class="badge-v2 badge-success"><i class="fa-solid fa-hotel"></i> {{ app()->getLocale() == 'ar' ? 'فندق' : 'Hotel' }}</span>
                                            @else
                                                <span class="badge-v2 badge-warning"><i class="fa-solid fa-suitcase"></i> {{ app()->getLocale() == 'ar' ? 'باقة' : 'Tour' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong style="color: var(--text-main);">{{ number_format($b->total_amount ?? $b->total_price ?? 0, 2) }} SAR</strong>
                                        </td>
                                        <td>
                                            @if(in_array(strtolower($b->status ?? ''), ['confirmed', 'paid', 'completed']))
                                                <span class="badge-v2 badge-success">{{ $b->status }}</span>
                                            @elseif(in_array(strtolower($b->status ?? ''), ['pending', 'processing']))
                                                <span class="badge-v2 badge-warning">{{ $b->status }}</span>
                                            @else
                                                <span class="badge-v2 badge-danger">{{ $b->status ?? 'Cancelled' }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $b->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="text-center">
                                            @if(($b->type ?? '') === 'flight' || isset($b->flight_booking_id))
                                                <a href="{{ route('admin.bookings.flights.show', $b->id) }}" class="btn-icon"><i class="fa-solid fa-eye text-primary"></i></a>
                                            @elseif(isset($b->hotel_name))
                                                <a href="{{ route('admin.bookings.hotels.show', $b->id) }}" class="btn-icon"><i class="fa-solid fa-eye text-success"></i></a>
                                            @else
                                                <a href="{{ route('admin.trip-bookings.show', $b->id) }}" class="btn-icon"><i class="fa-solid fa-eye text-warning"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                            {{ app()->getLocale() == 'ar' ? 'لا توجد حجوزات مسجلة لهذا المستخدم.' : 'No bookings found.' }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Searches Tab -->
                    <div class="tab-pane fade" id="tab-searches">
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
