<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
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
        $user = User::query()->where('id', $id)->first();

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
        $user = User::query()->where('id', $request['id'])->first();

        if (Hash::check($request['old_password'], $user['password']))
        {
            $user->update([
                'password' => Hash::make($request['new_password']),
            ]);

            return $this->sendResponse([]);
        }
        
        return $this->sendError(['error' => 'password does not match']);
    }
}
