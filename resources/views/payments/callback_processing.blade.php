<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>جاري التحقق من الدفع...</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4f46e5; --bg: #0f172a; --text: #f8fafc; }
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
            padding: 40px;
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.1);
            max-width: 420px;
            width: 90%;
        }
        .spinner {
            border: 4px solid rgba(255,255,255,0.1);
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            width: 50px; height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }
        h2 { margin-bottom: 10px; font-weight: 700; }
        p  { color: #94a3b8; font-size: 0.95rem; }
        .error-msg { color: #ef4444; margin-top: 15px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="card">
        <div class="spinner" id="spinner"></div>
        <h2>جاري التحقق من عملية الدفع</h2>
        <p>يرجى الانتظار، سيتم توجيهك تلقائياً خلال لحظات...</p>
        <p id="error-msg" class="error-msg" style="display:none;"></p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Data from the callback URL (passed by blade)
            const paymentType = "{{ $payment_type }}";
            const paymentId   = "{{ $payment_id }}";
            const checkoutId  = "{{ $checkout_id }}";
            const bookingId   = "{{ $booking_id }}";
            const bookingType = "{{ $booking_type }}";
            const source      = "{{ $source }}";

            // Build payload for /payments/verify (public web route, needs CSRF)
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const payload = new URLSearchParams();
            payload.append('_token', token);
            payload.append('payment_type', paymentType);
            if (paymentId) payload.append('payment_id', paymentId);
            if (checkoutId) payload.append('checkout_id', checkoutId);
            if (bookingId) payload.append('booking_id', bookingId);
            payload.append('type', bookingType || 'trip');

            fetch("{{ route('payments.web.verify') }}", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: payload.toString()
            })
            .then(response => {
                // If it's an HTML error (e.g. 500 without json), handle it
                if (!response.headers.get("content-type")?.includes("application/json")) {
                    throw new Error("Invalid response from server");
                }
                return response.json().then(data => ({ status: response.status, ok: response.ok, data }));
            })
            .then(({ status, ok, data }) => {
                if (ok && !data.error) {
                    if (window.FlutterBridge) {
                        window.FlutterBridge.postMessage(JSON.stringify({ success: true, message: 'عملية الدفع تمت بنجاح' }));
                    } else {
                        // Build success redirect URL with all context
                        const params = new URLSearchParams({
                            booking_id:     data.booking_id || bookingId,
                            type:           data.type       || bookingType,
                            source:         source,
                            payment_type:   paymentType,
                        });
                        window.location.href = "{{ route('payments.web.success') }}?" + params.toString();
                    }
                } else {
                    let msg = 'فشلت عملية التحقق من الدفع';
                    if (data && data.message) msg = data.message;
                    else if (data && data.errors) msg = data.errors;
                    
                    if (window.FlutterBridge) {
                        window.FlutterBridge.postMessage(JSON.stringify({ success: false, message: typeof msg === 'string' ? msg : JSON.stringify(msg) }));
                    } else {
                        showError(typeof msg === 'string' ? msg : JSON.stringify(msg));
                    }
                }
            })
            .catch(error => {
                if (window.FlutterBridge) {
                    window.FlutterBridge.postMessage(JSON.stringify({ success: false, message: 'حدث خطأ أثناء الاتصال بالخادم' }));
                } else {
                    showError('حدث خطأ أثناء الاتصال بالخادم');
                }
            });

            function showError(message) {
                document.getElementById('spinner').style.display = 'none';
                const errEl = document.getElementById('error-msg');
                errEl.innerText = message;
                errEl.style.display = 'block';

                // Auto-redirect to failure page after 3 seconds
                setTimeout(function () {
                    window.location.href = "{{ route('payments.web.failure') }}?error=" + encodeURIComponent(message) + "&source=" + source;
                }, 3000);
            }
        });
    </script>
</body>
</html>
