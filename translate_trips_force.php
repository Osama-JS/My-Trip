<?php

$arFile = __DIR__ . '/lang/ar.json';
$ar = json_decode(file_get_contents($arFile), true);

$trips_dictionary = [
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
    'Includes (Arabic)' => 'الخدمات المشمولة (بالعربية)',
    'Includes (English)' => 'الخدمات المشمولة (بالإنجليزية)',
    'Excludes (Arabic)' => 'الخدمات المستثناة (بالعربية)',
    'Excludes (English)' => 'الخدمات المستثناة (بالإنجليزية)',
    'Child Policy (Arabic)' => 'سياسة الأطفال (بالعربية)',
    'Child Policy (English)' => 'سياسة الأطفال (بالإنجليزية)',

    'Company' => 'الشركة',
    'Select a Company' => 'اختر الشركة',
    'Duration' => 'مدة الرحلة',
    
    'Origin Country' => 'دولة المغادرة',
    'Select an Origin Country' => 'اختر دولة المغادرة',
    'Origin City' => 'مدينة المغادرة',
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
    'Base Price (SAR)' => 'السعر الأساسي (بالريال)',
    'Capacity' => 'سعة الرحلة (الأشخاص)',
    'Available Tickets' => 'التذاكر المتاحة',
    'Extra Pass Price' => 'سعر تذكرة إضافية',
    'Extra Pass Price (SAR)' => 'سعر التذكرة الإضافية (بالريال)',
    
    'Expiry Date' => 'تاريخ انتهاء الرحلة',
    'Set Expiry Date' => 'حدد تاريخ الانتهاء',
    
    'Is Public?' => 'هل الرحلة عامة؟',
    'Public Trip' => 'رحلة عامة',
    'Is Featured?' => 'هل هي مميزة؟',
    'Featured Trip' => 'رحلة مميزة',
    'Is Ad?' => 'هل هي إعلان؟',
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
    'Category' => 'تصنيف الرحلة',
    'Select Category' => 'اختر التصنيف',
    'Choose Categories' => 'اختر التصنيفات',
    'Total Trips' => 'إجمالي الرحلات',
    'Active Trips' => 'الرحلات النشطة',
    'Inactive Trips' => 'الرحلات المعطلة',
    'Delete Trip' => 'حذف الرحلة',
    'Are you sure you want to delete this trip?' => 'هل أنت متأكد من حذف هذه الرحلة؟',
    'Back to Trips' => 'العودة لقائمة الرحلات',
    'Image Gallery' => 'معرض صور الرحلة',
    'Drop images here or click to upload' => 'أسقط الصور هنا للرفع',
    'Main Image' => 'الصورة الرئيسية',
    'Select Main Image' => 'اختر صورة رئيسية',
    'Gallery Images' => 'صور إضافية',
    'Current Images' => 'الصور الحالية',
    'Remove' => 'إزالة',
    'Price' => 'السعر',
    'Cancel' => 'إلغاء',
    
    // Additional trip-related ones that might be in the form
    'Create Trip' => 'إنشاء رحلة',
    'Add New Trip' => 'إضافة رحلة جديدة',
    'Basic Details' => 'التفاصيل الأساسية',
    'Location Details' => 'تفاصيل الموقع',
    'Pricing & Availability' => 'التسعير والتوافر',
    'Status & Settings' => 'الحالة والإعدادات'
];

$replacedCount = 0;

// Read all blade files in trips directory to ensure they are added to ar.json
$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views/admin/trips');
$iterator = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($iterator, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$enFile = __DIR__ . '/lang/en.json';
$en = json_decode(file_get_contents($enFile), true) ?: [];

foreach ($files as $file) {
    $content = file_get_contents($file[0]);
    preg_match_all("/__\(['\"](.*?)['\"]\)/", $content, $m);
    if (!empty($m[1])) {
        foreach($m[1] as $str) {
            if(!isset($en[$str])) {
                $en[$str] = $str;
            }
            if(!isset($ar[$str])) {
                $ar[$str] = $str;
            }
            // Now apply dictionary if we have a match
            if(isset($trips_dictionary[$str])) {
                $ar[$str] = $trips_dictionary[$str];
                $replacedCount++;
            }
        }
    }
}

file_put_contents($enFile, json_encode($en, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
file_put_contents($arFile, json_encode($ar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Successfully extracted all trips strings and translated $replacedCount occurrences.\n";
