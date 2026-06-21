<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use App\Models\Booking;
use App\Models\HotelBooking;
use App\Models\SupportTicket;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->get('q'));
        $user = Auth::user();

        if (empty($q) || strlen($q) < 2) {
            return response()->json([
                'trips'    => [],
                'flights'  => [],
                'hotels'   => [],
                'tickets'  => [],
                'payments' => [],
                'links'    => $this->getQuickLinks('')
            ]);
        }

        // 1. Search Trip Bookings
        $trips = TripBooking::where('user_id', $user->id)
            ->where(function($query) use ($q) {
                $query->where('id', 'like', "%{$q}%")
                      ->orWhere('status', 'like', "%{$q}%")
                      ->orWhereHas('trip', function($t) use ($q) {
                          $t->where('title', 'like', "%{$q}%")
                            ->orWhere('duration', 'like', "%{$q}%");
                      });
            })
            ->with('trip')
            ->limit(5)
            ->get()
            ->map(function($booking) {
                return [
                    'title'    => $booking->trip->title ?? __('Trip Booking'),
                    'subtitle' => '#' . $booking->id . ' | ' . ($booking->trip->duration ?? '') . ' | ' . $booking->created_at->format('Y-m-d'),
                    'link'     => route('customer.bookings.show', $booking->id),
                    'status'   => $booking->status,
                    'icon'     => 'fas fa-map-marked-alt'
                ];
            });

        // 2. Search Flight Bookings
        $flights = Booking::where('user_id', $user->id)
            ->where(function($query) use ($q) {
                $query->where('id', 'like', "%{$q}%")
                      ->orWhere('pnr', 'like', "%{$q}%")
                      ->orWhere('status', 'like', "%{$q}%")
                      ->orWhere('airline_name', 'like', "%{$q}%")
                      ->orWhere('origin_city', 'like', "%{$q}%")
                      ->orWhere('destination_city', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(function($booking) {
                return [
                    'title'    => ($booking->airline_name ?? __('Flight')) . ' (' . ($booking->pnr ?? '') . ')',
                    'subtitle' => '#' . $booking->id . ' | ' . ($booking->origin_city ?? '') . ' → ' . ($booking->destination_city ?? ''),
                    'link'     => route('customer.bookings.show', $booking->id) . '?type=flight',
                    'status'   => $booking->status,
                    'icon'     => 'fas fa-plane'
                ];
            });

        // 3. Search Hotel Bookings
        $hotels = HotelBooking::where('user_id', $user->id)
            ->where(function($query) use ($q) {
                $query->where('id', 'like', "%{$q}%")
                      ->orWhere('status', 'like', "%{$q}%")
                      ->orWhere('hotel_name', 'like', "%{$q}%")
                      ->orWhere('city_name', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(function($booking) {
                return [
                    'title'    => $booking->hotel_name ?? __('Hotel Stay'),
                    'subtitle' => '#' . $booking->id . ' | ' . ($booking->city_name ?? '') . ' | ' . ($booking->room_type ?? ''),
                    'link'     => route('customer.bookings.show', $booking->id) . '?type=hotel',
                    'status'   => $booking->status,
                    'icon'     => 'fas fa-hotel'
                ];
            });

        // 4. Search Support Tickets
        $tickets = SupportTicket::where('user_id', $user->id)
            ->where(function($query) use ($q) {
                $query->where('id', 'like', "%{$q}%")
                      ->orWhere('ticket_id', 'like', "%{$q}%")
                      ->orWhere('subject', 'like', "%{$q}%")
                      ->orWhere('status', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(function($ticket) {
                return [
                    'title'    => $ticket->subject,
                    'subtitle' => '#' . ($ticket->ticket_id ?? $ticket->id) . ' | ' . __('Priority') . ': ' . __($ticket->priority),
                    'link'     => route('customer.support.show', $ticket->id),
                    'status'   => $ticket->status,
                    'icon'     => 'fas fa-headset'
                ];
            });

        // 5. Search Payments
        $payments = Payment::where('user_id', $user->id)
            ->where(function($query) use ($q) {
                $query->where('id', 'like', "%{$q}%")
                      ->orWhere('transaction_id', 'like', "%{$q}%")
                      ->orWhere('gateway', 'like', "%{$q}%")
                      ->orWhere('payment_method', 'like', "%{$q}%")
                      ->orWhere('status', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(function($payment) {
                return [
                    'title'    => __('Payment via') . ' ' . ucfirst($payment->gateway ?: $payment->payment_method ?: __('Unknown')),
                    'subtitle' => ($payment->transaction_id ? ('TXN: ' . $payment->transaction_id . ' | ') : '') . number_format($payment->amount, 2) . ' ' . ($payment->currency ?: 'SAR'),
                    'link'     => route('customer.payments.index'),
                    'status'   => $payment->status,
                    'icon'     => 'fas fa-receipt'
                ];
            });

        return response()->json([
            'trips'    => $trips,
            'flights'  => $flights,
            'hotels'   => $hotels,
            'tickets'  => $tickets,
            'payments' => $payments,
            'links'    => $this->getQuickLinks($q)
        ]);
    }

    private function getQuickLinks($q)
    {
        $allLinks = [
            [
                'title'    => __('Dashboard'),
                'keywords' => ['dashboard', 'main', 'home', 'الرئيسية', 'لوحة التحكم'],
                'link'     => route('customer.dashboard'),
                'icon'     => 'fas fa-columns',
                'category' => __('Navigation')
            ],
            [
                'title'    => __('Trip Bookings'),
                'keywords' => ['trip', 'booking', 'trips', 'رحلات', 'حجوزات الرحلات'],
                'link'     => route('customer.bookings.trips'),
                'icon'     => 'fas fa-map-marked-alt',
                'category' => __('Navigation')
            ],
            [
                'title'    => __('Flight Bookings'),
                'keywords' => ['flight', 'flights', 'plane', 'طيران', 'حجوزات طيران'],
                'link'     => route('customer.bookings.flights'),
                'icon'     => 'fas fa-plane',
                'category' => __('Navigation')
            ],
            [
                'title'    => __('Hotel Bookings'),
                'keywords' => ['hotel', 'hotels', 'stay', 'فندق', 'فنادق', 'حجز فندق'],
                'link'     => route('customer.bookings.hotels'),
                'icon'     => 'fas fa-hotel',
                'category' => __('Navigation')
            ],
            [
                'title'    => __('Profile Settings'),
                'keywords' => ['profile', 'setting', 'password', 'edit', 'حسابي', 'الملف الشخصي', 'كلمة المرور', 'تعديل'],
                'link'     => route('customer.profile'),
                'icon'     => 'fas fa-user-cog',
                'category' => __('Navigation')
            ],
            [
                'title'    => __('My Wallet'),
                'keywords' => ['wallet', 'balance', 'card', 'money', 'شحن', 'محفظتي', 'المحفظة', 'الرصيد'],
                'link'     => route('customer.wallet.index'),
                'icon'     => 'fas fa-wallet',
                'category' => __('Navigation')
            ],
            [
                'title'    => __('Support Tickets'),
                'keywords' => ['support', 'ticket', 'help', 'message', 'تذاكر', 'الدعم الفني', 'المساعدة'],
                'link'     => route('customer.support.index'),
                'icon'     => 'fas fa-headset',
                'category' => __('Navigation')
            ],
            [
                'title'    => __('Payments'),
                'keywords' => ['payment', 'payments', 'invoice', 'receipt', 'مدفوعات', 'فاتورة', 'فواتير'],
                'link'     => route('customer.payments.index'),
                'icon'     => 'fas fa-receipt',
                'category' => __('Navigation')
            ],
            [
                'title'    => __('Favorites'),
                'keywords' => ['favorite', 'favorites', 'wishlist', 'like', 'المفضلة', 'مفضلاتي'],
                'link'     => route('customer.favorites.index'),
                'icon'     => 'fas fa-heart',
                'category' => __('Navigation')
            ]
        ];

        if (empty($q)) {
            return array_slice($allLinks, 0, 4); // return some quick links by default
        }

        $filtered = [];
        $qLower = mb_strtolower($q);

        foreach ($allLinks as $link) {
            foreach ($link['keywords'] as $keyword) {
                if (mb_strpos(mb_strtolower($keyword), $qLower) !== false) {
                    $filtered[] = [
                        'title'    => $link['title'],
                        'subtitle' => __('Quick navigation shortcut'),
                        'link'     => $link['link'],
                        'status'   => null,
                        'icon'     => $link['icon']
                    ];
                    break;
                }
            }
        }

        return $filtered;
    }
}
