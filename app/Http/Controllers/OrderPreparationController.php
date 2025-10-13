<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Mail\OrderPreparedMail;
use App\Mail\WarehouseOrderStatusMail;
use App\Models\Order;
use App\Services\Mail\LoggedMailSender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderPreparationController extends Controller
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

        if ($order->status === Order::STATUS_READY_TO_SHIP) {
            return back()->with('info', __('Order already marked as prepared.'));
        }

        if ($order->status === Order::STATUS_SHIPPED) {
            return back()->with('info', __('Order already shipped.'));
        }

        DB::transaction(function () use ($order) {
            $order->forceFill([
                'status' => Order::STATUS_READY_TO_SHIP,
                'prepared_at' => now(),
            ])->save();

            $order->warehouseNotifications()
                ->where('status', 'pending')
                ->update(['status' => 'in_progress']);
        });

        $warehouseEmail = config('checkout.notifications.warehouse_email');

        DB::afterCommit(function () use ($order, $warehouseEmail) {
            $order->refresh();
            $preparedAtIso = $order->prepared_at?->toIso8601String() ?? now()->toIso8601String();
            $statusMessage = __('The warehouse confirmed the package is ready for pickup.');

            $this->mailSender->send(
                new OrderPreparedMail($order),
                $order->user->email,
                $order->user->name,
                [
                    'subject' => __('Your order #:number is ready for shipment', ['number' => $order->id]),
                    'context' => [
                        'order_id' => $order->id,
                        'type' => 'preparation_update',
                        'package_status' => [
                            'approved' => true,
                            'approved_at' => $preparedAtIso,
                            'dispatched' => false,
                            'dispatched_at' => null,
                        ],
                        'status_message' => __('Your package has been prepared and is awaiting pickup.'),
                    ],
                    'related_type' => $order::class,
                    'related_id' => $order->id,
                ],
            );

            if (! empty($warehouseEmail)) {
                $this->mailSender->send(
                    new WarehouseOrderStatusMail($order, 'prepared', $statusMessage),
                    $warehouseEmail,
                    null,
                    [
                        'subject' => __('Order #:number prepared for shipment', ['number' => $order->id]),
                        'context' => [
                            'order_id' => $order->id,
                            'type' => 'warehouse_update',
                            'status' => 'prepared',
                            'status_message' => $statusMessage,
                        ],
                        'related_type' => $order::class,
                        'related_id' => $order->id,
                    ],
                );
            }
        });

        return back()->with('success', __('Order marked as prepared and customer notified.'));
    }
}
