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
            $booking->load(['user']);

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'tempDir' => storage_path('app/temp')
            ]);

            // Set direction dynamically
            $dir = app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
            $mpdf->SetDirectionality($dir);

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
            }

            $booking->load(['user', 'passengers', 'package', 'season']);

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'default_font' => 'cairo',
                'tempDir' => storage_path('app/temp')
            ]);

            $dir = app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
            $mpdf->SetDirectionality($dir);

            $html = view($view, compact('booking'))->render();
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
}
