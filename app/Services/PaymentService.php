<?php

namespace App\Services;

use App\Models\Order;
use App\Services\Payment\Strategies\PaymentStrategyInterface;
use App\Services\Payment\Strategies\StripePaymentStrategy;
use App\Services\Payment\Strategies\WalletPaymentStrategy;
use App\Services\Payment\Strategies\CashOnDeliveryStrategy;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $strategies = [];

    public function __construct()
    {
        // Register all payment strategies
        $this->strategies = [
            'stripe' => new StripePaymentStrategy(),
            'wallet' => new WalletPaymentStrategy(),
            'cod' => new CashOnDeliveryStrategy(),
        ];
    }

    /**
     * Process payment for an order
     */
    public function processPayment(Order $order, string $paymentMethod, array $paymentData = []): array
    {
        try {
            // Get the appropriate payment strategy
            $strategy = $this->getStrategy($paymentMethod);

            if (!$strategy) {
                return [
                    'success' => false,
                    'message' => app()->getLocale() === 'ar'
                        ? 'طريقة الدفع غير صالحة'
                        : 'Invalid payment method',
                    'data' => null
                ];
            }

            // Process payment using the selected strategy
            $result = $strategy->pay($order, $paymentData);

            // Log the payment attempt
            Log::info('Payment processed', [
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'success' => $result['success'],
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Get payment strategy by method
     */
    protected function getStrategy(string $paymentMethod): ?PaymentStrategyInterface
    {
        return $this->strategies[$paymentMethod] ?? null;
    }

    /**
     * Get available payment methods
     */
    public function getAvailablePaymentMethods(): array
    {
        return array_keys($this->strategies);
    }

    /**
     * Verify if payment method is valid
     */
    public function isValidPaymentMethod(string $paymentMethod): bool
    {
        return array_key_exists($paymentMethod, $this->strategies);
    }
}
