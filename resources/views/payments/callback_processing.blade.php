<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جاري التحقق من الدفع...</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
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
            padding: 40px;
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.1);
            max-width: 400px;
            width: 90%;
        }
        .spinner {
            border: 4px solid rgba(255,255,255,0.1);
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        h2 { margin-bottom: 10px; font-weight: 700; }
        p { color: #94a3b8; }
    </style>
</head>
<body>
    <div class="card">
        <div class="spinner"></div>
        <h2>جاري التحقق من عملية الدفع</h2>
        <p>يرجى الانتظار، سيتم توجيهك تلقائياً خلال لحظات...</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const paymentType = "{{ $payment_type }}";
            const checkoutId = "{{ $checkout_id }}";
            const paymentId = "{{ $payment_id }}";
            const source = "{{ $source }}";

            $.ajax({
                url: "/api/payment/verify",
                method: "POST",
                headers: {
                    'Accept-Language': 'ar'
                },
                data: {
                    payment_type: paymentType,
                    checkout_id: checkoutId,
                    payment_id: paymentId
                },
                success: function(response) {
                    window.location.href = "{{ route('payments.web.success') }}?source=" + source + "&payment_type=" + paymentType;
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'فشلت عملية التحقق من الدفع';
                    window.location.href = "{{ route('payments.web.failure') }}?error=" + encodeURIComponent(errorMsg) + "&source=" + source;
                }
            });
        });
    </script>
</body>
</html>
