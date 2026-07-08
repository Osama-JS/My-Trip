# مهام تنفيذ نظام المصادقة عبر واتساب (OTP) وتحديثات الملف الشخصي للضيوف

## Backend (Laravel API)
- [x] إنشاء كلاس `app/Services/WhatsAppService.php` لدعم إرسال OTP عبر Green API.
- [x] تحديث المتغيرات البيئية `GREEN_API_ID_INSTANCE` و `GREEN_API_TOKEN_INSTANCE` في `.env`.
- [x] تعديل نموذج `User.php` لإضافة دالة `isProfileComplete()` لمعرفة هل المستخدم مكتمل أو ضيف.
- [x] إضافة مسار API جديد `request-otp` في `AuthController` لإرسال الـ OTP للأرقام المسجلة والجديدة.
- [x] إضافة مسار API جديد `verify-phone-otp` للتحقق وتسجيل الدخول / إنشاء حساب الضيف.
- [x] إضافة مسار API جديد `complete-profile` للمستخدم الضيف لاستكمال بياناته.
- [x] تعديل `FlightController` و `HotelController` لإرجاع `403 PROFILE_INCOMPLETE` إذا كان المستخدم ضيفاً ويحاول الحجز.

## Frontend (Laravel Blade - Customer Web Portal)
- [x] إضافة تصميم نافذة تسجيل الدخول برقم الهاتف / OTP بجوار خيار الايميل في `login.blade.php`.
- [x] إنشاء مسارات Web Session للـ OTP في `AuthenticatedSessionController.php` و `routes/web.php`.
- [x] ربط واجهة الويب بنظام الـ OTP وإدارة التوجيهات (Redirects) إلى صفحة استكمال البيانات عند محاولة الحجز كضيف عبر `FrontendController.php`.
- [x] تصميم واجهة إكمال الملف الشخصي `complete_profile.blade.php` للضيوف.

## Mobile App (Flutter - My-Trip-app)
- [x] تحليل وبناء تصميم نظام تسجيل الدخول عبر OTP.
- [x] تعديل شاشة الدخول لتشمل إدخال رقم الهاتف وإرسال طلب OTP لـ `request-otp`.
- [x] بناء شاشة التحقق من الـ OTP وربطها مع `verify-phone-otp`.
- [x] إضافة شاشة استكمال البيانات كخطوة إلزامية قبل تأكيد الحجز وربطها مع `complete-profile`.
