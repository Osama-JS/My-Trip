<?php

namespace App\Http\Controllers;

use App\Models\InsurancePolicy;
use App\Models\InsuranceQuote;
use App\Models\Setting;
use App\Services\SitataInsuranceService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InsuranceController extends Controller
{
    protected SitataInsuranceService $insuranceService;

    public function __construct(SitataInsuranceService $insuranceService)
    {
        $this->insuranceService = $insuranceService;
    }

    /**
     * AJAX Endpoint to calculate real-time quote for Flight, Hotel, or Trip booking
     */
    public function getQuote(Request $request)
    {
        // If insurance is disabled globally in settings
        if (Setting::get('insurance_enabled', '1') !== '1') {
            return response()->json([
                'success' => false,
                'message' => __('Insurance service is currently unavailable.'),
            ], 403);
        }

        $request->validate([
            'destination_country' => 'nullable|string|max:10',
            'departure_date'      => 'nullable|date',
            'return_date'         => 'nullable|date',
            'trip_cost'           => 'nullable|numeric|min:0',
            'passengers_count'    => 'nullable|integer|min:1',
            'coverage_type'       => 'nullable|string|in:basic,comprehensive,schengen,vip',
            'booking_type'        => 'nullable|string|in:flight,trip,hotel,standalone',
        ]);

        try {
            $quote = $this->insuranceService->getQuote($request->all());
            return response()->json($quote);
        } catch (\Exception $e) {
            Log::error('Insurance Quote Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Could not calculate insurance quote: ') . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Customer Dashboard: List user's insurance policies
     */
    public function customerPolicies(Request $request)
    {
        $user = auth()->user();
        $policies = InsurancePolicy::where('user_id', $user->id)
            ->with(['flightBooking', 'tripBooking', 'hotelBooking'])
            ->latest()
            ->paginate(10);

        return view('frontend.customer.insurances.index', compact('policies'));
    }

    /**
     * Customer direct download of official insurance certificate PDF
     */
    public function downloadPdf($id)
    {
        $user = auth()->user();
        $policy = InsurancePolicy::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $invoiceService = app(InvoiceService::class);
        $pdfPath = $policy->pdf_path;

        if (!$pdfPath || !file_exists(storage_path('app/public/' . $pdfPath))) {
            $pdfPath = $invoiceService->generateInsurancePolicyPdf($policy);
            if ($pdfPath) {
                $policy->update(['pdf_path' => $pdfPath]);
            }
        }

        if ($pdfPath && file_exists(storage_path('app/public/' . $pdfPath))) {
            return response()->download(storage_path('app/public/' . $pdfPath), 'Travel_Insurance_' . $policy->policy_number . '.pdf');
        }

        return redirect()->back()->with('error', __('Could not generate insurance document.'));
    }
}
