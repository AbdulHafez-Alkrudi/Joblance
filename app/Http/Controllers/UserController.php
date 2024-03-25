<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;

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
        if ($user->role == 'company')
        {
            $company = $user->company;
            $company['phone_number'] = $user->phone_number;
            $company['email'] = $user->email;
            $company['role'] = $user->role;

            return $this->sendResponse($company);
        }
        else
        {
            $freelancer = $user->freelancer;
            $freelancer['phone_number'] = $user->phone_number;
            $freelancer['email'] = $user->email;
            $freelancer['role'] = $user->role;

            return $this->sendResponse($freelancer);
        }
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
}
