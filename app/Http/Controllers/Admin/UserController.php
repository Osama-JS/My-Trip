<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
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
        return view('admin.users.index', compact('stats'));
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

                $verifiedBadge = $user->email_verified_at
                    ? '<div class="d-flex align-items-center"><i class="fa fa-circle text-primary me-2" style="font-size: 8px;"></i> <span class="fw-medium text-dark">'.__('Verified').'</span></div>'
                    : '<div class="d-flex align-items-center"><i class="fa fa-circle text-warning me-2" style="font-size: 8px;"></i> <span class="fw-medium text-dark">'.__('Unverified').'</span></div>';

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
                    'actions' => auth()->user()->can('manage users') ? '
                        <div class="dropdown">
                            <button type="button" class="btn btn-white btn-sm rounded-circle border shadow-sm" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false" style="width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; background-color:#ffffff !important; border-color:#e2e8f0 !important;">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 py-2" style="z-index: 1060;">
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="viewUser(' . $user->id . ')"><i class="fa fa-eye text-info me-3 w-15px"></i> '.__('View').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="editUser(' . $user->id . ')"><i class="fas fa-pencil-alt text-primary me-3 w-15px"></i> '.__('Edit').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="toggleUserStatus(' . $user->id . ')"><i class="fas fa-ban text-warning me-3 w-15px"></i> '.__('Toggle Status').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="resetUserPassword(' . $user->id . ')"><i class="fa fa-key text-dark me-3 w-15px"></i> '.__('Reset Password').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="' . route('admin.users.activity', $user->id) . '"><i class="fa fa-chart-line text-secondary me-3 w-15px"></i> '.__('Activity').'</a>
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

        // Calculate Stats
        $confirmedTrips = $user->tripBookings->where('status', 'confirmed');
        $confirmedFlights = $user->flightBookings->where('status', 'confirmed');
        $confirmedHotels = $user->hotelBookings->where('status', 'confirmed');

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
            'favorites_count' => $user->favorites->count(),
            'last_active' => $user->updated_at->diffForHumans(),
            'success_rate' => $totalBookingsCount > 0 ? round(($confirmedCount / $totalBookingsCount) * 100) : 0,
        ];

        return view('admin.users.activity', compact('user', 'searchLogs', 'stats'));
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'user' => $user,
            'photo_url' => $user->profile_photo_url,
            'created_at' => $user->created_at->format('Y-m-d H:i')
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'nullable|string|unique:users,phone',
            'country_code' => 'nullable|string|max:10',
            'password'     => 'required|min:8',
            'status'       => 'required|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($request->password);

        User::create($validated);

        // dd($request->all());

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
        ]);

        $data = $request->only(['first_name', 'last_name', 'email', 'phone', 'country_code', 'status', 'country', 'city', 'address', 'gender', 'date_of_birth']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

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
        return view('admin.subscribers.index');
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

                $verifiedBadge = $user->email_verified_at
                    ? '<div class="d-flex align-items-center"><i class="fa fa-circle text-primary me-2" style="font-size: 8px;"></i> <span class="fw-medium text-dark">'.__('Verified').'</span></div>'
                    : '<div class="d-flex align-items-center"><i class="fa fa-circle text-warning me-2" style="font-size: 8px;"></i> <span class="fw-medium text-dark">'.__('Unverified').'</span></div>';

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
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="toggleSubscriberStatus(' . $user->id . ')"><i class="fas fa-ban text-warning me-3 w-15px"></i> '.__('Toggle Status').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="resetSubscriberPassword(' . $user->id . ')"><i class="fa fa-key text-dark me-3 w-15px"></i> '.__('Reset Password').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="' . route('admin.users.activity', $user->id) . '"><i class="fa fa-chart-line text-secondary me-3 w-15px"></i> '.__('Activity').'</a>
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
}
