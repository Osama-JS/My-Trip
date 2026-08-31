<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsurancePolicy;
use App\Models\InsuranceQuote;
use App\Models\InsuranceApiLog;
use App\Models\Setting;
use App\Services\SitataInsuranceService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InsurancePolicyController extends Controller
{
    protected SitataInsuranceService $insuranceService;

    public function __construct(SitataInsuranceService $insuranceService)
    {
        $this->insuranceService = $insuranceService;
    }

    /**
     * Display a listing of insurance policies with key financial metrics
     */
    public function index(Request $request)
    {
        $query = InsurancePolicy::with(['user', 'flightBooking', 'tripBooking', 'hotelBooking'])->latest();

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('booking_type')) {
            $query->where('booking_type', $request->booking_type);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('policy_number', 'like', "%{$s}%")
                  ->orWhere('certificate_number', 'like', "%{$s}%")
                  ->orWhereHas('user', function ($uq) use ($s) {
                      $uq->where('name', 'like', "%{$s}%")
                         ->orWhere('email', 'like', "%{$s}%")
                         ->orWhere('phone', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $policies = $query->paginate(15)->withQueryString();

        // Financial & Operational Metrics
        $totalPolicies = InsurancePolicy::count();
        $activePolicies = InsurancePolicy::where('status', 'active')->count();
        $totalRevenue = InsurancePolicy::where('status', '!=', 'cancelled')->sum('selling_price');
        $totalCost = InsurancePolicy::where('status', '!=', 'cancelled')->sum('net_cost');
        $totalProfit = InsurancePolicy::where('status', '!=', 'cancelled')->sum('platform_profit');

        $stats = [
            'total_policies'  => $totalPolicies,
            'active_policies' => $activePolicies,
            'total_revenue'   => $totalRevenue,
            'total_cost'      => $totalCost,
            'total_profit'    => $totalProfit,
        ];

        return view('admin.insurance.index', compact('policies', 'stats'));
    }

    /**
     * Display the specified insurance policy details
     */
    public function show($id)
    {
        $policy = InsurancePolicy::with(['user', 'quote', 'logs', 'flightBooking', 'tripBooking', 'hotelBooking'])->findOrFail($id);
        return view('admin.insurance.show', compact('policy'));
    }

    /**
     * Profit Margins & Financial Analytics Dashboard
     */
    public function profits(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $query = InsurancePolicy::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        $totalRevenue = (clone $query)->sum('selling_price');
        $totalCost = (clone $query)->sum('net_cost');
        $totalProfit = (clone $query)->sum('platform_profit');
        $totalCount = (clone $query)->count();

        $avgProfitMargin = $totalRevenue > 0 ? (($totalProfit / $totalRevenue) * 100) : 0;

        // Breakdown by Booking Type
        $byType = (clone $query)
            ->select('booking_type', 
                DB::raw('count(*) as count'), 
                DB::raw('sum(selling_price) as revenue'), 
                DB::raw('sum(net_cost) as cost'), 
                DB::raw('sum(platform_profit) as profit')
            )
            ->groupBy('booking_type')
            ->get();

        // Recent Profit Transactions
        $recentPolicies = (clone $query)->latest()->take(20)->get();

        return view('admin.insurance.profits', compact(
            'totalRevenue', 'totalCost', 'totalProfit', 'totalCount', 'avgProfitMargin', 'byType', 'recentPolicies', 'startDate', 'endDate'
        ));
    }

    /**
     * Insurance Settings Management View
     */
    public function settings()
    {
        $settings = [
            'insurance_enabled'       => Setting::get('insurance_enabled', '1'),
            'sitata_api_key'          => Setting::get('sitata_api_key', config('insurance.api_key', '')),
            'sitata_organization_id'  => Setting::get('sitata_organization_id', config('insurance.organization_id', '')),
            'sitata_public_token'     => Setting::get('sitata_public_token', config('insurance.public_token', '')),
            'sitata_api_url'          => Setting::get('sitata_api_url', config('insurance.api_url', 'https://staging.sitata.com/api/v2')),
            'insurance_mock_mode'     => Setting::get('insurance_mock_mode', '1'),
            'insurance_margin_type'   => Setting::get('insurance_margin_type', 'percentage'),
            'insurance_margin_value'  => Setting::get('insurance_margin_value', '20'),
            'insurance_min_price'     => Setting::get('insurance_min_price', '35'),
            'insurance_emergency_phone' => Setting::get('insurance_emergency_phone', '+1-800-456-7890'),
            'insurance_emergency_email' => Setting::get('insurance_emergency_email', 'assistance@sitata.com'),
        ];

        return view('admin.insurance.settings', compact('settings'));
    }

    /**
     * Update Insurance Settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'insurance_margin_type'  => 'required|in:percentage,fixed',
            'insurance_margin_value' => 'required|numeric|min:0',
            'insurance_min_price'    => 'required|numeric|min:0',
        ]);

        Setting::set('insurance_enabled', $request->has('insurance_enabled') ? '1' : '0');
        Setting::set('sitata_api_key', $request->input('sitata_api_key', ''));
        Setting::set('sitata_organization_id', $request->input('sitata_organization_id', ''));
        Setting::set('sitata_public_token', $request->input('sitata_public_token', ''));
        Setting::set('sitata_api_url', $request->input('sitata_api_url', 'https://staging.sitata.com/api/v2'));
        Setting::set('insurance_mock_mode', $request->has('insurance_mock_mode') ? '1' : '0');
        Setting::set('insurance_margin_type', $request->input('insurance_margin_type'));
        Setting::set('insurance_margin_value', $request->input('insurance_margin_value'));
        Setting::set('insurance_min_price', $request->input('insurance_min_price'));
        Setting::set('insurance_emergency_phone', $request->input('insurance_emergency_phone', '+1-800-456-7890'));
        Setting::set('insurance_emergency_email', $request->input('insurance_emergency_email', 'assistance@sitata.com'));

        return redirect()->back()->with('success', __('Insurance settings updated successfully.'));
    }

    /**
     * Test live connection to Sitata API
     */
    public function testConnection(Request $request)
    {
        $apiKey = $request->input('sitata_api_key') ?: Setting::get('sitata_api_key', config('insurance.api_key'));
        $orgId = $request->input('sitata_organization_id') ?: Setting::get('sitata_organization_id', config('insurance.organization_id'));
        $apiUrl = rtrim($request->input('sitata_api_url') ?: Setting::get('sitata_api_url', config('insurance.api_url', 'https://staging.sitata.com/api/v2')), '/');

        if (empty($apiKey) || empty($orgId)) {
            return response()->json([
                'success' => false,
                'message' => __('Please enter both Organization ID and Authentication Token before testing.')
            ], 422);
        }

        $startTime = microtime(true);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->withHeaders([
                'Organization'  => $orgId,
                'Authorization' => 'TKN ' . $apiKey,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->get($apiUrl . '/countries');

            $latency = round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                return response()->json([
                    'success'     => true,
                    'status_code' => $response->status(),
                    'latency_ms'  => $latency,
                    'endpoint'    => $apiUrl,
                    'message'     => __('Connection to Sitata API successful! (HTTP :status in :ms ms)', ['status' => $response->status(), 'ms' => $latency])
                ]);
            }

            return response()->json([
                'success'     => false,
                'status_code' => $response->status(),
                'latency_ms'  => $latency,
                'endpoint'    => $apiUrl,
                'message'     => __('Sitata API responded with error HTTP :status: :msg', [
                    'status' => $response->status(),
                    'msg'    => $response->json()['message'] ?? $response->body()
                ])
            ], 400);

        } catch (\Exception $e) {
            $latency = round((microtime(true) - $startTime) * 1000);
            return response()->json([
                'success'    => false,
                'latency_ms' => $latency,
                'message'    => __('Connection failed: :error', ['error' => $e->getMessage()])
            ], 500);
        }
    }

    /**
     * Cancel policy from admin
     */
    public function cancel(Request $request, $id)
    {
        $policy = InsurancePolicy::findOrFail($id);
        $reason = $request->input('reason', 'Cancelled by Admin');

        $this->insuranceService->cancelPolicy($policy, $reason);

        return redirect()->back()->with('success', __('Policy cancelled successfully.'));
    }

    /**
     * Download or stream the official insurance PDF
     */
    public function downloadPdf($id)
    {
        $policy = InsurancePolicy::findOrFail($id);
        $invoiceService = app(InvoiceService::class);

        $pdfPath = $policy->pdf_path;
        if (!$pdfPath || !file_exists(storage_path('app/public/' . $pdfPath))) {
            $pdfPath = $invoiceService->generateInsurancePolicyPdf($policy);
            if ($pdfPath) {
                $policy->update(['pdf_path' => $pdfPath]);
            }
        }

        if ($pdfPath && file_exists(storage_path('app/public/' . $pdfPath))) {
            return response()->download(storage_path('app/public/' . $pdfPath), 'Insurance_Policy_' . $policy->policy_number . '.pdf');
        }

        return redirect()->back()->with('error', __('Could not generate PDF file.'));
    }
}
