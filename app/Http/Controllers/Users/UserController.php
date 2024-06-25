<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ChangePasswordRequest;
use App\Mail\SendCodeEmailVerification;
use App\Models\Users\Company\Company;
use App\Models\Auth\EmailVerification;
use App\Models\Users\Evaluation;
use App\Models\Users\Freelancer\Freelancer;
use App\Models\Review\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::query()->find($id);
        if (is_null($user))
        {
            return $this->sendError(['error' => 'There is not user with this ID']);
        }

        $userable = $user->userable->get_info($user->userable, \request('lang'), false);

        return $this->sendResponse($userable);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id) ;
        if($user == null){
            return $this->sendError('there is no user with this ID');
        }
        $company_image = $user->userable->image;
        if($company_image != null){
            Storage::disk('public')->delete($company_image);
        }
        $user->delete();
        return $this->sendResponse([]);
    }

    public function changePassword(Request $request)
    {
        $chandePasswordRequest = new ChangePasswordRequest();
        $validator = Validator::make($request->all(), $chandePasswordRequest->rules());

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $user = Auth::user();
        $user = User::query()->find($user['id']);

        if (Hash::check($request['old_password'], $user['password']))
        {
            $user->update([
                'password' => Hash::make($request['new_password']),
            ]);

            return $this->sendResponse([]);
        }

        return $this->sendError(['error' => 'password does not match']);
    }

    public function get_type(User $user): string
    {
        if($user->userable_type == Company::class)
            return User::COMPANY;
        return USER::FREELANCER;
    }
}
