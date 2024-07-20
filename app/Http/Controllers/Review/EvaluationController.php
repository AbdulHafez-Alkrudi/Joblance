<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\BaseController;
use App\Models\Users\Evaluation;
use App\Models\Users\Freelancer\Freelancer;
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
            'freelancer_id' => ['required', 'exists:users,id'],
            'level'   => ['required', 'min:0.00', 'max:5.00'],
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $user = User::find($request->freelancer_id);
        if (is_null($user)) {
            return $this->sendError(['message' => 'There is not freelancer with this ID']);
        }

        if ($user->userable_type != Freelancer::class) {
            return $this->sendError(['message' => 'Userable type is not Freelancer']);
        }

        if (auth()->user()->hasEvaluated($user->userable_id)) {
            return $this->sendError('This user already evaluated');
        }

        if (Auth::id() == $request->freelancer_id) {
            return $this->sendError('You can not evaluate yourself');
        }

        Evaluation::create([
            'user_id' => Auth::id(),
            'freelancer_id' => $user->userable_id,
            'level' => $request->level,
        ]);

        $freelancer = $user->userable;
        $freelancer->update([
            'sum_rate' => $freelancer->sum_rate + $request->level,
            'counter' => $freelancer->counter + 1.0,
        ]);

        return $this->sendResponse($freelancer->rate($freelancer->sum_rate, $freelancer->counter));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return $this->sendError(['message' => 'There is no freelancer with this ID']);
        }

        if ($user->userable_type != Freelancer::class) {
            return $this->sendError(['message' => 'Userable type is not Freelancer']);
        }

        $evaluation = Evaluation::query()->where('user_id', Auth::id())->where('freelancer_id', $user->userable_id)->first();
        if (is_null($evaluation)) {
            return $this->sendError(['message' => 'There is no evaluation with this freelancer_id']);
        }

        $freelancer = $user->userable;
        $freelancer->update([
            'sum_rate' => $freelancer->sum_rate - $evaluation->level,
            'counter' => $freelancer->counter - 1.0
        ]);

        $evaluation->delete();
        return $this->sendResponse($freelancer->rate($freelancer->sum_rate, $freelancer->counter));
    }
}
