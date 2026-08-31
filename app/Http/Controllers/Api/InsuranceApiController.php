<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InsurancePolicy;
use App\Models\InsuranceQuote;
use App\Models\Setting;
use App\Services\SitataInsuranceService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InsuranceApiController extends Controller
{
    protected SitataInsuranceService $insuranceService;

    public function __construct(SitataInsuranceService $insuranceService)
    {
        $this->insuranceService = $insuranceService;
    }

    /**
     * Get Real-time Insurance Quote for Mobile App
     * POST /api/insurance/quote
     */
    public function getQuote(Request $request)
    {
        if (Setting::get('insurance_enabled', '1') !== '1') {
            return response()->json([
                'success' => false,
                'message' => __('Insurance service is currently disabled.'),
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
            Log::error('API Insurance Quote Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Authenticated User's Insurance Policies
     * GET /api/insurance/my-policies
     */
    public function myPolicies(Request $request)
    {
        $user = $request->user();
        $policies = InsurancePolicy::where('user_id', $user->id)
            ->with(['flightBooking', 'tripBooking', 'hotelBooking'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $policies,
        ]);
    }

    /**
     * Get Specific Policy Details & Certificate URL
     * GET /api/insurance/policies/{id}
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $policy = InsurancePolicy::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['flightBooking', 'tripBooking', 'hotelBooking'])
            ->firstOrFail();

        $invoiceService = app(InvoiceService::class);
        if (!$policy->pdf_path || !file_exists(storage_path('app/public/' . $policy->pdf_path))) {
            $pdfPath = $invoiceService->generateInsurancePolicyPdf($policy);
            if ($pdfPath) {
                $policy->update(['pdf_path' => $pdfPath]);
            }
        }

        $pdfDownloadUrl = $policy->pdf_path ? asset('storage/' . $policy->pdf_path) : null;

        return response()->json([
            'success' => true,
            'policy' => $policy,
            'pdf_url' => $pdfDownloadUrl,
        ]);
    }
}
