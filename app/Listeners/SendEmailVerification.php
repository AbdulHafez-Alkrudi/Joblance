<?php

namespace App\Listeners;

use App\Events\EmailVerification;
use App\Mail\SendCodeEmailVerification;
use App\Models\EmailVerification as ModelsEmailVerification;
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
        $data['email'] = $event->user->email;

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ModelsEmailVerification::query()->create($data);

        // Send email to user
        Mail::to($event->user->email)->send(new SendCodeEmailVerification($codeData['code']));
    }
}
