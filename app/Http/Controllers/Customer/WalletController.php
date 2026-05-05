<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function index()
    {
        $user = auth()->user();
        $wallet = $this->walletService->getOrCreateWallet($user->id);
        
        $transactions = $wallet->transactions()->orderBy('created_at', 'desc')->paginate(15);

        return view('frontend.customer.wallet.index', compact('wallet', 'transactions'));
    }
}
