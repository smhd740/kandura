<?php

namespace App\Listeners;

use App\Events\PaymentSuccessEvent;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class RecordPaymentSuccess
{
    public function handle(PaymentSuccessEvent $event): void
    {
        Log::info('🔥 Listener: RecordPaymentSuccess triggered', [
            'order_id' => $event->orderId,
            'amount' => $event->amount,
            'method' => $event->paymentMethod,
        ]);

        Transaction::create([
            'amount' => $event->amount,
            'type' => 'order',
            'payment_method' => $event->paymentMethod,
            'order_id' => $event->orderId,
            'wallet_id' => null,
            'status' => 'paid',
            'description' => "Payment via {$event->paymentMethod}",
        ]);
    }
}
