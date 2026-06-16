<?php

$arFile = __DIR__ . '/lang/ar.json';
$ar = json_decode(file_get_contents($arFile), true);

$trips_dictionary = [
    'Add Trip' => 'إضافة رحلة',
    'Edit Trip' => 'تعديل الرحلة',
    'General Info' => 'معلومات عامة',
    'Program Details' => 'تفاصيل البرنامج',
    'Logistics & Locations' => 'الخدمات اللوجستية والمواقع',
    'Pricing & Capacity' => 'الأسعار والسعة',
    'Visibility & Status' => 'الظهور والحالة',
    'Trip Title (Arabic)' => 'عنوان الرحلة (بالعربية)',
    'Trip Title (English)' => 'عنوان الرحلة (بالإنجليزية)',
    'Short Description (Arabic)' => 'وصف قصير (بالعربية)',
    'Short Description (English)' => 'وصف قصير (بالإنجليزية)',
    'Long Description (Arabic)' => 'وصف طويل (بالعربية)',
    'Long Description (English)' => 'وصف طويل (بالإنجليزية)',
    'Includes (Arabic)' => 'يشمل (بالعربية)',
    'Includes (English)' => 'يشمل (بالإنجليزية)',
    'Excludes (Arabic)' => 'لا يشمل (بالعربية)',
    'Excludes (English)' => 'لا يشمل (بالإنجليزية)',
    'Child Policy (Arabic)' => 'سياسة الأطفال (بالعربية)',
    'Child Policy (English)' => 'سياسة الأطفال (بالإنجليزية)',
    'Company' => 'الشركة',
    'Select a Company' => 'اختر الشركة',
    'Duration' => 'المدة',
    'Country' => 'الدولة',
    'Select a Country' => 'اختر الدولة',
    'Origin Country' => 'دولة المغادرة',
    'Origin City' => 'مدينة المغادرة',
    'Select an Origin Country' => 'اختر دولة المغادرة',
    'Select an Origin City' => 'اختر مدينة المغادرة',
    'Destination Country' => 'دولة الوصول',
    'Select a Destination Country' => 'اختر دولة الوصول',
    'Destination City' => 'مدينة الوصول',
    'Select a Destination City' => 'اختر مدينة الوصول',
    'Origin Location Info (Arabic)' => 'معلومات موقع المغادرة (بالعربية)',
    'Origin Location Info (English)' => 'معلومات موقع المغادرة (بالإنجليزية)',
    'Destination Location Info (Arabic)' => 'معلومات موقع الوصول (بالعربية)',
    'Destination Location Info (English)' => 'معلومات موقع الوصول (بالإنجليزية)',
    'Base Price' => 'السعر الأساسي',
    'Base Price (SAR)' => 'السعر الأساسي (رس)',
    'Capacity' => 'السعة',
    'Available Tickets' => 'التذاكر المتاحة',
    'Extra Pass Price' => 'سعر تذكرة إضافية',
    'Extra Pass Price (SAR)' => 'سعر التذكرة الإضافية (رس)',
    'Expiry Date' => 'تاريخ الانتهاء',
    'Set Expiry Date' => 'تحديد تاريخ الانتهاء',
    'Is Public?' => 'عام؟',
    'Public Trip' => 'رحلة عامة',
    'Is Featured?' => 'مميز؟',
    'Featured Trip' => 'رحلة مميزة',
    'Is Ad?' => 'إعلان؟',
    'Ad Trip' => 'إعلان',
    'Active Status' => 'حالة التفعيل',
    'Active Trip' => 'رحلة نشطة',
    'Next Tab' => 'الخطوة التالية',
    'Previous Tab' => 'الخطوة السابقة',
    'Save and Publish Trip' => 'حفظ ونشر الرحلة',
    'Update Trip' => 'تحديث الرحلة',
    'Trip created successfully.' => 'تم إنشاء الرحلة بنجاح.',
    'Trip updated successfully.' => 'تم تحديث الرحلة بنجاح.',
    'Trip Management' => 'إدارة الرحلات',
    'Manage Trips' => 'إدارة الرحلات',
    'All Trips' => 'جميع الرحلات',
    'Category' => 'القسم',
    'Select Category' => 'اختر القسم',
    'Choose Categories' => 'اختر الأقسام',
    'Total Trips' => 'إجمالي الرحلات',
    'Active Trips' => 'رحلات نشطة',
    'Inactive Trips' => 'رحلات معطلة',
    'Delete Trip' => 'حذف الرحلة',
    'Are you sure you want to delete this trip?' => 'هل أنت متأكد من أنك تريد حذف هذه الرحلة؟',
    'Back to Trips' => 'العودة للرحلات',
    'Image Gallery' => 'معرض الصور',
    'Drop images here or click to upload' => 'أسقط الصور هنا أو انقر للرفع',
    'Main Image' => 'الصورة الرئيسية',
    'Select Main Image' => 'اختر الصورة الرئيسية',
    'Gallery Images' => 'صور المعرض',
    'Current Images' => 'الصور الحالية',
    'Remove' => 'إزالة',
    'Price' => 'السعر',
    'Cancel' => 'إلغاء'
];

$replacedCount = 0;

foreach ($ar as $key => $value) {
    if (isset($trips_dictionary[$key])) {
        $ar[$key] = $trips_dictionary[$key];
        $replacedCount++;
    }
}

file_put_contents($arFile, json_encode($ar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Successfully translated $replacedCount trips-related strings in ar.json\n";
