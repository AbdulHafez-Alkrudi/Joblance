<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Models\Users\Follower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FollowerController extends BaseController
{
    public function show($user_id)
    {
        $user = User::find($user_id);
        if (is_null($user)) {
            return $this->sendError(['message' => 'There is no user with this ID']);
        }

        return $this->sendResponse(['followers' => count($user->followers)]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => ['required', 'exists:users,id']
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        Follower::create([
            'user_id' => $request->user_id
        ]);

        return $this->show($request->user_id);
    }

    public function destroy($user_id)
    {
        $user = User::find($user_id);
        if (is_null($user)) {
            return $this->sendError(['message' => 'There is no user with this ID']);
        }

        $follower = Follower::query()->where('user_id', $user_id)->first();
        if (is_null($follower)) {
            return $this->sendError(['message' => 'This user has no followers']);
        }

        $follower->delete();
        return $this->show($user_id);
    }
}
