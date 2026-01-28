<?php

namespace App\Services\Payment\Strategies;

use App\Services\Payment\Strategies\PaymentStrategyInterface;
use Stripe\Stripe;
use App\Models\Order;
use Stripe\Checkout\Session as StripeSession;

class StripePaymentStrategy implements PaymentStrategyInterface
{
    public function pay(Order $order, array $paymentData = []): array
    {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $session = StripeSession::create([
                'customer_email' => $order->user->email,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => "Order #{$order->order_number}",
                            'description' => "Payment for Kandura Store Order",
                        ],
                        'unit_amount' => $order->total_amount * 100, // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => url('/payment/success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => url('/payment/cancel'),
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                ]
            ]);

            // حفظ معلومات الدفع في الطلب (لكن بدون اعتباره مدفوع)
            $order->update([
                'payment_method' => 'stripe',
                'stripe_payment_intent_id' => $session->id,
            ]);

            return [
                'success' => true,
                'message' => 'Stripe session created successfully',
                'data' => [
                    'session_url' => $session->url,
                    'session_id' => $session->id,
                ]
            ];

        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => 'Stripe payment failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function getPaymentMethod(): string
    {
        return 'stripe';
    }
}
