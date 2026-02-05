<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Events\PaymentSuccessEvent;
use App\Events\PaymentFailedEvent;

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
            Log::error('Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe event type: ' . $event->type);

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            Log::info('Checkout session completed', [
                'session_id' => $session->id,
                'metadata' => $session->metadata,
            ]);

            $orderId = $session->metadata->order_id ?? null;

            if (!$orderId) {
                Log::error('No order_id in metadata');
                return response()->json(['error' => 'No order_id'], 400);
            }

            // ✅ منع التكرار باستخدام Cache Lock
            $lockKey = "stripe_webhook_{$session->id}";

            if (Cache::has($lockKey)) {
                Log::info('Duplicate webhook detected - skipping', [
                    'session_id' => $session->id,
                    'order_id' => $orderId,
                ]);
                return response()->json(['status' => 'duplicate_ignored']);
            }

            // قفل لمدة 10 دقائق
            Cache::put($lockKey, true, 600);

            $order = Order::find($orderId);

            if (!$order) {
                Log::error('Order not found', ['order_id' => $orderId]);

                event(new PaymentFailedEvent(
                    $orderId,
                    0,
                    'stripe',
                    'Order not found in webhook'
                ));

                return response()->json(['error' => 'Order not found'], 404);
            }

            Log::info('Order found', [
                'order_id' => $order->id,
                'current_payment_status' => $order->payment_status,
            ]);

            // ✅ التحقق: هل الأوردر مدفوع مسبقاً؟
            if ($order->payment_status === 'paid') {
                Log::info('Order already paid - skipping', [
                    'order_id' => $order->id,
                ]);
                return response()->json(['status' => 'already_paid']);
            }

            // ✅ التحقق: هل في transaction موجودة مسبقاً؟
            $existingTransaction = Transaction::where('order_id', $order->id)
                ->where('type', 'order')
                ->where('payment_method', 'stripe')
                ->where('status', 'paid')
                ->exists();

            if ($existingTransaction) {
                Log::info('Transaction already exists - skipping', [
                    'order_id' => $order->id,
                ]);
                return response()->json(['status' => 'transaction_exists']);
            }

            // تحديث حالة الأوردر
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
                'paid_at' => now(),
                'status' => 'processing',
            ]);

            $wallet = $order->user->getOrCreateWallet();

            // إنشاء transaction جديدة
            Transaction::create([
                'user_id' => $order->user_id,
                'wallet_id' => $wallet->id,
                'order_id' => $order->id,
                'amount' => -abs($order->total_amount),
                'type' => 'order',
                'payment_method' => 'stripe',
                'status' => 'paid',
                'description' => "Stripe payment for order #{$order->order_number}",
            ]);

            event(new PaymentSuccessEvent(
                $order->id,
                (float) $order->total_amount,
                'stripe'
            ));

            Log::info('Stripe payment successful', [
                'order_id' => $order->id,
                'session_id' => $session->id,
            ]);

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'ok']);
    }
}
