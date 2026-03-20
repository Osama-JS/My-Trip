<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فشلت عملية الدفع - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --danger: #ef4444;
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
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 40px;
            margin: 0 auto 25px;
            box-shadow: 0 0 30px rgba(239, 68, 68, 0.2);
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }
        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
        h1 { margin-bottom: 15px; font-weight: 800; font-size: 2rem; color: #fff; }
        .error-message { background: rgba(0,0,0,0.2); padding: 15px; border-radius: 12px; margin-bottom: 30px; border-left: 4px solid var(--danger); }
        p { color: #94a3b8; font-size: 1rem; line-height: 1.6; }
        .btn-retry {
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
        .btn-retry:hover { transform: translateY(-2px); box-shadow: 0 15px 30px rgba(79, 70, 229, 0.4); }
        .support-link { margin-top: 20px; display: block; color: #94a3b8; font-size: 0.9rem; text-decoration: none; }
        .support-link:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-box">
            <i class="fas fa-times"></i>
        </div>
        <h1>عذراً، فشلت العملية</h1>
        
        <div class="error-message">
            <p>{{ $error ?? 'حدث خطأ غير متوقع أثناء معالجة عملية الدفع.' }}</p>
        </div>

        <p style="margin-bottom: 30px;">لم يتم خصم أي مبالغ من حسابك. يمكنك المحاولة مرة أخرى أو اختيار وسيلة دفع بديلة.</p>
        
        @if(request('source') === 'api')
            <p style="font-size: 0.9rem; margin-top: 10px; color: var(--danger)">يرجى العودة إلى التطبيق للمحاولة مجدداً.</p>
        @else
            <a href="javascript:history.back()" class="btn-retry">المحاولة مرة أخرى</a>
            <a href="#" class="support-link">هل تواجه مشكلة؟ اتصل بالدعم الفني</a>
        @endif
    </div>
</body>
</html>
