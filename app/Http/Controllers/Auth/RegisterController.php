<?php

namespace App\Http\Controllers\Auth;

use App\Events\EmailVerification;
use App\Http\Controllers\BaseController;
use App\Models\Company;
use App\Models\Freelancer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $input = $request->all();

        // make validation for user and company
        if ($input['is_company'])
        {
            $validator = Validator::make($input, [
                'name'             => 'required',
                'phone_number'     => 'required|digits:10|unique:users,phone_number',
                'email'            => 'required|ends_with:@gmail.com|unique:users,email',
                'password'         => 'required|min:8',
                'major'            => 'required',
                'location'         => 'required',
                'num_of_employees' => 'required',
                'image'            => ['image' , 'mimes:jpeg,png,bmp,jpg,gif,svg']
            ],[
                'phone_number.unique' => 'Phone Nmuber is not unique',
                'phone_number.digits' => 'Phone Number must contain numbers only',
                'email.unique'        => 'Email is not unique',
                'email.ends_with'     => 'Email must be ends with @gmail.com',
                'password.min'        => 'Password must be at least 8 characters'
            ]);

            if($validator->fails())
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
            $input['image'] = $this->get_image($request , $input , "company");

            $company_data = [
                'name'              => $input['name'],
                'location'          => $input['location'],
                'major'             => $input['major'],
                'num_of_employees'  => $input['num_of_employees'],
                'description'       => $input['description'],
                'image'             => $input['image']
            ];

            $response = $this->extracted_data($user , Company::create($company_data));
            DB::commit();

            return $response ;
        }
        else
        {
            $validator = Validator::make($input, [
                'first_name'   => 'required',
                'last_name'    => 'required',
                'phone_number' => 'required|digits:10|unique:users,phone_number',
                'email'        => 'required|ends_with:@gmail.com|unique:users,email',
                'password'     => 'required|min:8',
                'major'        => 'required',
                'location'     => 'required',
                'study_case'   => 'required',
                'open_to_work' => 'required',
                'birth_date'   => 'required',
                'image'        => ['image' , 'mimes:jpeg,png,bmp,jpg,gif,svg']
            ],[
                'phone_number.unique' => 'Phone is not unique',
                'phone_number.digits' => 'Phone Number must contain numbers only',
                'email.unique'        => 'Email is not unique',
                'email.ends_with'     => 'Email must be ends with @gmail.com',
                'password.min'        => 'Password must be at least 8 characters'
            ]);

            if($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $input['password'] = Hash::make($input['password']);
            $input['role_id'] = Role::ROLE_USER;

            $user_data = [
                'phone_number' => $input['phone_number'],
                'email'        => $input['email'],
                'password'     => $input['password'],
                'role_id'      => $input['role_id'],

            ];

            $user = User::create($user_data);
            $input['image'] = $this->get_image($request, $input , "freelancer");

            $freelancer_data = [
                'study_case_id'  => $input['study_case'],
                'first_name'     => $input['first_name'],
                'last_name'      => $input['last_name'],
                'birth_date '    => $input['birth_date'],
                'location'       => $input['location'],
                'major'          => $input['major'],
                'open_to_work'   => $input['open_to_work'],
                'image'          => $input['image'],
            ];

            $response = $this->extracted_data($user , Freelancer::create($freelancer_data));
            DB::commit();

            return $response ;
        }
    }

    /**
     * @param Request $request
     * @param array $input
     * @return string
     */
    private function get_image(Request $request, array $input , string $type): string
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
    protected function extracted_data($user , $specified_user_data): JsonResponse
    {
        // $the second parameter can be company or freelancer:

        $user->userable()->associate($specified_user_data);
        $user->save();

        // just to send email verification
        EmailVerification::dispatch($user);

        // just to send it to the API
        $token = $user->createToken('Personal Access Token')->accessToken;

        $specified_user_data['phone_number'] = $user['phone_number'];
        $specified_user_data['email'] = $user['email'];
        $specified_user_data['role_id'] = $user['role_id'];
        $specified_user_data['accessToken'] = $token;
        $specified_user_data['id'] = $user['id'];

        return $this->sendResponse($specified_user_data);
    }
}
