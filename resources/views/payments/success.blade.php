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
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-box">
            <i class="fas fa-check"></i>
        </div>
        <h1>تم الدفع بنجاح!</h1>
        <p>لقد استلمنا دفعتك بنجاح. سيتم تأكيد حجزك وإرسال التفاصيل إلى بريدك الإلكتروني قريباً.</p>
        
        @if(request('source') === 'api')
            <p style="font-size: 0.9rem; margin-top: 10px; color: var(--success)">يمكنك العودة إلى التطبيق الآن.</p>
        @else
            <a href="{{ route('home') }}" class="btn-home">العودة للرئيسية</a>
        @endif
    </div>
</body>
</html>
