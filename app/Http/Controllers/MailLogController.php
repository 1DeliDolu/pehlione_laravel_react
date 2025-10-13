<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\MailLog;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MailLogController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $logs = MailLog::query()
            ->with('related')
            ->when(
                $user !== null && $user->role !== Role::ADMIN,
                function ($query) use ($user) {
                    $query->where(function ($builder) use ($user) {
                        $builder->where('to_email', $user->email);

                        if ($user->role === Role::LAGER) {
                            $builder->orWhereIn('context->type', ['warehouse_alert', 'warehouse_update']);
                        }
                    });
                }
            )
            ->latest('sent_at')
            ->paginate(20)
            ->withQueryString()
            ->through(function (MailLog $log) {
                $relatedOrder = $log->related instanceof Order ? $log->related : null;

                return [
                    'id' => $log->id,
                    'direction' => $log->direction,
                    'status' => $log->status,
                    'subject' => $log->subject,
                    'to_email' => $log->to_email,
                    'to_name' => $log->to_name,
                    'sent_at' => optional($log->sent_at)->toIso8601String(),
                    'read_at' => optional($log->read_at)->toIso8601String(),
                    'deleted_at' => optional($log->deleted_at)->toIso8601String(),
                    'context' => $log->context,
                    'order' => $relatedOrder ? [
                        'id' => $relatedOrder->id,
                        'status' => $relatedOrder->status,
                        'prepared_at' => optional($relatedOrder->prepared_at)->toIso8601String(),
                        'shipped_at' => optional($relatedOrder->shipped_at)->toIso8601String(),
                        'delivery_estimate_at' => optional($relatedOrder->delivery_estimate_at)->toIso8601String(),
                        'tracking_number' => $relatedOrder->tracking_number,
                    ] : null,
                ];
            });

        return Inertia::render('dashboard/mail/index', [
            'logs' => $logs,
        ]);
    }

    public function markRead(Request $request, MailLog $mailLog): RedirectResponse
    {
        $mailLog->markRead();

        return back()->with('success', __('Mail marked as read.'));
    }

    public function destroy(Request $request, MailLog $mailLog): RedirectResponse
    {
        $mailLog->delete();

        return back()->with('success', __('Mail log removed.'));
    }
}
