<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderShippedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function build(): self
    {
        return $this->subject(__('Your order #:number is on the way', ['number' => $this->order->id]))
            ->markdown('emails.orders.shipped', [
                'order' => $this->order->fresh(['items']),
            ]);
    }
}
