<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\BaseController;
use App\Http\Requests\TaskRequest;
use App\Models\Users\Task;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class TaskController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('user_id')) {
            return $this->indexByUserId($request->user_id);
        }

        if (Gate::allows('isAdmin', Auth::user())) {
            $tasks = Task::with('user.userable')
                        ->orderByDesc('created_at')
                        ->filter(\request(['user_id' , 'major_id' , 'date_posted']))
                        ->get();
        }
        else {
            $user = auth()->user()->userable;
            $tasks = Task::with('user.userable')
                        ->orderByRaw("CASE WHEN tasks.user_id IN (SELECT user_id FROM followers WHERE followers.follower_id = ?) THEN 0 ELSE 1 END, CASE WHEN major_id = ? THEN 0 ELSE 1 END, tasks.created_at DESC", [Auth::id(), $user->major_id])
                        ->filter(\request(['user_id' , 'major_id' , 'date_posted']))
                        ->get();
        }
        $tasks = (new Task)->get_all_tasks($tasks, request('lang'));
        return $this->sendResponse($tasks);
    }

    public function indexByUserId($user_id)
    {
        $user = User::find($user_id);
        if (is_null($user)) {
            return $this->sendError(['message' => 'There is no user with this ID']);
        }

        $tasks = Task::with('user.userable')
                        ->where('user_id', $user_id)
                        ->orderByDesc('created_at')
                        ->filter(\request(['user_id' , 'major_id' , 'date_posted']))
                        ->get();

        $tasks = (new Task)->get_all_tasks($tasks, request('lang'));
        return $this->sendResponse($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), (new TaskRequest)->rules());
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }

        $request['user_id'] = auth()->id();
        $task = Task::create($request->all());

        // To notify users following this user
        $current_user = Auth::user()->userable;
        if ($current_user->name) {
            $user_name = $current_user->name;
        }
        else {
            $user_name = $current_user->first_name . ' ' . $current_user->last_name;
        }
        $users = User::query()->whereIn('id', Auth::user()->followers()->pluck('follower_id'))->get();
        Notification::send($users, new UserNotification('Post a task', $user_name . ' posted a new task', ['task_id' => $task->id]));

        return $this->sendResponse($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::with('user.userable')->find($id);
        if(is_null($task)){
            return $this->sendError('there is no task with this ID');
        }
        $task = $task->get_task($task, request('lang'));
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
