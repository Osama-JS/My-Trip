<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'site_name_en' => 'Fly Vio',
            'site_name_ar' => 'فلاي فيو',
            'site_description_en' => 'Your ultimate travel companion.',
            'site_description_ar' => 'رفيقك الأمثل في السفر.',
            'site_logo' => 'images/logo.png',
            'site_favicon' => 'images/favicon.png',
            'maintenance_mode' => '0',
            'contact_email' => 'support@Flyvio.com',
            'contact_phone' => '+966 500 000 000',
            'contact_address_en' => 'Saudi Arabia',
            'contact_address_ar' => 'المملكة العربية السعودية',
            'facebook_url' => 'https://facebook.com/mytrip',
            'twitter_url' => 'https://twitter.com/mytrip',
            'instagram_url' => 'https://instagram.com/mytrip',
            'primary_color' => '#3b4bd3',
            'app_min_version' => '1.0.0',
            'android_url' => 'https://play.google.com/store/apps/details?id=com.mytrip',
            'ios_url' => 'https://apps.apple.com/app/mytrip/id000000000',
            'mission_title_en' => 'Creating Unforgettable Travel Experiences',
            'mission_title_ar' => 'خلق تجارب سفر لا تُنسى',
            'about_story_title_en' => 'The Story of Fly Vio',
            'about_story_title_ar' => 'قصة فلاي فيو',
            'about_story_desc_1_en' => 'Fly Vio started with a simple idea: to make premium travel accessible to everyone. We noticed a gap in the market for a truly personalized, high-quality booking experience that caters to modern travelers.',
            'about_story_desc_1_ar' => 'بدأت فلاي فيو بفكرة بسيطة: جعل السفر الفاخر في متناول الجميع. لقد لاحظنا وجود فجوة في السوق لتجربة حجز مخصصة وعالية الجودة حقاً تلبي احتياجات المسافرين العصريين.',
            'about_story_desc_2_en' => 'Today, we partner with top-tier airlines and luxury hotels to bring you an unmatched selection of destinations. Our platform is designed with love, attention to detail, and a commitment to making your journey unforgettable from the moment you click "Search".',
            'about_story_desc_2_ar' => 'اليوم، نحن نشارك أفضل شركات الطيران والفنادق الفاخرة لنقدم لك مجموعة لا ميل لها من الوجهات. تم تصميم منصتنا بحب واهتمام بالتفاصيل والتزام بجعل رحلتك لا تُنسى منذ اللحظة التي تضغط فيها على "بحث".',
            'about_story_image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
            'mission_desc_en' => 'We believe that travel enriches lives and creates lasting memories. Our platform connects travelers with the best tour packages, flights, and hotels, making it easy to plan and book your dream vacation. Founded with a passion for exploration, we work tirelessly to offer curated travel experiences at competitive prices. Our team of travel experts handpicks every package to ensure quality, safety, and unforgettable moments.',
            'mission_desc_ar' => 'نؤمن بأن السفر يثري الحياة ويخلق ذكريات لا تُنسى. تربط منصتنا المسافرين بأفضل باقات الرحلات الجوية والفنادق، مما يُسهّل عليهم التخطيط لعطلة أحلامهم وحجزها. تأسست وجهتك في عام 2015 بمهمة بسيطة: جعل تجارب السفر الفاخرة متاحة للجميع. ما بدأ كفريق صغير من المسافرين المتحمسين نما ليصبح أحد أكثر المنصات السياحية موثوقية في المنطقة. اليوم، نحن نتعاون مع أكثر من 200 شركة سياحية وساعدنا أكثر من 50,000 مسافر على استكشاف العالم. يظل التزامنا بالجودة والسلامة ورضا العملاء في جوهر كل ما نقوم به.',
            'value_1_title_en' => 'Passion',
            'value_1_title_ar' => 'العاطفة',
            'value_2_title_en' => 'Trust',
            'value_2_title_ar' => 'الثقة',
            'value_3_title_en' => 'Quality ',
            'value_3_title_ar' => 'الجودة',
            'value_4_title_en' => 'Customer First',
            'value_4_title_ar' => 'العميل أولاً',
            'value_1_desc_en' => 'We are passionate about travel and dedicated to making your journey perfect.',
            'value_1_desc_ar' => 'نحن متحمسون للسفر ومكرسون لجعل رحلتك مثالية.',
            'value_2_desc_en' => 'Transparency and honesty are the foundation of our relationships with clients.',
            'value_2_desc_ar' => 'الشفافية والصدق هما أساس علاقاتنا مع العملاء.',
            'value_3_desc_en' => 'We carefully select every package and partner to ensure the highest standards.',
            'value_3_desc_ar' => 'نحن نختار كل باقة وشريك بعناية لضمان أعلى المعايير.',
            'value_4_desc_en' => 'Your satisfaction is our priority. We go above and beyond for every traveler.',
            'value_4_desc_ar' => 'رضاكم هو أولويتنا. نحن نبذل قصارى جهدنا لكل مسافر.',
            'value_1_icon' => 'fas fa-heart',
            'value_2_icon' => 'fas fa-handshake',
            'value_3_icon' => 'fas fa-award',
            'value_4_icon' => 'fas fa-users',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
