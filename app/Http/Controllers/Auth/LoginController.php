<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class LoginController extends BaseController
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|ends_with:@gmail.com',
            'password' => 'required'
        ],[

            'email.ends_with' => 'Email must be ends with @gmail.com'
        ]);

        if (Auth::attempt($request->only(['email', 'password']))) {
            $token = $request->user()->createToken('Personal Access Token')->accessToken;
            $user = Auth::user();

            if (!$user['email_verified'])
            {
                return $this->sendError(['error' => 'email is not verified']);
            }

            if (Gate::allows('isCompany', $user))
            {
                $company = $user->company;
                $company['phone_number'] = $user->phone_number;
                $company['email'] = $user->email;
                $company['role_id'] = $user->role_id;
                $user = $company;
            }
            else if (Gate::allows('isFreelancer', $user))
            {
                $freelancer = $user->freelancer;
                $freelancer['phone_number'] = $user->phone_number;
                $freelancer['email'] = $user->email;
                $freelancer['role_id'] = $user->role_id;

                $user = $freelancer;
            }

            $data = [];
            $data['user'] = $user;
            $data['accessToken'] = $token;

            return $this->sendResponse($data);
        }

        return $this->sendError(['error' => 'Unauthorised']);
    }

}
