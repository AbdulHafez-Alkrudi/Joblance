<?php

namespace App\Http\Controllers\Users\Freelancer;

use App\Http\Controllers\BaseController;
use App\Models\Users\Freelancer\StudyCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class StudyCaseController extends BaseController
{
    public function index()
    {
        $lang = request('lang');
        $study_case = (new StudyCase)->get_all_study_case($lang);
        return $this->sendResponse($study_case);
    }

}
