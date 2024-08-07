<?php

namespace App\Http\Controllers\Users\Favourite;

use App\Http\Controllers\BaseController;
use App\Models\Users\Favourite\FavouriteTask;
use App\Models\Users\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FavouriteTaskController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('user_id')) {
            $favourite_tasks = Auth::user()->favourite_tasks()->with('task')->get();
            $favourite_tasks = (new FavouriteTask)->get_all_favourite_tasks($favourite_tasks);
            return $this->sendResponse($favourite_tasks);
        }
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
        $validator = Validator::make($request->all(), [
            'task_id' => ['required', 'exists:tasks,id']
        ]);

        if($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if (Auth::user()->hasFavouriteTask($request->task_id)) {
            return $this->sendError('This task already favourited');
        }

        $request['user_id'] = Auth::id();
        $favourite_task = FavouriteTask::create($request->all());

        return $this->sendResponse($favourite_task);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $favourite_task = FavouriteTask::with('task')->find($id);
        if (is_null($favourite_task)) {
            return $this->sendError('There is no favourite_task with this ID');
        }

        $favourite_task = $favourite_task->get_favourite_task($favourite_task);
        return $this->sendResponse($favourite_task);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FavouriteTask $favouriteTask)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FavouriteTask $favouriteTask)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (auth()->user()->hasFavouriteTask($id)) {
            FavouriteTask::where('user_id', Auth::id())->where('task_id', $id)->delete();
        }
        else {
            return $this->sendError("You don't have this task as a favourite");
        }

        return $this->sendResponse("I'm sorry i don't speak Englizy fery kood");
    }
}
