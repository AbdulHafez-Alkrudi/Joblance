<?php

namespace App\Http\Controllers\Users\Freelancer;

use App\Http\Controllers\BaseController;
use App\Models\Users\Freelancer\Skill;
use App\Models\Users\Freelancer\Tag;
use App\Models\Users\UserProjects\UserSkills;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TagController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = DB::table('tags')->orderByDesc('count')->get();
        return $this->sendResponse($tags);
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
        $validator = Validator::make($request->all(),[
            'name' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $tag = Tag::query()->where('name', $request->name)->first();
        if (is_null($tag)) {
            $tag = Tag::query()->create([
                'name'  => $request->name,
                'count' => 1
            ]);
        }
        else {
            $tag->update([
                'count' => $tag->count + 1
            ]);
        }
        return $this->sendResponse($tag);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tag = Tag::find($id);
        if (is_null($tag)) {
            return $this->sendError(['message' => 'There is no tag with this Id']);
        }
        return $this->sendResponse($tag);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // first I should check if such an id existed or not
        $tag = Tag::find($id);
        if(is_null($tag)) {
            return $this->sendError('there is not such an ID') ;
        }
        $tag->delete();
        return $this->sendResponse();
    }

    public function addToSkills($id)
    {
        $tag = Tag::find($id);
        if (is_null($tag)) {
            return $this->sendError(['There is no tag with this ID']);
        }

        $skill = Skill::query()->where('name', $tag->name)->first();
        if (is_null($skill)) {
            $skill = Skill::query()->create([
                'name' => $tag->name
            ]);
        }

        $userTags = $tag->userTags;
        if (!$userTags->isEmpty()) {
            foreach ($userTags as $userTag) {
                UserSkills::query()->create([
                    'user_id' => $userTag->user_id,
                    'skill_id' => $skill->id
                ]);
            }
        }

        $tag->delete();
        return $this->sendResponse($skill);
    }
}
