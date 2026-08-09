@extends('layouts.app')

@section('title', __('User Activity') . ' - ' . $user->full_name)

@section('content')

{{-- ═══ Page Header / Breadcrumb ═══ --}}
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('Users') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $user->full_name }}</a></li>
    </ol>
</div>

<div class="row g-4">

    {{-- ════════════════════════════════ --}}
    {{-- LEFT: Profile Card              --}}
    {{-- ════════════════════════════════ --}}
    <div class="col-xl-3 col-lg-4">
        <div class="act-profile-card">
            {{-- Avatar Header --}}
            <div class="act-profile-header">
                <div class="act-avatar-wrap position-relative d-inline-block mb-3">
                    <img src="{{ $user->profile_photo_url }}" width="100" height="100"
                         class="rounded-circle act-avatar" alt="{{ $user->full_name }}">
                    <span class="act-status-dot {{ $user->status === 'active' ? 'act-status-dot--green' : 'act-status-dot--red' }}"></span>
                </div>
                <h5 class="act-profile-name">{{ $user->full_name }}</h5>
                <p class="act-profile-email">{{ $user->email }}</p>
                <div class="d-flex justify-content-center flex-wrap gap-2">
                    <span class="act-badge act-badge--navy">{{ strtoupper(__($user->user_type)) }}</span>
                    @if($user->email_verified_at)
                        <span class="act-badge act-badge--green"><i class="fas fa-check-circle me-1"></i>{{ __('Verified') }}</span>
                    @endif
                </div>
            </div>

            {{-- Success Rate --}}
            <div class="act-profile-body">
                <div class="act-rate-wrap">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="act-rate-label">{{ __('Success Rate') }}</span>
                        <span class="act-rate-value">{{ $stats['success_rate'] }}%</span>
                    </div>
                    <div class="act-progress-track">
                        <div class="act-progress-bar" style="width: {{ $stats['success_rate'] }}%"></div>
                    </div>
                </div>

                {{-- Info List --}}
                <ul class="act-info-list">
                    <li class="act-info-item">
                        <span class="act-info-label"><i class="far fa-calendar-alt me-2"></i>{{ __('Joined Date') }}</span>
                        <span class="act-info-value">{{ $user->created_at->format('M d, Y') }}</span>
                    </li>
                    <li class="act-info-item">
                        <span class="act-info-label"><i class="fas fa-phone me-2"></i>{{ __('Phone') }}</span>
                        <span class="act-info-value">{{ $user->phone ?? '---' }}</span>
                    </li>
                    <li class="act-info-item">
                        <span class="act-info-label"><i class="fas fa-mobile-alt me-2"></i>{{ __('Device Type') }}</span>
                        <span class="act-badge act-badge--light">{{ strtoupper($user->device_type ?? 'Web') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════ --}}
    {{-- RIGHT: Stats + Activity         --}}
    {{-- ════════════════════════════════ --}}
    <div class="col-xl-9 col-lg-8">

        {{-- KPI Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="kpi-card kpi-card--blue">
                    <div class="kpi-icon-wrap"><i class="fas fa-wallet"></i></div>
                    <div class="kpi-info">
                        <span class="kpi-label">{{ __('Total Spent') }}</span>
                        <h3 class="kpi-value">
                            {{ number_format($stats['total_spent'], 2) }}
                            <small class="kpi-currency">SAR</small>
                        </h3>
                        <span class="kpi-badge kpi-badge--blue"><i class="fas fa-coins me-1"></i>{{ __('All bookings') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card kpi-card--amber">
                    <div class="kpi-icon-wrap"><i class="fas fa-calendar-check"></i></div>
                    <div class="kpi-info">
                        <span class="kpi-label">{{ __('Total Bookings') }}</span>
                        <h3 class="kpi-value">{{ $stats['total_bookings'] }}</h3>
                        <span class="kpi-badge kpi-badge--amber"><i class="fas fa-layer-group me-1"></i>{{ __('All types') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card kpi-card--red">
                    <div class="kpi-icon-wrap"><i class="fas fa-heart"></i></div>
                    <div class="kpi-info">
                        <span class="kpi-label">{{ __('Favorites') }}</span>
                        <h3 class="kpi-value">{{ $stats['favorites_count'] }}</h3>
                        <span class="kpi-badge kpi-badge--red"><i class="fas fa-bookmark me-1"></i>{{ __('Saved trips') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity Tabs Card --}}
        <div class="dash-chart-card">
            <div class="dash-chart-header">
                <div>
                    <h6 class="dash-chart-title">{{ __('Detailed Activity Logs') }}</h6>
                    <p class="dash-chart-sub">{{ __('Full history of trips, flights, hotels, searches and wishlist') }}</p>
                </div>
            </div>
            <div class="act-tabs-wrap">
                {{-- Tab Nav --}}
                <ul class="nav nav-tabs act-tab-nav border-0" id="activityTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link act-tab-link active" data-bs-toggle="tab" href="#tripsTab" role="tab">
                            <i class="fas fa-map-marked-alt"></i> {{ __('Trip Packages') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link act-tab-link" data-bs-toggle="tab" href="#flightsTab" role="tab">
                            <i class="fas fa-plane-departure"></i> {{ __('Flights') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link act-tab-link" data-bs-toggle="tab" href="#hotelsTab" role="tab">
                            <i class="fas fa-hotel"></i> {{ __('Hotels') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link act-tab-link" data-bs-toggle="tab" href="#searchesTab" role="tab">
                            <i class="fas fa-history"></i> {{ __('Search Log') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link act-tab-link" data-bs-toggle="tab" href="#favoritesTab" role="tab">
                            <i class="fas fa-heart"></i> {{ __('Wishlist') }}
                        </a>
                    </li>
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content act-tab-content">

                    {{-- ── Trip Packages ── --}}
                    <div class="tab-pane fade show active" id="tripsTab">
                        <div class="table-responsive act-table-wrap">
                            <table class="table act-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Trip Package') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->tripBookings as $booking)
                                    <tr>
                                        <td><span class="act-ref">#{{ $booking->id }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $booking->trip->image_url ?? asset('images/no-image.jpg') }}"
                                                     width="40" height="40" class="rounded-2 object-fit-cover shadow-sm">
                                                <span class="fw-semibold text-dark" style="font-size:13.5px;">{{ $booking->trip->title ?? __('N/A') }}</span>
                                            </div>
                                        </td>
                                        <td><span class="act-date">{{ $booking->created_at->format('Y-m-d') }}</span></td>
                                        <td><span class="act-amount">{{ number_format($booking->total_price, 2) }} <small class="text-muted">SAR</small></span></td>
                                        <td>
                                            @php
                                                $sc = $booking->status === 'confirmed' ? 'badge-state--green'
                                                    : ($booking->status === 'cancelled' ? 'badge-state--red' : 'badge-state--amber');
                                            @endphp
                                            <span class="badge-state {{ $sc }}">{{ strtoupper($booking->status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.trip-bookings.show', $booking->id) }}" class="act-action-btn">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="act-empty-row"><i class="fas fa-folder-open d-block mb-2 fa-2x opacity-25"></i>{{ __('No trip bookings found.') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ── Flights ── --}}
                    <div class="tab-pane fade" id="flightsTab">
                        <div class="table-responsive act-table-wrap">
                            <table class="table act-table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Reference') }}</th>
                                        <th>{{ __('Route') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->flightBookings as $booking)
                                    <tr>
                                        <td><span class="act-ref">{{ $booking->booking_reference }}</span></td>
                                        <td>
                                            <span class="act-route">
                                                <i class="fas fa-plane-departure text-muted me-1" style="font-size:11px;"></i>
                                                {{ $booking->flightBooking->origin ?? '---' }}
                                                <i class="fas fa-long-arrow-alt-right mx-1 text-muted" style="font-size:11px;"></i>
                                                {{ $booking->flightBooking->destination ?? '---' }}
                                            </span>
                                        </td>
                                        <td><span class="act-date">{{ $booking->created_at->format('Y-m-d') }}</span></td>
                                        <td><span class="act-amount">{{ number_format($booking->total_amount, 2) }} <small class="text-muted">{{ $booking->currency }}</small></span></td>
                                        <td>
                                            @php
                                                $sc = $booking->status === 'confirmed' ? 'badge-state--green'
                                                    : ($booking->status === 'cancelled' ? 'badge-state--red' : 'badge-state--amber');
                                            @endphp
                                            <span class="badge-state {{ $sc }}">{{ strtoupper($booking->status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.bookings.flights.show', $booking->id) }}" class="act-action-btn">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="act-empty-row"><i class="fas fa-folder-open d-block mb-2 fa-2x opacity-25"></i>{{ __('No flight bookings found.') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ── Hotels ── --}}
                    <div class="tab-pane fade" id="hotelsTab">
                        <div class="table-responsive act-table-wrap">
                            <table class="table act-table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Reference') }}</th>
                                        <th>{{ __('Hotel') }}</th>
                                        <th>{{ __('City') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->hotelBookings as $booking)
                                    <tr>
                                        <td><span class="act-ref">{{ $booking->reference_num }}</span></td>
                                        <td><span class="fw-semibold text-dark" style="font-size:13.5px;">{{ $booking->hotel_name }}</span></td>
                                        <td><span class="act-date">{{ $booking->city_name }}</span></td>
                                        <td><span class="act-amount">{{ number_format($booking->total_price, 2) }} <small class="text-muted">{{ $booking->currency }}</small></span></td>
                                        <td>
                                            @php $sc = $booking->status === 'confirmed' ? 'badge-state--green' : 'badge-state--amber'; @endphp
                                            <span class="badge-state {{ $sc }}">{{ strtoupper($booking->status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.bookings.hotels.show', $booking->id) }}" class="act-action-btn">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="act-empty-row"><i class="fas fa-folder-open d-block mb-2 fa-2x opacity-25"></i>{{ __('No hotel bookings found.') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ── Search Log ── --}}
                    <div class="tab-pane fade" id="searchesTab">
                        <div class="act-timeline-wrap">
                            @forelse($searchLogs as $log)
                            <div class="act-timeline-item">
                                <div class="act-timeline-dot"></div>
                                <div class="act-timeline-body">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <h6 class="act-timeline-title">
                                            {{ __('Searched for flight from') }}
                                            <span class="act-timeline-code">{{ $log->origin_code }}</span>
                                            {{ __('to') }}
                                            <span class="act-timeline-code">{{ $log->destination_code }}</span>
                                        </h6>
                                        <span class="act-timeline-time">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="act-timeline-sub">
                                        <i class="far fa-calendar-alt me-1"></i>{{ __('Travel Date') }}: {{ $log->departure_date }}
                                        &nbsp;·&nbsp;
                                        <i class="fas fa-users me-1"></i>{{ $log->adults }} {{ __('Adults') }}, {{ $log->children }} {{ __('Children') }}
                                    </p>
                                </div>
                            </div>
                            @empty
                            <div class="act-empty-row text-center py-5">
                                <i class="fas fa-search fa-2x opacity-25 d-block mb-2"></i>
                                {{ __('No search logs recorded.') }}
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- ── Wishlist ── --}}
                    <div class="tab-pane fade" id="favoritesTab">
                        <div class="row g-3 p-3">
                            @forelse($user->favorites as $favorite)
                            <div class="col-md-6">
                                <div class="act-fav-card">
                                    <img src="{{ $favorite->trip->image_url ?? asset('images/no-image.jpg') }}"
                                         width="64" height="64" class="rounded-2 object-fit-cover" alt="">
                                    <div class="flex-grow-1 min-width-0">
                                        <h6 class="act-fav-title">{{ $favorite->trip->title }}</h6>
                                        <p class="act-fav-price">{{ number_format($favorite->trip->price ?? 0, 2) }} <span class="text-muted">SAR</span></p>
                                    </div>
                                    <a href="{{ route('admin.trips.edit', $favorite->trip_id) }}" class="act-action-btn act-action-btn--sm">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                            @empty
                            <div class="col-12 act-empty-row text-center py-5">
                                <i class="fas fa-heart fa-2x opacity-25 d-block mb-2"></i>
                                {{ __('Nothing in the wishlist.') }}
                            </div>
                            @endforelse
                        </div>
                    </div>

                </div>{{-- /tab-content --}}
            </div>{{-- /act-tabs-wrap --}}
        </div>{{-- /dash-chart-card --}}

    </div>{{-- /col right --}}
</div>{{-- /row --}}

@endsection

@push('styles')
<style>
/* ════════════════════════════════════
   USER ACTIVITY – Design System
   Mirrors Dashboard + Subscribers style
   ════════════════════════════════════ */
:root {
    --dash-navy: #041741;
    --dash-navy-2: #0a2456;
    --dash-gold: #f5a623;
    --dash-surface: #ffffff;
    --dash-text: #1e293b;
    --dash-muted: #64748b;
    --dash-border: #e8edf5;
    --dash-radius: 16px;
    --dash-shadow: 0 4px 24px rgba(4, 23, 65, 0.06);
    --dash-shadow-hover: 0 12px 36px rgba(4, 23, 65, 0.13);
}

/* ─── Profile Card ─── */
.act-profile-card {
    background: var(--dash-surface);
    border-radius: var(--dash-radius);
    border: 1px solid var(--dash-border);
    box-shadow: var(--dash-shadow);
    overflow: hidden;
    transition: box-shadow 0.3s;
}
.act-profile-card:hover { box-shadow: var(--dash-shadow-hover); }

.act-profile-header {
    background: linear-gradient(160deg, #f8faff 0%, #eef3ff 100%);
    border-bottom: 1px solid var(--dash-border);
    padding: 28px 20px 24px;
    text-align: center;
}
.act-avatar {
    width: 100px; height: 100px;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 6px 20px rgba(4,23,65,0.12);
}
.act-avatar-wrap { display: inline-block; }
.act-status-dot {
    position: absolute; bottom: 5px; right: 5px;
    width: 14px; height: 14px; border-radius: 50%;
    border: 3px solid #fff;
}
.act-status-dot--green { background: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.2); }
.act-status-dot--red   { background: #ef4444; }

.act-profile-name {
    font-size: 17px; font-weight: 800; color: var(--dash-text); margin-bottom: 4px;
}
.act-profile-email {
    font-size: 12.5px; color: var(--dash-muted); margin-bottom: 14px;
    word-break: break-all;
}

.act-badge {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 600;
    padding: 4px 12px; border-radius: 50px;
    border: 1px solid transparent;
}
.act-badge--navy  { background: rgba(4,23,65,0.08); color: var(--dash-navy); border-color: rgba(4,23,65,0.12); }
.act-badge--green { background: rgba(16,185,129,0.10); color: #059669; border-color: rgba(16,185,129,0.2); }
.act-badge--light { background: #f1f5f9; color: var(--dash-muted); border-color: var(--dash-border); }

.act-profile-body { padding: 20px; }

.act-rate-wrap { margin-bottom: 20px; }
.act-rate-label { font-size: 12px; font-weight: 600; color: var(--dash-muted); }
.act-rate-value { font-size: 13px; font-weight: 800; color: var(--dash-navy); }
.act-progress-track {
    height: 6px; background: #f1f5f9; border-radius: 50px; overflow: hidden;
}
.act-progress-bar {
    height: 100%; border-radius: 50px;
    background: linear-gradient(90deg, var(--dash-navy) 0%, #1a3a8f 100%);
    transition: width 1s ease;
}

.act-info-list { list-style: none; padding: 0; margin: 0; }
.act-info-item {
    display: flex; justify-content: space-between; align-items: center;
    padding: 11px 0; border-bottom: 1px solid var(--dash-border);
    gap: 10px;
}
.act-info-item:last-child { border-bottom: none; }
.act-info-label { font-size: 12.5px; color: var(--dash-muted); }
.act-info-label i { opacity: 0.6; }
.act-info-value { font-size: 13px; font-weight: 600; color: var(--dash-text); text-align: end; }

/* ─── KPI Cards ─── */
.kpi-card {
    display: flex; align-items: flex-start; gap: 18px;
    background: var(--dash-surface);
    border-radius: var(--dash-radius); padding: 24px;
    box-shadow: var(--dash-shadow); border: 1px solid var(--dash-border);
    transition: all 0.3s ease; height: 100%;
    animation: kpiFadeIn 0.6s ease backwards;
}
.kpi-card:hover { transform: translateY(-5px); box-shadow: var(--dash-shadow-hover); }
@keyframes kpiFadeIn {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.kpi-card:nth-child(1) { animation-delay: 0.0s; }
.kpi-card:nth-child(2) { animation-delay: 0.1s; }
.kpi-card:nth-child(3) { animation-delay: 0.2s; }

.kpi-icon-wrap {
    flex-shrink: 0; width: 52px; height: 52px;
    border-radius: 14px; display: flex; align-items: center;
    justify-content: center; font-size: 1.3rem;
}
.kpi-card--blue   .kpi-icon-wrap { background: rgba(4,23,65,0.09); color: var(--dash-navy); }
.kpi-card--amber  .kpi-icon-wrap { background: rgba(245,158,11,0.12); color: #d97706; }
.kpi-card--red    .kpi-icon-wrap { background: rgba(239,68,68,0.12); color: #dc2626; }

.kpi-card--blue   { border-left: 4px solid var(--dash-navy); }
.kpi-card--amber  { border-left: 4px solid #f59e0b; }
.kpi-card--red    { border-left: 4px solid #ef4444; }

[dir="rtl"] .kpi-card--blue  { border-left: none; border-right: 4px solid var(--dash-navy); }
[dir="rtl"] .kpi-card--amber { border-left: none; border-right: 4px solid #f59e0b; }
[dir="rtl"] .kpi-card--red   { border-left: none; border-right: 4px solid #ef4444; }

.kpi-info { flex: 1; }
.kpi-label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.6px; color: var(--dash-muted); display: block; margin-bottom: 6px; }
.kpi-value { font-size: 1.85rem; font-weight: 800; color: var(--dash-text); margin-bottom: 8px; line-height: 1.1; }
.kpi-currency { font-size: 13px; font-weight: 500; color: var(--dash-muted); }
.kpi-badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 50px; }
.kpi-badge--blue  { background: rgba(4,23,65,0.08); color: var(--dash-navy); }
.kpi-badge--amber { background: rgba(245,158,11,0.12); color: #b45309; }
.kpi-badge--red   { background: rgba(239,68,68,0.10); color: #dc2626; }

/* ─── Chart / Activity Card ─── */
.dash-chart-card {
    background: var(--dash-surface);
    border-radius: var(--dash-radius); border: 1px solid var(--dash-border);
    box-shadow: var(--dash-shadow); overflow: hidden;
    transition: box-shadow 0.3s;
}
.dash-chart-card:hover { box-shadow: var(--dash-shadow-hover); }
.dash-chart-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 22px 24px 16px; border-bottom: 1px solid var(--dash-border);
}
.dash-chart-title { font-size: 15px; font-weight: 700; color: var(--dash-text); margin-bottom: 3px; }
.dash-chart-sub   { font-size: 11.5px; color: var(--dash-muted); margin: 0; }

/* ─── Tabs ─── */
.act-tab-nav {
    display: flex; list-style: none; padding: 0 20px; margin: 0;
    border-bottom: 1px solid var(--dash-border);
    overflow-x: auto; gap: 0;
    -webkit-overflow-scrolling: touch;
}
.act-tab-nav::-webkit-scrollbar { height: 0; }
.act-tab-link {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 14px 18px; font-size: 13px; font-weight: 600;
    color: var(--dash-muted); text-decoration: none; white-space: nowrap;
    border-bottom: 3px solid transparent; margin-bottom: -1px;
    transition: all 0.2s ease;
}
.act-tab-link:hover { color: var(--dash-navy); }
.act-tab-link.active {
    color: var(--dash-navy);
    border-bottom-color: var(--dash-navy);
}

.act-tab-content { padding: 0; }

/* ─── Activity Tables ─── */
.act-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.act-table { width: 100% !important; }
.act-table thead th {
    background: #f8fafc !important;
    color: var(--dash-muted) !important;
    font-weight: 700 !important; font-size: 12px !important;
    text-transform: uppercase !important; letter-spacing: 0.5px !important;
    padding: 13px 16px !important; border-bottom: 1px solid var(--dash-border) !important;
    white-space: nowrap;
}
.act-table tbody tr { transition: background 0.15s ease; }
.act-table tbody tr:hover { background: rgba(4,23,65,0.025) !important; }
.act-table tbody td {
    padding: 13px 16px !important; vertical-align: middle !important;
    color: var(--dash-text) !important; font-size: 13.5px !important;
    border-bottom: 1px solid var(--dash-border) !important;
}
.act-table tbody tr:last-child td { border-bottom: none !important; }

.act-ref   { font-size: 12.5px; font-weight: 700; color: var(--dash-navy); }
.act-date  { font-size: 12.5px; color: var(--dash-muted); font-weight: 500; }
.act-amount{ font-size: 13px; font-weight: 700; color: var(--dash-text); }
.act-route { font-size: 13px; font-weight: 600; color: var(--dash-text); }

.badge-state {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 600;
    padding: 4px 12px; border-radius: 50px;
}
.badge-state--green  { background: rgba(16,185,129,0.12); color: #059669; }
.badge-state--amber  { background: rgba(245,158,11,0.12); color: #b45309; }
.badge-state--red    { background: rgba(239,68,68,0.10); color: #dc2626; }

.act-action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 34px; height: 34px; border-radius: 10px;
    background: rgba(4,23,65,0.07); color: var(--dash-navy);
    text-decoration: none; transition: all 0.2s ease;
    font-size: 13px;
}
.act-action-btn:hover { background: var(--dash-navy); color: #fff; }
.act-action-btn--sm { width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0; }

.act-empty-row {
    text-align: center; padding: 40px 20px !important;
    color: var(--dash-muted) !important; font-size: 13px; font-weight: 500;
}

/* ─── Search Timeline ─── */
.act-timeline-wrap { padding: 24px; }
.act-timeline-item {
    display: flex; gap: 16px; padding-bottom: 24px;
    position: relative;
}
.act-timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 7px; top: 16px; bottom: 0;
    width: 2px; background: var(--dash-border);
}
[dir="rtl"] .act-timeline-item:not(:last-child)::after { left: auto; right: 7px; }

.act-timeline-dot {
    flex-shrink: 0; width: 16px; height: 16px; border-radius: 50%;
    background: var(--dash-navy); border: 3px solid #fff;
    box-shadow: 0 0 0 3px rgba(4,23,65,0.12);
    margin-top: 3px; z-index: 1;
}
.act-timeline-body { flex: 1; }
.act-timeline-title { font-size: 14px; font-weight: 600; color: var(--dash-text); margin-bottom: 5px; }
.act-timeline-code {
    display: inline-block; background: rgba(4,23,65,0.09); color: var(--dash-navy);
    padding: 1px 8px; border-radius: 6px; font-size: 13px; font-weight: 700;
}
.act-timeline-time { font-size: 11.5px; color: var(--dash-muted); white-space: nowrap; flex-shrink: 0; }
.act-timeline-sub  { font-size: 12.5px; color: var(--dash-muted); margin: 0; }

/* ─── Favorites ─── */
.act-fav-card {
    display: flex; align-items: center; gap: 14px;
    background: var(--dash-surface); border: 1px solid var(--dash-border);
    border-radius: 12px; padding: 14px;
    transition: all 0.2s ease;
}
.act-fav-card:hover {
    border-color: rgba(4,23,65,0.2);
    box-shadow: 0 4px 16px rgba(4,23,65,0.07);
    transform: translateY(-2px);
}
.act-fav-title { font-size: 13.5px; font-weight: 700; color: var(--dash-text); margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.act-fav-price { font-size: 13px; font-weight: 600; color: var(--dash-muted); margin: 0; }

/* ─── Dark Mode ─── */
[data-theme-version="dark"] .act-profile-card,
[data-theme-version="dark"] .kpi-card,
[data-theme-version="dark"] .dash-chart-card {
    background: #1e1e2d !important;
    border-color: rgba(255,255,255,0.06) !important;
}
[data-theme-version="dark"] .act-profile-header { background: #161625 !important; }
[data-theme-version="dark"] .act-profile-name,
[data-theme-version="dark"] .kpi-value,
[data-theme-version="dark"] .dash-chart-title,
[data-theme-version="dark"] .act-timeline-title,
[data-theme-version="dark"] .act-fav-title,
[data-theme-version="dark"] .act-table tbody td { color: #e2e8f0 !important; }
[data-theme-version="dark"] .act-info-item { border-color: rgba(255,255,255,0.06) !important; }
[data-theme-version="dark"] .act-table thead th { background: #161625 !important; border-color: rgba(255,255,255,0.06) !important; }
[data-theme-version="dark"] .act-table tbody tr:hover { background: rgba(255,255,255,0.03) !important; }
[data-theme-version="dark"] .act-table tbody td { border-color: rgba(255,255,255,0.05) !important; }
[data-theme-version="dark"] .act-tab-nav { border-color: rgba(255,255,255,0.06) !important; }
[data-theme-version="dark"] .act-fav-card { background: #161625; border-color: rgba(255,255,255,0.08); }
[data-theme-version="dark"] .act-action-btn { background: rgba(255,255,255,0.08); color: #e2e8f0; }

@media (max-width: 576px) {
    .act-tab-link { padding: 12px 12px; font-size: 12px; }
}
</style>
@endpush
