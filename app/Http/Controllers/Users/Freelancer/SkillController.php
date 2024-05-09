<?php

namespace App\Http\Controllers\Users\Freelancer;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkillController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->sendResponse(Skill::all());
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
         $data = $request->all();
         $validator = Validator::make($data , [
              'name' => 'required'
         ]);
         if($validator->fails()){
             return $this->sendError($validator->errors());
         }
         $skill = Skill::create($data);
         return $this->sendResponse($skill);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        // first I should check if such an id existed or not
        $skill = Skill::find($id);
        if($skill == null){
            return $this->sendError('there is not such an ID') ;
        }
        $skill->delete();
        return $this->sendResponse();
    }
}