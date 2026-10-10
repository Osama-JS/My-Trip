@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'حالة النظام والخدمات المباشرة' : 'Live System & Services Status')

@push('styles')
<style>
    .status-hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: var(--radius-lg);
        padding: 24px;
        color: #fff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .pulse-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        background-color: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse-ring 2s infinite;
    }
    @keyframes pulse-ring {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .service-row-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 16px 20px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .service-row-card:hover {
        border-color: var(--primary);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }
    .gateway-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
    }
    .gateway-pill {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 14px;
        text-align: center;
        transition: all 0.2s ease;
    }
    .gateway-pill:hover {
        border-color: var(--primary);
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                <i class="fa-solid fa-tower-broadcast text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'حالة النظام والخدمات المباشرة' : 'Live System & Services Status' }}
            </h1>
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                {{ app()->getLocale() == 'ar' ? 'مراقبة فورية لأداء محركات الحجز، بوابات الدفع، الاتصال بالخادم، ومعدل الاستجابة.' : 'Real-time performance monitoring of booking engines, payment gateways, and API latencies.' }}
            </span>
        </div>

        <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
            <span style="font-size: 0.75rem; color: var(--text-muted); background: var(--bg-card); border: 1px solid var(--border-color); padding: 6px 12px; border-radius: var(--radius-md);">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> {{ app()->getLocale() == 'ar' ? 'آخر تحديث:' : 'Last update:' }} <strong id="lastUpdateText">{{ now()->format('H:i:s') }}</strong>
            </span>
            <button type="button" class="btn btn-primary" id="refreshStatusBtn" style="background: var(--primary); border: none; font-weight: 700; border-radius: var(--radius-md);">
                <i class="fa-solid fa-arrows-rotate me-1" id="refreshIcon"></i> {{ app()->getLocale() == 'ar' ? 'تحديث فوري' : 'Refresh Now' }}
            </button>
            <a href="{{ route('admin.system.health') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-server me-1"></i> {{ app()->getLocale() == 'ar' ? 'مركز العمليات والصيانة' : 'Operations Hub' }}
            </a>
        </div>
    </div>

    <!-- Hero Operational Banner -->
    <div class="status-hero-card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span class="pulse-indicator"></span>
                <div>
                    <h3 style="font-size: 1.35rem; font-weight: 900; margin: 0; color: #fff;">
                        {{ app()->getLocale() == 'ar' ? 'كافة الخدمات والأنظمة تعمل بكفاءة تامة' : 'All Systems & Engines Operational' }}
                    </h3>
                    <p style="margin: 0.25rem 0 0; color: #94a3b8; font-size: 0.85rem;">
                        {{ app()->getLocale() == 'ar' ? 'تحديثات حية وتزامن مستمر مع شبكات الحجز العالمية وبوابات الدفع الإلكترونية.' : 'Live telemetry across flight search, hotel suppliers, and financial settlement channels.' }}
                    </p>
                </div>
            </div>

            <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                <div>
                    <span style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; display: block;">{{ app()->getLocale() == 'ar' ? 'معدل الجاهزية (Uptime)' : 'Uptime' }}</span>
                    <strong style="font-size: 1.35rem; color: #10b981; font-weight: 900;">99.98%</strong>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; display: block;">{{ app()->getLocale() == 'ar' ? 'زمن استجابة DB' : 'DB Latency' }}</span>
                    <strong style="font-size: 1.35rem; color: #38bdf8; font-weight: 900;" id="heroDbLatency">{{ $health['database']['latency_ms'] ?? 120 }} ms</strong>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; display: block;">{{ app()->getLocale() == 'ar' ? 'حالة السيرفر' : 'Server Load' }}</span>
                    <strong style="font-size: 1.35rem; color: #22c55e; font-weight: 900;">{{ app()->getLocale() == 'ar' ? 'ممتازة (Normal)' : 'Normal' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- 24-Hour Telemetry & Latency Chart -->
    <div class="admin-card p-4 mb-4">
        <div class="card-header-flex p-0 pb-3 mb-3" style="border-bottom: 1px solid var(--border-color);">
            <div>
                <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 0;">
                    <i class="fa-solid fa-chart-line text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'رسم بياني لزمن الاستجابة خلال 24 ساعة (Response Latency)' : '24-Hour Response Latency Telemetry' }}
                </h4>
                <small style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'تتبع سرعة الاستجابة لمحرك البحث عن الطيران والفنادق ومحرك الحجوزات (بالملي ثانية)' : 'Tracking search engine & booking pipeline speed across 2-hour sample intervals (ms)' }}</small>
            </div>
        </div>
        <div id="latencyChart" style="min-height: 250px;"></div>
    </div>

    <!-- Core Services & Components Breakdown -->
    <div class="admin-card p-4 mb-4">
        <div class="card-header-flex p-0 pb-3 mb-3" style="border-bottom: 1px solid var(--border-color);">
            <div>
                <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 0;">
                    <i class="fa-solid fa-cubes text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'حالة المكونات ومحركات الحجز الأساسية' : 'Core Engines & Subsystem Components' }}
                </h4>
                <small style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'فحص دوري لكل موديول في المنصة للتحقق من الاتصال واستمرارية الخدمة' : 'Detailed operational status and verified up-times per business module' }}</small>
            </div>
        </div>

        <div id="componentsContainer">
            @foreach($components as $comp)
            <div class="service-row-card">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(59, 130, 246, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="{{ $comp['icon'] ?? 'fa-solid fa-server' }}"></i>
                    </div>
                    <div>
                        <strong style="color: var(--text-main); font-size: 0.95rem; display: block;">{{ $comp['name'] }}</strong>
                        <small style="color: var(--text-muted); font-size: 0.8rem;">{{ $comp['description'] }}</small>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 1.5rem;">
                    <div style="text-align: center;">
                        <small style="color: var(--text-muted); font-size: 0.75rem; display: block;">{{ app()->getLocale() == 'ar' ? 'الاستجابة' : 'Latency' }}</small>
                        <strong style="color: var(--text-main); font-family: monospace; font-size: 0.85rem;">{{ $comp['latency'] }}</strong>
                    </div>

                    <div style="text-align: center;">
                        <small style="color: var(--text-muted); font-size: 0.75rem; display: block;">{{ app()->getLocale() == 'ar' ? 'الجاهزية' : 'Uptime' }}</small>
                        <strong style="color: #10b981; font-size: 0.85rem;">{{ $comp['uptime'] }}</strong>
                    </div>

                    <div>
                        @if($comp['status'] === 'operational')
                            <span class="badge-v2 badge-success"><i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() == 'ar' ? 'يعمل بكفاءة' : 'Operational' }}</span>
                        @elseif($comp['status'] === 'degraded')
                            <span class="badge-v2 badge-warning"><i class="fa-solid fa-triangle-exclamation"></i> {{ app()->getLocale() == 'ar' ? 'أداء منخفض' : 'Degraded' }}</span>
                        @else
                            <span class="badge-v2 badge-danger"><i class="fa-solid fa-circle-xmark"></i> {{ app()->getLocale() == 'ar' ? 'متوقف' : 'Outage' }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Payment Gateways Telemetry -->
    <div class="admin-card p-4 mb-4">
        <div class="card-header-flex p-0 pb-3 mb-3" style="border-bottom: 1px solid var(--border-color);">
            <div>
                <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 0;">
                    <i class="fa-solid fa-credit-card text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'حالة بوابات وقنوات الدفع المباشرة' : 'Payment Gateways & Settlement Channels' }}
                </h4>
                <small style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'جاهزية قنوات سداد وتأكيد الحجوزات الإلكترونية والتقسيط' : 'Availability status for card processing, Apple Pay, BNPL, and wire transfer endpoints' }}</small>
            </div>
        </div>

        <div class="gateway-grid">
            @foreach($paymentMethods as $pm)
            <div class="gateway-pill">
                <div style="font-size: 1.5rem; color: var(--primary); margin-bottom: 0.5rem;">
                    <i class="{{ $pm['icon'] ?? 'fa-solid fa-credit-card' }}"></i>
                </div>
                <strong style="color: var(--text-main); font-size: 0.9rem; display: block; margin-bottom: 0.35rem;">{{ $pm['title'] }}</strong>
                @if($pm['enabled'] && $pm['status'] === 'operational')
                    <span class="badge-v2 badge-success"><i class="fa-solid fa-check"></i> {{ app()->getLocale() == 'ar' ? 'نشط ومتاح' : 'Active' }}</span>
                @else
                    <span class="badge-v2 badge-danger"><i class="fa-solid fa-xmark"></i> {{ app()->getLocale() == 'ar' ? 'معطل' : 'Disabled' }}</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let latencyChart;
    const latencyData = @json($latencyData);

    document.addEventListener('DOMContentLoaded', function() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const chartOptions = {
            series: [
                { name: '{{ app()->getLocale() == "ar" ? "محرك البحث (Search)" : "Search Engine" }}', data: latencyData.search },
                { name: '{{ app()->getLocale() == "ar" ? "محرك الحجز (Booking)" : "Booking Engine" }}', data: latencyData.booking }
            ],
            chart: {
                type: 'area',
                height: 250,
                toolbar: { show: false },
                fontFamily: 'inherit',
                background: 'transparent'
            },
            colors: ['#3b82f6', '#10b981'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2.5 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: latencyData.labels,
                labels: { style: { colors: 'var(--text-muted)', fontSize: '11px' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: 'var(--text-muted)', fontSize: '11px' },
                    formatter: val => Math.round(val) + ' ms'
                }
            },
            grid: {
                borderColor: 'var(--border-color)',
                strokeDashArray: 4
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                y: { formatter: val => val + ' ms' }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                labels: { colors: 'var(--text-main)' }
            }
        };

        const chartEl = document.querySelector("#latencyChart");
        if (chartEl && typeof ApexCharts !== 'undefined') {
            latencyChart = new ApexCharts(chartEl, chartOptions);
            latencyChart.render();
        }

        // Live Refresh Button
        $('#refreshStatusBtn').on('click', function() {
            fetchLiveData();
        });

        // Auto Poll every 45 seconds
        setInterval(fetchLiveData, 45000);
    });

    function fetchLiveData() {
        const icon = $('#refreshIcon');
        icon.addClass('fa-spin');

        $.get("{{ route('system.status.public.data') }}", function(res) {
            if (res.success) {
                $('#lastUpdateText').text(new Date().toLocaleTimeString());
                if (res.currentLatency) {
                    $('#heroDbLatency').text(res.currentLatency + ' ms');
                }
                if (latencyChart && res.latencyData) {
                    latencyChart.updateSeries([
                        { name: '{{ app()->getLocale() == "ar" ? "محرك البحث (Search)" : "Search Engine" }}', data: res.latencyData.search },
                        { name: '{{ app()->getLocale() == "ar" ? "محرك الحجز (Booking)" : "Booking Engine" }}', data: res.latencyData.booking }
                    ]);
                }
                Notify.success('{{ app()->getLocale() == "ar" ? "تم تحديث مؤشرات النظام بنجاح" : "System telemetry updated" }}');
            }
        }).always(function() {
            icon.removeClass('fa-spin');
        });
    }
</script>
@endpush
