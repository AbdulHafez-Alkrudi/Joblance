<?php

namespace App\Listeners;

use App\Events\EmailVerification;
use App\Mail\SendCodeEmailVerification;
use App\Models\EmailVerification as ModelsEmailVerification;
use Egulias\EmailValidator\Validation\EmailValidation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailVerification
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
    public function handle(EmailVerification $event): void
    {
        $event->user->sendCode($event->user->email);
    }
}
