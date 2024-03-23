<?php

namespace App\Http\Controllers;

use App\Models\EmailVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function userCheckCode(Request $request) : JsonResponse
    {
        $request->validate([
            'code' => 'required|string|exists:email_verifications,code',
        ]);

        // find the code
        $emailverification = EmailVerification::query()->firstWhere('code', $request['code']);

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
}
