<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankTransfer;
use App\Models\Payment;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class BankTransferController extends Controller
{
    public function index()
    {
        return view('admin.bank_transfers.index');
    }

    public function getData()
    {
        try {
            $transfers = BankTransfer::with(['user', 'booking.trip'])->latest()->get();

            $data = $transfers->map(function ($row) {
                $statusBadge = $row->status === 'approved'
                    ? '<span class="badge bg-success-subtle text-success border border-success border-opacity-10 px-3 py-1 rounded-pill fw-bold"><i class="fas fa-check-circle me-1"></i>' . __('Approved') . '</span>'
                    : ($row->status === 'rejected'
                        ? '<span class="badge bg-danger-subtle text-danger border border-danger border-opacity-10 px-3 py-1 rounded-pill fw-bold"><i class="fas fa-times-circle me-1"></i>' . __('Rejected') . '</span>'
                        : '<span class="badge bg-warning-subtle text-warning border border-warning border-opacity-10 px-3 py-1 rounded-pill fw-bold"><i class="fas fa-clock me-1"></i>' . __('Pending') . '</span>');

                $userInfo = '
                <div class="d-flex align-items-center">
                    <img src="' . ($row->user ? $row->user->profile_photo_url : asset('images/default-avatar.png')) . '" class="rounded-circle shadow-sm border border-2 border-white me-2" style="width: 36px; height: 36px; object-fit: cover;" alt="">
                    <div>
                        <strong class="text-dark">' . ($row->user ? $row->user->full_name : __('Guest')) . '</strong><br>
                        <small class="text-muted">' . ($row->user ? $row->user->email : '') . '</small>
                    </div>
                </div>';

                return [
                    'id' => $row->id,
                    'user' => $userInfo,
                    'trip' => ($row->booking && $row->booking->trip) ? $row->booking->trip->title : '—',
                    'amount' => '<strong class="text-dark">' . number_format($row->booking->total_price ?? $row->amount, 2) . ' ' . __('SAR') . '</strong>',
                    'sender_name' => '<span class="fw-medium text-dark">' . $row->sender_name . '</span>',
                    'receipt_number' => '<code class="bg-light px-2 py-1 rounded text-dark fw-bold">' . $row->receipt_number . '</code>',
                    'status' => $statusBadge,
                    'created_at' => '<span class="text-muted">' . $row->created_at->format('Y-m-d H:i') . '</span>',
                    'actions' => '<div class="dropdown">
                                    <button type="button" class="btn btn-white btn-sm rounded-circle border shadow-sm" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false" style="width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; background-color:#ffffff !important; border-color:#e2e8f0 !important;">
                                        <i class="fas fa-ellipsis-v text-muted"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 py-2" style="z-index: 1060;">
                                        <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="' . route('admin.bank-transfers.show', $row->id) . '"><i class="fa fa-eye text-primary me-3 w-15px"></i> '.__('Review').'</a>
                                    </div>
                                </div>'
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $transfer = BankTransfer::with(['user', 'booking.trip', 'booking.passengers'])->findOrFail($id);
        return view('admin.bank_transfers.show', compact('transfer'));
    }

    public function approve(Request $request, $id)
    {
        $transfer = BankTransfer::with('booking')->findOrFail($id);

        if ($transfer->status !== 'pending') {
            return back()->with('error', __('This transfer has already been processed.'));
        }

        DB::beginTransaction();
        try {
            // 1. Update Transfer Status
            $transfer->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now()
            ]);

            // 2. Update Booking Status
            $booking = $transfer->booking;
            $oldState = $booking->booking_state;
            $booking->update([
                'status' => 'confirmed',
                'booking_state' => \App\Models\TripBooking::STATE_PREPARING // Move to preparing after payment
            ]);

            // Create History for the state change
            \App\Models\BookingHistory::create([
                'trip_booking_id' => $booking->id,
                'user_id' => auth()->id(),
                'action' => 'payment_approved',
                'description' => __('Booking state moved to Preparing after bank transfer approval.'),
                'previous_state' => $oldState,
                'new_state' => \App\Models\TripBooking::STATE_PREPARING,
            ]);

            // 3. Create Payment Record
            $payment = Payment::create([
                'trip_booking_id' => $booking->id,
                'payable_id' => $booking->id,
                'payable_type' => \App\Models\TripBooking::class,
                'user_id' => $transfer->user_id,
                'amount' => $booking->total_price,
                'payment_gateway' => 'bank_transfer',
                'payment_method' => 'manual',
                'transaction_id' => 'BT-' . strtoupper(Str::random(12)),
                'status' => 'paid',
                'raw_response' => [
                    'bank_transfer_id' => $transfer->id,
                    'user_reference' => $transfer->receipt_number,
                    'sender_name' => $transfer->sender_name
                ]
            ]);

            // 4. Generate Invoice & Send Notification
            $invoiceService = app(InvoiceService::class);
            $invoicePath = $invoiceService->generateInvoice($booking);
            
            if ($invoicePath) {
                $payment->update(['invoice_path' => $invoicePath]);
            }

            $notificationService = app(NotificationService::class);
            $notificationService->sendToUser(
                $booking->user,
                'payment_success',
                __('Payment Approved'),
                __('Your bank transfer for booking #:id has been approved. Your trip is now confirmed.', ['id' => $booking->id]),
                ['booking_id' => $booking->id]
            );

            DB::commit();
            return redirect()->route('admin.bank-transfers.index')->with('success', __('Bank transfer approved successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error: ') . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $transfer = BankTransfer::findOrFail($id);

        if ($transfer->status !== 'pending') {
            return back()->with('error', __('This transfer has already been processed.'));
        }

        $transfer->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now()
        ]);

        // Notify user
        $transfer->load('user');
        $notificationService = app(NotificationService::class);
        $notificationService->sendToUser(
            $transfer->user,
            'payment_failed',
            __('Payment Rejected'),
            __('Your bank transfer for booking #:id was rejected. Reason: :reason', ['id' => $transfer->trip_booking_id, 'reason' => $request->rejection_reason]),
            ['booking_id' => $transfer->trip_booking_id]
        );

        return redirect()->route('admin.bank-transfers.index')->with('success', __('Bank transfer rejected successfully.'));
    }
}
