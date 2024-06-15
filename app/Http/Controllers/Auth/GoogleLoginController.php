<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Users\UserController;
use App\Http\Requests\GoogleLoginCompanyRequest;
use App\Models\Company;
use App\Models\Freelancer;
use App\Models\Role;
use App\Models\User;
use App\Models\Users\Company\Company as CompanyCompany;
use App\Models\Users\Freelancer\Freelancer as FreelancerFreelancer;
use App\Models\Users\Role as UsersRole;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use League\OAuth2\Client\Provider\Google;

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
            $googleLoginComapnyRequest = new GoogleLoginCompanyRequest();
            $validator = Validator::make($input, $googleLoginComapnyRequest->rules());

            if($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $user = User::query()->where('email', $input['email'])->first();
            $user->update([
                'role_id' => UsersRole::ROLE_USER,
                'phone_number' => $input['phone_number'],
                'email_verified' => 1,
            ]);

            $company_data = [
                'name'              => $input['name'],
                'location'          => $input['location'],
                'major_id'          => $input['major_id'],
                'num_of_employees'  => $input['num_of_employees'],
                'description'       => $input['description'],
                'image'             => $input['image'],
            ];

            $response = (new RegisterController)->extracted_data($user , CompanyCompany::create($company_data));
        }
        else
        {
            $validator = Validator::make($input, );

            if($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $user = User::query()->where('email', $input['email'])->first();
            $user->update([
                'role_id' => UsersRole::ROLE_USER,
                'phone_number' => $input['phone_number'],
                'email_verified' => 1,
            ]);

            $freelancer_data = [
                'study_case_id'  => $input['study_case_id'],
                'first_name'     => $input['name'],
                'last_name'      => $input['last_name'],
                'birth_date'     => $input['birth_date'],
                'location'       => $input['location'],
                'major_id'       => $input['major_id'],
                'open_to_work'   => $input['open_to_work'],
                'image'          => $input['image'],
            ];

            $response = (new RegisterController)->extracted_data($user , FreelancerFreelancer::create($freelancer_data));
        }
        return $response;
    }
}
