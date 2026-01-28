<?php

namespace App\Listeners;

use App\Events\PaymentFailedEvent;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class RecordPaymentFailed
{
    public function handle(PaymentFailedEvent $event): void
    {
        Log::info('🔥 Listener: RecordPaymentFailed triggered', [
            'order_id' => $event->orderId,
            'amount' => $event->amount,
            'method' => $event->paymentMethod,
            'error' => $event->errorMessage,
        ]);

        Transaction::create([
            'amount' => $event->amount,
            'type' => 'order',
            'payment_method' => $event->paymentMethod,
            'order_id' => $event->orderId,
            'wallet_id' => null,
            'status' => 'cancel',
            'description' => "Payment failed: {$event->errorMessage}",
        ]);
    }
}
