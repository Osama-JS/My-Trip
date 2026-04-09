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
        $companies = Company::all(); // أو where حسب حاجتك

        return response()->json([
            'data' => $companies->map(function ($company) {
                return [
                    'id'    => $company->id,
                    'logo'  => '<img src="' . $company->logo_url . '" class="rounded-circle" width="35" height="35" alt="">',
                    'name'  => $company->name,
                    'en_name' => $company->en_name,
                    'email' => $company->email,
                    'phone' => ($company->phone_code ? '+'.$company->phone_code.' ' : '') . $company->phone,
                    'notes' => $company->notes,
                    'status' => $company->active
                        ? '<span class="badge bg-success">'.__('Active').'</span>'
                        : '<span class="badge bg-danger">'.__('Inactive').'</span>',
                    'actions' => '
                        <div class="d-flex">
                            <a href="' . route('admin.companies.agents', $company->id) . '" class="btn btn-info btn-xs me-1" title="' . __('Manage Agents') . '">
                                <i class="fas fa-users"></i>
                            </a>
                            <button onclick="editCompany('.$company->id.')" class="btn btn-primary btn-xs me-1">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button onclick="togglecompanytatus('.$company->id.')" class="btn btn-warning btn-xs me-1">
                                <i class="fas fa-ban"></i>
                            </button>
                            <button onclick="deletecompanie('.$company->id.')" class="btn btn-danger btn-xs">
                                <i class="fa fa-trash"></i>
                            </button>
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
