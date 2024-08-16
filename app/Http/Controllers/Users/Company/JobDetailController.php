<?php

namespace App\Http\Controllers\Users\Company;

use App\Http\Controllers\BaseController;
use App\Http\Requests\JobDetailRequest;
use App\Models\User;
use App\Models\Users\Company\JobDetail;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class JobDetailController extends BaseController
{
    public function index(Request $request)
    {
        if ($request->has('company_id')) {
            return $this->indexByCompanyId($request->company_id);
        }

        if (Gate::allows('isAdmin', Auth::user())) {
            $jobs_detail = JobDetail::with(['company.user', 'job_applications'])
                                    ->orderByDesc('created_at')
                                     ->filter(\request(['title' , 'location' , 'remote_type', 'job_type' , 'experience_level' , 'major' , 'date_posted']))
                                    ->paginate(5);
        }
        else {
            $user = auth()->user()->userable;
            $jobs_detail = JobDetail::with(['company.user', 'job_applications'])
                                    ->orderByRaw("CASE WHEN job_details.company_id IN (SELECT user_id FROM followers WHERE followers.follower_id = ?) THEN 0 ELSE 1 END, CASE WHEN major_id = ? THEN 0 ELSE 1 END, CASE WHEN location = ? THEN 0 ELSE 1 END, job_details.created_at DESC", [Auth::id(), $user->major_id, $user->location])
                                     ->filter(\request(['title' , 'location' , 'remote_type', 'job_type' , 'experience_level' , 'major' , 'date_posted']))
                                    ->paginate(5);
        }
        $jobs_detail = (new JobDetail)->get_all_jobs_detail($jobs_detail, request('lang'));

        return $this->send_response($jobs_detail);
    }

    public function indexByCompanyId($company_id)
    {
        $user = User::find($company_id);
        if (is_null($user)) {
            return $this->sendError(['message' => 'There is no Company with this ID']);
        }

        $jobs_detail = JobDetail::with(['company.user', 'job_applications'])
                        ->where('company_id', $user->userable_id)
                        ->orderByDesc('created_at')
                        ->filter(\request(['title' , 'location' , 'remote_type', 'job_type' , 'experience_level' , 'major' , 'date_posted']))
                        ->paginate(5);
        $jobs_detail = (new JobDetail)->get_all_jobs_detail($jobs_detail, request('lang'));
        return $this->send_response($jobs_detail);
    }

    public function send_response($jobs_detail): \Illuminate\Http\JsonResponse
    {
        $response = [
            'status' => 'success',
            'data' => $jobs_detail->items(),
            'meta' => [
                'current_page' => $jobs_detail->currentPage(),
                'last_page' => $jobs_detail->lastPage(),
                'per_page' => $jobs_detail->perPage(),
                'total' => $jobs_detail->total(),
            ]
        ];
        return response()->json($response, 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), (new JobDetailRequest)->rules());
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $request['company_id'] = Auth::user()->userable_id;
        $job_detail = JobDetail::create($request->all());

        // To notify users following this user
        $users = User::query()->whereIn('id', Auth::user()->followers()->pluck('follower_id'))->get();
        Notification::send($users, new UserNotification('Post a job', auth()->user()->userable_name . 'posted a new job', ['job_detail_id' => $job_detail->id]));

        return $this->sendResponse($job_detail);
    }

    public function show($id)
    {
        $job_detail = JobDetail::with(['company.user', 'job_applications'])->find($id);
        if (is_null($job_detail)) {
            return $this->sendError(['message' => 'There is no job_detail with this ID']);
        }

        $job_detail = $job_detail->get_job_detail($job_detail, request('lang'));
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
