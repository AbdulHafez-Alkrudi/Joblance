<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\BaseController;
use App\Models\Evaluation;
use App\Models\Freelancer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EvaluationController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'user_id' => ['required', 'exists:users,id'],
            'level'   => ['required', 'min:0.00', 'max:5.00'],
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $user = User::find($request->user_id);
        if (is_null($user)) {
            return $this->sendError(['message' => 'There is not user with this ID']);
        }

        if ($user->userable_type != Freelancer::class) {
            return $this->sendError(['message' => 'Userable type is not Freelancer']);
        }

        Evaluation::create([
            'user_id' => Auth::id(),
            'freelancer_id' => $user->userable_id,
            'level' => $request->level,
        ]);

        $freelancer = $user->userable;
        $freelancer->update([
            'sum' => $freelancer->sum + $request->level,
            'counter' => $freelancer->counter + 1.0,
        ]);

        return $this->sendResponse();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'level' => ['required', 'min:0.00', 'max:5.00']
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $user = User::find($id);
        if (is_null($user)) {
            return $this->sendError(['message' => 'There is not user with this ID']);
        }

        if ($user->userable_type != Freelancer::class) {
            return $this->sendError(['message' => 'Userable type is not Freelancer']);
        }

        $evaluation = Evaluation::query()->where('user_id', Auth::id())->where('freelancer_id', $user->userable_id)->first();
        if (is_null($evaluation)) {
            return $this->sendError(['message' => 'There is not evaluation with this user_id']);
        }

        $freelancer = $user->userable;
        $freelancer->update([
            'sum' => $freelancer->sum - $evaluation->level + $request->level
        ]);
        $evaluation->update([
            'level' => $request->level
        ]);

        return $this->sendResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return $this->sendError(['message' => 'There is not user with this ID']);
        }

        if ($user->userable_type != Freelancer::class) {
            return $this->sendError(['message' => 'Userable type is not Freelancer']);
        }

        $evaluation = Evaluation::query()->where('user_id', Auth::id())->where('freelancer_id', $user->userable_id)->first();
        if (is_null($evaluation)) {
            return $this->sendError(['message' => 'There is not evaluation with this user_id']);
        }

        $freelancer = $user->userable;
        $freelancer->update([
            'sum' => $freelancer->sum - $evaluation->level,
            'counter' => $freelancer->counter - 1.0
        ]);

        $evaluation->delete();

        return $this->sendResponse();
    }
}
