<?php

namespace App\Helpers;

class CountryHelper
{
    /**
     * Get a comprehensive list of all world countries with ISO, dial code, flag, and names.
     * Arab and GCC countries are prioritized at the top, followed by the rest of the world.
     *
     * @return array
     */
    public static function getAllCountries(): array
    {
        return [
            // GCC & Arab Countries (Top Priority)
            ['code' => 'SA', 'dial_code' => '+966', 'flag' => '🇸🇦', 'name_ar' => 'المملكة العربية السعودية', 'name_en' => 'Saudi Arabia'],
            ['code' => 'AE', 'dial_code' => '+971', 'flag' => '🇦🇪', 'name_ar' => 'الإمارات العربية المتحدة', 'name_en' => 'United Arab Emirates'],
            ['code' => 'KW', 'dial_code' => '+965', 'flag' => '🇰🇼', 'name_ar' => 'الكويت', 'name_en' => 'Kuwait'],
            ['code' => 'QA', 'dial_code' => '+974', 'flag' => '🇶🇦', 'name_ar' => 'قطر', 'name_en' => 'Qatar'],
            ['code' => 'BH', 'dial_code' => '+973', 'flag' => '🇧🇭', 'name_ar' => 'البحرين', 'name_en' => 'Bahrain'],
            ['code' => 'OM', 'dial_code' => '+968', 'flag' => '🇴🇲', 'name_ar' => 'عُمان', 'name_en' => 'Oman'],
            ['code' => 'EG', 'dial_code' => '+20',  'flag' => '🇪🇬', 'name_ar' => 'مصر', 'name_en' => 'Egypt'],
            ['code' => 'JO', 'dial_code' => '+962', 'flag' => '🇯🇴', 'name_ar' => 'الأردن', 'name_en' => 'Jordan'],
            ['code' => 'LB', 'dial_code' => '+961', 'flag' => '🇱🇧', 'name_ar' => 'لبنان', 'name_en' => 'Lebanon'],
            ['code' => 'SY', 'dial_code' => '+963', 'flag' => '🇸🇾', 'name_ar' => 'سوريا', 'name_en' => 'Syria'],
            ['code' => 'IQ', 'dial_code' => '+964', 'flag' => '🇮🇶', 'name_ar' => 'العراق', 'name_en' => 'Iraq'],
            ['code' => 'YE', 'dial_code' => '+967', 'flag' => '🇾🇪', 'name_ar' => 'اليمن', 'name_en' => 'Yemen'],
            ['code' => 'PS', 'dial_code' => '+970', 'flag' => '🇵🇸', 'name_ar' => 'فلسطين', 'name_en' => 'Palestine'],
            ['code' => 'SD', 'dial_code' => '+249', 'flag' => '🇸🇩', 'name_ar' => 'السودان', 'name_en' => 'Sudan'],
            ['code' => 'LY', 'dial_code' => '+218', 'flag' => '🇱🇾', 'name_ar' => 'ليبيا', 'name_en' => 'Libya'],
            ['code' => 'TN', 'dial_code' => '+216', 'flag' => '🇹🇳', 'name_ar' => 'تونس', 'name_en' => 'Tunisia'],
            ['code' => 'DZ', 'dial_code' => '+213', 'flag' => '🇩🇿', 'name_ar' => 'الجزائر', 'name_en' => 'Algeria'],
            ['code' => 'MA', 'dial_code' => '+212', 'flag' => '🇲🇦', 'name_ar' => 'المغرب', 'name_en' => 'Morocco'],
            ['code' => 'MR', 'dial_code' => '+222', 'flag' => '🇲🇷', 'name_ar' => 'موريتانيا', 'name_en' => 'Mauritania'],
            ['code' => 'SO', 'dial_code' => '+252', 'flag' => '🇸🇴', 'name_ar' => 'الصومال', 'name_en' => 'Somalia'],
            ['code' => 'DJ', 'dial_code' => '+253', 'flag' => '🇩🇯', 'name_ar' => 'جيبوتي', 'name_en' => 'Djibouti'],
            ['code' => 'KM', 'dial_code' => '+269', 'flag' => '🇰🇲', 'name_ar' => 'جزر القمر', 'name_en' => 'Comoros'],

            // Rest of the World (Alphabetical)
            ['code' => 'AF', 'dial_code' => '+93',  'flag' => '🇦🇫', 'name_ar' => 'أفغانستان', 'name_en' => 'Afghanistan'],
            ['code' => 'AL', 'dial_code' => '+355', 'flag' => '🇦🇱', 'name_ar' => 'ألبانيا', 'name_en' => 'Albania'],
            ['code' => 'AD', 'dial_code' => '+376', 'flag' => '🇦🇩', 'name_ar' => 'أندورا', 'name_en' => 'Andorra'],
            ['code' => 'AO', 'dial_code' => '+244', 'flag' => '🇦🇴', 'name_ar' => 'أنغولا', 'name_en' => 'Angola'],
            ['code' => 'AR', 'dial_code' => '+54',  'flag' => '🇦🇷', 'name_ar' => 'الأرجنتين', 'name_en' => 'Argentina'],
            ['code' => 'AM', 'dial_code' => '+374', 'flag' => '🇦🇲', 'name_ar' => 'أرمينيا', 'name_en' => 'Armenia'],
            ['code' => 'AU', 'dial_code' => '+61',  'flag' => '🇦🇺', 'name_ar' => 'أستراليا', 'name_en' => 'Australia'],
            ['code' => 'AT', 'dial_code' => '+43',  'flag' => '🇦🇹', 'name_ar' => 'النمسا', 'name_en' => 'Austria'],
            ['code' => 'AZ', 'dial_code' => '+994', 'flag' => '🇦🇿', 'name_ar' => 'أذربيجان', 'name_en' => 'Azerbaijan'],
            ['code' => 'BS', 'dial_code' => '+1242','flag' => '🇧🇸', 'name_ar' => 'باهاماس', 'name_en' => 'Bahamas'],
            ['code' => 'BD', 'dial_code' => '+880', 'flag' => '🇧🇩', 'name_ar' => 'بنغلاديش', 'name_en' => 'Bangladesh'],
            ['code' => 'BY', 'dial_code' => '+375', 'flag' => '🇧🇾', 'name_ar' => 'بيلاروسيا', 'name_en' => 'Belarus'],
            ['code' => 'BE', 'dial_code' => '+32',  'flag' => '🇧🇪', 'name_ar' => 'بلجيكا', 'name_en' => 'Belgium'],
            ['code' => 'BZ', 'dial_code' => '+501', 'flag' => '🇧🇿', 'name_ar' => 'بليز', 'name_en' => 'Belize'],
            ['code' => 'BJ', 'dial_code' => '+229', 'flag' => '🇧🇯', 'name_ar' => 'بنين', 'name_en' => 'Benin'],
            ['code' => 'BO', 'dial_code' => '+591', 'flag' => '🇧🇴', 'name_ar' => 'بوليفيا', 'name_en' => 'Bolivia'],
            ['code' => 'BA', 'dial_code' => '+387', 'flag' => '🇧🇦', 'name_ar' => 'البوسنة والهرسك', 'name_en' => 'Bosnia and Herzegovina'],
            ['code' => 'BR', 'dial_code' => '+55',  'flag' => '🇧🇷', 'name_ar' => 'البرازيل', 'name_en' => 'Brazil'],
            ['code' => 'BN', 'dial_code' => '+673', 'flag' => '🇧🇳', 'name_ar' => 'بروناي', 'name_en' => 'Brunei'],
            ['code' => 'BG', 'dial_code' => '+359', 'flag' => '🇧🇬', 'name_ar' => 'بلغاريا', 'name_en' => 'Bulgaria'],
            ['code' => 'CA', 'dial_code' => '+1',   'flag' => '🇨🇦', 'name_ar' => 'كندا', 'name_en' => 'Canada'],
            ['code' => 'CL', 'dial_code' => '+56',  'flag' => '🇨🇱', 'name_ar' => 'تشيلي', 'name_en' => 'Chile'],
            ['code' => 'CN', 'dial_code' => '+86',  'flag' => '🇨🇳', 'name_ar' => 'الصين', 'name_en' => 'China'],
            ['code' => 'CO', 'dial_code' => '+57',  'flag' => '🇨🇴', 'name_ar' => 'كولومبيا', 'name_en' => 'Colombia'],
            ['code' => 'CR', 'dial_code' => '+506', 'flag' => '🇨🇷', 'name_ar' => 'كوستاريكا', 'name_en' => 'Costa Rica'],
            ['code' => 'HR', 'dial_code' => '+385', 'flag' => '🇭🇷', 'name_ar' => 'كرواتيا', 'name_en' => 'Croatia'],
            ['code' => 'CY', 'dial_code' => '+357', 'flag' => '🇨🇾', 'name_ar' => 'قبرص', 'name_en' => 'Cyprus'],
            ['code' => 'CZ', 'dial_code' => '+420', 'flag' => '🇨🇿', 'name_ar' => 'التشيك', 'name_en' => 'Czech Republic'],
            ['code' => 'DK', 'dial_code' => '+45',  'flag' => '🇩🇰', 'name_ar' => 'الدنمارك', 'name_en' => 'Denmark'],
            ['code' => 'EC', 'dial_code' => '+593', 'flag' => '🇪🇨', 'name_ar' => 'الإكوادور', 'name_en' => 'Ecuador'],
            ['code' => 'EE', 'dial_code' => '+372', 'flag' => '🇪🇪', 'name_ar' => 'إستونيا', 'name_en' => 'Estonia'],
            ['code' => 'ET', 'dial_code' => '+251', 'flag' => '🇪🇹', 'name_ar' => 'إثيوبيا', 'name_en' => 'Ethiopia'],
            ['code' => 'FI', 'dial_code' => '+358', 'flag' => '🇫🇮', 'name_ar' => 'فنلندا', 'name_en' => 'Finland'],
            ['code' => 'FR', 'dial_code' => '+33',  'flag' => '🇫🇷', 'name_ar' => 'فرنسا', 'name_en' => 'France'],
            ['code' => 'GE', 'dial_code' => '+995', 'flag' => '🇬🇪', 'name_ar' => 'جورجيا', 'name_en' => 'Georgia'],
            ['code' => 'DE', 'dial_code' => '+49',  'flag' => '🇩🇪', 'name_ar' => 'ألمانيا', 'name_en' => 'Germany'],
            ['code' => 'GH', 'dial_code' => '+233', 'flag' => '🇬🇭', 'name_ar' => 'غانا', 'name_en' => 'Ghana'],
            ['code' => 'GR', 'dial_code' => '+30',  'flag' => '🇬🇷', 'name_ar' => 'اليونان', 'name_en' => 'Greece'],
            ['code' => 'HK', 'dial_code' => '+852', 'flag' => '🇭🇰', 'name_ar' => 'هونغ كونغ', 'name_en' => 'Hong Kong'],
            ['code' => 'HU', 'dial_code' => '+36',  'flag' => '🇭🇺', 'name_ar' => 'المجر', 'name_en' => 'Hungary'],
            ['code' => 'IS', 'dial_code' => '+354', 'flag' => '🇮🇸', 'name_ar' => 'آيسلندا', 'name_en' => 'Iceland'],
            ['code' => 'IN', 'dial_code' => '+91',  'flag' => '🇮🇳', 'name_ar' => 'الهند', 'name_en' => 'India'],
            ['code' => 'ID', 'dial_code' => '+62',  'flag' => '🇮🇩', 'name_ar' => 'إندونيسيا', 'name_en' => 'Indonesia'],
            ['code' => 'IR', 'dial_code' => '+98',  'flag' => '🇮🇷', 'name_ar' => 'إيران', 'name_en' => 'Iran'],
            ['code' => 'IE', 'dial_code' => '+353', 'flag' => '🇮🇪', 'name_ar' => 'أيرلندا', 'name_en' => 'Ireland'],
            ['code' => 'IT', 'dial_code' => '+39',  'flag' => '🇮🇹', 'name_ar' => 'إيطاليا', 'name_en' => 'Italy'],
            ['code' => 'JP', 'dial_code' => '+81',  'flag' => '🇯🇵', 'name_ar' => 'اليابان', 'name_en' => 'Japan'],
            ['code' => 'KZ', 'dial_code' => '+7',   'flag' => '🇰🇿', 'name_ar' => 'كازاخستان', 'name_en' => 'Kazakhstan'],
            ['code' => 'KE', 'dial_code' => '+254', 'flag' => '🇰🇪', 'name_ar' => 'كينيا', 'name_en' => 'Kenya'],
            ['code' => 'KR', 'dial_code' => '+82',  'flag' => '🇰🇷', 'name_ar' => 'كوريا الجنوبية', 'name_en' => 'South Korea'],
            ['code' => 'KG', 'dial_code' => '+996', 'flag' => '🇰🇬', 'name_ar' => 'قيرغيزستان', 'name_en' => 'Kyrgyzstan'],
            ['code' => 'MY', 'dial_code' => '+60',  'flag' => '🇲🇾', 'name_ar' => 'ماليزيا', 'name_en' => 'Malaysia'],
            ['code' => 'MV', 'dial_code' => '+960', 'flag' => '🇲🇻', 'name_ar' => 'المالديف', 'name_en' => 'Maldives'],
            ['code' => 'MX', 'dial_code' => '+52',  'flag' => '🇲🇽', 'name_ar' => 'المكسيك', 'name_en' => 'Mexico'],
            ['code' => 'MC', 'dial_code' => '+377', 'flag' => '🇲🇨', 'name_ar' => 'موناكو', 'name_en' => 'Monaco'],
            ['code' => 'ME', 'dial_code' => '+382', 'flag' => '🇲🇪', 'name_ar' => 'الجبل الأسود', 'name_en' => 'Montenegro'],
            ['code' => 'NP', 'dial_code' => '+977', 'flag' => '🇳🇵', 'name_ar' => 'نيبال', 'name_en' => 'Nepal'],
            ['code' => 'NL', 'dial_code' => '+31',  'flag' => '🇳🇱', 'name_ar' => 'هولندا', 'name_en' => 'Netherlands'],
            ['code' => 'NZ', 'dial_code' => '+64',  'flag' => '🇳🇿', 'name_ar' => 'نيوزيلندا', 'name_en' => 'New Zealand'],
            ['code' => 'NG', 'dial_code' => '+234', 'flag' => '🇳🇬', 'name_ar' => 'نيجيريا', 'name_en' => 'Nigeria'],
            ['code' => 'MK', 'dial_code' => '+389', 'flag' => '🇲🇰', 'name_ar' => 'مقدونيا الشمالية', 'name_en' => 'North Macedonia'],
            ['code' => 'NO', 'dial_code' => '+47',  'flag' => '🇳🇴', 'name_ar' => 'النرويج', 'name_en' => 'Norway'],
            ['code' => 'PK', 'dial_code' => '+92',  'flag' => '🇵🇰', 'name_ar' => 'باكستان', 'name_en' => 'Pakistan'],
            ['code' => 'PA', 'dial_code' => '+507', 'flag' => '🇵🇦', 'name_ar' => 'بنما', 'name_en' => 'Panama'],
            ['code' => 'PH', 'dial_code' => '+63',  'flag' => '🇵🇭', 'name_ar' => 'الفلبين', 'name_en' => 'Philippines'],
            ['code' => 'PL', 'dial_code' => '+48',  'flag' => '🇵🇱', 'name_ar' => 'بولندا', 'name_en' => 'Poland'],
            ['code' => 'PT', 'dial_code' => '+351', 'flag' => '🇵🇹', 'name_ar' => 'البرتغال', 'name_en' => 'Portugal'],
            ['code' => 'RO', 'dial_code' => '+40',  'flag' => '🇷🇴', 'name_ar' => 'رومانيا', 'name_en' => 'Romania'],
            ['code' => 'RU', 'dial_code' => '+7',   'flag' => '🇷🇺', 'name_ar' => 'روسيا', 'name_en' => 'Russia'],
            ['code' => 'RS', 'dial_code' => '+381', 'flag' => '🇷🇸', 'name_ar' => 'صربيا', 'name_en' => 'Serbia'],
            ['code' => 'SG', 'dial_code' => '+65',  'flag' => '🇸🇬', 'name_ar' => 'سنغافورة', 'name_en' => 'Singapore'],
            ['code' => 'SK', 'dial_code' => '+421', 'flag' => '🇸🇰', 'name_ar' => 'سلوفاكيا', 'name_en' => 'Slovakia'],
            ['code' => 'SI', 'dial_code' => '+386', 'flag' => '🇸🇮', 'name_ar' => 'سلوفينيا', 'name_en' => 'Slovenia'],
            ['code' => 'ZA', 'dial_code' => '+27',  'flag' => '🇿🇦', 'name_ar' => 'جنوب أفريقيا', 'name_en' => 'South Africa'],
            ['code' => 'ES', 'dial_code' => '+34',  'flag' => '🇪🇸', 'name_ar' => 'إسبانيا', 'name_en' => 'Spain'],
            ['code' => 'LK', 'dial_code' => '+94',  'flag' => '🇱🇰', 'name_ar' => 'سريلانكا', 'name_en' => 'Sri Lanka'],
            ['code' => 'SE', 'dial_code' => '+46',  'flag' => '🇸🇪', 'name_ar' => 'السويد', 'name_en' => 'Sweden'],
            ['code' => 'CH', 'dial_code' => '+41',  'flag' => '🇨🇭', 'name_ar' => 'سويسرا', 'name_en' => 'Switzerland'],
            ['code' => 'TH', 'dial_code' => '+66',  'flag' => '🇹🇭', 'name_ar' => 'تايلاند', 'name_en' => 'Thailand'],
            ['code' => 'TR', 'dial_code' => '+90',  'flag' => '🇹🇷', 'name_ar' => 'تركيا', 'name_en' => 'Turkey'],
            ['code' => 'UA', 'dial_code' => '+380', 'flag' => '🇺🇦', 'name_ar' => 'أوكرانيا', 'name_en' => 'Ukraine'],
            ['code' => 'GB', 'dial_code' => '+44',  'flag' => '🇬🇧', 'name_ar' => 'المملكة المتحدة', 'name_en' => 'United Kingdom'],
            ['code' => 'US', 'dial_code' => '+1',   'flag' => '🇺🇸', 'name_ar' => 'الولايات المتحدة', 'name_en' => 'United States'],
            ['code' => 'UZ', 'dial_code' => '+998', 'flag' => '🇺🇿', 'name_ar' => 'أوزبكستان', 'name_en' => 'Uzbekistan'],
            ['code' => 'VN', 'dial_code' => '+84',  'flag' => '🇻🇳', 'name_ar' => 'فيتنام', 'name_en' => 'Vietnam'],
        ];
    }

    /**
     * Accurately splits raw phone and country code into clean components.
     * Extracts national phone number if country code was embedded in the phone string.
     *
     * @param string|null $rawPhone
     * @param string|null $rawCountryCode
     * @return array ['country_code' => string, 'phone' => string]
     */
    public static function splitPhone(?string $rawPhone, ?string $rawCountryCode = null): array
    {
        $phone = trim($rawPhone ?? '');
        $countryCode = trim($rawCountryCode ?? '');

        // Standardize country code format (e.g. 966 -> +966)
        if ($countryCode !== '') {
            $countryCode = '+' . ltrim($countryCode, '+');
        }

        // If phone is empty, return whatever country code we have (default to +966)
        if ($phone === '') {
            return [
                'country_code' => $countryCode ?: '+966',
                'phone'        => ''
            ];
        }

        // Clean spaces and special characters from phone while keeping leading + if present
        $hasPlus = str_starts_with($phone, '+');
        $digitsOnly = preg_replace('/[^\d]/', '', $phone);

        // If countryCode was provided (e.g. +966)
        if ($countryCode !== '') {
            $dialDigits = ltrim($countryCode, '+');

            // Check if phone starts with the dial code
            if (str_starts_with($digitsOnly, $dialDigits)) {
                $stripped = substr($digitsOnly, strlen($dialDigits));
                // If it starts with 0 after stripping, remove leading 0
                $cleanPhone = ltrim($stripped, '0');
                return [
                    'country_code' => $countryCode,
                    'phone'        => $cleanPhone ?: $stripped
                ];
            }

            // Remove any single leading 0 (e.g. 0501234567 -> 501234567)
            $cleanPhone = (str_starts_with($digitsOnly, '0') && strlen($digitsOnly) === 10) 
                ? substr($digitsOnly, 1) 
                : $digitsOnly;

            return [
                'country_code' => $countryCode,
                'phone'        => $cleanPhone
            ];
        }

        // If no country_code was passed, try to detect country code from the phone string!
        // Get all dial codes sorted by length descending so +966 matches before +9
        $countries = self::getAllCountries();
        usort($countries, fn($a, $b) => strlen($b['dial_code']) <=> strlen($a['dial_code']));

        foreach ($countries as $c) {
            $dialDigits = ltrim($c['dial_code'], '+');
            if (str_starts_with($digitsOnly, $dialDigits)) {
                $stripped = substr($digitsOnly, strlen($dialDigits));
                $cleanPhone = ltrim($stripped, '0');
                return [
                    'country_code' => $c['dial_code'],
                    'phone'        => $cleanPhone ?: $stripped
                ];
            }
        }

        // Saudi local number fallback: 05xxxxxxxx -> +966 & 5xxxxxxxx
        if (str_starts_with($digitsOnly, '05') && strlen($digitsOnly) === 10) {
            return [
                'country_code' => '+966',
                'phone'        => substr($digitsOnly, 1)
            ];
        }

        return [
            'country_code' => '+966',
            'phone'        => $digitsOnly
        ];
    }
}
