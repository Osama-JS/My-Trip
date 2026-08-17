<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Notifications\TicketUploadedNotification;
use App\Services\NotificationService;
use App\Models\Notification;

class TripBookingController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stats = [
            'total' => TripBooking::count(),
            'confirmed' => TripBooking::where('status', 'confirmed')->count(),
            'pending' => TripBooking::where('status', 'pending')->count(),
            'cancelled' => TripBooking::where('status', 'cancelled')->count(),
        ];
        return view('admin.trip_bookings.index', compact('stats'));
    }

    /**
     * Get data for DataTables
     */
    public function getData()
    {
        try {
            $bookings = TripBooking::with(['user', 'trip'])->latest()->get();

            $data = $bookings->map(function ($booking) {
                $stateMap = [
                    TripBooking::STATE_AWAITING_PAYMENT => ['class' => 'badge-state--amber', 'label' => __('Awaiting Payment')],
                    TripBooking::STATE_PREPARING => ['class' => 'badge-state--blue', 'label' => __('Preparing')],
                    TripBooking::STATE_ISSUING_TICKETS => ['class' => 'badge-state--blue', 'label' => __('Issuing Tickets')],
                    TripBooking::STATE_TICKETS_UPLOADED => ['class' => 'badge-state--green', 'label' => __('Tickets Uploaded')],
                    TripBooking::STATE_COMPLETED => ['class' => 'badge-state--green', 'label' => __('Completed')],
                    TripBooking::STATE_CANCELLED => ['class' => 'badge-state--red', 'label' => __('Cancelled')],
                ];

                $stateInfo = $stateMap[$booking->booking_state] ?? ['class' => 'badge-state--default', 'label' => $booking->booking_state];
                $statusBadge = '<span class="badge-state ' . $stateInfo['class'] . '">' . $stateInfo['label'] . '</span>';

                return [
                    'id' => $booking->id,
                    'user' => $booking->user ? $booking->user->full_name . '<br><small class="text-muted">' . $booking->user->phone . '</small>' : __('Guest'),
                    'trip' => $booking->trip ? $booking->trip->title . '<br><small class="text-muted">' . $booking->booking_date->format('Y-m-d') . '</small>' : __('Deleted Trip'),
                    'price' => number_format($booking->total_price, 2) . ' ' . __('SAR'),
                    'tickets' => '<span class="badge-state badge-state--default">' . $booking->tickets_count . '</span>',
                    'status' => $statusBadge,
                    'created_at' => $booking->created_at->format('Y-m-d H:i'),
                    'actions' => $this->getActionButtons($booking),
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getActionButtons($booking)
    {
        $showBtn = '';
        if (auth()->user()->can('view bookings')) {
            $showBtn = '<a href="' . route('admin.trip-bookings.show', $booking->id) . '" class="act-action-btn" title="' . __('View Details') . '"><i class="fas fa-eye"></i></a>';
        }

        // Status Buttons / Delete
        $deleteBtn = '';
        if (auth()->user()->can('manage bookings')) {
            $isCancelled = ($booking->status === 'cancelled' || $booking->booking_state === TripBooking::STATE_CANCELLED);
            if ($isCancelled) {
                $deleteBtn = '<button type="button" onclick="deleteBooking(' . $booking->id . ')" class="act-action-btn" style="color: #ef4444; background: rgba(239,68,68,0.1); border:none;" title="' . __('Delete') . '"><i class="fas fa-trash"></i></button>';
            } else {
                $deleteBtn = '<button type="button" class="act-action-btn" style="color: #94a3b8; background: rgba(148,163,184,0.1); border:none; cursor:not-allowed; opacity:0.6;" title="' . __('Cannot delete active booking (must be cancelled first)') . '" onclick="toastr.warning(\'' . __('Cannot delete booking unless its status is cancelled.') . '\')"><i class="fas fa-trash"></i></button>';
            }
        }

        return '<div class="d-flex align-items-center gap-1">' . $showBtn . $deleteBtn . '</div>';
    }

    /**
     * Delete a booking only if it is cancelled.
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('manage bookings')) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => __('Unauthorized action.')], 403);
            }
            return redirect()->back()->with('error', __('Unauthorized action.'));
        }

        $booking = TripBooking::findOrFail($id);

        $isCancelled = ($booking->status === 'cancelled' || $booking->booking_state === TripBooking::STATE_CANCELLED);
        if (!$isCancelled) {
            $msg = __('Cannot delete booking unless its status is cancelled.');
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        DB::beginTransaction();
        try {
            if ($booking->ticket_file_path && Storage::disk('public')->exists($booking->ticket_file_path)) {
                Storage::disk('public')->delete($booking->ticket_file_path);
            }
            $booking->passengers()->delete();
            $booking->histories()->delete();
            $booking->delete();

            DB::commit();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Booking deleted successfully.')
                ]);
            }
            return redirect()->route('admin.trip-bookings.index')->with('success', __('Booking deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to delete booking: ') . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', __('Failed to delete booking: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $booking = TripBooking::with(['user', 'trip', 'passengers', 'payment', 'payments', 'bankTransfers', 'histories.user'])->findOrFail($id);
        $latestBankTransfer = $booking->bankTransfers->sortByDesc('created_at')->first();
        return view('admin.trip_bookings.show', compact('booking', 'latestBankTransfer'));
    }

    /**
     * Update status
     */
    public function updateStatus(Request $request, $id)
    {
        $booking = TripBooking::findOrFail($id);

        $oldState = $booking->booking_state;
        $booking->update(['status' => $request->status]);

        if ($request->status == 'cancelled' || $request->status == 'refunded') {
             $newState = $request->status == 'cancelled' ? TripBooking::STATE_CANCELLED : $oldState;
             $booking->update(['booking_state' => $newState]);
             
             \App\Models\BookingHistory::create([
                'trip_booking_id' => $booking->id,
                'user_id' => auth()->id(),
                'action' => 'status_changed',
                'description' => __('Booking status updated to :status', ['status' => __($request->status)]),
                'previous_state' => $oldState,
                'new_state' => $booking->booking_state,
            ]);

            // SEND NOTIFICATION
            $title = $request->status == 'cancelled' ? __('Booking Cancelled') : __('Booking Updated');
            $type = $request->status == 'cancelled' ? Notification::TYPE_BOOKING_CANCELLED : Notification::TYPE_BOOKING_CONFIRMED;
            
            $this->notificationService->sendToUser(
                $booking->user,
                $type,
                $title,
                __('Your booking #:id status has been updated to :status by our team.', [
                    'id' => $booking->id,
                    'status' => __($request->status)
                ]),
                ['booking_id' => $booking->id, 'type' => 'trip']
            );
        }

        return redirect()->back()->with('success', __('Booking status updated successfully.'));
    }

    /**
     * Update Booking State (Received, Preparing, etc)
     */
    public function updateBookingState(Request $request, $id)
    {
        $booking = TripBooking::findOrFail($id);
        $request->validate([
            'booking_state' => 'required|in:awaiting_payment,preparing,issuing_tickets,tickets_uploaded,completed,cancelled'
        ]);

        $oldState = $booking->booking_state;
        $newState = $request->booking_state;
        $booking->update(['booking_state' => $newState]);

        \App\Models\BookingHistory::create([
            'trip_booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'action' => 'state_changed',
            'description' => __('Booking state manually updated to :state by admin.', ['state' => __($newState)]),
            'previous_state' => $oldState,
            'new_state' => $newState,
        ]);

        return redirect()->back()->with('success', __('Booking state updated successfully.'));
    }

    /**
     * Upload ticket for a booking
     */
    public function uploadTicket(Request $request, $id)
    {
        $booking = TripBooking::with('user', 'trip')->findOrFail($id);

        $request->validate([
            'ticket_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
            'send_email' => 'nullable|boolean'
        ]);

        if ($request->hasFile('ticket_file')) {
            // Delete old ticket if exists
            if ($booking->ticket_file_path && Storage::disk('public')->exists($booking->ticket_file_path)) {
                Storage::disk('public')->delete($booking->ticket_file_path);
            }

            $path = $request->file('ticket_file')->store('tickets', 'public');

            $oldState = $booking->booking_state;
            $booking->update([
                'ticket_file_path' => $path,
                'booking_state' => \App\Models\TripBooking::STATE_TICKETS_UPLOADED
            ]);
             \App\Models\BookingHistory::create([
                'trip_booking_id' => $booking->id,
                'user_id' => auth()->id(),
                'action' => 'ticket_uploaded',
                'description' => __('Trip ticket uploaded by admin'),
                'previous_state' => $oldState,
                'new_state' => \App\Models\TripBooking::STATE_TICKETS_UPLOADED,
            ]);

            // SEND NOTIFICATION
            $this->notificationService->sendToUser(
                $booking->user,
                Notification::TYPE_BOOKING_CONFIRMED,
                __('Tickets Uploaded'),
                __('Your tickets for :trip have been uploaded. You can download them from your dashboard.', [
                    'trip' => $booking->trip->title
                ]),
                ['booking_id' => $booking->id, 'type' => 'trip', 'icon' => 'ticket-alt']
            );

            // Optional: send email to customer
            if ($request->has('send_email') && $booking->user) {
                $booking->user->notify(new TicketUploadedNotification($booking));
                return redirect()->back()->with('success', __('Ticket uploaded and sent to customer successfully.'));
            }

            return redirect()->back()->with('success', __('Ticket uploaded successfully.'));
        }

        return redirect()->back()->with('error', __('Failed to upload ticket.'));
    }

    /**
     * Manually re-send an already uploaded ticket to the customer via email.
     */
    public function sendTicket($id)
    {
        $booking = TripBooking::with('user', 'trip')->findOrFail($id);

        if (!$booking->ticket_file_path) {
            return redirect()->back()->with('error', __('No ticket has been uploaded for this booking yet.'));
        }

        if (!$booking->user) {
            return redirect()->back()->with('error', __('The customer account no longer exists.'));
        }

        $booking->user->notify(new TicketUploadedNotification($booking));

        return redirect()->back()->with('success', __('Ticket sent to customer successfully.'));
    }

    /**
     * Display Platform Profits for Tour Packages.
     */
    public function profits(Request $request)
    {
        $query = TripBooking::where(function($q) {
            $q->whereIn('status', ['confirmed', 'paid', 'completed'])
              ->whereNotIn('booking_state', [TripBooking::STATE_AWAITING_PAYMENT, TripBooking::STATE_CANCELLED]);
        });

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $allBookings = $query->get();
        $totalProfit = $allBookings->sum('platform_profit');
        $totalRevenue = $allBookings->sum('total_price');
        $totalProviderEarnings = $allBookings->sum('provider_price');
        $companies = \App\Models\Company::active()->orderBy('name')->get();

        return view('admin.trip_bookings.profits', compact('totalProfit', 'totalRevenue', 'totalProviderEarnings', 'companies'));
    }

    /**
     * Get JSON data for Tour Packages profits DataTable.
     */
    public function getProfitsData(Request $request)
    {
        $query = TripBooking::with(['user', 'trip.company', 'company', 'passengers'])
            ->where(function($q) {
                $q->whereIn('status', ['confirmed', 'paid', 'completed'])
                  ->whereNotIn('booking_state', [TripBooking::STATE_AWAITING_PAYMENT, TripBooking::STATE_CANCELLED]);
            });

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $bookings = $query->latest()->get();
        $data = $bookings->map(function ($b) {
            $companyName = optional($b->company)->name ?? (optional(optional($b->trip)->company)->name ?? __('Direct Platform'));
            $tripTitle = optional($b->trip)->title_ar ?? (optional($b->trip)->title ?? __('Tour Package'));
            $commLabel = '';
            if ($b->commission_type === 'percentage') {
                $commLabel = '<small class="text-muted d-block">(' . floatval($b->commission_value) . '%)</small>';
            } elseif ($b->commission_type === 'fixed') {
                $commLabel = '<small class="text-muted d-block">(' . number_format($b->commission_value, 2) . ' ' . __('SAR/Pax') . ')</small>';
            }

            return [
                'id' => $b->id,
                'reference' => '<strong>#TRIP-' . str_pad($b->id, 5, '0', STR_PAD_LEFT) . '</strong>',
                'trip' => '<div><strong>' . e($tripTitle) . '</strong><small class="text-muted d-block">' . ($b->tickets_count ?? 1) . ' ' . __('Pax') . '</small></div>',
                'company' => '<span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-building me-1 text-primary"></i>' . e($companyName) . '</span>',
                'customer' => optional($b->user)->full_name ?? (optional($b->passengers->first())->name ?? __('Guest')),
                'date' => $b->created_at->format('Y-m-d H:i'),
                'provider_price' => number_format($b->provider_price, 2) . ' ' . __('SAR'),
                'total_amount' => number_format($b->total_price, 2) . ' ' . __('SAR'),
                'profit' => '<span class="text-success fw-bold">+' . number_format($b->platform_profit, 2) . ' ' . __('SAR') . '</span>' . $commLabel,
                'actions' => '<a href="' . route('admin.trip-bookings.show', $b->id) . '" class="btn btn-primary shadow btn-xs sharp" title="' . __('View') . '"><i class="fas fa-eye"></i></a>'
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Display Tour Packages Analytics and Statistics.
     */
    public function analytics(Request $request)
    {
        $query = TripBooking::with(['user', 'trip.company', 'company', 'passengers']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'confirmed') {
                $query->where(function($q) {
                    $q->whereIn('status', ['confirmed', 'paid', 'completed'])
                      ->orWhereIn('booking_state', ['confirmed', 'tickets_uploaded', 'completed']);
                });
            } else {
                $query->where('status', $status);
            }
        }
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $allFiltered = $query->get();

        // KPIs
        $totalBookings = $allFiltered->count();
        $confirmedList = $allFiltered->filter(function($b) {
            return in_array($b->status, ['confirmed', 'paid', 'completed']) || in_array($b->booking_state, ['confirmed', 'tickets_uploaded', 'completed']);
        });

        $totalRevenue = $confirmedList->sum('total_price');
        $totalProfit = $confirmedList->sum('platform_profit');
        $totalProviderEarnings = $confirmedList->sum('provider_price');

        $pendingBookings = $allFiltered->filter(function($b) {
            return $b->status === 'pending' || $b->booking_state === 'awaiting_payment';
        })->count();

        $confirmedBookings = $confirmedList->count();

        $cancelledBookings = $allFiltered->filter(function($b) {
            return $b->status === 'cancelled' || $b->booking_state === 'cancelled';
        })->count();

        $totalPassengers = $allFiltered->sum('tickets_count');

        // Timeline Trend
        $groupByFormat = $request->filled('date_from') && $request->filled('date_to') ? 'Y-m-d' : 'Y-m';
        $trendData = $allFiltered->groupBy(function($b) use ($groupByFormat) {
            return optional($b->created_at)->format($groupByFormat) ?? date($groupByFormat);
        })->map->count();

        $chartLabels = $trendData->keys()->toArray();
        $chartDataValues = $trendData->values()->toArray();

        // Top Companies Doughnut
        $companiesData = $allFiltered->map(function($b) {
            return optional($b->company)->name ?? (optional(optional($b->trip)->company)->name ?? __('Direct Platform'));
        })->countBy()->sortDesc()->take(5);

        // Status Distribution Doughnut
        $statusData = [
            __('Confirmed / Completed') => $confirmedBookings,
            __('Pending') => $pendingBookings,
            __('Cancelled') => $cancelledBookings,
        ];

        // Top Packages Bar Chart
        $packagesData = $allFiltered->map(function($b) {
            return optional($b->trip)->title_ar ?? (optional($b->trip)->title ?? __('Tour Package'));
        })->countBy()->sortDesc()->take(5);

        $stats = [
            'total' => $totalBookings,
            'revenue' => $totalRevenue,
            'profit' => $totalProfit,
            'provider_earnings' => $totalProviderEarnings,
            'pending' => $pendingBookings,
            'confirmed' => $confirmedBookings,
            'cancelled' => $cancelledBookings,
            'passengers' => $totalPassengers,
            'chartLabels' => $chartLabels,
            'chartData' => $chartDataValues,
            'companiesLabels' => $companiesData->keys()->toArray(),
            'companiesData' => $companiesData->values()->toArray(),
            'statusLabels' => array_keys($statusData),
            'statusData' => array_values($statusData),
            'packagesLabels' => $packagesData->keys()->toArray(),
            'packagesData' => $packagesData->values()->toArray(),
        ];

        // Top Customers
        $topCustomers = $confirmedList->groupBy('user_id')->map(function($userBookings) {
            $first = $userBookings->first();
            return (object) [
                'name' => optional($first->user)->full_name ?? (optional($first->passengers->first())->name ?? __('Guest')),
                'email' => optional($first->user)->email ?? (optional($first->passengers->first())->phone ?? __('N/A')),
                'bookings_count' => $userBookings->count(),
                'total_spent' => $userBookings->sum('total_price'),
                'platform_profit' => $userBookings->sum('platform_profit'),
            ];
        })->sortByDesc('bookings_count')->take(10);

        $recentBookings = $allFiltered->sortByDesc('created_at')->take(10);
        $companies = \App\Models\Company::active()->orderBy('name')->get();

        return view('admin.trip_bookings.analytics', compact('stats', 'recentBookings', 'topCustomers', 'companies'));
    }
}
