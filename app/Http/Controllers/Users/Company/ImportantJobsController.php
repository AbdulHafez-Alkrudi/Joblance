<?php

namespace App\Http\Controllers\Users\Company;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Payment\BudgetController;
use App\Http\Requests\ImportantJobRequest;
use App\Jobs\ImportantJob;
use App\Models\Payment\Price;
use App\Models\Users\Company\ImportantJobs;
use App\Models\Users\Company\JobDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ImportantJobsController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Gate::allows('isAdmin', Auth::user())) {
            $importantJobs = ImportantJobs::with('job_detail')->orderByDesc('created_at')->get();
        }
        else {
            $user = auth()->user()->userable;
            $importantJobs = ImportantJobs::with('job_detail')
                                        ->join('job_details', 'important_jobs.job_detail_id', '=', 'job_details.id')
                                        ->orderByRaw("CASE WHEN job_details.company_id IN (SELECT user_id FROM followers WHERE followers.follower_id = ?) THEN 0 ELSE 1 END, CASE WHEN job_details.major_id = ? THEN 0 ELSE 1 END, CASE WHEN job_details.location = ? THEN 0 ELSE 1 END, job_details.created_at DESC", [Auth::id(), $user->major_id, $user->location])
                                        ->get();
        }
        $importantJobs = (new ImportantJobs)->get_all_important_jobs($importantJobs, request('lang'));
        return $this->sendResponse($importantJobs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), (new ImportantJobRequest)->rules());
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $job_detail = JobDetail::find($request->job_detail_id);

        if (auth()->user()->userable_id != $job_detail->company_id) {
            return $this->sendError(['message' => 'You do not own this job']);
        }


        $check = ImportantJobs::query()->where('job_detail_id' , $request->job_detail_id);
        if($check != null){
            return $this->sendResponse(['message' => 'this job is already existed in the important jobs']);
        }

        $price = Price::where('name_EN', 'Important Job')->first();
        if ($request->budget) {
            $payRequest = new Request(['balance' => $price->price]);
            $response = (new BudgetController)->pay($payRequest);
            if ($response->getData()->status == 'failure') {
                return $response;
            }
        }
        else {
            //
        }
        $request['price_id'] = $price->id;
        $importantJob = ImportantJobs::create($request->all());
        dispatch(new ImportantJob($importantJob))->delay(now()->addDays(10));
        return $this->sendResponse($importantJob);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $importantJob = ImportantJobs::with('job_detail')->find($id);
        if (is_null($importantJob)) {
            return $this->sendError(['message' => 'There is no importantJob with this ID']);
        }

        $lang = request('lang');
        $importantJob = $importantJob->get_important_job($importantJob, $lang);

        return $this->sendResponse($importantJob);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ImportantJobs $importantJobs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ImportantJobs $importantJobs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ImportantJobs $importantJobs)
    {
        //
    }
}
