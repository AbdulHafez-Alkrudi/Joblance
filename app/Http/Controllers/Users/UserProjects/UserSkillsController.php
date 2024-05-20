<?php

namespace App\Http\Controllers\Users\UserProjects;

use App\Http\Controllers\BaseController;
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
        $user_skills = UserSkills::query()->where('user_id' , $user_id)->get();

        $user_skills = (new UserSkills)->get_skills($user_skills);

        return $this->sendResponse($user_skills);
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
