<?php

namespace App\Http\Controllers;

use App\Mail\SendCodeResetPassword;
use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ResetCodePasswordController extends BaseController
{
    public function userForgotPassword(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Delete all old code that user send before
        ResetCodePassword::query()->where('email', $request['email'])->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::query()->create($data);

        // Send email to user
        Mail::to($request['email'])->send(new SendCodeResetPassword($codeData['code']));

        return response()->json(['status' => 'success', 'message' => trans('code.sent')], 200);
    }

    public function userCheckCode(Request $request) : JsonResponse
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords,code',
        ]);

        // find the code
        $passwordReset = ResetCodePassword::query()->firstWhere('code', $request['code']);

        // check if it is not expired : the time is one hour
        if ($passwordReset['created_at'] > now()->addHour()) {
            $passwordReset->delete();
            return response()->json(['status' => 'failure', 'message' => trans('password.code_is_expire')], 422);
        }

        return response()->json([
            'status' => 'success',
            'code' => $passwordReset['code'],
            'message' => trans('password.code_is_valid'),
        ]);
    }

    public function userResetPassword(Request $request) : JsonResponse
    {
        $input = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        // find the email
        $passwordReset = ResetCodePassword::query()->firstWhere('email', $request['email']);

        // check if it is not expired : the time is one hour
        if ($passwordReset['created_at'] > now()->addHour()) {
            $passwordReset->delete();
            return response()->json(['status' => 'failure', 'message' => trans('password.code_is_expire')], 422);
        }

        // find user's email
        $user = User::query()->firstWhere('email', $passwordReset['email']);

        // update user password
        $input['password'] = bcrypt($input['password']);

        $user->update([
            'password' => $input['password'],
        ]);

        // delete current code
        $passwordReset->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'password hase been successfully reset',
        ]);
    }

    public function userResendCode(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Delete all old code that user send before
        ResetCodePassword::query()->where('email', $request['email'])->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        $data['email'] = $request['email'];

        // Create a new code
        $codeData = ResetCodePassword::query()->create($data);

        // Send email to user
        Mail::to($request['email'])->send(new SendCodeResetPassword($codeData['code']));

        return response()->json(['status' => 'success', 'message' => trans('code.resent')], 200);
    }
}
