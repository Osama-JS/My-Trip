<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Payment Successful') }} - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --success: #10b981;
            --primary: #4f46e5;
            --primary-light: #818cf8;
            --bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.8);
            --text: #f8fafc;
            --text-muted: #94a3b8;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Cairo', sans-serif;
            background: radial-gradient(circle at top right, #1e1b4b, #0f172a);
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 40px 30px;
            border-radius: 32px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 500px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .icon-box {
            width: 80px;
            height: 80px;
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 38px;
            margin: 0 auto 20px;
            box-shadow: 0 0 35px rgba(16, 185, 129, 0.25);
            animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes scaleIn { from { transform: scale(0); } to { transform: scale(1); } }
        
        h1 { margin: 0 0 10px; font-weight: 800; font-size: 1.85rem; color: #fff; }
        .sub-msg { color: var(--text-muted); font-size: 1rem; margin-bottom: 25px; line-height: 1.5; }
        
        .booking-info-box {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 25px;
            text-align: right;
        }
        .booking-info-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .booking-type-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
        }
        .tag-flight { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
        .tag-hotel  { background: rgba(168, 85, 247, 0.15); color: #c084fc; }
        .tag-trip   { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 0.92rem;
        }
        .info-row:last-child { margin-bottom: 0; }
        .info-label { color: var(--text-muted); }
        .info-value { font-weight: 700; color: #fff; }
        .info-highlight { color: var(--success); font-size: 1.1rem; letter-spacing: 0.5px; }

        .btn-primary-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            padding: 15px 28px;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.35);
            margin-bottom: 12px;
        }
        .btn-primary-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(79, 70, 229, 0.5);
            color: #fff;
        }
        .btn-secondary-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.07);
            color: #e2e8f0;
            padding: 12px 24px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.2s;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
        }
        .btn-secondary-action:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }
        .back-home-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            display: inline-block;
            transition: color 0.2s;
        }
        .back-home-link:hover { color: #fff; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-processing { background: rgba(251, 191, 36, 0.15); color: #fbbf24; }
        .status-confirmed  { background: rgba(16, 185, 129, 0.15); color: #10b981; }

        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .fa-spinner { animation: spin 2s linear infinite; }

        .redirect-countdown {
            font-size: 0.82rem;
            color: #94a3b8;
            margin-top: 15px;
            background: rgba(0, 0, 0, 0.2);
            padding: 8px 14px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .redirect-countdown span { color: #38bdf8; font-weight: 700; }
    </style>
</head>
<body>

    @php
        // Resolve Target URLs dynamically based on booking type
        $showUrl = route('customer.bookings.index');
        $listUrl = route('customer.bookings.index');
        $typeName = __('Bookings');

        if ($booking_type === 'flight') {
            $typeName = __('حجز طيران');
            $listUrl  = route('customer.bookings.flights');
            if ($booking && isset($booking->id)) {
                $showUrl = route('customer.bookings.show', ['id' => $booking->id, 'type' => 'flight']);
            }
        } elseif ($booking_type === 'hotel') {
            $typeName = __('حجز فندق');
            $listUrl  = route('customer.bookings.hotels');
            if ($booking && isset($booking->id)) {
                $showUrl = route('customer.bookings.show', ['id' => $booking->id, 'type' => 'hotel']);
            }
        } elseif ($booking_type === 'trip') {
            $typeName = __('حجز رحلة وباقة');
            $listUrl  = route('customer.bookings.trips');
            if ($booking && isset($booking->id)) {
                $showUrl = route('customer.bookings.show', ['id' => $booking->id, 'type' => 'trip']);
            }
        }
    @endphp

    <div class="card">
        <div class="icon-box">
            <i class="fas fa-check"></i>
        </div>
        <h1>{{ __('تم الدفع بنجاح!') }}</h1>
        <p class="sub-msg">{{ __('لقد تم تأكيد واستلام دفعتك بنجاح. بيانات حجزك موضحة أدناه.') }}</p>

        @if($booking)
            <div class="booking-info-box">
                {{-- Card Header --}}
                <div class="booking-info-header">
                    <span class="info-label">{{ __('نوع الحجز') }}</span>
                    @if($booking_type === 'flight')
                        <span class="booking-type-tag tag-flight"><i class="fas fa-plane"></i> {{ __('رحلة طيران') }}</span>
                    @elseif($booking_type === 'hotel')
                        <span class="booking-type-tag tag-hotel"><i class="fas fa-hotel"></i> {{ __('حجز فندقي') }}</span>
                    @else
                        <span class="booking-type-tag tag-trip"><i class="fas fa-map-marked-alt"></i> {{ __('باقة سياحية') }}</span>
                    @endif
                </div>

                {{-- Flight Booking Details --}}
                @if($booking_type === 'flight')
                    @php
                        $pnr = $booking->booking_reference ?? $booking->pnr_code ?? '#' . $booking->id;
                        $airline = $booking->airline_name ?? $booking->airline_code ?? __('طيران');
                        $origin = $booking->origin ?? ($booking->flightBooking->origin ?? '');
                        $destination = $booking->destination ?? ($booking->flightBooking->destination ?? '');
                    @endphp
                    <div class="info-row">
                        <span class="info-label">{{ __('رقم مرجع الحجز (PNR)') }}</span>
                        <span class="info-value info-highlight">{{ $pnr }}</span>
                    </div>
                    @if($airline)
                    <div class="info-row">
                        <span class="info-label">{{ __('الخطوط الناقلة') }}</span>
                        <span class="info-value">{{ $airline }}</span>
                    </div>
                    @endif
                    @if($origin && $destination)
                    <div class="info-row">
                        <span class="info-label">{{ __('مسار الرحلة') }}</span>
                        <span class="info-value">{{ $origin }} <i class="fas fa-arrow-left" style="font-size: 0.75rem; opacity: 0.7;"></i> {{ $destination }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">{{ __('حالة التذكرة') }}</span>
                        <span class="status-badge status-confirmed"><i class="fas fa-check-circle"></i> {{ __('مؤكد ومدفوع') }}</span>
                    </div>

                {{-- Hotel Booking Details --}}
                @elseif($booking_type === 'hotel')
                    <div class="info-row">
                        <span class="info-label">{{ __('اسم الفندق') }}</span>
                        <span class="info-value">{{ $booking->hotel_name }}</span>
                    </div>
                    @if($booking->city_name)
                    <div class="info-row">
                        <span class="info-label">{{ __('المدينة') }}</span>
                        <span class="info-value">{{ $booking->city_name }}</span>
                    </div>
                    @endif
                    @if($booking->supplier_confirmation_num)
                        <div class="info-row">
                            <span class="info-label">{{ __('رقم تأكيد المزود') }}</span>
                            <span class="info-value info-highlight">{{ $booking->supplier_confirmation_num }}</span>
                        </div>
                    @else
                        <div class="info-row">
                            <span class="info-label">{{ __('حالة الحجز لدى المزود') }}</span>
                            <span class="status-badge status-processing" id="booking-status-container">
                                <i class="fas fa-spinner"></i>
                                <span>{{ __('جاري التأكيد مع الفندق...') }}</span>
                            </span>
                        </div>
                    @endif

                {{-- Trip Booking Details --}}
                @else
                    <div class="info-row">
                        <span class="info-label">{{ __('اسم الرحلة') }}</span>
                        <span class="info-value">{{ $booking->trip->title ?? __('باقة سياحية') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('رقم الحجز') }}</span>
                        <span class="info-value info-highlight">#{{ $booking->id }}</span>
                    </div>
                    @if($booking->booking_date)
                    <div class="info-row">
                        <span class="info-label">{{ __('تاريخ السفر') }}</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d') }}</span>
                    </div>
                    @endif
                @endif
            </div>
        @endif

        {{-- Action Buttons --}}
        @if(request('source') === 'api')
            <p style="font-size: 0.95rem; margin-top: 15px; color: var(--success); font-weight: 700;">
                <i class="fas fa-mobile-alt"></i> {{ __('يمكنك الآن العودة إلى التطبيق.') }}
            </p>
        @else
            {{-- Primary: View Specific Booking Details --}}
            <a href="{{ $showUrl }}" class="btn-primary-action" id="btn-view-booking">
                <i class="fas fa-file-invoice"></i>
                @if($booking_type === 'flight')
                    <span>{{ __('عرض تذكرة وتفاصيل الحجز') }}</span>
                @elseif($booking_type === 'hotel')
                    <span>{{ __('عرض تفاصيل حجز الفندق') }}</span>
                @else
                    <span>{{ __('عرض تفاصيل الحجز') }}</span>
                @endif
            </a>

            {{-- Secondary: Go to specific category list --}}
            <a href="{{ $listUrl }}" class="btn-secondary-action">
                <i class="fas fa-list-ul"></i>
                @if($booking_type === 'flight')
                    <span>{{ __('جميع حجوزات الطيران') }}</span>
                @elseif($booking_type === 'hotel')
                    <span>{{ __('جميع حجوزات الفنادق') }}</span>
                @else
                    <span>{{ __('جميع حجوزات الرحلات') }}</span>
                @endif
            </a>

            {{-- Back to Home --}}
            <a href="{{ route('home') }}" class="back-home-link">
                <i class="fas fa-home"></i> {{ __('العودة إلى الصفحة الرئيسية') }}
            </a>

            {{-- Auto-Redirect Notice --}}
            @if($booking_type !== 'hotel' || !empty($booking->supplier_confirmation_num))
            <div class="redirect-countdown" id="countdown-box">
                <i class="fas fa-clock"></i>
                <span>{{ __('سيتم توجيهك تلقائياً خلال') }} <strong id="countdown-num">6</strong> {{ __('ثوانٍ...') }}</span>
            </div>
            @endif
        @endif
    </div>

    @if($booking && $booking_type === 'hotel' && !$booking->supplier_confirmation_num)
    <script>
        function checkStatus() {
            fetch('{{ route("customer.bookings.hotels.sync-status", $booking->id) }}', {
                method: 'POST',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'confirmed') {
                    window.location.reload();
                } else {
                    setTimeout(checkStatus, 3000);
                }
            })
            .catch(error => {
                console.error('Error checking status:', error);
                setTimeout(checkStatus, 5000);
            });
        }
        setTimeout(checkStatus, 2000);
    </script>
    @elseif(request('source') !== 'api')
    <script>
        // Auto-redirect timer to the specific booking details page
        let secondsLeft = 6;
        const countdownEl = document.getElementById('countdown-num');
        const targetUrl = "{{ $showUrl }}";

        const interval = setInterval(function() {
            secondsLeft--;
            if (countdownEl) countdownEl.textContent = secondsLeft;
            if (secondsLeft <= 0) {
                clearInterval(interval);
                window.location.href = targetUrl;
            }
        }, 1000);
    </script>
    @endif

</body>
</html>
