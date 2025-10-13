<?php

namespace App\Http\Controllers;

use App\Models\MailLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MailLogController extends Controller
{
    public function index(Request $request): Response
    {
        $logs = MailLog::query()
            ->latest('sent_at')
            ->paginate(20)
            ->withQueryString()
            ->through(function (MailLog $log) {
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
