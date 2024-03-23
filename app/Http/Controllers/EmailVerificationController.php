<?php

namespace App\Http\Controllers;

use App\Mail\SendCodeEmailVerification;
use App\Models\EmailVerification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use LDAP\Result;

use function PHPUnit\Framework\isNull;

class EmailVerificationController extends Controller
{
    public function userCheckCode(Request $request) : JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:email_verifications,email',
            'code' => 'required|string|exists:email_verifications,code',
        ],[
            'email.exists' => 'Email is not valid',
            'code.exists' => 'Code is not valid',
        ]);

        // find the code
        $emailverification = EmailVerification::query()->firstWhere('email', $request['email']);

        if ($request['code'] != $emailverification['code'])
        {
            return response()->json([
                'status' => 'failure',
                'message' => 'Code is not valid',
            ]);
        }

        // check if it is not expired : the time is one hour
        if ($emailverification['created_at'] > now()->addHour()) {
            $emailverification->delete();
            return response()->json(['status' => 'failure', 'message' => trans('email.code_is_expire')], 422);
        }

        return response()->json([
            'status' => 'success',
            'code' => $emailverification['code'],
            'message' => trans('email.code_is_valid'),
        ]);
    }

    public function userResendCode(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

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
