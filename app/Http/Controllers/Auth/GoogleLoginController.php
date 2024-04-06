<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\UserController;
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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;

class GoogleLoginController extends BaseController
{
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

        $client = new Client();

        try {
            $response = $client->request('GET', 'https://www.googleapis.com/oauth2/v3/userinfo', [
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
                $existingUser = User::query()->create([
                    'email'    => $email,
                    'password' => bcrypt(Str::random(13)),
                ]);
            }

            $user = User::query()->where('email', $email)->first();
            $user['accessToken'] = $user->createToken('Personal Access Token')->accessToken;
            $user['authorized']  = $user['email_verified'];
            $user['image']       = $body->picture;
            $user['name']        = $body->name;
            $user['given_name']  = $body->given_name;
            $user['family_name'] = $body->family_name;

            $user['type']        = (new UserController())->get_type($user);
            return $this->sendResponse($user);
        }
        catch (RequestException $e) {
            return $this->sendError(['error' => 'token is wrong']);
        }
    }

    public function getUserINfo(Request $request) : JsonResponse
    {
        $input = $request->all();
        if ($input['is_company'])
        {
            $validator = Validator::make($input, [
                'name'             => 'required',
                'phone_number'     => 'required|digits:10|unique:users,phone_number',
                'email'            => 'required|ends_with:@gmail.com|exists:users,email',
                'major_id'            => 'required',
                'location'         => 'required',
                'num_of_employees' => 'required',
                'image'            => 'required',
            ],[
                'phone_number.unique' => 'Phone Nmuber is not unique',
                'phone_number.digits' => 'Phone Number must contain numbers only',
                'email.ends_with'     => 'Email must be ends with @gmail.com',
                'email.exists'        => 'Email is invalid',
            ]);

            if($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $user = User::query()->where('email', $input['email'])->first();
            $user->update([
                'role_id' => Role::ROLE_USER,
                'phone_number' => $input['phone_number'],
                'email_verified' => 1,
            ]);

            $company_data = [
                'name'              => $input['name'],
                'location'          => $input['location'],
                'major_id'             => $input['major_id'],
                'num_of_employees'  => $input['num_of_employees'],
                'description'       => $input['description'],
                'image'             => $input['image'],
            ];

            $response = (new RegisterController)->extracted_data($user , Company::create($company_data));
        }
        else
        {
            $validator = Validator::make($input, [
                'first_name'         => 'required',
                'last_name'          => 'required',
                'phone_number' => 'required|digits:10|unique:users,phone_number',
                'email'        => 'required|ends_with:@gmail.com|exists:users,email',
                'major_id'        => 'required',
                'location'     => 'required',
                'study_case'   => 'required',
                'open_to_work' => 'required',
                'birth_date'   => 'required',
                'image'        => 'required',
            ],[
                'phone_number.unique' => 'Phone is not unique',
                'phone_number.digits' => 'Phone Number must contain numbers only',
                'email.exists'        => 'Email is invalid',
                'email.ends_with'     => 'Email must be ends with @gmail.com',
            ]);

            if($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $user = User::query()->where('email', $input['email'])->first();
            $user->update([
                'role_id' => Role::ROLE_USER,
                'phone_number' => $input['phone_number'],
                'email_verified' => 1,
            ]);

            $freelancer_data = [
                'study_case_id'  => $input['study_case'],
                'first_name'     => $input['name'],
                'last_name'      => $input['last_name'],
                'birth_date'     => $input['birth_date'],
                'location'       => $input['location'],
                'major_id'       => $input['major_id'],
                'open_to_work'   => $input['open_to_work'],
                'image'          => $input['image'],
            ];

            $response = (new RegisterController)->extracted_data($user , Freelancer::create($freelancer_data));
        }
        return $response;
    }
}
