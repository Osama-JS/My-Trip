<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view roles', only: ['index', 'getData', 'show']),
            new Middleware('permission:create roles', only: ['create', 'store']),
            new Middleware('permission:edit roles', only: ['edit', 'update']),
            new Middleware('permission:delete roles', only: ['destroy']),
        ];
    }

    public function index()
    {
        $permissions = Permission::all();
        return view('admin.roles.index', compact('permissions'));
    }

    public function getData()
    {
        $roles = Role::with('permissions')->get();
        return response()->json([
            'data' => $roles->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => '<strong>' . $role->name . '</strong>',
                    'permissions' => $role->permissions->map(function($permission) {
                        return '<span class="badge badge-outline-primary me-1">' . $permission->name . '</span>';
                    })->implode(''),
                    'permissions_count' => '<span class="badge badge-info">' . $role->permissions->count() . '</span>',
                    'actions' => '
                        <div class="d-flex">
                            <button class="btn btn-primary shadow btn-xs sharp me-1 edit-role-btn" data-id="' . $role->id . '"><i class="fas fa-pencil-alt"></i></button>
                            <button class="btn btn-danger shadow btn-xs sharp delete-role-btn" data-id="' . $role->id . '"><i class="fa fa-trash"></i></button>
                        </div>'
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->name]);
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Role $role)
    {
        return response()->json([
            'success' => true,
            'role' => $role,
            'permissions' => $role->permissions->pluck('name')
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->permissions ?? []);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Role $role)
    {
        try {
            $role->delete();
            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role'
            ], 500);
        }
    }
}
