<?php

namespace App\Services\Payment\Strategies;

use App\Models\Order;

class CashOnDeliveryStrategy implements PaymentStrategyInterface
{
    public function pay(Order $order, array $paymentData = []): array
    {
        try {
            // Update order with COD payment method
            $order->update([
                'payment_method' => 'cod',
                'payment_status' => 'pending', // Will be marked as paid by admin on delivery
                'status' => 'processing',
            ]);

            return [
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم تأكيد الطلب. الدفع عند الاستلام'
                    : 'Order confirmed. Cash on delivery',
                'data' => [
                    'payment_method' => 'cod',
                    'note' => 'Please pay the delivery person upon receipt',
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'COD setup failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function getPaymentMethod(): string
    {
        return 'cod';
    }
}
