# خطة دمج بوابات الدفع (Tabby & Tamara)

## الهدف

دمج خدمات الدفع **Tabby** و **Tamara** في المشروع بشكل احترافي وآمن، مع توحيد واجهة التعامل (Interface) للسماح باستخدامها بمرونة في الـ Web والـ API.

## التغييرات المقترحة

### 1. التهيئة (Configuration)

- **[ملف]** `config/services.php`
    - إضافة مفاتيح الإعدادات لـ `tabby` و `tamara` للوصول إليها عبر `.env`.
- **[ملف]** `.env` (تحديث الملف للعميل لاحقاً، سنضيف الأمثلة في `.env.example`)
    - إضافة متغيرات البيئة:
        - `TABBY_PUBLIC_KEY`, `TABBY_SECRET_KEY`, `TABBY_MERCHANT_CODE`
        - `TAMARA_API_TOKEN`, `TAMARA_API_URL`, `TAMARA_NOTIFICATION_KEY`

### 2. البنية البرمجية (Architecture)

- **[NEW]** `App\Interfaces\PaymentGatewayInterface.php`
    - واجهة موحدة تحتوي على الدوال الأساسية:
        - `initiateCheckout(array $data): array`
        - `verifyPayment(string $paymentId): array`
        - `getPaymentStatus(string $paymentId): string`

### 3. الخدمات (Services)

- **[NEW]** `App\Services\TabbyPaymentService.php`
    - تنفيذ `PaymentGatewayInterface`.
    - التعامل مع `api.tabby.ai/api/v2/checkout`.
    - دعم إنشاء الجلسة (Session) والتحقق من الويب هوك (Webhook) أو الـ Callback.
- **[NEW]** `App\Services\TamaraPaymentService.php`
    - تنفيذ `PaymentGatewayInterface`.
    - التعامل مع `api.tamara.co`.
    - دعم `Create Checkout Session` و `Authorise Order`.

### 4. وحدات التحكم (Controllers)

- **[MODIFY]** `App\Http\Controllers\Api\PaymentController.php`
    - تعديل الكونستركتور (Constructor) لدعم `PaymentGatewayInterface` أو استخدام Factory Pattern بسيط لاختيار الخدمة بناءً على `payment_type` (مثل: `tabby`, `tamara`, `hyperpay`).
    - تحديث دالة `initiate` لتقبل بيانات إضافية مطلوبة لـ Tabby/Tamara (مثل بيانات العميل التفصيلية).

## خطة التحقق (Verification Plan)

### اختبار يدوي (Manual Scripts)

سنقوم بإنشاء ملفات PHP بسيطة في الجذر (مشابهة لـ `test_hyperpay.php`) لتجربة الاتصال بالـ API وتوليد روابط الدفع:

1.  **[NEW]** `test_tabby.php`: لإنشاء جلسة دفع Tabby وطباعة الرابط والرد.
2.  **[NEW]** `test_tamara.php`: لإنشاء جلسة دفع Tamara وطباعة الرابط والرد.

### أوامر التشغيل

```bash
# اختبار Tabby
php test_tabby.php

# اختبار Tamara
php test_tamara.php
```

### التحقق من النتائج

- يجب أن يعود الرابط `checkout_url` في الاستجابة.
- يجب أن يكون الرابط قابلاً للفتح في المتصفح ويؤدي لصفحة الدفع الصحيحة.
