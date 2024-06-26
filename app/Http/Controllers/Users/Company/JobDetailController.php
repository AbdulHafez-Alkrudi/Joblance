<?php

namespace App\Http\Controllers\Users\Company;

use App\Http\Controllers\BaseController;
use App\Http\Requests\JobDetailRequest;
use App\Models\User;
use App\Models\Users\Company\JobDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobDetailController extends BaseController
{
    public function index(Request $request)
    {
        if ($request->has('company_id')) {
            return $this->indexByCompanyId($request->company_id);
        }
        $jobs_detail = JobDetail::all();
        $jobs_detail = (new JobDetail)->get_all_jobs($jobs_detail);
        return $this->sendResponse($jobs_detail);
    }

    public function indexByCompanyId($company_id)
    {
        if (is_null(User::find($company_id))) {
            return $this->sendError(['message' => 'There is no Company with this ID']);
        }

        $jobs_detail = JobDetail::query()->where('company_id', $company_id)->get();
        $jobs_detail = (new JobDetail)->get_all_jobs($jobs_detail);
        return $this->sendResponse($jobs_detail);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), (new JobDetailRequest)->rules());
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $request['company_id'] = Auth::id();
        $job_detail = JobDetail::create($request->all());

        return $this->sendResponse($job_detail);
    }

    public function show($id)
    {
        $job_detail = JobDetail::find($id);
        if (is_null($job_detail)) {
            return $this->sendError(['message' => 'There is no job_detail with this ID']);
        }
        $job_detail = (new JobDetail)->get_job($job_detail);
        return $this->sendResponse($job_detail);
    }

    public function update(Request $request, $id)
    {
        $job_detail = JobDetail::find($id);
        if (is_null($job_detail)) {
            return $this->sendError(['message' => 'There is no job_detail with this ID']);
        }

        $job_detail->update($request->all());
        return $this->sendResponse($job_detail);
    }

    public function destroy($id)
    {
        $job_detail = JobDetail::find($id);
        if (is_null($job_detail)) {
            return $this->sendError(['message' => 'There is no job_detail with this ID']);
        }

        $job_detail->delete();
        return $this->sendResponse();
    }
}
