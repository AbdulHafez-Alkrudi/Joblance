<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Models\Users\Follower;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $user = User::find($request->user_id);
        if (is_null($user)) {
            return $this->sendError('There is no user with this ID');
        }

        if ($user->hasFollow($user)) {
            return $this->sendError('You already followed this user');
        }

        if ($request->user_id == Auth::id()) {
            return $this->sendError('You can not follow yourself');
        }

        Follower::create([
            'user_id' => $request->user_id,
            'follower_id' => Auth::id()
        ]);

        // To notify the user
        $current_user = Auth::user()->userable;
        if ($current_user->name) {
            $user_name = $current_user->name;
        }
        else {
            $user_name = $current_user->first_name . ' ' . $current_user->last_name;
        }
        $user->notify(new UserNotification('New Follow', $user_name . ' started following you', ['user_id' => Auth::id()]));

        return $this->show($request->user_id);
    }

    public function destroy($user_id)
    {
        $user = User::find($user_id);
        if (is_null($user)) {
            return $this->sendError(['message' => 'There is no user with this ID']);
        }

        $follower = auth()->user()->followings()->where('user_id', $user_id)->first();
        if (is_null($follower)) {
            return $this->sendError(['message' => 'You are not following this user']);
        }

        $follower->delete();
        return $this->show($user_id);
    }
}
