<?php

namespace App\Http\Controllers\Users\UserProjects;

use App\Http\Controllers\BaseController;
use App\Models\Users\UserProjects\UserSkills;
use App\Models\Users\UserProjects\UserTags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserSkillsController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $user_skills = collect((new UserSkills)->get_skills($user->skills));
        $userTags = collect((new UserTags)->get_tags($user->tags));

        $merged = $user_skills->merge($userTags);

        return $this->sendResponse($merged);
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->hasSkill($request->skill_id)) {
            return $this->sendError(['User already has skill with this ID']);
        }
        $data['user_id'] = $user->id;
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
