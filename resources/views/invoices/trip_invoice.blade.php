<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>فاتورة حجز رحلة - {{ $booking->id }}</title>
    <style>
        body {
            font-family: 'cairo', sans-serif;
            direction: rtl;
            text-align: right;
            padding: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #0f4c81;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #0f4c81;
            margin-bottom: 10px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .details-section {
            margin-bottom: 30px;
        }
        .details-header {
            background-color: #f8f9fa;
            padding: 10px;
            font-weight: bold;
            border-right: 5px solid #0f4c81;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: right;
        }
        th {
            background-color: #f1f3f5;
            color: #495057;
        }
        .total-row {
            background-color: #0f4c81;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">{{ config('app.name', 'MyTrip') }}</div>
        <h1>فاتورة ضريبية / Invoice</h1>
        <p>رقم الحجز: {{ $booking->id }}</p>
    </div>

    <table style="border: none; margin-bottom: 30px;">
        <tr>
            <td style="border: none; width: 50%;">
                <strong>بيانات العميل:</strong><br>
                الاسم: {{ $booking->user->full_name ?? __('Guest') }}<br>
                الهاتف: {{ $booking->user->phone ?? '—' }}<br>
                الايميل: {{ $booking->user->email ?? '—' }}
            </td>
            <td style="border: none; width: 50%; text-align: left;">
                <strong>تفاصيل الفاتورة:</strong><br>
                تاريخ الحجز: {{ $booking->created_at->format('Y-m-d') }}<br>
                طريقة الدفع: تحويل بنكي (Bank Transfer)<br>
                حالة الدفع: مؤكد (Confirmed)
            </td>
        </tr>
    </table>

    <div class="details-section">
        <div class="details-header">بيانات الرحلة والتذاكر</div>
        <table>
            <thead>
                <tr>
                    <th>الوصف</th>
                    <th>عدد التذاكر</th>
                    <th>سعر الوحدة</th>
                    <th>المجموع</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $booking->trip->title ?? 'حجز رحلة سياحية' }}</td>
                    <td>{{ $booking->tickets_count }}</td>
                    <td>{{ number_format($booking->total_price / ($booking->tickets_count ?: 1), 2) }} ر.س</td>
                    <td>{{ number_format($booking->total_price, 2) }} ر.س</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" style="text-align: left;">الإجمالي الكلي / Total</td>
                    <td>{{ number_format($booking->total_price, 2) }} ر.س</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="details-section">
        <div class="details-header">قائمة المسافرين</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>رقم الجواز</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->passengers as $pax)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $pax->name }}</td>
                    <td>{{ $pax->passport_number ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>هذه الفاتورة تم إنشاؤها آلياً بواسطة النظام ولا تحتاج إلى ختم.</p>
        <p>شكراً لثقتكم بنا. نتمنى لكم رحلة سعيدة!</p>
        <p>{{ config('app.url') }}</p>
    </div>
</body>
</html>
