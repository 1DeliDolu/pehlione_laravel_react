<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WarehouseOrderAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function build(): self
    {
        return $this->subject(__('New order #:number awaiting fulfilment', ['number' => $this->order->id]))
            ->markdown('emails.orders.warehouse-alert', [
                'order' => $this->order->fresh(['items', 'user']),
            ]);
    }
}

