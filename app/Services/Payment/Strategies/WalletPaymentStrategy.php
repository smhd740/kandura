<?php

namespace App\Services\Payment\Strategies;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class WalletPaymentStrategy implements PaymentStrategyInterface
{
    public function pay(Order $order, array $paymentData = []): array
    {
        DB::beginTransaction();

        try {
            $wallet = $order->user->getOrCreateWallet();

            // Check if wallet has sufficient balance
            if (!$wallet->hasSufficientBalance((float)$order->total_amount)) {
                return [
                    'success' => false,
                    'message' => app()->getLocale() === 'ar'
                        ? 'رصيد المحفظة غير كافٍ'
                        : 'Insufficient wallet balance',
                    'data' => [
                        'required' => $order->total_amount,
                        'available' => $wallet->amount,
                        'shortage' => $order->total_amount - $wallet->amount,
                    ]
                ];
            }

            // Deduct amount from wallet
            $wallet->deductBalance((float)$order->total_amount);

            // Create transaction record
            Transaction::create([
                'amount' => $order->total_amount,
                'type' => 'order',
                'wallet_id' => $wallet->id,
                'order_id' => $order->id,
                'status' => 'paid',
                'description' => "Payment for order #{$order->order_number}",
            ]);

            // Update order
            $order->update([
                'payment_method' => 'wallet',
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم الدفع من المحفظة بنجاح'
                    : 'Payment from wallet successful',
                'data' => [
                    'remaining_balance' => $wallet->fresh()->amount,
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Wallet payment failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function getPaymentMethod(): string
    {
        return 'wallet';
    }
}
