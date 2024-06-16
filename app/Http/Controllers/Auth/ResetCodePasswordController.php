<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ResetPasswordCheckCodeRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\SendCodeResetPassword;
use App\Models\Auth\ResetCodePassword;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ResetCodePasswordController extends BaseController
{
    public function userForgotPassword(Request $request) : JsonResponse
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'email' => 'required|email|exists:users,email',
        ]);

        if($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $user = User::query()->where('email', $data['email'])->first();
        if (!$user['email_verified'])
        {
            return $this->sendError(['error' => 'email is not verified']);
        }

        // Delete all old code that user send before
        ResetCodePassword::query()->where('email', $request['email'])->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::query()->create($data);

        // Send email to user
        Mail::to($request['email'])->send(new SendCodeResetPassword($codeData['code']));

        return $this->sendResponse([]);
    }

    public function userCheckCode(Request $request) : JsonResponse
    {
        $checkCodeRequest = new ResetPasswordCheckCodeRequest();
        $validator = Validator::make($request->all(), $checkCodeRequest->rules());

        if($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        // find the code
        $passwordReset = ResetCodePassword::query()->firstWhere('code', $request['code']);

        // check if it is not expired : the time is one hour
        if ($passwordReset['created_at'] > now()->addHour()) {
            $passwordReset->delete();
            return $this->sendError(['error' => trans('password.code_is_expire')]);
        }

        return $this->sendResponse([]);
    }

    public function userResetPassword(Request $request) : JsonResponse
    {
        $resetPasswordRequest = new ResetPasswordRequest();
        $validator = Validator::make($request->all(), $resetPasswordRequest->rules());

        if($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        // find the email
        $passwordReset = ResetCodePassword::query()->firstWhere('email', $request['email']);

        // check if it is not expired : the time is one hour
        if ($passwordReset['created_at'] > now()->addHour()) {
            $passwordReset->delete();
            return $this->sendError(['error' => trans('password.code_is_expire')]);
        }

        // find user's email
        $user = User::query()->firstWhere('email', $passwordReset['email']);

        // update user password
        $request['password'] = bcrypt($request['password']);

        $user->update([
            'password' => $request['password'],
        ]);

        // delete current code
        $passwordReset->delete();

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
