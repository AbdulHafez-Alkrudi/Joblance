<?php

namespace App\Listeners;

use App\Events\PayoutEmail;
use App\Mail\SendPayoutEmail as MailSendPayoutEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendPayoutEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PayoutEmail $event): void
    {
        $data = [
            'user'    => $event->user,
            'body'    => $event->message_body,
            'subject' => $event->message_subject,
        ];

        Mail::to($event->user->email)->send(new MailSendPayoutEmail($data));
    }
}
