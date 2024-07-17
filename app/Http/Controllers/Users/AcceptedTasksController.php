<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\BaseController;
use App\Http\Requests\AcceptedTaskRequest;
use App\Jobs\AcceptedTask;
use App\Mail\AcceptedUser;
use App\Models\Users\AcceptedTasks;
use App\Models\Users\Task;
use App\Models\Users\TaskState;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AcceptedTasksController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => ['required', 'exists:tasks,id']
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $accepted_tasks = AcceptedTasks::with('user.userable')->where('task_id', $request->task_id)->get();
        $accepted_tasks = (new AcceptedTasks)->get_all_accepted_tasks($accepted_tasks);
        return $this->sendResponse($accepted_tasks);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), (new AcceptedTaskRequest)->rules());
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if (AcceptedTasks::query()->where('task_id', $request->task_id)->where('user_id', $request->user_id)->exists()) {
            return $this->sendError('This user already accepted in this task');
        }

        $request['task_state_id'] = TaskState::query()->where('name_EN', 'Pending')->first()->id;
        $acceptedTask = AcceptedTasks::create($request->all());

        // To reduce the duration 1 every day
        dispatch(new AcceptedTask($acceptedTask))->delay(now()->addDay());

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $task = Task::find($request->task_id);

        // send email to user
        Mail::to($user->email)->send(new AcceptedUser('Acceptance In Task', 'You have been successfully accepted into ' . $task->title . ' task'));

        // To notify the user
        $user->notify(new UserNotification('Acceptance In Task', 'You have been successfully accepted into ' . $task->title . ' task', ['task_id' => $task->id]));

        return $this->sendResponse($acceptedTask);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcceptedTasks $acceptedTasks)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'task_state_id' => ['required', 'exists:task_states,id']
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $accepted_task = AcceptedTasks::find($id);
        if (is_null($accepted_task)) {
            return $this->sendError('There is no accepted_task with this ID');
        }

        $accepted_task->update([
            'task_state_id' => $request->task_state_id
        ]);

        return $this->sendResponse($accepted_task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $acceptedTask = AcceptedTasks::find($id);
        if (is_null($acceptedTask)) {
            return $this->sendError('There is no accepted_task with this ID');
        }
        $acceptedTask->delete();
        return $this->sendResponse();
    }
}
