<?php

namespace App\Helpers;

use App\Models\Setting;

class PaymentHelper
{
    /**
     * Get a list of enabled payment methods based on platform settings.
     * 
     * @param int|null $bookingId
     * @param string $bookingType (flight, hotel, trip)
     * @return array
     */
    public static function getAvailableMethods($bookingId = null, $bookingType = 'trip')
    {
        $allMethods = [
            [
                'key' => 'mada',
                'name' => __('Mada'),
                'id' => 'mada', // For backward compatibility with mobile app
                'type' => 'card',
                'icon' => asset('assets/img/payments/mada.png'),
                'logo' => asset('assets/img/payments/mada.png') // For backward compatibility
            ],
            [
                'key' => 'visa_master',
                'name' => __('Visa / MasterCard'),
                'id' => 'visa_master',
                'type' => 'card',
                'icon' => asset('assets/img/payments/visa.png'),
                'logo' => asset('assets/img/payments/visa.png')
            ],
            [
                'key' => 'apple_pay',
                'name' => __('Apple Pay'),
                'id' => 'apple_pay',
                'type' => 'digital_wallet',
                'icon' => asset('assets/img/payments/apple-pay.png'),
                'logo' => asset('assets/img/payments/apple-pay.png')
            ],
            [
                'key' => 'tabby',
                'name' => __('Tabby (Installments)'),
                'id' => 'tabby',
                'type' => 'redirect',
                'icon' => asset('assets/img/payments/tabby.png'),
                'logo' => asset('assets/img/payments/tabby.png')
            ],
            [
                'key' => 'tamara',
                'name' => __('Tamara'),
                'id' => 'tamara',
                'type' => 'redirect',
                'icon' => asset('assets/img/payments/tamara.png'),
                'logo' => asset('assets/img/payments/tamara.png')
            ],
            [
                'key' => 'tap',
                'name' => __('Tap Payment'),
                'id' => 'tap',
                'type' => 'redirect',
                'icon' => asset('assets/img/payments/tap.png'),
                'logo' => asset('assets/img/payments/tap.png')
            ]
        ];

        // Add Bank Transfer EXCLUSIVELY for Trips
        if ($bookingType === 'trip') {
            $allMethods[] = [
                'key' => 'bank_transfer',
                'name' => __('Bank Transfer'),
                'id' => 'bank_transfer',
                'type' => 'manual',
                'icon' => asset('assets/img/payments/bank-transfer.png'),
                'logo' => asset('assets/img/payments/bank-transfer.png')
            ];
        }

        $enabledMethods = [];

        foreach ($allMethods as $method) {
            // Check if enabled in settings (Default is '1' / enabled if not found)
            $isEnabled = Setting::get("payment_{$method['key']}_enabled", '1') == '1';
            
            if ($isEnabled) {
                // Attach URL if booking ID is provided (needed by the mobile app API responses)
                if ($bookingId) {
                    $method['url'] = route('payments.web.checkout', [
                        'booking_id' => $bookingId, 
                        'method' => $method['key'], 
                        'type' => $bookingType
                    ]);
                }
                
                $enabledMethods[] = $method;
            }
        }

        return $enabledMethods;
    }
}
