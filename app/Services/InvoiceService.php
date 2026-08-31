<?php

namespace App\Services;

use App\Models\TripBooking;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    /**
     * Generate voucher for a hotel booking
     *
     * @param \App\Models\HotelBooking $booking
     * @return string|false Path to the generated PDF
     */
    public function generateHotelVoucher(\App\Models\HotelBooking $booking)
    {
        try {
            $booking->load(['user', 'passengers']);

            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'fontDir' => array_merge($fontDirs, [
                    public_path('fonts'),
                ]),
                'fontdata' => $fontData + [
                    'tajawal' => [
                        'R' => 'tajawal.ttf',
                        'B' => 'tajawal.ttf',
                        'useOTL' => 0xFF,
                        'useKashida' => 75,
                    ]
                ],
                'default_font' => 'tajawal',
                'tempDir' => storage_path('app/temp')
            ]);

            // Set direction dynamically
            $dir = app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
            $mpdf->SetDirectionality($dir);
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'tajawal';

            $html = view('invoices.hotel_voucher', compact('booking'))->render();
            $mpdf->WriteHTML($html);

            $fileName = 'voucher_' . $booking->id . '_' . time() . '.pdf';
            $filePath = 'invoices/' . $fileName;

            Storage::disk('public')->put($filePath, $mpdf->Output('', 'S'));

            return $filePath;
        } catch (\Exception $e) {
            Log::error('Hotel Voucher Generation Failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate invoice for a trip or flight booking
     *
     * @param mixed $booking
     * @return string|false Path to the generated PDF
     */
    public function generateInvoice($booking)
    {
        try {
            $view = 'invoices.trip_invoice';
            $fileNamePrefix = 'trip_invoice_';

            // Detect booking type
            if ($booking instanceof \App\Models\Booking) {
                $view = 'invoices.flight_invoice';
                $fileNamePrefix = 'flight_invoice_';
                $tripDetails = null;
                
                try {
                    $traveloproService = app(\App\Services\TraveloproService::class);
                    $tripDetails = $traveloproService->getTripDetails($booking->booking_reference, $booking->id);
                    if (isset($tripDetails['TripDetailsResponse']['TripDetailsResult']['TravelItinerary']['ItineraryInfo']['CustomerInfos'])) {
                        $customerInfos = $tripDetails['TripDetailsResponse']['TripDetailsResult']['TravelItinerary']['ItineraryInfo']['CustomerInfos'];
                        $eTickets = [];
                        foreach ($customerInfos as $info) {
                            $name = trim(strtoupper($info['CustomerInfo']['PassengerFirstName'] ?? '') . ' ' . strtoupper($info['CustomerInfo']['PassengerLastName'] ?? ''));
                            if (!empty($info['CustomerInfo']['eTicketNumber'])) {
                                $eTickets[$name] = $info['CustomerInfo']['eTicketNumber'];
                            }
                        }
                        view()->share('eTickets', $eTickets);
                    }
                } catch (\Exception $e) {
                    $tripDetails = null;
                }

                // If itinerary_data is missing on old bookings, try to recover from API logs
                $booking->load(['flightBooking']);
                $fb = $booking->flightBooking;
                if ($fb && empty($fb->itinerary_data)) {
                    try {
                        // Find the createBooking request log for this booking
                        $bookingLog = \App\Models\FlightApiLog::where('action', 'createBooking')
                            ->where('booking_id', $booking->id)
                            ->first();

                        if ($bookingLog) {
                            $req = $bookingLog->request_payload;
                            if (is_string($req)) $req = json_decode($req, true);
                            $info = $req['flightBookingInfo'][0] ?? $req['flightBookingInfo'] ?? [];
                            $fareSourceCode = $info['fare_source_code'] ?? null;

                            // Find the closest search log before this booking
                            $searchLog = \App\Models\FlightApiLog::where('action', 'search')
                                ->where('created_at', '<=', $bookingLog->created_at)
                                ->latest()
                                ->first();

                            if ($searchLog) {
                                $sr = $searchLog->response_payload;
                                if (is_string($sr)) $sr = json_decode($sr, true);
                                $fareItineraries = $sr['AirSearchResponse']['AirSearchResult']['FareItineraries'] ?? [];

                                $matchedFare = null;
                                // Try exact FareSourceCode match first
                                foreach ($fareItineraries as $item) {
                                    $fi = $item['FareItinerary'] ?? $item;
                                    $fsc = $fi['AirItineraryFareInfo']['FareSourceCode'] ?? null;
                                    if ($fareSourceCode && $fsc === $fareSourceCode) {
                                        $matchedFare = $fi;
                                        break;
                                    }
                                }

                                // Fallback: match by origin/destination
                                if (!$matchedFare) {
                                    $originReq = $fb->origin ?? null;
                                    $destReq   = $fb->destination ?? null;
                                    foreach ($fareItineraries as $item) {
                                        $fi = $item['FareItinerary'] ?? $item;
                                        $odo = $fi['OriginDestinationOptions'] ?? [];
                                        foreach ($odo as $wrapper) {
                                            $odOpts = $wrapper['OriginDestinationOption'] ?? [];
                                            if (!isset($odOpts[0])) $odOpts = [$odOpts];
                                            $seg = $odOpts[0]['FlightSegment'] ?? null;
                                            if ($seg) {
                                                $sDepCode = $seg['DepartureAirportLocationCode'] ?? '';
                                                $sArrCode = $seg['ArrivalAirportLocationCode'] ?? '';
                                                if ($originReq && $destReq &&
                                                    strtoupper($sDepCode) === strtoupper($originReq) &&
                                                    strtoupper($sArrCode) === strtoupper($destReq)) {
                                                    $matchedFare = $fi;
                                                    break 2;
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($matchedFare) {
                                    // Extract and save segments
                                    $odo = $matchedFare['OriginDestinationOptions'] ?? [];
                                    $segments = [];
                                    foreach ($odo as $wrapper) {
                                        $odOpts = $wrapper['OriginDestinationOption'] ?? [];
                                        if (!isset($odOpts[0])) $odOpts = [$odOpts];
                                        $legSegs = [];
                                        foreach ($odOpts as $opt) {
                                            $seg = $opt['FlightSegment'] ?? null;
                                            if ($seg) $legSegs[] = $seg;
                                        }
                                        if (!empty($legSegs)) $segments[] = ['legs' => $legSegs];
                                    }
                                    if (!empty($segments)) {
                                        $fb->update(['itinerary_data' => ['segments' => $segments]]);
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning('Could not recover itinerary_data: ' . $e->getMessage());
                    }
                }
            }

            if ($booking instanceof \App\Models\Booking) {
                $booking->load(['user', 'passengers', 'flightBooking']);
            } else {
                $booking->load(['user', 'passengers', 'package', 'season']);
            }

            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'fontDir' => array_merge($fontDirs, [
                    public_path('fonts'),
                ]),
                'fontdata' => $fontData + [
                    'tajawal' => [
                        'R' => 'tajawal.ttf',
                        'B' => 'tajawal.ttf',
                        'useOTL' => 0xFF,
                        'useKashida' => 75,
                    ]
                ],
                'default_font' => 'tajawal',
                'tempDir' => storage_path('app/temp')
            ]);

            $dir = app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
            $mpdf->SetDirectionality($dir);
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'tajawal';

            $html = view($view, compact('booking', 'tripDetails'))->render();
            $mpdf->WriteHTML($html);

            $fileName = $fileNamePrefix . $booking->id . '_' . time() . '.pdf';
            $filePath = 'invoices/' . $fileName;

            Storage::disk('public')->put($filePath, $mpdf->Output('', 'S'));

            return $filePath;
        } catch (\Exception $e) {
            Log::error('Invoice Generation Failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate Official Travel Insurance Certificate PDF
     *
     * @param \App\Models\InsurancePolicy $policy
     * @return string|false Path to the generated PDF
     */
    public function generateInsurancePolicyPdf(\App\Models\InsurancePolicy $policy)
    {
        try {
            $policy->load(['user', 'flightBooking', 'tripBooking', 'hotelBooking']);

            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'fontDir' => array_merge($fontDirs, [
                    public_path('fonts'),
                ]),
                'fontdata' => $fontData + [
                    'tajawal' => [
                        'R' => 'tajawal.ttf',
                        'B' => 'tajawal.ttf',
                        'useOTL' => 0xFF,
                        'useKashida' => 75,
                    ]
                ],
                'default_font' => 'tajawal',
                'tempDir' => storage_path('app/temp')
            ]);

            $dir = app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
            $mpdf->SetDirectionality($dir);
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'tajawal';

            $html = view('invoices.insurance_policy', compact('policy'))->render();
            $mpdf->WriteHTML($html);

            $fileName = 'insurance_policy_' . $policy->policy_number . '_' . time() . '.pdf';
            $filePath = 'invoices/insurance/' . $fileName;

            Storage::disk('public')->put($filePath, $mpdf->Output('', 'S'));

            return $filePath;
        } catch (\Exception $e) {
            Log::error('Insurance Policy PDF Generation Failed: ' . $e->getMessage());
            return false;
        }
    }
}
