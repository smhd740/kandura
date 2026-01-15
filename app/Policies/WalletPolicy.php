<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wallet;

class WalletPolicy
{
    /**
     * Determine if the user can view the wallet
     */
    public function view(User $user, Wallet $wallet): bool
    {
        // User can only view their own wallet
        return $user->id === $wallet->user_id;
    }

    /**
     * Determine if the user can view wallet transactions
     */
    public function viewTransactions(User $user, Wallet $wallet): bool
    {
        return $user->id === $wallet->user_id;
    }

    /**
     * Determine if admin can manage any wallet
     */
    public function manageAny(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Determine if admin can add balance
     */
    public function addBalance(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Determine if admin can deduct balance
     */
    public function deductBalance(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }
}
