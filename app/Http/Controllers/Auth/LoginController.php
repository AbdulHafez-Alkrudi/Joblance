<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Users\UserController;
use App\Http\Requests\LoginRequest;
use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LoginController extends BaseController
{
    public function login(Request $request)
    {
        $loginRequest = new LoginRequest();
        $validator = Validator::make($request->all(), $loginRequest->rules($request));

        if($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        if (Auth::attempt($request->only(['email', 'password']))) {
            $token = $request->user()->createToken('Personal Access Token')->accessToken;
            $user = Auth::user();

            if (!$user['email_verified'])
            {
                return $this->sendError(['error' => 'email is not verified']);
            }

            if ($user['role_id'] == 2 && !DeviceToken::where('user_id', $user->id)->where('token', $request->device_token)->exists()) {
                DeviceToken::create([
                    'user_id' => $user->id,
                    'token'   => $request->device_token,
                ]);
            }

            $userable = $user->userable;
            $userable['email'] = $user['email'];
            $userable['phone_number'] = $user['phone_number'];
            $userable['role_id'] = $user['role_id'];
            $userable['id'] = $user['id'];

            if ($user['role_id'] == 2)
                $userable['type'] = (new UserController())->get_type($user);

            $userable['accessToken'] = $token;

            //$request->user()->notify(new UserNotification('Login', 'Welcome To Our App!'));

            return $this->sendResponse($userable);
        }

        return $this->sendError(['error' => 'Unauthorised']);
    }

}
