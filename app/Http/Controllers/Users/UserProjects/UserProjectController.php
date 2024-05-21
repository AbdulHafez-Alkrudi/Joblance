<?php

namespace App\Http\Controllers\Users\UserProjects;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProject;
use Illuminate\Http\JsonResponse;
use App\Models\UserProjectImage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserProjectController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('user_id')) {
            return $this->indexByUserId($request->user_id);
        }
        elseif ($request->has('project_id')) {
            return $this->show($request->project_id);
        }

        $projects = UserProject::query()->where('user_id', Auth::id())->get();
        return $this->sendResponse($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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


            // checking if the creation of the images has done or not
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
        $project = UserProject::query()->find($id);
        if (is_null($project)) {
            return $this->sendError(['message' => 'Thers is not project with this ID']);
        }

        $projectImages = UserProjectImage::query()->where('project_id', $project->id)->get();

        return $this->sendResponse(['project' => $project, 'images' => $projectImages]);
    }

    protected function indexByUserId(string $userId)
    {
        if (is_null(User::find($userId))) {
            return $this->sendError('There is no user with this ID');
        }

        $projects = UserProject::query()->where('user_id', $userId)->get();
        return $this->sendResponse($projects);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all() ;
        $project = UserProject::find($id);
        if($project == null){
            return $this->sendError('there is no project with this ID');
        }
        // first I'll check if the user want to edit the images:
        // here there are two types of editing:
        // he can remove an existing image/images and add new one/ones
        if($request->input('images_del')){
            foreach($data['images_del'] as $image_id){
                (new UserProjectImageController)->destroy($image_id);
            }
        }
        if($request->hasFile('images_add')){
            (new UserProjectImage)->store($data['images_add'] , $id) ;
        }
        // excluding images_del and images_add from the array if they are existed
        $data = array_diff_key($data , array_flip(['images_del' , 'images_add']));
        $project->update($data);
        return $this->sendResponse($project->with('images'));
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
