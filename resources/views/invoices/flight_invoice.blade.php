<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>فاتورة ضريبية</title>
    <style>
        body {
            font-family: 'cairo', sans-serif;
            direction: rtl;
            text-align: right;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
        }
        .details {
            margin-top: 30px;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: right;
        }
        th {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>فاتورة ضريبية</h1>
        <p>رقم الفاتورة: {{ $booking->booking_reference }}</p>
        <p>التاريخ: {{ $booking->created_at->format('Y-m-d') }}</p>
    </div>

    <div class="details">
        <h3>بيانات العميل</h3>
        <p>الاسم: {{ $booking->passengers->first()->first_name ?? 'N/A' }} {{ $booking->passengers->first()->last_name ?? '' }}</p>
        <p>رقم الهاتف: {{ $booking->contact_phone }}</p>
    </div>

    <div class="details">
        <h3>تفاصيل الرحلة</h3>
        <table>
            <thead>
                <tr>
                    <th>الوصف</th>
                    <th>المبلغ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>حجز طيران ({{ $booking->booking_reference }})</td>
                    <td>{{ $booking->total_amount }} {{ $booking->currency }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th>الإجمالي</th>
                    <th>{{ $booking->total_amount }} {{ $booking->currency }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div style="margin-top: 50px; text-align: center;">
        <p>شكراً لاختياركم خدماتنا</p>
    </div>
</body>
</html>
