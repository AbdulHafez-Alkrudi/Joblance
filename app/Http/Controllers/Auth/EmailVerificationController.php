<?php

namespace App\Http\Controllers\Auth;

use App\Events\EmailVerification as EventsEmailVerification;
use App\Http\Controllers\BaseController;
use App\Http\Requests\EmailVerificationRequest;
use App\Mail\SendCodeEmailVerification;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailVerificationController extends BaseController
{
    public function userCheckCode(Request $request) : JsonResponse
    {
        $emailVerificationRequest = new EmailVerificationRequest();
        $validator = Validator::make($request->all(), $emailVerificationRequest->rules());

        if($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        // find the code
        $email_verification = EmailVerification::query()->firstWhere('email', $request['email']);

        if ($request['code'] != $email_verification['code'])
        {
            return $this->sendError(['error' => trans('Code is not valid')]);
        }

        // check if it is not expired : the time is one hour
        if ($email_verification['created_at'] > now()->addHour()) {
            $email_verification->delete();
            return $this->sendError(['error' => trans('password.code_is_expire')]);
        }

        // find user's email
        $user = User::query()->where('email', $email_verification['email']);

        // update user email_verified
        $user->update([
            'email_verified' => 1,
        ]);

        // delete current code
        $email_verification->delete();

        return $this->sendResponse([]);
    }

    public function userResendCode(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        if($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $user = User::query()->where('email', $request->email);
        $user->sendCode($request->email);

        return $this->sendResponse([]);
    }
}
