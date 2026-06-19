<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stats = [
            'total' => Company::count(),
            'active' => Company::where('active', true)->count(),
            'inactive' => Company::where('active', false)->count(),
        ];
        return view('admin.companies.index', compact('stats'));
    }


    public function getData(Request $request)
    {
        $companies = Company::all();

        return response()->json([
            'data' => $companies->map(function ($company) {
                $statusBadge = '
                <div class="form-check form-switch d-inline-flex align-items-center p-0 m-0">
                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="status_switch_' . $company->id . '" ' . ($company->active ? 'checked' : '') . ' onclick="togglecompanytatus(' . $company->id . ')" style="width: 36px; height: 18px; cursor: pointer;">
                    <label class="form-check-label fw-medium text-dark small cursor-pointer" for="status_switch_' . $company->id . '">' . ($company->active ? __('Active') : __('Inactive')) . '</label>
                </div>';

                return [
                    'id'    => $company->id,
                    'logo'  => '<img src="' . $company->logo_url . '" class="rounded-circle shadow-sm border border-2 border-white" style="width: 40px; height: 40px; object-fit: cover;" alt="">',
                    'info'  => '<div>
                                <strong class="text-dark">' . $company->name . '</strong><br>
                                <span class="text-muted small">' . $company->en_name . '</span>
                               </div>',
                    'contact' => '<div>
                                    <span class="text-dark fw-medium">' . $company->email . '</span><br>
                                    <small class="text-muted">' . ($company->phone_code ? '+'.$company->phone_code.' ' : '') . ($company->phone ?? '---') . '</small>
                                  </div>',
                    'notes' => $company->notes ? '<span class="text-muted small">' . \Illuminate\Support\Str::limit($company->notes, 60) . '</span>' : '<span class="text-muted">---</span>',
                    'status' => $statusBadge,
                    'actions' => '
                        <div class="dropdown">
                            <button type="button" class="btn btn-white btn-sm rounded-circle border shadow-sm" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false" style="width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; background-color:#ffffff !important; border-color:#e2e8f0 !important;">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 py-2" style="z-index: 1060;">
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="viewCompany(' . $company->id . ')"><i class="fa fa-eye text-info me-3 w-15px"></i> '.__('View').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="' . route('admin.companies.agents', $company->id) . '"><i class="fas fa-users text-secondary me-3 w-15px"></i> '.__('Manage Agents').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="editCompany('.$company->id.')"><i class="fas fa-pencil-alt text-primary me-3 w-15px"></i> '.__('Edit').'</a>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="togglecompanytatus('.$company->id.')"><i class="fas fa-ban text-warning me-3 w-15px"></i> '.__('Toggle Status').'</a>
                                <div class="dropdown-divider my-1"></div>
                                <a class="dropdown-item text-danger py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="deletecompanie('.$company->id.')"><i class="fa fa-trash text-danger me-3 w-15px"></i> '.__('Delete').'</a>
                            </div>
                        </div>'
                ];
            })
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->merge([
            'active' => $request->boolean('active'), 
        ]); 
        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'en_name' => 'nullable|string|max:100',
            'logo'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email'  => 'required|email|',
            'phone'  => 'nullable|string|max:100',
            'phone_code' => 'nullable|string|max:10',
            'notes'  => 'nullable|string',
            'active' => 'sometimes|boolean',
            'bank_name' => 'nullable|string|max:255',
            'beneficiary_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'iban_number' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        Company::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Company created successfully'
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
         return response()->json([
            'success' => true,
            'Company' => $company,
            'logo_url' => $company->logo_url
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
         $request->merge([
             'active' => $request->boolean('active'),
         ]);
       $validated = $request->validate([
            'name' => 'required|string|max:100',
            'en_name' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email' => 'nullable|email|',
            'phone' => 'nullable|',
            'phone_code' => 'nullable|string|max:10',
            'notes' => 'nullable',
            'active' => 'sometimes|boolean',
            'bank_name' => 'nullable|string|max:255',
            'beneficiary_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'iban_number' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                \Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        $data = $request->only(['name','en_name', 'email', 'phone','phone_code', 'notes', 'bank_name', 'beneficiary_name', 'account_number', 'iban_number']);
        $data['active'] = $request->boolean('active');
        $company->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Company updated successfully'
        ]);
    }

     /**
     * Toggle user status.
     */
    public function toggleStatus(Company $company)
    {
        $company->update(['active' => !$company->active]);

        return response()->json([
            'success' => true,
            'message' => $company->active ? __('company activated') : __('company deactivated'),
        ]);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
       try {

            $company->delete();
            return response()->json([
                'success' => true,
                'message' => 'Company deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Company'
            ], 500);
        }
    }




}
