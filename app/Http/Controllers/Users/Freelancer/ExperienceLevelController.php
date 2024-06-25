<?php

namespace App\Http\Controllers\Users\Freelancer;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ExperienceLevelRequest;
use App\Models\Users\Freelancer\ExperienceLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExperienceLevelController extends BaseController
{
    public function index()
    {
        $lang = request('lang');
        $experience_levels = (new ExperienceLevel)->get_all_experience_levels($lang);
        return $this->sendResponse($experience_levels);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), (new ExperienceLevelRequest)->rules());
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $experience_level = ExperienceLevel::create($request->all());
        return $this->sendResponse($experience_level);
    }

    public function destroy($id)
    {
        $experience_level = ExperienceLevel::find($id);
        if (is_null($experience_level)) {
            return $this->sendError(['message' => 'There is no Experience_Level with this ID']);
        }

        $experience_level->delete();
        return $this->sendResponse();
    }
}
