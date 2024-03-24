<?php

namespace App\Http\Controllers\Auth;

use App\Events\EmailVerification;
use App\Http\Controllers\BaseController;
use App\Models\Company;
use App\Models\Freelancer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $input = $request->all();

        // make validation for user and company
        if ($input['is_company'])
        {
            $validator = Validator::make($input, [
                'name'             => 'required',
                'phone_number'     => 'required|unique:users,phone_number',
                'email'            => 'required|ends_with:@gmail.com|unique:users,email',
                'password'         => 'required|min:8',
                'major'            => 'required',
                'location'         => 'required',
                'num_of_employees' => 'required',
                'image'            => ['image' , 'mimes:jpeg,png,bmp,jpg,gif,svg']
            ],[
                'phone_number.unique' => 'Phone is not unique',
                'email.unique'        => 'Email is not unique',
                'email.ends_with'     => 'Email must be ends with @gmail.com',
                'password.min'        => 'Password must be at least 8 characters'
            ]);

            if($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $input['password'] = Hash::make($input['password']);
            $input['role_id'] = Role::ROLE_COMPANY;

            $user = User::create($input);
            $input['user_id'] = $user->id;

            $company_image = null;

            if($request->hasFile('image'))
            {
                $image= $request->file('image');
                $company_image = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('image'),$company_image);
                $company_image = 'image/'.$company_image ;
            }

            $input['image'] = $company_image;

            Company::create($input);

            EmailVerification::dispatch($user);

            return $this->sendResponse([]);
        }
        else
        {
            $validator = Validator::make($input, [
                'first_name'   => 'required',
                'last_name'    => 'required',
                'phone_number' => 'required|unique:users,phone_number',
                'email'        => 'required|ends_with:@gmail.com|unique:users,email',
                'password'     => 'required|min:8',
                'major'        => 'required',
                'location'     => 'required',
                'study_case'   => 'required',
                'open_to_work' => 'required',
                'image'        => ['image' , 'mimes:jpeg,png,bmp,jpg,gif,svg']
            ],[
                'phone_number.unique' => 'Phone is not unique',
                'email.unique'        => 'Email is not unique',
                'email.ends_with'     => 'Email must be ends with @gmail.com',
                'password.min'        => 'Password must be at least 8 characters'
            ]);

            if($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $input['password'] = Hash::make($input['password']);
            $input['role_id'] = Role::ROLE_FREELANCER;

            $user = User::create($input);
            $input['user_id'] = $user->id;

            $freelancer_image = null;

            if($request->hasFile('image'))
            {
                $image= $request->file('image');
                $freelancer_image = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('image'),$freelancer_image);
                $freelancer_image = 'image/'.$freelancer_image ;
            }

            $input['image'] = $freelancer_image;

            Freelancer::create($input);

            EmailVerification::dispatch($user);

            return $this->sendResponse([]);
        }
    }
}
