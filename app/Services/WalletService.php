<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletService
{
    /**
     * Get wallet for user (create if not exists)
     */
    public function getOrCreateWallet(User $user): Wallet
    {
        return $user->getOrCreateWallet();
    }

    /**
     * Add balance to wallet (Admin action)
     */
    public function addBalance(Wallet $wallet, float $amount, ?string $description = null): array
    {
        DB::beginTransaction();

        try {
            // Add balance to wallet
            $wallet->addBalance($amount);

            // Create transaction record
            Transaction::create([
                'amount' => $amount,
                'type' => 'wallet',
                'wallet_id' => $wallet->id,
                'order_id' => null,
                'status' => 'complete',
                'description' => $description ?? 'Balance added by admin',
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم إضافة الرصيد بنجاح'
                    : 'Balance added successfully',
                'data' => [
                    'new_balance' => $wallet->fresh()->amount,
                    'added_amount' => $amount,
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add balance failed', [
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to add balance: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Deduct balance from wallet (Admin action)
     */
    public function deductBalance(Wallet $wallet, float $amount, ?string $description = null): array
    {
        DB::beginTransaction();

        try {
            // Check if wallet has sufficient balance
            if (!$wallet->hasSufficientBalance($amount)) {
                return [
                    'success' => false,
                    'message' => app()->getLocale() === 'ar'
                        ? 'الرصيد غير كافٍ'
                        : 'Insufficient balance',
                    'data' => [
                        'current_balance' => $wallet->amount,
                        'required_amount' => $amount,
                        'shortage' => $amount - $wallet->amount,
                    ]
                ];
            }

            // Deduct balance from wallet
            $wallet->deductBalance($amount);

            // Create transaction record
            Transaction::create([
                'amount' => $amount,
                'type' => 'wallet',
                'wallet_id' => $wallet->id,
                'order_id' => null,
                'status' => 'complete',
                'description' => $description ?? 'Balance deducted by admin',
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم خصم الرصيد بنجاح'
                    : 'Balance deducted successfully',
                'data' => [
                    'new_balance' => $wallet->fresh()->amount,
                    'deducted_amount' => $amount,
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Deduct balance failed', [
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to deduct balance: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Get transactions for wallet with filters
     */
    public function getTransactions(Wallet $wallet, array $filters = [])
    {
        $query = Transaction::where('wallet_id', $wallet->id)
            ->with(['order']);

        // Filter by type
        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    /**
     * Get wallet balance
     */
    public function getBalance(Wallet $wallet): float
    {
        return (float)$wallet->amount;
    }

    /**
     * Refund to wallet (when order is cancelled)
     */
    public function refundToWallet(Wallet $wallet, float $amount, int $orderId, ?string $description = null): array
    {
        DB::beginTransaction();

        try {
            // Add balance back to wallet
            $wallet->addBalance($amount);

            // Create refund transaction
            Transaction::create([
                'amount' => $amount,
                'type' => 'order',
                'wallet_id' => $wallet->id,
                'order_id' => $orderId,
                'status' => 'complete',
                'description' => $description ?? 'Refund for cancelled order',
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم استرجاع المبلغ إلى المحفظة'
                    : 'Amount refunded to wallet',
                'data' => [
                    'new_balance' => $wallet->fresh()->amount,
                    'refunded_amount' => $amount,
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund failed', [
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Refund failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
