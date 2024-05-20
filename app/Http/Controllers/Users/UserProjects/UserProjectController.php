<?php

namespace App\Http\Controllers\Users\UserProjects;

use App\Http\Controllers\BaseController;
use App\Models\UserProject;
use Illuminate\Http\JsonResponse;
use App\Models\UserProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $user_id = Auth::id();
        $projects = UserProject::query()->where('user_id', $user_id)->get();
        return $this->sendResponse($projects);
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

        $project = UserProject::query()->findOrFail($id);
        $projectImages = UserProjectImage::query()->where('project_id', $project->id)->get();

        return $this->sendResponse(['project' => $project, 'images' => $projectImages]);
    }

    protected function indexByUserId(string $userId)
    {
        $projects = UserProject::query()->where('user_id', $userId)->get();
        return $this->sendResponse($projects);
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
