<?php

namespace App\Services\Mail;

use App\Models\MailLog;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Throwable;

class LoggedMailSender
{
    /**
     * Send a mail and persist a log entry. Errors are captured and logged without bubbling up.
     *
     * @param  array{subject?: string, context?: array<string, mixed>, related_type?: ?string, related_id?: ?int, direction?: string, status?: string}  $log
     */
    public function send(Mailable $mailable, string $email, ?string $name = null, array $log = []): void
    {
        $subject = $log['subject'] ?? $mailable->subject ?? 'Notification';
        $context = $log['context'] ?? [];
        $direction = $log['direction'] ?? 'outgoing';
        $status = $log['status'] ?? 'sent';

        try {
            if ($subject) {
                $mailable->subject($subject);
            }

            $recipient = $name ? [$email => $name] : $email;
            Mail::to($recipient)->send($mailable);
        } catch (TransportExceptionInterface | Throwable $exception) {
            $status = 'failed';
            $context['error'] = $exception->getMessage();
            report($exception);
        }

        MailLog::record([
            'subject' => $subject,
            'status' => $status,
            'direction' => $direction,
            'to_email' => $email,
            'to_name' => $name,
            'context' => $context,
            'related_type' => $log['related_type'] ?? null,
            'related_id' => $log['related_id'] ?? null,
        ]);
    }
}

