<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyCodes;
use App\Models\Company;


class CompanyCodesController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::select('id', 'name')->get();
        $stats = [
            'total' => CompanyCodes::count(),
            'active' => CompanyCodes::where('active', true)->count(),
            'inactive' => CompanyCodes::where('active', false)->count(),
            'companies_count' => Company::has('company_codes')->count(),
        ];
        return view('admin.companycodes.index', compact('companies', 'stats'));
    }

    public function getData()
    {
         $codes = CompanyCodes::with('company')->get();

        return response()->json([
            'data' => $codes->map(function ($code) {
                return [
                    'company' => $code->company->name ?? '-',
                    'code'    => $code->code,
                    'type'    => ucfirst($code->type),
                    'value'   => $code->type === 'percentage'
                        ? $code->value.' %'
                        : $code->value,
                    'status'  => $code->active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>',
                    'actions' => '
                        <button class="btn btn-sm btn-primary" onclick="editCode('.$code->id.')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="toggleCodeStatus('.$code->id.')">
                            <i class="fas fa-ban"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCode('.$code->id.')">
                            <i class="fas fa-trash"></i>
                        </button>
                    ',
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
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code'       => 'required|string|unique:company_codes,code',
            'type'       => 'required|in:fixed,percentage',
            'value'      => 'required|numeric|min:0',
            'active'     => 'boolean',
        ]);

        $data['active'] = true;

        CompanyCodes::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Code created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(CompanyCodes $companycode)
    {
        return response()->json([
            'success' => true,
            'CompanyCodes' => $companycode,
        ]);


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CompanyCodes $companycode)
    {
         $request->merge([
             'active' => $request->boolean('active'),
         ]);
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code'       => 'required|string|unique:company_codes,code,' . $companycode->id,
            'type'       => 'required|in:fixed,percentage',
            'value'      => 'required|numeric|min:0',
            'active'     => 'boolean',
        ]);
        
        $data['active'] = $request->boolean('active');

        $companycode->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Code updated successfully'
        ]);
    }

    public function toggleStatus(CompanyCodes $companycode)
    {
        $companycode->active = !$companycode->active;
        $companycode->save();

        return response()->json([
            'success' => true,
            'message' => 'Code status updated successfully',
            'status'  => $companycode->active ? 'Active' : 'Inactive'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompanyCodes $companycode)
    {
        $companycode->delete();

        return response()->json([
            'success' => true,
            'message' => 'Code deleted successfully'
        ]);
    }


}
