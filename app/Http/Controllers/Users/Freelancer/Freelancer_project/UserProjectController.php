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
        // if the user send an id with the request then I'll send all the projects related to that user
        // else I'll send all the user's project, but first I should make sure that the admin has sent this request
        $id = request('id') ;
        if($id != null){
            $user_projects = UserProject::query()->where("user_id" , $id)->get() ;
            return $this->sendResponse($user_projects);
        }
        else{

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $data = $request->all();
        $validator = Validator::make($data, [
            'project_name' => 'required',
            'project_description' => 'required',
            'link' => 'required'
        ]);
        if ($validator->fails()) {
            DB::rollBack();
            return $this->sendError($validator->errors());
        }
        $data['user_id'] = auth()->id();
        $project = (new UserProject)->store($data);
        if (array_key_exists('images', $data)) {


            $response = (new UserProjectImageController)->store($data, $project->id);


            // checking if the creation of the images have done or not
            // the format of the response is as following:
            // [status] => success OR failure
            // if the status is success then there going to bo [images] key
            // otherwise there going to be [error_message] key
            if ($response['status'] == 'failure') {
                return $this->sendError($response['error_message']);
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
        $user_project = UserProject::find($id);
        if ($user_project == null) {
            return $this->send_error('there is no project with this ID');
        }
        $user_project->delete();
        return $this->sendResponse();
    }
}
