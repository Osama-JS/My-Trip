<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyAgentController extends Controller
{
    public function index(Company $company)
    {
        $countries = \App\Models\Country::active()->get();
        return view('admin.companies.agents', compact('company', 'countries'));
    }

    public function getData(Company $company)
    {
        $agents = $company->agents;

        return response()->json([
            'data' => $agents->map(function ($agent) {
                $statusBadge = $agent->status === 'active'
                    ? '<div class="d-flex align-items-center"><i class="fa fa-circle text-success me-2" style="font-size: 8px;"></i> <span class="fw-medium text-dark">'.__('Active').'</span></div>'
                    : '<div class="d-flex align-items-center"><i class="fa fa-circle text-danger me-2" style="font-size: 8px;"></i> <span class="fw-medium text-dark">'.__('Inactive').'</span></div>';

                return [
                    'id' => $agent->id,
                    'name' => '<strong class="text-dark">' . $agent->first_name . ' ' . $agent->last_name . '</strong>',
                    'phone' => '<span class="text-muted" dir="ltr">+' . $agent->country_code . ' ' . $agent->phone . '</span>',
                    'email' => '<span class="text-muted">' . $agent->email . '</span>',
                    'status' => $statusBadge,
                    'actions' => '
                        <div class="dropdown">
                            <button type="button" class="btn btn-light btn-sm rounded-circle border-0 shadow-sm" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 py-2">
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="editAgent(' . $agent->id . ')"><i class="fas fa-pencil-alt text-primary me-3 w-15px"></i> '.__('Edit').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="toggleAgentStatus(' . $agent->id . ')"><i class="fas fa-ban text-warning me-3 w-15px"></i> '.__('Toggle Status').'</a>
                                <div class="dropdown-divider my-1"></div>
                                <a class="dropdown-item text-danger py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="deleteAgent(' . $agent->id . ')"><i class="fa fa-trash text-danger me-3 w-15px"></i> '.__('Delete').'</a>
                            </div>
                        </div>'
                ];
            })
        ]);
    }

    public function store(Request $request, Company $company)
    {
        $validated = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'country_code' => 'required|string|max:10',
            'phone'        => 'required|string|max:20',
            'password'     => 'required|string|min:8|confirmed',
        ]);

        $agent = User::create([
            'first_name'   => $validated['first_name'],
            'last_name'    => $validated['last_name'],
            'email'         => $validated['email'],
            'country_code' => $validated['country_code'],
            'phone'        => $validated['phone'],
            'password'     => Hash::make($validated['password']),
            'user_type'    => User::TYPE_AGENT,
            'company_id'   => $company->id,
            'status'       => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Agent added successfully')
        ]);
    }

    public function edit(User $user)
    {
        if ($user->user_type !== User::TYPE_AGENT) {
            return response()->json(['success' => false, 'message' => 'Invalid user type'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        if ($user->user_type !== User::TYPE_AGENT) {
            return response()->json(['success' => false, 'message' => 'Invalid user type'], 403);
        }

        $validated = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email,'.$user->id,
            'country_code' => 'required|string|max:10',
            'phone'        => 'required|string|max:20',
            'password'     => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'first_name'   => $validated['first_name'],
            'last_name'    => $validated['last_name'],
            'email'         => $validated['email'],
            'country_code' => $validated['country_code'],
            'phone'        => $validated['phone'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => __('Agent updated successfully')
        ]);
    }

    public function toggleStatus(User $user)
    {
        if ($user->user_type !== User::TYPE_AGENT) {
            return response()->json(['success' => false, 'message' => 'Invalid user type'], 403);
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => __('Agent status updated successfully')
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->user_type !== User::TYPE_AGENT) {
            return response()->json(['success' => false, 'message' => 'Invalid user type'], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => __('Agent deleted successfully')
        ]);
    }
}
