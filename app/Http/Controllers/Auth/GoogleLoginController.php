<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Google_Client;
use Google_Service_Oauth2;
use League\OAuth2\Client\Provider\Google;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Freelancer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Exception;
use GuzzleHttp\Exception\RequestException;

class GoogleLoginController extends BaseController
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
    }

    public function handleGoogleCallback(Request $request)
    {
        $user = Socialite::driver('google')->stateless()->user();

        $existingUser = User::where('email', $user->email)->first();

        if (is_null($existingUser))
        {
            $newUser = new User();
            $newUser->email       = $user->email;
            $newUser->phone_nmber = $user->phone;
            $newUser->password    = bcrypt(Str::random(13));
            $newUser->role_id     = $request['is_company'] ? Role::ROLE_COMPANY : Role::ROLE_FREELANCER;
            $newUser->email_verified = 1;
            $newUser->save();

            $existingUser = User::find($newUser->id);

            if ($request['is_company'])
            {
                Company::query()->create([
                    'user_id' => $existingUser->id,
                    'name'    => $user->name,
                ]);
            }
            else
            {
                Freelancer::query()->create([
                    'user_id' => $existingUser->id,
                    'first_name'    => $user->name,
                ]);
            }
        }
        $existingUser->email_verified = 1;
        $existingUser->save();

        $user = $existingUser;
        $token = $user->createToken('Personal Access Token')->accessToken;

        $data = [];
        $data['user'] = $user;
        $data['accessToken'] = $token;

        return $this->sendResponse($data);
    }

    public function googleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_token' => "required",
        ]);

        if($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $access_token = $request->access_token;

        $user = new User();

        try {
            $response = $user->request('GET', 'https://www.googleapis.com/oauth2/v3/userinfo', [
                'query' => [
                    'access_token' => $access_token,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody()->getContents());
            $email = $body->email;
            $existingUser = User::where('email', $email)->first();

            if (is_null($existingUser))
            {
                $newUser = new User();
                $newUser->email = $email;
                $newUser->phone_number = $body->phone;
                $newUser->password = bcrypt(Str::random(13));
                $newUser->role_id     = $request['is_company'] ? Role::ROLE_COMPANY : Role::ROLE_FREELANCER;
                $newUser->email_verified = 1;
                $newUser->save();

                $existingUser = User::find($newUser->user_id);
                if ($request['is_company'])
                {
                    Company::query()->create([
                        'user_id' => $existingUser->id,
                        'name'    => $user->name,
                    ]);
                }
                else
                {
                    Freelancer::query()->create([
                        'user_id' => $existingUser->id,
                        'first_name'    => $user->name,
                    ]);
                }
            }
            $existingUser->email_verified = 1;
            $existingUser->save();

            $user = $existingUser;
            $token = $user->createToken('Personal Access Token')->accessToken;

            $data = [];
            $data['user'] = $user;
            $data['accessToken'] = $token;

            return $this->sendResponse($data);
        }
        catch (RequestException $e) {
            return $this->sendError(['error' => 'token is wrong']);
        }
    }
}
