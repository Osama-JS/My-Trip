<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\TripBooking;
use App\Models\HotelBooking;
use App\Models\Booking;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CustomerPaymentController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * List all payments for the authenticated customer.
     */
    public function index(Request $request)
    {
        $query = Payment::where('user_id', Auth::id())->with(['payable']);

        $status = $request->get('status');
        $gateway = $request->get('gateway');
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Filter by status
        if ($status && in_array($status, ['paid', 'pending', 'failed'])) {
            $query->where('status', $status);
        }

        // Filter by gateway
        if ($gateway) {
            $query->where('payment_gateway', $gateway);
        }

        // Filter by search keyword (description, method, gateway, or inside relations)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('payment_gateway', 'like', "%{$search}%")
                  ->orWhere('payment_method', 'like', "%{$search}%")
                  ->orWhereHasMorph('payable', [TripBooking::class, HotelBooking::class, Booking::class], function($subQ, $type) use ($search) {
                      if ($type === TripBooking::class) {
                          $subQ->whereHas('trip', function($t) use ($search) {
                              $t->where('title_ar', 'like', "%{$search}%")
                                ->orWhere('title_en', 'like', "%{$search}%");
                          })->orWhere('id', 'like', "%{$search}%");
                      } elseif ($type === HotelBooking::class) {
                          $subQ->where('hotel_name', 'like', "%{$search}%")
                               ->orWhere('city_name', 'like', "%{$search}%")
                               ->orWhere('id', 'like', "%{$search}%");
                      } elseif ($type === Booking::class) {
                          $subQ->where('airline_name', 'like', "%{$search}%")
                               ->orWhere('pnr_code', 'like', "%{$search}%")
                               ->orWhere('id', 'like', "%{$search}%");
                      }
                  });
            });
        }

        // Filter by dates
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $payments = $query->latest()->paginate(10);

        // Fetch counts for stats boxes
        $stats = [
            'total'   => Payment::where('user_id', Auth::id())->count(),
            'paid'    => Payment::where('user_id', Auth::id())->where('status', 'paid')->count(),
            'pending' => Payment::where('user_id', Auth::id())->where('status', 'pending')->count(),
            'failed'  => Payment::where('user_id', Auth::id())->where('status', 'failed')->count(),
        ];

        return view('frontend.customer.payments.index', compact('payments', 'stats'));
    }

    /**
     * Show checkout page for a booking.
     */
    public function checkout($bookingId)
    {
        $booking = TripBooking::with(['trip', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);

        if ($booking->status === 'confirmed') {
            return redirect()->route('customer.bookings.show', $bookingId)
                ->with('info', __('هذا الحجز مدفوع بالفعل.'));
        }

        if ($booking->status === 'cancelled') {
            return redirect()->route('customer.bookings.show', $bookingId)
                ->with('error', __('لا يمكن الدفع لحجز ملغى.'));
        }

        return view('frontend.customer.payments.checkout', compact('booking'));
    }

    /**
     * Download invoice for a confirmed booking.
     */
    public function downloadInvoice($bookingId)
    {
        $booking = TripBooking::where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->with(['trip', 'user', 'passengers'])
            ->findOrFail($bookingId);

        $payment = $booking->payments()->latest()->first();

        if ($payment && $payment->invoice_path && Storage::disk('public')->exists($payment->invoice_path)) {
            $path = Storage::disk('public')->path($payment->invoice_path);
            return response()->download($path, 'invoice-' . $booking->id . '.pdf');
        }

        $invoicePath = $this->invoiceService->generateInvoice($booking);
        if (!$invoicePath) {
            return back()->with('error', __('تعذّر توليد الفاتورة.'));
        }

        return response()->download(Storage::disk('public')->path($invoicePath), 'invoice-' . $booking->id . '.pdf');
    }
}
