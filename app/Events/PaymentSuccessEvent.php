<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessEvent
{
    use Dispatchable, SerializesModels;

    public $orderId;
    public $amount;
    public $paymentMethod;

    public function __construct(int $orderId, float $amount, string $paymentMethod)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
    }
}
