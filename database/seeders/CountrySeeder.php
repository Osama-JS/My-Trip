<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            // GCC - Detailed Cities
            ['SA', 'Saudi Arabia', 'المملكة العربية السعودية', '966', [
                ['title_ar' => 'الرياض', 'title_en' => 'Riyadh'],
                ['title_ar' => 'جدة', 'title_en' => 'Jeddah'],
                ['title_ar' => 'الدمام', 'title_en' => 'Dammam'],
                ['title_ar' => 'مكة المكرمة', 'title_en' => 'Mecca'],
                ['title_ar' => 'المدينة المنورة', 'title_en' => 'Medina'],
                ['title_ar' => 'الخبر', 'title_en' => 'Al Khobar'],
                ['title_ar' => 'أبها', 'title_en' => 'Abha'],
                ['title_ar' => 'تبوك', 'title_en' => 'Tabuk'],
                ['title_ar' => 'بريدة', 'title_en' => 'Buraidah'],
                ['title_ar' => 'الطائف', 'title_en' => 'Taif'],
                ['title_ar' => 'حائل', 'title_en' => 'Hail'],
                ['title_ar' => 'نجران', 'title_en' => 'Najran'],
                ['title_ar' => 'جازان', 'title_en' => 'Jazan'],
                ['title_ar' => 'الجبيل', 'title_en' => 'Jubail'],
                ['title_ar' => 'ينبع', 'title_en' => 'Yanbu'],
                ['title_ar' => 'الأحساء', 'title_en' => 'Al Ahsa'],
                ['title_ar' => 'القطيف', 'title_en' => 'Al Qatif'],
                ['title_ar' => 'خميس مشيط', 'title_en' => 'Khamis Mushait'],
                ['title_ar' => 'الخرج', 'title_en' => 'Al Kharj'],
                ['title_ar' => 'حفر الباطن', 'title_en' => 'Hafr Al Batin'],
            ]],
            ['AE', 'United Arab Emirates', 'الإمارات العربية المتحدة', '971', [
                ['title_ar' => 'دبي', 'title_en' => 'Dubai'],
                ['title_ar' => 'أبو ظبي', 'title_en' => 'Abu Dhabi'],
                ['title_ar' => 'الشارقة', 'title_en' => 'Sharjah'],
                ['title_ar' => 'عجمان', 'title_en' => 'Ajman'],
                ['title_ar' => 'رأس الخيمة', 'title_en' => 'Ras Al Khaimah'],
                ['title_ar' => 'الفجيرة', 'title_en' => 'Fujairah'],
                ['title_ar' => 'أم القيوين', 'title_en' => 'Umm Al Quwain'],
                ['title_ar' => 'العين', 'title_en' => 'Al Ain'],
                ['title_ar' => 'خورفكان', 'title_en' => 'Khor Fakkan'],
            ]],
            ['QA', 'Qatar', 'قطر', '974', [
                ['title_ar' => 'الدوحة', 'title_en' => 'Doha'],
                ['title_ar' => 'الريان', 'title_en' => 'Al Rayyan'],
                ['title_ar' => 'الوكرة', 'title_en' => 'Al Wakrah'],
                ['title_ar' => 'الخور', 'title_en' => 'Al Khor'],
                ['title_ar' => 'الشمال', 'title_en' => 'Madinat ash Shamal'],
                ['title_ar' => 'أم صلال', 'title_en' => 'Umm Salal'],
                ['title_ar' => 'مسايعيد', 'title_en' => 'Mesaieed'],
            ]],
            ['KW', 'Kuwait', 'الكويت', '965', [
                ['title_ar' => 'مدينة الكويت', 'title_en' => 'Kuwait City'],
                ['title_ar' => 'الأحمدي', 'title_en' => 'Al Ahmadi'],
                ['title_ar' => 'حولي', 'title_en' => 'Hawalli'],
                ['title_ar' => 'السالمية', 'title_en' => 'Salmiya'],
                ['title_ar' => 'الفروانية', 'title_en' => 'Farwaniya'],
                ['title_ar' => 'الجهراء', 'title_en' => 'Jahra'],
                ['title_ar' => 'مبارك الكبير', 'title_en' => 'Mubarak Al-Kabeer'],
            ]],
            ['OM', 'Oman', 'عُمان', '968', [
                ['title_ar' => 'مسقط', 'title_en' => 'Muscat'],
                ['title_ar' => 'صلالة', 'title_en' => 'Salalah'],
                ['title_ar' => 'صحار', 'title_en' => 'Sohar'],
                ['title_ar' => 'نزوى', 'title_en' => 'Nizwa'],
                ['title_ar' => 'صور', 'title_en' => 'Sur'],
                ['title_ar' => 'البريمي', 'title_en' => 'Al Buraimi'],
                ['title_ar' => 'خصب', 'title_en' => 'Khasab'],
            ]],
            ['BH', 'Bahrain', 'البحرين', '973', [
                ['title_ar' => 'المنامة', 'title_en' => 'Manama'],
                ['title_ar' => 'الرفاع', 'title_en' => 'Riffa'],
                ['title_ar' => 'المحرق', 'title_en' => 'Muharraq'],
                ['title_ar' => 'مدينة حمد', 'title_en' => 'Hamad Town'],
                ['title_ar' => 'مدينة عيسى', 'title_en' => 'Isa Town'],
                ['title_ar' => 'سترة', 'title_en' => 'Sitra'],
            ]],
            
            // NORTH AFRICA
            ['EG', 'Egypt', 'مصر', '20', [
                ['title_ar' => 'القاهرة', 'title_en' => 'Cairo'],
                ['title_ar' => 'الإسكندرية', 'title_en' => 'Alexandria'],
                ['title_ar' => 'الجيزة', 'title_en' => 'Giza'],
                ['title_ar' => 'شرم الشيخ', 'title_en' => 'Sharm El Sheikh'],
                ['title_ar' => 'الغردقة', 'title_en' => 'Hurghada'],
                ['title_ar' => 'الأقصر', 'title_en' => 'Luxor'],
                ['title_ar' => 'أسوان', 'title_en' => 'Aswan'],
                ['title_ar' => 'المنصورة', 'title_en' => 'Mansoura'],
                ['title_ar' => 'طنطا', 'title_en' => 'Tanta'],
                ['title_ar' => 'بور سعيد', 'title_en' => 'Port Said'],
            ]],
            ['MA', 'Morocco', 'المغرب', '212', [
                ['title_ar' => 'الدار البيضاء', 'title_en' => 'Casablanca'],
                ['title_ar' => 'الرباط', 'title_en' => 'Rabat'],
                ['title_ar' => 'مراكش', 'title_en' => 'Marrakesh'],
                ['title_ar' => 'فاس', 'title_en' => 'Fes'],
                ['title_ar' => 'طنجة', 'title_en' => 'Tangier'],
                ['title_ar' => 'أكادير', 'title_en' => 'Agadir'],
                ['title_ar' => 'مكناس', 'title_en' => 'Meknes'],
            ]],
            ['DZ', 'Algeria', 'الجزائر', '213', [
                ['title_ar' => 'الجزائر العاصمة', 'title_en' => 'Algiers'],
                ['title_ar' => 'وهران', 'title_en' => 'Oran'],
                ['title_ar' => 'قسنطينة', 'title_en' => 'Constantine'],
                ['title_ar' => 'عنابة', 'title_en' => 'Annaba'],
                ['title_ar' => 'البليدة', 'title_en' => 'Blida'],
            ]],
            ['TN', 'Tunisia', 'تونس', '216', [
                ['title_ar' => 'تونس العاصمة', 'title_en' => 'Tunis'],
                ['title_ar' => 'صفاقس', 'title_en' => 'Sfax'],
                ['title_ar' => 'سوسة', 'title_en' => 'Sousse'],
                ['title_ar' => 'القيروان', 'title_en' => 'Kairouan'],
                ['title_ar' => 'بنزرت', 'title_en' => 'Bizerte'],
            ]],
            ['LY', 'Libya', 'ليبيا', '218', [
                ['title_ar' => 'طرابلس', 'title_en' => 'Tripoli'],
                ['title_ar' => 'بنغازي', 'title_en' => 'Benghazi'],
                ['title_ar' => 'مصراتة', 'title_en' => 'Misrata'],
                ['title_ar' => 'البيضاء', 'title_en' => 'Bayda'],
                ['title_ar' => 'الزاوية', 'title_en' => 'Zawiya'],
            ]],
            ['SD', 'Sudan', 'السودان', '249', [
                ['title_ar' => 'الخرطوم', 'title_en' => 'Khartoum'],
                ['title_ar' => 'أم درمان', 'title_en' => 'Omdurman'],
                ['title_ar' => 'بور سودان', 'title_en' => 'Port Sudan'],
            ]],

            // LEVANT & OTHERS
            ['JO', 'Jordan', 'الأردن', '962', [
                ['title_ar' => 'عمان', 'title_en' => 'Amman'],
                ['title_ar' => 'الزرقاء', 'title_en' => 'Zarqa'],
                ['title_ar' => 'إربد', 'title_en' => 'Irbid'],
                ['title_ar' => 'العقبة', 'title_en' => 'Aqaba'],
                ['title_ar' => 'مادبا', 'title_en' => 'Madaba'],
                ['title_ar' => 'السلط', 'title_en' => 'Salt'],
            ]],
            ['LB', 'Lebanon', 'لبنان', '961', [
                ['title_ar' => 'بيروت', 'title_en' => 'Beirut'],
                ['title_ar' => 'طرابلس', 'title_en' => 'Tripoli'],
                ['title_ar' => 'صيدا', 'title_en' => 'Sidon'],
                ['title_ar' => 'صور', 'title_en' => 'Tyre'],
                ['title_ar' => 'زحلة', 'title_en' => 'Zahle'],
            ]],
            ['SY', 'Syria', 'سوريا', '963', [
                ['title_ar' => 'دمشق', 'title_en' => 'Damascus'],
                ['title_ar' => 'حلب', 'title_en' => 'Aleppo'],
                ['title_ar' => 'حمص', 'title_en' => 'Homs'],
                ['title_ar' => 'اللاذقية', 'title_en' => 'Latakia'],
                ['title_ar' => 'حماة', 'title_en' => 'Hama'],
            ]],
            ['IQ', 'Iraq', 'العراق', '964', [
                ['title_ar' => 'بغداد', 'title_en' => 'Baghdad'],
                ['title_ar' => 'البصرة', 'title_en' => 'Basra'],
                ['title_ar' => 'الموصل', 'title_en' => 'Mosul'],
                ['title_ar' => 'أربيل', 'title_en' => 'Erbil'],
                ['title_ar' => 'السليمانية', 'title_en' => 'Sulaymaniyah'],
                ['title_ar' => 'النجف', 'title_en' => 'Najaf'],
                ['title_ar' => 'كربلاء', 'title_en' => 'Karbala'],
            ]],
            ['YE', 'Yemen', 'اليمن', '967', [
                ['title_ar' => 'صنعاء', 'title_en' => 'Sana\'a'],
                ['title_ar' => 'عدن', 'title_en' => 'Aden'],
                ['title_ar' => 'تعز', 'title_en' => 'Taiz'],
                ['title_ar' => 'الحديدة', 'title_en' => 'Al Hudaydah'],
                ['title_ar' => 'المكلا', 'title_en' => 'Mukalla'],
            ]],
            ['PS', 'Palestine', 'فلسطين', '970', [
                ['title_ar' => 'القدس', 'title_en' => 'Jerusalem'],
                ['title_ar' => 'غزة', 'title_en' => 'Gaza'],
                ['title_ar' => 'رام الله', 'title_en' => 'Ramallah'],
                ['title_ar' => 'الخليل', 'title_en' => 'Hebron'],
                ['title_ar' => 'نابلس', 'title_en' => 'Nablus'],
                ['title_ar' => 'أريحا', 'title_en' => 'Jericho'],
            ]],

            // International
            ['TR', 'Turkey', 'تركيا', '90'],
            ['MV', 'Maldives', 'المالديف', '960'],
            ['US', 'United States', 'الولايات المتحدة', '1'],
            ['GB', 'United Kingdom', 'المملكة المتحدة', '44'],
            ['FR', 'France', 'فرنسا', '33'],
            ['DE', 'Germany', 'ألمانيا', '49'],
            ['IT', 'Italy', 'إيطاليا', '39'],
            ['ES', 'Spain', 'إسبانيا', '34'],
            ['IN', 'India', 'الهند', '91'],
            ['PK', 'Pakistan', 'باكستان', '92'],
            ['MY', 'Malaysia', 'ماليزيا', '60'],
            ['ID', 'Indonesia', 'إندونيسيا', '62'],
            ['TH', 'Thailand', 'تايلاند', '66'],
            ['JP', 'Japan', 'اليابان', '81'],
        ];

        foreach ($countries as $c) {
            $country = \App\Models\Country::updateOrCreate(
                ['iso' => $c[0]],
                [
                    'name' => strtoupper($c[1]),
                    'nicename' => $c[1],
                    'phonecode' => $c[3],
                    'active' => true
                ]
            );

            if (!empty($c[4]) && is_array($c[4])) {
                foreach ($c[4] as $cityData) {
                    \App\Models\City::updateOrCreate(
                        ['country_id' => $country->id, 'title_en' => $cityData['title_en']],
                        [
                            'title_ar' => $cityData['title_ar'],
                            'active' => true
                        ]
                    );
                }
            }
        }
    }



}
