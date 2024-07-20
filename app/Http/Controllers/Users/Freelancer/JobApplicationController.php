<?php

namespace App\Http\Controllers\Users\Freelancer;

use App\Http\Controllers\BaseController;
use App\Http\Requests\JobApplicationRequest;
use App\Models\Users\Freelancer\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobApplicationController extends BaseController
{
    public function index()
    {
        $lang = request('lang');
        $job_detail_id = request('job_detail_id');
        $job_applications = (new JobApplication)->get_all_job_applications($job_detail_id, $lang);
        return $this->sendResponse($job_applications);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), (new JobApplicationRequest)->rules());
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->hasApplication($request->job_detail_id)) {
            return $this->sendError('This user has already applied for the job');
        }

        $file_path = $this->get_file($request->file('CV'), "CVs");
        $job_application = JobApplication::create([
            'job_detail_id' => $request->job_detail_id,
            'freelancer_id' => Auth::id(),
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'phone_number'  => $request->phone_number,
            'cover_letter'  => $request->cover_letter,
            'CV'            => $file_path
        ]);
        $job_application['CV'] = asset('storage/' . $job_application['CV']);

        return $this->sendResponse($job_application);
    }

    public function show($id)
    {
        $lang = request('lang');
        $job_application = JobApplication::find($id);
        if (is_null($job_application)) {
            return $this->sendError(['message' => 'There is no job_application with this ID']);
        }

        $job_application = $job_application->get_job_application($job_application, $lang);
        return $this->sendResponse($job_application);
    }
}
