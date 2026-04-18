<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use App\Models\HotelBooking;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Services\InvoiceService;
use App\Traits\HotelBookingFinalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;

class BookingController extends Controller
{
    use HotelBookingFinalizer;
    protected InvoiceService $invoiceService;
    protected NotificationService $notificationService;

    public function __construct(InvoiceService $invoiceService, NotificationService $notificationService)
    {
        $this->invoiceService = $invoiceService;
        $this->notificationService = $notificationService;
    }

    /**
     * List all bookings for the authenticated customer (Redirects to trips by default).
     */
    public function index(Request $request)
    {
        return redirect()->route('customer.bookings.trips');
    }

    /**
     * List Trip Bookings
     */
    public function trips(Request $request)
    {
        $status = $request->get('status');
        $query = \App\Models\TripBooking::with(['trip.images'])->where('user_id', Auth::id());
        
        if ($status && in_array($status, ['pending', 'confirmed', 'cancelled'])) {
            $query->where('status', $status);
        }

        $bookings = $query->latest()->paginate(10);
        return view('frontend.customer.bookings.trips', compact('bookings'));
    }

    /**
     * List Flight Bookings
     */
    public function flights(Request $request)
    {
        $status = $request->get('status');
        $query = \App\Models\Booking::where('user_id', Auth::id());
        
        if ($status && in_array($status, ['pending', 'confirmed', 'cancelled'])) {
            $query->where('status', $status);
        }

        $bookings = $query->latest()->paginate(10);
        return view('frontend.customer.bookings.flights', compact('bookings'));
    }

    /**
     * List Hotel Bookings
     */
    public function hotels(Request $request)
    {
        $status = $request->get('status');
        $query = \App\Models\HotelBooking::where('user_id', Auth::id());
        
        if ($status && in_array($status, ['pending', 'confirmed', 'cancelled'])) {
            $query->where('status', $status);
        }

        $bookings = $query->latest()->paginate(10);
        return view('frontend.customer.bookings.hotels', compact('bookings'));
    }

    /**
     * Show booking details.
     */
    public function show($id, Request $request)
    {
        $type = $request->get('type', 'trip');

        if ($type === 'flight') {
            $booking = Booking::with(['passengers', 'payments', 'flightApiLogs'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            return view('frontend.customer.bookings.flights_show', compact('booking'));
        }

        if ($type === 'hotel') {
            $booking = \App\Models\HotelBooking::where('user_id', Auth::id())
                ->findOrFail($id);

            return view('frontend.customer.bookings.hotels_show', compact('booking'));
        }

        $booking = TripBooking::with(['trip.images', 'trip.toCountry', 'trip.toCity', 'passengers', 'payments', 'bankTransfers', 'histories' => function($q) {
            $q->orderBy('created_at', 'asc');
        }])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('frontend.customer.bookings.show', compact('booking'));
    }

    /**
     * Show the booking form for a specific trip.
     */
    public function create($trip_id)
    {
        $trip = Trip::active()->with(['images', 'fromCountry', 'toCountry'])->findOrFail($trip_id);

        return view('frontend.customer.bookings.create', compact('trip'));
    }

    /**
     * Create a new booking from the trip show page.
     */
    public function store(Request $request)
    {
        $request->validate([
            'trip_id'                      => 'required|exists:trips,id',
            'notes'                        => 'nullable|string|max:500',
            'passengers'                   => 'required|array|min:1|max:20',
            'passengers.*.name'            => 'required|string|max:255',
            'passengers.*.phone'           => 'nullable|string|max:20',
            'passengers.*.passport_number' => 'nullable|string|max:50',
            'passengers.*.passport_expiry' => 'nullable|date',
            'passengers.*.nationality'     => 'nullable|string|max:100',
        ]);

        $trip = Trip::active()->findOrFail($request->trip_id);

        $passengersCount = count($request->passengers);

        if ($trip->tickets < $passengersCount) {
            return back()->withErrors([
                'passengers' => __('لا توجد تذاكر كافية. المتاح: :count', ['count' => $trip->tickets])
            ])->withInput();
        }

        // Dynamic pricing
        $baseCapacity = $trip->base_capacity ?? 2;
        $extraPrice   = $trip->extra_passenger_price ?? 0;
        $totalPrice   = $trip->price;

        if ($passengersCount > $baseCapacity) {
            $totalPrice += ($passengersCount - $baseCapacity) * $extraPrice;
        }

        $booking = TripBooking::create([
            'user_id'        => Auth::id(),
            'trip_id'        => $trip->id,
            'tickets_count'  => $passengersCount,
            'total_price'    => $totalPrice,
            'status'         => 'pending',
            'notes'          => $request->notes,
            'booking_date'   => now(),
        ]);

        foreach ($request->passengers as $passengerData) {
            $booking->passengers()->create($passengerData);
        }

        return redirect()->route('customer.bookings.show', $booking->id)
            ->with('success', __('تم إنشاء الحجز بنجاح! يمكنك الآن إتمام الدفع.'));
    }

    /**
     * Cancel a pending booking.
     */
    public function cancel($id)
    {
        $booking = TripBooking::where('user_id', Auth::id())->findOrFail($id);

        if ($booking->status !== 'pending') {
            return back()->with('error', __('لا يمكن إلغاء حجز تم تأكيده أو دفعه.'));
        }

        $booking->passengers()->delete();
        $booking->update(['status' => 'cancelled']);

        return redirect()->route('customer.bookings.index')
            ->with('success', __('تم إلغاء الحجز بنجاح.'));
    }

    /**
     * Get Hotel Cancellation Charge before actual cancellation.
     */
    public function cancelHotelCharge($id, \App\Services\TraveloproHotelService $hotelService)
    {
        $booking = \App\Models\HotelBooking::where('user_id', Auth::id())->findOrFail($id);

        if ($booking->status === 'cancelled') {
            return response()->json(['status' => 'error', 'message' => __('هذا الحجز ملغى بالفعل.')]);
        }

        $result = $hotelService->getCancelCharge([
            'supplierConfirmationNum' => $booking->supplier_confirmation_num,
            'referenceNum' => $booking->reference_num,
            'sessionId' => $booking->session_id,
            'productId' => $booking->product_id,
            'tokenId' => $booking->token_id,
        ]);

        return response()->json($result);
    }

    /**
     * Finalize Hotel Cancellation.
     */
    public function cancelHotel(Request $request, $id, \App\Services\TraveloproHotelService $hotelService)
    {
        $booking = \App\Models\HotelBooking::where('user_id', Auth::id())->findOrFail($id);

        if ($booking->status === 'cancelled') {
            return back()->with('error', __('هذا الحجز ملغى بالفعل.'));
        }

        $result = $hotelService->cancel([
            'supplierConfirmationNum' => $booking->supplier_confirmation_num,
            'referenceNum' => $booking->reference_num,
            'sessionId' => $booking->session_id,
            'productId' => $booking->product_id,
            'tokenId' => $booking->token_id,
        ]);

        if (isset($result['status']) && $result['status'] === 'success') {
            $booking->update(['status' => 'cancelled']);

            // SEND NOTIFICATION
            $this->notificationService->sendToUser(
                Auth::user(),
                \App\Models\Notification::TYPE_BOOKING_CANCELLED,
                __('Booking Cancelled'),
                __('Your hotel booking #:id for :hotel has been successfully cancelled.', [
                    'id' => $booking->id,
                    'hotel' => $booking->hotel_name
                ]),
                ['booking_id' => $booking->id, 'type' => 'hotel']
            );

            return redirect()->route('customer.bookings.hotels')
                ->with('success', __('تم إلغاء حجز الفندق بنجاح.'));
        }

        $msg = $result['message'] ?? __('تعذر إلغاء الحجز حالياً. يرجى التواصل مع الدعم.');
        return back()->with('error', $msg);
    }

    /**
     * Sync/Refresh Hotel Booking Status from Provider.
     */
    public function syncHotelBookingStatus($id, \App\Services\TraveloproHotelService $hotelService)
    {
        $booking = \App\Models\HotelBooking::where('user_id', Auth::id())->findOrFail($id);

        // AGGRESSIVE SYNC: If not confirmed, always try to finalize with supplier
        if (empty($booking->supplier_confirmation_num)) {
            Log::info("Sync Status calling finalizer for booking {$id}");
            $finalized = $this->finalizeHotelSupplierBooking($booking);
            if ($finalized) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => __('Booking finalized and confirmed successfully.'),
                        'status' => 'confirmed'
                    ]);
                }
                return back()->with('success', __('Booking finalized and confirmed successfully.'));
            }
        }

        // If still no confirmation num, we can't sync with API
        if (empty($booking->supplier_confirmation_num)) {
             return back()->with('info', __('الحجز بانتظار الدفع أو التأكيد.'));
        }

        $result = $hotelService->getBookingDetails([
            'supplierConfirmationNum' => $booking->supplier_confirmation_num,
            'referenceNum' => $booking->reference_num,
            'sessionId' => $booking->session_id,
            'productId' => $booking->product_id,
            'tokenId' => $booking->token_id,
        ]);

        if (isset($result['status']) && $result['status'] === 'success' && isset($result['bookingDetails'])) {
            $apiStatus = strtolower($result['bookingDetails']['status'] ?? $booking->status);
            
            if ($apiStatus !== $booking->status) {
                $booking->update(['status' => $apiStatus]);
                return back()->with('success', __('تم تحديث حالة الحجز بنجاح. الحالة الحالية: :status', ['status' => __($apiStatus)]));
            }
            
            return back()->with('info', __('حالة الحجز محدثة بالفعل.'));
        }

        return back()->with('error', __('تعذر جلب تفاصيل الحجز من المزود حالياً.'));
    }

    /**
     * Download invoice PDF.
     */
    public function downloadInvoice($id)
    {
        $booking = TripBooking::where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->findOrFail($id);

        // Use existing invoice or generate new one
        $payment = $booking->payments()->latest()->first();

        if ($payment && $payment->invoice_path && Storage::disk('public')->exists($payment->invoice_path)) {
            $filePath = Storage::disk('public')->path($payment->invoice_path);
            return response()->download($filePath, 'invoice-' . $booking->id . '.pdf');
        }

        // Generate on demand
        $invoicePath = $this->invoiceService->generateInvoice($booking);

        if (!$invoicePath) {
            return back()->with('error', __('تعذّر توليد الفاتورة. الرجاء المحاولة لاحقاً.'));
        }

        $filePath = Storage::disk('public')->path($invoicePath);
        return response()->download($filePath, 'invoice-' . $booking->id . '.pdf');
    }

    /**
     * Download hotel voucher PDF.
     */
    public function downloadHotelVoucher($id)
    {
        $booking = \App\Models\HotelBooking::where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->findOrFail($id);

        if ($booking->invoice_path && Storage::disk('public')->exists($booking->invoice_path)) {
            $filePath = Storage::disk('public')->path($booking->invoice_path);
            return response()->download($filePath, 'voucher-' . $booking->id . '.pdf');
        }

        // Generate on demand if missing
        $voucherPath = $this->invoiceService->generateHotelVoucher($booking);

        if (!$voucherPath) {
            return back()->with('error', __('تعذّر توليد القسيمة. الرجاء المحاولة لاحقاً.'));
        }

        // Save generated path
        $booking->update(['invoice_path' => $voucherPath]);

        $filePath = Storage::disk('public')->path($voucherPath);
        return response()->download($filePath, 'voucher-' . $booking->id . '.pdf');
    }
}
