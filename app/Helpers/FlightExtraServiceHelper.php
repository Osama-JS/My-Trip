<?php

namespace App\Helpers;

class FlightExtraServiceHelper
{
    /**
     * Common meal SSR code descriptions (EN & AR).
     */
    protected static array $mealCodes = [
        'VGSW' => ['en' => 'Vegetarian Sandwich', 'ar' => 'شطيرة نباتية'],
        'VGML' => ['en' => 'Vegetarian Vegan Meal', 'ar' => 'وجبة نباتية خالية من منتجات الألبان'],
        'AVML' => ['en' => 'Asian Vegetarian Meal', 'ar' => 'وجبة نباتية آسيوية'],
        'HNML' => ['en' => 'Hindu Meal', 'ar' => 'وجبة هندوسية'],
        'MOML' => ['en' => 'Muslim / Halal Meal', 'ar' => 'وجبة حلال إسلامية'],
        'KSML' => ['en' => 'Kosher Meal', 'ar' => 'وجبة كوشر'],
        'DBML' => ['en' => 'Diabetic Meal', 'ar' => 'وجبة لمرضى السكري'],
        'GFML' => ['en' => 'Gluten-Free Meal', 'ar' => 'وجبة خالية من الجلوتين'],
        'LCML' => ['en' => 'Low Calorie Meal', 'ar' => 'وجبة منخفضة السعرات'],
        'LFML' => ['en' => 'Low Fat Meal', 'ar' => 'وجبة قليلة الدسم'],
        'LSML' => ['en' => 'Low Salt Meal', 'ar' => 'وجبة قليلة الملح'],
        'NLML' => ['en' => 'Non-Lactose Meal', 'ar' => 'وجبة خالية من اللاكتوز'],
        'CHML' => ['en' => 'Child Meal', 'ar' => 'وجبة أطفال'],
        'BBML' => ['en' => 'Baby Meal', 'ar' => 'وجبة رضع'],
        'SFML' => ['en' => 'Seafood Meal', 'ar' => 'وجبة مأكولات بحرية'],
        'FPML' => ['en' => 'Fruit Platter Meal', 'ar' => 'طبق فواكه طازجة'],
        'BLML' => ['en' => 'Bland Meal', 'ar' => 'وجبة خفيفة سهلة الهضم'],
        'SPML' => ['en' => 'Special Meal', 'ar' => 'وجبة خاصة'],
        'RVML' => ['en' => 'Raw Vegetarian Meal', 'ar' => 'وجبة نباتية نيئة'],
        'VLML' => ['en' => 'Vegetarian Lacto-Ovo Meal', 'ar' => 'وجبة نباتية بيض وألبان'],
        'VOML' => ['en' => 'Vegetarian Oriental Meal', 'ar' => 'وجبة نباتية شرقية'],
        'NVML' => ['en' => 'Non-Vegetarian Meal', 'ar' => 'وجبة غير نباتية'],
    ];

    /**
     * Resolve all extra services for a given passenger data array.
     *
     * @param array|null $paxData
     * @param string $currency
     * @return array
     */
    public static function resolvePassengerExtras(?array $paxData, string $currency = 'SAR'): array
    {
        if (empty($paxData) || !is_array($paxData)) {
            return [];
        }

        $locale = app()->getLocale();
        $isAr = ($locale === 'ar');
        $resolved = [];

        // 1. If structured details were stored, parse and enrich them
        if (!empty($paxData['extra_services_details']) && is_array($paxData['extra_services_details'])) {
            foreach ($paxData['extra_services_details'] as $detail) {
                if (empty($detail) || !is_array($detail)) continue;

                $type = strtolower($detail['type'] ?? 'service');
                $code = $detail['code'] ?? '';
                $rawDesc = $detail['description'] ?? $detail['desc'] ?? '';
                $price = (float)($detail['price'] ?? 0);
                $curr = $detail['currency'] ?? $currency;
                $flightType = $detail['flight_type'] ?? 'outbound';

                // Skip "No Extra" placeholders
                if (stripos($code, 'none') !== false || stripos($rawDesc, 'No Extra') !== false || stripos($rawDesc, 'بدون') !== false) {
                    continue;
                }

                $meta = self::getServiceMeta($type, $code, $rawDesc, $isAr);

                $resolved[] = [
                    'type'         => $type,
                    'code'         => $code,
                    'title'        => $meta['title'],
                    'category'     => $meta['category'],
                    'icon'         => $meta['icon'],
                    'emoji'        => $meta['emoji'],
                    'price'        => $price,
                    'currency'     => $curr,
                    'flight_type'  => $flightType,
                ];
            }

            return $resolved;
        }

        // 2. Fallback: Parse codes from extra_services_outbound & extra_services_inbound
        $codeGroups = [
            'outbound' => array_merge(
                (array)($paxData['extra_services_outbound'] ?? []),
                (array)($paxData['seat_outbound'] ?? [])
            ),
            'inbound' => array_merge(
                (array)($paxData['extra_services_inbound'] ?? []),
                (array)($paxData['seat_inbound'] ?? [])
            ),
        ];

        foreach ($codeGroups as $flightType => $codes) {
            foreach ($codes as $rawCode) {
                if (empty($rawCode) || !is_string($rawCode)) continue;
                $code = trim($rawCode);

                // Ignore "No..." entries
                if (in_array(strtolower($code), ['none', 'no', 'noseat', 'nomeal', 'nobaggage', '0'])) {
                    continue;
                }

                $service = self::parseRawServiceCode($code, $flightType, $currency, $isAr);
                if ($service) {
                    $resolved[] = $service;
                }
            }
        }

        return $resolved;
    }

    /**
     * Parse raw service code (e.g. EB05, VGSW, base64 seat).
     */
    protected static function parseRawServiceCode(string $code, string $flightType, string $currency, bool $isAr): ?array
    {
        // 1. Extra Baggage check: EB05, EB10, EB15, EB20, EB25, EB30, BAG10, etc.
        if (preg_match('/^(?:EB|BAG)(\d+)$/i', $code, $m)) {
            $weight = (int)$m[1];
            return [
                'type'        => 'baggage',
                'code'        => $code,
                'title'       => $isAr ? "أمتعة إضافية ({$weight} كجم)" : "Extra Baggage ({$weight} KG)",
                'category'    => $isAr ? 'الأمتعة الإضافية' : 'Extra Baggage',
                'icon'        => 'fa-suitcase-rolling',
                'emoji'       => '🧳',
                'price'       => 0, // Fallback price
                'currency'    => $currency,
                'flight_type' => $flightType,
            ];
        }

        // 2. Meal check: standard IATA SSR meal code
        $upperCode = strtoupper($code);
        if (isset(self::$mealCodes[$upperCode])) {
            $mealInfo = self::$mealCodes[$upperCode];
            return [
                'type'        => 'meal',
                'code'        => $code,
                'title'       => $isAr ? $mealInfo['ar'] : $mealInfo['en'],
                'category'    => $isAr ? 'الوجبات' : 'Meals',
                'icon'        => 'fa-utensils',
                'emoji'       => '🍽️',
                'price'       => 0,
                'currency'    => $currency,
                'flight_type' => $flightType,
            ];
        }

        // 3. Seat check: base64 encoded seat ID or seat label like 12A, 14F
        $seatLabel = null;
        if (preg_match('/^\d{1,2}[A-F]$/i', $code)) {
            $seatLabel = strtoupper($code);
        } else {
            // Check if base64 decoded
            $decoded = @base64_decode($code, true);
            if ($decoded && preg_match('/^[0-9_a-zA-Z-]+$/', $decoded)) {
                // e.g. 1_0_1_0
                $parts = explode('_', $decoded);
                if (count($parts) >= 2) {
                    $row = (int)($parts[0] ?? 1);
                    $colIdx = (int)($parts[2] ?? ($parts[1] ?? 0));
                    $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
                    $colLetter = $letters[$colIdx] ?? 'A';
                    $seatLabel = "{$row}{$colLetter}";
                }
            }
        }

        if ($seatLabel) {
            return [
                'type'        => 'seat',
                'code'        => $code,
                'title'       => $isAr ? "مقعد محجوز ({$seatLabel})" : "Reserved Seat ({$seatLabel})",
                'category'    => $isAr ? 'المقاعد' : 'Seats',
                'icon'        => 'fa-chair',
                'emoji'       => '💺',
                'price'       => 0,
                'currency'    => $currency,
                'flight_type' => $flightType,
            ];
        }

        // 4. Generic Extra Service fallback
        return [
            'type'        => 'service',
            'code'        => $code,
            'title'       => ($isAr ? 'خدمة إضافية' : 'Extra Service') . " ({$code})",
            'category'    => $isAr ? 'خدمات إضافية' : 'Extra Services',
            'icon'        => 'fa-concierge-bell',
            'emoji'       => '✨',
            'price'       => 0,
            'currency'    => $currency,
            'flight_type' => $flightType,
        ];
    }

    /**
     * Get metadata and localized title from service details.
     */
    protected static function getServiceMeta(string $type, string $code, string $rawDesc, bool $isAr): array
    {
        $type = strtolower($type);
        if (stripos($type, 'baggage') !== false || stripos($rawDesc, 'kg') !== false || stripos($rawDesc, 'baggage') !== false || stripos($rawDesc, 'حقيبة') !== false || stripos($rawDesc, 'وزن') !== false) {
            $category = $isAr ? 'الأمتعة الإضافية' : 'Extra Baggage';
            $icon = 'fa-suitcase-rolling';
            $emoji = '🧳';
            $title = $rawDesc ?: ($isAr ? 'أمتعة إضافية' : 'Extra Baggage');
            // If rawDesc is in english e.g. "Extra Baggage 5 Kg"
            if ($isAr && preg_match('/(\d+)\s*(?:kg|kilo)/i', $rawDesc, $m)) {
                $title = "أمتعة إضافية ({$m[1]} كجم)";
            }
            return compact('category', 'icon', 'emoji', 'title');
        }

        if (stripos($type, 'meal') !== false || stripos($rawDesc, 'meal') !== false || stripos($rawDesc, 'وجبة') !== false || stripos($rawDesc, 'sandwich') !== false) {
            $category = $isAr ? 'الوجبات' : 'Meals';
            $icon = 'fa-utensils';
            $emoji = '🍽️';
            $upperCode = strtoupper($code);
            $title = $rawDesc ?: ($isAr ? 'وجبة طعام' : 'Meal');
            if (isset(self::$mealCodes[$upperCode])) {
                $title = $isAr ? self::$mealCodes[$upperCode]['ar'] : self::$mealCodes[$upperCode]['en'];
            }
            return compact('category', 'icon', 'emoji', 'title');
        }

        if (stripos($type, 'seat') !== false || stripos($rawDesc, 'seat') !== false || stripos($rawDesc, 'مقعد') !== false) {
            $category = $isAr ? 'المقاعد' : 'Seats';
            $icon = 'fa-chair';
            $emoji = '💺';
            $title = $rawDesc ?: ($isAr ? 'مقعد محجوز' : 'Reserved Seat');
            if ($isAr && preg_match('/Seat\s*(\d+[A-Z])/i', $rawDesc, $m)) {
                $title = "مقعد محجوز ({$m[1]})";
            }
            return compact('category', 'icon', 'emoji', 'title');
        }

        return [
            'category' => $isAr ? 'خدمات إضافية' : 'Extra Services',
            'icon'     => 'fa-plus-circle',
            'emoji'    => '✨',
            'title'    => $rawDesc ?: ($isAr ? 'خدمة إضافية' : 'Extra Service'),
        ];
    }

    /**
     * Get all extra services for a booking mapped by passenger index, along with total price.
     *
     * @param mixed $booking
     * @return array ['passengers' => [...], 'total_price' => float, 'has_extras' => bool]
     */
    public static function getBookingAllExtras($booking): array
    {
        $extraServicesData = $booking->flightBooking->extra_services ?? [];
        if (is_string($extraServicesData)) {
            $extraServicesData = json_decode($extraServicesData, true) ?? [];
        }

        $currency = $booking->currency ?? 'SAR';
        $passengersExtras = [];
        $totalPrice = 0.0;
        $hasExtras = false;

        $passengers = $booking->passengers ?? [];
        foreach ($passengers as $paxIndex => $pax) {
            $paxData = $extraServicesData[$paxIndex] ?? null;
            $extras = self::resolvePassengerExtras($paxData, $currency);

            if (!empty($extras)) {
                $hasExtras = true;
                foreach ($extras as $ex) {
                    $totalPrice += (float)($ex['price'] ?? 0);
                }
            }

            $passengersExtras[$paxIndex] = $extras;
        }

        // If extra_services_details did not specify price, but the booking total contains an extra services margin
        if ($totalPrice <= 0 && $hasExtras) {
            // Check if there is an implied difference: total_amount - (base_amount + insurance_amount)
            $totalAmount = (float)($booking->total_amount ?? 0);
            $insAmount = (float)($booking->insurance_amount ?? 0);
            $baseAmount = (float)($booking->base_amount ?? 0);

            if ($baseAmount > 0 && ($totalAmount - $baseAmount - $insAmount) > 0) {
                $totalPrice = $totalAmount - $baseAmount - $insAmount;
            }
        }

        return [
            'passengers'  => $passengersExtras,
            'total_price' => $totalPrice,
            'has_extras'  => $hasExtras,
        ];
    }
}
