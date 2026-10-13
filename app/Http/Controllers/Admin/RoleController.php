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
        $user = auth()->user();
        $canEdit = $user && $user->can('edit roles');
        $canDelete = $user && $user->can('delete roles');
        $systemRoles = ['super-admin', 'admin', 'agent', 'company_manager', 'accountant', 'customer_service', 'data_entry', 'auditor'];

        $roles = Role::with('permissions')->withCount('users')->get();

        return response()->json([
            'data' => $roles->map(function ($role) use ($canEdit, $canDelete, $systemRoles) {
                $isSuper = $role->name === 'super-admin';
                $isSystem = in_array($role->name, $systemRoles);

                $actions = '<div class="role-actions">';
                $actions .= '<button type="button" class="role-action-btn role-action-btn--view view-role-perms-btn" data-id="' . $role->id . '" title="' . e(__('View Details')) . '"><i class="fas fa-eye"></i></button>';
                if ($canEdit) {
                    $actions .= '<button type="button" class="role-action-btn role-action-btn--edit edit-role-btn" data-id="' . $role->id . '" title="' . e(__('Edit')) . '"><i class="fas fa-pen"></i></button>';
                }
                if ($canDelete && !$isSuper) {
                    $actions .= '<button type="button" class="role-action-btn role-action-btn--delete delete-role-btn" data-id="' . $role->id . '" title="' . e(__('Delete')) . '"><i class="fas fa-trash-alt"></i></button>';
                }
                $actions .= '</div>';

                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'is_super' => $isSuper,
                    'is_system' => $isSystem,
                    'users_count' => (int) $role->users_count,
                    'permissions' => $role->permissions->pluck('name')->values(),
                    'permissions_count' => $role->permissions->count(),
                    'created_at' => $role->created_at ? $role->created_at->format('Y-m-d') : '—',
                    'actions' => $actions,
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
        if ($role->name === 'super-admin') {
            return response()->json([
                'success' => false,
                'message' => __('The super-admin role cannot be deleted.')
            ], 422);
        }

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
