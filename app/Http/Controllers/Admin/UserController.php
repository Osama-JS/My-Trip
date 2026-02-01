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
        return view('admin.users.index');
    }

    /**
     * Get users for DataTables.
     */
    public function getData(Request $request)
    {
        $users = User::where('user_type', User::TYPE_CUSTOMER)->get();

        return response()->json([
            'data' => $users->map(function($user) {
                // ... map logic
                return [
                     // ...
                    'actions' => '
                        <div class="d-flex">
                            <a href="'.route('admin.users.activity', $user->id).'" class="btn btn-info shadow btn-xs sharp me-1"><i class="fa fa-chart-line"></i></a>
                            <button onclick="editUser(' . $user->id . ')" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></button>
                            <button onclick="toggleUserStatus(' . $user->id . ')" class="btn btn-warning shadow btn-xs sharp me-1"><i class="fas fa-ban"></i></button>
                            <button onclick="deleteUser(' . $user->id . ')" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></button>
                        </div>'
                ];
            })
        ]);
    }

    /**
     * Show User Activity (Bookings, Searches)
     */
    public function activity($id)
    {
        $user = User::with(['bookings' => function($q) {
            $q->latest();
        }])->findOrFail($id);

        $searchLogs = \App\Models\FlightSearchLog::where('user_id', $id)->latest()->limit(50)->get();

        $stats = [
            'total_bookings' => $user->bookings->count(),
            'total_spent' => $user->bookings->where('status', 'confirmed')->sum('total_amount'),
            'last_active' => $user->updated_at->diffForHumans()
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
                $statusBadge = $user->status === 'active'
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';

                return [
                    'photo' => '<img src="' . $user->profile_photo_url . '" class="rounded-circle" width="40" alt="">',
                    'name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => ($user->country_code ? $user->country_code . ' ' : '') . ($user->phone ?? '---'),
                    'status' => $statusBadge,
                    'joined' => $user->created_at->format('Y-m-d'),
                    'actions' => '
                        <div class="d-flex">
                            <button onclick="viewSubscriber(' . $user->id . ')" class="btn btn-info shadow btn-xs sharp me-1"><i class="fa fa-eye"></i></button>
                            <a href="' . route('admin.users.activity', $user->id) . '" class="btn btn-primary shadow btn-xs sharp"><i class="fa fa-chart-line"></i></a>
                        </div>'
                ];
            })
        ]);
    }
}
