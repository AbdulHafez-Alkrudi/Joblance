<?php

namespace App\Http\Controllers\Users\Favourite;

use App\Http\Controllers\BaseController;
use App\Models\Users\Company\JobDetail;
use App\Models\Users\Favourite\FavouriteJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FavouriteJobController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('user_id')) {
            $favourite_jobs = Auth::user()->favourite_jobs()->with('job_detail')->get();
            $favourite_jobs = (new FavouriteJob)->get_all_favourite_jobs($favourite_jobs);
            return $this->sendResponse($favourite_jobs);
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
            'job_detail_id' => ['required', 'exists:job_details,id']
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if (Auth::user()->hasFavouriteJob($request->job_detail_id)) {
            return $this->sendError('This job already favourited');
        }

        $request['user_id'] = Auth::id();
        $favourite_job = FavouriteJob::create($request->all());

        return $this->sendResponse($favourite_job);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $favourite_job = FavouriteJob::with('job_detail')->find($id);
        if (is_null($favourite_job)) {
            return $this->sendError('There is no favourite_job with this ID');
        }

        $favourite_job = $favourite_job->get_favourite_job($favourite_job);
        return $this->sendResponse($favourite_job);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FavouriteJob $favouriteJob)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FavouriteJob $favouriteJob)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $job_detail = JobDetail::find($id);
        if (is_null($job_detail)) {
            return $this->sendError('There is no job_detail with this ID');
        }

        $job_detail->delete();
        return $this->sendResponse();
    }
}
