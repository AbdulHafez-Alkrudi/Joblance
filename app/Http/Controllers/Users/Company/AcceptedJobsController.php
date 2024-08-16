<?php

namespace App\Http\Controllers\Users\Company;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Users\UserController;
use App\Mail\AcceptedUser;
use App\Models\User;
use App\Models\Users\Company\AcceptedJobs;
use App\Models\Users\Company\JobDetail;
use App\Models\Users\Freelancer\Freelancer;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AcceptedJobsController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('job_detail_id'))
        {
            $accepted_jobs = AcceptedJobs::query()->where('job_detail_id', $request->job_detail_id)->get();
            $accepted_jobs = (new AcceptedJobs)->get_all_accepted_jobs($accepted_jobs);
            return $this->sendResponse($accepted_jobs);
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
            'job_detail_id' => ['required', 'exists:job_details,id'],
            'user_id' => ['required', 'exists:freelancers,id']
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if (AcceptedJobs::query()->where('job_detail_id', $request->job_detail_id)->where('user_id', $request->user_id)->exists()) {
            return $this->sendError('This user already accepted in this job');
        }

        $acceptedJob = AcceptedJobs::create($request->all());

        /** @var \App\Models\User $user */
        //$user = Auth::user();

        $user = Freelancer::find($request->user_id)->user;

        $job_detail = JobDetail::find($request->job_detail_id);

        // send email to user
        Mail::to($user->email)->send(new AcceptedUser('Acceptance In Job', 'You have been successfully accepted into ' . $job_detail->title . ' job'));

        // To notify the user
        $user->notify(new UserNotification('Acceptance In Job', 'You have been successfully accepted into ' . $job_detail->title . ' task', ['job_detail_id' => $job_detail->id]));

        return $this->sendResponse($acceptedJob);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $acceptedJob = AcceptedJobs::find($id);
        if (is_null($acceptedJob)) {
            return $this->sendError('There is no accepted_job with this ID');
        }
        $acceptedJob = $acceptedJob->get_accepted_job($acceptedJob, request('lang'));
        return $this->sendResponse($acceptedJob);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcceptedJobs $acceptedJobs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcceptedJobs $acceptedJobs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
