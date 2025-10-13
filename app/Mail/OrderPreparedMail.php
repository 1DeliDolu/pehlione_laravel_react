<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPreparedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function build(): self
    {
        return $this->subject(__('Your order #:number is ready for shipment', ['number' => $this->order->id]))
            ->markdown('emails.orders.prepared', [
                'order' => $this->order->fresh(['items']),
            ]);
    }
}
