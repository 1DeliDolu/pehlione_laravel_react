<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function build(): self
    {
        return $this->subject(__('Order #:number confirmation', ['number' => $this->order->id]))
            ->markdown('emails.orders.confirmation', [
                'order' => $this->order->fresh(['items', 'user']),
            ]);
    }
}
