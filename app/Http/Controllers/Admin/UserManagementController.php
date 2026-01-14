<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('admin.users.index', compact('roles'));
    }

    public function getData()
    {
        $users = User::with('roles')->get();
        return response()->json([
            'data' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'profile_photo' => $user->profile_photo_url,
                    'name' => '<strong>' . $user->name . '</strong><br><small>' . $user->email . '</small>',
                    'user_type' => '<span class="badge badge-primary">' . $user->user_type . '</span>',
                    'roles' => $user->getRoleNames()->map(function($role) {
                        return '<span class="badge badge-outline-primary me-1">' . $role . '</span>';
                    })->implode(''),
                    'phone' => $user->phone ?? 'N/A',
                    'actions' => '
                        <div class="d-flex">
                            <button onclick="editUser(' . $user->id . ')" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></button>
                            <button onclick="deleteUser(' . $user->id . ')" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></button>
                        </div>'
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'user_type' => 'required|in:admin,customer',
            'phone' => 'nullable|string',
            'country' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'roles' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['password', 'profile_photo', 'roles']);
            $data['password'] = Hash::make($request->password);

            if ($request->hasFile('profile_photo')) {
                $data['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
            }

            $user = User::create($data);

            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(User $user)
    {
        return response()->json([
            'success' => true,
            'user' => $user,
            'roles' => $user->roles->pluck('name'),
            'photo_url' => $user->profile_photo_url
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'user_type' => 'required|in:admin,customer',
            'phone' => 'nullable|string',
            'country' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'roles' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['password', 'profile_photo', 'roles']);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('profile_photo')) {
                // Delete old photo
                if ($user->profile_photo) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                $data['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
            }

            $user->update($data);

            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

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
}
