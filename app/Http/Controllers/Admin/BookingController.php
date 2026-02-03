<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TraveloproService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected $traveloproService;

    public function __construct(TraveloproService $traveloproService)
    {
        $this->traveloproService = $traveloproService;
    }

    // Flights
    public function availableFlights()
    {
        return view('admin.bookings.flights.available');
    }

    /**
     * Display listing of local bookings
     */
    public function index()
    {
        return view('admin.bookings.index');
    }

    /**
     * Display booking details
     */
    public function show($id)
    {
        $booking = \App\Models\Booking::with(['user', 'passengers', 'flightApiLogs'])->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Download Invoice
     */
    public function invoice($id)
    {
        $booking = \App\Models\Booking::findOrFail($id);
        $service = new \App\Services\InvoiceService();
        $pdf = $service->generateInvoice($booking);

        if (!$pdf) {
            return back()->with('error', 'Failed to generate invoice.');
        }

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="invoice-'.$booking->booking_reference.'.pdf"');
    }

    /**
     * Get bookings for DataTables
     */
    public function getData(Request $request)
    {
        $bookings = \App\Models\Booking::with('user')->latest()->get();

        return response()->json([
            'data' => $bookings->map(function ($booking) {
                $statusBadge = match($booking->status) {
                    'confirmed' => '<span class="badge badge-success">Confirmed</span>',
                    'paid' => '<span class="badge badge-info">Paid (Processing)</span>',
                    'pending' => '<span class="badge badge-warning">Pending</span>',
                    'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
                    default => '<span class="badge badge-light">'.$booking->status.'</span>'
                };

                return [
                    'id' => $booking->id,
                    'reference' => $booking->booking_reference,
                    'user' => $booking->user ? $booking->user->name : 'N/A', // Assuming user->name exists or fix logic
                    'amount' => $booking->total_amount . ' ' . $booking->currency,
                    'status' => $statusBadge,
                    'date' => $booking->created_at->format('Y-m-d H:i'),
                    'actions' => '
                        <div class="d-flex">
                            <a href="'.route('admin.bookings.show', $booking->id).'" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fa fa-eye"></i></a>
                            '.($booking->ticket_status === 'ticketed'
                                ? '<a href="'.route('admin.bookings.invoice', $booking->id).'" class="btn btn-secondary shadow btn-xs sharp" target="_blank"><i class="fa fa-file-invoice"></i></a>'
                                : '').'
                        </div>'
                ];
            })
        ]);
    }

    /**
     * Search for flights via AJAX/POST
     */
    public function searchFlights(Request $request)
    {
        try {
            $results = $this->traveloproService->searchFlights($request->all());

            if (isset($results['status']) && $results['status'] === 'error') {
                return response()->json(['error' => true, 'message' => $results['message']], 500);
            }

            return response()->json(['error' => false, 'data' => $results]);
        } catch (\Exception $e) {
            Log::error('Admin Flight Search Error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => __('An error occurred while searching for flights.')], 500);
        }
    }

    /**
     * Validate the selected flight fare
     */
    public function validateFare(Request $request)
    {
        try {
            $result = $this->traveloproService->validateFare($request->all());

            if (isset($result['status']) && $result['status'] === 'error') {
                return response()->json(['error' => true, 'message' => $result['message']], 500);
            }

            // Check if IsValid is true in response
            $isValid = $result['AirRevalidateResponse']['AirRevalidateResult']['IsValid'] ?? false;
            if ($isValid !== true && $isValid !== 'true') {
                 return response()->json(['error' => true, 'message' => __('Fare is no longer valid or available.')], 422);
            }

            return response()->json(['error' => false, 'data' => $result]);
        } catch (\Exception $e) {
            Log::error('Admin Flight Validate Error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => __('An error occurred while validating the fare.')], 500);
        }
    }

    /**
     * Create actual booking (PNR)
     */
    public function createBooking(Request $request)
    {
        try {
            $result = $this->traveloproService->createBooking($request->all());

            if (isset($result['status']) && $result['status'] === 'error') {
                return response()->json(['error' => true, 'message' => $result['message']], 500);
            }

            // Persist locally for Admin/Web flow as well
            $uniqueId = $result['CreateBookingResponse']['CreateBookingResult']['UniqueID'] ?? null;
            $totalAmount = $result['CreateBookingResponse']['CreateBookingResult']['TotalAmount'] ?? 0;
            $redirectUrl = null;

            if ($uniqueId) {
                // Check if already exists to avoid dupes (though unlikely with new UUID)
                $booking = \App\Models\Booking::firstOrCreate(
                    ['booking_reference' => $uniqueId],
                    [
                        'user_id' => auth()->id() ?? 1,
                        'supplier_session_id' => $request->flight_session_id,
                        'status' => 'pending',
                        'ticket_status' => 'booked',
                        'total_amount' => $totalAmount,
                        'currency' => 'SAR',
                        'contact_email' => $request->customerEmail,
                        'contact_phone' => $request->customerPhone,
                        'pnr_created_at' => now(),
                    ]
                );

                // Save Passengers
                if ($request->has('passengers') && is_array($request->passengers)) {
                    foreach ($request->passengers as $pax) {
                        $booking->passengers()->create([
                            'title' => $pax['title'] ?? 'Mr',
                            'first_name' => $pax['first_name'],
                            'last_name' => $pax['last_name'],
                            'type' => $pax['type'] ?? 'adult',
                            'dob' => $pax['dob'] ?? null,
                            'nationality' => $pax['nationality'] ?? null,
                            'passport_no' => $pax['passport_no'] ?? null,
                        ]);
                    }
                }

                $redirectUrl = route('payment.show', $booking->id);
            }

            return response()->json([
                'error' => false,
                'data' => $result,
                'redirect_url' => $redirectUrl,
                'message' => __('Booking created. Redirecting to payment...')
            ]);
        } catch (\Exception $e) {
            Log::error('Admin Flight Booking Error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => __('An error occurred while creating the booking.')], 500);
        }
    }

    public function flightRequests()
    {
        return view('admin.bookings.flights.requests');
    }

    public function ongoingFlights()
    {
        return view('admin.bookings.flights.ongoing');
    }

    /**
     * Utility endpoints for UI (Airports/Airlines)
     */
    /**
     * Utility endpoints for UI (Airports/Airlines)
     */
    public function getAirports(Request $request)
    {
        $refresh = $request->boolean('refresh');
        return response()->json($this->traveloproService->getAirportList($refresh));
    }

    public function getAirlines(Request $request)
    {
        $refresh = $request->boolean('refresh');
        return response()->json($this->traveloproService->getAirlineList($refresh));
    }

    // Hotels
    public function hotelList()
    {
        return view('admin.bookings.hotels.index');
    }

    public function hotelRequests()
    {
        return view('admin.bookings.hotels.requests');
    }
}
