<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhook
     * POST /stripe/webhook
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');

        Log::info('Stripe webhook received', [
            'payload' => $payload,
            'signature' => $sig,
        ]);

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig,
                env('STRIPE_WEBHOOK_SECRET')
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe event type: ' . $event->type);

        // Handle the event
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            Log::info('Checkout session completed', [
                'session_id' => $session->id,
                'metadata' => $session->metadata,
            ]);

            // Get order from metadata
            $orderId = $session->metadata->order_id ?? null;

            if ($orderId) {
                $order = Order::find($orderId);

                if ($order) {
                    Log::info('Order found', [
                        'order_id' => $order->id,
                        'current_payment_status' => $order->payment_status,
                    ]);

                    if ($order->payment_status !== 'paid') {
                        // Mark order as paid
                        $order->update([
                            'payment_status' => 'paid',
                            'paid_at' => now(),
                        ]);

                        // Create transaction record
                        Transaction::create([
                            'amount' => $order->total_amount,
                            'type' => 'order',
                            'wallet_id' => null,
                            'order_id' => $order->id,
                            'status' => 'paid',
                            'description' => "Stripe payment for order #{$order->order_number}",
                        ]);

                        Log::info('Stripe payment successful', [
                            'order_id' => $order->id,
                            'session_id' => $session->id,
                        ]);
                    } else {
                        Log::info('Order already paid', [
                            'order_id' => $order->id,
                        ]);
                    }
                } else {
                    Log::error('Order not found', [
                        'order_id' => $orderId,
                    ]);
                }
            } else {
                Log::error('No order_id in metadata');
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
