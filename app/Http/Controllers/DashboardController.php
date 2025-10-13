<?php

namespace App\Http\Controllers;

use App\Models\MailLog;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $shipments = collect();

        if ($user && Schema::hasTable('mail_logs')) {
            $shipments = MailLog::query()
                ->with('related')
                ->where('to_email', $user->email)
                ->where('direction', 'outgoing')
                ->whereIn('context->type', ['preparation_update', 'shipment_update'])
                ->latest('sent_at')
                ->get()
                ->groupBy(function (MailLog $log) {
                    return data_get($log->context, 'order_id') ?? $log->id;
                })
                ->map(function ($logs): array {
                    /** @var \Illuminate\Support\Collection<int, MailLog> $logs */
                    $preparationLog = $logs->first(function (MailLog $entry) {
                        return data_get($entry->context, 'type') === 'preparation_update';
                    });
                    $shipmentLog = $logs->first(function (MailLog $entry) {
                        return data_get($entry->context, 'type') === 'shipment_update';
                    });

                    $baseLog = $shipmentLog ?? $preparationLog ?? $logs->first();
                    $order = $baseLog && $baseLog->related instanceof Order ? $baseLog->related : null;

                    $statusContext = $shipmentLog?->context ?? $preparationLog?->context ?? [];

                    return [
                        'id' => $order?->id ?? $baseLog->id,
                        'subject' => $shipmentLog?->subject ?? $preparationLog?->subject ?? __('Order update'),
                        'sentAt' => optional($baseLog?->sent_at)->toIso8601String(),
                        'orderId' => data_get($statusContext, 'order_id'),
                        'statusMessage' => data_get($statusContext, 'status_message'),
                        'packageStatus' => [
                            'approved' => (bool) data_get($statusContext, 'package_status.approved', false),
                            'approvedAt' => data_get(
                                $preparationLog?->context ?? $statusContext,
                                'package_status.approved_at'
                            ),
                            'dispatched' => (bool) data_get(
                                $shipmentLog?->context ?? [],
                                'package_status.dispatched',
                                false
                            ),
                            'dispatchedAt' => data_get($shipmentLog?->context ?? [], 'package_status.dispatched_at'),
                        ],
                        'trackingNumber' => data_get($shipmentLog?->context ?? [], 'tracking_number')
                            ?? $order?->tracking_number,
                        'estimatedDeliveryAt' => data_get(
                            $shipmentLog?->context ?? [],
                            'estimated_delivery_at'
                        ) ?? optional($order?->delivery_estimate_at)->toIso8601String(),
                    ];
                })
                ->sortByDesc(function (array $entry) {
                    return $entry['sentAt'] ?? '';
                })
                ->values()
                ->take(5);
        }

        return Inertia::render('dashboard', [
            'shipments' => $shipments->values(),
        ]);
    }
}
