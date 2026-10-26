<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BankAccountController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view payments|manage payments', only: ['index', 'getData', 'show']),
            new Middleware('permission:manage payments', only: ['store', 'update', 'toggleActive', 'destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stats = [
            'total' => BankAccount::count(),
            'active' => BankAccount::where('is_active', true)->count(),
            'inactive' => BankAccount::where('is_active', false)->count(),
            'disabled' => BankAccount::where('is_active', false)->count(),
        ];
        return view('admin.bank_accounts.index', compact('stats'));
    }

    /**
     * Display the specified bank account with statistics and transfers.
     */
    public function show(BankAccount $bank_account)
    {
        $transfers = $bank_account->transfers()
            ->with(['user', 'booking'])
            ->latest()
            ->get();

        $approvedSum = $transfers->where('status', 'approved')->sum(function($t) {
            return $t->amount ?? ($t->booking->total_price ?? 0);
        });
        $pendingSum = $transfers->where('status', 'pending')->sum(function($t) {
            return $t->amount ?? ($t->booking->total_price ?? 0);
        });

        $stats = [
            'total_count' => $transfers->count(),
            'total_transfers' => $transfers->count(),
            'approved_count' => $transfers->where('status', 'approved')->count(),
            'approved_transfers' => $transfers->where('status', 'approved')->count(),
            'pending_count' => $transfers->where('status', 'pending')->count(),
            'pending_transfers' => $transfers->where('status', 'pending')->count(),
            'total_received' => $approvedSum,
            'total_amount' => $approvedSum,
            'pending_amount' => $pendingSum,
        ];

        return view('admin.bank_accounts.show', compact('bank_account', 'transfers', 'stats'));
    }

    /**
     * Get data for DataTables.
     */
    public function getData()
    {
        try {
            $accounts = BankAccount::all();

            $data = $accounts->map(function ($account) {
                $logoHtml = $account->logo_path 
                    ? '<div class="d-flex align-items-center gap-2"><img src="' . asset('storage/' . $account->logo_path) . '" alt="' . e($account->bank_name) . '" class="rounded" width="36" height="36" style="object-fit: contain; background: #fff; border: 1px solid #e2e8f0; padding: 2px;"> <span>' . e($account->bank_name) . '</span></div>'
                    : '<div class="d-flex align-items-center gap-2"><span class="badge bg-light text-muted p-2"><i class="fa-solid fa-building-columns"></i></span> <span>' . e($account->bank_name) . '</span></div>';

                $statusBadge = $account->is_active
                    ? '<span class="badge bg-success" style="font-weight: 700; padding: 6px 12px; border-radius: 50px;">' . __('Active') . '</span>'
                    : '<span class="badge bg-danger" style="font-weight: 700; padding: 6px 12px; border-radius: 50px;">' . __('Inactive') . '</span>';

                return [
                    'id' => $account->id,
                    'bank_name' => $account->bank_name,
                    'bank_with_logo' => $logoHtml,
                    'beneficiary_name' => $account->beneficiary_name,
                    'account_name' => $account->beneficiary_name,
                    'account_number' => $account->account_number ?? $account->iban,
                    'raw_account_number' => $account->account_number,
                    'iban' => '<code style="font-size: 0.85rem; font-weight: 700; color: #0f172a; background: #f1f5f9; padding: 2px 8px; border-radius: 6px;">' . e($account->iban) . '</code>',
                    'raw_iban' => $account->iban,
                    'swift_code' => $account->swift_code,
                    'currency' => $account->currency ?? 'SAR',
                    'branch' => $account->branch,
                    'logo' => $logoHtml,
                    'is_active' => $statusBadge,
                    'status' => $statusBadge,
                    'actions' => $this->getActionButtons($account),
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Action buttons for DataTables.
     */
    private function getActionButtons($account): string
    {
        $user = auth()->user();
        $canManage = $user && $user->can('manage payments');

        $showBtn = '<a href="' . route('admin.bank-accounts.show', $account->id) . '" class="btn btn-info btn-sm me-1" title="' . __('View Details') . '"><i class="fas fa-eye"></i></a>';
        $editBtn = $canManage ? '<button type="button" class="btn btn-primary btn-sm me-1" onclick="editAccount(' . $account->id . ')" title="' . __('Edit') . '"><i class="fas fa-edit"></i></button>' : '';
        $toggleBtn = $canManage ? '<button type="button" class="btn btn-' . ($account->is_active ? 'warning' : 'success') . ' btn-sm me-1" onclick="toggleAccountStatus(' . $account->id . ')" title="' . ($account->is_active ? __('Deactivate') : __('Activate')) . '"><i class="fas fa-' . ($account->is_active ? 'ban' : 'check') . '"></i></button>' : '';
        $deleteBtn = $canManage ? '<button type="button" class="btn btn-danger btn-sm" onclick="deleteAccount(' . $account->id . ')" title="' . __('Delete') . '"><i class="fas fa-trash"></i></button>' : '';

        return '<div class="d-flex justify-content-center align-items-center">' . $showBtn . $editBtn . $toggleBtn . $deleteBtn . '</div>';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'iban' => 'required|string|max:255',
            'beneficiary_name' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:20',
            'branch' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors(), 'message' => $validator->errors()->first()], 422);
        }

        $beneficiary = $request->input('beneficiary_name') ?: $request->input('account_name');
        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => __('Beneficiary name is required')], 422);
        }

        $data = [
            'bank_name' => $request->input('bank_name'),
            'iban' => $request->input('iban'),
            'beneficiary_name' => $beneficiary,
            'account_number' => $request->input('account_number'),
            'swift_code' => $request->input('swift_code'),
            'currency' => $request->input('currency', 'SAR') ?: 'SAR',
            'branch' => $request->input('branch'),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('banks', 'public');
        }

        BankAccount::create($data);

        return response()->json([
            'success' => true,
            'message' => __('Bank account created successfully'),
        ]);
    }

    /**
     * Show data for editing.
     */
    public function edit(BankAccount $bank_account)
    {
        return response()->json([
            'success' => true,
            'account' => $bank_account,
            'logo_url' => $bank_account->logo_path ? asset('storage/' . $bank_account->logo_path) : null,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bank_account)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'iban' => 'required|string|max:255',
            'beneficiary_name' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:20',
            'branch' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors(), 'message' => $validator->errors()->first()], 422);
        }

        $beneficiary = $request->input('beneficiary_name') ?: $request->input('account_name');
        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => __('Beneficiary name is required')], 422);
        }

        $data = [
            'bank_name' => $request->input('bank_name'),
            'iban' => $request->input('iban'),
            'beneficiary_name' => $beneficiary,
            'account_number' => $request->input('account_number'),
            'swift_code' => $request->input('swift_code'),
            'currency' => $request->input('currency', 'SAR') ?: 'SAR',
            'branch' => $request->input('branch'),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('logo')) {
            if ($bank_account->logo_path) {
                Storage::disk('public')->delete($bank_account->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('banks', 'public');
        }

        $bank_account->update($data);

        return response()->json([
            'success' => true,
            'message' => __('Bank account updated successfully'),
        ]);
    }

    /**
     * Toggle status.
     */
    public function toggleActive($id)
    {
        $account = BankAccount::findOrFail($id);
        $account->update(['is_active' => !$account->is_active]);

        return response()->json([
            'success' => true,
            'message' => $account->is_active ? __('Bank account activated') : __('Bank account deactivated'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bank_account)
    {
        if ($bank_account->logo_path) {
            Storage::disk('public')->delete($bank_account->logo_path);
        }
        $bank_account->delete();

        return response()->json([
            'success' => true,
            'message' => __('Bank account deleted successfully'),
        ]);
    }
}
