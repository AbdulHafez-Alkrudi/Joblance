<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Mail\SendCodeEmailVerification;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use LDAP\Result;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isNull;

class EmailVerificationController extends BaseController
{
    public function userCheckCode(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:email_verifications,email',
            'code' => 'required|string|exists:email_verifications,code',
        ],[
            'email.exists' => 'Email is not valid',
            'code.exists' => 'Code is not valid',
        ]);

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

        return response()->json([
            'status' => 'success',
            'code' => $email_verification['code'],
            'message' => trans('email.code_is_valid'),
        ], 200);
    }

    public function userResendCode(Request $request) : JsonResponse
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'email' => 'required|email|exists:users,email',
        ]);

        if($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        // Delete all old code that user send before
        EmailVerification::query()->where('email', $request['email'])->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        $data['email'] = $request['email'];

        // Create a new code
        $codeData = EmailVerification::query()->create($data);

        // Send email to user
        Mail::to($request['email'])->send(new SendCodeEmailVerification($codeData['code']));

        return response()->json(['status' => 'success', 'message' => trans('code.resent')], 200);
    }
}
