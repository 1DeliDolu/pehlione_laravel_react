<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Mail\OrderShippedMail;
use App\Models\Order;
use App\Services\Mail\LoggedMailSender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderShipmentController extends Controller
{
    public function __construct(private readonly LoggedMailSender $mailSender)
    {
    }

    public function __invoke(Request $request, Order $order): RedirectResponse
    {
        $user = $request->user();

        if (! in_array($user->role, [Role::ADMIN, Role::LAGER], true)) {
            abort(403);
        }

        $validated = $request->validate([
            'tracking_number' => ['nullable', 'string', 'max:190'],
        ]);

        if ($order->status === Order::STATUS_SHIPPED) {
            return back()->with('info', __('Order already marked as shipped.'));
        }

        DB::transaction(function () use ($order, $validated) {
            $order->forceFill([
                'status' => Order::STATUS_SHIPPED,
                'shipped_at' => now(),
                'tracking_number' => $validated['tracking_number'] ?? null,
            ])->save();

            $order->warehouseNotifications()
                ->where('status', 'pending')
                ->update(['status' => 'completed']);
        });

        DB::afterCommit(function () use ($order) {
            $this->mailSender->send(
                new OrderShippedMail($order),
                $order->user->email,
                $order->user->name,
                [
                    'subject' => __('Your order #:number is on the way', ['number' => $order->id]),
                    'context' => [
                        'order_id' => $order->id,
                        'type' => 'shipment_update',
                    ],
                    'related_type' => $order::class,
                    'related_id' => $order->id,
                ],
            );
        });

        return back()->with('success', __('Order marked as shipped and customer notified.'));
    }
}
