<?php

namespace App\Services\Payment\Strategies;

use App\Models\Order;

interface PaymentStrategyInterface
{
    /**
     * Process payment for an order
     *
     * @param Order $order
     * @param array $paymentData
     * @return array ['success' => bool, 'message' => string, 'data' => mixed]
     */
    public function pay(Order $order, array $paymentData = []): array;

    /**
     * Get payment method name
     *
     * @return string
     */
    public function getPaymentMethod(): string;
}
