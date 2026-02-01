# تحليل العمليات وتصميم قاعدة بيانات السجلات والحجوزات

لضمان تسجيل كل حركة وعملية تتم على الموقع بشكل دقيق وآمن، قمنا بتحليل جميع العمليات في نظام `FlightController` و `TraveloproService` واقتراح هيكلية قاعدة البيانات المناسبة.

## 1. تحليل العمليات التي تستوجب التسجيل (Operations Analysis)

الجدول التالي يوضح كل عملية والبيانات الحرجة التي يجب تسجيلها:

| العملية (Action)        | الأهمية (Criticality)       | البيانات المطلوبة للتسجيل (Data to Log)                               | الغرض (Purpose)                                          |
| :---------------------- | :-------------------------- | :-------------------------------------------------------------------- | :------------------------------------------------------- |
| **Search Flights**      | متوسطة (Analytics)          | `Origin`, `Destination`, `Date`, `User ID`                            | تحليل وجهات العملاء الأكثر طلباً.                        |
| **Validate Fare**       | عالية (Audit)               | `Session ID`, `Fare Source Code`, `Price Check`                       | التأكد من أن السعر المعروض هو السعر الفعلي وقت الاختيار. |
| **Book Flight (PNR)**   | **حرجة جداً (Transaction)** | `User ID`, `PNR`, `Passenger Details`, `Contact Info`, `Total Amount` | إنشاء سجل الحجز الرسمي وربطه بالمستخدم.                  |
| **Order Ticket**        | **حرجة جداً (Financial)**   | `UNIQUE ID`, `Ticket Status`, `Payment Status`                        | إثبات إصدار التذكرة والدفع.                              |
| **Cancel Booking**      | عالية (Audit/Financial)     | `UNIQUE ID`, `Cancellation Reason`, `Refund Amount` (if any)          | تتبع عمليات الإلغاء ومنع الاحتيال.                       |
| **Refund/Void/Reissue** | **حرجة جداً (Financial)**   | `UNIQUE ID`, `Original Amount`, `Refund Amount`, `Penalty`, `Date`    | المطابقة المالية والتدقيق المحاسبي.                      |
| **Add Notes**           | متوسطة (Support)            | `Note Content`, `Admin User ID`                                       | تتبع الملاحظات الإدارية على الحجوزات.                    |

---

## 2. تصميم قاعدة البيانات المقترح (Database Schema Design)

نقترح إضافة الجداول التالية لضمان التغطية الكاملة:

### أ. جدول سجلات العمليات (`flight_api_logs`) - "الصندوق الأسود"

هذا الجدول هو الأهم للتدقيق التقني والأمني. يسجل كل طلب (Request) ورد (Response) مع Travelopro.

```sql
CREATE TABLE flight_api_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL, -- المستخدم الذي قام بالعملية
    action VARCHAR(50) NOT NULL, -- نوع العملية: 'search', 'book', 'void', etc.
    endpoint VARCHAR(255) NOT NULL, -- رابط الـ API المستدعى
    method VARCHAR(10) DEFAULT 'POST', -- POST/GET
    request_payload JSON NULL, -- البيانات المرسلة (يتم تشفير الحساسة منها)
    response_payload JSON NULL, -- البيانات المستقبلة من المزود
    status_code INT NOT NULL, -- كود الحالة: 200, 400, 500
    ip_address VARCHAR(45) NULL, -- عنوان IP للمستخدم
    user_agent TEXT NULL, -- معلومات المتصفح والجهاز
    execution_time FLOAT NULL, -- وقت التنفيذ بالثواني (للأداء)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

### ب. جدول الحجوزات (`bookings`) - السجل التجاري

يخزن الحجوزات الفعلية لغرض العرض والإدارة.

```sql
CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    booking_reference VARCHAR(50) NOT NULL, -- PNR / UniqueID من Travelopro
    supplier_session_id VARCHAR(255) NULL, -- للرجوع للعملية
    status ENUM('pending', 'confirmed', 'cancelled', 'refunded', 'failed') DEFAULT 'pending',
    ticket_status ENUM('booked', 'ticketed', 'voided', 'reissued') DEFAULT 'booked',
    total_amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'SAR',
    contact_email VARCHAR(255) NOT NULL,
    contact_phone VARCHAR(50) NOT NULL,
    pnr_created_at TIMESTAMP NULL, -- وقت إنشاء الحجز الفعلي عند المزود
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### ج. جدول المسافرين (`booking_passengers`) - تفاصيل الركاب

يخزن بيانات كل مسافر مرتبط بالحجز.

```sql
CREATE TABLE booking_passengers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(10) NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    type ENUM('ADT', 'CHD', 'INF') DEFAULT 'ADT',
    ticket_number VARCHAR(100) NULL, -- رقم التذكرة (Update بعد الإصدار)
    passport_no VARCHAR(100) NULL, -- (يجب تشفير هذا الحقل)
    nationality VARCHAR(50) NULL,
    dob DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);
```

### د. جدول سجلات البحث (`flight_search_logs`) - تحليلات (اختياري)

لتخزين ما يبحث عنه العملاء لأغراض التسويق وتحسين الخدمة.

```sql
CREATE TABLE flight_search_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    origin_code VARCHAR(3) NOT NULL,
    destination_code VARCHAR(3) NOT NULL,
    departure_date DATE NOT NULL,
    return_date DATE NULL,
    adults INT DEFAULT 1,
    children INT DEFAULT 0,
    infants INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 3. خطة التنفيذ (Implementation Plan)

لتطبيق هذا النظام، سنقوم بالتالي:

1.  **إنشاء Migrations:** تحويل التصميم أعلاه إلى ملفات Migration في Laravel.
2.  **إنشاء Models:** إنشاء مودلز `Booking`, `BookingPassenger`, `FlightApiLog`, `FlightSearchLog`.
3.  **تحديث `TraveloproService`:** إضافة كود التسجيل (Logging Code) في دالة `sendRequest` لتخزين كل طلب ورد في جدول `flight_api_logs` تلقائياً.
4.  **تحديث `FlightController`:**
    - عند نجاح `book`: إنشاء سجل في جدول `bookings` و `booking_passengers`.
    - عند نجاح `orderTicket`: تحديث حالة الحجز في `bookings` وإضافة أرقام التذاكر لـ `booking_passengers`.
    - عند نجاح `cancel`/`void`/`refund`: تحديث حالة الحجز في `bookings`.

هل تريد البدء في إنشاء ملفات الـ Migration وتنفيذ هذا المخطط؟
