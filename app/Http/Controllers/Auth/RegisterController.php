<?php

namespace App\Http\Controllers\Auth;

use App\Events\EmailVerification;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Users\UserController;
use App\Http\Requests\{RegisterCompanyRequest, RegisterFreelancerRequest};
use App\Jobs\DeleteAccount;
use App\Models\{Company, Freelancer, Role, User};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\{Facades\DB, Facades\Hash, Facades\Validator};

class RegisterController extends BaseController
{
    public function __invoke(Request $request)
    {
        DB::beginTransaction();
        $input = $request->all();

        // make validation for user and company
        if ($input['is_company'])
        {
            $registerRequest = new RegisterCompanyRequest;
            $validator = Validator::make($input, $registerRequest->rules());

            if ($validator->fails())
            {
                DB::rollBack();
                return $this->sendError($validator->errors());
            }

            $input['password'] = Hash::make($input['password']);
            $input['role_id']  = Role::ROLE_USER;

            /*
             * Here we have two types of data:
             * The user data
             * the company data
             *
            */

            $user_data = [
                'phone_number' => $input['phone_number'],
                'email'        => $input['email'],
                'password'     => $input['password'],
                'role_id'      => $input['role_id'],
            ];

            $user = User::create($user_data);
            $input['image'] = $this->get_image($request, "company");

            $company_data = [
                'name'              => $input['name'],
                'location'          => $input['location'],
                'major_id'          => $input['major_id'],
                'num_of_employees'  => $input['num_of_employees'],
                'description'       => $input['description'],
                'image'             => $input['image']
            ];

            $response = $this->extracted_data($user , Company::create($company_data));
        }
        else
        {
            $registerRequest = new RegisterFreelancerRequest();
            $validator = Validator::make($input, $registerRequest->rules());

            if ($validator->fails())
            {
                DB::rollBack();
                return $this->sendError($validator->errors());
            }

            $input['password'] = Hash::make($input['password']);
            $input['role_id']  = Role::ROLE_USER;
            $user_data = [
                'phone_number' => $input['phone_number'],
                'email'        => $input['email'],
                'password'     => $input['password'],
                'role_id'      => $input['role_id'],
            ];

            $user = User::create($user_data);

            $input['image'] = $this->get_image($request, "freelancer");

            $freelancer_data = [
                'study_case_id'  => $input['study_case_id'],
                'first_name'     => $input['first_name'],
                'last_name'      => $input['last_name'],
                'birth_date'     => $input['birth_date'],
                'location'       => $input['location'],
                'major_id'       => $input['major_id'],
                'open_to_work'   => $input['open_to_work'],
                'image'          => $input['image'],
                'bio'            => $input['bio'],
                'gender'         => $input['gender'],
            ];

            $response = $this->extracted_data($user , Freelancer::create($freelancer_data));
        }


        DB::commit();
        return $response ;
    }

    /**
     * @param Request $request
     * @param array $input
     * @return string
     */
    public function get_image(Request $request, string $type): string
    {
        $user_image_name = "";

        if($request->hasFile('image'))
        {
            $image = $request->file('image');
            $user_image_name = time().'.'.$image->getClientOriginalExtension();

            $path = 'images/' . $type.'/' ;

            $image->move($path,$user_image_name);
            $user_image_name = $path.$user_image_name ;
        }

        return $user_image_name ;
    }

    /**
     * @param $user
     * @param $company
     * @return JsonResponse
     */
    public function extracted_data($user , $specified_user_data): JsonResponse
    {
        // $the second parameter can be company or freelancer:

        $user->userable()->associate($specified_user_data);
        $user->save();

        // just to send it to the API
        $token = $user->createToken('Personal Access Token')->accessToken;

        $specified_user_data['id'] = $user['id'];
        $specified_user_data['phone_number'] = $user['phone_number'];
        $specified_user_data['email'] = $user['email'];
        $specified_user_data['role_id'] = $user['role_id'];
        $specified_user_data['type'] = (new UserController)->get_type($user) ;
        $specified_user_data['accessToken'] = $token;

        // just to send email verification EmailVerification::dispatch($user);
        if (!$user['email_verified'])
        {
            EmailVerification::dispatch($user);
            DeleteAccount::dispatch($user)->delay(86400);

        }

        return $this->sendResponse($specified_user_data);
    }
}
