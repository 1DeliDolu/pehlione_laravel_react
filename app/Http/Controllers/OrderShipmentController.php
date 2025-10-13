<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Mail\OrderShippedMail;
use App\Mail\WarehouseOrderStatusMail;
use App\Models\Order;
use App\Services\Mail\LoggedMailSender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            'delivery_estimate_at' => ['nullable', 'date'],
        ]);

        if ($order->status === Order::STATUS_SHIPPED) {
            return back()->with('info', __('Order already marked as shipped.'));
        }

        $warehouseEmail = config('checkout.notifications.warehouse_email');

        DB::transaction(function () use ($order, $validated) {
            $order->forceFill([
                'status' => Order::STATUS_SHIPPED,
                'shipped_at' => now(),
                'tracking_number' => $validated['tracking_number'] ?? null,
                'delivery_estimate_at' => isset($validated['delivery_estimate_at'])
                    ? Carbon::parse($validated['delivery_estimate_at'])
                    : null,
            ])->save();

            $order->warehouseNotifications()
                ->where('status', 'pending')
                ->update(['status' => 'completed']);
        });

        DB::afterCommit(function () use ($order, $warehouseEmail) {
            $order->refresh();

            $shippedAtIso = $order->shipped_at?->toIso8601String() ?? now()->toIso8601String();
            $approvedAtIso = $order->prepared_at?->toIso8601String() ?? $shippedAtIso;
            $estimatedDeliveryIso = $order->delivery_estimate_at?->toIso8601String();
            $packageStatus = [
                'approved' => true,
                'approved_at' => $approvedAtIso,
                'dispatched' => true,
                'dispatched_at' => $shippedAtIso,
            ];

            $statusMessage = __('Your package has been approved and handed over to the carrier.');

            if ($estimatedDeliveryIso) {
                $statusMessage = __(
                    'Your package is on the way and is scheduled for delivery on :date.',
                    ['date' => $order->delivery_estimate_at?->timezone(config('app.timezone'))->format('d.m.Y H:i')]
                );
            }

            $warehouseStatusMessage = $estimatedDeliveryIso
                ? __('Order dispatched with an expected delivery on :date.', [
                    'date' => $order->delivery_estimate_at?->timezone(config('app.timezone'))->format('d.m.Y H:i'),
                ])
                : __('Order dispatched to the carrier. Customer has received tracking details.');

            $this->mailSender->send(
                new OrderShippedMail($order),
                $order->user->email,
                $order->user->name,
                [
                    'subject' => __('Your order #:number is on the way', ['number' => $order->id]),
                    'context' => [
                        'order_id' => $order->id,
                        'type' => 'shipment_update',
                        'package_status' => $packageStatus,
                        'tracking_number' => $order->tracking_number,
                        'status_message' => $statusMessage,
                        'estimated_delivery_at' => $estimatedDeliveryIso,
                    ],
                    'related_type' => $order::class,
                    'related_id' => $order->id,
                ],
            );

            if (! empty($warehouseEmail)) {
                $this->mailSender->send(
                    new WarehouseOrderStatusMail($order, 'shipped', $warehouseStatusMessage),
                    $warehouseEmail,
                    null,
                    [
                        'subject' => __('Order #:number dispatched to carrier', ['number' => $order->id]),
                        'context' => [
                            'order_id' => $order->id,
                            'type' => 'warehouse_update',
                            'status' => 'shipped',
                            'status_message' => $warehouseStatusMessage,
                            'tracking_number' => $order->tracking_number,
                            'estimated_delivery_at' => $estimatedDeliveryIso,
                        ],
                        'related_type' => $order::class,
                        'related_id' => $order->id,
                    ],
                );
            }
        });

        return back()->with('success', __('Order marked as shipped and customer notified.'));
    }
}
