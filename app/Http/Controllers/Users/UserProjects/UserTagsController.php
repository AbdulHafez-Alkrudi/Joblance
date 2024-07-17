<?php

namespace App\Http\Controllers\Users\UserProjects;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Users\Freelancer\TagController;
use App\Http\Requests\UserTagRequest;
use App\Models\Users\Freelancer\Tag;
use App\Models\Users\UserProjects\UserTags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserTagsController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userTags = (new UserTags)->get_tags(auth()->user()->tags);
        return $this->sendResponse($userTags);
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
            'name' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->hasTag($request->name)) {
            return $this->sendError(['User already has tag with this name']);
        }

        $tag = (new TagController)->store($request);
        $userTag = UserTags::query()->create([
            'user_id' => $user->id,
            'tag_id'  => $tag->getData()->data->id
        ]);

        return $this->sendResponse($userTag);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserTags $userTags)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserTags $userTags)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserTags $userTags)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $userTag = UserTags::find($id);
        if (is_null($userTag)) {
            return $this->sendError(['message' => 'User does not have a tag with this ID']);
        }

        $tag = Tag::find($userTag->tag_id);

        $userTag->delete();
        $tag->update([
            'count' => $tag->count - 1
        ]);

        return $this->sendResponse();
    }
}
