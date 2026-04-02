<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم الدفع بنجاح - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --success: #10b981;
            --bg: #0f172a;
            --text: #f8fafc;
        }
        body {
            font-family: 'Cairo', sans-serif;
            background: radial-gradient(circle at top right, #1e1b4b, #0f172a);
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        .card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            padding: 50px 30px;
            border-radius: 32px;
            border: 1px solid rgba(255,255,255,0.1);
            max-width: 450px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .icon-box {
            width: 80px;
            height: 80px;
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 40px;
            margin: 0 auto 25px;
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.2);
            animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes scaleIn { from { transform: scale(0); } to { transform: scale(1); } }
        h1 { margin-bottom: 15px; font-weight: 800; font-size: 2rem; color: #fff; }
        p { color: #94a3b8; font-size: 1.1rem; margin-bottom: 30px; line-height: 1.6; }
        .btn-home {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5, #818cf8);
            color: white;
            padding: 16px 32px;
            border-radius: 18px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }
        .btn-home:hover { transform: translateY(-2px); box-shadow: 0 15px 30px rgba(79, 70, 229, 0.4); }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .status-processing { background: rgba(251, 191, 36, 0.1); color: #fbbf24; }
        .status-confirmed { background: rgba(16, 185, 129, 0.1); color: #10b981; }

        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .fa-spinner { animation: spin 2s linear infinite; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-box">
            <i class="fas fa-check"></i>
        </div>
        <h1>تم الدفع بنجاح!</h1>
        <p>لقد استلمنا دفعتك بنجاح. تم تأكيد مسار حجزك لدينا.</p>

        @if($booking && $booking_type === 'hotel')
            <div class="booking-info" style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 20px; margin-bottom: 30px; border: 1px solid rgba(255,255,255,0.1); text-align: right;">
                <div style="font-size: 0.9rem; color: #94a3b8; margin-bottom: 5px;">{{ __('Hotel Name') }}</div>
                <div style="font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 15px;">{{ $booking->hotel_name }}</div>
                
                @if($booking->supplier_confirmation_num)
                    <div style="font-size: 0.9rem; color: #94a3b8; margin-bottom: 5px;">{{ __('Supplier Confirmation Number') }}</div>
                    <div style="font-size: 1.2rem; font-weight: 800; color: var(--success); letter-spacing: 1px;">{{ $booking->supplier_confirmation_num }}</div>
                @else
                    <div style="font-size: 0.9rem; color: #94a3b8; margin-bottom: 8px;">{{ __('Status') }}</div>
                    <div class="status-badge status-processing" id="booking-status-container">
                        <i class="fas fa-spinner"></i>
                        <span>{{ __('Processing with supplier...') }}</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #64748b; margin-top: 10px; margin-bottom: 0;">
                        {{ __('We are finalizing your reservation with the hotel provider. This usually takes a few seconds.') }}
                    </p>
                @endif
            </div>
        @endif
        
        @if(request('source') === 'api')
            <p style="font-size: 0.9rem; margin-top: 10px; color: var(--success)">{{ __('You can return to the app now.') }}</p>
        @else
            <a href="{{ route('customer.bookings.hotels') }}" class="btn-home" style="margin-bottom: 15px;">{{ __('My Bookings') }}</a>
            <br>
            <a href="{{ route('home') }}" style="color: #94a3b8; text-decoration: none; font-size: 0.9rem; font-weight: 600;">{{ __('Back to Home') }}</a>
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
    @endif
</body>
</html>
