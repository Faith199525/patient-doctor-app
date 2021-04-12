<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentMade
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payment;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($payment)
    {
       $this->payment = $payment;
    }
}
