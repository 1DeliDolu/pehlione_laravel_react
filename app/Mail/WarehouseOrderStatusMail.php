<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WarehouseOrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $status,
        public ?string $statusMessage = null
    ) {
    }

    public function build(): self
    {
        $subject = match ($this->status) {
            'prepared' => __('Order #:number prepared for shipment', ['number' => $this->order->id]),
            'shipped' => __('Order #:number dispatched to carrier', ['number' => $this->order->id]),
            default => __('Order #:number update', ['number' => $this->order->id]),
        };

        return $this->subject($subject)->markdown('emails.orders.warehouse-status', [
            'order' => $this->order->fresh(['items', 'user']),
            'status' => $this->status,
            'statusMessage' => $this->statusMessage,
        ]);
    }
}
