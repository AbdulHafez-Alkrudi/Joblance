<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends BaseController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|ends_with:@gmail.com|exists:users,email',
            'password' => 'required'
        ],[
            'email.ends_with' => 'Email must be ends with @gmail.com',
            'email.exists'    => 'Email is invalid'
        ]);

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

            $userable = $user->userable;
            $userable['email'] = $user['email'];
            $userable['phone_number'] = $user['phone_number'];
            $userable['role_id'] = $user['role_id'];
            $userable['id'] = $user['id'];
            $userable['type'] = (new UserController())->get_type($user);
            $userable['accessToken'] = $token;



            return $this->sendResponse($userable);
        }

        return $this->sendError(['error' => 'Unauthorised']);
    }

}
