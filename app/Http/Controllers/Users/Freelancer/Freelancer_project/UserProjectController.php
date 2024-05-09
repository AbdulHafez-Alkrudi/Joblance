<?php

namespace App\Http\Controllers\Users\Freelancer\Freelancer_project;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\UserProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class UserProjectController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Here if the admins want all the user's projects, they won't send a specific user_id
        // else I should return all the projects for a certain user:


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
       DB::beginTransaction();
       $data = $request->all();
       $validator = Validator::make($data , [
           'project_name' => 'required' ,
           'project_description' => 'required' ,
           'link' => 'required'
       ]);
       if($validator->fails()){
           DB::rollBack();
           return $this->sendError($validator->errors()) ;
       }
       $data['user_id'] = auth()->id();
       $project = (new UserProject)->store($data);
       if(array_key_exists('images' , $data))
       {
           $response = (new UserProjectImageController)->store($data , $project->id) ;
           // checking if the creation of the images have done or not
           // the format of the response if as following:
           // [status] => success OR failure
           // if the status is success then there going to bo [images] key
           // otherwise there going to be [error_message] key
           if($response['status'] == 'failure'){
               return $this->sendError($response['error_message']) ;
           }
           $project['images'] = $response['images'];
       }
       DB::commit();
       return $this->sendResponse($project);
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
        //
    }
}
