<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lang = request('lang');
        $user_id = request('user_id');
        if(!is_null($user_id)){
            return $this->sendResponse((new Task)->get_all_tasks($user_id));
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all() ;
        $validator = Validator::make($data , (new TaskRequest)->rules());
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $data['user_id'] = auth()->id();
        $task = Task::create($data);
        return $this->sendResponse($task);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::find($id);
        $user = User::find($task->user_id);
        if(is_null($task)){
            return $this->sendError('there is no task with this ID');
        }
        $task['image'] = $user->userable->image;
        if($user->userable->name!=null)
        $task['name'] = $user->userable->name;
        else
        $task['name'] = $user->userable->first_name.' '.$user->userable->last_name;
        return $this->sendResponse($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::find($id);
        if(is_null($task)){
            return $this->sendError('there is no task with this ID');
        }
        $task->update($request->except('lang'));
        return $this->sendResponse($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::find($id);
        if(is_null($task)){
            return $this->sendError('there is no task with this ID');
        }
        $task->delete();
        return $this->sendResponse();
    }
}
