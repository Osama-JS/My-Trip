<?php

namespace App\Services;

use Mpdf\Mpdf;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;

class InvoiceService
{
    /**
     * Generate Invoice PDF for a booking
     *
     * @param Booking $booking
     * @return string|null Raw PDF content or null on failure
     */
    public function generateInvoice(Booking $booking)
    {
        try {
            // Configuration for Arabic Support
            $config = [
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
            ];

            $mpdf = new Mpdf($config);
            $mpdf->SetDirectionality('rtl');

            // Render the invoice view
            $html = view('invoices.flight_invoice', compact('booking'))->render();

            $mpdf->WriteHTML($html);

            return $mpdf->Output('', 'S'); // Return as string
        } catch (\Exception $e) {
            Log::error("Invoice Generation Failed: " . $e->getMessage());
            return null;
        }
    }
}
