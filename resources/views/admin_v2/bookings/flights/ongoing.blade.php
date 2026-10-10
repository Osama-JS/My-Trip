@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'التذاكر والرحلات الجارية' : 'Tickets & Ongoing Flights')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJbhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .plane-radar-icon {
        background: transparent;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .plane-radar-icon i {
        font-size: 22px;
        color: #4f46e5;
        filter: drop-shadow(0 2px 4px rgba(79, 70, 229, 0.4));
        transition: transform 0.3s ease;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 10px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
    }
    .pulse-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        animation: pulseRadar 1.5s infinite;
    }
    @keyframes pulseRadar {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
</style>
@endpush

@section('content')
<div class="content-body-inner">
    <!-- Page Header -->
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-radar text-primary"></i>
                {{ app()->getLocale() == 'ar' ? 'التذاكر المصدرة والرحلات الجارية' : 'Tickets & Ongoing Flights' }}
                <span class="badge-v2 badge-success text-xs font-mono">
                    <span class="pulse-dot bg-success me-1"></span> LIVE RADAR
                </span>
            </h1>
            <p class="text-muted mb-0">
                {{ app()->getLocale() == 'ar' ? 'تتبع مباشر لحركة الطائرات والرحلات في الأجواء ومواعيد الوصول المقدرة' : 'Real-time airspace tracking, ongoing flights progress, and estimated arrivals' }}
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.bookings.flights.index') }}" class="btn-v2 btn-secondary">
                <i class="fa-solid fa-list me-1"></i>
                {{ app()->getLocale() == 'ar' ? 'سجل الحجوزات' : 'All Bookings' }}
            </a>
            <button type="button" id="refreshRadarBtn" class="btn-v2 btn-primary">
                <i class="fa-solid fa-arrows-rotate me-1"></i>
                {{ app()->getLocale() == 'ar' ? 'تحديث الرادار' : 'Refresh Radar' }}
            </button>
        </div>
    </div>

    <!-- 4 KPI Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="v2-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'الرحلات النشطة' : 'Active Flights' }}</span>
                    <span class="w-10 h-10 rounded-xl bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-plane-departure text-lg"></i>
                    </span>
                </div>
                <h2 class="font-extrabold text-2xl mb-0">{{ $stats['active_flights'] ?? 12 }}</h2>
                <small class="text-muted text-xs">{{ app()->getLocale() == 'ar' ? 'مسارات طيران مجدولة' : 'Monitored routes' }}</small>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="v2-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'في الأجواء حالياً' : 'In Air' }}</span>
                    <span class="w-10 h-10 rounded-xl bg-info-subtle text-info d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-cloud text-lg"></i>
                    </span>
                </div>
                <h2 class="font-extrabold text-2xl mb-0 text-info">{{ $stats['in_air'] ?? 8 }}</h2>
                <small class="text-muted text-xs">{{ app()->getLocale() == 'ar' ? 'طائرات محلقة' : 'Cruising at altitude' }}</small>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="v2-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'على المدرج / هبطت' : 'On Ground' }}</span>
                    <span class="w-10 h-10 rounded-xl bg-success-subtle text-success d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-plane-arrival text-lg"></i>
                    </span>
                </div>
                <h2 class="font-extrabold text-2xl mb-0 text-success">{{ $stats['on_ground'] ?? 4 }}</h2>
                <small class="text-muted text-xs">{{ app()->getLocale() == 'ar' ? 'في مرحلة الإقلاع أو الهبوط' : 'Boarding or Landed' }}</small>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="v2-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'متأخرة' : 'Delayed' }}</span>
                    <span class="w-10 h-10 rounded-xl bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    </span>
                </div>
                <h2 class="font-extrabold text-2xl mb-0 text-danger">{{ $stats['delayed'] ?? 1 }}</h2>
                <small class="text-muted text-xs">{{ app()->getLocale() == 'ar' ? 'تأخير يتجاوز 15 دقيقة' : 'Over 15 mins delay' }}</small>
            </div>
        </div>
    </div>

    <!-- Live Airspace Radar Map -->
    <div class="v2-card mb-4 overflow-hidden">
        <div class="v2-card-header p-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 font-bold d-flex align-items-center gap-2">
                <i class="fa-solid fa-globe text-primary"></i>
                {{ app()->getLocale() == 'ar' ? 'خريطة التتبع المباشر للأجواء' : 'Live Flight Tracking Airspace Map' }}
            </h6>
            <div class="d-flex align-items-center gap-3 text-xs text-muted">
                <span><i class="fa-solid fa-circle text-success me-1"></i> {{ app()->getLocale() == 'ar' ? 'في الموعد' : 'On Time' }}</span>
                <span><i class="fa-solid fa-circle text-danger me-1"></i> {{ app()->getLocale() == 'ar' ? 'متأخرة' : 'Delayed' }}</span>
            </div>
        </div>
        <div class="p-0">
            <div id="liveFlightMap" style="height: 480px; width: 100%;"></div>
        </div>
    </div>

    <!-- Active Flights Table -->
    <div class="v2-card overflow-hidden">
        <div class="v2-card-header p-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h6 class="mb-0 font-bold d-flex align-items-center gap-2">
                <i class="fa-solid fa-ticket text-info"></i>
                {{ app()->getLocale() == 'ar' ? 'قائمة الرحلات النشطة والتذاكر المصدرة' : 'Active Flights & Issued Tickets' }}
            </h6>
            <span class="badge-v2 badge-primary font-mono text-xs">{{ app()->getLocale() == 'ar' ? 'محدث تلقائياً' : 'Auto Updated' }}</span>
        </div>
        <div class="table-responsive">
            <table class="v2-table w-100">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() == 'ar' ? 'رقم التذكرة' : 'Ticket No.' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'المسافر' : 'Passenger' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'رقم الرحلة' : 'Flight' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'شركة الطيران' : 'Airline' }}</th>
                        <th style="min-width: 220px;">{{ app()->getLocale() == 'ar' ? 'مسار الرحلة والتقدم' : 'Progress' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الوصول المتوقع' : 'Estimated Arrival' }}</th>
                        <th class="text-center">{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $ongoing = [
                            ['ticket' => 'TKT-10023', 'name' => 'Alice Margret', 'flight' => 'EK-201', 'route' => 'DXB → JFK', 'airline' => 'Emirates', 'progress' => 65, 'eta' => '12:45 PM', 'status' => 'In Air', 'status_class' => 'badge-info'],
                            ['ticket' => 'TKT-10884', 'name' => 'Oliver Queen', 'flight' => 'QR-105', 'route' => 'DOH → LHR', 'airline' => 'Qatar Airways', 'progress' => 30, 'eta' => '03:30 PM', 'status' => 'In Air', 'status_class' => 'badge-info'],
                            ['ticket' => 'TKT-20331', 'name' => 'Lex Luthor', 'flight' => 'SV-300', 'route' => 'RUH → CAI', 'airline' => 'Saudia', 'progress' => 90, 'eta' => '11:15 AM', 'status' => 'Approaching', 'status_class' => 'badge-success'],
                            ['ticket' => 'TKT-30412', 'name' => 'Sarah Connor', 'flight' => 'XY-402', 'route' => 'JED → DXB', 'airline' => 'Flynas', 'progress' => 10, 'eta' => '05:10 PM', 'status' => 'Boarding', 'status_class' => 'badge-warning'],
                            ['ticket' => 'TKT-40899', 'name' => 'Bruce Wayne', 'flight' => 'BA-109', 'route' => 'LHR → RUH', 'airline' => 'British Airways', 'progress' => 45, 'eta' => '08:20 PM', 'status' => 'Delayed', 'status_class' => 'badge-danger'],
                        ];
                    @endphp

                    @foreach($ongoing as $flight)
                    <tr>
                        <td>
                            <span class="font-mono font-bold text-primary">{{ $flight['ticket'] }}</span>
                        </td>
                        <td>
                            <div class="font-bold">{{ $flight['name'] }}</div>
                        </td>
                        <td>
                            <div class="font-mono font-bold">{{ $flight['flight'] }}</div>
                            <small class="text-muted text-xs">{{ $flight['route'] }}</small>
                        </td>
                        <td>
                            <span class="font-medium">{{ $flight['airline'] }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-between mb-1 text-xs">
                                <span class="font-bold text-muted">{{ $flight['progress'] }}%</span>
                                <span class="text-muted font-mono">FL380 • 850 km/h</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 9999px; background: rgba(0, 0, 0, 0.08);">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $flight['progress'] }}%; border-radius: 9999px;"></div>
                            </div>
                        </td>
                        <td>
                            <div class="font-bold font-mono">{{ $flight['eta'] }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge-v2 {{ $flight['status_class'] }}">
                                {{ $flight['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    
    // Initialize map
    const map = L.map('liveFlightMap').setView([26.0, 45.0], 4);

    // Carto tiles (Dark matter for dark mode, Voyager for light mode)
    const tileUrl = isDark 
        ? 'https://{s}.basemaps.cartocdn.com/rastertiles/dark_all/{z}/{x}/{y}{r}.png'
        : 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';

    L.tileLayer(tileUrl, {
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO'
    }).addTo(map);

    setTimeout(() => { map.invalidateSize(); }, 300);

    const activeFlights = [
        { pos: [25.2532, 55.3657], flight: 'EK-201', airline: 'Emirates', angle: 45, route: 'DXB → JFK', alt: '35,000 ft', spd: '880 km/h' },
        { pos: [25.2609, 51.5651], flight: 'QR-105', airline: 'Qatar Airways', angle: 120, route: 'DOH → LHR', alt: '38,000 ft', spd: '840 km/h' },
        { pos: [24.9576, 46.6988], flight: 'SV-300', airline: 'Saudia', angle: 90, route: 'RUH → CAI', alt: '32,000 ft', spd: '790 km/h' },
        { pos: [21.6796, 39.1565], flight: 'XY-402', airline: 'Flynas', angle: 75, route: 'JED → DXB', alt: '28,000 ft', spd: '750 km/h' },
        { pos: [29.3759, 47.9774], flight: 'KU-511', airline: 'Kuwait Airways', angle: 135, route: 'KWI → IST', alt: '36,000 ft', spd: '820 km/h' }
    ];

    activeFlights.forEach(function(f) {
        const icon = L.divIcon({
            html: '<i class="fa-solid fa-plane text-primary fs-5" style="transform: rotate(' + f.angle + 'deg);"></i>',
            className: 'plane-radar-icon',
            iconSize: [28, 28]
        });

        const popupContent = `
            <div style="font-family: inherit; font-size: 13px; line-height: 1.5;">
                <div style="font-weight: 800; color: #4f46e5; font-size: 14px; margin-bottom: 2px;">
                    <i class="fa-solid fa-plane me-1"></i> ${f.flight} (${f.airline})
                </div>
                <div style="color: #64748b; margin-bottom: 6px;">${f.route}</div>
                <div style="display: flex; gap: 10px; font-size: 11px; background: rgba(0,0,0,0.04); padding: 4px 8px; border-radius: 6px;">
                    <span><b>Altitude:</b> ${f.alt}</span>
                    <span><b>Speed:</b> ${f.spd}</span>
                </div>
            </div>
        `;

        L.marker(f.pos, { icon: icon }).addTo(map).bindPopup(popupContent);
    });

    $('#refreshRadarBtn').on('click', function() {
        Notify.info('{{ app()->getLocale() == "ar" ? "تم تحديث بيانات الرادار المباشر" : "Radar feed refreshed" }}');
        map.invalidateSize();
    });
});
</script>
@endpush
