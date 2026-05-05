<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function index(Request $request)
    {
        $query = Wallet::with('user');
        
        if ($request->has('search') && $request->search != '') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $wallets = $query->paginate(15);
        return view('admin.wallets.index', compact('wallets'));
    }

    public function show($id)
    {
        $wallet = Wallet::with(['user', 'transactions' => function($q) {
            $q->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        return view('admin.wallets.show', compact('wallet'));
    }

    public function addTransaction(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        $wallet = Wallet::findOrFail($id);

        try {
            if ($request->type == 'credit') {
                $this->walletService->credit($wallet->id, $request->amount, $request->description, 'Admin Action', auth()->id());
            } else {
                $this->walletService->debit($wallet->id, $request->amount, $request->description, 'Admin Action', auth()->id());
            }

            return back()->with('success', __('Transaction added successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
