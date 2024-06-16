<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\BaseController;
use App\Http\Requests\TaskRequest;
use App\Models\Users\Task;
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

        $tasks = Task::all();
        if(!is_null($user_id)){
            $tasks = Task::query()->where('user_id', $user_id)->get();
        }
        $tasks = (new Task)->get_all_tasks($tasks, $lang);

        return $this->sendResponse($tasks);
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
        $lang = request('lang');
        $task = Task::find($id);
        if(is_null($task)){
            return $this->sendError('there is no task with this ID');
        }
        $task = $task->get_task($task, $lang);
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
