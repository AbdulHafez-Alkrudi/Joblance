<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        if (!$user)
        {
            return $this->sendError(['error' => 'id is invalid']);
        }

        $userable = $user->userable;
        $userable['email'] = $user['email'];
        $userable['phone_number'] = $user['phone_number'];
        $userable['role_id'] = $user['role_id'];

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
        //
    }

    public function changePassword(Request $request)
    {
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
