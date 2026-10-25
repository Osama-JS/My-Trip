<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\HotelBooking;
use App\Models\TripBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view users', only: ['index', 'show', 'getData']),
            new Middleware('permission:create users', only: ['create', 'store']),
            new Middleware('permission:edit users', only: ['edit', 'update', 'toggleStatus', 'resetPassword', 'verify']),
            new Middleware('permission:delete users', only: ['destroy']),
            
            // Similarly for subscribers (which is essentially viewing/managing customers)
            new Middleware('permission:view subscribers', only: ['subscribers', 'subscribersData']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $stats = [
            'total' => User::where('user_type', User::TYPE_ADMIN)->count(),
            'active' => User::where('user_type', User::TYPE_ADMIN)->where('status', 'active')->count(),
            'inactive' => User::where('user_type', User::TYPE_ADMIN)->where('status', 'inactive')->count(),
            'unverified' => User::where('user_type', User::TYPE_ADMIN)->whereNull('email_verified_at')->count(),
        ];
        $roles = \Spatie\Permission\Models\Role::all();
        $countries = \App\Helpers\CountryHelper::getAllCountries();
        return view('admin.users.index', compact('stats', 'roles', 'countries'));
    }

 /**
     * Get users for DataTables.
     */
    public function getData(Request $request)
    {
        $users = User::where('user_type', User::TYPE_ADMIN)->get();

        return response()->json([
            'data' => $users->map(function($user) {
                $statusBadge = '
                <div class="form-check form-switch d-inline-flex align-items-center p-0 m-0">
                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="status_switch_' . $user->id . '" ' . ($user->status === 'active' ? 'checked' : '') . ' onclick="toggleUserStatus(' . $user->id . ')" style="width: 36px; height: 18px; cursor: pointer;">
                    <label class="form-check-label fw-medium text-dark small cursor-pointer" for="status_switch_' . $user->id . '">' . ($user->status === 'active' ? __('Active') : __('Inactive')) . '</label>
                </div>';

                $isVerified = ($user->email_verified_at || $user->phone_verified_at);
                $verifiedBadge = '
                <div class="form-check form-switch d-inline-flex align-items-center p-0 m-0">
                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="verify_switch_' . $user->id . '" ' . ($isVerified ? 'checked' : '') . ' onclick="verifyUser(' . $user->id . ')" style="width: 36px; height: 18px; cursor: pointer;">
                    <label class="form-check-label fw-medium text-dark small cursor-pointer" for="verify_switch_' . $user->id . '">' . ($isVerified ? __('Verified') : __('Unverified')) . '</label>
                </div>';

                return [
                    'id' => $user->id,
                    'photo' => '<img src="' . $user->profile_photo_url . '" class="rounded-circle shadow-sm border border-2 border-white" style="width: 40px; height: 40px; object-fit: cover;" alt="">',
                    'info' => '<div>
                                <strong class="text-dark">' . $user->full_name . '</strong><br>
                                <small class="text-muted">' . $user->email . '</small><br>
                                ' . $user->roles->map(fn($r) => '<span class="badge bg-primary-subtle text-primary border border-primary border-opacity-10 mt-1">'.$r->name.'</span>')->implode(' ') . '
                            </div>',
                    'phone' => '<span class="text-muted">' . ($user->country_code ? $user->country_code . ' ' : '') . ($user->phone ?? '---') . '</span>',
                    'status' => $statusBadge,
                    'verified' => $verifiedBadge,
                    'actions' => auth()->user()->can('manage users') ? '
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm rounded-circle border shadow-sm" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false" style="width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; background-color: var(--bg-card) !important; border-color: var(--border-color) !important; color: var(--text-muted) !important;">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 py-2" style="z-index: 1060;">
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="viewUser(' . $user->id . ')"><i class="fa fa-eye text-info me-3 w-15px"></i> '.__('View').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="editUser(' . $user->id . ')"><i class="fas fa-pencil-alt text-primary me-3 w-15px"></i> '.__('Edit').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="resetUserPassword(' . $user->id . ')"><i class="fa fa-key text-warning me-3 w-15px"></i> '.__('Reset Password').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="verifyUser(' . $user->id . ')"><i class="fas fa-certificate text-success me-3 w-15px"></i> ' . ($isVerified ? __('Revoke Verification') : __('Verify Account')) . '</a>
                                <div class="dropdown-divider my-1"></div>
                                <a class="dropdown-item text-danger py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="deleteUser(' . $user->id . ')"><i class="fa fa-trash text-danger me-3 w-15px"></i> '.__('Delete').'</a>
                            </div>
                        </div>' : ''
                ];
            })
        ]);
    }

    /**
     * Show User Activity (Bookings, Searches)
     */
    public function activity($id)
    {
        $user = User::with([
            'tripBookings.trip',
            'flightBookings',
            'hotelBookings',
            'favorites.trip',
        ])->findOrFail($id);

        $searchLogs = \App\Models\FlightSearchLog::where('user_id', $id)->latest()->limit(50)->get();

        // Calculate Stats (only confirmed and paid bookings)
        $confirmedTrips = $user->tripBookings->whereIn('status', ['confirmed', 'paid', 'completed']);
        $confirmedFlights = $user->flightBookings->whereIn('status', ['confirmed', 'paid', 'completed']);
        $confirmedHotels = $user->hotelBookings->whereIn('status', ['confirmed', 'paid', 'completed']);

        $totalSpent = $confirmedTrips->sum('total_price') + 
                      $confirmedFlights->sum('total_amount') + 
                      $confirmedHotels->sum('total_price');

        $totalBookingsCount = $user->tripBookings->count() + 
                              $user->flightBookings->count() + 
                              $user->hotelBookings->count();

        $confirmedCount = $confirmedTrips->count() + 
                          $confirmedFlights->count() + 
                          $confirmedHotels->count();

        $stats = [
            'total_bookings' => $totalBookingsCount,
            'confirmed_bookings' => $confirmedCount,
            'total_spent' => $totalSpent,
            'favorites_count' => $user->favorites ? $user->favorites->count() : 0,
            'total_searches' => $searchLogs->count(),
            'last_active' => $user->updated_at ? $user->updated_at->diffForHumans() : '—',
            'success_rate' => $totalBookingsCount > 0 ? round(($confirmedCount / $totalBookingsCount) * 100) : 0,
        ];

        $bookings = collect()
            ->concat($user->tripBookings)
            ->concat($user->flightBookings)
            ->concat($user->hotelBookings)
            ->sortByDesc('created_at');
        $searches = $searchLogs;

        // Categorized live counts for tabs
        $counts = [
            'flights' => $user->flightBookings->count(),
            'hotels'  => $user->hotelBookings->count(),
            'trips'   => $user->tripBookings->count(),
        ];
        $counts['all'] = $counts['flights'] + $counts['hotels'] + $counts['trips'];

        // Initial paginator for 'all' tab
        $flightsInitial = $user->flightBookings->map(function ($b) { $b->booking_type = 'flight'; return $b; });
        $hotelsInitial  = $user->hotelBookings->map(function ($b) { $b->booking_type = 'hotel'; return $b; });
        $tripsInitial   = $user->tripBookings->map(function ($b) { $b->booking_type = 'trip'; return $b; });

        $mergedInitial = $flightsInitial->concat($hotelsInitial)->concat($tripsInitial)->sortByDesc('created_at')->values();
        $initialBookings = new LengthAwarePaginator(
            $mergedInitial->slice(0, 10)->values(),
            $mergedInitial->count(),
            10,
            1,
            ['path' => route('admin.users.bookings.data', $user->id)]
        );

        return view('admin.users.activity', compact('user', 'searchLogs', 'stats', 'bookings', 'searches', 'counts', 'initialBookings'));
    }

    /**
     * Get paginated and filtered bookings data for user activity (AJAX)
     */
    public function getBookingsData(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $type = $request->get('type', 'all'); // all, flights, hotels, trips
        $status = $request->get('status', 'all');
        $search = trim($request->get('search', ''));
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $perPage = max(5, min(100, (int) $request->get('per_page', 10)));
        $page = max(1, (int) $request->get('page', 1));

        // Common filter applier
        $applyFilters = function ($query, $serviceType) use ($status, $dateFrom, $dateTo, $search) {
            if ($status && $status !== 'all') {
                if ($status === 'confirmed') {
                    $query->whereIn('status', ['confirmed', 'paid', 'completed', 'active']);
                } elseif ($status === 'pending') {
                    $query->whereIn('status', ['pending', 'processing', 'awaiting_payment']);
                } elseif ($status === 'cancelled') {
                    $query->whereIn('status', ['cancelled', 'failed', 'rejected', 'refunded']);
                } else {
                    $query->where('status', $status);
                }
            }
            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            if ($search) {
                if ($serviceType === 'flight') {
                    $query->where(function ($q) use ($search) {
                        $q->where('booking_reference', 'like', "%{$search}%")
                          ->orWhere('airline_name', 'like', "%{$search}%")
                          ->orWhere('pnr_code', 'like', "%{$search}%")
                          ->orWhereHas('flightBooking', function ($fq) use ($search) {
                              $fq->where('origin', 'like', "%{$search}%")
                                 ->orWhere('destination', 'like', "%{$search}%")
                                 ->orWhere('flight_number', 'like', "%{$search}%");
                          })
                          ->orWhereHas('passengers', function ($pq) use ($search) {
                              $pq->where('first_name', 'like', "%{$search}%")
                                 ->orWhere('last_name', 'like', "%{$search}%");
                          });
                    });
                } elseif ($serviceType === 'hotel') {
                    $query->where(function ($q) use ($search) {
                        $q->where('reference_num', 'like', "%{$search}%")
                          ->orWhere('hotel_name', 'like', "%{$search}%")
                          ->orWhere('city_name', 'like', "%{$search}%")
                          ->orWhere('room_name', 'like', "%{$search}%");
                    });
                } elseif ($serviceType === 'trip') {
                    $query->where(function ($q) use ($search) {
                        $q->where('id', 'like', "%{$search}%")
                          ->orWhereHas('trip', function ($tq) use ($search) {
                              $tq->where('title', 'like', "%{$search}%")
                                 ->orWhere('destination', 'like', "%{$search}%");
                          });
                    });
                }
            }
        };

        // Base queries
        $flightsQuery = Booking::where('user_id', $user->id)->with(['flightBooking', 'passengers']);
        $applyFilters($flightsQuery, 'flight');

        $hotelsQuery = HotelBooking::where('user_id', $user->id);
        $applyFilters($hotelsQuery, 'hotel');

        $tripsQuery = TripBooking::where('user_id', $user->id)->with('trip');
        $applyFilters($tripsQuery, 'trip');

        // Dynamic counts for each tab under current search / date filters
        $counts = [
            'flights' => (clone $flightsQuery)->count(),
            'hotels'  => (clone $hotelsQuery)->count(),
            'trips'   => (clone $tripsQuery)->count(),
        ];
        $counts['all'] = $counts['flights'] + $counts['hotels'] + $counts['trips'];

        // Paginate according to type
        if ($type === 'flights') {
            $bookings = $flightsQuery->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        } elseif ($type === 'hotels') {
            $bookings = $hotelsQuery->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        } elseif ($type === 'trips') {
            $bookings = $tripsQuery->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        } else {
            // 'all': merge the 3 sets, sort by created_at DESC, and wrap in LengthAwarePaginator
            $flights = $flightsQuery->orderBy('created_at', 'desc')->get()->map(function ($b) {
                $b->booking_type = 'flight';
                return $b;
            });
            $hotels = $hotelsQuery->orderBy('created_at', 'desc')->get()->map(function ($b) {
                $b->booking_type = 'hotel';
                return $b;
            });
            $trips = $tripsQuery->orderBy('created_at', 'desc')->get()->map(function ($b) {
                $b->booking_type = 'trip';
                return $b;
            });

            $merged = $flights->concat($hotels)->concat($trips)->sortByDesc('created_at')->values();
            $total = $merged->count();
            $items = $merged->slice(($page - 1) * $perPage, $perPage)->values();

            $bookings = new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => route('admin.users.bookings.data', $user->id)]
            );
        }

        $html = view('admin_v2.users.partials.bookings_table', compact('bookings', 'type', 'user'))->render();

        return response()->json([
            'success' => true,
            'type' => $type,
            'html' => $html,
            'counts' => $counts,
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page'    => $bookings->lastPage(),
                'total'        => $bookings->total(),
                'from'         => $bookings->firstItem() ?? 0,
                'to'           => $bookings->lastItem() ?? 0,
            ]
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Flight stats
        $flightBookings = $user->flightBookings;
        $flightCompleted = $flightBookings->whereIn('status', ['confirmed', 'paid', 'completed'])->count();
        $flightFailed    = $flightBookings->whereIn('status', ['cancelled', 'failed', 'rejected', 'refunded'])->count();
        $flightPending   = $flightBookings->whereIn('status', ['pending', 'processing', 'awaiting_payment'])->count();

        // Hotel stats
        $hotelBookings = $user->hotelBookings;
        $hotelCompleted = $hotelBookings->whereIn('status', ['confirmed', 'paid', 'completed'])->count();
        $hotelFailed    = $hotelBookings->whereIn('status', ['cancelled', 'failed', 'rejected', 'refunded'])->count();
        $hotelPending   = $hotelBookings->whereIn('status', ['pending', 'processing', 'awaiting_payment'])->count();

        // Trip stats
        $tripBookings = $user->tripBookings;
        $tripCompleted = $tripBookings->whereIn('status', ['confirmed', 'paid', 'completed'])->count();
        $tripFailed    = $tripBookings->whereIn('status', ['cancelled', 'failed', 'rejected', 'refunded'])->count();
        $tripPending   = $tripBookings->whereIn('status', ['pending', 'processing', 'awaiting_payment'])->count();

        $totalSpent = $flightBookings->whereIn('status', ['confirmed', 'paid', 'completed'])->sum('total_amount')
                    + $hotelBookings->whereIn('status', ['confirmed', 'paid', 'completed'])->sum('total_price')
                    + $tripBookings->whereIn('status', ['confirmed', 'paid', 'completed'])->sum('total_price');

        $isVerified = ($user->email_verified_at || $user->phone_verified_at);

        // Separate country code and phone cleanly so they never appear merged in the edit modal
        $split = \App\Helpers\CountryHelper::splitPhone($user->phone, $user->country_code);
        $user->country_code = $split['country_code'];
        $user->phone = $split['phone'];

        return response()->json([
            'success' => true,
            'user' => $user,
            'clean_phone' => $split['phone'],
            'clean_country_code' => $split['country_code'],
            'is_verified' => (bool) $isVerified,
            'roles' => $user->roles->pluck('name'),
            'photo_url' => $user->profile_photo_url,
            'created_at' => $user->created_at->format('Y-m-d H:i'),
            'activity_url' => route('admin.users.activity', $user->id),
            'stats' => [
                'total_spent'    => (float) $totalSpent,
                'total_bookings' => $flightBookings->count() + $hotelBookings->count() + $tripBookings->count(),
                'flights' => [
                    'total'     => $flightBookings->count(),
                    'completed' => $flightCompleted,
                    'failed'    => $flightFailed,
                    'pending'   => $flightPending,
                ],
                'hotels' => [
                    'total'     => $hotelBookings->count(),
                    'completed' => $hotelCompleted,
                    'failed'    => $hotelFailed,
                    'pending'   => $hotelPending,
                ],
                'trips' => [
                    'total'     => $tripBookings->count(),
                    'completed' => $tripCompleted,
                    'failed'    => $tripFailed,
                    'pending'   => $tripPending,
                ],
            ]
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'nullable|string',
            'country_code' => 'nullable|string|max:10',
            'password'     => 'required|min:8',
            'status'       => 'required|in:active,inactive',
            'user_type'    => 'nullable|in:admin,customer',
            'roles'        => 'nullable|array',
            'roles.*'      => 'exists:roles,name'
        ]);

        $split = \App\Helpers\CountryHelper::splitPhone($request->phone, $request->country_code);
        $validated['country_code'] = $split['country_code'];
        $validated['phone'] = $split['phone'];

        $validated['password'] = Hash::make($request->password);

        if ($request->has('user_type')) {
            $validated['user_type'] = $request->input('user_type');
        } else {
            // Fallback for cached browsers: check referer URL
            if (str_contains($request->headers->get('referer', ''), '/subscribers')) {
                $validated['user_type'] = User::TYPE_CUSTOMER;
            } else {
                $validated['user_type'] = User::TYPE_ADMIN;
            }
        }

        $user = User::create($validated);
        
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return response()->json([
            'success' => true,
            'message' => 'User created successfully'
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', Rule::unique('users')->ignore($user->id)],
            'country_code' => 'nullable|string|max:10',
            'password' => 'nullable|min:8',
            'status' => 'required|in:active,inactive',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name'
        ]);

        $data = $request->only(['first_name', 'last_name', 'email', 'phone', 'country_code', 'status', 'country', 'city', 'address', 'gender', 'date_of_birth']);

        $split = \App\Helpers\CountryHelper::splitPhone($request->phone, $request->country_code);
        $data['country_code'] = $split['country_code'];
        $data['phone'] = $split['phone'];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus(User $user)
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $updated = $user->update(['status' => $newStatus]);

        // dd([
        //     'user_id' => $user->id,
        //     'new_status' => $newStatus,
        //     'updated' => $updated
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'User status updated to ' . $newStatus,
            'status' => $newStatus
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user'
            ], 500);
        }
    }

    /**
     * Display subscribers list
     */
    public function subscribers()
    {
        $countries = \App\Helpers\CountryHelper::getAllCountries();
        return view('admin.subscribers.index', compact('countries'));
    }

    /**
     * Get subscribers data for DataTables
     */
    public function subscribersData()
    {
        $users = User::where('user_type', User::TYPE_CUSTOMER)->get();

        return response()->json([
            'data' => $users->map(function($user) {
                $statusBadge = '
                <div class="form-check form-switch d-inline-flex align-items-center p-0 m-0">
                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="status_switch_' . $user->id . '" ' . ($user->status === 'active' ? 'checked' : '') . ' onclick="toggleSubscriberStatus(' . $user->id . ')" style="width: 36px; height: 18px; cursor: pointer;">
                    <label class="form-check-label fw-medium text-dark small cursor-pointer" for="status_switch_' . $user->id . '">' . ($user->status === 'active' ? __('Active') : __('Inactive')) . '</label>
                </div>';

                $isVerified = ($user->email_verified_at || $user->phone_verified_at);
                $verifiedBadge = '
                <div class="form-check form-switch d-inline-flex align-items-center p-0 m-0">
                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="verify_switch_' . $user->id . '" ' . ($isVerified ? 'checked' : '') . ' onclick="verifySubscriber(' . $user->id . ')" style="width: 36px; height: 18px; cursor: pointer;">
                    <label class="form-check-label fw-medium text-dark small cursor-pointer" for="verify_switch_' . $user->id . '">' . ($isVerified ? __('Verified') : __('Unverified')) . '</label>
                </div>';

                return [
                    'id' => $user->id,
                    'photo' => '<img src="' . $user->profile_photo_url . '" class="rounded-circle shadow-sm border border-2 border-white" style="width: 40px; height: 40px; object-fit: cover;" alt="">',
                    'info' => '<div>
                                <strong class="text-dark">' . $user->full_name . '</strong><br>
                                <small class="text-muted">' . $user->email . '</small>
                            </div>',
                    'phone' => '<span class="text-muted">' . ($user->country_code ? $user->country_code . ' ' : '') . ($user->phone ?? '---') . '</span>',
                    'status' => $statusBadge,
                    'verified' => $verifiedBadge,
                    'actions' => '
                        <div class="dropdown">
                            <button type="button" class="btn btn-white btn-sm rounded-circle border shadow-sm" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false" style="width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; background-color:#ffffff !important; border-color:#e2e8f0 !important;">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 py-2" style="z-index: 1060;">
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="viewSubscriber(' . $user->id . ')"><i class="fa fa-eye text-info me-3 w-15px"></i> '.__('View').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="editSubscriber(' . $user->id . ')"><i class="fas fa-pencil-alt text-primary me-3 w-15px"></i> '.__('Edit').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="resetSubscriberPassword(' . $user->id . ')"><i class="fa fa-key text-dark me-3 w-15px"></i> '.__('Reset Password').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="' . route('admin.users.activity', $user->id) . '"><i class="fa fa-chart-line text-secondary me-3 w-15px"></i> '.__('Activity').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="verifySubscriber(' . $user->id . ')"><i class="fas fa-certificate text-success me-3 w-15px"></i> ' . ($isVerified ? __('Revoke Verification') : __('Verify Account')) . '</a>
                                <div class="dropdown-divider my-1"></div>
                                <a class="dropdown-item text-danger py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="deleteSubscriber(' . $user->id . ')"><i class="fa fa-trash text-danger me-3 w-15px"></i> '.__('Delete').'</a>
                            </div>
                        </div>'
                ];
            })
        ]);
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Password reset successfully')
        ]);
    }

    /**
     * Manually toggle a user's account verification
     */
    public function verify(User $user)
    {
        $isVerified = ($user->email_verified_at || $user->phone_verified_at);
        if ($isVerified) {
            $user->update([
                'email_verified_at' => null,
                'phone_verified_at' => null,
            ]);
            $msg = app()->getLocale() == 'ar' ? 'تم إلغاء توثيق الحساب بنجاح' : __('Account verification revoked successfully');
            $newStatus = 'unverified';
        } else {
            $user->update([
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);
            $msg = app()->getLocale() == 'ar' ? 'تم توثيق الحساب بنجاح' : __('Account manually verified successfully');
            $newStatus = 'verified';
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'status' => $newStatus,
            'verified' => $newStatus
        ]);
    }
}
