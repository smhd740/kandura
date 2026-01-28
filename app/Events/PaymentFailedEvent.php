<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailedEvent
{
    use Dispatchable, SerializesModels;

    public $orderId;
    public $amount;
    public $paymentMethod;
    public $errorMessage;

    public function __construct(int $orderId, float $amount, string $paymentMethod, string $errorMessage)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
        $this->errorMessage = $errorMessage;
    }
}
