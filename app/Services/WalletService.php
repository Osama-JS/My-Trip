<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletService
{
    /**
     * Credit (Add) money to the wallet.
     *
     * @param int $walletId
     * @param float $amount
     * @param string $description
     * @param string|null $referenceType
     * @param int|null $referenceId
     * @return WalletTransaction
     * @throws Exception
     */
    public function credit(int $walletId, float $amount, string $description = '', ?string $referenceType = null, ?int $referenceId = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new Exception("Credit amount must be greater than zero.");
        }

        return DB::transaction(function () use ($walletId, $amount, $description, $referenceType, $referenceId) {
            // Lock the wallet row for update
            $wallet = Wallet::where('id', $walletId)->lockForUpdate()->firstOrFail();

            if ($wallet->status !== 'active') {
                throw new Exception("Cannot credit a non-active wallet.");
            }

            $balanceBefore = $wallet->balance;
            $balanceAfter = $balanceBefore + $amount;

            // Create Transaction Record
            $transaction = WalletTransaction::create([
                'wallet_id'      => $wallet->id,
                'type'           => 'credit',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => $description,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
            ]);

            // Update Wallet Balance
            $wallet->balance = $balanceAfter;
            $wallet->save();

            return $transaction;
        });
    }

    /**
     * Debit (Deduct) money from the wallet.
     *
     * @param int $walletId
     * @param float $amount
     * @param string $description
     * @param string|null $referenceType
     * @param int|null $referenceId
     * @return WalletTransaction
     * @throws Exception
     */
    public function debit(int $walletId, float $amount, string $description = '', ?string $referenceType = null, ?int $referenceId = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new Exception("Debit amount must be greater than zero.");
        }

        return DB::transaction(function () use ($walletId, $amount, $description, $referenceType, $referenceId) {
            // Lock the wallet row for update
            $wallet = Wallet::where('id', $walletId)->lockForUpdate()->firstOrFail();

            if ($wallet->status !== 'active') {
                throw new Exception("Cannot debit a non-active wallet.");
            }

            if ($wallet->balance < $amount) {
                throw new Exception("Insufficient funds.");
            }

            $balanceBefore = $wallet->balance;
            $balanceAfter = $balanceBefore - $amount;

            // Create Transaction Record
            $transaction = WalletTransaction::create([
                'wallet_id'      => $wallet->id,
                'type'           => 'debit',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => $description,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
            ]);

            // Update Wallet Balance
            $wallet->balance = $balanceAfter;
            $wallet->save();

            return $transaction;
        });
    }

    /**
     * Get or create a wallet for a user.
     * 
     * @param int $userId
     * @return Wallet
     */
    public function getOrCreateWallet(int $userId): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $userId],
            [
                'balance'  => 0.0000,
                'currency' => 'SAR',
                'status'   => 'active'
            ]
        );
    }
}
