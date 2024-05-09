<?php

namespace App\Http\Controllers\Users\Freelancer\Freelancer_project;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\UserSkills;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserSkillsController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = auth()->id();
        $skills = UserSkills::query()->where('user_id' , $user_id)->get();
        return $this->sendResponse($skills);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all() ;

        $validator = Validator::make($data , [
           'skill_id' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors()) ;
        }
        $data['user_id'] = auth()->id();
        $user_skill = UserSkills::create($data);
        return $this->sendResponse($user_skill) ;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
        $user_skill = UserSkills::find($id);
        if($user_skill == null){
            return $this->sendError('The user does not have a skill with this ID');
        }
        $user_skill->delete();
        return $this->sendResponse();
    }
}